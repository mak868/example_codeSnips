<?php

class Upload extends Filesystem{
	
	//file
	private static $file;
	private static $fileExtention;
	private static $filesize;
	private static $filename;
	private static $file_fullname;
	private static $extention_valid  =true;
	
	//file options
	private static $auto_name  = false;
	private static $makeFolder = false;
	private static $file_type	= null;
	private static $default_size = 2; //MB
	
	private static $alloweTypes  = ['video'=> array('flv','vob','ogg','ogv','avi','mov','qt','wmv','mp4','m4p','mpg','mp2','mpeg','mpe','mpv','m4v','flv','f4v','f4p','f4a','f4b'),
							 'sound'=> array('mid','wma','aac','wav','ogg','mp3'),
							 'img'=>   array('jpeg','jpg','bmp','png','gif','svg'),
							 'doc'=>   array('doc','docx','docb','dotx','dot','xls','xlt','xlsx','xltx','xlam','ppt','pot','pps','pptx','potx','ppam','sldx','pub','xml','pdf', 'txt'),
							 'zip'=>   array('iso','zip','rar','7z','s7z'),
							 '*'=>'*'];

	
	/* @name save
	 * @brief saves the selected file
	 * @detailt in the first parameter enter the file \n
	 * The second parm is used if the file is in a string format, set this param to true if the file contents is a string.
	 * @param  the file
	 * @param  boolean (standart = false)
	 */	
	public static function save($file, $string = false){
		$extention =  pathinfo((self::$filename)? self::$filename:$file['name']);
		self::$fileExtention = strtolower($extention['extension']); 						// set the extention to all lower case
		self::$filename      = $extention['filename'];
		self::$file_fullname = self::$filename.'.'.self::$fileExtention;
		
		self::MakeFolder();						//create a folder
		if(!self::check_extention())	 return [false, 'extention'];			//not a valid file extention
		$file_name = (self::$auto_name)? self::filename():self::$file_fullname;
		$upload_path = self::storage().'/'.$file_name;
	
		if(!$string)  move_uploaded_file($file['tmp_name'], $upload_path);
		else file_put_contents($upload_path, $file);
		
		if(file_exists($upload_path)){										//check if the file has been moved
		   return  array(TRUE,$file_name,'1',self::$fileExtention);		// return a array with the data in it
		}else{
		   return array(FALSE, 'uploadError','1', $upload_path);			// file error
		}		
	}

	/* @name init
	 * @brief this is uses for function chaining
	 */	
	public static function init(){
	   if (self::$_instance === null) {
			self::$_instance = new self;
	   }
	   return self::$_instance;
	}
	
	public static function file_name($name = null){
		if($name !== null)	self::$filename = $name;
		return self::init();
	}
	
	/* @name: Type
	 * @brief: the type of file that will be uploaded
	 * @param <string> name of file type
	 * @details you can select what type of file it needs to be.\n
	 * all posible file types. \n
	 * video= types('flv','vob','ogg','ogv','avi','mov','qt','wmv','mp4','m4p','mpg','mp2','mpeg','mpe','mpv','m4v','flv','f4v','f4p','f4a','f4b') \n
	 * sound = types('mid','wma','aac','wav','ogg','mp3')\n
	 * img = types('jpeg','jpg','bmp','png','gif','svg')\n
	 * doc= types('doc','docx','docb','dotx','dot','xls','xlt','xlsx','xltx','xlam','ppt','pot','pps','pptx','potx','ppam','sldx','pub','xml','pdf', 'txt')\n
	 * zip= types('iso','zip','rar','7z','s7z')\n
	 * '*'= types(all files)
	 *\n
	 * if you type nothing all the file types will be accepted(exept for the * this needs to be called)
	 */
	public static function type($name){
		@self::$file_type =self::$alloweTypes[$name];		
		return self::init();
	}
	/* @name: auto name
	 * @brief: generate a random name for the file
	 * @param  true/false(boolean)
	 */	
	public static function auto_name($bool = false){
		self::$auto_name = $bool;		
		return self::init();		
	}
	/* @name: create folder
	 * @brief: create the folder for the file if its true
	 * @param  true/false(boolean)
	 */	
	public static function create_folder($bool = false){
		self::$makeFolder = $bool;		
		return self::init();		
	}
	/* @name file size
	 * @brief the max size of the uploaded file
	 * @param  $size the size of the file in MB
	 */		
	public static function file_size($size = null){
		if($size) self::$default_size = $size;	
		return self::init();		
	}
	
	/* @name folder
	 * @brief folder that needs to be selected
	 * @param  string of the folder
	 */		
	public static function folder($folder){
		self::$path [] = $folder;
		return self::init();
	}

	
	/* @name make folder
	 * @brief make a folder if the folder does not exist
	 */
	private static function MakeFolder(){
		if(!self::$makeFolder) return;
		if(!is_dir(self::storage())){
			return mkdir(self::storage(), 0775, true);		
		}
		return true;
	}
	
	
	/* @name max filesize
	 * @brief check the max size of a file
	 */
	private static function maxSize($size){
		$filesize = self::$file["size"]; //filesize in B
		$filesizeMB = $filesize/pow(1024, 2);		   //filesize in MB

		if($size < $filesizeMB && $size != 0){
			self::$errortype[] = 'size';
		}
	}
	
	/* @name check extention
	 * @brief checks if a extention is allowed 	 * 
	 */
	private static function check_extention()
	{
		if(self::$file_type === '*') return true; 
		$search_array = (!self::$file_type)? self::$alloweTypes: self::$file_type; //select the array that needs to be search (or all arrays)

		
		foreach($search_array as  $value){
			if(is_array($value)){
				if(in_array(self::$fileExtention, $value)) return true;
			}
			else{
				if($value == self::$fileExtention) return true;
			}
		}
		
		return false;
	}
	
	/* @name file namer
	 * @brief generates a random unique name for the file
	 */
	private static function filename(){
		$length = 10;
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		$fileName     = '';
		$location  = self::storage();																					//loop till a file name has been found that is not in use
		for($b = 0; $b < 10; $b++){ 
			for ($i = 0; $i < $length; $i++) {
				$randomString .= $characters[rand(0, $charactersLength - 1)];
			}			
			
			$fileName = $randomString.'.'.self::$fileExtention;
			if(!file_exists($location.'/'.$fileName)){
				break;
			}
			
		}
		
		return $fileName;
		
	}	
	
}