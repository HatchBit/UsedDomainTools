<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Result extends CI_Controller {

    /**
     * Index Page for this controller.
     *
     * Maps to the following URL
     * 		http://example.com/index.php/welcome
     *	- or -
     * 		http://example.com/index.php/welcome/index
     *	- or -
     * Since this controller is set as the default controller in
     * config/routes.php, it's displayed at http://example.com/
     *
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/welcome/<method_name>
     * @see https://codeigniter.com/user_guide/general/urls.html
     */
    
    private $data = array();
    private $cookie_names = array('','','','','','','','','','','','','','','','','','','','','','','','','');
    
    public function __construct()
    {
        parent::__construct();
        $this->load->helper(array('form', 'url', 'csv', 'download', 'cookie'));
        $this->load->model('Domain_model', 'domain', TRUE);
        
        $this->_counter();
        
        $cookieLimit = time() + 60 * 60 * 24 * 90;// 90日

        /*

        if( get_cookie('startYear') === NULL)
        {
            set_cookie('startYear', 2010, $cookieLimit);
        }
        if( get_cookie('startMonth') === NULL)
        {
            set_cookie('startMonth', 1, $cookieLimit);
        }
        if( get_cookie('startDate') === NULL)
        {
            set_cookie('startDate', 1, $cookieLimit);
        }
        if( get_cookie('endYear') === NULL)
        {
            set_cookie('endYear', date("Y"), $cookieLimit);
        }
        if( get_cookie('endMonth') === NULL)
        {
            set_cookie('endMonth', date("n"), $cookieLimit);
        }
        if( get_cookie('endDate') === NULL)
        {
            set_cookie('endDate', date("j"), $cookieLimit);
        }
        if( get_cookie('pageAuthorityCheck') === NULL)
        {
            set_cookie('pageAuthorityCheck', "on", $cookieLimit);
        }
        if( get_cookie('pageAuthorityMin') === NULL)
        {
            set_cookie('pageAuthorityMin', 0, $cookieLimit);
        }
        if( get_cookie('pageAuthorityMax') === NULL)
        {
            set_cookie('pageAuthorityMax', 100, $cookieLimit);
        }
        if( get_cookie('domainAuthorityCheck') === NULL)
        {
            set_cookie('domainAuthorityCheck', "on", $cookieLimit);
        }
        if( get_cookie('domainAuthorityMin') === NULL)
        {
            set_cookie('domainAuthorityMin', 0, $cookieLimit);
        }
        if( get_cookie('domainAuthorityMax') === NULL)
        {
            set_cookie('domainAuthorityMax', 100, $cookieLimit);
        }
        if( get_cookie('totalLinksCheck') === NULL)
        {
            set_cookie('totalLinksCheck', "on", $cookieLimit);
        }
        if( get_cookie('totalLinksMin') === NULL)
        {
            set_cookie('totalLinksMin', 0, $cookieLimit);
        }
        if( get_cookie('totalLinksMax') === NULL)
        {
            set_cookie('totalLinksMax', 100000, $cookieLimit);
        }
        if( get_cookie('linkRootDomainCheck') === NULL)
        {
            set_cookie('linkRootDomainCheck', "on", $cookieLimit);
        }
        if( get_cookie('linkRootDomainMin') === NULL)
        {
            set_cookie('linkRootDomainMin', -9, $cookieLimit);
        }
        if( get_cookie('linkRootDomainMax') === NULL)
        {
            set_cookie('linkRootDomainMax', 100000, $cookieLimit);
        }
        if( get_cookie('seomozLinkCheck') === NULL)
        {
            set_cookie('seomozLinkCheck', "on", $cookieLimit);
        }
        if( get_cookie('seomozLinkMin') === NULL)
        {
            set_cookie('seomozLinkMin', 0, $cookieLimit);
        }
        if( get_cookie('seomozLinkMax') === NULL)
        {
            set_cookie('seomozLinkMax', 100000, $cookieLimit);
        }
        if( get_cookie('ueidCheck') === NULL)
        {
            set_cookie('ueidCheck', "on", $cookieLimit);
        }
        if( get_cookie('ueidMin') === NULL)
        {
            set_cookie('ueidMin', 0, $cookieLimit);
        }
        if( get_cookie('ueidMax') === NULL)
        {
            set_cookie('ueidMax', 100, $cookieLimit);
        }
        if( get_cookie('displaymax') === NULL)
        {
            set_cookie('displaymax', 1000, $cookieLimit);
        }
        //$this->data['cookie'] = $_COOKIE;
         */
    }
    
    public function index()
    {
        $this->load->view('result', $this->data);
    }

    public function linkmets($domain_id = NULL)
    {
        $results = array();

        $param = array();
        $param[] = array('kind'=>'where', 'colname'=>'domain_id', 'value'=>$domain_id);
        $param[] = array('kind'=>'order_by', 'colname'=>'id', 'value'=>'DESC');
        $results = $this->domain->get_domainLinks($param);

        $this->data['results'] = $results;
        $this->load->view('result-linkmets', $this->data);
    }

    public function searchlist($n=NULL)
    {
        switch($n)
        {
            // 被リンクチェック数
            case 5:
                $params = array(
                    'whereparam' => array(
                        'kind' => 'where',
                        'colname' => 'domains.status',
                        'value' => 0 
                    )
                );
                break;
            // SEOmoz Link Metrics チェック数
            case 4:
                $params = array(
                    'whereparam' => array(
                        array(
                            'kind' => 'or_where',
                            'colname' => 'domains.http_code',
                            'value' => -9
                        ),
                        array(
                            'kind' => 'or_where',
                            'colname' => 'domains.http_code >',
                            'value' => 1000
                        )
                    )
                );
                break;
            // SEOmoz Site Metrics チェック数
            case 3:
                $params = array(
                    'whereparam' => array(
                        array(
                            'kind' => 'where',
                            'colname' => 'domainCheckSites.totalLinks >',
                            'value' => 0 
                        ),
                        array(
                            'kind' => 'where',
                            'colname' => 'domains.status <>',
                            'value' => 0 
                        )
                    )
                );
                break;
            // HTTPステータスチェック数
            case 2:
                $params = array(
                    'whereparam' => array(
                        array(
                            'kind' => 'where',
                            'colname' => 'domains.mozcheck',
                            'value' => 1 
                        ),
                        array(
                            'kind' => 'where',
                            'colname' => 'domains.status <>',
                            'value' => 0 
                        )
                    )
                );
                break;
            // 処理待ちのドメイン数
            case 1:
                $params = array(
                    'whereparam' => array(
                        array(
                            'kind' => 'where',
                            'colname' => 'domains.status <>',
                            'value' => 0 
                        )
                    )
                );
                break;
            // 登録されているドメイン数
            case 0:
            default:
                $params = array(
                    'whereparam' => array(
                        array(
                            'kind' => 'where',
                            'colname' => 'domains.status',
                            'value' => 0 
                        )
                    )
                );
                break;
        }
        
        // 検索クエリー
        $results = $this->domain->get_results($params['whereparam']);
        
        $this->data['searchResults'] = $results;
        
        $this->load->view('result', $this->data);
    }
    
    public function search()
    {
        // COOKIEから検索パラメータ生成
        $params = $this->_set_param();
        
        // 検索クエリー
        $results = $this->domain->get_results($params['whereparam'], $params['limit'], $params['offset'], $params['sort']);
        
        $this->data['searchResults'] = $results;
        
        $this->load->view('result', $this->data);
    }
    
    public function download()
    {
        // COOKIEから検索パラメータ生成
        $params = $this->_set_param();
        
        // 検索クエリー
        $results = $this->domain->get_results($params['whereparam'], $params['limit'], $params['offset'], $params['sort']);
        
        $this->data['searchResults'] = $results;
        
        
        $csvfile = create_csv("result.csv", $results, TRUE, "SJIS-WIN");
        
        if($csvfile !== FALSE)
        {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename='."result.csv");
            header('Content-Transfer-Encoding: binary');
            header('Content-Length: '.filesize($csvfile));
            readfile($csvfile);
            exit();
        }
        else
        {
            $this->data['msg'][] = array('class'=>'danger', 'text'=>'失敗しました。');
            $this->load->view('result', $this->data);
        }
    }
    
    public function cookieset()
    {
        $cookieLimit = time() + 60 * 60 * 24 * 90;// 90日
        set_cookie('startYear', $_POST['startYear'], $cookieLimit);
        set_cookie('startMonth', $_POST['startMonth'], $cookieLimit);
        set_cookie('startDate', $_POST['startDate'], $cookieLimit);
        set_cookie('endYear', $_POST['endYear'], $cookieLimit);
        set_cookie('endMonth', $_POST['endMonth'], $cookieLimit);
        set_cookie('endDate', $_POST['endDate'], $cookieLimit);
        if(isset($_POST['pageAuthorityCheck']))
        {
            set_cookie('pageAuthorityCheck', $_POST['pageAuthorityCheck'], $cookieLimit);
            set_cookie('pageAuthorityMin', $_POST['pageAuthorityMin'], $cookieLimit);
            set_cookie('pageAuthorityMax', $_POST['pageAuthorityMax'], $cookieLimit);
        }
        else
        {
            delete_cookie('pageAuthorityCheck');
            delete_cookie('pageAuthorityMin');
            delete_cookie('pageAuthorityMax');
        }
        if(isset($_POST['domainAuthorityCheck']))
        {
            set_cookie('domainAuthorityCheck', $_POST['domainAuthorityCheck'], $cookieLimit);
            set_cookie('domainAuthorityMin', $_POST['domainAuthorityMin'], $cookieLimit);
            set_cookie('domainAuthorityMax', $_POST['domainAuthorityMax'], $cookieLimit);
        }
        else
        {
            delete_cookie('domainAuthorityCheck');
            delete_cookie('domainAuthorityMin');
            delete_cookie('domainAuthorityMax');
        }
        //set_cookie('pagerank2', implode(",",$_GET['pagerank']), $cookieLimit);
        if(isset($_POST['totalLinksCheck']))
        {
            set_cookie('totalLinksCheck', $_POST['totalLinksCheck'], $cookieLimit);
            set_cookie('totalLinksMin', $_POST['totalLinksMin'], $cookieLimit);
            set_cookie('totalLinksMax', $_POST['totalLinksMax'], $cookieLimit);
        }
        else
        {
            delete_cookie('totalLinksCheck');
            delete_cookie('totalLinksMin');
            delete_cookie('totalLinksMax');
        }
        if(isset($_POST['linkRootDomainCheck']))
        {
            set_cookie('linkRootDomainCheck', $_POST['linkRootDomainCheck'], $cookieLimit);
            set_cookie('linkRootDomainMin', $_POST['linkRootDomainMin'], $cookieLimit);
            set_cookie('linkRootDomainMax', $_POST['linkRootDomainMax'], $cookieLimit);
        }
        else
        {
            delete_cookie('linkRootDomainCheck');
            delete_cookie('linkRootDomainMin');
            delete_cookie('linkRootDomainMax');
        }
        if(isset($_POST['seomozLinkCheck']))
        {
            set_cookie('seomozLinkCheck', $_POST['seomozLinkCheck'], $cookieLimit);
            set_cookie('seomozLinkMin', $_POST['seomozLinkMin'], $cookieLimit);
            set_cookie('seomozLinkMax', $_POST['seomozLinkMax'], $cookieLimit);
        }
        else
        {
            delete_cookie('seomozLinkCheck');
            delete_cookie('seomozLinkMin');
            delete_cookie('seomozLinkMax');
        }
        if(isset($_POST['ueidCheck']))
        {
            set_cookie('ueidCheck', $_POST['ueidCheck'], $cookieLimit);
            set_cookie('ueidMin', $_POST['ueidMin'], $cookieLimit);
            set_cookie('ueidMax', $_POST['ueidMax'], $cookieLimit);
        }
        else
        {
            delete_cookie('ueidCheck');
            delete_cookie('ueidMin');
            delete_cookie('ueidMax');
        }
        set_cookie('displaymax', $_POST['displaymax'], $cookieLimit);
        $redirectFlag = true;
        header("Location: ".base_url("result/search"));
        exit();
    }
    
    public function cookieclear()
    {
        $deleteCookieTime = time() - 3600;
        delete_cookie('startYear');
        delete_cookie('startMonth');
        delete_cookie('startDate');
        delete_cookie('endYear');
        delete_cookie('endMonth');
        delete_cookie('endDate');
        delete_cookie('pageAuthorityMin');
        delete_cookie('pageAuthorityMax');
        delete_cookie('pageAuthorityCheck');
        delete_cookie('domainAuthorityMin');
        delete_cookie('domainAuthorityMax');
        delete_cookie('domainAuthorityCheck');
        //delete_cookie('pagerank2');
        delete_cookie('totalLinksMin');
        delete_cookie('totalLinksMax');
        delete_cookie('totalLinksCheck');
        delete_cookie('linkRootDomainMin');
        delete_cookie('linkRootDomainMax');
        delete_cookie('linkRootDomainCheck');
        delete_cookie('seomozLinkMin');
        delete_cookie('seomozLinkMax');
        delete_cookie('seomozLinkCheck');
        delete_cookie('ueidMin');
        delete_cookie('ueidMax');
        delete_cookie('ueidCheck');
        delete_cookie('displaymax');
        header("Location: ".base_url("result"));
        exit();
    }
    
    private function _set_param()
    {
        $results = array("whereparam" => NULL, "limit" => NULL, "offset" => 0, "sort" => NULL);
        $whereparam = array();

        if(get_cookie('startYear') && get_cookie('startMonth') && get_cookie('startDate') && get_cookie('endYear') && get_cookie('endMonth') && get_cookie('endDate'))
        {
            $startTime = mktime(0,0,0, intval(get_cookie('startMonth'),10), intval(get_cookie('startDate'),10), intval(get_cookie('startYear'),10));
            $endTime = mktime(23,59,59, intval(get_cookie('endMonth'),10), intval(get_cookie('endDate'),10), intval(get_cookie('endYear'),10));
            $startDateTime = date("Y-m-d H:i:s", $startTime);
            $endDateTime = date("Y-m-d H:i:s", $endTime);
            $whereparam[] = array('kind' => 'where', 'colname' => 'domains.insertdatetime >=', 'value' => $startDateTime);
            $whereparam[] = array('kind' => 'where', 'colname' => 'domains.insertdatetime <=', 'value' => $endDateTime);
        }
        
        if(get_cookie('pageAuthorityCheck') == "on")
        {
            $whereparam[] = array('kind' => 'where', 'colname' => 'domainCheckSites.pageAuthority >=', 'value' => intval(get_cookie('pageAuthorityMin')));
            $whereparam[] = array('kind' => 'where', 'colname' => 'domainCheckSites.pageAuthority <=', 'value' => intval(get_cookie('pageAuthorityMax')));
        }
        
        if(get_cookie('domainAuthorityCheck') == "on")
        {
            $whereparam[] = array('kind' => 'where', 'colname' => 'domainCheckSites.domainAuthority >=', 'value' => intval(get_cookie('domainAuthorityMin')));
            $whereparam[] = array('kind' => 'where', 'colname' => 'domainCheckSites.domainAuthority <=', 'value' => intval(get_cookie('domainAuthorityMax')));
        }
        
        if(get_cookie('totalLinksCheck') == "on")
        {
            $whereparam[] = array('kind' => 'where', 'colname' => 'domainCheckSites.totalLinks >=', 'value' => intval(get_cookie('totalLinksMin')));
            $whereparam[] = array('kind' => 'where', 'colname' => 'domainCheckSites.totalLinks <=', 'value' => intval(get_cookie('totalLinksMax')));
        }
        
        if(get_cookie('linkRootDomainCheck') == "on")
        {
            $whereparam[] = array('kind' => 'where', 'colname' => 'domainCheckSites.linkRootDomain >=', 'value' => intval(get_cookie('linkRootDomainMin')));
            $whereparam[] = array('kind' => 'where', 'colname' => 'domainCheckSites.linkRootDomain <=', 'value' => intval(get_cookie('linkRootDomainMax')));
        }
        
        if(get_cookie('seomozLinkCheck') == "on")
        {
            $whereparam[] = array('kind' => 'where', 'colname' => 'domainCheckSites.linksMet >=', 'value' => intval(get_cookie('seomozLinkMin')));
            $whereparam[] = array('kind' => 'where', 'colname' => 'domainCheckSites.linksMet <=', 'value' => intval(get_cookie('seomozLinkMax')));
        }
        
        if(get_cookie('ueidCheck') == "on")
        {
            $whereparam[] = array('kind' => 'where', 'colname' => 'domainCheckSites.ueid >=', 'value' => intval(get_cookie('ueidMin')));
            $whereparam[] = array('kind' => 'where', 'colname' => 'domainCheckSites.ueid <=', 'value' => intval(get_cookie('ueidMax')));
        }
        
        if(get_cookie('displaymax'))
        {
            $results['limit'] = get_cookie('displaymax');
        }
        
        $results['whereparam'] = $whereparam;
        
        return $results;
    }
    
    private function _counter()
    {
        $this->data['countAllDomains'] = $this->domain->get_count_domains(NULL, 'domains');
        
        $whereparam = array();
        $whereparam[] = array('kind'=>'where', 'colname'=>'status', 'value'=> 0);
        $this->data['countPendings'] = $this->domain->get_count_domains($whereparam, 'domains');
        
        $whereparam = array();
        $whereparam[] = array('kind'=>'where', 'colname'=>'status <>', 'value'=> 0);
        $this->data['countHttpChecked'] = $this->domain->get_count_domains($whereparam, 'domains');
        
        $whereparam = array();
        $whereparam[] = array('kind'=>'where', 'colname'=>'mozcheck', 'value'=> 1);
        $whereparam[] = array('kind'=>'or_where', 'colname'=>'status', 'value'=> -3);
        $whereparam[] = array('kind'=>'or_where', 'colname'=>'status', 'value'=> -4);
        $this->data['countSiteMetChecked'] = $this->domain->get_count_domains($whereparam, 'domains');

        $whereparam = array();
        $whereparam[] = array('kind'=>'where', 'colname'=>'totalLinks >', 'value'=> 0);
        $this->data['countLinkMetChecked'] = $this->domain->get_count_domains($whereparam, 'domainCheckSites');

        $whereparam = array();
        $whereparam[] = array('kind'=>'or_where', 'colname'=>'http_code', 'value'=> -9);
        $whereparam[] = array('kind'=>'or_where', 'colname'=>'http_code >', 'value'=> 1000);
        $this->data['countLinkChecked'] = $this->domain->get_count_domains($whereparam, 'domainLinks');


    }
}
