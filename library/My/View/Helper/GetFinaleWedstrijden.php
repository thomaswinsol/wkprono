<?php
/**
 * Product helper
 *
 * @uses viewHelper Zend_View_Helper
 */
class Zend_View_Helper_GetFinaleWedstrijden extends Zend_View_Helper_Abstract
{
		
        public function GetFinaleWedstrijden($id, $ploegen, $wedstrijden){
           $finale=array("8"=>"AchtsteFinale","4"=>"Kwartfinale","2"=>"Halvefinale","1"=>"Finale");
           $htm=null;        	
			 $htm  = "<div class='hoofding'>". $this->view->translate(trim($finale[$id])) ."</div>";
			 $htm .= "<div class='groep'>";
			 $htm .= "<table width='100%' cellspacing='1' cellpadding='1' border='0'>";
			 $htm .= "<tbody>";

		     if (!empty($wedstrijden)) {
		     	foreach ($wedstrijden as $w) {
		     		$field1=$w['id']."_1";
			 		$field2=$w['id']."_2";			 		
			 		$htm .= "<tr>";
			 		$htm .= "<td width='8%'>".trim($w['id'])."</td>";	
			 		$htm .= "<td width='18%'>".trim($w['Datum'])." ".trim($w['Uur'])."</td>";		 	
			 		$htm .= "<td width='15%' align='right'>";
			 		$options=null;			 		
			 		if (!empty($ploegen)) {	
						$g=$this->getGroep($w['thuis']);
						$options=$ploegen[$g];
			 		}
			 		$htm .=  $this->view->formSelect($field1,null,array('class'=>$finale[$id]),$options );
			 		$htm .= " ".$w['thuis'];
			 		$htm .= "</td>";
			 		$htm .= "<td width='4%' align='center'>-</td>";			 		
			 		$htm .= "<td width='1%' align='right'>";
			 		$htm .= $w['uit']." ";	
			 		$htm .= "</td>";	
			 		$htm .= "<td width='41%' align='left'>";	 		
		     		$options=null;			 		
			 		if (!empty($ploegen)) {	
						
						if( strpos($w['uit'], '/') !== false ) {
							$pieces = explode("/", $w['uit']);
							$ii=0;
							foreach ($pieces as $p) {
								if ($ii==0) {
									$g=$this->getGroep($p);
									//$options=$ploegen[$g];
									$gg=substr($p,1,1);
								}
								else {
									$g=$this->getGroep($p,1);
									$gg=$p;									
								}
								
									foreach ($ploegen[$g] as $k => $v) {
										if (trim($v)<>"") {
											$options[$k]=trim($gg).". ". $v;	
										}
										else {
											$options[$k]=$v;
										}
										
									}
								$ii++;
							}
			 			}
			 			else {
			 				$g=$this->getGroep($w['uit']);
							$options=$ploegen[$g];
			 			}
			 		}
			 		$htm .=  $this->view->formSelect($field2,null,array('class'=>$finale[$id]),$options);
			 		$htm .= "</td>";
			 		$htm .= "</tr>";
			 		
		     	}
		     }	
			 $htm .= "</tbody>";
			 $htm .= "</table>";
			 $htm .= "</div>";

           return $htm;
        }
        
        private function getGroep($value, $flag=0) { 	
        	 $groepen=array("","A","B","C","D","E","F");         
        	 if ($flag) {
        	 	$groep=$value;
        	 }  
        	 else { 
        	 	$groep=substr($value, 1 ,1);
        	 } 
        	 $result= array_keys($groepen,$groep);
        	 return $result[0];
        }

}

