<?php if ( ! defined('BASEPATH')) exit('No direct access allowed');

class Settings extends Admin_Controller {
    
    public function __construct() {
		parent::__construct(); //  calls the constructor

        $this->user->restrict('Site.Settings.Manage');

        $this->load->model('Locations_model');
		$this->load->model('Settings_model');
		$this->load->model('Countries_model');
		$this->load->model('Currencies_model');
		$this->load->model('Statuses_model');
		$this->load->model('Categories_model');

        $this->lang->load('settings');
    }

	public function index() {
        $this->template->setTitle($this->lang->line('text_title'));
        $this->template->setHeading($this->lang->line('text_heading'));
		$this->template->setButton($this->lang->line('button_save'), array('class' => 'btn btn-primary', 'onclick' => '$(\'#edit-form\').submit();'));

        $this->template->setStyleTag(root_url('assets/js/fancybox/jquery.fancybox.css'), 'jquery-fancybox-css');
        $this->template->setScriptTag(root_url("assets/js/fancybox/jquery.fancybox.js"), 'jquery-fancybox-js');

        $post_data = $this->input->post();
		$config_items = $this->config->config;

        $data['current_time'] = mdate('%d-%m-%Y %H:%i:%s', time());

        foreach ($config_items as $key => $value) {
            if (isset($post_data[$key])) {
                $data[$key] = $post_data[$key];
            } else {
                $data[$key] = $value;
            }
        }

        $this->load->model('Image_tool_model');
        $data['no_photo'] = $this->Image_tool_model->resize('data/no_photo.png');
        if ($this->config->item('site_logo')) {
            $data['logo_val'] = $this->config->item('site_logo');
            $data['site_logo'] = $this->Image_tool_model->resize($data['logo_val']);
            $data['logo_name'] = basename($data['logo_val']);
        } else {
            $data['logo_val'] = 'data/no_photo.png';
            $data['site_logo'] = $this->Image_tool_model->resize('data/no_photo.png');
            $data['logo_name'] = 'no_photo.png';
        }

        if (is_array($data['main_address'])) {
            $main_address = $data['main_address'];
        } else {
            $main_address = array();
        }

        $data['main_address'] = array(
            'location_id' 	=> (isset($main_address['location_id'])) ? $main_address['location_id'] : '',
            'address_1' 	=> (isset($main_address['address_1'])) ? $main_address['address_1'] : '',
            'address_2' 	=> (isset($main_address['address_2'])) ? $main_address['address_2'] : '',
            'city' 			=> (isset($main_address['city'])) ? $main_address['city'] : '',
            'postcode' 		=> (isset($main_address['postcode'])) ? $main_address['postcode'] : '',
            'country_id' 	=> (isset($main_address['country_id'])) ? $main_address['country_id'] : ''
        );

        if (is_array($data['image_manager'])) {
            $image_manager = $data['image_manager'];
        } else {
            $image_manager = array();
        }

        $data['image_manager'] = array(
            'max_size' 				=> (isset($image_manager['max_size'])) ? $image_manager['max_size'] : '',
            'thumb_height' 			=> (isset($image_manager['thumb_height'])) ? $image_manager['thumb_height'] : '',
            'thumb_width' 			=> (isset($image_manager['thumb_width'])) ? $image_manager['thumb_width'] : '',
            'uploads' 				=> (isset($image_manager['uploads'])) ? $image_manager['uploads'] : '',
            'new_folder' 			=> (isset($image_manager['new_folder'])) ? $image_manager['new_folder'] : '',
            'copy' 					=> (isset($image_manager['copy'])) ? $image_manager['copy'] : '',
            'move' 					=> (isset($image_manager['move'])) ? $image_manager['move'] : '',
            'rename' 				=> (isset($image_manager['rename'])) ? $image_manager['rename'] : '',
            'delete' 				=> (isset($image_manager['delete'])) ? $image_manager['delete'] : '',
            'transliteration' 		=> (isset($image_manager['transliteration'])) ? $image_manager['transliteration'] : '',
            'remember_days' 		=> (isset($image_manager['remember_days'])) ? $image_manager['remember_days'] : '',
            'delete_thumbs'			=> site_url('settings/delete_thumbs'),
        );

        if (empty($data['customer_online_time_out'])) {
            $data['customer_online_time_out'] = '120';
        }

        if (empty($data['cache_time'])) {
            $data['cache_time'] = '0';
        }

        $data['page_limits'] = array('10', '20', '50', '75', '100');

        $data['protocols'] 	= array('mail', 'sendmail', 'smtp');
        $data['mailtypes'] 	= array('text', 'html');
        $data['thresholds'] = array('Disable', 'Error Only', 'Debug Only', 'Info Only', 'All');

		$timezones = $this->getTimezones();
		foreach ($timezones as $key => $value) {
			$data['timezones'][$key] = $value;
		}

		$data['countries'] = array();
		$results = $this->Countries_model->getCountries();
		foreach ($results as $result) {
			$data['countries'][] = array(
				'country_id'	=>	$result['country_id'],
				'name'			=>	$result['country_name'],
			);
		}

		$data['currencies'] = array();
		$currencies = $this->Currencies_model->getCurrencies();
		foreach ($currencies as $currency) {
			$data['currencies'][] = array(
				'currency_id'		=>	$currency['currency_id'],
				'currency_name'		=>	$currency['currency_name'],
				'currency_status'	=>	$currency['currency_status']
			);
		}

		$this->load->model('Languages_model');
		$data['languages'] = array();
		$results = $this->Languages_model->getLanguages();
		foreach ($results as $result) {
			$data['languages'][] = array(
				'language_id'	=>	$result['language_id'],
				'name'			=>	$result['name'],
			);
		}

		$this->load->model('Customer_groups_model');
		$data['customer_groups'] = array();
		$results = $this->Customer_groups_model->getCustomerGroups();
		foreach ($results as $result) {
			$data['customer_groups'][] = array(
				'customer_group_id'	=>	$result['customer_group_id'],
				'group_name'		=>	$result['group_name']
			);
		}

		$data['categories'] = array();
		$categories = $this->Categories_model->getCategories();
		foreach ($categories as $category) {
			$data['categories'][] = array(
				'category_id'	=>	$category['category_id'],
				'category_name'	=>	$category['name']
			);
		}

        $data['statuses'] = array();
		$results = $this->Statuses_model->getStatuses();
		foreach ($results as $result) {
			$data['statuses'][] = array(
				'status_id'		=> $result['status_id'],
				'status_name'	=> $result['status_name'],
				'status_for'	=> $result['status_for']
			);
		}

		$this->load->model('Pages_model');
		$data['pages'] = array();
		$results = $this->Pages_model->getPages();
		foreach ($results as $result) {
			$data['pages'][] = array(
				'page_id'		=>	$result['page_id'],
				'name'			=>	$result['name'],
			);
		}

		if ($this->input->post() AND $this->_updateSettings() === TRUE) {
			redirect('settings');
		}

		$this->template->setPartials(array('header', 'footer'));
		$this->template->render('settings', $data);
	}

