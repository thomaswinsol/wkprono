<?php
class IndexController extends My_Controller_Action
{

    public function indexAction()
    {
    	define('DB_HOST', getenv('OPENSHIFT_MYSQL_DB_HOST'));
		define('DB_PORT', getenv('OPENSHIFT_MYSQL_DB_PORT'));
		define('DB_USER', getenv('OPENSHIFT_MYSQL_DB_USERNAME'));
		define('DB_PASS', getenv('OPENSHIFT_MYSQL_DB_PASSWORD'));

		/*echo DB_HOST;
		ECHO "xxx";
		echo DB_PORT;
		ECHO "xxx";
		echo DB_USER;
		ECHO "xxx";
		echo DB_PASS;*/
		
        $this->_helper->redirector('prono', 'index');
        
    }

    public function homeAction()
    {
        $this->flashMessenger->setNamespace('Errors');
        $this->view->flashMessenger = $this->flashMessenger->getMessages();
    }

    public function pronoAction()
    {
    	if ($this->getRequest()->isPost()){
            $postParams= $this->getRequest()->getPost();

            $deelnemerModel = new Application_Model_Deelnemer();
            $fields=array("naam"=>$postParams['naam'],"email"=>$postParams['email']);
            $id= $deelnemerModel->save($fields);
            
            $deelnemerinputModel = new Application_Model_Deelnemerinput();
            for ($ii=1; $ii<=36; $ii++) {
            	$index1=$ii."_1";
            	$index2=$ii."_2";
            	if (isset($postParams[$index1])) {
            		$fields=array("id_deelnemer"=>$id, "id_wedstrijd"=>$ii, "doelpunten_thuis"=>$postParams[$index1],"doelpunten_uit"=>$postParams[$index2]);
            		$deelnemerinputModel->save($fields);
            	}
            }
    	}
    	 
    	$ploegenModel = new Application_Model_Ploeg();
        $this->view->ploegen= $ploegenModel->getAll();
            
        $this->flashMessenger->setNamespace('Errors');
        $this->view->flashMessenger = $this->flashMessenger->getMessages();
        
        $wedstrijdModel = new Application_Model_Wedstrijd();
        $this->view->wedstrijden = $wedstrijdModel->getAll();
    }

    
	public function ajaxSaveFormAction() {
            $this->_helper->layout->disableLayout();
            //$this->_helper->viewRenderer->setNoRender();
            $formData  = $this->_request->getPost();
            parse_str($formData['data'], $data);

            $error=0;
            $messages=null;
            $form = new Application_Form_Prono;
            if (!$form->isValid($data)) {
                $error=1;
                $messages=$this->printMessages($form->getMessages());
            }
            else {
            	$postParams= $data;
            	$deelnemerModel = new Application_Model_Deelnemer();
            	$fields=array("naam"=>$postParams['naam'],"email"=>$postParams['email']);
            	$id= $deelnemerModel->save($fields);
            
           		$deelnemerinputModel = new Application_Model_Deelnemerinput();
            	for ($ii=1; $ii<=51; $ii++) {
            		$index1=$ii."_1";
            		$index2=$ii."_2";
            		if (isset($postParams[$index1])) {
            			$fields=array("id_deelnemer"=>$id, "id_wedstrijd"=>$ii, "input1"=>$postParams[$index1],"input2"=>$postParams[$index2]);
            			$deelnemerinputModel->save($fields);
            		}
            	}
            	for ($ii=52; $ii<=53; $ii++) {
            		if (isset($postParams[$ii])) {
            			$fields=array("id_deelnemer"=>$id, "id_wedstrijd"=>$ii, "input1"=>$postParams[$ii],"input2"=>0);
            			$deelnemerinputModel->save($fields);
            		}
            	}
            	$this->sendmail($id);
    		}
            $this->view->error=$error;
            $this->view->messages=$messages;
    }
    
    
	private function sendmail($id) {
    			$mail = new My_Controller_Plugin_Mail();	
                $templateName = My_Controller_Plugin_Mail::TEMPLATE_WKPRONO;

                $data=array();
                //$id = $this->_getParam('id');    	
				$deelnemerModel = new Application_Model_Deelnemer();
        		$data[0] = $deelnemerModel->getOne($id);
        
        		$deelnemerinputModel = new Application_Model_Deelnemerinput();
        		$where="id_deelnemer in (".(int)$id.")";
        		$data[1] = $deelnemerinputModel->getAll($where);
        
        		$ploegenModel = new Application_Model_Ploeg();
        		$data[2] = $ploegenModel->getPloegen();           

        		$wedstrijdModel = new Application_Model_Wedstrijd();
        		$data[3] = $wedstrijdModel->getAll();       
    	 	
                $mail->send($templateName,$data);  
    }
    
