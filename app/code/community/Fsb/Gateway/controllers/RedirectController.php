<?php


class Fsb_Gateway_RedirectController extends Mage_Core_Controller_Front_Action {

    protected $_order;

    protected function _expireAjax() {
        if (!Mage::getSingleton('checkout/session')->getQuote()->hasItems()) {
            $this->getResponse()->setHeader('HTTP/1.1','403 Session Expired');
            exit;
        }
    }

    public function indexAction() {
	/*
		save cutomer redirect status 
	*/
	    $session = Mage::getSingleton('checkout/session');
        $order = Mage::getModel('sales/order');
        $order->loadByIncrementId($session->getLastRealOrderId());
        $order->addStatusToHistory($order->getStatus(), Mage::helper('gateway')->__('Customer was redirected to Brac Bank Gateway.'));
        $order->save();
	    $this->getResponse()
                ->setHeader('Content-type', 'text/html; charset=utf8')
                ->setBody($this->getLayout()
                ->createBlock('gateway/redirect')
                ->toHtml());
    }

    public function successAction() {
	
		$amount = $this->getRequest()->getParam('amount');
		$transaction_status = trim($this->getRequest()->getParam('Transaction_status'));//echo "<br />";
		$card_no = $this->getRequest()->getParam('Acc_No');
		$ipg_txn_id = $this->getRequest()->getParam('IPG_txn_ID');
		$transaction_id = $this->getRequest()->getParam('transaction_id');//order id
		$reason = $this->getRequest()->getParam('Reason');
		
		// get data
		 $order_id = $transaction_id;//$_SESSION['specialy_order_id_odd'];
	    if(!empty($ipg_txn_id) && !empty($transaction_id)  ) {
		
		/*
			get transaction id for order process
		*/
		if($transaction_id){
		
				$order = Mage::getModel('sales/order');
				$order->loadByIncrementId($transaction_id);
				$date    =  date("l F d, Y, h:i:s");
				/*
					save checkout status in database
				*/
					if($transaction_status=="ACCEPTED"){
						
						$invoice = $order->prepareInvoice();
						Mage::getModel('core/resource_transaction')
							->addObject($invoice)
							->addObject($invoice->getOrder())
							->save();
							
						$invoice->sendEmail(true, '');//sending invoice to customer[25/09/13]
						$order->addStatusToHistory($order->getStatus(), $transaction_id);//saving the transaction id 
						$order->addStatusToHistory($order->getStatus(), $transaction_status);
						$order->addStatusToHistory($order->getStatus(), $reason);
						$order->addStatusToHistory($order->getStatus(), $card_no);
						$order->addStatusToHistory($order->getStatus(), Mage::helper('gateway')->__('Customer successfully returned from Brac Bank'));
						
						$status = true;
						// Send Invoice to Customer after Successfull checkout (As email where not sending)
						//This code will force to send email
						//@$order->sendEmail();
						//@$order->setEmailSent(true);
						
					} else {
				   
					$order->cancel();
					$order->addStatusToHistory($order->getStatus(), $transaction_id);//saving the transaction id 
					$order->addStatusToHistory($order->getStatus(), $transaction_status);
					$order->addStatusToHistory($order->getStatus(), $reason);
					$order->addStatusToHistory($order->getStatus(), $card_no);
					$order->addStatusToHistory($order->getStatus(), Mage::helper('gateway')->__('Customer was rejected by Brac Bank'));
					$status = false;
					// Send Invoice to Customer after Successfull checkout (As email where not sending)
					//This code will force to send email
					//@$order->sendEmail();
					//@$order->setEmailSent(true);
					Mage::getSingleton('core/session')->addError('The payment could not be completed.');
		
				}
		
				$order->save();
			
							
					$session = Mage::getSingleton('checkout/session');
					$session->setQuoteId($transaction_id);
					
					Mage::getSingleton('checkout/session')->getQuote()->setIsActive(false)->save();
					
					
					if($status){
						$this->_redirect('checkout/onepage/success', array('_secure'=>true));
						} else{
							$this->_redirect('checkout/onepage/failure');
							}
					

	
	
	}//fails to get order id

		
		}else {
            //$this->_redirect('*/*/failure');
			$this->_redirect('checkout/onepage/failure');
        }
        
    }
	
	
	
	public function failureAction()
    {

		if (!Mage::getSingleton('core/session')->getError()) {
            $this->norouteAction();
            return;
        }

        $this->getCheckout()->clear();

        $this->loadLayout();
        $this->renderLayout();
    }

}

?>
