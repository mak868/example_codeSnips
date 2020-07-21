<?php
namespace Controler\auth

class password_auth {
	
		/*
		* @name verify
		* @brief verifys the password
		* @param <string> $password
		* @param <string> $pwd_databs
		* @return <boolean> 
		*/
	
		public function verify($password, $pwd_databs){
			if(!$password) return false;	
			if(!$pwd_databs) return false;
			
			
			$password = $this->pepper($password);
			return password_verify($password, $pwd_databs);
		}
		
		/*
		* @name verify
		* @brief hashs the password for saving in the db
		* @param <string> $password
		* @return <string> $password
		*/
		
		public function hash($password){
			if(!$password) return false;
			
			$password = $this->pepper($password);
			return password_hash($password, PASSWORD_DEFAULT);
		}	

		private function pepper($password){
			$pepper = getConfigVariable("pepper");
			$passwordPeppered = hash_hmac(***, $password, $pepper);
			return $passwordPeppered;
		}
		
		
	}