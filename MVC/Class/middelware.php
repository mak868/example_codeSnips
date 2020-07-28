<?php
/* @author Thomas Theil
 * @name WareFunction
 * @brief This are ware function. all the functions are a trait 
 * @warning This is a trait 
 * @date 12-04-2020
 * @version 1.0v
 */
trait WareFunction{
	
	/* @name auth
	 * @brief this function change the "$load_view" state on the return of a auth function.
	 */
	protected function auth($f):bool
	{
		$type =  gettype($f);
		
		if(!$this->getDisplay()) return false;
		switch($type){
			case "boolean":
				$status =  $f;
			break;
			case "object":
				$status = $value->__invoke();
			break;
		}
		

		
		$this->changeDisplay($status);
		return $status;
	}
	/*
	 * @name view
	 * @brief load in the requested view
	 * @param <string> path (the path that needs to be loaded)
	 */
	protected function view($path = null):void
	{
		
		if($path === null) $path = $this->path;
		$page = PageLoader::load($path);
		if($page === false) return false;
		$this->req_page = $page;	
	}
	
	/*
	 * @name arguments
	 * @brief runs your own arguments(function) of the request view or other class
	 * @example $route->get(..)->arg(function(){
	 *		echo 'kaas koekje';
	 * 	})
	 */
	protected function arg(...$arguments):bool
	{
		if($this->chain_state === false) return $this;
		if(!$arguments) return $this;
		try {
			foreach($arguments as $arg){				
				//run the function that are in the class
				if(is_object($arg)) {
					$arg->__invoke();
				}
			}			
		} catch (Exception $e) {
			echo 'Caught exception: ',  $e->getMessage(), "\n";
			$this->view_load = false;
			return false;
		}
		return true;
	}	
	
}