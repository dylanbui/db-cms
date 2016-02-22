<?php

function redirect($uri = '', $method = 'location', $http_response_code = 302)
{
    if ( ! preg_match('#^https?://#i', $uri))
    {
        $uri = site_url($uri);
    }
    
    switch($method)
    {
        case 'refresh'  : header("Refresh:0;url=".$uri);
            break;
        default         : header("Location: ".$uri, TRUE, $http_response_code);
            break;
    }
    exit;
}

function current_site_url($uri = '')
{
    $pageURL = 'http';
    $pageURL .= "://";
    if ($_SERVER["SERVER_PORT"] != "80") {
        $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"];
    } else {
        $pageURL .= $_SERVER["SERVER_NAME"];
    }
    return $pageURL . site_url($uri);       
}

function site_url($uri = '')
{
    return __BASE_URL.ltrim($uri, '/');
}

// Check varible existed or not
function df(&$value, $default = "")
{
    return empty($value) ? $default : $value;
}

function h(&$str)
{
    return isset($str) ? htmlspecialchars($str) : '';
//      return isset($str) ? nl2br(htmlspecialchars_decode($str)) : '';
        // Chu y : Khi su dung PDO thi no tu dong encode html khi insert, ke ca textarea cung bi thay the \n = <br/>
//      return isset($str) ? nl2br(htmlspecialchars($str)) : '';        
//      return empty($str) ? '' : nl2br(htmlspecialchars($str));
}

function xh(&$str)
{
    //      return isset($str) ? $str : '';
    //      return isset($str) ? nl2br(htmlspecialchars_decode($str)) : '';
    // Chu y : Khi su dung PDO thi no tu dong encode html khi insert, ke ca textarea cung bi thay the \n = <br/>
    //      return isset($str) ? nl2br(htmlspecialchars($str)) : '';
    return empty($str) ? '' : nl2br(htmlspecialchars($str));
}

function n(&$str ,$decimals = 0)
{
    return isset($str) ? number_format($str, $decimals, '.', ',') : '';
//         return empty($str) ? '' : nl2br(htmlspecialchars($str));
}

// Show data html from database
function html(&$str)
{
    return empty($str) ? '' : htmlspecialchars_decode($str);
}

function now_to_mysql()
{
    return date('Y-m-d H:i:s');
}

function mysql_to_fulldate($date)
{
    if(empty($date) || $date=='0000-00-00 00:00:00')
        return '';
    return date("Y-m-d H:i:s", strtotime($date));
}

function mysql_to_unix_timestamp($date)
{
    if(empty($date) || $date=='0000-00-00 00:00:00')
        return '';
    return strtotime($date);
}

function string_to_datetime($str_date, $str_format)
{
    // PHP 5.3 and up
    $myDateTime = DateTime::createFromFormat($str_format, $str_date);
    return $myDateTime->getTimestamp();
}

function real_escape_string($str)
{
    return addslashes($str);
}

/**
* Determines if the current version of PHP is greater then the supplied value
*
* Since there are a few places where we conditionally test for PHP > 5
* we'll set a static variable.
*
* @access   public
* @param    string
* @return   bool
*/
function is_php($version = '5.0.0')
{
    static $_is_php;
    $version = (string)$version;
    
    if ( ! isset($_is_php[$version]))
    {
        $_is_php[$version] = (version_compare(PHP_VERSION, $version) < 0) ? FALSE : TRUE;
    }

    return $_is_php[$version];
}

// ------------------------------------------------------------------------

/**
 * Tests for file writability
 *
 * is_writable() returns TRUE on Windows servers when you really can't write to 
 * the file, based on the read-only attribute.  is_writable() is also unreliable
 * on Unix servers if safe_mode is on. 
 *
 * @access  private
 * @return  void
 */
