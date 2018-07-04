<?php
/**
 * Created by PhpStorm.
 * User: a.kader
 * Date: 12-Jun-18
 * Time: 1:15 PM
 */

class Employees_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getAllBillerCompanies()
    {
        $q = $this->db->get_where('companies', array('group_name' => 'biller'));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getAllCompanies()
    {
        $q = $this->db->get("company");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getAllOperators()
    {
        $q = $this->db->get("operators");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getAllPackages()
    {
        $q = $this->db->get("packages");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getAllDesignations()
    {
        $q = $this->db->get("designations");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function addEmployee($data = array())
    {
        if ($this->db->insert('employees', $data)) {
            $cid = $this->db->insert_id();
            return $cid;
        }
        return false;
    }
    public function updateEmployee($id, $data = array())
    {
        $this->db->where('id', $id);
        if ($this->db->update('employees', $data)) {
            return true;
        }
        return false;
    }

    public function getEmployeeById($id)
    {
        $q = $this->db->get_where('employees', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }


    public function deleteEmployee($id)
    {
        if ($this->db->delete('employees', array('id' => $id)))  return true;
        else return FALSE;
    }

    public function getDesignationByCode($id)
    {
        $q = $this->db->get_where('designations', array('code' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }


    public function addEmployees($data)
    {
        if ($this->db->insert_batch('employees', $data)) {
            return true;
        }
        return false;
    }
}