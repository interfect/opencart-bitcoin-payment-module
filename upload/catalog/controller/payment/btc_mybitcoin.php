<?php
require_once(DIR_SYSTEM . 'payment/sci-client.lib.php');
class ControllerPaymentBTCmybitcoin extends Controller {
	private $payment_module_name  = 'btc_mybitcoin';

	protected function index() {
		global $mbc_username,$mbc_sci_auto_key;

		$this->load->model('checkout/order');
		$order = $this->model_checkout_order->getOrder($this->session->data['order_id']);
		
    	
		$this->data['button_back'] 				= $this->language->get('button_back');
		
		$this->language->load('payment/'.$this->payment_module_name);
		$this->data['text_mbc_exchange_rate'] 	= $this->language->get('text_mbc_exchange_rate');
		$this->data['text_mbc_total_bitcoins'] 	= $this->language->get('text_mbc_total_bitcoins');
		$this->data['text_mbc_order_id'] 		= $this->language->get('text_mbc_order_id');
		$this->data['button_confirm'] 			= $this->language->get('button_mbc_confirm');
		
		$this->data['continue'] = HTTPS_SERVER . 'index.php?route=checkout/success';
		if ($this->request->get['route'] != 'checkout/guest_step_3') {
			$this->data['back'] = HTTPS_SERVER . 'index.php?route=checkout/payment';
		} else {
			$this->data['back'] = HTTPS_SERVER . 'index.php?route=checkout/guest_step_2';
		}
		
		$this->id = 'payment';

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/btc_mybitcoin.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/payment/btc_mybitcoin.tpl';
		} else {
			$this->template = 'default/template/payment/btc_mybitcoin.tpl';
		}	
		
		if ($this->config->get($this->payment_module_name.'_username')) {
			$mbc_username=$this->config->get($this->payment_module_name.'_username');
			$this->data['mbc_username']	=$this->config->get($this->payment_module_name.'_username');
		}
		if ($this->config->get($this->payment_module_name.'_autokey')) {
			$mbc_sci_auto_key		=$this->config->get($this->payment_module_name.'_autokey');
			$this->data['mbc_sci_auto_key']	=$this->config->get($this->payment_module_name.'_autokey');
		}
		if ($this->config->get($this->payment_module_name.'_address')) {
			$this->data['mbc_address']	=$this->config->get($this->payment_module_name.'_address');
		}
		
		$this->data['url_success'] 	= HTTPS_SERVER . 'index.php?route=checkout/success';
		$this->data['url_cancel'] 	= HTTPS_SERVER . 'index.php?route=checkout/payment';
		
		$this->data['store_name']		=$order['store_name'];
		$this->data['order_id']			=$order['order_id'];
		$this->data['order_total']		=$order['total'];
		$this->data['order_currency']	=$order['currency'];
		
		$result=mbc_getrates();
		if ($result['SCI_Reason'] && $result['SCI_Currency_'.$order['currency'].'_Rate']) {
			$this->data['total_btc']		=round($order['total']/$result['SCI_Currency_'.$order['currency'].'_Rate'],2);
			$this->data['btc_rate']			=round($result['SCI_Currency_'.$order['currency'].'_Rate'],3);
			$this->data['main_currency']	=$order['currency'];
		}
		
		$plaintext_querystring=
//		"amount="			.$order['total'].			// Use if you want mybitcoin.com to calculate for you
		"amount="			.$this->data['total_btc'].	// Use above calculation (mybitcoin.com has small rounding errors)
//		"&currency="		.$order['currency'].		// Use if you want mybitcoin.com to calculate for you
		"&currency="		."BTC".						// Use above calculation (mybitcoin.com has small rounding errors)
		"&payee_bcaddr="	.$this->data['mbc_address'].
		"&payee_name="		.$this->data['mbc_username'].
		"&note="			.$this->data['store_name'].''.$this->data['text_mbc_order_id'].''.$this->data['order_id'].
		"&success_url="		.urlencode($this->data['url_success']).
		"&cancel_url="		.urlencode($this->data['url_cancel']).
		"&baggage="			.$this->data['order_id'];
		$result=mbc_encryptformdata($plaintext_querystring);
		$this->data['enc_token']=$result['SCI_Encrypted_Form_Data_Token'];
		
		$this->render();
	}
	
	
	public function confirm() {
		$this->load->model('checkout/order');
		//$this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get($this->payment_module_name.'_order_status_id'));
		//$this->redirect(HTTPS_SERVER . 'index.php?route=checkout/success');
	}
	
	
	public function callback() {
		global $mbc_username,$mbc_sci_auto_key;
		require_once(DIR_SYSTEM . 'payment/sci-client.lib.php');
		$this->load->model('checkout/order');
		
		if ($this->config->get($this->payment_module_name.'_username')) {
			$mbc_username=$this->config->get($this->payment_module_name.'_username');
			$this->data['mbc_username']	=$this->config->get($this->payment_module_name.'_username');
		}
		if ($this->config->get($this->payment_module_name.'_autokey')) {
			$mbc_sci_auto_key		=$this->config->get($this->payment_module_name.'_autokey');
			$this->data['mbc_sci_auto_key']	=$this->config->get($this->payment_module_name.'_autokey');
		}

	  if((isset($_POST['input']))&&(isset($_POST['sci_auto_key']))&&($_POST['input']!="")&&($_POST['sci_auto_key']!="")) {
		if($_POST['sci_auto_key']!=$mbc_sci_auto_key) { //verify the sci key
		  echo "ERROR\n";
		  exit;
		}
		$response=mbc_post_process($_POST['input']); //Returns an array of pgp verified transaction data.
		
		if ($response['SCI_Baggage_Field'] > 0 && is_numeric($response['SCI_Baggage_Field'])) {
			$order = $this->model_checkout_order->getOrder($response['SCI_Baggage_Field']);
			$this->model_checkout_order->confirm($order['order_id'],  $this->config->get($this->payment_module_name.'_order_status_id'));
		}
		else {
			echo "ERROR\n";
			exit;
		}
		
		
		
		//*** Do something here such as connect to your database and save the transaction data, etc.
		if ($response['SCI_Reason'] == 'OK. Payment Received.' && $order['amount']==$response['SCI_Fiat_Amount']) {
			$this->model_checkout_order->confirm($order['order_id'],  $this->config->get($this->payment_module_name.'_order_status_id'));
		}
		
		
		//Our example here just logs the incoming data to a file in your temp directory.
		$output="";
		foreach($response as $key => $value) {
		  $output.=$key." = ".$value."\n";
		}
		$fp=fopen(DIR_LOGS."sci-receipt-handler.log","a");
		if	($fp) {
			if(flock($fp,LOCK_EX)) {
				fwrite($fp,$output."\n\n\n\n");
				flock($fp,LOCK_UN);
			}
			fclose($fp);
		}
		//end of logging example
		
	  } else echo "ERROR\n";
	}
}
?>