function is_really_writable($file)
{   
    // If we're on a Unix server with safe_mode off we call is_writable
    if (DIRECTORY_SEPARATOR == '/' AND @ini_get("safe_mode") == FALSE)
    {
        return is_writable($file);
    }

    // For windows servers and safe_mode "on" installations we'll actually
    // write a file then read it.  Bah...
    if (is_dir($file))
    {
        $file = rtrim($file, '/').'/'.md5(rand(1,100));

        if (($fp = @fopen($file, FOPEN_WRITE_CREATE)) === FALSE)
        {
            return FALSE;
        }

        fclose($fp);
        @chmod($file, DIR_WRITE_MODE);
        @unlink($file);
        return TRUE;
    }
    elseif (($fp = @fopen($file, FOPEN_WRITE_CREATE)) === FALSE)
    {
        return FALSE;
    }

    fclose($fp);
    return TRUE;
}

function ip_address()
{
    static $ip = FALSE;
    
    if( $ip ) {
        return $ip;
    }
    //Get IP address - if proxy lets get the REAL IP address

    if (!empty($_SERVER['REMOTE_ADDR']) AND !empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    } elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = '0.0.0.0';
    }

    //Clean the IP and return it
    return $ip = preg_replace('/[^0-9\.]+/', '', $ip);
}

/**
 * Create a encryption string
 *
 * @return string
 */
function encryption($string ,$salt = "")
{
    return sha1($salt.$string); 
}

/**
 * Create a fairly random 32 character MD5 token
 *
 * @return string
 */
function token()
{
    return md5(str_shuffle(chr(mt_rand(32, 126)). uniqid(). microtime(TRUE)));
}

/**
 * Encode a string so it is safe to pass through the URI
 * @param string $string
 * @return string
 */
function base64_url_encode($string = NULL)
{
    return strtr(base64_encode($string), '+/=', '-_~');
}

/**
 * Decode a string passed through the URI
 *
 * @param string $string
 * @return string
 */
function base64_url_decode($string = NULL)
{
    return base64_decode(strtr($string, '-_~','+/='));
}

