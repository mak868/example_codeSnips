<?php
	class PageLoader{
		/**
		   @name Load page
		   @version 1v
		   @date 2020-02-27
		   @author Thomas T
		   @brief Handels the loading of a page
		   
		   @details \n Loads the CV from the requeste page. \n
		   Replaces all the helper tags with the correct content
		   @return <string> $content The content of the requeste page
		  
		  @example HelperTags
			\n 
			This is a instruction on how to make a helperTags in a view page.\n
			The list of posible Tags.\n
				-# include(string) (includes a doc form the main + string)
				-# url(string) (adds the main url to the string)
				-# template(string) (include a doc form the template folder )
				-# asset(string) (include a doc from the asset folder)
			-
			\n
			
			\n\n\n
			The tags wil be replace with the corosponding output.\n
			@b
			@b Example how to tag works \n
			
			@code
			 <html>
				@template(head.html)
			 </html>
			@endcode
			
			@code
				<html>
					<h1> head template </h1>
				</html>
			@endcode
		  
		 */
		public static function load($url){
			self::load_controler($url);
			return self::load_view($url);
		}
		
		
		public static function load_controler($url){
			if(!file_exists(__VIEWS__.$url)) return false;
			files_load(true,__VIEWS__.$url.'/controllers/');			
		}
		
		public static function load_view($url){
			ob_start();
				require(__VIEWS__.$url.'/index.php');
				$content = ob_get_contents();
			ob_get_clean();
						
			self::Replace_helpers($content);
	
			
			return $content;
		}
		
		
		
		
		/**
		 * @name Replace helpers
		 * @brief The function will replace all the "helpers" tags in a loaded string

		 */
		private static function Replace_helpers(& $page){
			self::includes($page);
			self::url($page);
			self::asset($page);
			self::template($page);
			self::lang($page);
		}
		
		/** @name Includes
		 * @brief replaces the @include with in the requested doc
		 */
		protected static function includes(& $string){
			$tag_name = 'include';

			$replace_tag= '#@'.$tag_name.'\(([^)]+)\)#';
			$string = preg_replace_callback( $replace_tag, function($m){
				return file_get_contents(__MAIN__.$m[1]);

			}, $string);

			
		}

		/** @name template
		 * @brief replaces the @template with in the requested doc
		 */
		protected static function template(& $string){
			$tag_name = 'template';

			$replace_tag= '#@'.$tag_name.'\(([^)]+)\)#';
			$string = preg_replace_callback( $replace_tag, function($m){
				if(file_exists(__TEMP__.$m[1])){
					//return file_get_contents(__TEMP__.$m[1]);
					ob_start();
					include "".__TEMP__.$m[1]."";
					return ob_get_clean();					
				}
				return '';

			}, $string);

			
		}
		/** @name url
		 * @brief replaces the @url with in the requested doc
		*/
		protected static function url(&$string){
			$tag_name = 'url';
			
			$replace_tag= '#@'.$tag_name.'\(([^)]+)\)#';
			$string = preg_replace_callback( $replace_tag, function($m){
	
				return __MAIN__.$m[1];

			}, $string);
			
		}
		/** @name asset
		 * @brief replaces the @asset with in the requested doc
		 */
		protected static function asset(&$string){
			$tag_name = 'asset';
			
			$replace_tag= '#@'.$tag_name.'\(([^)]+)\)#';
			$string = preg_replace_callback( $replace_tag, function($m){
				return file_get_contents(__ASSETS__.$m[1]);
			}, $string);			
		}
		/*
		 * @name Language
		 * @brief change the lang tags to the requird language.
		 */
		protected static function lang(&$string){
			$tag_name = 'lang';
			$replace_tag= '#@'.$tag_name.'\(([^)]+)\)#';
			$string = preg_replace_callback( $replace_tag, function($m){
				return Language::get_string($m[1]);
			}, $string);			
		}
			
		
	}