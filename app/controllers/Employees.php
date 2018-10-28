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
            $email=null;
            if ($this->input->post('id')) $email=$this->input->post('email');
            $service_start_date= (string) $this->input->post('service_start_date');
            $actual_date="";
            if($service_start_date != ""){
                $new_service_start_date= date('d-m-Y', strtotime($service_start_date));
                $actual_date= date('Y-m-d', strtotime($new_service_start_date));
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
        $this->form_validation->set_rules('mobile_number', lang("mobile_number"), 'trim|required|regex_match[/^[0-9]{10,14}$/]');
        $this->form_validation->set_rules('ceiling_amount', lang("ceiling_amount"), 'trim|required|numeric|is_natural_no_zero');
        $this->form_validation->set_rules('credit_limit', lang("credit_limit"), 'trim|required|numeric|is_natural_no_zero');
        $this->form_validation->set_rules('name', lang("name"), 'trim|required');
//        $this->form_validation->set_rules('email', lang("email"), 'trim|required|valid_email');
        //    $this->form_validation->set_rules('email', lang("email"), 'trim');
        $this->form_validation->set_rules('service_start_date', lang("service_start_date"), 'trim');
//        $this->form_validation->set_rules('service_start_date', lang("service_start_date"), 'trim|required');
        $this->form_validation->set_rules('active', lang("active"), 'trim|required');



        if ($this->form_validation->run() == true) {
            $email=null;
            if ($this->input->post('id')) $email=$this->input->post('email');
            $service_start_date= (string) $this->input->post('service_start_date');
            $actual_date="";
            if($service_start_date != ""){
                $new_service_start_date= date('d-m-Y', strtotime($service_start_date));
                $actual_date= date('Y-m-d', strtotime($new_service_start_date));
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
//                'service_start_date' => $this->input->post('service_start_date'),
                'service_start_date' =>$actual_date,
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
        if (!$this->Owner && !$this->Admin) {
            $get_permission = $this->permission_details[0];
            if ((!$get_permission['employees-employee_by_csv'])) {
                $this->session->set_flashdata('warning', lang('access_denied'));
                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
                redirect($_SERVER["HTTP_REFERER"]);
            }
        }

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
                        $service_start_date= (string) $csv_pr['service_start_date'];
                        $new_service_start_date= date('d-m-Y', strtotime($service_start_date));
                        $actual_date= date('Y-m-d', strtotime($new_service_start_date));

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
                            'service_start_date' =>$actual_date,
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

    function Employees_actions($wh = NULL) {
        if (!$this->Owner && !$this->GP['bulk_actions']) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER["HTTP_REFERER"]);
        }

        $this->form_validation->set_rules('form_action', lang("form_action"), 'required');

        if ($this->form_validation->run() == true) {

            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {

                    $this->sma->checkPermissions('delete');
                    foreach ($_POST['val'] as $id) {
                        $this->employees_model->deleteEmployee($id);
                    }
                    $this->session->set_flashdata('message', $this->lang->line("employee_deleted"));
                    redirect($_SERVER["HTTP_REFERER"]);
                } elseif ($this->input->post('form_action') == 'export_excel' || $this->input->post('form_action') == 'export_pdf') {

                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle('Employees');
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('employee_id'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('name'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('company_name'));
                    $this->excel->getActiveSheet()->SetCellValue('D1', lang('operator_name'));
                    $this->excel->getActiveSheet()->SetCellValue('E1', lang('package_name'));
                    $this->excel->getActiveSheet()->SetCellValue('F1', lang('mobile_no'));
                    $this->excel->getActiveSheet()->SetCellValue('G1', lang('ceiling_amount'));
                    $this->excel->getActiveSheet()->SetCellValue('H1', lang('credit_limit'));
                    $this->excel->getActiveSheet()->SetCellValue('I1', lang('service_start_date'));
                    $this->excel->getActiveSheet()->SetCellValue('J1', lang('status'));
//
                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $employees = $this->employees_model->getALlEmployeeDetails($id);
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $employees->employee_id);
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $employees->name);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $employees->c_name);
                        $this->excel->getActiveSheet()->SetCellValue('D' . $row, $employees ? $employees->o_name : '');
                        $this->excel->getActiveSheet()->SetCellValue('E' . $row, $employees->p_name);
                        $this->excel->getActiveSheet()->SetCellValue('F' . $row, $employees->mobile_number);
                        $this->excel->getActiveSheet()->SetCellValue('G' . $row, $employees->ceiling_amount);
                        $this->excel->getActiveSheet()->SetCellValue('H' . $row, $employees->credit_limit);
                        $this->excel->getActiveSheet()->SetCellValue('I' . $row, $employees->service_start_date);
                        $this->excel->getActiveSheet()->SetCellValue('J' . $row, $employees->active = 1 ? 'Active' : 'Inactive');
                        $row++;
                    }

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(50);
                    $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
                    $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
                    $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
                    $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
                    $this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(30);
                    $this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(30);
                    $this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(30);
                    $this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(30);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'employees_' . date('Y_m_d_H_i_s');
                    if ($this->input->post('form_action') == 'export_pdf') {
                        $styleArray = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)));
                        $this->excel->getDefaultStyle()->applyFromArray($styleArray);
                        $this->excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
                        require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . "MPDF" . DIRECTORY_SEPARATOR . "mpdf.php");
                        $rendererName = PHPExcel_Settings::PDF_RENDERER_MPDF;
                        $rendererLibrary = 'MPDF';
                        $rendererLibraryPath = APPPATH . 'third_party' . DIRECTORY_SEPARATOR . $rendererLibrary;
                        if (!PHPExcel_Settings::setPdfRenderer($rendererName, $rendererLibraryPath)) {
                            die('Please set the $rendererName: ' . $rendererName . ' and $rendererLibraryPath: ' . $rendererLibraryPath . ' values' .
                                PHP_EOL . ' as appropriate for your directory structure');
                        }

                        header('Content-Type: application/pdf');
                        header('Content-Disposition: attachment;filename="' . $filename . '.pdf"');
                        header('Cache-Control: max-age=0');

                        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'PDF');
                        return $objWriter->save('php://output');
                    }
                    if ($this->input->post('form_action') == 'export_excel') {
                        header('Content-Type: application/vnd.ms-excel');
                        header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
                        header('Cache-Control: max-age=0');

                        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
                        return $objWriter->save('php://output');
                    }

                    redirect($_SERVER["HTTP_REFERER"]);
                }
            } else {
                $this->session->set_flashdata('error', $this->lang->line("no_product_selected"));
                redirect($_SERVER["HTTP_REFERER"]);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }


    function bills()
    {
        if (!$this->Owner && !$this->Admin) {
            $get_permission = $this->permission_details[0];
            if ((!$get_permission['employees-bill_index'])) {
                $this->session->set_flashdata('warning', lang('access_denied'));
                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
                redirect($_SERVER["HTTP_REFERER"]);
            }
        }
        $data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('employees')));
        $meta = array('page_title' => lang('bills'), 'bc' => $bc);
        $this->page_construct('employees/bills', $meta, $this->data);
    }

    function getBills()
    {
        if (!$this->Owner && !$this->Admin) {
            $get_permission = $this->permission_details[0];
            if ((!$get_permission['employees-bill_index'])) {
                $this->session->set_flashdata('warning', lang('access_denied'));
                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
                redirect($_SERVER["HTTP_REFERER"]);
            }
        }
        if($get_permission['employees-bill_delete'] || $this->Owner || $this->Admin) $delete_link = "<a href='#' class='po' title='<b>" . lang("delete_bill") . "</b>' data-content=\"<p>"
            . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . site_url('employees/delete_bill/$1') . "'>"
            . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i> "
            . lang('delete_bill') . "</a>";
        $detail_link = anchor('employees/view_bills/$1', '<i class="fa fa-file-text-o"></i> ' . lang('bill_details'));
        $detail_link_company = anchor('employees/view_bills/$1', '<i class="fa fa-file-text-o"></i> ' . lang('bill_details_company'));
        $action = '<div class="text-center"><div class="btn-group text-left">'
            . '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">'
            . lang('actions') . ' <span class="caret"></span></button>
        <ul class="dropdown-menu pull-right" role="menu">
            <li>' . $detail_link . '</li>
            <li>' . $delete_link . '</li>
        </ul>
    </div></div>';
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->load->library('datatables');
        $this->datatables
            ->select($this->db->dbprefix('bills') . ".reference_no as id, ".$this->db->dbprefix('operators') . ".name as op_name,sum(" . $this->db->dbprefix('bills') . ".ceiling_amount) as c_amount,sum(" . $this->db->dbprefix('bills') . ".usage_amount) as u_amount," . $this->db->dbprefix('bills') . ".month," . $this->db->dbprefix('bills') . ".year")
            ->from("bills")
            ->join('operators', 'bills.operator_id=operators.id', 'left')
            ->group_by('bills.month')
            ->group_by('bills.year')
            ->edit_column('active', '$1__$2', 'active, id')
            ->add_column("Actions", $action, "id");
        echo $this->datatables->generate();
    }

    public function bill_add()
    {
        if (!$this->Owner && !$this->Admin) {
            $get_permission = $this->permission_details[0];
            if ((!$get_permission['employees-bill_add'])) {
                $this->session->set_flashdata('warning', lang('access_denied'));
                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
                redirect($_SERVER["HTTP_REFERER"]);
            }
        }
        $this->load->helper('security');
        $this->form_validation->set_rules('userfile', lang("upload_file"), 'xss_clean');
        $this->form_validation->set_message('is_natural_no_zero', lang("no_zero_required"));
        $this->form_validation->set_rules('year', lang("year"), 'trim|required');
        $this->form_validation->set_rules('month', lang("month"), 'trim|required');
        $this->form_validation->set_rules('operator_id', lang("operator_id"), 'trim|required');
        $this->form_validation->set_rules('start_date', lang("start_date"), 'trim|required');
        $this->form_validation->set_rules('end_date', lang("end_date"), 'trim|required');

        if ($this->form_validation->run() == true) {

            $month = $this->input->post('month');
            $operator_id = $this->input->post('operator_id');
            $year = $this->input->post('year');
            $start_date= (string) $this->input->post('start_date');;
            $end_date= (string) $this->input->post('end_date');;
            $new_start_date= date('d-m-Y', strtotime($start_date));
            $new_end_date= date('d-m-Y', strtotime($end_date));
            $new_start_date= date('Y-m-d', strtotime($new_start_date));
            $new_end_date= date('Y-m-d', strtotime($new_end_date));

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
                    redirect("employees/bill_upload_by_csv");
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

                $keys = array('mobile_no', 'usage_amount');
                $final = array();
                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }
                $rw = 2;

                foreach ($final as $csv_pr) {
                    if (isset($csv_pr['mobile_no']) && isset($csv_pr['usage_amount'])) {

                        $bill_details = $this->employees_model->getBillByMonthAndYear($month,$year,$operator_id);
                        if ($bill_details) {
                            $this->session->set_flashdata('error', lang("bill_already_exist"));
                            redirect($_SERVER["HTTP_REFERER"]);
                        }

                        $employee_details = $this->employees_model->getEmployeeByMobile($csv_pr['employee_code'],$csv_pr['mobile_no']);
                        if (!$employee_details) {
                            $this->session->set_flashdata('error', lang("mobile_number") . " ( " .$csv_pr['mobile_no'] . " ). " . "not found");
                            redirect($_SERVER["HTTP_REFERER"]);
                        }
                        $dues=0;
                        if($employee_details->ceiling_amount < $csv_pr['usage_amount']) $dues=abs(((float)$employee_details->ceiling_amount) -((float)$csv_pr['usage_amount']));

                        $bills[] = array(
                            'employee_id' => $employee_details->employee_id,
                            'reference_no' => ($year."_".$month),
                            'year' => $year,
                            'month' => $month,
                            'operator_id' => $operator_id,
                            'start_date' => $new_start_date,
                            'end_date' => $new_end_date,
                            'mobile_number' => $csv_pr['mobile_no'],
                            'ceiling_amount' => $employee_details->ceiling_amount,
                            'usage_amount' => $csv_pr['usage_amount'],
                            'dues' => $dues,
                        );
                    }
                }

            }
        }


        if ($this->form_validation->run() == true && $this->employees_model->addBills($bills)) {
            $this->session->set_flashdata('message', lang("bill_added"));
            redirect("employees/bills");
        } else {

            $data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['companies'] = $this->employees_model->getAllCompanies();
            $this->data['operators'] = $this->employees_model->getAllOperators();
            $this->data['packages'] = $this->employees_model->getAllPackages();

            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('employees'), 'page' => lang('employees')), array('link' => '#', 'page' => lang('bill_upload')));
            $meta = array('page_title' => lang('add_employee_by_csv'), 'bc' => $bc);
            $this->page_construct('employees/bill_upload_by_csv', $meta, $this->data);

        }
    }


    function delete_bill($id = NULL){
        if (!$this->Owner && !$this->Admin) {
            $get_permission = $this->permission_details[0];
            if ((!$get_permission['employees-bill_delete'])) {
                $this->session->set_flashdata('warning', lang('access_denied'));
                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
                redirect($_SERVER["HTTP_REFERER"]);
            }
        }

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        if ($this->employees_model->deleteBill($id)) {
            if ($this->input->is_ajax_request()) {
                echo lang("bill_deleted");
                die();
            }
            $this->session->set_flashdata('message', lang('bill_deleted'));
            redirect('employees/bills');
        }
    }

    public function view_bills($bill_id = null)
    {
        $this->sma->checkPermissions('index');

        if ($this->input->get('id')) {
            $bill_id = $this->input->get('id');
        }
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $info = $this->employees_model->getAllBillDetails($bill_id);
        $this->data['id'] = $bill_id;
        $this->data['rows'] = $info;
        $this->data['supplier'] = $this->employees_model->getOperatorByID($info[0]->operator_id);
        $this->data['warehouse'] = $this->site->getWarehouseByID(1);
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('employees'), 'page' => lang('bill_details')), array('link' => '#', 'page' => lang('view')));
        $meta = array('page_title' => lang('view_purchase_details'), 'bc' => $bc);
        $this->page_construct('employees/view', $meta, $this->data);

    }

    public function pdf($bill_id = null, $view = null, $save_bufffer = null)
    {
        //$this->sma->checkPermissions();

        if ($this->input->get('id')) {
            $bill_id = $this->input->get('id');
        }

        if ($this->input->get('id')) {
            $bill_id = $this->input->get('id');
        }

        $footer=' <table width="100%">
        <tr>
            <td style="width:23%; text-align:center">
                <div style="float:left; margin:5px 15px">
                    <p>&nbsp;</p>

                    <p style="text-transform: capitalize;">

                    <p style="border-top: 1px solid #000;">Reviewed By</p>
                </div>
            </td>

            <td style="width:23%; text-align:center">
                <div style="float:left; margin:5px 15px">
                    <p>&nbsp;</p>

                    <p style="border-top: 1px solid #000;">Chief Financial Officer</p>
                </div>
            </td>


            <td style="width:23%; text-align:center">

                <div style="float:left; margin:5px 15px">
                    <p>&nbsp;</p>

                    <p style="border-top: 1px solid #000;">MD Sir</p>
                </div>
            </td>

        </tr>
    </table>';
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $info = $this->employees_model->getAllBillDetails($bill_id);
        $this->data['id'] = $bill_id;
        $this->data['rows'] = $info;
        $this->data['supplier'] = $this->employees_model->getOperatorByID($info[0]->operator_id);
        $this->data['warehouse'] = $this->site->getWarehouseByID(1);
        $name = $this->lang->line("bills") . "_" . str_replace('/', '_', $bill_id) . ".pdf";
        $html = $this->load->view($this->theme . 'employees/pdf', $this->data, true);
        if (! $this->Settings->barcode_img) {
            $html = preg_replace("'\<\?xml(.*)\?\>'", '', $html);
        }
        if ($view) {
            $this->load->view($this->theme . 'employees/pdf', $this->data);
        } elseif ($save_bufffer) {
            return $this->sma->generate_pdf($html, $name, $save_bufffer);
        } else {
            $this->sma->generate_pdf($html, $name,null,$footer);
        }

    }


    function xls_all($bill_id=null){
        if ($this->input->get('id')) {
            $bill_id = $this->input->get('id');
        }

        if ($this->input->get('id')) {
            $bill_id = $this->input->get('id');
        }
        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle(lang('bill'));
        $this->excel->getActiveSheet()->SetCellValue('A1', lang('emp_id'));
        $this->excel->getActiveSheet()->SetCellValue('B1', lang('name'));
        $this->excel->getActiveSheet()->SetCellValue('C1', lang('package_name'));
        $this->excel->getActiveSheet()->SetCellValue('D1', lang('mobile_number'));
        $this->excel->getActiveSheet()->SetCellValue('E1', lang('ceiling_amount'));
        $this->excel->getActiveSheet()->SetCellValue('F1', lang('usage_amount'));
        $row = 2;

        $bill_items = $this->employees_model->getAllBillDetailsForUploadAll($bill_id);
        foreach ($bill_items as $bill_item) {
            $dues_amount=($bill_item->ceiling_amount - $bill_item->usage_amount);
                $this->excel->getActiveSheet()->SetCellValue('A' . $row,  $bill_item->employee_id);
                $this->excel->getActiveSheet()->SetCellValue('B' . $row,  $bill_item->nam);
                $this->excel->getActiveSheet()->SetCellValue('C' . $row,  $bill_item->p_name);
                $this->excel->getActiveSheet()->SetCellValue('D' . $row, $bill_item->mobile_number);
                $this->excel->getActiveSheet()->SetCellValue('E' . $row, $bill_item->ceiling_amount);
                $this->excel->getActiveSheet()->SetCellValue('F' . $row, $bill_item->usage_amount);
            $row++;
        }

        $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $filename = 'bills_' . date('Y_m_d_H_i_s');
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
            header('Cache-Control: max-age=0');

            $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');

            return $objWriter->save('php://output');
    }


    function xls($bill_id=null){
        if ($this->input->get('id')) {
            $bill_id = $this->input->get('id');
        }

        if ($this->input->get('id')) {
            $bill_id = $this->input->get('id');
        }
        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle(lang('bill'));
        $this->excel->getActiveSheet()->SetCellValue('A1', lang('emp_id'));
        $this->excel->getActiveSheet()->SetCellValue('B1', lang('mobile_number'));
        $this->excel->getActiveSheet()->SetCellValue('C1', lang('dues_amount'));
        $row = 2;

        $bill_items = $this->employees_model->getAllBillDetailsForUpload($bill_id);
        foreach ($bill_items as $bill_item) {
            $dues_amount=($bill_item->ceiling_amount - $bill_item->usage_amount);
            if($dues_amount < 0){
                $this->excel->getActiveSheet()->SetCellValue('A' . $row,  $bill_item->employee_id);
                $this->excel->getActiveSheet()->SetCellValue('B' . $row, $bill_item->mobile_number);
                $this->excel->getActiveSheet()->SetCellValue('C' . $row, abs($bill_item->ceiling_amount - $bill_item->usage_amount));
            }

            $row++;
        }

        $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $filename = 'bills_' . date('Y_m_d_H_i_s');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');

        return $objWriter->save('php://output');
    }

}