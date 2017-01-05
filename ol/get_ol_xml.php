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
 * @since       Version 1.0
 * @filesource
 */

/*====================
  DEFINE
  ====================*/

ini_set( "display_errors", "Off");

require_once('./config.php');

/*====================
  BEFORE ACTIONS
  ====================*/

set_include_path(ROOT_DIR . PATH_SEPARATOR . get_include_path());

require_once ROOT_DIR.'authentication/authenticator.php';

require_once ROOT_DIR.'constants/anchor_text_constants.php';
require_once ROOT_DIR.'constants/links_constants.php';
require_once ROOT_DIR.'constants/metadata_constants.php';
require_once ROOT_DIR.'constants/top_pages_constants.php';
require_once ROOT_DIR.'constants/url_metrics_constants.php';

require_once ROOT_DIR.'services/abstract_service.php';
require_once ROOT_DIR.'services/anchor_text_service.php';
require_once ROOT_DIR.'services/links_service.php';
require_once ROOT_DIR.'services/metadata_service.php';
require_once ROOT_DIR.'services/top_pages_service.php';
require_once ROOT_DIR.'services/url_metrics_service.php';

require_once ROOT_DIR.'utilities/connection_utility.php';
require_once ROOT_DIR.'utilities/database_utility.php';

//Add your accessID here kunugi Account
$AccessID = AccessID;
//Add your secretKey here
$SecretKey = SecretKey;


/*====================
  MAIN ACTIONS
  ====================*/

$authenticator = new Authenticator();
$authenticator->setAccessID(AccessID);
$authenticator->setSecretKey(SecretKey);
$authenticator->setExpiresInterval(420);

if(isset($_GET['accessid']) && !empty($_GET['accessid'])){
    $AccessID = strval($_GET['accessid']);
}
if(isset($_GET['secretkey']) && !empty($_GET['secretkey'])){
    $SecretKey = strval($_GET['secretkey']);
}

$url ="";
$url = htmlspecialchars($_GET['url']);

$password ="";
$password = htmlspecialchars($_GET['password']);

if(!$url || $password != Password ){
	header("HTTP/1.0 404 Not Found");
	exit;
}

$xscope = "page_to_page";
$xfilter = "external+follow";
$xsort = "page_authority";
$xcol = 103079215108; //30;
$xtargetcol = 0;
$xlinkcol = 1;

$options = array(
	"scope"=>$xscope
	,"filters"=>$xfilter
	,"sort"=>$xsort
	,"source_cols"=>$xcol
	,"target_cols"=>$xtargetcol
	,"link_cols"=>$xlinkcol
	,"offset"=>0
	,"limit"=>100
);

$linksService = new LinksService($authenticator);
$response = $linksService->getLinks($url, $options);

//print_r($response);

$row = 0;
$tableData = array();

if(!empty($response)){
	foreach($response as $val){
		$ins = (array)$val;
		$tableData[$row]["uu"] = $ins["uu"];//pageURL
		$tableData[$row]["luuu"] = $url;//リンク元URL
		$tableData[$row]["ltgt"] = $ins["ltgt"];
		$tableData[$row]["lsrc"] = $ins["lsrc"];
		$tableData[$row]["lrid"] = $ins["lrid"];
		$tableData[$row]["pda"] = $ins["pda"];//Domain Authority
		$tableData[$row]["upa"] = $ins["upa"];//Page Authority
		$row++;
	}

	//print_r($tableData);

}

if(isset($tableData)){
	xml_Document($tableData);
}else{
	//xml_Document("-9");
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
		$pref->setAttribute('code', $i);

		foreach($val as $key => $val2){
			$pref->appendChild($dom->createElement($key, $val2));
		}

		$i++;
	}

	//$pref = $prefs->appendChild($dom->createElement('pref'));
	
	//XML を整形（改行・字下げ）して出力
	$dom->formatOutput = true;

	header("Content-Type: text/xml; charset=utf-8");
	echo $dom->saveXML();

}
