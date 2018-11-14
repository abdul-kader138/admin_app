<?php
/**
 * Created by PhpStorm.
 * User: a.kader
 * Date: 12-Nov-18
 * Time: 9:57 AM
 */

class Document_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }



    public function addDocument($data = array())
    {
        if ($this->db->insert('documents', $data)) {
            $cid = $this->db->insert_id();
            return $cid;
        }
        return false;
    }

    public function getDocumentById($id)
    {
        $q = $this->db->get_where('documents', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function deleteDocument($id)
    {
        if ($this->db->delete('documents', array('id' => $id)))  return true;
        else return FALSE;
    }

    public function updateDocument($id, $data = array())
    {
        $this->db->where('id', $id);
        if ($this->db->update('documents', $data)) {
            return true;
        }
        return false;
    }

    public function addDocumentMovement($data = array())
    {
        if ($this->db->insert('document_movement', $data)) {
            $cid = $this->db->insert_id();
            return $cid;
        }
        return false;
    }

    public function getDocumentMovementById($id)
    {
        $q = $this->db->get_where('document_movement', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }


    public function deleteDocumentMovement($id)
    {
        if ($this->db->delete('document_movement', array('id' => $id)))  return true;
        else return FALSE;
    }

} 