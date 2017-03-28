<?php
/* 
 * get_ol_xml.php
 * 
 * @package     UsedDomainTools
 * @author      Y.Yajima <yajima@hatchbit.jp>
 * @copyright   2016, HatchBit & Co.
 * @license     This software is released under the MIT License.
 *              http://opensource.org/licenses/mit-license.php
 * @link        http://www.hatchbit.jp
 * @since       Version 2.0
 * @filesource
 */

require_once './config.php';
$password = strval($_GET['password']);
if( $password != Password ){
    header("HTTP/1.0 400 Bad Request");
    echo "<h1>400 Bad Request</h1>";
    exit();
}

// Get your access id and secret key here: https://moz.com/products/api/keys
$accessID = "mozscape-220b92a7b6";
$secretKey = "46d7fb637e136ce02287928947b98d9c";

if(isset($_GET['accessid']) && !empty($_GET['accessid'])){
    $accessID = strval($_GET['accessid']);
}

if(isset($_GET['secretkey']) && !empty($_GET['secretkey'])){
    $secretKey = strval($_GET['secretkey']);
}

// Set your expires times for several minutes into the future.
// An expires time excessively far in the future will not be honored by the Mozscape API.
$expires = time() + 300;

// Put each parameter on a new line.
$stringToSign = $accessID."\n".$expires;

// Get the "raw" or binary output of the hmac hash.
$binarySignature = hash_hmac('sha1', $stringToSign, $secretKey, true);

// Base64-encode it and then url-encode that.
$urlSafeSignature = urlencode(base64_encode($binarySignature));

// Specify the URL that you want link metrics for.
$objectURL = "moz.com";
$objectURL = htmlspecialchars($_GET['url']);

// Add up all the bit flags you want returned.
// Learn more here: https://moz.com/help/guides/moz-api/mozscape/api-reference/url-metrics
$cols = "103079215108";

// Put it all together and you get your request URL.
// This example uses the Mozscape URL Metrics API.
//$requestUrl = "http://lsapi.seomoz.com/linkscape/url-metrics/".urlencode($objectURL)."?Cols=".$cols."&AccessID=".$accessID."&Expires=".$expires."&Signature=".$urlSafeSignature;
$requestUrl = "http://lsapi.seomoz.com/linkscape/links/".urlencode($objectURL)."?Scope=page_to_page&Sort=page_authority&Filter=external&Limit=100&SourceCols=".$cols."&TargetCols=4&AccessID=".$accessID."&Expires=".$expires."&Signature=".$urlSafeSignature;
//$requestUrl = "http://lsapi.seomoz.com/linkscape/links/moz.com?Scope=pagetopage&Sort=page_authority&Filter=internal+301&Limit=1&SourceCols=536870916&TargetCols= 4&AccessID=".$accessID."&Expires=".$expires."&Signature=".$urlSafeSignature;
//echo $requestUrl.PHP_EOL;

// Use Curl to send off your request.
$options = array(
    CURLOPT_RETURNTRANSFER => true
    ,CURLOPT_HEADER => false
);

$ch = curl_init($requestUrl);
curl_setopt_array($ch, $options);
$content = curl_exec($ch);
curl_close($ch);

//var_dump($content);
//exit();

$tableData = json_decode($content);
//var_dump($tableData);
//exit();

if(isset($tableData)){
	$results = xml_Document($tableData);
    header("Content-Type: text/xml; charset=utf-8");
    echo $results;
}else{
	echo "no data.";
}


/*====================
  AFTER ACTIONS
  ====================*/

/*====================
  FUNCTIONS
  ====================*/

//xml表示
function xml_Document($tableData){
	$i = 1;

	// インスタンスの生成
	$dom = new DomDocument('1.0', 'UTF-8');
	// prefs ノードを追加
	$prefs = $dom->appendChild($dom->createElement('linkchecks'));
	
	// 要素ノードを追加してテキストを入れる

	foreach($tableData as $val){
		// code 属性の追加
		$pref = $prefs->appendChild($dom->createElement('linkcheck'));
		//$pref->setAttribute('code', $i);

		foreach($val as $key2 => $val2){
			$pref->appendChild($dom->createElement($key2, $val2));
		}
		unset($key2,$val2);

		$i++;
	}

	//$pref = $prefs->appendChild($dom->createElement('pref'));
	
	//XML を整形（改行・字下げ）して出力
	$dom->formatOutput = true;

	return $dom->saveXML();

}
