<?php
/**
 * Created by PhpStorm.
 * User: a.kader
 * Date: 24-Jan-19
 * Time: 3:40 PM
 */

class Hrms extends  MY_Controller
{

    function __construct()
    {
        parent::__construct();

        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }
        if (!$this->Owner) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        $this->lang->load('billers', $this->Settings->user_language);
        $this->load->library('form_validation');
        $this->load->model('hr_model');
    }


    public function add_manpower_requisition()
    {
        if (!$this->Owner && !$this->Admin) {
            $get_permission = $this->permission_details[0];
//            if ((!$get_permission['document-add'])) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
            redirect($_SERVER["HTTP_REFERER"]);
//            }
        }

        $this->data['title'] = "Add Document";
        $this->form_validation->set_rules('name', lang("name"), 'trim|required');
        $this->form_validation->set_rules('reference_no', lang("reference_no"), 'trim');
        $this->form_validation->set_rules('company_id', lang("company_id"), 'trim|required');
        $this->form_validation->set_rules('status_id', lang("status_id"), 'trim|required');
        $this->form_validation->set_rules('doctype_id', lang("doctype_id"), 'trim|required');
        $this->form_validation->set_rules('other_info', lang("other_info"), 'trim');

        if ($this->form_validation->run() == true) {

            $doc_url = "";

            $data = array(
                'name' => $this->input->post('name'),
                'reference_no' => $this->input->post('reference_no'),
                'company_id' => $this->input->post('company_id'),
                'status_id' => $this->input->post('status_id'),
                'doctype_id' => $this->input->post('doctype_id'),
                'created_by' => $this->session->userdata('user_id'),
                'created_date' => date("Y-m-d H:i:s"),
                'other_info' => $this->input->post('other_info')
            );

//            if ($_FILES['document']['size'] > 0) {
//                $this->load->library('upload');
//                $config['upload_path'] = $this->upload_path;
//                $config['allowed_types'] = $this->digital_file_types;
//                $config['max_size'] = $this->allowed_file_size;
//                $config['overwrite'] = true;
//                $this->upload->initialize($config);
//                if (!$this->upload->do_upload('document')) {
//                    $error = $this->upload->display_errors();
//                    $this->session->set_flashdata('error', $error);
//                    redirect($_SERVER["HTTP_REFERER"]);
//                }
//                $doc_url = (($this->upload_path) . "/" . ($photo = $this->upload->file_name));
//                $data['url'] = $doc_url;
//                $data['attachment_name'] = $this->upload->file_name;
//
//            }

        }

//        if ($this->form_validation->run() == true && $this->hr_model->addDocument($data)) {
        if ($this->form_validation->run() == true) {
            $this->session->set_flashdata('message', lang("doc_added"));
            redirect("document/index");

        } else {
            $data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['companies'] = $this->site->getAllCompany();
            $this->data['designations'] = $this->site->getAllDesignation();

            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('document'), 'page' => lang('document')), array('link' => '#', 'page' => lang('add_document')));
            $meta = array('page_title' => lang('add_document'), 'bc' => $bc);
            $this->page_construct('hr/add_manpower_requisition', $meta, $this->data);
        }
    }
}