<?php if ( ! defined('BASEPATH')) exit('No direct access allowed');

class Extensions extends Admin_Controller {

   	public function __construct() {
		parent::__construct();

        $this->load->model('Extensions_model');

        $this->lang->load('extensions');
    }

	public function index() {
        $this->user->restrict('Admin.Modules');

        $this->template->setTitle($this->lang->line('text_title'));
        $this->template->setHeading($this->lang->line('text_heading'));
        $this->template->setButton($this->lang->line('button_new'), array('class' => 'btn btn-primary', 'href' => page_url() .'/add'));

        $data['extensions'] = array();
        $results = $this->Extensions_model->getList(array('type' => 'module'));
        foreach ($results as $result) {
            if ($result['installed'] === TRUE) {
                $manage = 'uninstall';
            } else {
                $manage = 'install';
            }

            $data['extensions'][] = array(
				'extension_id' 	=> $result['extension_id'],
				'name' 			=> $result['name'],
				'title' 		=> $result['title'],
				'installed' 	=> $result['installed'],
				'type' 			=> $result['type'],
				'options' 		=> $result['options'],
				'edit' 			=> site_url('extensions/edit?action=edit&name='.$result['name'].'&id='.$result['extension_id']),
				'delete' 		=> site_url('extensions/edit?action=delete&name='.$result['name'].'&id='.$result['extension_id']),
				'manage'		=> site_url('extensions/edit?action='.$manage.'&name='.$result['name'].'&id='.$result['extension_id'])
			);
        }

        $this->template->setPartials(array('header', 'footer'));
        $this->template->render('extensions', $data);
    }

	public function edit() {
        $this->user->restrict('Admin.Modules.Access');

        $extension_name = $this->input->get('name');
		$action = $this->input->get('action');
		$loaded = FALSE;
        $error_msg = FALSE;

        if ($extension = $this->Extensions_model->getExtension('module', $extension_name, FALSE)) {

            $data['extension_name'] = $extension['name'];
            $ext_controller = $extension['name'] . '/admin_' . $extension['name'];
            $ext_class = strtolower('admin_'.$extension['name']);

            if (isset($extension['installed'], $extension['config'], $extension['options']) AND $action === 'edit') {
                if ($extension['config'] === FALSE) {
                    $error_msg = $this->lang->line('error_config');
                } else if ($extension['options'] === FALSE) {
                    $error_msg = $this->lang->line('error_options');
                } else if ($extension['installed'] === FALSE) {
                    $error_msg = $this->lang->line('error_installed');
                } else {
                    $this->load->module($ext_controller);
                    if (class_exists($ext_class, FALSE)) {
                        $data['extension'] = $this->{$ext_class}->index($extension);
                        $loaded = TRUE;
                    } else {
                        $error_msg = sprintf($this->lang->line('error_failed'), $extension_name);
                    }
                }
            }
        }

        if ($this->input->get('name') AND $this->input->get('action') AND $action !== 'edit') {
            $_POST = $_GET;

            if ($this->input->get('action') === 'install' AND $this->_install() === TRUE) {
                redirect('extensions');
            } else if ($this->input->get('action') === 'uninstall' AND $this->_uninstall() === TRUE) {
                redirect('extensions');
            } else if ($this->input->get('action') === 'delete' AND $this->_delete() === TRUE) {
                redirect('extensions');
            }
        }

        if (!$loaded OR $error_msg) {
            $this->alert->set('warning', $error_msg);
            redirect(referrer_url());
        }

		$this->template->setPartials(array('header', 'footer'));
		$this->template->render('extensions_edit', $data);
	}

	public function add() {
        $this->user->restrict('Admin.Modules.Access');

        $this->template->setTitle($this->lang->line('text_add_heading'));
        $this->template->setHeading($this->lang->line('text_add_heading'));

        $this->template->setButton($this->lang->line('button_upload'), array('class' => 'btn btn-primary', 'onclick' => '$(\'#edit-form\').submit();'));
        $this->template->setButton($this->lang->line('button_upload_close'), array('class' => 'btn btn-default', 'onclick' => 'saveClose();'));
        $this->template->setBackButton('btn btn-back', site_url('extensions'));

        $data['_action']	= site_url('extensions/add');

        if ($this->_uploadExtension() === TRUE) {
            if ($this->input->post('save_close') === '1') {
                redirect('extensions');
            }

            redirect('extensions/add');
        }

		$this->template->setPartials(array('header', 'footer'));
		$this->template->render('extensions_add', $data);
	}

