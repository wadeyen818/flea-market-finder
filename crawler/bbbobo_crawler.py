"""跳蚤本舖：列表取得 AID/GID，詳情補上分類、日期、簡述與庫存。"""
import re
import time
from datetime import datetime, timezone
from pathlib import Path
from urllib.parse import urlencode, urljoin, urlparse, parse_qs

import requests
from bs4 import BeautifulSoup

SEARCH_URL = 'https://www.bbbobo.com.tw/shop/0/Goods_List.asp'
LABELS = ('商品所在','商品分類','商品編號','上架時間','商品單價','產品簡述','庫存數')
LABEL_PATTERN = re.compile('('+'|'.join(LABELS)+r')\s*[：:]')


def text_of(card, selector):
    node=card.select_one(selector)
    return node.get_text(' ',strip=True) if node else None


def money(value):
    match=re.fullmatch(r'\s*\$\s*([\d,]+)\s*',value or '')
    return int(match.group(1).replace(',','')) if match else None


def list_products(soup,page_url):
    products={}
    for card in soup.select('div.pr-info[aid][gid]'):
        aid=card.get('aid','').strip();gid=card.get('gid','').strip()
        if not aid.isdigit() or not re.fullmatch(r'[A-Za-z0-9_-]+',gid): continue
        title_node=card.select_one('.pr-name')
        title=(title_node.get('title') or title_node.get_text(' ',strip=True)) if title_node else None
        products.setdefault((aid,gid),{
            'aid':aid,'gid':gid,'title':title,
            'url':urljoin(page_url,'Goods.asp')+'?'+urlencode({'AID':aid,'GID':gid}),
            'list_price':money(text_of(card,'.pr-price-org')),
            # 此 class 的 dwon 拼字沿用網站 HTML，不是筆誤。
            'list_original_price':money(text_of(card,'.pr-price-org-dwon')),
        })
    return list(products.values())


def extract_fields(text):
    matches=list(LABEL_PATTERN.finditer(text));fields={}
    for i,m in enumerate(matches):
        end=matches[i+1].start() if i+1<len(matches) else len(text)
        fields[m.group(1)]=text[m.end():end].strip()
    return fields


def normalize_date(value):
    if not value: return None
    for pattern in ('%Y/%m/%d','%Y-%m-%d'):
        try: return datetime.strptime(value.strip(),pattern).date().isoformat()
        except ValueError: pass
    raise ValueError(f'無法辨識上架日期：{value!r}')


def parse_detail(soup,item,keyword):
    for node in soup(['script','style','noscript']):node.decompose()
    text=soup.get_text('\n',strip=True)
    text=re.split(r'您可能喜歡的商品|購物流程及注意事項',text,maxsplit=1)[0]
    marker=re.search(r'商品所在\s*[：:]',text)
    if not marker:raise RuntimeError('詳情頁沒有商品欄位；無法判定商品是否下架。')
    fields=extract_fields(text[marker.start():])
    if fields.get('商品編號','').strip()!=item['gid']:raise RuntimeError('詳情頁商品編號與列表不符。')
    title=item['title']
    if not title:raise RuntimeError('商品名稱缺失。')
    price_text=fields.get('商品單價','')
    detail_prices=[int(v.replace(',','')) for v in re.findall(r'\$\s*([\d,]+)',price_text)]
    # 詳情僅一個價格時採詳情值；多個價格時保留 null，避免誤判。
    price=detail_prices[0] if len(detail_prices)==1 else None
    if not detail_prices:raise RuntimeError('詳情價格無法解析，請檢查 debug HTML。')
    original=item['list_original_price']
    # 列表與詳情在請求間可能異動，只有目前售價一致時採用列表原價。
    if price!=item['list_price']:original=None
    stock_match=re.match(r'\s*(\d+)',fields.get('庫存數',''))
    stock=int(stock_match.group(1)) if stock_match else None
    description=fields.get('產品簡述')
    if description:
        description=re.split(r'加入購物車',description,maxsplit=1)[0].strip()
        if description in ('(無)','（無）','無'):description=None
    images=[]
    for image in soup.find_all('img'):
        source=image.get('data-src') or image.get('src')
        if not source:continue
        url=urljoin(item['url'],source)
        if item['gid'].lower() in urlparse(url).path.lower() and url not in images:images.append(url)
    return {
        'platform_code':'bbbobo','search_key_word':keyword,'external_item_id':item['gid'],
        'title':title,'url':item['url'],'price':price,'original_price':original,'currency':'TWD',
        'description':description,'source_category':fields.get('商品分類'),
        'stock_quantity':stock,'availability':'unknown' if stock is None else ('available' if stock>0 else 'sold_out'),
        'listed_at':normalize_date(fields.get('上架時間')),
        'store':{'external_store_id':item['aid'],'name':fields.get('商品所在')},
        'images':[{'url':url,'order':i} for i,url in enumerate(images,1)],
        'observed_at':datetime.now(timezone.utc).isoformat(),
    }


class Fetcher:
    def __init__(self,session,delay,directory):
        self.session=session;self.delay=delay;self.directory=directory;self.last=None
    def get(self,url,name,params=None):
        if self.last is not None:time.sleep(max(0,self.delay-(time.monotonic()-self.last)))
        try:response=self.session.get(url,params=params,timeout=(10,30))
        finally:self.last=time.monotonic()
        (self.directory/name).write_bytes(response.content)
        response.raise_for_status()
        return BeautifulSoup(response.content,'html.parser'),response.url


def has_next(soup,page):
    for a in soup.select('a[href]'):
        parsed=urlparse(urljoin(SEARCH_URL,a['href']))
        if parsed.netloc!=urlparse(SEARCH_URL).netloc or parsed.path.lower()!=urlparse(SEARCH_URL).path.lower():continue
        query={k.lower():v[0] for k,v in parse_qs(parsed.query).items()}
        value=query.get('page','')
        if value.isdigit() and int(value)>page:return True
    return False


def crawl(keyword,max_pages=1,max_items=5,delay=3,debug_dir='debug'):
    directory=Path(debug_dir);directory.mkdir(parents=True,exist_ok=True)
    products=[];seen=set()
    with requests.Session() as session:
        session.headers.update({'User-Agent':'FindAgainPortfolioCrawler/0.3','Accept-Language':'zh-TW'})
        fetcher=Fetcher(session,delay,directory)
        for page in range(1,max_pages+1):
            soup,page_url=fetcher.get(SEARCH_URL,f'list_{page}.html',{'KeyWord':keyword,'Page':page})
            items=list_products(soup,page_url)
            if not items:
                if re.search(r'共\s*0\s*筆',soup.get_text(' ',strip=True)):break
                raise RuntimeError('找不到商品區塊，請檢查列表 HTML。')
            new=[item for item in items if (item['aid'],item['gid']) not in seen]
            if not new:raise RuntimeError('列表重複，分頁可能未生效。')
            for item in new:
                seen.add((item['aid'],item['gid']))
                print(f"取得詳情：{item['gid']}",flush=True)
                detail,_=fetcher.get(item['url'],f"detail_{item['aid']}_{item['gid']}.html")
                products.append(parse_detail(detail,item,keyword))
                if len(products)>=max_items:return products
            if not has_next(soup,page):break
    return products