	public function delete_thumbs() {
        if (file_exists(IMAGEPATH . 'thumbs')) {
            $this->_delete_thumbs(IMAGEPATH . 'thumbs/*');
            $this->alert->set('success', 'Thumbs deleted successfully.');
        }

		redirect('settings');
	}

	private function _updateSettings() {
        if ($this->validateForm() === TRUE) {
			$update = array(
                'site_name' 				=> $this->input->post('site_name'),
				'site_email' 				=> $this->input->post('site_email'),
				'site_logo' 				=> $this->input->post('site_logo'),
				'country_id' 				=> $this->input->post('country_id'),
				'timezone' 					=> $this->input->post('timezone'),
				'currency_id' 				=> $this->input->post('currency_id'),
				'language_id' 				=> $this->input->post('language_id'),
				'customer_group_id' 		=> $this->input->post('customer_group_id'),
				'page_limit' 				=> $this->input->post('page_limit'),
				'meta_description' 			=> $this->input->post('meta_description'),
				'meta_keywords' 			=> $this->input->post('meta_keywords'),
				'menus_page_limit' 			=> $this->input->post('menus_page_limit'),
				'show_menu_images' 			=> $this->input->post('show_menu_images'),
				'menu_images_h' 			=> $this->input->post('menu_images_h'),
				'menu_images_w' 			=> $this->input->post('menu_images_w'),
				'special_category_id' 		=> $this->input->post('special_category_id'),
				'registration_terms' 		=> $this->input->post('registration_terms'),
				'checkout_terms' 			=> $this->input->post('checkout_terms'),
				'registration_email'		=> $this->input->post('registration_email'),
				'customer_order_email'		=> $this->input->post('customer_order_email'),
				'customer_reserve_email'	=> $this->input->post('customer_reserve_email'),
				'main_address'				=> $this->input->post('main_address'),
				'maps_api_key'				=> $this->input->post('maps_api_key'),
				'distance_unit'				=> $this->input->post('distance_unit'),
				'future_orders' 			=> $this->input->post('future_orders'),
				'location_order'			=> $this->input->post('location_order'),
				'location_order_email'		=> $this->input->post('location_order_email'),
				'location_reserve_email'	=> $this->input->post('location_reserve_email'),
				'approve_reviews'			=> $this->input->post('approve_reviews'),
				'new_order_status'			=> $this->input->post('new_order_status'),
				'complete_order_status'		=> $this->input->post('complete_order_status'),
				'canceled_order_status'		=> $this->input->post('canceled_order_status'),
				'guest_order'				=> $this->input->post('guest_order'),
				'delivery_time'				=> $this->input->post('delivery_time'),
				'collection_time'			=> $this->input->post('collection_time'),
				'reservation_mode'			=> $this->input->post('reservation_mode'),
				'new_reservation_status'	=> $this->input->post('new_reservation_status'),
				'confirmed_reservation_status'	=> $this->input->post('confirmed_reservation_status'),
				'canceled_reservation_status'	=> $this->input->post('canceled_reservation_status'),
				'reservation_time_interval'		=> $this->input->post('reservation_time_interval'),
				'reservation_stay_time'			=> $this->input->post('reservation_stay_time'),
				'themes_allowed_img'		=> $this->input->post('themes_allowed_img'),
				'themes_allowed_file'		=> $this->input->post('themes_allowed_file'),
				'themes_hidden_files'		=> $this->input->post('themes_hidden_files'),
				'themes_hidden_folders'		=> $this->input->post('themes_hidden_folders'),
				'image_manager'				=> $this->input->post('image_manager'),
				'protocol'	 				=> strtolower($this->input->post('protocol')),
				'mailtype' 					=> strtolower($this->input->post('mailtype')),
				'smtp_host' 				=> $this->input->post('smtp_host'),
				'smtp_port' 				=> $this->input->post('smtp_port'),
				'smtp_user' 				=> $this->input->post('smtp_user'),
				'smtp_pass' 				=> $this->input->post('smtp_pass'),
				'customer_online_time_out' 	=> $this->input->post('customer_online_time_out'),
				'customer_online_archive_time_out' => $this->input->post('customer_online_archive_time_out'),
				'permalink' 				=> $this->input->post('permalink'),
				'maintenance_mode' 			=> $this->input->post('maintenance_mode'),
				'maintenance_message' 		=> $this->input->post('maintenance_message'),
				'cache_mode' 				=> $this->input->post('cache_mode'),
				'cache_time' 				=> $this->input->post('cache_time')
			);


			if (!empty($update['main_address']) AND is_array($update['main_address'])) {
				$this->load->model('Locations_model');
				$this->Locations_model->updateDefault($update['main_address']);
			}

			if ($this->Settings_model->updateSettings('config', $update)) {
                $this->alert->set('success', sprintf($this->lang->line('alert_success'), 'Settings updated '));
            } else {
                $this->alert->set('warning', sprintf($this->lang->line('alert_error_nothing'), 'updated'));
			}

			return TRUE;
		}
	}

