<?php
class Application_Model_Ploeg extends My_Model
{
    protected $_name = 'ploegen'; //table name
    protected $_id   = 'id'; //primary key

	public function getAll($where=null,$order=null)
    {       
    	$tr= Zend_Registry::get('Zend_Translate');
    	$result = parent::getAll($where);
    	if (empty($where)) {
    		$ploegen=array();    	 
    		foreach ($result as $r) {
    			$ploegen[$r['groep']][""]="";
    			$ploegen[$r['groep']][$r['id']]=$tr->translate($r['naam']);
    		}
    		return $ploegen;
    	}
    	else {
    		return $result;
    	}
        
    }
    
	public function getPloegen()
    {       
    	$result = parent::getAll();
    	if (empty($where)) {
    		$ploegen=array();    	 
    		foreach ($result as $r) {
    			if ($r['status']) {
    				$ploegen[$r['id']]="*".$r['naam'];
    			} else {
    				$ploegen[$r['id']]=" ".$r['naam'];
    			}
    		}
    		return $ploegen;
    	}        
    }
    /**
     * Insert
     * @return int last insert ID
     */
    public function insert($data)
    {
        return parent::insert($data);       
    }

    /**
     * Update
     * @return int numbers of rows updated
     */
    public function update($data,$id)
    {
        return parent::update($data, 'id = '. (int)$id);
    }
    
    
    public function save2($data,$id = NULL)
    {
        $dbFields = array(
                'status'  => (int)$data['status'],
        );

        return $this->update($dbFields,$id);                               
    }    


}