<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Created by PhpStorm.
 * User: a.kader
 * Date: 31-Jan-19
 * Time: 1:20 PM
 */
class Approval_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getApproval($id)
    {
        $q = $this->db->get_where('approve_details', array('id' => $id, 'approve_status' => 0));
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }


    public function getNextApprovals($id, $table_name)
    {
        $q = $this->db->get_where('approver_list', array('approver_seq' => $id, 'interface_name' => 'add_' . $table_name));
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function updateStatus($approve_details_new, $approve_details_previous, $info_new, $id,$application_id,$table_name)
    {
        $this->db->trans_strict(TRUE);
        //$approve_details_new
        $this->db->trans_start();
        $this->db->where('id',$application_id);
        $this->db->update('manpower_requisition', $info_new);
        $this->db->where('id',$id);
        $this->db->update('approve_details', $approve_details_previous);
        if($approve_details_new) $this->db->insert('approve_details',$approve_details_new);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) return false;
        return true;

    }
}

// need to add next_approve_seq at requisition table
// database save
// complete and decline option

