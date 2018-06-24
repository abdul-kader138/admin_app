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
        if (!$this->Owner) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        $this->lang->load('employees', $this->Settings->user_language);
        $this->load->library('form_validation');
        $this->load->model('employees_model');
    }

    function index()
    {
        $this->sma->checkPermissions();
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('employees')));
        $meta = array('page_title' => lang('employees'), 'bc' => $bc);
        $this->page_construct('employees/index', $meta, $this->data);
    }

    function getEmployees()
    {
        $this->sma->checkPermissions('index');
        $this->load->library('datatables');
        $this->datatables
            ->select($this->db->dbprefix('employees').".id as id, employee_id,name,company_id,operator_id,package_id,mobile_number, ceiling_amount, active")
            ->from("employees")
//            ->join('groups', 'users.group_id=groups.id', 'left')
//            ->group_by('users.id')
//            ->where('company_id', NULL)
            ->edit_column('active', '$1__$2', 'active, id')
            ->add_column("Actions", "<div class=\"text-center\"><a href='" . site_url('auth/profile/$1') . "' class='tip' title='" . lang("edit_user") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");

        if (!$this->Owner) {
            $this->datatables->unset_column('id');
        }
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

        $this->data['title'] = "Add User";
        $this->form_validation->set_rules('username', lang("username"), 'trim|is_unique[users.username]');
        $this->form_validation->set_rules('email', lang("email"), 'trim|is_unique[users.email]');
        $this->form_validation->set_rules('status', lang("status"), 'trim|required');
        $this->form_validation->set_rules('group', lang("group"), 'trim|required');

        if ($this->form_validation->run() == true) {

            $username = strtolower($this->input->post('username'));
            $email = strtolower($this->input->post('email'));
            $password = $this->input->post('password');
            $notify = $this->input->post('notify');

            $additional_data = array(
                'first_name' => $this->input->post('first_name'),
                'last_name' => $this->input->post('last_name'),
                'company' => $this->input->post('company'),
                'phone' => $this->input->post('phone'),
                'gender' => $this->input->post('gender'),
                'group_id' => $this->input->post('group') ? $this->input->post('group') : '3',
                'biller_id' => $this->input->post('biller'),
                'warehouse_id' => $this->input->post('warehouse'),
                'view_right' => $this->input->post('view_right'),
                'edit_right' => $this->input->post('edit_right'),
                'allow_discount' => $this->input->post('allow_discount'),
            );
            $active = $this->input->post('status');
        }
        if ($this->form_validation->run() == true) {
//        if ($this->form_validation->run() == true && $this->ion_auth->register($username, $password, $email, $additional_data, $active, $notify)) {

            $this->session->set_flashdata('message', $this->ion_auth->messages());
            redirect("employees/index");

        } else {
            $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
//            $this->data['groups'] = $this->ion_auth->groups()->result_array();
//            $this->data['billers'] = $this->site->getAllCompanies('biller');
//            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $bc = array(array('link' => site_url('home'), 'page' => lang('home')), array('link' => site_url('employees/add_employee'), 'page' => lang('users')), array('link' => '#', 'page' => lang('add_employee')));
            $meta = array('page_title' => lang('employees'), 'bc' => $bc);
            $this->page_construct('employees/add_employee', $meta, $this->data);
        }
    }
}