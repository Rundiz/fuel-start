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
            // this condition is requested by normal request.
            array_shift($this->segments);
            $this->uri = implode('/', $this->segments);

            Config::set('language', $first);
            Config::set('locale', $locales[$first]['locale']);
        } else {
            // this condition is requested by hmvc. so /{lang} prefix can not be retrieve via $this->segments.
            $uris = \Input::uri();
            if (mb_substr($uris, 0, 1) == '/') {
                $uris = mb_substr($uris, 1);
            }
            $uri_exp = explode('/', $uris);
            
            if (array_key_exists($uri_exp[0], $locales)) {
                Config::set('language', $uri_exp[0]);
                Config::set('locale', $locales[$uri_exp[0]]['locale']);
            }
            unset($uri_exp, $uris);
        }
    }// detectLanguage
    
    
    /**
     * get all the query string
     * @param boolean $url_encode
     * @return array
     */
    public static function getAllQuerystring($url_encode = true)
    {
        $querystring_array = array();
        
        if (isset($_GET) && is_array($_GET)) {
            $querystrings = \Input::server('QUERY_STRING');
            $querystring_exp = explode('&', $querystrings);
            
            if (is_array($querystring_exp) && !empty($querystring_exp)) {
                foreach ($querystring_exp as $querystring) {
                    $querystring_n_v = explode('=', $querystring);
                    
                    if (is_array($querystring_n_v)) {
                        if ($url_encode !== true) {
                            $querystring_array[urldecode($querystring_n_v[0])] = (isset($querystring_n_v[1]) ? urldecode($querystring_n_v[1]) : null);
                        } else {
                            $querystring_array[$querystring_n_v[0]] = (isset($querystring_n_v[1]) ? $querystring_n_v[1] : null);
                        }
                    } else {
                        $querystring_array[$querystring_n_v];
                    }
                }
                
                return $querystring_array;
            } else {
                if (is_array($querystrings)) {
                    return $querystrings;
                } else {
                    return array($querystrings);
                }
            }
        } else {
            if (!is_array($_GET)) {
                return array($_GET);
            } else {
                return $_GET;
            }
        }
    }// getAllQuerystring


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
        // the current query string can be ?a=b, ?a[]=b, ?a[a]=b, ?aก[bข]=b, ?a=b&amp;=b (a=b amp;=b), and can be more weird. so, i cannot use $_SERVER['querystring'] to re-format valid url and valid html encode.
        $all_querystring = static::getAllQuerystring($valid_html);
        $querystring = '';
        $querystring_array = array();
        
        if (!empty($all_querystring)) {
            foreach ($all_querystring as $q_name => $q_value) {
                if (!empty($q_name) || !empty($q_value)) {
                    $querystring_array[] = $q_name . '=' . $q_value;
                }
            }
        }
        
        if ($valid_url === true) {
            $querystring = implode('&amp;', $querystring_array);
        } else {
            $querystring = implode('&', $querystring_array);
        }

        unset($querystring_array);

        if ($querystring != null && $question_param === true) {
            $querystring = '?' . $querystring;
        }
        
        return $querystring;
    }// getCurrentQuerystrings
    
    
    /**
     * get current protocol
     * 
     * @return string
     */
    public static function protocol()
    {
        if (\Input::server('SERVER_PORT') == '443') {
            return 'https://';
        } else {
            return 'http://';
        }
    }// protocol


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
                $uri_exp = explode('/', \Input::uri());
                // the \Input::uri will return uri segments with / at the start. when explode it, the first array might be null.
                // check that first array of exploded uri is not null.
                if (isset($uri_exp[0]) && $uri_exp[0] != null) {
                    $first_uri = $uri_exp[0];
                } elseif (isset($uri_exp[1])) {
                    $first_uri = $uri_exp[1];
                } else {
                    // in case that \Input::uri with exploded / is not array or something wrong.
                    $first_uri = $default_lang;
                }

                // if first uri is NOT in locales.
                if (!array_key_exists($first_uri, $locales)) {
                    // first uri segment is not lang. the url is http://domain.tld/fuelphp_root_web/page
                    $need_redirect = true;

                    // redirect to http://domain.tld/fuelphp_root_web/{lang}/page
                    $redirect_url = $default_lang . '/' . implode('/', $this->segments);
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
    
    
    /**
     * get website root path
     * example: website install at /fuelstart from webroot (/). sitePath will return /fuelstart
     * 
     * @param string $uri
     * @return string
     */
    public static function sitePath($uri = '')
    {
        $site_url = \Uri::create($uri);
        
        $domain = static::protocol() . \Input::server('HTTP_HOST');
        
        return str_replace($domain, '', $site_url);
    }// sitePath


}
