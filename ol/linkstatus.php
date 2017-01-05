<?php
require_once './config.php';
// スタートスクリプト
$url ="";
$url = htmlspecialchars($_GET['url']);

$password ="";
$password = htmlspecialchars($_GET['password']);

if(!$url || $password != Password ){
	header("HTTP/1.0 404 Not Found");
	exit;
}

$get_url = "http://".$url;
$httpStatus = get_http_statuscode($get_url);
//echo $url.' => '.$httpStatus.PHP_EOL;

$tableData = array();
$tableData[$url]["linkstatus"]=$httpStatus;

if(isset($tableData)){
	xml_Document($tableData);
}else{
}

function get_http_statuscode($url){
	$return = "";
	$agent = "Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; WOW64; Trident/6.0)";
	
	// 新しい cURL リソースを作成します
	$curl = curl_init();
	// URL その他のオプションを適切に設定します
	$options = array(
		CURLOPT_URL            => $url,// 取得する URL 。 curl_init() でセッションを 初期化する際に指定することも可能です。
		CURLOPT_HEADER         => TRUE,// TRUE を設定すると、ヘッダの内容も出力します。
		CURLOPT_NOBODY         => TRUE,// TRUE を設定すると、出力から本文を削除します。 リクエストメソッドは HEAD となります。これを FALSE に変更してもリクエストメソッドは GET には変わりません。
		CURLOPT_RETURNTRANSFER => TRUE,// TRUE を設定すると、 curl_exec() の返り値を 文字列で返します。通常はデータを直接出力します。
		CURLOPT_FRESH_CONNECT  => TRUE,// TRUE を設定すると、キャッシュされている接続を利用せずに 新しい接続を確立します。
		CURLOPT_USERAGENT      => $agent// HTTP リクエストで使用される "User-Agent: " ヘッダの内容。
		//CURLOPT_CONNECTTIMEOUT => 5
		);
	curl_setopt_array($curl, $options);
	// URL の内容を取得し、ブラウザに渡します
	$result = curl_exec($curl);
	
	$info = curl_getinfo($curl);
	$return = $info['http_code'];
	
	// cURL リソースを閉じ、システムリソースを開放します
	curl_close($curl);
	
	//return $result;
	return $return;
}

//xml表示
function xml_Document($tableData){
	$i = 1;

	// インスタンスの生成
	$dom = new DomDocument('1.0', 'UTF-8');
	// prefs ノードを追加
	$prefs = $dom->appendChild($dom->createElement('linkchecks'));
	
	// 要素ノードを追加してテキストを入れる

	foreach($tableData as $key => $val){
		// code 属性の追加
		$pref = $prefs->appendChild($dom->createElement('linkcheck'));
		$pref->setAttribute('url', $key);

		foreach($val as $key => $val2){
			$pref->appendChild($dom->createElement($key, $val2));
		}

		$i++;
	}
	unset($key,$val);
	
	//$pref = $prefs->appendChild($dom->createElement('pref'));
	
	//XML を整形（改行・字下げ）して出力
	$dom->formatOutput = true;

	header("Content-Type: text/xml; charset=utf-8");
	echo $dom->saveXML();

}
?>