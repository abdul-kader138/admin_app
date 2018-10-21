<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: a.kader
 * Date: 20-Oct-18
 * Time: 2:19 PM
 */

class Guard extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }
        $this->permission_details = $this->site->checkPermissions();
        $this->lang->load('guard', $this->Settings->user_language);
        $this->digital_upload_path = 'files/';
        $this->upload_path = 'assets/uploads/';
        $this->thumbs_path = 'assets/uploads/thumbs/';
        $this->image_types = 'gif|jpg|jpeg|png|tif';
        $this->digital_file_types = 'zip|psd|ai|rar|pdf|doc|docx|xls|xlsx|ppt|pptx|gif|jpg|jpeg|png|tif|txt';
        $this->allowed_file_size = '1024';
        $this->data['logo'] = true;
        $this->load->library('form_validation');
        $this->load->model('guard_model');
    }

    function index()
    {
        if (!$this->Owner && !$this->Admin) {
            $get_permission = $this->permission_details[0];
//            if ((!$get_permission['guard-index'])) {
//                $this->session->set_flashdata('warning', lang('access_denied'));
//                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
//                redirect($_SERVER["HTTP_REFERER"]);
//            }
        }
        $data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('guards')));
        $meta = array('page_title' => lang('guards'), 'bc' => $bc);
        $this->page_construct('guard/index', $meta, $this->data);
    }

    function getGuard()
    {
        if (!$this->Owner && !$this->Admin) {
            $get_permission = $this->permission_details[0];
//            if ((!$get_permission['guard-index'])) {
//                $this->session->set_flashdata('warning', lang('access_denied'));
//                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
//                redirect($_SERVER["HTTP_REFERER"]);
//            }
        }
        $edit_link="";
        $delete_link="";
        $edit_link = anchor('guard/edit_guard/$1', '<i class="fa fa-edit"></i> ' . lang('edit_guard'), 'class="sledit"');
        $delete_link = "<a href='#' class='po' title='<b>" . lang("delete_guard") . "</b>' data-content=\"<p>"
//        if($get_permission['employees-edit'] || $this->Owner || $this->Admin) $edit_link = anchor('employees/edit_employee/$1', '<i class="fa fa-edit"></i> ' . lang('edit_employee'), 'class="sledit"');
//        if($get_permission['employees-delete'] || $this->Owner || $this->Admin) $delete_link = "<a href='#' class='po' title='<b>" . lang("delete_employee") . "</b>' data-content=\"<p>"
            . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . site_url('guard/delete_guard/$1') . "'>"
            . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i> "
            . lang('delete_guard') . "</a>";
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
            ->select($this->db->dbprefix('guards') . ".id as id, employee_id," . $this->db->dbprefix('guards') . ".name as nam," . $this->db->dbprefix('company') . ".name as c_name," . $this->db->dbprefix('designations') . ".name as d_name,mobile_number, dob,floor(datediff(curdate(),dob) / 365),present_weight,concat(height_feet,'.',height_inch), joining_date,active")
            ->from("guards")
            ->join('company', 'guards.company_id=company.id', 'left')
            ->join('designations', 'guards.designation_id=designations.id', 'left')
            ->group_by('guards.id')
            ->edit_column('active', '$1__$2', 'active, id')
            ->add_column("Actions", $action, "id");
        echo $this->datatables->generate();
    }


    function add_guard()
    {
        if (!$this->Owner && !$this->Admin) {
            $get_permission = $this->permission_details[0];
//            if ((!$get_permission['employees-add'])) {
//                $this->session->set_flashdata('warning', lang('access_denied'));
//                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
//                redirect($_SERVER["HTTP_REFERER"]);
//            }
        }

        $this->data['title'] = "Add Guard";
        $this->form_validation->set_rules('employee_id', lang("employee_id"), 'trim|required|is_unique[employees.employee_id]');
        $this->form_validation->set_rules('designation_id', lang("designation_id"), 'trim|required');
        $this->form_validation->set_rules('company_id', lang("company_id"), 'trim|required');
        $this->form_validation->set_rules('height_feet', lang("height_feet"), 'trim|required');
        $this->form_validation->set_rules('height_inch', lang("height_inch"), 'trim|required');
        $this->form_validation->set_rules('present_weight', lang("height_inch"), 'trim|required');
        $this->form_validation->set_rules('mobile_number', lang("mobile_number"), 'trim|is_unique[guards.mobile_number]|regex_match[/^[0-9]{10,14}$/]');
        $this->form_validation->set_rules('name', lang("name"), 'trim|required');
        $this->form_validation->set_rules('dob', lang("service_start_date"), 'trim|required');
        $this->form_validation->set_rules('joining_date', lang("service_start_date"), 'trim|required');
        $this->form_validation->set_rules('active', lang("active"), 'trim|required');

        if ($this->form_validation->run() == true) {
            $email=null;
            $service_start_date= (string) $this->input->post('joining_date');
            $dob_init= (string) $this->input->post('dob');
            $actual_date="";
            $actual_date_dob="";
            if($service_start_date != ""){
                $new_service_start_date= date('d-m-Y', strtotime($service_start_date));
                $actual_date= date('Y-m-d', strtotime($new_service_start_date));
            }
            if($dob_init != ""){
                $new_dob= date('d-m-Y', strtotime($dob_init));
                $actual_date_dob= date('Y-m-d', strtotime($new_dob));
            }
            $data = array(
                'employee_id' => $this->input->post('employee_id'),
                'designation_id' => $this->input->post('designation_id'),
                'company_id' => $this->input->post('company_id'),
                'height_feet' => $this->input->post('height_feet'),
                'height_inch' => $this->input->post('height_inch'),
                'present_weight' => $this->input->post('present_weight'),
                'mobile_number' => $this->input->post('mobile_number'),
                'name' => $this->input->post('name'),
                'joining_date' => $actual_date,
                'dob' => $actual_date_dob,
                'active' => $this->input->post('active'),
            );
        }
        if ($this->form_validation->run() == true && $this->guard_model->addGuard($data)) {
            $this->session->set_flashdata('message', lang("guard_added"));
            redirect("guard/index");

        } else {
            $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['companies'] = $this->guard_model->getAllCompanies();
            $this->data['operators'] = $this->guard_model->getAllOperators();
            $this->data['packages'] = $this->guard_model->getAllPackages();
            $this->data['designations'] = $this->guard_model->getAllDesignations();
            $bc = array(array('link' => site_url('home'), 'page' => lang('home')), array('link' => site_url('guard/add_guard'), 'page' => lang('guard')), array('link' => '#', 'page' => lang('add_guard')));
            $meta = array('page_title' => lang('guard'), 'bc' => $bc);
            $this->page_construct('guard/add_guard', $meta, $this->data);
        }
    }


    function edit_guard($id = NULL)
    {
        if (!$this->Owner && !$this->Admin) {
            $get_permission = $this->permission_details[0];
//            if ((!$get_permission['employees-edit'])) {
//                $this->session->set_flashdata('warning', lang('access_denied'));
//                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
//                redirect($_SERVER["HTTP_REFERER"]);
//            }
        }
        if ($this->input->post('id')) {
            $id = $this->input->post('id');
        }
        $this->data['title'] = lang("edit_guard");

        $this->data['title'] = "Add Employee";
        $this->form_validation->set_rules('employee_id', lang("employee_id"), 'trim|required|is_unique[employees.employee_id]');
        $this->form_validation->set_rules('designation_id', lang("designation_id"), 'trim|required');
        $this->form_validation->set_rules('company_id', lang("company_id"), 'trim|required');
        $this->form_validation->set_rules('height_feet', lang("height_feet"), 'trim|required');
        $this->form_validation->set_rules('height_inch', lang("height_inch"), 'trim|required');
        $this->form_validation->set_rules('present_weight', lang("height_inch"), 'trim|required');
        $this->form_validation->set_rules('mobile_number', lang("mobile_number"), 'trim|is_unique[guards.mobile_number]|regex_match[/^[0-9]{10,14}$/]');
        $this->form_validation->set_rules('name', lang("name"), 'trim|required');
        $this->form_validation->set_rules('dob', lang("service_start_date"), 'trim|required');
        $this->form_validation->set_rules('joining_date', lang("service_start_date"), 'trim|required');
        $this->form_validation->set_rules('active', lang("active"), 'trim|required');


        if ($this->form_validation->run() == true) {
            $email=null;
            $service_start_date= (string) $this->input->post('joining_date');
            $dob_init= (string) $this->input->post('dob');
            $actual_date="";
            $actual_date_dob="";
            if($service_start_date != ""){
                $new_service_start_date= date('d-m-Y', strtotime($service_start_date));
                $actual_date= date('Y-m-d', strtotime($new_service_start_date));
            }
            if($dob_init != ""){
                $new_dob= date('d-m-Y', strtotime($dob_init));
                $actual_date_dob= date('Y-m-d', strtotime($new_dob));
            }
            $data = array(
                'employee_id' => $this->input->post('employee_id'),
                'designation_id' => $this->input->post('designation_id'),
                'company_id' => $this->input->post('company_id'),
                'height_feet' => $this->input->post('height_feet'),
                'height_inch' => $this->input->post('height_inch'),
                'present_weight' => $this->input->post('present_weight'),
                'mobile_number' => $this->input->post('mobile_number'),
                'name' => $this->input->post('name'),
                'joining_date' => $actual_date,
                'dob' => $actual_date_dob,
                'active' => $this->input->post('active'),
            );
        }

        if ($this->form_validation->run() === TRUE && $this->guard_model->updateGuard($id, $data)) {
            $this->session->set_flashdata('message', lang('guard_updated'));
            redirect("guard/index");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['guard'] = $this->guard_model->getGuardById($id);
            $this->data['companies'] = $this->guard_model->getAllCompanies();
            $this->data['designations'] = $this->guard_model->getAllDesignations();
            $bc = array(array('link' => site_url('home'), 'page' => lang('home')), array('link' => site_url('guard/edit_guard'), 'page' => lang('guard')), array('link' => '#', 'page' => lang('edit_guard')));
            $meta = array('page_title' => lang('guard'), 'bc' => $bc);
            $this->page_construct('guard/edit_guard', $meta, $this->data);
        }
    }

    function delete_guard($id = NULL){
        if (!$this->Owner && !$this->Admin) {
            $get_permission = $this->permission_details[0];
//            if ((!$get_permission['employees-delete'])) {
//                $this->session->set_flashdata('warning', lang('access_denied'));
//                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
//                redirect($_SERVER["HTTP_REFERER"]);
//            }
        }

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        if ($this->guard_model->deleteGuard($id)) {
            if ($this->input->is_ajax_request()) {
                echo lang("guard_deleted");
                die();
            }
            $this->session->set_flashdata('message', lang('guard_deleted'));
            redirect('guard/index');
        }
    }


    function guard_actions($wh = NULL) {
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
                        $this->guard_model->deleteEmployee($id);
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
                    $this->excel->getActiveSheet()->SetCellValue('D1', lang('designation_name'));
                    $this->excel->getActiveSheet()->SetCellValue('E1', lang('mobile_number'));
                    $this->excel->getActiveSheet()->SetCellValue('F1', lang('dob'));
                    $this->excel->getActiveSheet()->SetCellValue('G1', lang('age'));
                    $this->excel->getActiveSheet()->SetCellValue('H1', lang('weight'));
                    $this->excel->getActiveSheet()->SetCellValue('I1', lang('height'));
                    $this->excel->getActiveSheet()->SetCellValue('J1', lang('joining_date'));
                    $this->excel->getActiveSheet()->SetCellValue('K1', lang('status'));
//
                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $guard = $this->guard_model->getALlGuardDetails($id);
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $guard->employee_id);
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $guard->nam);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $guard->c_name);
                        $this->excel->getActiveSheet()->SetCellValue('D' . $row, $guard->d_name);
                        $this->excel->getActiveSheet()->SetCellValue('E' . $row, $guard->mobile_number);
                        $this->excel->getActiveSheet()->SetCellValue('F' . $row, $guard->dob);
                        $this->excel->getActiveSheet()->SetCellValue('G' . $row, $guard->age);
                        $this->excel->getActiveSheet()->SetCellValue('H' . $row, $guard->weight);
                        $this->excel->getActiveSheet()->SetCellValue('I' . $row, $guard->height);
                        $this->excel->getActiveSheet()->SetCellValue('J' . $row, $guard->joining_date);
                        $this->excel->getActiveSheet()->SetCellValue('K' . $row, $guard->active = 1 ? 'Active' : 'Inactive');
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
                    $this->excel->getActiveSheet()->getColumnDimension('K')->setWidth(30);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'guards_' . date('Y_m_d_H_i_s');
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





} 