    private function _install() {
        $this->user->restrict('Admin.Modules.Manage');

        if ($this->input->get('action') === 'install') {
            if ($this->Extensions_model->extensionExists($this->input->get('name'))) {
                if ($this->Extensions_model->install('module', $this->input->get('name'), $this->input->get('id'))) {
                    log_activity($this->user->getStaffId(), 'installed', 'extensions', get_activity_message('activity_custom_no_link',
                        array('{staff}', '{action}', '{context}', '{item}'),
                        array($this->user->getStaffName(), 'installed', 'extension module', $this->input->get('name'))
                    ));

                    $this->alert->set('success', sprintf($this->lang->line('alert_success'), 'Extension installed '));
                    return TRUE;
                }
            }

            $this->alert->danger_now($this->lang->line('alert_error_try_again'));
            return TRUE;
        }
    }

    private function _uninstall() {
        $this->user->restrict('Admin.Modules.Manage');

        if ($this->input->get('action') === 'uninstall') {
            if ($this->Extensions_model->uninstall('module', $this->input->get('name'), $this->input->get('id'))) {
                log_activity($this->user->getStaffId(), 'uninstalled', 'extensions', get_activity_message('activity_custom_no_link',
                    array('{staff}', '{action}', '{context}', '{item}'),
                    array($this->user->getStaffName(), 'uninstalled', 'extension module', $this->input->get('name'))
                ));

                $this->alert->set('success', sprintf($this->lang->line('alert_success'), 'Extension uninstalled '));
                return TRUE;
            }

            $this->alert->danger_now($this->lang->line('alert_error_try_again'));
            return TRUE;
        }
	}

    private function _delete() {
        $this->user->restrict('Admin.Modules.Delete');

        if ($this->input->get('action') === 'delete') {
            if ($this->Extensions_model->extensionExists($this->input->get('name'))) {
                if ($this->Extensions_model->delete('module', $this->input->get('name'), $this->input->get('id'))) {
                    $this->alert->set('success', sprintf($this->lang->line('alert_success'), 'Extension deleted '));
                    return TRUE;
                }
            }

            $this->alert->danger_now($this->lang->line('alert_error_try_again'));
            return TRUE;
        }
    }

    private function _uploadExtension() {
        $this->user->restrict('Admin.Modules.Add', site_url('extensions/add'));

        if (isset($_FILES['extension_zip'])) {
            if ($this->validateUpload() === TRUE) {
                if ($this->Extensions_model->upload('module', $_FILES['extension_zip'])) {
                    log_activity($this->user->getStaffId(), 'uploaded', 'extensions', get_activity_message('activity_custom_no_link',
                        array('{staff}', '{action}', '{context}', '{item}'),
                        array($this->user->getStaffName(), 'uploaded', 'extension', $_FILES['extension_zip']['name'])
                    ));

                    $this->alert->set('success', sprintf($this->lang->line('alert_success'), 'Extension uploaded '));
                    return TRUE;
                }

                $this->alert->danger_now($this->lang->line('alert_error_try_again'));
            }
        }

        return FALSE;
    }

    private function validateUpload() {
        if (!empty($_FILES['extension_zip']['name']) AND !empty($_FILES['extension_zip']['tmp_name'])) {

            if ($_FILES['extension_zip']['type'] !== 'application/zip') {
                $this->alert->danger_now($this->lang->line('error_upload'));
                return FALSE;
            }

            $_FILES['extension_zip']['name'] = html_entity_decode($_FILES['extension_zip']['name'], ENT_QUOTES, 'UTF-8');
            $_FILES['extension_zip']['name'] = str_replace(array('"', "'", "/", "\\"), "", $_FILES['extension_zip']['name']);
            $filename = $this->security->sanitize_filename($_FILES['extension_zip']['name']);
            $_FILES['extension_zip']['name'] = basename($filename);

            if (!empty($_FILES['extension_zip']['error'])) {
                $this->alert->danger_now($this->lang->line('error_php_upload'). $_FILES['extension_zip']['error']);
                return FALSE;
            }

            if (is_uploaded_file($_FILES['extension_zip']['tmp_name'])) return TRUE;
            return FALSE;
        }
    }
}

/* End of file extensions.php */
/* Location: ./admin/controllers/extensions.php */