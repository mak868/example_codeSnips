<?php
namespace Controler\Login

class Login{

	private $model;
	private $hash;
	/*
	* @name Login controler
	* @brief the login constructor
	* @param <object> $model (model for the contoler)
	* @param <object> $hash  (hasing algeritme)
	*/
	
	function __construct($model = null, $hash = null){	
		if(!$hash) die("ERROR 50: LOGIN");
		if(!$model) die("ERROR 51: LOGIN");

		$this->model = $model;
		$this->hash  = $hash;
	}
	
	/*
	* @name Login
	* @param <string> $login
	* @param <string> $pass
	* @return <boolean> 
	*/
	
	public function login($login, $pass){
		
		$login = htmlentities($login);
		$user =  $this->model->login($login);
		
		if(!$user) return false;
				
		if(!$this->hash->valid($pass, $result['pass'])) return false;
		
		return true;
		
	}
	

	
}
