<?php

/* 
/*
 	public function __construct($db) {
 		$this->db=$db;
 	}
		} if (isset($feedback)) {
				echo "<h5>" . $feedback . "</h5>";
			//	unset($feedback);
		}  if (isset($_SESSION['user-feedback'])) {
        		$output = $_SESSION['user-feedback'];
				echo "<h5>" . $output . "</h5>";        		
        		unset($_SESSION['user-feedback']);
/*
 * login
 *
 */
	public function login($user,$password) {
		$sql = "SELECT acronym, name FROM User WHERE acronym = ? AND password = md5(concat(?, salt))";
		$params = array();
		$params=[htmlentities($user),  htmlentities($password)];
		$res = $this->db->ExecuteSelectQueryAndFetchAll($sql, $params);

		if(isset($res[0])) {
			$_SESSION['user'] = $res[0];
			return true;
		} else {
			return false;
		}
	}


	public function findAll()
	{
	  	$this->db->select()->from($this->getSource());
     	$this->db->execute();
     	return $this->db->fetchInfo($this);	
	}
	
	

    
    ---------- check password ----------------
    if(password_verify($password, $res->password)){
                    return $res;
                }else{
                    return false;
                }
*/ 
*/
*/