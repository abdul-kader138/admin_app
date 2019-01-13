<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
ini_set('memory_limit', '-1');
// set max execution time 2 hours / mostly used for exporting PDF
ini_set('max_execution_time', 3600);

class Filemanager extends MY_Controller
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
        $this->load->model('document_model');
        $this->digital_file_types = 'zip|psd|ai|rar|pdf|doc|docx|xls|xlsx|ppt|pptx|gif|jpg|jpeg|png|tif|txt';
        $this->allowed_file_size = '10';
    }

    public function index()
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
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('File_Manager')));
        $meta = array('page_title' => lang('File_Manager'), 'bc' => $bc);
        $this->page_construct('filemanager/filemanager', $meta, $this->data);
    }

    public function elfinder_init()
    {

        if (!$this->Owner && !$this->Admin) {
            $get_permission = $this->permission_details[0];
            if ((!$get_permission['document-index'])) {
                $this->session->set_flashdata('warning', lang('access_denied'));
                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
                redirect($_SERVER["HTTP_REFERER"]);
            }
        }

        //
        $this->load->helper('path');
        $_allowed_files = explode('|', $this->digital_file_types);
        $config_allowed_files = array();
        if (is_array($_allowed_files)) {
            foreach ($_allowed_files as $v_extension) {
                array_push($config_allowed_files, '.' . $v_extension);
            }
        }

        $allowed_files = array();
        if (is_array($config_allowed_files)) {
            foreach ($config_allowed_files as $extension) {
                $_mime = get_mime_by_extension($extension);

                if ($_mime == 'application/x-zip') {
                    array_push($allowed_files, 'application/zip');
                }
                if ($extension == '.exe') {
                    array_push($allowed_files, 'application/x-executable');
                    array_push($allowed_files, 'application/x-msdownload');
                    array_push($allowed_files, 'application/x-ms-dos-executable');
                }
                array_push($allowed_files, $_mime);
            }
        }

        $root_options = array(
            'driver' => 'LocalFileSystem',
            'path' => set_realpath('assets/uploads'),
            'URL' => site_url('assets/uploads/'),
            'uploadMaxSize' => $this->allowed_file_size . 'M',
            'accessControl' => 'access',
            'uploadAllow' => $allowed_files,

            //  to  disable all folder creation
//            'disabled' => array('extract', 'archive', 'mkdir'),
          //  to  disable all folder download
         //   'disabled' => array('zipdl'),
         //  to  stop all upload
//            'uploadDeny'  => array('all'),
        //  to  disable all delete
//            'disabled' => array('rename', 'rm'),
            'uploadOrder' => array(
                'allow',
                'deny'
            ),
            'attributes' => array(
                array(
                    'pattern' => '/.tmb/',
                    'hidden' => true
                ),
                array(
                    'pattern' => '/.quarantine/',
                    'hidden' => true
                ),
                array(
                    // You can also set permissions for file types by adding, for example, \.jpg inside pattern.
                    // The path for pattern evaluation is passed by converting the path as the root to '/'.
//                    'pattern' => '/^\/TEST$/',
                    'read'    => true,
                    'write'   => true,
//                    'locked'  => true
                )
            )
        );


        if ($this->session->userdata('user_type') == 3) {
            $user = $this->db->where('user_id', $this->session->userdata('user_id'))->get('tbl_users')->row();
            $user->media_path_slug="";
//            $path = set_realpath('filemanager_doc/' . $user->media_path_slug);
            $path = set_realpath('filemanager_doc/' . $user->media_path_slug);
            if (empty($user->media_path_slug)) {
                $this->db->where('user_id', $user->user_id);
                $slug = slug_it($user->username);
                $this->db->update('tbl_users', array(
                    'media_path_slug' => $slug
                ));
                $user->media_path_slug = $slug;
                $path = set_realpath('filemanager/' . $user->media_path_slug);
            }
            if (!is_dir($path)) {
                mkdir($path);
            }
            if (!file_exists($path . '/index.html')) {
                fopen($path . '/index.html', 'w');
            }
            array_push($root_options['attributes'], array(
                'pattern' => '/.(' . $user->media_path_slug . '+)/', // Prevent deleting/renaming folder
                'read' => true,
                'write' => true
            ));
            $root_options['path'] = $path;
            $root_options['URL'] = site_url('filemanager/' . $user->media_path_slug) . '/';
        }



        $opts = array(
            'roots' => array(
                $root_options
            )
        );
        $this->load->library('elfinder_lib', $opts);
    }
}