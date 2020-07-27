<?php
/********************************************************\
 *@name Form Token
 @brief Generates a token so that a form can be send only once
 @example:
 * Short explanation on how to use:
 *
 * $token = FormToken::generate("form_name");
 *
 * //Put this inside the form
 * <input type="hidden" name="token" value="{$token}">
 *
 * if(FormToken::verify("form_name")) {
 *      //Execute form post function
 * }
 *
\********************************************************/

/**
 * generate token
 */

 class FormToken{
	
	static $token_name =  '_token';
	 
    static function generate($form) {
    
        // generate a token from an unique value
        $token = md5(uniqid(microtime(), true));
    
        // Write the generated token to the session variable to check it against the hidden field when the form is sent
        $_SESSION[$form.self::token_name] = $token;
    
        return $token;
    }
    
    static function verify($form) {
    
        // check if a session is started and a token is transmitted, if not return an error
        if(!isset($_SESSION[$form.self::token_name])) return false;
    
        // check if the form is sent with token in it
        if(!isset($_POST['token']))  return false;
    
        // compare the tokens against each other if they are still the same
        if ($_SESSION[$form.self::token_name] !== $_POST['token']) return false;
        
        return true;
    }  
 }
 

?>