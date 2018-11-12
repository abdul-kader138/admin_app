<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: a.kader
 * Date: 12-Nov-18
 * Time: 9:55 AM
 */

class Document  extends MY_Controller
{

    function __construct()
    {
        parent::__construct();

        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }
        $this->permission_details = $this->site->checkPermissions();
        $this->lang->load('document', $this->Settings->user_language);
        $this->digital_upload_path = 'files/';
        $this->upload_path = 'assets/uploads/';
        $this->thumbs_path = 'assets/uploads/thumbs/';
        $this->image_types = 'gif|jpg|jpeg|png|tif';
        $this->digital_file_types = 'zip|psd|ai|rar|pdf|doc|docx|xls|xlsx|ppt|pptx|gif|jpg|jpeg|png|tif|txt';
        $this->allowed_file_size = '1024';
        $this->data['logo'] = true;
        $this->load->library('form_validation');
        $this->load->model('document_model');
    }

    public function add()
    {
        if (!$this->Owner && !$this->Admin) {
            $get_permission = $this->permission_details[0];
            if ((!$get_permission['document-add'])) {
                $this->session->set_flashdata('warning', lang('access_denied'));
                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
                redirect($_SERVER["HTTP_REFERER"]);
            }
        }

        $this->data['title'] = "Add Document";
        $this->form_validation->set_rules('employee_id', lang("employee_id"), 'trim|required|is_unique[employees.employee_id]');
        $this->form_validation->set_rules('designation_id', lang("designation_id"), 'trim|required');
        $this->form_validation->set_rules('company_id', lang("company_id"), 'trim|required');
        $this->form_validation->set_rules('operator_id', lang("operator_id"), 'trim|required');
        $this->form_validation->set_rules('package_id', lang("package_id"), 'trim|required');
        $this->form_validation->set_rules('mobile_number', lang("mobile_number"), 'trim|required|is_unique[employees.mobile_number]|regex_match[/^[0-9]{10,14}$/]');
        $this->form_validation->set_rules('ceiling_amount', lang("ceiling_amount"), 'trim|required|numeric|is_natural_no_zero');
        $this->form_validation->set_rules('name', lang("name"), 'trim|required');
        //$this->form_validation->set_rules('email', lang("email"), 'trim|valid_email|is_unique[employees.email]');
//        $this->form_validation->set_rules('email', lang("email"), 'trim|required|valid_email|is_unique[employees.email]');
//        $this->form_validation->set_rules('service_start_date', lang("service_start_date"), 'trim|required');
        $this->form_validation->set_rules('service_start_date', lang("service_start_date"), 'trim');
        $this->form_validation->set_rules('active', lang("active"), 'trim|required');
        $this->form_validation->set_rules('credit_limit', lang("credit_limit"), 'trim|required|numeric|is_natural_no_zero');

        if ($this->form_validation->run() == true) {
            $email = null;
            if ($this->input->post('id')) $email = $this->input->post('email');
            $service_start_date = (string)$this->input->post('service_start_date');
            $actual_date = "";
            if ($service_start_date != "") {
                $new_service_start_date = date('d-m-Y', strtotime($service_start_date));
                $actual_date = date('Y-m-d', strtotime($new_service_start_date));
            }
            $data = array(
                'employee_id' => $this->input->post('employee_id'),
                'designation_id' => $this->input->post('designation_id'),
                'company_id' => $this->input->post('company_id'),
                'operator_id' => $this->input->post('operator_id'),
                'package_id' => $this->input->post('package_id'),
                'email' => $email,
                'mobile_number' => $this->input->post('mobile_number'),
                'ceiling_amount' => $this->input->post('ceiling_amount'),
                'credit_limit' => $this->input->post('credit_limit'),
                'name' => $this->input->post('name'),
                'service_start_date' => $actual_date,
                'active' => $this->input->post('active'),
            );
        }

        if ($this->form_validation->run() == true ) {
//        if ($this->form_validation->run() == true && $this->employees_model->addEmployee($data)) {
            $this->session->set_flashdata('message', lang("employee_added"));
            redirect("employees/index");

        } else {
            $data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['companies'] = $this->site->getAllCompany();
            $this->data['doctypes'] = $this->site->getAllDocType();

            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('document'), 'page' => lang('document')), array('link' => '#', 'page' => lang('add_document')));
            $meta = array('page_title' => lang('add_document'), 'bc' => $bc);
            $this->page_construct('document/add_document', $meta, $this->data);
        }
    }
} 