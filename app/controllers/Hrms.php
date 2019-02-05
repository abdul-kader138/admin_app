<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Hrms extends  MY_Controller
{

    function __construct()
    {
        parent::__construct();

        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }
        $this->permission_details = $this->site->checkPermissions();
        $this->lang->load('billers', $this->Settings->user_language);
        $this->load->library('form_validation');
        $this->load->model('hr_model');
    }


    function manpower_requisition()
    {
        if (!$this->Owner && !$this->Admin) {
            $get_permission = $this->permission_details[0];
            if ((!$get_permission['hrms-manpower_requisition'])) {
                $this->session->set_flashdata('warning', lang('access_denied'));
                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
                redirect($_SERVER["HTTP_REFERER"]);
            }
        }
        $data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('HR')));
        $meta = array('page_title' => lang('HR'), 'bc' => $bc);
        $this->page_construct('hr/manpower_requisition', $meta, $this->data);
    }

    function getMR()
    {
        if (!$this->Owner && !$this->Admin) {
            $get_permission = $this->permission_details[0];
            if ((!$get_permission['hrms-manpower_requisition'])) {
                $this->session->set_flashdata('warning', lang('access_denied'));
                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
                redirect($_SERVER["HTTP_REFERER"]);
            }
        }
        $edit_link = "";
        $delete_link = "";
        if ($get_permission['hrms-edit_manpower_requisition'] || $this->Owner || $this->Admin) $edit_link = anchor('hrms/edit_manpower_requisition/$1', '<i class="fa fa-edit"></i> ' . lang('edit'), 'class="sledit"');
        if ($get_permission['hrms-delete_manpower_requisition'] || $this->Owner || $this->Admin) $delete_link = "<a href='#' class='po' title='<b>" . lang("delete") . "</b>' data-content=\"<p>"
            . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . site_url('hrms/delete_manpower_requisition/$1') . "'>"
            . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i> "
            . lang('delete') . "</a>";
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
            ->select($this->db->dbprefix('manpower_requisition') . ".id as id, " . $this->db->dbprefix('manpower_requisition') . ".requisition_date," . $this->db->dbprefix('manpower_requisition') . ".position as nam," . $this->db->dbprefix('manpower_requisition') . ".workstation as ref,". $this->db->dbprefix('manpower_requisition') . ".department," . $this->db->dbprefix('company') . ".name as d_name," . $this->db->dbprefix('manpower_requisition') .".organization_type,". $this->db->dbprefix('manpower_requisition') . ".number_required,". $this->db->dbprefix('manpower_requisition') . ".status")
            ->from("manpower_requisition")
            ->join('company', 'manpower_requisition.company_id=company.id', 'left')
            ->join('designations', 'manpower_requisition.designation_id=designations.id', 'left')
            ->group_by('manpower_requisition.id')
            ->add_column("Actions", $action, "id");
        echo $this->datatables->generate();
    }

    public function add_manpower_requisition()
    {
        if (!$this->Owner && !$this->Admin) {
            $get_permission = $this->permission_details[0];
            if ((!$get_permission['hrms-add_manpower_requisition'])) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
            redirect($_SERVER["HTTP_REFERER"]);
            }
        }

        $this->data['title'] = "Add Manpower Requisition";
        $this->form_validation->set_rules('workstation', lang("workstation"), 'trim|required');
        $this->form_validation->set_rules('requisition_date', lang("requisition_date"), 'trim|required');
        $this->form_validation->set_rules('company_id', lang("company_id"), 'trim|required');
        $this->form_validation->set_rules('department', lang("department"), 'trim|required');
        $this->form_validation->set_rules('designation_id', lang("designation_id"), 'trim|required');
        $this->form_validation->set_rules('position', lang("position"), 'trim|required');
        $this->form_validation->set_rules('organization_type', lang("organization_type"), 'trim|required');
        $this->form_validation->set_rules('number_required', lang("number_required"), 'trim|required');
        $this->form_validation->set_rules('exp_min', lang("exp_min"), 'trim|required');
        $this->form_validation->set_rules('exp_max', lang("exp_max"), 'trim|required');
        $this->form_validation->set_rules('age_min', lang("age_min"), 'trim|required');
        $this->form_validation->set_rules('age_max', lang("age_max"), 'trim|required');
        $this->form_validation->set_rules('mb_year', lang("mb_year"), 'trim|required');
        $this->form_validation->set_rules('education', lang("education"), 'trim|required');
        $this->form_validation->set_rules('skill', lang("skill"), 'trim|required');
        $this->form_validation->set_rules('minimum_experience', lang("minimum_experience"), 'trim|required');
        $this->form_validation->set_rules('nature_experience', lang("nature_experience"), 'trim|required');
        $this->form_validation->set_rules('areas_of_responsibility', lang("areas_of_responsibility"), 'trim|required');
        $this->form_validation->set_rules('reporting_to', lang("reporting_to"), 'trim|required');
        $this->form_validation->set_rules('no_of_reportees', lang("no_of_reportees"), 'trim|required');
        if ($this->form_validation->run() == true) {

            $userLists = $this->site->getAllUser();
            $approver_details = $this->site->getApproverList('add_manpower_requisition');
            $user_details = $this->getApproveCustomer($userLists,$approver_details->approver_id);
            $reason=$this->input->post('requirement');
            $approve_data = array(
                'aprrover_id' => $approver_details->approver_id,
                'status' => 'Waiting For Approval-' . $user_details->username,
                'table_name' => 'manpower_requisition',
                'approver_seq' => $approver_details->approver_seq,
                'approver_seq_name' => $approver_details->approver_seq_name,
                'created_by' => $this->session->userdata('user_id'),
                'interface_name' => $approver_details->interface_name,
                'next_approve_seq' => $approver_details->approver_next_seq,
                'created_date' => date("Y-m-d H:i:s")
            );

            $data = array(
                'workstation' => $this->input->post('workstation'),
                'requisition_date' => $this->sma->fld($this->input->post('requisition_date')),
                'company_id' => $this->input->post('company_id'),
                'department' => $this->input->post('department'),
                'designation_id' => $this->input->post('designation_id'),
                'position' => $this->input->post('position'),
                'organization_type' => $this->input->post('organization_type'),
                'corporate_name' => $this->input->post('corporate_name'),
                'business_name' => $this->input->post('business_name'),
                'number_required' => $this->input->post('number_required'),
                'exp_min' => $this->input->post('exp_min'),
                'exp_max' => $this->input->post('exp_max'),
                'age_min' => $this->input->post('age_min'),
                'age_max' => $this->input->post('age_max'),
                'ap' => (($reason =='ap') ? 1 :0),
                'rr' => (($reason =='rr') ? 1 :0),
                'rt' => (($reason =='rt') ? 1 :0),
                'rp' => (($reason =='rp') ? 1 :0),
                'rtr' => (($reason =='rtr') ? 1 :0),
                'mb_year' => $this->input->post('mb_year'),
                'reason_ap' => $this->input->post('reason_ap'),
                'time_limit' => $this->input->post('time_limit'),
                'education' => $this->input->post('education'),
                'skill' => $this->input->post('skill'),
                'minimum_experience' => $this->input->post('minimum_experience'),
                'nature_experience' => $this->input->post('nature_experience'),
                'areas_of_responsibility' => $this->input->post('areas_of_responsibility'),
                'reporting_to' => $this->input->post('reporting_to'),
                'no_of_reportees' => $this->input->post('no_of_reportees'),
                'created_by' => $this->session->userdata('user_id'),
                'created_date' => date("Y-m-d H:i:s"),
                'status' => 'Waiting For Approval-' . $user_details->username,
                'next_approve_seq' => $approver_details->approver_next_seq,
                'other_info' => $this->input->post('other_info')
            );
        }

        if ($this->form_validation->run() == true && $this->hr_model->addMR($data,$approve_data)) {
            $this->session->set_flashdata('message', "Information Successfully added.");
            redirect("hrms/manpower_requisition");

        } else {
            $data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['companies'] = $this->site->getAllCompany();
            $this->data['designations'] = $this->site->getAllDesignation();

            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('hrms'), 'page' => lang('HR')), array('link' => '#', 'page' => lang('Add_Manpower_Requisition')));
            $meta = array('page_title' => lang('Add_Manpower_Requisition'), 'bc' => $bc);
            $this->page_construct('hr/add_manpower_requisition', $meta, $this->data);
        }
    }


    function edit_manpower_requisition($id = NULL)
    {

        if (!$this->Owner && !$this->Admin) {
            $get_permission = $this->permission_details[0];
            if ((!$get_permission['hrms-edit_manpower_requisition'])) {
                $this->session->set_flashdata('warning', lang('access_denied'));
                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
                redirect($_SERVER["HTTP_REFERER"]);
            }
        }
        if ($this->input->post('id')) {
            $id = $this->input->post('id');
        }

        $this->data['title'] = "Edit Manpower Requisition";
        $this->form_validation->set_rules('workstation', lang("workstation"), 'trim|required');
        $this->form_validation->set_rules('requisition_date', lang("requisition_date"), 'trim|required');
        $this->form_validation->set_rules('company_id', lang("company_id"), 'trim|required');
        $this->form_validation->set_rules('department', lang("department"), 'trim|required');
        $this->form_validation->set_rules('designation_id', lang("designation_id"), 'trim|required');
        $this->form_validation->set_rules('position', lang("position"), 'trim|required');
        $this->form_validation->set_rules('organization_type', lang("organization_type"), 'trim|required');
        $this->form_validation->set_rules('number_required', lang("number_required"), 'trim|required');
        $this->form_validation->set_rules('exp_min', lang("exp_min"), 'trim|required');
        $this->form_validation->set_rules('exp_max', lang("exp_max"), 'trim|required');
        $this->form_validation->set_rules('age_min', lang("age_min"), 'trim|required');
        $this->form_validation->set_rules('age_max', lang("age_max"), 'trim|required');
        $this->form_validation->set_rules('mb_year', lang("mb_year"), 'trim|required');
        $this->form_validation->set_rules('education', lang("education"), 'trim|required');
        $this->form_validation->set_rules('skill', lang("skill"), 'trim|required');
        $this->form_validation->set_rules('minimum_experience', lang("minimum_experience"), 'trim|required');
        $this->form_validation->set_rules('nature_experience', lang("nature_experience"), 'trim|required');
        $this->form_validation->set_rules('areas_of_responsibility', lang("areas_of_responsibility"), 'trim|required');
        $this->form_validation->set_rules('reporting_to', lang("reporting_to"), 'trim|required');
        $this->form_validation->set_rules('no_of_reportees', lang("no_of_reportees"), 'trim|required');

        if ($this->form_validation->run() == true) {
            $reason=$this->input->post('requirement');
            $reason1=$this->input->post('requirement');
            $data = array(
                'workstation' => requirement,
                'requisition_date' => $this->sma->fld($this->input->post('requisition_date')),
                'company_id' => $this->input->post('company_id'),
                'department' => $this->input->post('department'),
                'designation_id' => $this->input->post('designation_id'),
                'position' => $this->input->post('position'),
                'organization_type' => $this->input->post('organization_type'),
                'corporate_name' => $this->input->post('corporate_name'),
                'business_name' => $this->input->post('business_name'),
                'number_required' => $this->input->post('number_required'),
                'exp_min' => $this->input->post('exp_min'),
                'exp_max' => $this->input->post('exp_max'),
                'age_min' => $this->input->post('age_min'),
                'age_max' => $this->input->post('age_max'),
                'ap' => (($reason =='ap') ? 1 :0),
                'rr' => (($reason =='rr') ? 1 :0),
                'rt' => (($reason =='rt') ? 1 :0),
                'rp' => (($reason =='rp') ? 1 :0),
                'rtr' => (($reason =='rtr') ? 1 :0),
                'mb_year' => $this->input->post('mb_year'),
                'reason_ap' => $this->input->post('reason_ap'),
                'time_limit' => $this->input->post('time_limit'),
                'education' => $this->input->post('education'),
                'skill' => $this->input->post('skill'),
                'minimum_experience' => $this->input->post('minimum_experience'),
                'nature_experience' => $this->input->post('nature_experience'),
                'areas_of_responsibility' => $this->input->post('areas_of_responsibility'),
                'reporting_to' => $this->input->post('reporting_to'),
                'no_of_reportees' => $this->input->post('no_of_reportees'),
                'created_by' => $this->session->userdata('user_id'),
                'created_date' => date("Y-m-d H:i:s"),
                'other_info' => $this->input->post('other_info')
            );
        }

        if ($this->form_validation->run() === TRUE && $this->hr_model->updateMR($id, $data)) {
            $this->session->set_flashdata('message', "Information Successfully updated.");
            redirect("hrms/manpower_requisition");
        } else {
            $data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['document'] = $this->hr_model->getMRById($id);
            $this->data['companies'] = $this->site->getAllCompany();
            $this->data['designations'] = $this->site->getAllDesignation();
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('hrms'), 'page' => lang('HR')), array('link' => '#', 'page' => lang('Edit_Manpower_Requisition')));
            $meta = array('page_title' => lang('Edit_Manpower_Requisition'), 'bc' => $bc);
            $this->page_construct('hr/edit_manpower_requisition', $meta, $this->data);
        }
    }

    function delete_manpower_requisition($id = NULL)
    {
        if (!$this->Owner && !$this->Admin) {
            $get_permission = $this->permission_details[0];
            if ((!$get_permission['hrms-delete_manpower_requisition'])) {
                $this->session->set_flashdata('warning', lang('access_denied'));
                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
                redirect($_SERVER["HTTP_REFERER"]);
            }
        }

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        if ($this->hr_model->deleteMR($id)) {
            if ($this->input->is_ajax_request()) {
                echo lang("Information Successfully deleted.");
                die();
            }
            $this->session->set_flashdata('message', lang('doc_mov_deleted'));
            redirect('hrms/manpower_requisition');
        }
    }

    function modal_manpower_requisition($id = NULL) {
        $this->sma->checkPermissions('manpower_requisition', TRUE);

        $pr_details = $this->hr_model->getMRById($id);
        if (!$id || !$pr_details) {
            $this->session->set_flashdata('error', lang('doc_not_found'));
            $this->sma->md();
        }
        $this->data['document'] = $pr_details;
        $this->data['unit'] = "Decimal";
        $this->data['companies'] = $this->hr_model->getCompanyById($pr_details->company_id);
        $this->data['designations'] = $this->hr_model->getDesignationById($pr_details->designation_id);
        $this->load->view($this->theme . 'hr/modal_manpower_requisition', $this->data);
    }

    function getApproveCustomer($userList,$approveId){
        $userDetails=null;
        foreach ($userList as $user){
            if($approveId == $user->id) {
                $userDetails=$user;
                break;
            }
        }
        return $userDetails;
    }

}