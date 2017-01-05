<?php

class Domain_model extends CI_Model {
    
    public $title;
    public $content;
    public $date;
    
    public function __construct()
    {
        // CI_Model constructor の呼び出し
        parent::__construct();
    }
    
    public function extract_zip($downloadfile=NULL, $downloaddir=NULL)
    {
        $zip = new ZipArchive;
    }
    
    public function download_zip_from_pool($downloadurl=NULL, $opts=NULL, $downloadfile=NULL)
    {
        $context = stream_context_create($opts);
        $zipfile = file_get_contents($downloadurl, false, $context);
        file_put_contents($downloadfile, $zipfile, LOCK_EX);
        chmod($downloadfile, 0666);
        return $downloadfile;
    }
    
    public function insert_domain_from_csv($filepath="", $encode="SJIS-WIN")
    {
        $results = array();
        setlocale(LC_ALL, 'ja_JP');
        $fp = fopen($filepath, "r");
        $inserted = array();
        $this->db->trans_start();
        while(($line = fgetcsv($fp, NULL, ',', '"')) !== FALSE)
        {
            $encto = mb_internal_encoding();
            //$this->db->select('count(id)');
            //$this->db->where('domainname', $line[0]);
            //$query = $this->db->get('domains');
            //$test = $query->row();
            
            $sql = "SELECT COUNT(id) AS counts FROM domains WHERE domainname = ".$this->db->escape($line[0]);
            $query = $this->db->query($sql);
            $test = $query->row();
            
            if( isset($test->counts) && $test->counts > 0)
            {
                continue;
            }
            else
            {
                // 文字コードを変換
                mb_convert_variables($encto, $encode, $line);
                
                $domainname = $line[0];
                
                //A列がcom/net/org/info/biz 以外を除き、全てDBへ入れる
                $enableTLDs = array("com","net","org","info","biz","jp");
                $domainsplits = explode(".", $domainname);
                $tld = end($domainsplits);
                if( array_search($tld, $enableTLDs) === FALSE)
                {
                    continue;
                }
                
                //・B列が当日以前の物は削除
                $nowDateStr = time();
                $thisTime = strtotime(substr($line[1], 0, 19));
                if($thisTime < time())
                {
                    continue;
                }
                $thisDate = date("Y-m-d H:i:s", $thisTime);
                
                // エラー回避したのでDB登録
                $insertdata = array(
                    'domainname' => $domainname,
                    'expiredatetime' => $thisDate,
                    'status' => 0,
                    'memo' => implode(",", $line),
                    'insertdatetime' => date("Y-m-d H:i:s"),
                );
                $results[] = $insertdata;
                $this->db->insert('domains', $insertdata);
                $inserted[] = $domainname;
            }
        }
        $this->db->trans_complete();
        
        // 結果
        /*
        $results = array();
        if(count($inserted) > 0)
        {
            $this->db->where_in('domainname', $inserted);
            $query = $this->db->get('domains');
            foreach ($query->result_array() as $row)
            {
                $results[] = $row;
            }
        }
        */
        return $results;
    }
    
    public function insert_checksites($insertdata=NULL)
    {
        if($insertdata == NULL)
        {
            return FALSE;
        }
        else
        {
            $this->db->insert('domainCheckSites', $insertdata);
            return $this->db->insert_id();
        }
    }
    
    public function insert_checkdomains($insertdata=NULL)
    {
        if($insertdata == NULL)
        {
            return FALSE;
        }
        else
        {
            $this->db->insert('domainLinks', $insertdata);
            return $this->db->insert_id();
        }
    }
    
    public function get_domains($domainname=NULL, $status=NULL, $limit=NULL, $offset=0)
    {
        $results = array();
        if( $status !== NULL )
        {
            $this->db->where('status', $status);
        }
        if( ! empty($domainname))
        {
            if(is_array($domainname))
            {
                $this->db->where_in('domainname', $domainname);
            }
            else
            {
                $this->db->where('domainname', $domainname);
            }
        }
        if( ! empty($limit))
        {
            $this->db->limit($limit, $offset);
        }
        $query = $this->db->get('domains');
        foreach ($query->result_array() as $row)
        {
            $results[] = $row;
        }
        return $results;
    }
    
