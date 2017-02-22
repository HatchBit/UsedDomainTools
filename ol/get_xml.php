<?php
/* 
 * get_xml.php
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

if(isset($_GET['accessid']) && !empty($_GET['accessid'])){
    $AccessID = strval($_GET['accessid']);
}
if(isset($_GET['secretkey']) && !empty($_GET['secretkey'])){
    $SecretKey = strval($_GET['secretkey']);
}

$password ="";
$password = strval($_GET['password']);

$url ="";
$url = htmlspecialchars($_GET['url']);

if(!$url || $password != Password ){
    header("HTTP/1.0 400 Bad Request");
    echo "<h1>400 Bad Request</h1>";
    exit;
}


/*====================
  MAIN ACTIONS
  ====================*/

$authenticator = new Authenticator();
$authenticator->setAccessID(AccessID);
$authenticator->setSecretKey(SecretKey);
$authenticator->setExpiresInterval(300);


// http://apiwiki.moz.com/link-metrics
//$xscope = "page_to_page";
//$xfilter = "external+follow";
//$xsort = "page_authority";
//$xcol = 103079215108; //30;
//$xtargetcol = 0;
//$xlinkcol = 2;
//
//$options = array(
//	"scope"=>$xscope
//	,"filters"=>$xfilter
//	,"sort"=>$xsort
//	,"source_cols"=>$xcol
//	,"target_cols"=>$xtargetcol
//	,"link_cols"=>$xlinkcol
//	,"offset"=>0
//	,"limit"=>100
//);

//$linksService = new LinksService($authenticator);
//$response = $linksService->getLinks($url, $options);

$col = 0;
$col += 1;//Title	1	ut	The title of the page, if available	yes
$col += 4;//Canonical URL	4	uu	The canonical form of the URL	yes
//$col += 8;//Subdomain	8	ufq	The subdomain of the URL (for example, apiwiki.moz.com) no
//$col += 16;//Root Domain	16	upl	The root domain of the URL (for example, moz.com)	no
$col += 32;//External Links	32	ueid	The number of external equity links to the URL	yes
//$col += 64;//Subdomain External Links	64	feid	The number of external equity links to the subdomain of the URL no
//$col += 128;//Root Domain External Links	128	peid	The number of external equity links to the root domain of the URL	no
//$col += 256;//Equity Links	256	ujid	The number of equity links (internal or external) to the URL	no
//$col += 512;//Subdomains Linking	512	uifq	The number of subdomains with any pages linking to the URL	no
//$col += 1024;//Root Domains Linking	1024	uipl	The number of root domains with any pages linking to the URL	no
$col += 2048;//Links	2048	uid	The number of links (equity or nonequity or not, internal or external) to the URL	yes
//$col += 4096;//Subdomain Subdomains Linking	4096	fid	The number of subdomains with any pages linking to the subdomain of the URL	no
//$col += 8192;//Root Domain Root Domains Linking	8192	pid	The number of root domains with any pages linking to the root domain of the URL	no
$col += 16384;//MozRank	16384	umrp umrr	The MozRank of the URL, in both the normalized 10-point score (umrp) and the raw score (umrr)	yes
$col += 32768;//Subdomain MozRank	32768	fmrp fmrr	The MozRank of the subdomain of the URL, in both the normalized 10-point score (fmrp) and the raw score (fmrr)	yes
//$col += 65536;//Root Domain MozRank	65536	pmrp pmrr	The MozRank of the Root Domain of the URL, in both the normalized 10-point score (pmrp) and the raw score (pmrr) no
//$col += 131072;//MozTrust	131072	utrp utrr	The MozTrust of the URL, in both the normalized 10-point score (utrp) and the raw score (utrr)	no
//$col += 262144;//Subdomain MozTrust	262144	ftrp ftrr	The MozTrust of the subdomain of the URL, in both the normalized 10-point score (ftrp) and the raw score (ftrr) no
//$col += 524288;//Root Domain MozTrust	524288	ptrp ptrr	The MozTrust of the root domain of the URL, in both the normalized 10-point score (ptrp) and the raw score (ptrr) no
//$col += 1048576;//External MozRank	1048576	uemrp uemrr	The portion of the URL's MozRank coming from external links, in both the normalized 10-point score (uemrp) and the raw score (uemrr). no
//$col += 2097152;//Subdomain External Link Equity	2097152	fejp fejr	The portion of the MozRank of all pages on the subdomain coming from external links, in both the normalized 10-digit score (pejp) and the raw score	no Root Domain External Link Equity	4194304	pejp pejr	The portion of the MozRank of all pages on the root domain coming from external links, in both the normalized 10-digit score (pejp) and the raw source	no
//$col += 8388608;//Subdomain Link Equity	8388608	fjp fjr	The MozRank of all pages on the subdomain combined, in both the normalized 10-point score (fjp) and the raw score (fjr)	no
//$col += 16777216;//Root Domain Link Equity	16777216	pjp pjr	The MozRank of all pages on the root domain combined, in both the normalized 10-point score (pjp) and the raw score (pjr)	no
$col += 536870912;//HTTP Status Code	536870912	us	The HTTP status code recorded by Mozscape for this URL, if available	yes
//$col += 4294967296;//Links to Subdomain	4294967296	fuid	The total number of links (including internal and nofollow links) to the subdomain of the URL	no
//$col += 8589934592;//Links to Root Domain	8589934592	puid	The total number of links, including internal and nofollow links, to the root domain of the URL	no
//$col += 17179869184;//Root Domains Linking to Subdomain	17179869184	fipl	The number of root domains with at least one link to the subdomain of the URL	no
$col += 34359738368;//Page Authority	34359738368	upa	A normalized 100-point score representing the likelihood of a page to rank well in search engine results	yes
$col += 68719476736;//Domain Authority	68719476736	pda	A normalized 100-point score representing the likelihood of a domain to rank well in search engine results	yes

$urlMetrics = new URLMetricsService($authenticator);
$response = $urlMetrics->getUrlMetrics($url, $col);
//print_r($response);
/*
array(11) {
	["fmrp"]=> float(6.5995981714117)
	["fmrr"]=> float(2.8178521251086E-7)
	["pda"]=> float(98.278061284217)
	["ueid"]=> int(344768)
	["uid"]=> int(353425)
	["umrp"]=> float(7.5806892024037)
	["umrr"]=> float(1.1041761900322E-6)
	["upa"]=> float(90.625236283632)
	["us"]=> int(301)
	["ut"]=> string(0) ""
	["uu"]=> string(12) "yahoo.co.jp/"
}
*/

$row = 0;
$tableData = array();

if(!empty($response)){
	//var_dump($response);exit();
	foreach($response as $key => $val){
		$tableData[$url][$key] = $val;
	}
	//print_r($tableData);
}
unset($key,$val);

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

	foreach($tableData as $key => $val){
		// code 属性の追加
		$pref = $prefs->appendChild($dom->createElement('linkcheck'));
		//$pref->setAttribute('url', $key);

		foreach($val as $key2 => $val2){
			$pref->appendChild($dom->createElement($key2, $val2));
		}
        unset($key2,$val2);

		$i++;
	}
	unset($key,$val);
	
	//$pref = $prefs->appendChild($dom->createElement('pref'));
	
	//XML を整形（改行・字下げ）して出力
	$dom->formatOutput = true;

	return $dom->saveXML();

}