	private function validateForm() {
		$this->form_validation->set_rules('site_name', 'lang:label_site_name', 'xss_clean|trim|required|min_length[2]|max_length[128]');
		$this->form_validation->set_rules('site_email', 'lang:label_site_email', 'xss_clean|trim|required|valid_email');
		$this->form_validation->set_rules('site_logo', 'lang:label_site_logo', 'xss_clean|trim|required');
		$this->form_validation->set_rules('country_id', 'lang:label_site_country', 'xss_clean|trim|required|integer');
		$this->form_validation->set_rules('timezone', 'lang:label_timezone', 'xss_clean|trim|required');
		$this->form_validation->set_rules('currency_id', 'lang:label_site_currency', 'xss_clean|trim|required|integer');
		$this->form_validation->set_rules('language_id', 'lang:label_site_language', 'xss_clean|trim|required|integer');
		$this->form_validation->set_rules('customer_group_id', 'lang:label_customer_group', 'xss_clean|trim|required|integer');
		$this->form_validation->set_rules('page_limit', 'lang:label_page_limit', 'xss_clean|trim|required|integer');
		$this->form_validation->set_rules('meta_description', 'lang:label_meta_description', 'xss_clean|trim');
		$this->form_validation->set_rules('meta_keywords', 'lang:label_meta_keyword', 'xss_clean|trim');
		$this->form_validation->set_rules('menus_page_limit', 'lang:label_menu_page_limit', 'xss_clean|trim|required|integer');
		$this->form_validation->set_rules('show_menu_images', 'lang:label_show_menu_image', 'xss_clean|trim|required|integer');

		if ($this->input->post('show_menu_images') == '1') {
			$this->form_validation->set_rules('menu_images_h', 'lang:label_menu_image_height', 'xss_clean|trim|required|numeric');
			$this->form_validation->set_rules('menu_images_w', 'lang:label_menu_image_width', 'xss_clean|trim|required|numeric');
		}

		$this->form_validation->set_rules('special_category_id', 'lang:label_special_category', 'xss_clean|trim|numeric');
		$this->form_validation->set_rules('registration_terms', 'lang:label_registration_terms', 'xss_clean|trim|required|numeric');
		$this->form_validation->set_rules('checkout_terms', 'lang:label_checkout_terms', 'xss_clean|trim|required|numeric');
		$this->form_validation->set_rules('registration_email', 'lang:label_registration_email', 'xss_clean|trim|required|numeric');
		$this->form_validation->set_rules('customer_order_email', 'lang:label_customer_order_email', 'xss_clean|trim|required|numeric');
		$this->form_validation->set_rules('customer_reserve_email', 'lang:label_customer_reserve_email', 'xss_clean|trim|required|numeric');

		$this->form_validation->set_rules('main_address[address_1]', 'lang:label_address_1', 'xss_clean|trim|required|min_length[2]|max_length[128]|get_lat_lag[main_address]');
		$this->form_validation->set_rules('main_address[address_2]', 'lang:label_address_2', 'xss_clean|trim|max_length[128]');
		$this->form_validation->set_rules('main_address[city]', 'lang:label_city', 'xss_clean|trim|required|min_length[2]|max_length[128]');
		$this->form_validation->set_rules('main_address[postcode]', 'lang:label_postcode', 'xss_clean|trim|required|min_length[2]|max_length[10]');
		$this->form_validation->set_rules('main_address[country_id]', 'lang:label_country', 'xss_clean|trim|required|integer');

		$this->form_validation->set_rules('maps_api_key', 'lang:label_maps_api_key', 'xss_clean|trim');
		$this->form_validation->set_rules('distance_unit', 'lang:label_distance_unit', 'xss_clean|trim|required');
		$this->form_validation->set_rules('future_orders', 'lang:label_future_order', 'xss_clean|trim|required|numeric');
		$this->form_validation->set_rules('location_order', 'lang:label_location_order', 'xss_clean|trim|required|integer');
		$this->form_validation->set_rules('location_order_email', 'lang:label_location_order_email', 'xss_clean|trim|required|integer');
		$this->form_validation->set_rules('location_reserve_email', 'lang:label_location_reserve_email', 'xss_clean|trim|required|integer');
		$this->form_validation->set_rules('approve_reviews', 'lang:label_approve_reviews', 'xss_clean|trim|required|integer');
		$this->form_validation->set_rules('new_order_status', 'lang:label_new_order_status', 'xss_clean|trim|required|integer');
		$this->form_validation->set_rules('complete_order_status', 'lang:label_complete_order_status', 'xss_clean|trim|required|integer');
		$this->form_validation->set_rules('canceled_order_status', 'lang:label_canceled_order_status', 'xss_clean|trim|required|integer');
		$this->form_validation->set_rules('guest_order', 'lang:label_guest_order', 'xss_clean|trim|required|integer');
		$this->form_validation->set_rules('delivery_time', 'lang:label_delivery_time', 'xss_clean|trim|required|integer');
		$this->form_validation->set_rules('collection_time', 'lang:label_collection_time', 'xss_clean|trim|required|integer');
		$this->form_validation->set_rules('reservation_mode', 'lang:label_reservation_mode', 'xss_clean|trim|required|integer');
		$this->form_validation->set_rules('new_reservation_status', 'lang:label_new_reservation_status', 'xss_clean|trim|required|integer');
		$this->form_validation->set_rules('confirmed_reservation_status', 'lang:label_confirmed_reservation_status', 'xss_clean|trim|required|integer');
		$this->form_validation->set_rules('canceled_reservation_status', 'lang:label_canceled_reservation_status', 'xss_clean|trim|required|integer');
		$this->form_validation->set_rules('reservation_time_interval', 'lang:label_reservation_time_interval', 'xss_clean|trim|required|integer');
		$this->form_validation->set_rules('reservation_stay_time', 'lang:label_reservation_stay_time', 'xss_clean|trim|required|integer');
		$this->form_validation->set_rules('image_manager[max_size]', 'lang:label_media_max_size', 'xss_clean|trim|required|numeric');
		$this->form_validation->set_rules('image_manager[thumb_height]', 'lang:label_media_thumb_height', 'xss_clean|trim|required|numeric');
		$this->form_validation->set_rules('image_manager[thumb_width]', 'lang:label_media_thumb_width', 'xss_clean|trim|required|numeric');
		$this->form_validation->set_rules('image_manager[uploads]', 'lang:label_media_uploads', 'xss_clean|trim|integer');
		$this->form_validation->set_rules('image_manager[new_folder]', 'lang:label_media_new_folder', 'xss_clean|trim|integer');
		$this->form_validation->set_rules('image_manager[copy]', 'lang:label_media_copy', 'xss_clean|trim|integer');
		$this->form_validation->set_rules('image_manager[move]', 'lang:label_media_move', 'xss_clean|trim|integer');
		$this->form_validation->set_rules('image_manager[rename]', 'lang:label_media_rename', 'xss_clean|trim|integer');
		$this->form_validation->set_rules('image_manager[delete]', 'lang:label_media_delete', 'xss_clean|trim|integer');
		$this->form_validation->set_rules('image_manager[transliteration]', 'lang:label_media_transliteration', 'xss_clean|trim|integer');
		$this->form_validation->set_rules('image_manager[remember_days]', 'lang:label_media_remember_days', 'xss_clean|trim|integer');
		$this->form_validation->set_rules('protocol', 'lang:label_protocol', 'xss_clean|trim|required');
		$this->form_validation->set_rules('mailtype', 'lang:label_mailtype', 'xss_clean|trim|required');
		$this->form_validation->set_rules('smtp_host', 'lang:label_smtp_host', 'xss_clean|trim');
		$this->form_validation->set_rules('smtp_port', 'lang:label_smtp_port', 'xss_clean|trim');
		$this->form_validation->set_rules('smtp_user', 'lang:label_smtp_user', 'xss_clean|trim');
		$this->form_validation->set_rules('smtp_pass', 'lang:label_smtp_pass', 'xss_clean|trim');
		$this->form_validation->set_rules('customer_online_time_out', 'lang:label_customer_online_time_out', 'xss_clean|trim|required|integer');
		$this->form_validation->set_rules('customer_online_archive_time_out', 'lang:label_customer_online_archive_time_out', 'xss_clean|trim|required|integer');
		$this->form_validation->set_rules('permalink', 'lang:label_permalink', 'xss_clean|trim|required|integer');
		$this->form_validation->set_rules('maintenance_mode', 'lang:label_maintenance_mode', 'xss_clean|trim|required|integer');
		$this->form_validation->set_rules('maintenance_message', 'lang:label_maintenance_message', 'xss_clean|trim');
		$this->form_validation->set_rules('cache_mode', 'lang:label_cache_mode', 'xss_clean|trim|required|integer');
		$this->form_validation->set_rules('cache_time', 'lang:label_cache_time', 'xss_clean|trim|integer');

		if ($this->form_validation->run() == TRUE) {
			return TRUE;
		} else {
			return FALSE;
		}

	}

