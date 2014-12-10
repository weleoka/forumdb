 /*   $aaa = new \stdClass();
foreach ($one as $item => $value)
{
    $aaa->$item = $value;
}
	*/
	
	
		public function findAll()
	{
	  	$this->db->select()->from($this->getSource());
     	$this->db->execute();
     	return $this->db->fetchInto($this);
	}
	
	gives GMT time
						//	$now = gmdate('Y-m-d H:i:s'); // returns UTC