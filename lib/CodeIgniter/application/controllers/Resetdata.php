<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Resetdata extends CI_Controller {

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
    
    public function __construct()
    {
        parent::__construct();
        $this->load->helper(array('form', 'url', 'csv', 'download', 'cookie'));
        $this->load->model('Domain_model', 'domain', TRUE);
        
    }
    
    public function index()
    {
        $this->load->view('resetdata', $this->data);
    }
    
    public function allclear()
    {
        $mode = $this->input->post('mode');
        if ($mode == 'allclear')
        {
            if ($this->domain->truncate_domains())
            {
                $this->data['msg'][] = array('class'=>'success', 'text'=>'データベースを空にしました。');
            }
            else
            {
                $this->data['msg'][] = array('class'=>'danger', 'text'=>'失敗しました。');
            }
            $this->load->view('resetdata', $this->data);
        }
        else
        {
            header("Location: ".base_url("resetdata"));
            exit();
        }
    }
    
    public function deletedate()
    {
        $mode = $this->input->post('mode');
        if ($mode == 'deletedate')
        {
            $del_year = $this->input->post('year');
            $del_month = $this->input->post('month');
            $del_day = $this->input->post('day');
            
            $startTime = mktime(0,0,0,$del_month,$del_day,$del_year);
            $endTime = mktime(23,59,59,$del_month,$del_day,$del_year);
            $startDateTime = date("Y-m-d H:i:s", $startTime);
            $endDateTime = date("Y-m-d H:i:s", $endTime);
            
            $wherestring = "`domains`.`insertdatetime` < '".$endDateTime."'";
            
            if ($this->domain->delete_domains($wherestring))
            {
                $this->data['msg'][] = array('class'=>'success', 'text'=>'指定された日付以前のドメインを削除しました。');
            }
            else
            {
                $this->data['msg'][] = array('class'=>'danger', 'text'=>'失敗しました。');
            }
            $this->load->view('resetdata', $this->data);
        }
    }
}