	private function printMessages($messages) {
    	$msg=" ";
    	if (!empty($messages)) {
    		$msg .="<div class='msg_error'>";
    		foreach ($messages as $key => $value) {
    			$msg.= "<p>";
    			$msg.= $key." : ";
    			foreach ($value as $v) {
    				$msg.= $v."<br/>";
    			}
    			$msg.= "</p>";
    		}
    		$msg .="</div>";
    	}
    	return $msg;
    }
    
    public function spelregelsAction()
    {

    }
    
	public function klassementAction()
    {
    	$this->view->msgid = $this->_getParam('msg');  
    	//$this->view->view = $this->_getParam('view');
    	$id = $this->_getParam('id');  
    	$this->view->view = 1;
		$deelnemerModel = new Application_Model_Deelnemer();
        $this->view->result= $deelnemerModel->getScore($id);
    }
    
    
    public function deelnemersAction()
    {
    	$this->view->msgid = $this->_getParam('msg');  
    	$this->view->view = $this->_getParam('view');
    	$id = $this->_getParam('id');  
    	//$this->view->view = 1;
	$deelnemerModel = new Application_Model_Deelnemer();
        $this->view->result= $deelnemerModel->getScore($id);
    }
    
	public function detailAction()
    {
    	$id = $this->_getParam('id');    	
	$deelnemerModel = new Application_Model_Deelnemer();
        $this->view->deelnemer = $deelnemerModel->getOne($id);
        
        $this->view->result2 = $deelnemerModel->getScoreGroep($id);        
        $this->view->finales=$this->getfinales();
        
        $this->view->result = $deelnemerModel->getWedstrijden($id,48);
        
        $deelnemerinputModel = new Application_Model_Deelnemerinput();
        $where="id_deelnemer in (".(int)$id.")";
        $this->view->deelnemerinput = $deelnemerinputModel->getAll($where);
        
        $ploegenModel = new Application_Model_Ploeg();
        $this->view->ploegen= $ploegenModel->getPloegen();           

        $wedstrijdModel = new Application_Model_Wedstrijd();
        $this->view->wedstrijden = $wedstrijdModel->getAll();
    }
    
	private function getfinales() {
    		$finales=array("9"=>"Achtste finale","10"=>"Kwartfinale","11"=>"Halve finale","12"=>"Finale");
    		return $finales;
    }
    
	public function ajaxFillKwartfinaleAction() {
        $this->_helper->layout->disableLayout();		 
        $formData  = $this->_request->getPost();
        parse_str($formData['data'], $data);     
        print_r($formData);
        die("ok");   
        $modelPloeg=  new Application_Model_Ploeg();
        $where ="id in (".$formData['a1'].",".$formData['a2'].")";
        $this->view->ploegen= $modelPloeg->getAll($where);
    }

	public function ajaxFillNextRoundAction() {
        $this->_helper->layout->disableLayout();		 
        $formData  = $this->_request->getPost();
        
        $modelWedstrijd = new Application_Model_Wedstrijd();
        $wedstrijd=$modelWedstrijd->getOne($formData['w']);
       
        $this->view->wedstrijd=$wedstrijd['volgende_wedstrijd'];
        $modelPloeg=  new Application_Model_Ploeg();
        $where ="id in (".(int)$formData['f1'].",".(int)$formData['f2'].")";
        $this->view->ploegen= $modelPloeg->getAll($where);
    }

	public function wedstrijdenAction()
    {
    	$id = $this->_getParam('id'); 
    	$this->view->doelpunten= $this->_getParam('doelpunten'); 
    	
    	$this->view->id=$id;
    	$ploegenModel = new Application_Model_Ploeg();
        $this->view->ploegen= $ploegenModel->getAll();           

        $wedstrijdModel = new Application_Model_Wedstrijd();
        $this->view->wedstrijden = $wedstrijdModel->getAll();
        $this->view->AantalDoelpunten = $wedstrijdModel->getAantalDoelpunten();
        if (!empty($id)) {
        	$wedstrijd=$wedstrijdModel->getOne($id);
        	$this->view->wedstrijd=$wedstrijd;
        	
        	if ($wedstrijd['groep']>8) {
        		$this->view->result2=$wedstrijdModel->getScoreForGroep($wedstrijd['groep']);
        	}
        	else {
        		$this->view->result=$wedstrijdModel->getScoreForWedstrijd($id);        	
        	}
        }
    }

}