    public function get_results($whereparam=NULL, $limit=NULL, $offset=0, $sort=NULL)
    {
        $results = array();
        
        $this->db->select('
            domains.id AS domain_id, 
            domains.domainname, 
            domains.expiredatetime, 
            domains.status, 
            domains.http_code, 
            domains.mozcheck, 
            domains.insertdatetime, 
            domainCheckSites.id AS cs_id, 
            domainCheckSites.domainName AS url, 
            domainCheckSites.http_code AS cs_http_code, 
            domainCheckSites.pageTitle, 
            domainCheckSites.pageAuthority, 
            domainCheckSites.domainAuthority, 
            domainCheckSites.linkRootDomain, 
            domainCheckSites.totalLinks, 
            domainCheckSites.linksMet, 
            domainCheckSites.ueid AS ueid, 
            domainCheckSites.LostlinksMet,
            domainCheckSites.pageRank AS pageRank', FALSE);
        $this->db->from('domains');
        
        // 特定のデータを探す
        foreach($whereparam as $val)
        {
            $colname = $val['colname'];
            $colvalue = $val['value'];
            
            switch($val['kind'])
            {
                case 'or_like':
                    $this->db->or_like($colname, $colvalue, 'both');
                break;
                
                case 'like':
                    $this->db->like($colname, $colvalue, 'both');
                break;
                
                case 'or_where_not_in':
                    $this->db->or_where_not_in($colname, $colvalue);
                break;
                
                case 'where_not_in':
                    $this->db->where_not_in($colname, $colvalue);
                break;
                
                case 'or_where_in':
                    $this->db->or_where_in($colname, $colvalue);
                break;
                
                case 'where_in':
                    $this->db->where_in($colname, $colvalue);
                break;
                
                case 'or_where':
                    $this->db->or_where($colname, $colvalue);
                break;
                
                case 'where':
                default:
                    $this->db->where($colname, $colvalue);
                break;
            }
        }
        unset($val);
        
        $this->db->join('domainCheckSites', 'domainCheckSites.domain_id = domains.id', 'inner');
        
        // 結果の並び替え
        if( ! empty($sort) )
        {
            $this->db->order_by($sort);
        }
        
        // 結果を制限
        if( ! empty($limit))
        {
            $this->db->limit($limit, $offset);
        }
        
        // クエリ実行
        //var_dump($this->db->get_compiled_select(NULL, FALSE));
        $query = $this->db->get();
        
        // 結果
        foreach ($query->result_array() as $row)
        {
            $results[] = $row;
        }
        return $results;
    }
    
    public function get_checksites($whereparam=NULL, $limit=NULL, $offset=0)
    {
        $results = array();
        if( $whereparam !== NULL )
        {
            foreach($whereparam as $val)
            {
                $colname = $val['colname'];
                $colvalue = $val['value'];
                
                switch($val['kind'])
                {
                    case 'or_where':
                        $this->db->or_where($colname, $colvalue);
                    break;
                    
                    case 'where_in':
                        $this->db->where_in($colname, $colvalue);
                    break;
                    
                    case 'where':
                    default:
                        $this->db->where($colname, $colvalue);
                    break;
                }
            }
            unset($val);
        }
        if( ! empty($limit))
        {
            $this->db->limit($limit, $offset);
        }
        $this->db->from('domainCheckSites');
        //$this->db->join('domains', 'domains.id = domainCheckSites.domain_id');
        //$this->db->select('domain_id, domainname, colname, coltype, colvalue, colnum, status');
        $query = $this->db->get();
        foreach ($query->result_array() as $row)
        {
            $results[] = $row;
        }
        return $results;
    }
    
    public function get_count_domains($whereparam=NULL, $tablename='domains')
    {
        $counts = 0;
        $this->db->select('count(id) as counts');
        if( $whereparam !== NULL )
        {
            foreach($whereparam as $val)
            {
                $colname = $val['colname'];
                $colvalue = $val['value'];
                
                switch($val['kind'])
                {
                    case 'or_where':
                        $this->db->or_where($colname, $colvalue);
                    break;
                    
                    case 'where_in':
                        $this->db->where_in($colname, $colvalue);
                    break;
                    
                    case 'where':
                    default:
                        $this->db->where($colname, $colvalue);
                    break;
                }
            }
            unset($val);
        }
        $this->db->from($tablename);
        $query = $this->db->get();
        $row = $query->row();
        $counts = $row->counts;
        return $counts;
    }
    
    public function update_domains($whereparam=NULL, $updatedata=NULL, $tablename="domains")
    {
        if( $whereparam !== NULL )
        {
            foreach($whereparam as $val)
            {
                $colname = $val['colname'];
                $colvalue = $val['value'];
                
                switch($val['kind'])
                {
                    case 'where_in':
                        $this->db->where_in($colname, $colvalue);
                    break;
                    
                    case 'where':
                    default:
                        $this->db->where($colname, $colvalue);
                    break;
                }
            }
            unset($val);
        }
        $this->db->update($tablename, $updatedata);
    }
    
    public function get_http_statuscode($url=NULL)
    {
        if($url == NULL)
        {
            return FALSE;
        }
        
        $return = "";
        $agent = "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1)";
        
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
    
    public function get_apiserver($svnum=NULL)
    {
        if( ! $svnum)
        {
            $svnum = rand(1,100);
        }
        $this->db->from('apisv');
        $this->db->where('id', $svnum);
        $query = $this->db->get();
        $row = $query->row_array();
        if (isset($row))
        {
            $result['id'] = $row['id'];
            $result['name'] = $row['name'];
        }
        return $result;
    }
    
    public function get_response_body($requesturi=NULL)
    {
        if($requesturi == NULL)
        {
            return FALSE;
        }
        // cURL options
        $options = array(
            CURLOPT_URL            => $requesturi,// 取得する URL 。 curl_init() でセッションを 初期化する際に指定することも可能です。
            CURLOPT_HEADER         => FALSE,// TRUE を設定すると、ヘッダの内容も出力します。
            CURLOPT_NOBODY         => FALSE,// TRUE を設定すると、出力から本文を削除します。 リクエストメソッドは HEAD となります。これを FALSE に変更してもリクエストメソッドは GET には変わりません。
            CURLOPT_RETURNTRANSFER => TRUE,// TRUE を設定すると、 curl_exec() の返り値を 文字列で返します。通常はデータを直接出力します。
            CURLOPT_FRESH_CONNECT  => TRUE,// TRUE を設定すると、キャッシュされている接続を利用せずに 新しい接続を確立します。
            //CURLOPT_USERAGENT      => $agent,// HTTP リクエストで使用される "User-Agent: " ヘッダの内容。
            //CURLOPT_COOKIEFILE     => $cookiefile
        );
        // new cURL resource
        $ch = curl_init($requesturi);
        curl_setopt_array($ch, $options);
        // execute.
        $response = curl_exec($ch);
        // close.
        curl_close($ch);
        
        $results = json_decode($response, TRUE);
        return $results;
    }
    
    public function delete_domains($wherestring=NULL)
    {
        $sql = "DELETE FROM `domains`,`domainLinks`,`domainCheckSites`
        FROM `domains`
        LEFT JOIN `domainCheckSites` ON `domainCheckSites`.`domain_id` = `domains`.`id`
        LEFT JOIN `domainLinks` ON `domainLinks`.`domain_id` = `domains`.`id`";
        
        if( $wherestring !== NULL )
        {
            $sql .= "WHERE ".$wherestring;
        }
        
        $this->db->trans_start();
        $this->db->query($sql);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE)
        {
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }
    
    public function truncate_domains()
    {
        $this->db->trans_start();
        $this->db->query('TRUNCATE `domainLinks`');
        $this->db->query('TRUNCATE `domainCheckSites`');
        $this->db->query('TRUNCATE `domains`');
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE)
        {
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }
}