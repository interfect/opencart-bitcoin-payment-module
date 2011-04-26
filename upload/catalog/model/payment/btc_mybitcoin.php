<?php 
class ModelPaymentBTCmybitcoin extends Model {
  	public function getMethod($address) {
		$this->load->language('payment/btc_mybitcoin');
		
		if ($this->config->get('btc_mybitcoin_status')) {
        	$status = TRUE;
		} else {
			$status = FALSE;
		}
		
		$method_data = array();
	
		if ($status) {  
      		$method_data = array( 
        		'id'         	=> 'btc_mybitcoin',
        		'title'      	=> $this->language->get('text_title'),
				'sort_order' 	=> $this->config->get('btc_mybitcoin_sort_order'),
      		);
    	}
   
    	return $method_data;
  	}
}
?>