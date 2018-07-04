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
        $this->digital_upload_path = 'files/';
        $this->upload_path = 'assets/uploads/';
        $this->thumbs_path = 'assets/uploads/thumbs/';
        $this->image_types = 'gif|jpg|jpeg|png|tif';
        $this->digital_file_types = 'zip|psd|ai|rar|pdf|doc|docx|xls|xlsx|ppt|pptx|gif|jpg|jpeg|png|tif|txt';
        $this->allowed_file_size = '1024';
        $this->data['logo'] = true;
        $this->load->library('form_validation');
        $this->load->model('employees_model');
    }

    function index()
    {
        if (!$this->Owner && !$this->Admin) {
            $get_permission = $this->permission_details[0];
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
        if (!$this->Owner && !$this->Admin) {
            $get_permission = $this->permission_details[0];
            if ((!$get_permission['employees-index'])) {
                $this->session->set_flashdata('warning', lang('access_denied'));
                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
                redirect($_SERVER["HTTP_REFERER"]);
            }
        }
        $edit_link="";
        $delete_link="";
        if($get_permission['employees-edit'] || $this->Owner || $this->Admin) $edit_link = anchor('employees/edit_employee/$1', '<i class="fa fa-edit"></i> ' . lang('edit_employee'), 'class="sledit"');
        if($get_permission['employees-delete'] || $this->Owner || $this->Admin) $delete_link = "<a href='#' class='po' title='<b>" . lang("delete_employee") . "</b>' data-content=\"<p>"
            . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . site_url('employees/delete_employee/$1') . "'>"
            . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i> "
            . lang('delete_employee') . "</a>";
        $action = '<div class="text-center"><div class="btn-group text-left">'
            . '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">'
            . lang('actions') . ' <span class="caret"></span></button>
        <ul class="dropdown-menu pull-right" role="menu">
            <li>' . $edit_link . '</li>
            <li>' . $delete_link . '</li>
        </ul>
    </div></div>';
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->load->library('datatables');
        $this->datatables
            ->select($this->db->dbprefix('employees') . ".id as id, employee_id," . $this->db->dbprefix('employees') . ".name as nam," . $this->db->dbprefix('company') . ".name as c_name," . $this->db->dbprefix('operators') . ".name as po_name," . $this->db->dbprefix('packages') . ".name as p_name,mobile_number, ceiling_amount,credit_limit, service_start_date,active")
            ->from("employees")
            ->join('company', 'employees.company_id=company.id', 'left')
            ->join('operators', 'employees.operator_id=operators.id', 'left')
            ->join('packages', 'employees.package_id=packages.id', 'left')
            ->join('designations', 'employees.designation_id=designations.id', 'left')
            ->group_by('employees.id')
            ->edit_column('active', '$1__$2', 'active, id')
            ->add_column("Actions", $action, "id");
//            ->add_column("Actions", "<div class=\"text-center\"><a href='" . site_url('employees/edit_employee/$1') . "' class='tip' title='" . lang("edit_employee") . "'><i class=\"fa fa-edit\"></i></a></div>", "id");
        echo $this->datatables->generate();
    }

    function add_employee()
    {
        if (!$this->Owner && !$this->Admin) {
            $get_permission = $this->permission_details[0];
            if ((!$get_permission['employees-add'])) {
                $this->session->set_flashdata('warning', lang('access_denied'));
                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
                redirect($_SERVER["HTTP_REFERER"]);
            }
        }

        $this->data['title'] = "Add Employee";
        $this->form_validation->set_rules('employee_id', lang("employee_id"), 'trim|required|is_unique[employees.employee_id]');
        $this->form_validation->set_rules('designation_id', lang("designation_id"), 'trim|required');
        $this->form_validation->set_rules('company_id', lang("company_id"), 'trim|required');
        $this->form_validation->set_rules('operator_id', lang("operator_id"), 'trim|required');
        $this->form_validation->set_rules('package_id', lang("package_id"), 'trim|required');
        $this->form_validation->set_rules('mobile_number', lang("mobile_number"), 'trim|required|is_unique[employees.mobile_number]');
        $this->form_validation->set_rules('ceiling_amount', lang("ceiling_amount"), 'trim|required');
        $this->form_validation->set_rules('name', lang("name"), 'trim|required');
        $this->form_validation->set_rules('email', lang("email"), 'trim|required|is_unique[employees.email]');
        $this->form_validation->set_rules('service_start_date', lang("service_start_date"), 'trim|required');
        $this->form_validation->set_rules('active', lang("active"), 'trim|required');
        $this->form_validation->set_rules('credit_limit', lang("credit_limit"), 'trim|required');

        $t = $this->form_validation->run();
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
            'credit_limit' => $this->input->post('credit_limit'),
            'name' => $this->input->post('name'),
            'service_start_date' => $this->input->post('service_start_date'),
            'active' => $this->input->post('active'),
        );
        }
        if ($this->form_validation->run() == true && $this->employees_model->addEmployee($data)) {
            $this->session->set_flashdata('message', lang("employee_added"));
            redirect("employees/index");

        } else {
            $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['companies'] = $this->employees_model->getAllCompanies();
            $this->data['operators'] = $this->employees_model->getAllOperators();
            $this->data['packages'] = $this->employees_model->getAllPackages();
            $this->data['designations'] = $this->employees_model->getAllDesignations();
            $bc = array(array('link' => site_url('home'), 'page' => lang('home')), array('link' => site_url('employees/add_employee'), 'page' => lang('employee')), array('link' => '#', 'page' => lang('add_employee')));
            $meta = array('page_title' => lang('employees'), 'bc' => $bc);
            $this->page_construct('employees/add_employee', $meta, $this->data);
        }
    }

    function edit_employee($id = NULL)
    {

        if (!$this->Owner && !$this->Admin) {
            $get_permission = $this->permission_details[0];
            if ((!$get_permission['employees-edit'])) {
                $this->session->set_flashdata('warning', lang('access_denied'));
                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
                redirect($_SERVER["HTTP_REFERER"]);
            }
        }
        if ($this->input->post('id')) {
            $id = $this->input->post('id');
        }
        $this->data['title'] = lang("edit_employee");

        $this->data['title'] = "Add Employee";
        $this->form_validation->set_rules('employee_id', lang("employee_id"), 'trim|required');
        $this->form_validation->set_rules('designation_id', lang("designation_id"), 'trim|required');
        $this->form_validation->set_rules('company_id', lang("company_id"), 'trim|required');
        $this->form_validation->set_rules('operator_id', lang("operator_id"), 'trim|required');
        $this->form_validation->set_rules('package_id', lang("package_id"), 'trim|required');
        $this->form_validation->set_rules('mobile_number', lang("mobile_number"), 'trim|required');
        $this->form_validation->set_rules('ceiling_amount', lang("ceiling_amount"), 'trim|required');
        $this->form_validation->set_rules('credit_limit', lang("credit_limit"), 'trim|required');
        $this->form_validation->set_rules('name', lang("name"), 'trim|required');
        $this->form_validation->set_rules('email', lang("email"), 'trim|required');
        $this->form_validation->set_rules('service_start_date', lang("service_start_date"), 'trim|required');
        $this->form_validation->set_rules('active', lang("active"), 'trim|required');



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
                'credit_limit' => $this->input->post('credit_limit'),
                'name' => $this->input->post('name'),
                'service_start_date' => $this->input->post('service_start_date'),
                'active' => $this->input->post('active'),
            );
        }

        if ($this->form_validation->run() === TRUE && $this->employees_model->updateEmployee($id, $data)) {
            $this->session->set_flashdata('message', lang('employee_updated'));
            redirect("employees/index");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['employee'] = $this->employees_model->getEmployeeById($id);
            $this->data['companies'] = $this->employees_model->getAllCompanies();
            $this->data['operators'] = $this->employees_model->getAllOperators();
            $this->data['packages'] = $this->employees_model->getAllPackages();
            $this->data['designations'] = $this->employees_model->getAllDesignations();
            $bc = array(array('link' => site_url('home'), 'page' => lang('home')), array('link' => site_url('employees/add_employee'), 'page' => lang('employee')), array('link' => '#', 'page' => lang('edit_employee')));
            $meta = array('page_title' => lang('employees'), 'bc' => $bc);
            $this->page_construct('employees/edit_employee', $meta, $this->data);
        }
    }

    function delete_employee($id = NULL){
        if (!$this->Owner && !$this->Admin) {
            $get_permission = $this->permission_details[0];
            if ((!$get_permission['employees-delete'])) {
                $this->session->set_flashdata('warning', lang('access_denied'));
                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
                redirect($_SERVER["HTTP_REFERER"]);
            }
        }

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        if ($this->employees_model->deleteEmployee($id)) {
            if ($this->input->is_ajax_request()) {
                echo lang("employee_deleted");
                die();
            }
            $this->session->set_flashdata('message', lang('employee_deleted'));
            redirect('employees/index');
        }
    }


    public function employee_by_csv()
    {
        $this->sma->checkPermissions('csv');
        $this->load->helper('security');
        $this->form_validation->set_rules('userfile', lang("upload_file"), 'xss_clean');
        $this->form_validation->set_message('is_natural_no_zero', lang("no_zero_required"));
        $this->form_validation->set_rules('company_id', lang("company_id"), 'trim|required');
        $this->form_validation->set_rules('operator_id', lang("operator_id"), 'trim|required');
        $this->form_validation->set_rules('package_id', lang("package_id"), 'trim|required');

        if ($this->form_validation->run() == true) {

            $company_id = $this->input->post('company_id');
            $operator_id = $this->input->post('operator_id');
            $package_id = $this->input->post('package_id');

            if (isset($_FILES["userfile"])) {

                $this->load->library('upload');

                $config['upload_path'] = $this->digital_upload_path;
                $config['allowed_types'] = 'csv';
                $config['max_size'] = $this->allowed_file_size;
                $config['overwrite'] = true;

                $this->upload->initialize($config);

                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect("employees/employee_by_csv");
                }

                $csv = $this->upload->file_name;
                $data['attachment'] = $csv;

                $arrResult = array();
                $handle = fopen($this->digital_upload_path . $csv, "r");
                if ($handle) {
                    while (($row = fgetcsv($handle, 1000, ",")) !== false) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                $titles = array_shift($arrResult);

                $keys = array('employee_id', 'name', 'email','mobile_no', 'ceiling_amount', 'credit_limit', 'designation_code', 'service_start_date');
                $final = array();
                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }
                $rw = 2;
                foreach ($final as $csv_pr) {
                    if (isset($csv_pr['designation_code']) && isset($csv_pr['employee_id']) && isset($csv_pr['name'])) {
                        $designation_details = $this->employees_model->getDesignationByCode($csv_pr['designation_code']);
                                if (!$designation_details) {
                                    $this->session->set_flashdata('error', lang("designation_code") . " ( " .$csv_pr['designation_code'] . " ). " . "not found");
                                    redirect($_SERVER["HTTP_REFERER"]);
                                }
                                $employees[] = array(
                                    'employee_id' =>$csv_pr['employee_id'],
                                    'designation_id' =>$designation_details->id,
                                    'company_id' => $company_id,
                                    'operator_id' => $operator_id,
                                    'package_id' => $package_id,
                                    'email' => $csv_pr['email'],
                                    'mobile_number' => $csv_pr['mobile_no'],
                                    'ceiling_amount' => $csv_pr['ceiling_amount'],
                                    'credit_limit' => $csv_pr['credit_limit'],
                                    'name' => $csv_pr['name'],
                                    'service_start_date' =>date('Y-m-d', strtotime( $csv_pr['service_start_date'] )),
                                    'active' => 1,
                                );
                            }
                    }

                }
            }


        if ($this->form_validation->run() == true && $this->employees_model->addEmployees($employees)) {
            $this->session->set_flashdata('message', lang("employee_added"));
            redirect("employees/index");
        } else {

            $data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['companies'] = $this->employees_model->getAllCompanies();
            $this->data['operators'] = $this->employees_model->getAllOperators();
            $this->data['packages'] = $this->employees_model->getAllPackages();

            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('employees'), 'page' => lang('sales')), array('link' => '#', 'page' => lang('add_sale_by_csv')));
            $meta = array('page_title' => lang('add_employee_by_csv'), 'bc' => $bc);
            $this->page_construct('employees/employee_by_csv', $meta, $this->data);

        }
    }
}