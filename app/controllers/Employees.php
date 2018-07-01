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
        $this->permission_details = $this->site->checkPermissions();
        $this->lang->load('employees', $this->Settings->user_language);
        $this->load->library('form_validation');
        $this->load->model('employees_model');
    }

    function index()
    {
        if(! $this->Owner && ! $this->Admin) {
            $get_permission=$this->permission_details[0];
            if ((!$get_permission['employees-index'])) {
                $this->session->set_flashdata('warning', lang('access_denied'));
                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
                redirect($_SERVER["HTTP_REFERER"]);
            }
        }
        $data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('employees')));
        $meta = array('page_title' => lang('employees'), 'bc' => $bc);
        $this->page_construct('employees/index', $meta, $this->data);
    }

    function getEmployees()
    {
        if(! $this->Owner && ! $this->Admin) {
            $get_permission=$this->permission_details[0];
            if ((!$get_permission['employees-index'])) {
                $this->session->set_flashdata('warning', lang('access_denied'));
                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
                redirect($_SERVER["HTTP_REFERER"]);
            }
        }
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->load->library('datatables');
        $this->datatables
            ->select($this->db->dbprefix('employees').".id as id, employee_id,name,company_id,operator_id,package_id,mobile_number, ceiling_amount, active")
            ->from("employees")
//            ->join('groups', 'users.group_id=groups.id', 'left')
//            ->group_by('users.id')
//            ->where('company_id', NULL)
            ->edit_column('active', '$1__$2', 'active, id')
            ->add_column("Actions", "<div class=\"text-center\"><a href='" . site_url('employees/edit_employee/$1') . "' class='tip' title='" . lang("edit_employee") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");

//            $this->datatables->unset_column('id');
//        if (!$this->Owner) {
//        }
        echo $this->datatables->generate();
    }

    function add_employee()
    {
        if(! $this->Owner && ! $this->Admin) {
            $get_permission=$this->permission_details[0];
            if ((!$get_permission['employees-add'])) {
                $this->session->set_flashdata('warning', lang('access_denied'));
                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
                redirect($_SERVER["HTTP_REFERER"]);
            }
        }

        $this->data['title'] = "Add Employee";
        $this->form_validation->set_rules('employee_id', lang("employee_id"), 'trim|is_unique[employees.employee_id]');
        $this->form_validation->set_rules('designation_id', lang("designation_id"), 'trim||required');
        $this->form_validation->set_rules('company_id', lang("company_id"), 'trim|required');
        $this->form_validation->set_rules('operator_id', lang("operator_id"), 'trim|required');
        $this->form_validation->set_rules('package_id', lang("package_id"), 'trim|required');
        $this->form_validation->set_rules('mobile_number', lang("mobile_number"), 'trim|is_unique[employees.mobile_number]');
        $this->form_validation->set_rules('ceiling_amount', lang("ceiling_amount"), 'trim|required');
        $this->form_validation->set_rules('name', lang("name"), 'trim|required');
        $this->form_validation->set_rules('email', lang("email"), 'trim|required');
        $this->form_validation->set_rules('service_start_date', lang("service_start_date"), 'trim|required');
        $this->form_validation->set_rules('active', lang("active"), 'trim|required');

        $t=$this->form_validation->run();
        if ($this->form_validation->run() == true) {

            $data = array(
                'employee_id' => $this->input->post('employee_id'),
                'designation_id' => $this->input->post('designation_id'),
                'company_id' => $this->input->post('company_id'),
                'operator_id' => $this->input->post('operator_id'),
                'package_id' => $this->input->post('package_id'),
                'email' => $this->input->post('email'),
                'mobile_number' => $this->input->post('mobile_number'),
                'ceiling_amount' => $this->input->post('ceiling_amount'),
                'name' => $this->input->post('name'),
                'service_start_date' => $this->input->post('service_start_date'),
                'active' => $this->input->post('active'),
            );
            $active = $this->input->post('status');
        }
//        if ($this->form_validation->run() == true) {
        if ($this->form_validation->run() == true && $this->employees_model->addEmployee($data)) {
            $this->session->set_flashdata('message', lang("employee_added"));
            redirect("employees/index");

        } else {
            $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
//            $this->data['groups'] = $this->ion_auth->groups()->result_array();
            $this->data['companies'] = $this->employees_model->getAllCompanies();
            $this->data['operators'] = $this->employees_model->getAllOperators();
            $this->data['packages'] = $this->employees_model->getAllPackages();
            $this->data['designations'] = $this->employees_model->getAllDesignations();
            $bc = array(array('link' => site_url('home'), 'page' => lang('home')), array('link' => site_url('employees/add_employee'), 'page' => lang('employee')), array('link' => '#', 'page' => lang('add_employee')));
            $meta = array('page_title' => lang('employees'), 'bc' => $bc);
            $this->page_construct('employees/add_employee', $meta, $this->data);
        }
    }
}