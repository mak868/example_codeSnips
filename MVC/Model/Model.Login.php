<?php
namespace Model\Login

class Login{
	
	private $connect;
	/*
	* @name Login model
	* @brief the login Model
	* @param <object> $connect (the database class)
	*/
	function __construct($connection = null){	
		if(!$connection) die("ERROR 52: LOGIN");

		$this->connect =  $connection;
	}
	
	/*
	* @name Login
	* @brief get encrypted pass of the user
	* @param <string> $login (login name)
	* @return <string> $password (the password of the user)
	*
	*/
	
	public function login($login){
		if(!$login) return false;
		
		$sql = "SELECT `pass`, `id` FROM `users`
		WHERE `login`= :LOGIN ;";
		
		$result = $this->connect->fetch($sql,['LOGIN'=>$login]);
		if(!$result['pass']) return false;
		
		return $result;	
	}
		
}
