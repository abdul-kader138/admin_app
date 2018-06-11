<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Time_scheduler_model extends CI_Model {

    private $EmployeeID;

    public function __construct() {
        parent::__construct();
    }

    public function setEID($id) {
        $this->EmployeeID = $id;
    }

    public function getStatus() {
        $status = $this->db->select('time_in,time_out, time_date')->where(array(
                    'user_id' => $this->EmployeeID,
                    'time_date' => date('Y-m-d')
                ))->order_by('time_schedule_id', 'desc')->get('time_schedule')->row();

        return is_object($status) ? $status : false;
    }

    public function registerTimeIn() {
        $this->db->insert('time_schedule', array(
            'user_id' => $this->EmployeeID,
            'time_in' => date('H:i:s'),
            'ip_address' => $_SERVER['REMOTE_ADDR'],
            'time_date' => date('Y-m-d'),
            'time_schedule_status_id' => 3 //FOR REVIEW
        ));
    }

    public function registerTimeOut() {
        $timeID = $this->db->select_max('time_schedule_id')->where('user_id', $this->EmployeeID)->get('time_schedule')->row();

        $this->db->where(array(
            'user_id' => $this->EmployeeID,
            'time_schedule_id' => $timeID->time_schedule_id
        ))->update('time_schedule', array(
            'time_out' => date('H:i:s'),
            'ip_address' => $_SERVER['REMOTE_ADDR']
        ));
    }

}
