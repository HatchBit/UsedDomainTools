<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Insertdomain extends CI_Controller {

    /**
     * Index Page for this controller.
     *
     * Maps to the following URL
     *         http://example.com/index.php/welcome
     *    - or -
     *         http://example.com/index.php/welcome/index
     *    - or -
     * Since this controller is set as the default controller in
     * config/routes.php, it's displayed at http://example.com/
     *
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/welcome/<method_name>
     * @see https://codeigniter.com/user_guide/general/urls.html
     */
    
    public $downloaddir = FILEPATH;
    public $zipfilename = 'PoolDeletingDomainsList.zip';
    public $csvfilename = 'PoolDeletingDomainsList.txt';
    private $data = array();
    
    public function __construct()
    {
        parent::__construct();
        $this->load->helper(array('form', 'url', 'csv', 'download'));
        $this->load->model('Domain_model', 'domain', TRUE);
        
        $this->_filelist();
    }
    
    public function index()
    {
        $this->data['panel'] = 'csv';
        $this->load->view('insertdomain', $this->data);
    }
    
    /**
     * CSVファイルアップロード
     */
    public function csv()
    {
        $config['upload_path']      = $this->downloaddir;
        //$config['allowed_types']    = 'csv|txt|text';
        $config['allowed_types']    = '*';
        $config['overwrite']        = TRUE;
        $config['remove_spaces']    = TRUE;
        
        $this->load->library('upload', $config);
        
        // CSVファイルアップロード
        $field_name = "csvfile";
        // 一時ファイルをディレクトリに移動（保存）
        if( $this->upload->do_upload($field_name) )
        {
            $file_path = $this->upload->data('full_path');
            $encode = $this->input->post('encode', TRUE);
            $results = $this->domain->insert_domain_from_csv($file_path, $encode);
            $this->data['results'] = $results;
            
            $this->load->view('insertdomain-confirm', $this->data);
        }
        else
        {
            $this->data['panel'] = 'csv';
            $this->data['msg'][] = array('kind' => 'danger', 'message'=>$this->upload->display_errors('', ''));
            $this->load->view('insertdomain', $this->data);
        }
        
    }
    
    public function pool($mode=NULL)
    {
        
        if($mode == "downloadzip")
        {
            $zipfileurl = 'http://www.pool.com/Downloads/PoolDeletingDomainsList.zip';
            $downloaddir = $this->downloaddir;
            $zipfilename = $downloaddir.DIRECTORY_SEPARATOR.$this->zipfilename;
            $csvfilename = $downloaddir.DIRECTORY_SEPARATOR.$this->csvfilename;
            $opts = array(
                'http'=>array(
                    'method'=>"GET",
                    'header'=>"Accept-language: en\r\n".
                    "User-Agent: Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0)\r\n"
                )
            );
            $downloadfile = $this->domain->download_zip_from_pool($zipfileurl, $opts, $zipfilename);
            
            $zip = new ZipArchive;
            if($zip->open($zipfilename) === TRUE)
            {
                $zip->extractTo($downloaddir);// ディレクトリに展開
                if(file_exists($csvfilename) === TRUE)
                {
                    chmod($csvfilename, 0666);
                }
            }
            $zip->close();
            $this->_filelist();
        }
        $this->data['msg'][] = array('kind' => 'success', 'message'=>'ファイルをダウンロードしました');
        $this->data['panel'] = 'pool';
        $this->load->view('insertdomain', $this->data);
    }
    
    public function download($mode=NULL)
    {
        
        if($mode == "zip")
        {
            force_download($this->downloaddir.DIRECTORY_SEPARATOR.$this->zipfilename, NULL);
        }
        if($mode == "txt")
        {
            force_download($this->downloaddir.DIRECTORY_SEPARATOR.$this->csvfilename, NULL);
        }
        $this->data['panel'] = 'download';
        $this->load->view('insertdomain', $this->data);
    }
    
    public function _filelist()
    {
        // サーバー上のファイル
        if(file_exists($this->downloaddir.DIRECTORY_SEPARATOR.$this->zipfilename))
        {
            $this->data['zipfilename'] = basename($this->downloaddir.DIRECTORY_SEPARATOR.$this->zipfilename);
            if(filemtime($this->downloaddir.DIRECTORY_SEPARATOR.$this->zipfilename) !== FALSE)
            {
                $this->data['zipfiledate'] = date("Y-n-j H:i:s", filemtime($this->downloaddir.DIRECTORY_SEPARATOR.$this->zipfilename));
            }
        }
        if(file_exists($this->downloaddir.DIRECTORY_SEPARATOR.$this->csvfilename))
        {
            $this->data['csvfilename'] = basename($this->downloaddir.DIRECTORY_SEPARATOR.$this->csvfilename);
            if(filemtime($this->downloaddir.DIRECTORY_SEPARATOR.$this->csvfilename) !== FALSE)
            {
                $this->data['csvfiledate'] = date("Y-n-j H:i:s", filemtime($this->downloaddir.DIRECTORY_SEPARATOR.$this->csvfilename));
            }
        }
        
    }
}
