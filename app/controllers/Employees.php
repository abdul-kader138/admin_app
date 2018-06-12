<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Employees extends MY_Controller
{

    function __construct()
    {
        parent::__construct();

        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }
        if ($this->Customer || $this->Supplier) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        $this->lang->load('employees', $this->Settings->user_language);
        $this->load->library('form_validation');
        $this->load->model('employees_model');
    }

    function index($action = NULL)
    {
        $this->sma->checkPermissions();

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('employees')));
        $meta = array('page_title' => lang('employees'), 'bc' => $bc);
        $this->page_construct('employees/create_employee', $meta, $this->data);
    }
}