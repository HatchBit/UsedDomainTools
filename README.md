# useddomaintools
Used domain scrutiny tool


管理画面などフロントエンドの作成
仮のデータを入れて、テスト

トップページ
/

ドメインDB登録
/insertdomain

-- CSVファイルアップロード
/insertdomain/csv

-- pool.com からダウンロード
/insertdomain/pool

-- サーバー上のファイルをダウンロード
/insertdomain/download

精査結果
/result

-- データ検索（ダウンロード）
/result/search/(params)

データリセット
/resetdata

-- 全てのドメインを削除
-- 指定した日付のドメインを削除

データ検索（ドメインの件数）
/domainlist

/domainlist/search/(params)

スクリプト手動実行
/execute


## STATUS 

0 未処理（初期値）  
-12 HTTPCODE.100-600  
-1 HTTPCODE.それ以外  
10000over 一時ステータス  
-3 XML OK  
-4 XML NG  
30000over 一時ステータス  
