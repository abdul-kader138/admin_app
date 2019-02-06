<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Approval extends MY_Controller
{

    function __construct()
    {
        parent::__construct();

        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }
        if (!$this->Admin && !$this->Owner && !$this->GP['approval_manpower_requisition']) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        $this->lang->load('customers', $this->Settings->user_language);
        $this->load->library('form_validation');
        $this->load->model('approval_model');
        $this->load->model('hr_model');
        $this->permission_details = $this->site->checkPermissions();
    }


    function approval_list($ids = null)
    {
        if (!$this->Owner && !$this->Admin) {
            $get_permission = $this->permission_details[0];
            if ((!$get_permission['approval_manpower_requisition'])) {
                $this->session->set_flashdata('warning', lang('access_denied'));
                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
                redirect($_SERVER["HTTP_REFERER"]);
            }
        }

        if ($this->input->post('id')) {
            $ids = $this->input->post('id');
        }

        $data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['id'] = $ids;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('Approval')));
        $meta = array('page_title' => lang('Manpower_Requisition'), 'bc' => $bc);
        $this->page_construct('approval/approval_list', $meta, $this->data);
    }

    function getApproval($ids = null)
    {
        if (!$this->Owner && !$this->Admin) {
            $get_permission = $this->permission_details[0];
            if ((!$get_permission['approval_manpower_requisition'])) {
                $this->session->set_flashdata('warning', lang('access_denied'));
                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
                redirect($_SERVER["HTTP_REFERER"]);
            }
        }

        if ($this->input->post('id')) {
            $ids = $this->input->post('id');
        }

        $approve_link = "";
        if ($get_permission['approval_manpower_requisition'] || $this->Owner || $this->Admin)
            $approve_link = '<div class="row_approve_status" id="$2">&nbsp;&nbsp;<i class="fa fa-edit"></i> Approve</div>';
        $action = '<div class="text-center"><div class="btn-group text-left">'
            . '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">'
            . lang('actions') . ' <span class="caret"></span></button>
        <ul class="dropdown-menu pull-right" role="menu">
            <li>' . $approve_link . '</li>
        </ul>
    </div></div>';
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->load->library('datatables');
        $this->datatables
            ->select($this->db->dbprefix('approve_details') . ".application_id as id, " . $this->db->dbprefix('approve_details') . ".table_name," . $this->db->dbprefix('approve_details') . ".status as nam," . $this->db->dbprefix('approve_details') . ".approver_seq_name as ref,"
                . $this->db->dbprefix($ids) . ".department as departments," . $this->db->dbprefix('company') . ".name as names," . $this->db->dbprefix('approve_details') . ".id as approves_id," . $this->db->dbprefix('approve_details') . ".created_date")
            ->from("approve_details")
            ->join($ids, $ids . '.id=approve_details.application_id', 'left')
            ->join('company', 'company.id=' . $ids . '.company_id', 'left')
            ->where('table_name', $ids)
            ->where('approve_status', 0)
            ->where('aprrover_id', $this->session->userdata('user_id'))
            ->group_by('approve_details.id')
            ->add_column("Actions", $action, "id,approves_id")
            ->unset_column('approves_id');
        echo $this->datatables->generate();
    }


    public function update_status($id=null)
    {

        $this->form_validation->set_rules('status', lang("status"), 'trim|required');
        $this->form_validation->set_rules('note', lang("note"), 'trim|required');
        if ($this->input->post('id')) {
            $ids = $this->input->post('id');
        }
        $info = $this->approval_model->getApproval($id);
        if ($this->form_validation->run() == true) {
            $status = $this->input->post('status');
            $note = $this->sma->clear_tags($this->input->post('note'));
        } elseif ($this->input->post('update')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'sales');
        }

        if ($this->form_validation->run() == true) {
            $approve_details_new = null;
            $approve_details_previous = null;
            $info_new = null;
//        if ($this->form_validation->run() == true && $this->sales_model->updateStatus($id, $status, $note)) {
            $getNextApproval = $this->getNextApproval($info->next_approve_seq, $info->table_name, $info->created_by, $info->created_date,$info->application_id);
            // update current approval details
            $previous_approve_data = array(
                'approve_status' => 1,
                'updated_by' => $this->session->userdata('user_id'),
                'status' => 'Approved',
                'updated_date' => date("Y-m-d H:i:s"),
                'comments'=>$note
            );
            // update requisition
            $info_update = array(
                'updated_by' => $this->session->userdata('user_id'),
                'updated_date' => date("Y-m-d H:i:s")

            );
            // close requisition
            $info_close = array(
                'updated_by' => $this->session->userdata('user_id'),
                'status' => 'Approved',
                'approved' => 1,
                'updated_date' => date("Y-m-d H:i:s")

            );
            $approve_details_previous = $previous_approve_data;        // update
            if ($getNextApproval) {
                // if fount next level
                $approve_details_new = $getNextApproval['approve_data']; // insert
                $info_update['status'] = $getNextApproval['status'];
                $info_update['next_approve_seq'] = $approve_details_new['next_approve_seq'];
                $info_new = $info_update;                                       // update

            } else {
                // if not fount next level
                $approve_details_new = null;
                $info_close['next_approve_seq'] =0;
                $info_close['status'] ='Approved';
                $info_new = $info_close;
            }

        }
        if ($this->form_validation->run() == true && $this->approval_model->updateStatus($approve_details_new, $approve_details_previous, $info_new,$id,$info->application_id,$info->table_name)) {
            $this->session->set_flashdata('message', lang('status_updated'));
            redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'approval/approval_list/' . $info->table_name);
        } else {
            $this->data['approve'] = $info;
            $this->data['id'] = $id;
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'approval/update_status', $this->data);

        }
    }


    function getNextApproval($next_id, $table_naame,$created_by,$created_date,$application_id)
    {
        $userLists = $this->site->getAllUser();
        $info_details= $this->approval_model->getNextApprovals($next_id, $table_naame);
        $user_details = $this->getApproveCustomer($userLists,$info_details->approver_id);
        $details=array();
        $approve_data = array(
            'aprrover_id' => $info_details->approver_id,
            'status' => 'Waiting For Approval-' . $user_details->username,
            'table_name' => $table_naame,
            'approver_seq' => $info_details->approver_seq,
            'approver_seq_name' => $info_details->approver_seq_name,
            'created_by' => $created_by,
            'interface_name' => $info_details->interface_name,
            'next_approve_seq' => $info_details->approver_next_seq,
            'application_id' => $application_id,
            'created_date' => $created_date
        );


        if($info_details && $user_details) {
            $details['approve_data']=$approve_data;
            $details['status']='Waiting For Approval-' . $user_details->username;
            return $details;
        };
        return false;

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