<?php 
class ControllerPaymentBTCmybitcoin extends Controller {
	private $error = array();
	private $payment_module_name  = 'btc_mybitcoin';
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'payment/'.$this->payment_module_name)) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
				
		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}	
	}
	
	public function index() {
		$this->load->language('payment/'.$this->payment_module_name);
		$this->load->model('setting/setting');
		
		
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
			$this->model_setting_setting->editSetting($this->payment_module_name, $this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			$this->redirect(HTTPS_SERVER . 'index.php?route=extension/payment&token=' . $this->session->data['token']);
		}
 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		
		$this->document->title = $this->language->get('heading_title');
		
		$this->data['heading_title'] 		= $this->language->get('heading_title');

		$this->data['text_enabled'] 		= $this->language->get('text_enabled');
		$this->data['text_disabled'] 		= $this->language->get('text_disabled');
		$this->data['text_all_zones'] 		= $this->language->get('text_all_zones');
				
		$this->data['entry_order_status'] 	= $this->language->get('entry_order_status');		
		$this->data['entry_geo_zone'] 		= $this->language->get('entry_geo_zone');
		$this->data['entry_status'] 		= $this->language->get('entry_status');
		$this->data['entry_sort_order'] 	= $this->language->get('entry_sort_order');
		$this->data['entry_username'] 		= $this->language->get('entry_username');
		$this->data['entry_autokey'] 		= $this->language->get('entry_autokey');
		$this->data['entry_address'] 		= $this->language->get('entry_btc_address');
		
		$this->data['button_save'] 			= $this->language->get('button_save');
		$this->data['button_cancel'] 		= $this->language->get('button_cancel');

		$this->data['tab_general'] 			= $this->language->get('tab_general');

		
 		if (isset($this->error['username'])) {
			$this->data['error_username'] = $this->error['username'];
		} else {
			$this->data['error_username'] = '';
		}
 		if (isset($this->error['autokey'])) {
			$this->data['error_autokey'] = $this->error['autokey'];
		} else {
			$this->data['error_autokey'] = '';
		}
 		if (isset($this->error['address'])) {
			$this->data['error_address'] = $this->error['address'];
		} else {
			$this->data['error_address'] = '';
		}
		
  		$this->document->breadcrumbs = array();

   		$this->document->breadcrumbs[] = array(
       		'href'      => HTTPS_SERVER . 'index.php?route=common/home&token=' . $this->session->data['token'],
       		'text'      => $this->language->get('text_home'),
      		'separator' => FALSE
   		);

   		$this->document->breadcrumbs[] = array(
       		'href'      => HTTPS_SERVER . 'index.php?route=extension/payment&token=' . $this->session->data['token'],
       		'text'      => $this->language->get('text_payment'),
      		'separator' => ' :: '
   		);
		
   		$this->document->breadcrumbs[] = array(
       		'href'      => HTTPS_SERVER . 'index.php?route=payment/'.$this->payment_module_name.'&token=' . $this->session->data['token'],
       		'text'      => $this->language->get('heading_title'),
      		'separator' => ' :: '
   		);
		
		$this->data['action'] = HTTPS_SERVER . 'index.php?route=payment/'.$this->payment_module_name.'&token=' . $this->session->data['token'];

		$this->data['cancel'] = HTTPS_SERVER . 'index.php?route=extension/payment&token=' . $this->session->data['token'];	
		
		if (isset($this->request->post[$this->payment_module_name.'_order_status_id'])) {
			$this->data[$this->payment_module_name.'_order_status_id'] = $this->request->post[$this->payment_module_name.'_order_status_id'];
		} else {
			$this->data[$this->payment_module_name.'_order_status_id'] = $this->config->get($this->payment_module_name.'_order_status_id'); 
		} 

		$this->load->model('localisation/order_status');
		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		
			if (isset($this->request->post[$this->payment_module_name.'_status'])) {
			$this->data[$this->payment_module_name.'_status'] = $this->request->post[$this->payment_module_name.'_status'];
		} else {
			$this->data[$this->payment_module_name.'_status'] = $this->config->get($this->payment_module_name.'_status');
		}
		
		if (isset($this->request->post[$this->payment_module_name.'_sort_order'])) {
			$this->data[$this->payment_module_name.'_sort_order'] = $this->request->post[$this->payment_module_name.'_sort_order'];
		} else {
			$this->data[$this->payment_module_name.'_sort_order'] = $this->config->get($this->payment_module_name.'_sort_order');
		}
		if (isset($this->request->post[$this->payment_module_name.'_username'])) {
			$this->data[$this->payment_module_name.'_username'] = $this->request->post[$this->payment_module_name.'_username'];
		} else {
			$this->data[$this->payment_module_name.'_username'] = $this->config->get($this->payment_module_name.'_username');
		} 
		if (isset($this->request->post[$this->payment_module_name.'_autokey'])) {
			$this->data[$this->payment_module_name.'_autokey'] = $this->request->post[$this->payment_module_name.'_autokey'];
		} else {
			$this->data[$this->payment_module_name.'_autokey'] = $this->config->get($this->payment_module_name.'_autokey');
		} 
		if (isset($this->request->post[$this->payment_module_name.'_address'])) {
			$this->data[$this->payment_module_name.'_address'] = $this->request->post[$this->payment_module_name.'_address'];
		} else {
			$this->data[$this->payment_module_name.'_address'] = $this->config->get($this->payment_module_name.'_address');
		} 
		
		$this->template = 'payment/'.$this->payment_module_name.'.tpl';
		$this->children = array(
			'common/header',	
			'common/footer'	
		);
		
		$this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
		
	}
	
}