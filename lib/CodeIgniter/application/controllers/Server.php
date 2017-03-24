<?php
/**
 * Copyright (c) 2017. HatchBit & Co.
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Server extends CI_Controller {

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
    private $data = array();
    
    public function __construct()
    {
        parent::__construct();
        $this->load->helper(array('form', 'url', 'csv', 'download'));
        $this->load->model('Domain_model', 'domain', TRUE);

    }
    
    public function index()
    {
        $whereparam = array();
        $whereparam[] = array('kind'=>'where', 'colname'=>'status', 'value'=>1);
        $whereparam[] = array('kind'=>'order_by', 'colname'=>'id', 'value'=>'DESC');
        $this->data['servers'] = $this->domain->get_apiservers($whereparam, NULL, 0);
        $this->load->view('server', $this->data);
    }

    public function upload()
    {
        $config['upload_path']      = $this->downloaddir;
        $config['allowed_types']    = '*';
        $config['overwrite']        = TRUE;
        $config['remove_spaces']    = TRUE;

        $this->load->library('upload', $config);

        // ファイルが送信されてきたら
        if (isset($_FILES['csvfile']))
        {
            // 一時ファイルをディレクトリに移動（保存）
            $field_name = "csvfile";
            if( $this->upload->do_upload($field_name) )
            {
                $file_path = $this->upload->data('full_path');
                $paid = $this->input->post('paid', TRUE);
                $results = $this->domain->insert_accessid_from_csv($file_path, $paid);
                $this->data['results'] = $results;

                $this->data['msg'][] = array('kind'=>'success', 'message'=>'登録しました！');
            }
        }

        $this->load->view('server-upload', $this->data);
    }

    public function deleted()
    {
        $whereparam = array();
        $whereparam[] = array('kind'=>'where', 'colname'=>'status', 'value'=>0);
        $whereparam[] = array('kind'=>'order_by', 'colname'=>'id', 'value'=>'ASC');
        $this->data['servers'] = $this->domain->get_apiservers($whereparam, NULL, 0);
        $this->load->view('server-deleted', $this->data);
    }
}
