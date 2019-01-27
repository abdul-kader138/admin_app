<?php defined('BASEPATH') OR exit('No direct script access allowed');

class HR_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();

    }


    public function addMR($data = array())
    {
        $this->db->trans_strict(TRUE);
        $approver_details = $this->getApproverList('add_manpower_requisition');
//        $user_details = $this->getAllUser();
        $user_details = $this->getUsersByID($approver_details->approver_id);

        $data['status'] = 'Waiting For Approval-' . $user_details[0]->username;
//        $data['status']='Waiting For Approval-';
        $data['next_approve_by'] = $approver_details->approver_id;
        $this->db->trans_start();
        $this->db->insert('manpower_requisition', $data);
        $cid = $this->db->insert_id();
        $approve_data = array(
            'aprrover_id' => $approver_details->approver_id,
            'status' => 'Waiting For Approval-' . $user_details[0]->username,
            'status' => 'Waiting For Approval-',
            'table_name' => 'manpower_requisition',
            'approver_seq' => $approver_details->approver_seq,
            'created_by' => $this->session->userdata('user_id'),
            'created_date' => date("Y-m-d H:i:s"),
            'application_id' => $cid
        );
        $this->db->insert('approve_details', $approve_data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) return false;
        return true;
    }

    public function getMRById($id)
    {
        $q = $this->db->get_where('manpower_requisition', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function deleteMR($id)
    {
        $this->db->trans_strict(TRUE);
        $this->db->trans_start();
        $this->db->delete('manpower_requisition', array('id' => $id));
        $this->db->delete('approve_details', array('application_id' => $id));
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) return false;
        return true;
    }

    public function updateMR($id, $data = array())
    {
        $this->db->where('id', $id);
        if ($this->db->update('manpower_requisition', $data)) {
            return true;
        }
        return false;
    }

    public function getCompanyById($id)
    {
        $q = $this->db->get_where('company', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getDesignationById($id)
    {
        $q = $this->db->get_where('designations', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getApproverList($name)
    {
        $q = $this->db->get_where('approver_list', array('interface_name' => $name), 1);
        $this->db->order_by('approver_seq', 'asc');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }


    public function getUsersByID($ids)
    {
//        $query = $this->db->select('id, username, email,gender')
//            ->where('id', $ids)
//            ->get('users')
//            ->limit(1);
//        $q = $this->db->get_where('approve_details', array('id' => 2336), 1);
        $q = $this->db->get_where('users', array("username" => "owner"), 1);
//        $this->db->order_by('id', 'asc');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getAllUser()
    {
        $q = $this->db->get('approver_list');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

}