<?php
/**
* Setup view variables
*/
class My_Controller_Plugin_Mail extends Zend_Controller_Plugin_Abstract
{
	
     const TEMPLATE_WKPRONO = 'WKPRONO';
    
     public $view;
    
     public function __construct()
     {
     	$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
     	$viewRenderer->init();
     	$this->view = $viewRenderer->view;
     }     
     
     
     private function hasMailAccess(){
     	if (APPLICATION_ENV == 'production'){
     		return TRUE;
     	}
     	return false;
     }
     
     
     public function send($templateName,$data = null){ 
     	/*if (!$this->hasMailAccess()){
     		return FALSE; 
     	}*/
     	
     	$templateMethod = 'template_'.$templateName;
     	if (!method_exists($this,$templateMethod)){
     		throw new Exception('Mail template not found');
     	}
     	 
     	return $this->$templateMethod($data);
     	// mail('thomas.vanhuysse@telenet.be','testing','het werkt');
     	// echo 'mail model é: ';    
     }
     
     
     // ---------
     // TEMPLATES
     // ---------     
     protected function template_WKPRONO($data){
                /*ini_set("SMTP", "smtp.gmail.com");
				ini_set("sendmail_from", "thomasvanhuysse0@gmail.com");
				ini_set("smtp_port", "587");*/
	
     		//$mail = new Zend_Mail('ISO-8859-1');
     		$mail = new Zend_Mail('UTF-8');
     		$mail->setFrom('thomasvanhuysse0@gmail.com', 'EKPronostiek');

     		$mail->addTo($data[0]['email'], $data[0]['email'] );
     		$mail->addBcc('thomas.vanhuysse@telenet.be', 'thomas.vanhuysse@telenet.be' );
     		$mail->setHeaderEncoding(Zend_Mime::ENCODING_BASE64);
     		$this->view->data = $data;     	
     		$html = $this->view->render('/mail/wkprono.phtml');
     		//$mail->setBodyHtml($html,'ISO-8859-1',Zend_Mime::ENCODING_BASE64);
     		$mail->setBodyHtml($html,'UTF-8',Zend_Mime::ENCODING_BASE64);
     		$mail->setSubject("Uw EK Pronostiek :".$data[0]['naam']);
     		$mail->send(); 
     		
     		return TRUE;
     }     
     
	 
} 
