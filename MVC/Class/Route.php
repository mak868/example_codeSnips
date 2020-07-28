<?php

/* @author Thomas Theil
 * @name Route
 * @brief The routing class for loading Routs
 * @detail
   This class controls the loading of routs of a webpage.
   A rout is a page that can be request by the user.
   Loading a page must be done by call get(...).
   See the get funtion for more information.
 * @note A slash and empty space will be seen as the same.
 * @note Middelware posible class see the class.middelware.php	 	
 * @example 
   $app  =  new Route();
   
   //load in the login page of a website
	$app->get('/',
          ["view"=>'login',
			   function(){
				Login::post();
                request_rest::post();
                rest_pass::post();
			   }
			   ],
            [function(){
                Login::display();
            }]);
    //loading of a normal page
	$app->get('home',["view"=>'index',
	"auth"=>enviroment_auth::allowed_page()]);
 *   
 *   
 * @date 12-04-2020
 * @version 1.29v
 */ 
class Route{
	use WareFunction; 
	protected static $path;
	protected static $endware   = null;
	protected static $valid_path= false;
	protected static $display   = true;
	

	
	private static $req_page;

	
	public function __construct(){
		$url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		$req = parse_url($url, PHP_URL_PATH);
		$path = substr($req, 1);
		$this->path =  $path;
	}
	
	/* 
	 * @name destructor
	 * @brief calls the display class
	 */
	public function __destruct(){
		$this->display();
	}
	
    /*
	* @name get	
	* @brief main class that can be called for loading a rout
    * @detail this class only runs if the right page is requested.
      if the the correct rout is found.  the middel ware will be run.
      the middel ware can turn of the displaying of a page	  
	* @param <string> route  (the route of the page)
	* @param <array> middelware (the middelware will always load before thisplaying the page to a user)
	*/	
	public function get($route, $middelware = null):void
	{
		if(!$this->request($route)) return false;
		$this->endware = $endware;
		if($middelware === null) return true;
		
		try {
			$this->runWare($middelware);								

		} catch (Exception $e) {
			echo 'Caught exception: ',  $e->getMessage(), "\n";
		}
	}
	
	/*
	* @name runWare
	* @brief loops through the ware array.
	* @detail wareFunction each of the ware keys in the ware arrays are function. 
	  So if you want to add other options to a ware thane you need to add a new function.
	  You can also add your own objects to the function. 
	*/
	protected function runWare($ware = null):void
	{
		if($ware === null) return true;
		foreach($ware as $key => $value){
				
				if(method_exists($this, $key)){
					$this->{$key}($value);
				}else{
					if(is_object($value)) {
						$status = $value->__invoke();
					}
				}
		}
	}
	
	/*
	 * @name request
	 * @brief Check if a root is requested
	 * @param <string> the request route.
	 * @return <boolean> state of the request
	 */
	protected function request($route): bool
	{
		$req_path = ($this->path == '/')? '': self::$path;
		$route    = ($route == '/')? '': $route;
		
		//check if the root is the same as url
		if($req_path == $route) self::$valid_path = true;
		return ($req_path == $route);
	}
	

	
	/*
	* @name display
	* @brief displays the request view.
	*  
	*/
	protected function display($path = null): void
	{
		if($this->display !== true || $this->valid_path !== true){
			header("HTTP/1.0 404 Not Found");
			exit();
		}
		
		echo $this->req_page;
	}
	/*
	* @name changeDisplay
	* @param <boolean> state
	* @brief change the display state of a page
	*/
	
	protected function changeDisplay($state = true):void
	{
		$this->display = $state;
	}
/*
*	@name getDisplay
*	@brief get the curent display state
*	@return <boolean> status
*/	
	protected function getDisplay():bool
	{
		return $this->display;
	}
	
}
