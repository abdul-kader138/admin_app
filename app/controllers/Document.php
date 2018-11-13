<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: a.kader
 * Date: 12-Nov-18
 * Time: 9:55 AM
 */

class Document  extends MY_Controller
{

    function __construct()
    {
        parent::__construct();

        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }
        $this->permission_details = $this->site->checkPermissions();
        $this->lang->load('document', $this->Settings->user_language);
        $this->digital_upload_path = 'files/';
        $this->upload_path = 'assets/uploads/document';
        $this->thumbs_path = 'assets/uploads/thumbs/';
        $this->image_types = 'gif|jpg|jpeg|png|tif';
        $this->digital_file_types = 'zip|psd|ai|rar|pdf|doc|docx|xls|xlsx|ppt|pptx|gif|jpg|jpeg|png|tif|txt';
        $this->allowed_file_size = '5080';
        $this->data['logo'] = true;
        $this->load->library('form_validation');
        $this->load->model('document_model');
    }


    function index()
    {
        if (!$this->Owner && !$this->Admin) {
            $get_permission = $this->permission_details[0];
            if ((!$get_permission['document-index'])) {
                $this->session->set_flashdata('warning', lang('access_denied'));
                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
                redirect($_SERVER["HTTP_REFERER"]);
            }
        }
        $data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('document')));
        $meta = array('page_title' => lang('document'), 'bc' => $bc);
        $this->page_construct('document/index', $meta, $this->data);
    }


    function getDocuments()
    {
        if (!$this->Owner && !$this->Admin) {
            $get_permission = $this->permission_details[0];
            if ((!$get_permission['document-index'])) {
                $this->session->set_flashdata('warning', lang('access_denied'));
                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
                redirect($_SERVER["HTTP_REFERER"]);
            }
        }
        $edit_link="";
        $delete_link="";
        if($get_permission['document-edit'] || $this->Owner || $this->Admin) $edit_link = anchor('document/edit/$1', '<i class="fa fa-edit"></i> ' . lang('doc_edit'), 'class="sledit"');
        if($get_permission['document-delete'] || $this->Owner || $this->Admin) $delete_link = "<a href='#' class='po' title='<b>" . lang("doc_delete") . "</b>' data-content=\"<p>"
            . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . site_url('document/delete/$1') . "'>"
            . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i> "
            . lang('doc_delete') . "</a>";
        $action = '<div class="text-center"><div class="btn-group text-left">'
            . '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">'
            . lang('actions') . ' <span class="caret"></span></button>
        <ul class="dropdown-menu pull-right" role="menu">
            <li>' . $edit_link . '</li>
            <li>' . $delete_link . '</li>
        </ul>
    </div></div>';
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->load->library('datatables');
        $this->datatables
            ->select($this->db->dbprefix('documents') . ".id as id, " . $this->db->dbprefix('documents') . ".name as nam," . $this->db->dbprefix('documents') . ".reference_no as ref,". $this->db->dbprefix('company') . ".name as c_name,upper(" . $this->db->dbprefix('documents') . ".status_id) as status," . $this->db->dbprefix('doctype') . ".name as p_name,  concat(" . $this->db->dbprefix('documents') . ".url,'#351#',"  . $this->db->dbprefix('documents') . ".attachment_name) as url,")
            ->from("documents")
            ->join('company', 'documents.company_id=company.id', 'left')
            ->join('doctype', 'documents.doctype_id=doctype.id', 'left')
            ->group_by('documents.id')
            ->add_column("Actions", $action, "id");
        echo $this->datatables->generate();
    }

    public function add()
    {
        if (!$this->Owner && !$this->Admin) {
            $get_permission = $this->permission_details[0];
            if ((!$get_permission['document-add'])) {
                $this->session->set_flashdata('warning', lang('access_denied'));
                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
                redirect($_SERVER["HTTP_REFERER"]);
            }
        }

        $this->data['title'] = "Add Document";
        $this->form_validation->set_rules('name', lang("name"), 'trim|required|is_unique[documents.name]');
        $this->form_validation->set_rules('reference_no', lang("reference_no"), 'trim');
        $this->form_validation->set_rules('company_id', lang("company_id"), 'trim|required');
        $this->form_validation->set_rules('status_id', lang("status_id"), 'trim|required');
        $this->form_validation->set_rules('doctype_id', lang("doctype_id"), 'trim|required');
        $this->form_validation->set_rules('other_info', lang("other_info"), 'trim');

        if ($this->form_validation->run() == true) {

            $doc_url="";

            $data = array(
                'name' => $this->input->post('name'),
                'reference_no' => $this->input->post('reference_no'),
                'company_id' => $this->input->post('company_id'),
                'status_id' => $this->input->post('status_id'),
                'doctype_id' => $this->input->post('doctype_id'),
                'created_by' => $this->session->userdata('user_id'),
                'created_date' => date("Y-m-d H:i:s"),
                'other_info' => other_info,
            );

            if ($_FILES['document']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path'] = $this->upload_path;
                $config['allowed_types'] = $this->digital_file_types;
                $config['max_size'] = $this->allowed_file_size;
                $config['overwrite'] = true;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('document')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER["HTTP_REFERER"]);
                }
                $doc_url=(($this->upload_path) ."/". ( $photo = $this->upload->file_name));
                $data['url'] = $doc_url;
                $data['attachment_name'] = $this->upload->file_name;

            }

        }

        if ($this->form_validation->run() == true && $this->document_model->addDocument($data)) {
            $this->session->set_flashdata('message', lang("employee_added"));
            redirect("document/index");

        } else {
            $data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['companies'] = $this->site->getAllCompany();
            $this->data['doctypes'] = $this->site->getAllDocType();

            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('document'), 'page' => lang('document')), array('link' => '#', 'page' => lang('add_document')));
            $meta = array('page_title' => lang('add_document'), 'bc' => $bc);
            $this->page_construct('document/add_document', $meta, $this->data);
        }
    }

    function delete($id = NULL){
        if (!$this->Owner && !$this->Admin) {
            $get_permission = $this->permission_details[0];
            if ((!$get_permission['document-delete'])) {
                $this->session->set_flashdata('warning', lang('access_denied'));
                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
                redirect($_SERVER["HTTP_REFERER"]);
            }
        }

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $documents_details=$this->document_model->getDocumentById($id);
        $delete_file=true;
        if($documents_details->attachment_name){
            $this->load->helper("file");
            $delete_file=unlink("./assets/uploads/document/".$documents_details->attachment_name);
        }

        if ($this->document_model->deleteDocument($id) && $delete_file) {
            if ($this->input->is_ajax_request()) {
                echo lang("doc_deleted");
                die();
            }
            $this->session->set_flashdata('message', lang('doc_deleted'));
            redirect('document/index');
        }
    }


    function edit($id = NULL)
    {

        if (!$this->Owner && !$this->Admin) {
            $get_permission = $this->permission_details[0];
            if ((!$get_permission['document-edit'])) {
                $this->session->set_flashdata('warning', lang('access_denied'));
                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
                redirect($_SERVER["HTTP_REFERER"]);
            }
        }
        if ($this->input->post('id')) {
            $id = $this->input->post('id');
        }
        $this->data['title'] = lang("edit_doc");

        $this->data['title'] = "Edit Document";
        $this->form_validation->set_rules('name', lang("name"), 'trim|required');
        $this->form_validation->set_rules('reference_no', lang("reference_no"), 'trim');
        $this->form_validation->set_rules('company_id', lang("company_id"), 'trim|required');
        $this->form_validation->set_rules('status_id', lang("status_id"), 'trim|required');
        $this->form_validation->set_rules('doctype_id', lang("doctype_id"), 'trim|required');
        $this->form_validation->set_rules('other_info', lang("other_info"), 'trim');



        if ($this->form_validation->run() == true) {
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


            if ($_FILES['document']['size'] > 0) {
                $t=true;
                $document_details=$this->document_model->getDocumentById($id);
                if($document_details->attachment_name){
                    $this->load->helper("file");
                    $t=unlink("./assets/uploads/document/".$document_details->attachment_name);
                }

                $this->load->library('upload');
                $config['upload_path'] = $this->upload_path;
                $config['allowed_types'] = $this->digital_file_types;
                $config['max_size'] = $this->allowed_file_size;
                $config['overwrite'] = true;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('document')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER["HTTP_REFERER"]);
                }
                $doc_url=(($this->upload_path) ."/". ( $photo = $this->upload->file_name));
                $data['url'] = $doc_url;
                $data['attachment_name'] = $this->upload->file_name;

            }

        }

        if ($this->form_validation->run() === TRUE && $this->document_model->updateDocument($id, $data)) {
            $this->session->set_flashdata('message', lang('doc_updated'));
            redirect("document/index");
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['document'] = $this->document_model->getDocumentById($id);
            $this->data['companies'] = $this->site->getAllCompany();
            $this->data['doctypes'] = $this->site->getAllDocType();
            $bc = array(array('link' => site_url('home'), 'page' => lang('home')), array('link' => site_url('document/edit'), 'page' => lang('document')), array('link' => '#', 'page' => lang('doc_edit')));
            $meta = array('page_title' => lang('employees'), 'bc' => $bc);
            $this->page_construct('document/edit_document', $meta, $this->data);
        }
    }


} 