	private function getTimezones() {
		$timezone_identifiers = DateTimeZone::listIdentifiers();
		$utc_time = new DateTime('now', new DateTimeZone('UTC'));

		$temp_timezones = array();
		foreach ($timezone_identifiers as $timezone_identifier) {
			$current_timezone = new DateTimeZone($timezone_identifier);

			$temp_timezones[] = array(
				'offset' => (int)$current_timezone->getOffset($utc_time),
				'identifier' => $timezone_identifier
			);
		}

		usort($temp_timezones, function($a, $b) {
			return ($a['offset'] == $b['offset']) ? strcmp($a['identifier'], $b['identifier']) : $a['offset'] - $b['offset'];
		});

        $timezone_list = array();
		foreach ($temp_timezones as $tz) {
			$sign = ($tz['offset'] > 0) ? '+' : '-';
			$offset = gmdate('H:i', abs($tz['offset']));
			$timezone_list[$tz['identifier']] = $tz['identifier'] .' (UTC ' . $sign . $offset .')';
		}

		return $timezone_list;
	}

	private function _delete_thumbs($thumb_path) {
		foreach (glob($thumb_path) as $path) {

			if (file_exists($path) AND is_file($path) AND basename($path) === "index.html") {
				continue;
			}

			if (file_exists($path) AND is_file($path)) {
				unlink($path);
				continue;
			}

			foreach (scandir($path) as $item) {
				if ($item != '.' AND $item != '..') {
					if ( ! is_dir($path .'/'. $item)) {
						unlink($path .'/'. $item);
					} else {
						$this->_delete_thumbs($path .'/'. $item);
					}
				}
			}

			if (is_dir($path)) {
				rmdir($path);
				continue;
			}
		}
	}
}

/* End of file settings.php */
/* Location: ./admin/controllers/settings.php */