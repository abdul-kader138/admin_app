<?php defined('BASEPATH') OR exit('No direct script access allowed');

class HR_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();

    }


    public function addMR($data = array())
    {
        if ($this->db->insert('manpower_requisition', $data)) {
            $cid = $this->db->insert_id();
            return $cid;
        }
        return false;
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
        if ($this->db->delete('manpower_requisition', array('id' => $id)))  return true;
        else return FALSE;
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


}