<?php
/**
 * Exnteds FuelPHP core Uri class
 * 
 * @link http://www.marcopace.it/blog/2012/12/fuelphp-i18n-internationalization-of-a-web-application tutorial i18n.
 */


class Uri extends \Fuel\Core\Uri 
{
	
	
	public function __construct($uri = null)
	{
		parent::__construct($uri);
		
		$this->detectLanguage();
	}// __construct
	
	
	/**
	 * create URI
	 * this method replaced old one to make it multilingual url ready (/en, /fr, /th, /{lang})
	 * 
	 * @param string $uri
	 * @param array $variables
	 * @param array $get_variables
	 * @param boolean $secure
	 * @return string
	 */
	public static function create($uri = null, $variables = array(), $get_variables = array(), $secure = null)
	{
		return self::createi18n($uri, $variables, $get_variables, $secure);
	}// create
	
	
	/**
	 * create URI with lang prefix in front of that url. (/en, /fr, /th, /{lang})
	 * 
	 * @link http://www.marcopace.it/blog/2012/12/fuelphp-i18n-internationalization-of-a-web-application tutorial i18n.
	 * @param string $uri
	 * @param array $variables
	 * @param array $get_variables
	 * @param boolean $secure
	 * @return string
	 */
	public static function createi18n($uri = null, $variables = array(), $get_variables = array(), $secure = null) 
	{
		$language = Config::get('language');
 
		if (!empty($uri)) {
			$language .= '/';
		}

		return \Fuel\Core\Uri::create($language.$uri, $variables, $get_variables, $secure);
	}// createi18n
	
	
	/**
	 * create URI without lang uri prefix
	 * in other word. this will call original uri create method.
	 * 
	 * @param string $uri
	 * @param array $variables
	 * @param array $get_variables
	 * @param boolean $secure
	 * @return string
	 */
	public static function createNL($uri = null, $variables = array(), $get_variables = array(), $secure = null) 
	{
		return \Fuel\Core\Uri::create($uri, $variables, $get_variables, $secure);
	}// createNL


	/**
	 * detect language from uri
	 * 
	 * @link http://www.marcopace.it/blog/2012/12/fuelphp-i18n-internationalization-of-a-web-application tutorial i18n.
	 * @return boolean|null
	 */
	public function detectLanguage() 
	{
		// redirect url with no /lang uri to /lang uri. example http://localhost -> http://localhost/en
		$this->redirectLanguageUri();
		
		if (!count($this->segments)) {
			return false;
		}
 
		$first = $this->segments[0];
		$locales = Config::get('locales');

		if(array_key_exists($first, $locales)) {
			array_shift($this->segments);
			$this->uri = implode('/', $this->segments);

			Config::set('language', $first);
			Config::set('locale', $locales[$first]['locale']);
		}
	}// detectLanguage
	
	
	/**
	 * generate current querystrings 
	 * 
	 * @param boolean $question_param
	 * @param boolean $valid_html
	 * @param boolean $valid_url
	 * @return string
	 */
	public static function getCurrentQuerystrings($question_param = true, $valid_html = true, $valid_url = true) 
	{
		$querystring = '';
		
		foreach ($_GET as $key => $value) {
			if ($valid_html === true) {
				$querystring .= urlencode($key) . '=' . urlencode($value);
			} else {
				$querystring .= $key . '=' . $value;
			}
			
			if (end($_GET) != $value) {
				if ($valid_url === true) {
					$querystring .= '&amp;';
				} else {
					$querystring .= '&';
				}
			}
		}
		
		if ($querystring != null && $question_param === true) {
			$querystring = '?' . $querystring;
		}
		
		return $querystring;
	}// getCurrentQuerystrings
	
	
	/**
	 * redirect to url that contain language
	 * example: 
	 * http://localhost/ -> http://localhost/en
	 * http://localhost/page -> http://localhost/en/page
	 * 
	 * @author Vee Winch.
	 * @license MIT
	 * @link http://okvee.net The author's website.
	 * @package Fuel Start
	 */
	public function redirectLanguageUri() 
	{
		$locales = \Config::get('locales');
		$default_lang = \Config::get('language');
		
		if (is_array($locales) && !empty($locales)) {
			if (!count($this->segments)) {
				// current uri is in root web. the url is http://domain.tld/fuelphp_root_web/
				$need_redirect = true;
				
				// redirect to http://domain.tld/fuelphp_root_web/{lang}
				$redirect_url = $default_lang;
			} else {
				// current url is in dir or /lang
				$first_uri = $this->segments[0];
				
				// if first uri is NOT in locales.
				if (!array_key_exists($first_uri, $locales)) {
					// first uri segment is not lang. the url is http://domain.tld/fuelphp_root_web/page
					// Never use redirect when current url is not at root web because HMVC request will get redirect and error or wrong result.
					// @todo fix redirect error.
					//$need_redirect = true;
					
					// redirect to http://domain.tld/fuelphp_root_web/{lang}/page
					//$redirect_url = $default_lang . '/' . implode('/', $this->segments);
				}
			}
			
			// if need to redirect.
			if (isset($need_redirect) && $need_redirect === true) {
				// set no cache header.
				$response = new Response();
				$response->set_header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate');
				$response->set_header('Expires', 'Mon, 26 Jul 1997 05:00:00 GMT');
				$response->set_header('Pragma', 'no-cache');
				$response->send_headers();
				
				// clean vars.
				unset($default_lang, $first_uri, $locales, $need_redirect);
				
				// go! redirect. (do not use fuelphp redirect because it generate error 404 in home page)
				$redirect_url = self::createNL($redirect_url);
				// use redirect manually.
				$response->set_status(301);
				$response->set_header('Location', $redirect_url);
				$response->send(true);
				exit;
			}
			
			// clean vars.
			unset($default_lang, $locales);
		}
			
		// clean vars.
		unset($default_lang, $locales);
	}// redirectLanguageUri


}

