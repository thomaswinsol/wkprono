<?php
class Application_Model_Deelnemer extends My_Model
{
    protected $_name = 'deelnemers'; //table name
    protected $_id   = 'id'; //primary key
    
	public function save($data,$id = NULL)
    {
    	$currentTime =  date("Y-m-d H:i:s", time());
        $dbFields = array(
                'naam'     => $data['naam'],
                'email'    => strtolower($data['email']),
        		'timestamp'=> $currentTime,
        		'afdeling' => 1,
                'status'   => 1,
        	    'betaald'  => 0
        );

        return $this->insert($dbFields);                               
    }    
    
    public function save2($data,$id = NULL)
    {
    	$currentTime =  date("Y-m-d H:i:s", time());
        $dbFields = array(
        		'afdeling' => (int)$data['afdeling'],
                'betaald'  => (int)$data['betaald'],
        );

        return $this->update($dbFields,$id);                               
    }    
    
    public function getScore($id=null)
    {
            $sql = $this->db
            ->select()
            ->from(array('d' => 'deelnemers'), array('id','naam', 'email','status','timestamp','afdeling','betaald') )
            ->join(array('a' => 'afdeling'), ' d.afdeling = a.id  ', array('a.naam as naamafdeling') )
            ->join(array('s' => 'deelnemers_input'), ' d.id = s.id_deelnemer  ', array('sum(score) as score') )            
            ->join(array('w' => 'wedstrijden'), ' s.id_wedstrijd = w.id  ', array() )
            ->group(array('id','naam', 'email','status','timestamp','afdeling', 'betaald', 'naamafdeling') )
            ->order(array('sum(score) desc') );
            $data = $this->db->fetchAll($sql);
            if (!empty($id)) {
            	$sql->where ('w.id <=48'); 
            }
            return $data;
    }
    
	public function getScoreGroep($id=null)
    {
            $sql = $this->db
            ->select()
            ->from(array('d' => 'deelnemers'), array('id','naam', 'email','status','timestamp','afdeling','betaald') )
            ->join(array('a' => 'afdeling'), ' d.afdeling = a.id  ', array('a.naam as naamafdeling') )
            ->join(array('s' => 'deelnemers_input'), ' d.id = s.id_deelnemer  ', array('sum(score) as score') )
            ->join(array('w' => 'wedstrijden'), ' s.id_wedstrijd = w.id  ', array('groep') )
            ->group(array('id','naam', 'email','status','timestamp','afdeling', 'betaald', 'naamafdeling','groep') )
            ->order(array('sum(score) desc') );
             $sql->where ('w.groep >=9'); 
             $sql->where ('w.groep <=12');
             $sql->where ('d.id ='.(int)$id);   
            $data = $this->db->fetchAll($sql);
            return $data;
    }
    
	public function getDeelnemers()
    {
            $sql = $this->db
            ->select()
            ->from(array('d' => 'deelnemers'), array('id','naam', 'email','status','timestamp','afdeling','betaald') )
            ->join(array('a' => 'afdeling'), ' d.afdeling = a.id  ', array('a.naam as naamafdeling') )
            ->join(array('s' => 'deelnemers_input'), ' d.id = s.id_deelnemer  ', array('sum(score) as score') )
            ->group(array('id','naam', 'email','status','timestamp', 'afdeling','betaald', 'naamafdeling') )
            ->order(array('naam asc') );
            $data = $this->db->fetchAll($sql);
            return $data;
    }
    
	public function getWedstrijden($id, $wedstrijdsnr=null)
    {
            $sql = $this->db
            ->select()
            ->from(array('w' => 'wedstrijden'), array('id as wid','thuis','uit','uitslag') )
            ->join(array('i' => 'deelnemers_input'), ' i.id_wedstrijd = w.id  ', array('input1','input2','score') )
            ->where ('i.id_deelnemer='.(int)$id)
            ->where ('w.uitslag<>""')            
            ->order(array('wid') );
            if (!empty($wedstrijdsnr)) {
            	 $sql->where ('w.id <='.(int)$wedstrijdsnr);  
            }
            $data = $this->db->fetchAll($sql);
            return $data;
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
    


}