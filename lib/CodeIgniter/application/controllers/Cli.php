<?php
/**
 * Copyright (c) 2017. HatchBit & Co.
 *
 * @property    Domain_model
 *
 */

if(is_cli() === FALSE)
{
    exit();
}

/**
 * Class Cli
 */
class Cli extends CI_Controller {
    
    public $downloaddir = FILEPATH;
    public $zipfilename = 'PoolDeletingDomainsList.zip';
    public $csvfilename = 'PoolDeletingDomainsList.csv';
    public $txtfilename = 'PoolDeletingDomainsList.txt';
    public $prefix_splitfile = 'SPLIT-';
    
    public function __construct()
    {
        parent::__construct();
        $this->load->helper(array('csv', 'directory', 'file', 'xml'));
        $this->load->model('Domain_model', 'domain', TRUE);
    }
    
    public function index()
    {
        
    }
    
/**
 * 中古ドメインの取得
 *
 * POOL.COMから中古ドメインのリストをダウンロードし、ファイル分割
 * Status 0
 * 
 */
    public function getlist()
    {
        echo date("Y-m-d H:i:s", time()).' START.'.PHP_EOL;
        
        // ZIPファイルがあれば削除
        $zipfilepath = $this->downloaddir . DIRECTORY_SEPARATOR . $this->zipfilename;
        if(file_exists($zipfilepath))
        {
            echo date("Y-m-d H:i:s", time())." delete:".$zipfilepath.PHP_EOL;
            unlink($zipfilepath);
        }
        // CSVファイルがあれば削除
        $csvfilepath = $this->downloaddir . DIRECTORY_SEPARATOR . $this->csvfilename;
        if(file_exists($csvfilepath))
        {
            echo date("Y-m-d H:i:s", time())." delete:".$csvfilepath.PHP_EOL;
            unlink($csvfilepath);
        }
        // TXTファイルがあれば削除
        $txtfilepath = $this->downloaddir . DIRECTORY_SEPARATOR . $this->txtfilename;
        if(file_exists($txtfilepath))
        {
            echo date("Y-m-d H:i:s", time())." delete:".$txtfilepath.PHP_EOL;
            unlink($txtfilepath);
        }
        
        // ZIP ファイルを取ってきて、保存。
        $zipfileurl = 'http://www.pool.com/Downloads/PoolDeletingDomainsList.zip';
        $opts = array(
            'http'=>array(
                'method'=>"GET",
                'header'=>"Accept-language: en\r\n".
                    "User-Agent: Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0)\r\n"
            )
        );
        $context = stream_context_create($opts);
        $zipfile = file_get_contents($zipfileurl, false, $context);
        
        file_put_contents($zipfilepath, $zipfile, LOCK_EX);
        chmod($zipfilepath, 0666);
        
        // ZIP ファイルを解凍
        $zip = new ZipArchive;
        $distFileName = (string) '';
        if($zip->open($zipfilepath) === TRUE){
            $zip->extractTo($this->downloaddir);// ディレクトリに展開
            if(file_exists($csvfilepath)){
                chmod($csvfilepath, 0666);
                $distFileName = $this->csvfilename;
            }
            if(file_exists($txtfilepath)){
                chmod($txtfilepath, 0666);
                $distFileName = $this->txtfilename;
            }
            $zip->close();
            echo date("Y-m-d H:i:s", time())." dist file name : ".$distFileName.PHP_EOL;
        }
        
        // CSVファイルアップロード
        if(file_exists($this->downloaddir . DIRECTORY_SEPARATOR . $distFileName)){
            echo date("Y-m-d H:i:s", time())." file_exists(" . $this->downloaddir . DIRECTORY_SEPARATOR . $distFileName . ")".PHP_EOL;
            //chmod($downloadDir.$csvfilename, 0666);
            //$encode = $_POST['encode'];// ファイルエンコード
            
            // ファイル分割
            $prefix = $this->prefix_splitfile;
            $i = $j = 0;
            $file = new SplFileObject($this->downloaddir . DIRECTORY_SEPARATOR . $distFileName, "r");
            foreach($file as $line_num => $line)
            {
                if($i < 10000 )
                {
                    $i++;
                }
                else
                {
                    $i = 0;
                    $j++;
                }
                $newfile = new SplFileObject($this->downloaddir . DIRECTORY_SEPARATOR . $prefix . $j . '-' . $distFileName, "a");
                $newfile->fwrite($line);
                $newfile = NULL;
            }
            
            //$results = $this->domain->insert_domain_from_csv($this->downloaddir . DIRECTORY_SEPARATOR . $distFileName, "SJIS-WIN");
            
        }
        
        echo date("Y-m-d H:i:s", time()).' END.'.PHP_EOL;
    }
    
/**
 * 中古ドメインの取得
 *
 * 分割されたファイルからドメインをDBへ登録
 * Status 0
 * 
 */
    public function insertfromfiles()
    {
        echo date("Y-m-d H:i:s", time()).' START.'.PHP_EOL;
        $filemap = directory_map($this->downloaddir, 1);
        //var_dump($filemap);
        
        foreach($filemap as $key => $val)
        {
            if(strpos($val, $this->prefix_splitfile) === 0)
            {
                echo date("Y-m-d H:i:s", time()).' FILENAME = '.$val.PHP_EOL;
                $this->domain->insert_domain_from_csv($this->downloaddir . DIRECTORY_SEPARATOR . $val, "SJIS-WIN");
                unlink($this->downloaddir . DIRECTORY_SEPARATOR . $val);
                break;
            }
        }
        echo date("Y-m-d H:i:s", time()).' END.'.PHP_EOL;
    }
    
/**
 * HTTP HEADER STATUS のドメイン調査
 *
 * HTTP HEADER STATUS を取得し、DBへ格納
 * Status -12 (HTTPCODE 100以上600未満)
 * Status -1 (HTTPCODE それ以外)
 * 
 */
    public function getstatus($num=500)
    {
        echo date("Y-m-d H:i:s", time()).' START.'.PHP_EOL;
        
        $domainlist = $this->domain->get_domains(NULL, 0, $num, 0);
        
        if( $domainlist )
        {
            $whereparam = $updatedata = array();
            $idlist = array();
            foreach($domainlist as $dl)
            {
                $idlist[] = $dl['id'];
            }
            unset($dl);
            $whereparam[] = array('kind' => 'where_in', 'colname' =>'id', 'value' => $idlist);
            $this->domain->update_domains($whereparam, array('status' => -9));
            
            foreach($domainlist as $dl)
            {
                $whereparam = $updatedata = array();
                $url = "http://".$dl['domainname'];
                $httpstatus = $this->domain->get_http_statuscode($url);
                echo date("Y-m-d H:i:s", time()).' '.$dl['domainname'].' '.$httpstatus.PHP_EOL;
                
                if(intval($httpstatus) < 600 && intval($httpstatus) >= 100)
                {
                    $newstatus = -12;
                }
                else
                {
                    $newstatus = -1;
                }
                $updatedata['status'] = $newstatus;
                $updatedata['http_code'] = $httpstatus;
                $whereparam[] = array('kind' => 'where', 'colname' => 'id', 'value' => $dl['id']);
                $this->domain->update_domains($whereparam, $updatedata);
            }
            unset($dl);
        }
        else
        {
            $whereparam = array();
            $whereparam[] = array('kind'=>'where', 'colname'=>'http_code', 'value'=>0);
            $checksitelist = $this->domain->get_checksites($whereparam, $num, 0, NULL);

            if( $checksitelist )
            {
                foreach($checksitelist as $dl)
                {
                    $whereparam = $updatedata = array();
                    $url = "http://".$dl['domainName'];
                    $httpstatus = $this->domain->get_http_statuscode($url);
                    echo date("Y-m-d H:i:s", time()).' '.$dl['domainName'].' '.$httpstatus.PHP_EOL;

                    $updatedata['http_code'] = $httpstatus;
                    $whereparam[] = array('kind' => 'where', 'colname' => 'id', 'value' => $dl['id']);
                    $this->domain->update_domains($whereparam, $updatedata, 'domainCheckSites');
                }
                unset($dl);
            }
            else
            {
                echo date("Y-m-d H:i:s", time()).' ドメインなしの為、処理途中終了'.PHP_EOL;
            }

        }
        echo date("Y-m-d H:i:s", time()).' END.'.PHP_EOL;
    }
    
/**
 * Moz freeapi のドメイン調査
 *
 * DA・PA・被リンク数を取得し、DBへ格納
 * Status = -1 を対象に
 *
 * @param   integer    $sv_min  APIサーバーID
 * @param   integer    $sv_max  APIサーバーID
 * @param   integer    $num     処理件数
 *
 * @response success -> domains.status = -3, failure -> domains.status = -4
 */
    public function getxml($sv_min=1, $sv_max=100, $num=1000, $debug=FALSE)
    {
        echo date("Y-m-d H:i:s", time()).' START.'.PHP_EOL;
        
        // SEOMOZ
        //$apiurl = 'http://lsapi.seomoz.com/linkscape/url-metrics/##DOMAIN##?Cols=103079217184&AccessID=##ACCESSID##&Expires=##EXPIRES##&Signature=##SIGNATURE##';
        
        // Get your access id and secret key here: https://moz.com/products/api/keys
        $accessID = "mozscape-1bd70e3419";
        $secretKey = "968e070b8a65f43decda0d60034c134";
        
        // Set your expires times for several minutes into the future.
        // An expires time excessively far in the future will not be honored by the Mozscape API.
        //$expires = time() + 300;
        
        // Put each parameter on a new line.
        //$stringToSign = $accessID."\n".$expires;
        
        // Get the "raw" or binary output of the hmac hash.
        //$binarySignature = hash_hmac('sha1', $stringToSign, $secretKey, true);
        
        // Base64-encode it and then url-encode that.
        //$urlSafeSignature = urlencode(base64_encode($binarySignature));
        
        // build URL
        //$apiurl = str_replace('##ACCESSID##', $accessID, $apiurl);
        //$apiurl = str_replace('##EXPIRES##', $expires, $apiurl);
        //$apiurl = str_replace('##SIGNATURE##', $urlSafeSignature, $apiurl);
        
        echo date("Y-m-d H:i:s", time()).' '.__FILE__ . PHP_EOL;
        
        // APIサーバー
        if($sv_max >= $sv_min)
        {
            $svmax = intval($sv_max);
            $svmin = intval($sv_min);
        }
        elseif($sv_min > $sv_max)
        {
            $svmax = intval($sv_min);
            $svmin = intval($sv_max);
        }
        else
        {
            $svmax = 100;
            $svmin = 1;
        }
        echo date("Y-m-d H:i:s", time()).' SERVER NO. = '.$svmin.' - '.$svmax.PHP_EOL;

        // APIサーバーを取得設定
        $cloud_urlmetrics_urls = array();
        for($i = $svmin; $i <= $svmax; $i++)
        {
        	$param = array();
            $param[] = array('kind'=>'where', 'colname'=>'status', 'value'=>1);
            $param[] = array('kind'=>'order_by', 'colname'=>'id', 'value'=>'DESC');
            $param[] = array('kind'=>'limit', 'colname'=>'limit', 'value'=>'1, '.$i);
            $cloud_urlmetrics_urls[] = $this->domain->get_apiserver($param);
        }        
        $count_cuu = count($cloud_urlmetrics_urls);

        // アクセスIDを取得設定
        $accessides = $this->domain->get_accessid('free', 1);
        $count_acid = count($accessides);

        // 処理
        // 対象ドメインを選定
        $qstatus = 10000;
        $updatedata = $whereparam = array();
        $updatedata['status'] = $qstatus;
        $whereparam[] = array('kind' => 'where', 'colname' => 'status', 'value' => -1);
        $this->domain->update_domains($whereparam, $updatedata, "domains", $num);
        
        $domainlist = $this->domain->get_domains(NULL, $qstatus, $num, 0);
        
        if( $domainlist )
        {
            $counter = 0;
            foreach($domainlist as $dl)
            {
                $objectURLs = array();
                $objectURLs[] = $dl['domainname'];
                $objectURLs[] = "www.".$dl['domainname'];
                
                foreach($objectURLs as $objecturl)
                {
                    echo date("Y-m-d H:i:s", time()).' OBJECT URL = '.$objecturl.PHP_EOL;

                    $thissec = time();
                    //$requestUrl = str_replace('##DOMAIN##', $objecturl, $apiurl);
                    // seomoz.
                    //$response = $this->domain->get_response_body($requestUrl);
                    // XML を取得
                    // $response_xml = $this->domain->get_xml_object('xml', $cloud_urlmetrics_url, $objecturl, $accessID, $secretKey, 'YwwZlCRX');
                    $cloud_urlmetrics_url = "";
                    $counter++;
                    if($counter <= $count_cuu)
                    {
                        $count_index = $counter - 1;
                    }
                    else
                    {
                        $count_index = ($counter % $count_cuu) - 1;
                    }

                    if ( ! empty($cloud_urlmetrics_urls[$count_index]['private_hostname']))
                    {
                        $cloud_urlmetrics_url = $cloud_urlmetrics_urls[$count_index]['private_hostname'];
                    }
                    elseif ( ! empty($cloud_urlmetrics_urls[$count_index]['private_ipv4']))
                    {
                        $cloud_urlmetrics_url = $cloud_urlmetrics_urls[$count_index]['private_ipv4'];
                    }
                    elseif ( ! empty($cloud_urlmetrics_urls[$count_index]['public_hostname']))
                    {
                        $cloud_urlmetrics_url = $cloud_urlmetrics_urls[$count_index]['public_hostname'];
                    }
                    elseif ( ! empty($cloud_urlmetrics_urls[$count_index]['public_ipv4']))
                    {
                        $cloud_urlmetrics_url = $cloud_urlmetrics_urls[$count_index]['public_ipv4'];
                    }
                    else
                    {
                        $cloud_urlmetrics_url = $cloud_urlmetrics_urls[$count_index]['name'];
                    }

                    //$access_id = $cloud_urlmetrics_urls[$count_index]['access_id'];
                    //$secret_key = $cloud_urlmetrics_urls[$count_index]['secret_key'];

                    if($counter <= $count_acid)
                    {
                        $accessidindex = $counter - 1;
                    }
                    else
                    {
                        $accessidindex = ($counter % $count_acid) - 1;
                    }
                    $access_id = $accessides[$accessidindex]['accessid'];
                    $secret_key = $accessides[$accessidindex]['secretkey'];

                    echo date("Y-m-d H:i:s", time()).' CLOUD URL METRICS URL = '.$cloud_urlmetrics_url.PHP_EOL;

                    // delay
                    // 2 秒待つ
                    // usleep(2000000);
                    // usleep(10000);// マイクロ秒

                    // Free access allows you to make one request every ten seconds,
                    // up to 25,000 rows per month.
                    // https://moz.com/products/api/pricing
                    sleep(1);

                    $response_xml = $this->domain->get_xml_object('xml', $cloud_urlmetrics_url, $objecturl, $access_id, $secret_key, 'YwwZlCRX');

                    echo date("Y-m-d H:i:s", time()).' RESPONSE XML = '.print_r($response_xml, true).PHP_EOL;

                    if(strpos($response_xml, 'center>') !== FALSE OR strpos($response_xml, 'DOCTYPE') !== FALSE OR strpos($response_xml, 'ERROR:') !== FALSE OR empty($response_xml))
                    {
                        echo date("Y-m-d H:i:s", time()).' RESPONSE ERROR!'.PHP_EOL;
                        $updatedata['status'] = -4;
                        goto skipexecute1;
                    }

                    // XML を配列に変換
                    $response = xml2array(simplexml_load_string($response_xml));

                    if($debug !== FALSE)
                    {
                        echo date("Y-m-d H:i:s", time()).' response = '.print_r($response, true).PHP_EOL;
                        goto skipexecute;
                    }

                    $updatedata = $whereparam = array();
                    if( ! empty($response) && ! isset($response['error_message']) )
                    {
                        echo date("Y-m-d H:i:s", time()).' RESPONSE,'.json_encode($response).PHP_EOL;
                        $insertdata = array();
                        if(isset($response['linkcheck']))
                        {
                            $res = $response['linkcheck'];
                        }
                        if(isset($res['uid']))
                        {
                            $insertdata['totalLinks'] = $res["uid"];
                        }
                        if(isset($res['upa']))
                        {
                            $insertdata['pageAuthority'] = $res["upa"];
                        }
                        if(isset($res['pda']))
                        {
                            $insertdata['domainAuthority'] = $res["pda"];
                        }
                        if(isset($res['ueid']))
                        {
                            $insertdata['ueid'] = $res["ueid"];
                        }
                        $insertdata['domain_id'] = $dl['id'];
                        $insertdata['domainName'] = $objecturl;
                        $insertdata['linkRootDomain'] = -9;
                        $insertdata['linksMet'] = -9;
                        $insertdata['LostlinksMet'] = 0;
                        $this->domain->insert_checksites($insertdata);
                        
                        $updatedata['mozcheck'] = 1;
                        $updatedata['status'] = -3;
                    }
                    else
                    {
                        echo date("Y-m-d H:i:s", time()).' RESPONSE,'.json_encode($response).PHP_EOL;
                        $updatedata['status'] = -4;
                    }

                    skipexecute1:

                    $whereparam[] = array('kind' => 'where', 'colname' => 'id', 'value' => $dl['id']);
                    $this->domain->update_domains($whereparam, $updatedata);

                    skipexecute:



                }
                unset($objecturl);
            }
            unset($dl);
        }
        else
        {
            echo date("Y-m-d H:i:s", time()).' ドメインなしの為、処理途中終了'.PHP_EOL;
        }
        
        echo date("Y-m-d H:i:s", time()).' END.'.PHP_EOL;
    }
    
/**
 * Moz freeapi の被リンク調査
 *
 * DA18以上・被リンク数5本以上のドメインに対し、被リンクURLを全て取得し、DBへ保存
 *
 * @param   integer    $sv_min  APIサーバーID
 * @param   integer    $sv_max  APIサーバーID
 * @param   integer    $num     処理件数
 *
 */
    public function getolxml($sv_min=101, $sv_max=200, $num=500, $debug=FALSE)
    {
        echo date("Y-m-d H:i:s", time()).' START.'.PHP_EOL;
        
        // SEOMOZ
        //$apiurl = 'http://lsapi.seomoz.com/linkscape/url-metrics/##DOMAIN##?Cols=103079231493&AccessID=##ACCESSID##&Expires=##EXPIRES##&Signature=##SIGNATURE##';
        
        // Get your access id and secret key here: https://moz.com/products/api/keys
        $accessID = "mozscape-1bd70e3419";
        $secretKey = "968e070b8a65f43decda0d60034c134";
        
        // Set your expires times for several minutes into the future.
        // An expires time excessively far in the future will not be honored by the Mozscape API.
        //$expires = time() + 300;
        
        // Put each parameter on a new line.
        //$stringToSign = $accessID."\n".$expires;
        
        // Get the "raw" or binary output of the hmac hash.
        //$binarySignature = hash_hmac('sha1', $stringToSign, $secretKey, true);
        
        // Base64-encode it and then url-encode that.
        //$urlSafeSignature = urlencode(base64_encode($binarySignature));
        
        // build URL
        //$apiurl = str_replace('##ACCESSID##', $accessID, $apiurl);
        //$apiurl = str_replace('##EXPIRES##', $expires, $apiurl);
        //$apiurl = str_replace('##SIGNATURE##', $urlSafeSignature, $apiurl);
        
        echo date("Y-m-d H:i:s", time()).' '.__FILE__ . PHP_EOL;

        // APIサーバー
        if($sv_max >= $sv_min)
        {
            $svmax = intval($sv_max);
            $svmin = intval($sv_min);
        }
        elseif($sv_min > $sv_max)
        {
            $svmax = intval($sv_min);
            $svmin = intval($sv_max);
        }
        else
        {
            $svmax = 200;
            $svmin = 101;
        }
        echo date("Y-m-d H:i:s", time()).' SERVER NO. = '.$svmin.' - '.$svmax.PHP_EOL;

        // APIサーバーを取得設定
        $cloud_urlmetrics_urls = array();
        for($i = $svmin; $i <= $svmax; $i++)
        {
            $param = array();
            $param[] = array('kind'=>'where', 'colname'=>'status', 'value'=>1);
            $param[] = array('kind'=>'order_by', 'colname'=>'id', 'value'=>'DESC');
            $param[] = array('kind'=>'limit', 'colname'=>'limit', 'value'=>'1, '.$i);
            $cloud_urlmetrics_urls[] = $this->domain->get_apiserver($param);
        }
        $count_cuu = count($cloud_urlmetrics_urls);

        // アクセスIDを取得設定
        $accessides = $this->domain->get_accessid('free', 1);
        $count_acid = count($accessides);

        // 処理
        // 対象ドメインを選定
        $qstatus = 30000;
        $whereparam = array();
        $whereparam[] = array('kind' => 'where', 'colname' => 'domainAuthority >=', 'value' => 18);
        $whereparam[] = array('kind' => 'where', 'colname' => 'ueid >=', 'value' => 5);
        $whereparam[] = array('kind' => 'where', 'colname' => 'linksMet', 'value' => -9);
        $checklist = $this->domain->get_checksites($whereparam, $num, 0);
        
        // ステータスを更新
        $domain_ids = array();
        foreach($checklist as $key => $val)
        {
            $domain_ids[] = $val['domain_id'];
        }
        unset($key,$val);
        $domain_ids = array_unique($domain_ids, SORT_NUMERIC);
        
        if(count($domain_ids) === 0)
        {
            echo date("Y-m-d H:i:s", time()).' ドメインなしの為、処理途中終了'.PHP_EOL;
            echo date("Y-m-d H:i:s", time()).' END.'.PHP_EOL;
            exit();
        }
        
        // ステータスを一時変更
        $updatedata = $whereparam = array();
        $updatedata['status'] = $qstatus;
        $whereparam[] = array('kind' => 'where_in', 'colname' => 'id', 'value' => $domain_ids);
        $this->domain->update_domains($whereparam, $updatedata, "domains", $num);
        
        $domainlist = $this->domain->get_domains(NULL, $qstatus, $num, 0);
        echo date("Y-m-d H:i:s", time()).' domainlist,'.count($domainlist).PHP_EOL;
        
        if( $domainlist )
        {
            $counter = 0;
            foreach($domainlist as $dl)
            {
                $objecturl = $dl['domainname'];
                $thissec = time();
                echo date("Y-m-d H:i:s", time()).' OBJECT URL = '.$objecturl.PHP_EOL;

                // DAPA からインテグレート
                //$requestUrl = str_replace('##DOMAIN##', $objecturl, $apiurl);
                //echo date("Y-m-d H:i:s", time()).' REQUEST URL : '.$requestUrl.PHP_EOL;
                
                // seomoz.
                //$response = $this->domain->get_response_body($requestUrl);
                // XML を取得
                //$response_xml = $this->domain->get_xml_object('ol_xml', $cloud_urlmetrics_url, $objecturl, $accessID, $secretKey, 'YwwZlCRX');
                $counter++;
                if($counter <= $count_cuu)
                {
                    $count_index = $counter - 1;
                }
                else
                {
                    $count_index = ($counter % $count_cuu) - 1;
                }

                if ( ! empty($cloud_urlmetrics_urls[$count_index]['private_hostname']))
                {
                    $cloud_urlmetrics_url = $cloud_urlmetrics_urls[$count_index]['private_hostname'];
                }
                elseif ( ! empty($cloud_urlmetrics_urls[$count_index]['private_ipv4']))
                {
                    $cloud_urlmetrics_url = $cloud_urlmetrics_urls[$count_index]['private_ipv4'];
                }
                elseif ( ! empty($cloud_urlmetrics_urls[$count_index]['public_hostname']))
                {
                    $cloud_urlmetrics_url = $cloud_urlmetrics_urls[$count_index]['public_hostname'];
                }
                elseif ( ! empty($cloud_urlmetrics_urls[$count_index]['public_ipv4']))
                {
                    $cloud_urlmetrics_url = $cloud_urlmetrics_urls[$count_index]['public_ipv4'];
                }
                else
                {
                    $cloud_urlmetrics_url = $cloud_urlmetrics_urls[$count_index]['name'];
                }

                //$access_id = $cloud_urlmetrics_urls[$count_index]['access_id'];
                //$secret_key = $cloud_urlmetrics_urls[$count_index]['secret_key'];

                if($counter <= $count_acid)
                {
                    $accessidindex = $counter - 1;
                }
                else
                {
                    $accessidindex = ($counter % $count_acid) - 1;
                }
                $access_id = $accessides[$accessidindex]['accessid'];
                $secret_key = $accessides[$accessidindex]['secretkey'];
                echo date("Y-m-d H:i:s", time()).' CLOUD URL METRICS URL = '.$cloud_urlmetrics_url.PHP_EOL;

                // delay
                // 2 秒待つ
                // usleep(2000000);
                // usleep(10000);// マイクロ秒

                // Free access allows you to make one request every ten seconds,
                // up to 25,000 rows per month.
                // https://moz.com/products/api/pricing
                sleep(1);

                $response_xml = $this->domain->get_xml_object('ol_xml', $cloud_urlmetrics_url, $objecturl, $access_id, $secret_key, 'YwwZlCRX');

                echo date("Y-m-d H:i:s", time()).' RESPONSE XML = '.print_r($response_xml, true);

                if(strpos($response_xml, 'center>') !== FALSE OR strpos($response_xml, 'DOCTYPE') !== FALSE OR strpos($response_xml, 'ERROR:') !== FALSE OR empty($response_xml))
                {
                    echo date("Y-m-d H:i:s", time()).' RESPONSE ERROR!'.PHP_EOL;
                    goto skipexecute1;
                }

                // XML を配列に変換
                $response = xml2array(simplexml_load_string($response_xml));

                if($debug !== FALSE)
                {
                    echo date("Y-m-d H:i:s", time()).' response = '.print_r($response, true).PHP_EOL;
                    goto skipexecute;
                }

                if( ! empty($response) && ! isset($response['error_message']) )
                {
                    foreach($response as $val)
                    {
                        echo date("Y-m-d H:i:s", time()).' RESPONSE,'.json_encode($val).PHP_EOL;
                        $insertdata = array();
                        if(isset($val['ut']))
                        {
                            $insertdata['pageTitle'] = $val['ut'];
                        }
                        else
                        {
                            $insertdata['pageTitle'] = 'NO TITLE.';
                        }
                        if(isset($val['uu']))
                        {
                            $insertdata['pageURL'] = $val['uu'];
                        }
                        if(isset($val['upa']))
                        {
                            $insertdata['pageAuthority'] = $val['upa'];
                        }
                        if(isset($val['pda']))
                        {
                            $insertdata['domainAuthority'] = $val['pda'];
                        }
                        if(isset($val['umrp']))
                        {
                            $insertdata['mozRank'] = $val['umrp'];
                        }
                        $insertdata['domain_id'] = $dl['id'];
                        $insertdata['domainName'] = $objecturl;
                        $insertdata['pageRank'] = -9;
                        $this->domain->insert_checkdomains($insertdata);
                    }
                    unset($val);
                    
                    // UPDATE domainCheckSites.linkMet
                    $linkMets = count($response);
                    $updatedata = $whereparam = array();
                    $updatedata['linksMet'] = $linkMets;
                    $whereparam[] = array('kind' => 'where', 'colname' => 'domain_id', 'value' => $dl['id']);
                    $this->domain->update_domains($whereparam, $updatedata, "domainCheckSites");
                }

                skipexecute1:

                skipexecute:



            }
            unset($dl);
        }
        else
        {
            echo date("Y-m-d H:i:s", time()).' ドメインなしの為、処理途中終了'.PHP_EOL;
        }
        echo date("Y-m-d H:i:s", time()).' END.'.PHP_EOL;
    }
}
