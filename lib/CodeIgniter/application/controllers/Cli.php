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
            echo date("Y-m-d H:i:s", time()).' ドメインなしの為、処理途中終了'.PHP_EOL;
        }
        echo date("Y-m-d H:i:s", time()).' END.'.PHP_EOL;
    }
    
/**
 * Moz freeapi のドメイン調査
 *
 * DA・PA・被リンク数を取得し、DBへ格納
 * Status = -1 を対象に
 *
 * @param   integer   $sv     APIサーバーID
 * @param   integer   $num    処理件数
 *
 * @response success -> domains.status = -3, failure -> domains.status = -4
 */
    public function getxml($sv=NULL, $num=10, $debug=FALSE)
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
        if( $sv === NULL )
        {
            $svnum = rand(1,100);
        }
        else
        {
            $svnum = intval($sv);
        }
        echo date("Y-m-d H:i:s", time()).' sv='.$svnum.PHP_EOL;
        
        $apiservers = $this->domain->get_apiserver($svnum);
        echo date("Y-m-d H:i:s", time()).' apiserver='.print_r($apiservers, true).PHP_EOL;

        $cloud_urlmetrics_url = $filename = '';
        if($apiservers['name'])
        {
            $cloud_urlmetrics_url = $apiservers['name'];
        }
        if($apiservers['id'])
        {
            $filename = $apiservers['id'];
        }
        
        
        // 処理
        // 対象ドメインを選定
        $qstatus = 10000 + $filename;
        $updatedata = $whereparam = array();
        $updatedata['status'] = $qstatus;
        $whereparam[] = array('kind' => 'where', 'colname' => 'status', 'value' => -1);
        $this->domain->update_domains($whereparam, $updatedata);
        
        $domainlist = $this->domain->get_domains(NULL, $qstatus, $num, 0);
        
        if( $domainlist )
        {
            foreach($domainlist as $dl)
            {
                $objectURLs = array();
                $objectURLs[] = $dl['domainname'];
                $objectURLs[] = "www.".$dl['domainname'];
                
                foreach($objectURLs as $objecturl)
                {
                    $thissec = time();
                    //$requestUrl = str_replace('##DOMAIN##', $objecturl, $apiurl);
                    // seomoz.
                    //$response = $this->domain->get_response_body($requestUrl);
                    // XML を取得
                    $response_xml = $this->domain->get_xml_object('xml', $cloud_urlmetrics_url, $objecturl, $accessID, $secretKey);
                    
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
                        if(isset($response['uid']))
                        {
                            $insertdata['totalLinks'] = $response["uid"];
                        }
                        if(isset($response['upa']))
                        {
                            $insertdata['pageAuthority'] = $response["upa"];
                        }
                        if(isset($response['pda']))
                        {
                            $insertdata['domainAuthority'] = $response["pda"];
                        }
                        if(isset($response['ueid']))
                        {
                            $insertdata['ueid'] = $response["ueid"];
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
                        $updatedata['status'] = -4;
                    }
                    $whereparam[] = array('kind' => 'where', 'colname' => 'id', 'value' => $dl['id']);
                    $this->domain->update_domains($whereparam, $updatedata);

                    skipexecute:

                    $responsesec = time() - $thissec;
                    // delay 14 sec.
                    if($responsesec < 14)
                    {
                        $delaysec = 14 - $responsesec;
                    }
                    else
                    {
                        $delaysec = 1;
                    }
                    sleep($delaysec);
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
 */
    public function getolxml($sv=NULL, $num=10, $debug=FALSE)
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
        if( $sv === NULL )
        {
            $svnum = rand(1,100);
        }
        else
        {
            $svnum = intval($sv);
        }
        echo date("Y-m-d H:i:s", time()).' sv='.$svnum.PHP_EOL;
        
        $apiservers = $this->domain->get_apiserver($svnum);
        echo date("Y-m-d H:i:s", time()).' apiserver='.print_r($apiservers, true).PHP_EOL;
        $cloud_urlmetrics_url = '';
        if($apiservers['name'])
        {
            $cloud_urlmetrics_url = $apiservers['name'];
        }
        $filename = 0;
        if($apiservers['id'])
        {
            $filename = $apiservers['id'];
        }
        
        // 処理
        // 対象ドメインを選定
        $qstatus = 30000 + $filename;
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
        $this->domain->update_domains($whereparam, $updatedata);
        
        $domainlist = $this->domain->get_domains(NULL, $qstatus, $num, 0);
        echo date("Y-m-d H:i:s", time()).' domainlist,'.count($domainlist).PHP_EOL;
        
        if( $domainlist )
        {
            foreach($domainlist as $dl)
            {
                $objecturl = $dl['domainname'];
                $thissec = time();
                // DAPA からインテグレート
                //$requestUrl = str_replace('##DOMAIN##', $objecturl, $apiurl);
                //echo date("Y-m-d H:i:s", time()).' REQUEST URL : '.$requestUrl.PHP_EOL;
                
                // seomoz.
                //$response = $this->domain->get_response_body($requestUrl);
                // XML を取得
                $response_xml = $this->domain->get_xml_object('ol_xml', $cloud_urlmetrics_url, $objecturl, $accessID, $secretKey);
                
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

                skipexecute:

                $responsesec = time() - $thissec;
                // delay 14 sec.
                if($responsesec < 14)
                {
                    $delaysec = 14 - $responsesec;
                }
                else
                {
                    $delaysec = 1;
                }
                sleep($delaysec);
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
