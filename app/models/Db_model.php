<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Db_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }


    public function getTotalMRApproval($id = NULL)
    {
        $this->db->select('count(id) as total', FALSE)
            ->where('aprrover_id ', $id)
            ->where('approve_status ', 0);
        $q = $this->db->get('approve_details');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }


}
