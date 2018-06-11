<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Time_scheduler extends MY_Controller {

    const TIME_SCHEDULER_SESSION_NAME = 'TS_USER';

    function __construct() {
        parent::__construct();

        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            redirect('login');
        }

        $this->load->library('form_validation');
        $this->load->library('Ion_auth');
        $this->load->model('time_scheduler_model');
    }

    function index($action = NULL) {
        //$this->sma->checkPermissions();

        $time_user = $this->session->userdata(self::TIME_SCHEDULER_SESSION_NAME);

        if (isset($time_user['user']) && (!empty($time_user['user']))) {
            $this->time_scheduler_model->setEID($time_user['user']->id);
            $this->data['status'] = $this->time_scheduler_model->getStatus();
        } else {
            $this->data['status'] = false;
        }

        $this->load->view($this->theme . 'time_scheduler/index', $this->data);
    }

    function ajax_login() {
        $result = $this->ion_auth->reloginConfirmation($this->input->post('email'), $this->input->post('password'));

        if ($result !== false) {
            $this->session->set_userdata(self::TIME_SCHEDULER_SESSION_NAME, array(
                'user' => $result
            ));

            echo 1;
        } else {
            echo 0;
        }
    }

    function ajax_logout() {
        $this->session->unset_userdata(self::TIME_SCHEDULER_SESSION_NAME);
    }

    function ajax_refresh() {
        $user = $this->session->userdata(self::TIME_SCHEDULER_SESSION_NAME);

        if (isset($user['user']) && !empty($user['user'])) {
            $this->time_scheduler_model->setEID($user['user']->id);
            $status = $this->time_scheduler_model->getStatus();

            $user = $user['user'];
        } else {
            $user = false;
            $status = false;
        }

        $this->session->set_userdata(self::TIME_SCHEDULER_SESSION_NAME, array(
            'user' => $user,
            'status' => $status
        ));

        echo json_encode($this->session->userdata(self::TIME_SCHEDULER_SESSION_NAME));
    }

    function ajax_punch_time() {
        $user = $this->session->userdata(self::TIME_SCHEDULER_SESSION_NAME);

        if ($user) {
            $this->time_scheduler_model->setEID($user['user']->id);
            $workStat = $this->time_scheduler_model->getStatus();


            if ($this->input->post('time_type') == 'time_in' && (!$workStat || ($workStat && $workStat->time_out != NULL))) {
                $this->time_scheduler_model->registerTimeIn();
            }

            if ($this->input->post('time_type') == 'time_out' && $workStat->time_out == NULL) {
                $this->time_scheduler_model->registerTimeOut();
            }
        }

        echo $this->db->last_query();
    }

    public function update_time_schedule() {
        $this->sma->checkPermissions();

        if (!($this->Owner || $this->Admin)) {
            redirect('/welcome');
        }

        $this->db->select('time_schedule_status_id AS id');
        $this->db->where('handle', $this->input->post('timeStatus'));
        $objTimeStatus = $this->db->get('time_schedule_status')->row();

        if ($objTimeStatus) {
            if ($this->input->post('id')) {
                $arrData = array(
                    'time_in' => $this->input->post('timeIn'),
                    'time_out' => $this->input->post('timeOut'),
                    'time_schedule_status_id' => $objTimeStatus->id
                );

                $this->db->where('time_schedule_id', $this->input->post('id'));
                $this->db->update('time_schedule', $arrData);
            } else {
                $arrData = array(
                    'user_id' => $this->input->post('userID'),
                    'time_in' => $this->input->post('timeIn'),
                    'time_out' => $this->input->post('timeOut'),
                    'time_date' => $this->input->post('timeDate') ? date('Y-m-d', strtotime($this->input->post('timeDate'))) : '',
                    'ip_address' => $_SERVER['REMOTE_ADDR'],
                    'time_schedule_status_id' => $objTimeStatus->id
                );

                $this->db->insert('time_schedule', $arrData);
            }

            echo 1;
        } else {
            echo 0;
        }
    }

}
