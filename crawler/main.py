"""Laravel 呼叫入口：標準輸出為 log，商品結果寫入指定 JSON。"""
import argparse
import json
import sys
from pathlib import Path
from datetime import datetime, timezone


def main():
    parser = argparse.ArgumentParser()
    parser.add_argument('--keyword', required=True)
    parser.add_argument('--mode', choices=['demo', 'live'], default='demo')
    parser.add_argument('--max-pages', type=int, default=1)
    parser.add_argument('--max-items', type=int, default=5)
    parser.add_argument('--delay', type=float, default=3)
    parser.add_argument('--output', required=True)
    parser.add_argument('--debug-dir', default='debug')
    args = parser.parse_args()
    keyword = args.keyword.strip()
    if not keyword or len(keyword)>40:
        parser.error('關鍵字必須為 1～40 字。')
    if not 1<=args.max_pages<=3 or not 1<=args.max_items<=20 or args.delay<1:
        parser.error('頁數 1～3、商品 1～20、間隔至少 1 秒。')
    if args.mode=='demo':
        rows=json.loads(Path(__file__).with_name('sample.json').read_text(encoding='utf-8'))
        seen=set();products=[]
        for row in rows:
            if row['search_key_word']!=keyword: continue
            key=(row['platform_code'],row['external_item_id'])
            if key in seen: continue
            seen.add(key)
            row['observed_at']=datetime.now(timezone.utc).isoformat()
            products.append(row)
            if len(products)>=args.max_items: break
    else:
        from bbbobo_crawler import crawl
        products=crawl(keyword,args.max_pages,args.max_items,args.delay,args.debug_dir)
    output=Path(args.output)
    output.parent.mkdir(parents=True,exist_ok=True)
    temporary=output.with_suffix('.tmp')
    temporary.write_text(json.dumps(products,ensure_ascii=False,indent=2),encoding='utf-8')
    temporary.replace(output)
    print(f'完成：模式={args.mode}，關鍵字={keyword}，商品={len(products)}')


if __name__=='__main__':
    try:
        main()
    except Exception as error:
        print(f'失敗：{error}',file=sys.stderr)
        sys.exit(1)