//  http://phpgoogle.blogspot.com/2007/08/four-ways-to-generate-unique-id-by-php.html
//  http://kvz.io/blog/2009/06/10/create-short-ids-with-php-like-youtube-or-tinyurl/
function create_uniqid($random_id_length = 10)
{
    //generate a random id encrypt it and store it in $rnd_id
    $rnd_id = crypt(uniqid(rand(),1), 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ');
    
    //to remove any slashes that might have come
    $rnd_id = strip_tags(stripslashes($rnd_id));
    
    //Removing any . or / and reversing the string
    $rnd_id = str_replace(".","",$rnd_id);
    $rnd_id = strrev(str_replace("/","",$rnd_id));
    
    //finally I take the first 10 characters from the $rnd_id
    $rnd_id = substr($rnd_id,0,$random_id_length);      
    
    return $rnd_id;
}


/**
 * An example of a general-purpose implementation that includes the optional
 * functionality of allowing multiple base directories for a single namespace
 * prefix.
 * 
 * Given a foo-bar package of classes in the file system at the following
 * paths ...
 * 
 *     /path/to/packages/foo-bar/
 *         src/
 *             Baz.php             # Foo\Bar\Baz
 *             Qux/
 *                 Quux.php        # Foo\Bar\Qux\Quux
 *         tests/
 *             BazTest.php         # Foo\Bar\BazTest
 *             Qux/
 *                 QuuxTest.php    # Foo\Bar\Qux\QuuxTest
 * 
 * ... add the path to the class files for the \Foo\Bar\ namespace prefix
 * as follows:
 * 
 *      <?php
 *      // instantiate the loader
 *      $loader = new \Example\Psr4AutoloaderClass;
 *      
 *      // register the autoloader
 *      $loader->register();
 *      
 *      // register the base directories for the namespace prefix
 *      $loader->addNamespace('Foo\Bar', '/path/to/packages/foo-bar/src');
 *      $loader->addNamespace('Foo\Bar', '/path/to/packages/foo-bar/tests');
 * 
 * The following line would cause the autoloader to attempt to load the
 * \Foo\Bar\Qux\Quux class from /path/to/packages/foo-bar/src/Qux/Quux.php:
 * 
 *      <?php
 *      new \Foo\Bar\Qux\Quux;
 * 
 * The following line would cause the autoloader to attempt to load the 
 * \Foo\Bar\Qux\QuuxTest class from /path/to/packages/foo-bar/tests/Qux/QuuxTest.php:
 * 
 *      <?php
 *      new \Foo\Bar\Qux\QuuxTest;
 */
class Psr4Autoloader
{
    /**
     * An associative array where the key is a namespace prefix and the value
     * is an array of base directories for classes in that namespace.
     *
     * @var array
     */
    protected $prefixes = array();

    /**
     * Register loader with SPL autoloader stack.
     * 
     * @return void
     */
    public function register()
    {
        spl_autoload_register(array($this, 'loadClass'));
    }

    /**
     * Adds a base directory for a namespace prefix.
     *
     * @param string $prefix The namespace prefix.
     * @param string $base_dir A base directory for class files in the
     * namespace.
     * @param bool $prepend If true, prepend the base directory to the stack
     * instead of appending it; this causes it to be searched first rather
     * than last.
     * @return void
     */
    public function addNamespace($prefix, $base_dir, $prepend = false)
    {
        // normalize namespace prefix
        $prefix = trim($prefix, '\\') . '\\';

        // normalize the base directory with a trailing separator
        $base_dir = rtrim($base_dir, DIRECTORY_SEPARATOR) . '/';

        // initialize the namespace prefix array
        if (isset($this->prefixes[$prefix]) === false) {
            $this->prefixes[$prefix] = array();
        }

        // retain the base directory for the namespace prefix
        if ($prepend) {
            array_unshift($this->prefixes[$prefix], $base_dir);
        } else {
            array_push($this->prefixes[$prefix], $base_dir);
        }
    }

    /**
     * Loads the function file for a given class name.
     *
     * @param string $path_file The fully-qualified class name.
     * @return mixed The mapped file name on success, or boolean false on
     * failure.
     */
    public function addFunction($path_file)
    {
        if(!is_array($path_file))
            $path_file = array($path_file);

        foreach ($path_file as $file)
            $this->requireFile($file);
    }
    /**
     * Loads the class file for a given class name.
     *
     * @param string $class The fully-qualified class name.
     * @return mixed The mapped file name on success, or boolean false on
     * failure.
     */
    public function loadClass($class)
    {
        // the current namespace prefix
        $prefix = $class;

        // work backwards through the namespace names of the fully-qualified
        // class name to find a mapped file name
        while (false !== $pos = strrpos($prefix, '\\')) {

            // retain the trailing namespace separator in the prefix
            $prefix = substr($class, 0, $pos + 1);

            // the rest is the relative class name
            $relative_class = substr($class, $pos + 1);

            // try to load a mapped file for the prefix and relative class
            $mapped_file = $this->loadMappedFile($prefix, $relative_class);
            if ($mapped_file) {
                return $mapped_file;
            }

            // remove the trailing namespace separator for the next iteration
            // of strrpos()
            $prefix = rtrim($prefix, '\\');   
        }

        // never found a mapped file
        return false;
    }

    /**
     * Load the mapped file for a namespace prefix and relative class.
     * 
     * @param string $prefix The namespace prefix.
     * @param string $relative_class The relative class name.
     * @return mixed Boolean false if no mapped file can be loaded, or the
     * name of the mapped file that was loaded.
     */
    protected function loadMappedFile($prefix, $relative_class)
    {
        // are there any base directories for this namespace prefix?
        if (isset($this->prefixes[$prefix]) === false) {
            return false;
        }

        // look through base directories for this namespace prefix
        foreach ($this->prefixes[$prefix] as $base_dir) {

            // replace the namespace prefix with the base directory,
            // replace namespace separators with directory separators
            // in the relative class name, append with .php
            $file = $base_dir
                  . str_replace('\\', '/', $relative_class)
                  . '.php';

            // if the mapped file exists, require it
            if ($this->requireFile($file)) {
                // yes, we're done
                return $file;
            }
        }

        // never found it
        return false;
    }

    /**
     * If a file exists, require it from the file system.
     * 
     * @param string $file The file to require.
     * @return bool True if the file exists, false if not.
     */
    protected function requireFile($file)
    {
        if (file_exists($file)) {
            require $file;
            return true;
        }
        return false;
    }
}