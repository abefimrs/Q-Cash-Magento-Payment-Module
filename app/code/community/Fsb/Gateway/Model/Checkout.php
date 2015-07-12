<?php

class Fsb_Gateway_Model_Checkout extends Mage_Payment_Model_Method_Abstract {

    protected $_code          = 'gateway';
    protected $_formBlockType = 'gateway/form';
    protected $_infoBlockType = 'gateway/info';


    public function getCheckout() {
        return Mage::getSingleton('checkout/session');
    }

    public function getOrderPlaceRedirectUrl() {
        return Mage::getUrl('gateway/redirect', array('_secure' => true));
    }

    public function getWebmoneyUrl() {
		$url = 'http://bangladeshbrand.com/bracpay/payment.php';
		// For new separate key
		//$url = 'http://bangladeshbrand.com/bracpay/bracbbrandpayment.php';
        return $url;
    }

    public function getQuote() {

        $orderIncrementId = $this->getCheckout()->getLastRealOrderId();
        $order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);		        
        return $order;
    }

    public function getWebmoneyCheckoutFormFields() {



        $order_id = $this->getCheckout()->getLastRealOrderId();
        $order    = Mage::getModel('sales/order')->loadByIncrementId($order_id);
        $amount   = trim(round($order->getGrandTotal(), 2));
		
		$name = Mage::getSingleton('customer/session')->getCustomer()->getName();
		$customer_id = Mage::getSingleton('customer/session')->getCustomer()->getCustomerId();
		$email =  Mage::getSingleton('customer/session')->getCustomer()->getEmail();
		
		
		
		/*
		*   set order id in session
		*/
		
		
		$_SESSION['specialy_order_id_odd'] = $order_id;
		
		/*
		*	Put the values for desier domain and redirect url
		*	domain name is the domain name from which is going to check out	
		*	redirect url is the url to redirect	
		*/
		
		$payment_method = 'brac';
		$marchankID = 'FSBBRAND';
		
		
		$domain_name = 'http://bangladeshbrands.com/';
		$redirect_url = 'http://bangladeshbrands.com/gateway/redirect/success/';


        $params = array(
	
		'paymnet_method' => $payment_method,
		'merchantID' => $marchankID,
		'name' 			 => $name,
		'email' 		 => $email,
		'customer_id'  	 => $customer_id,
		'domain' 		 => $domain_name,
		'red_url' 		 => $redirect_url,
		'transaction_id' => $order_id,
		'amount' 		 => $amount 
		
			
        );
        return $params;


    }
}