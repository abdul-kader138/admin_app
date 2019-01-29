<?php defined('BASEPATH') OR exit('No direct script access allowed');

class HR_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();

    }


    public function addMR($data = array(),$approve_data = array())
    {
        $this->db->trans_strict(TRUE);
        $this->db->trans_start();
        $this->db->insert('manpower_requisition', $data);
        $cid = $this->db->insert_id();
        $approve_data['application_id'] = $cid;
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


    public function getUsersByID($id)
    {
        $q = $this->db->get_where('users', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }


}