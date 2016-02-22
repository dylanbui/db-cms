<?php
/**
 *
 * @View Class
 *
 */

namespace App\Lib\Core;

class View
{	
	public $_disableLayout = false;	
	public $_default_layout_path = "default";
	
	/**
	 *
	 * The constructor, duh
	 * @var string
	 */
	public function __construct($default_layout_sub_path = "default")
	{
		$this->_default_layout_path = $default_layout_sub_path;
	}

	/**
	 * The variable property contains the variables
	 * that can be used inside of the templates.
	 *
	 * @access private
	 * @var array
	 */
	private $variables = array();

	/**
	 * The directory where the templates are stored
	 *
	 * @access private
	 * @var string
	 */
	private $template_dir = null;
	
	/**
	 * The directory where the templates are stored
	 *
	 * @access private
	 * @var string
	 */
	private $layout_dir = null;

    /**
     * The content html after render layout
     *
     * @access private
     * @var string
     */
    private $content_html = null;

    /**
	 * Adds a variable that can be used by the templates.
	 *
	 * Adds a new array index to the variable property. This
	 * new array index will be treated as a variable by the templates.
	 *
	 * @param string $name The variable name to use in the template
	 *
	 * @param string $value The content you assign to $name
	 *
	 * @access public
	 *
	 * @return void
	 *
	 * @see getVars, $variables
	 *
	 */
	public function __set($name, $value)
	{
		$this->variables[$name] = $value;
	}
	
	/**
	 * @Returns names of all the added variables
	 *
	 * Returns a numeral array containing the names of all
	 * added variables.
	 *
	 * @access public
	 * @return array
	 * @see addVar, $variables
	 */
	public function getVars()
	{
		 $variables = array_keys($this->variables);
		 return !empty($variables) ? $variables : false;
	}

    /**
     * Set template dir
     * @access public
     * @return none
     */
    public function setTemplateDir($template_dir)
    {
        $this->template_dir = $template_dir;
    }

    /**
     * Set layout dir
     * @access public
     * @return none
     */
    public function setLayoutDir($layout_dir)
    {
        $this->layout_dir = $layout_dir;
    }

    /**
     * Get content html
     * @access public
     * @return string
     */
    public function getContent()
    {
        return $this->content_html;
    }

    /**
     * Set content html
     * @access public
     * @return none
     */
    public function setContent($content)
    {
        $this->content_html = $content;
    }

    /**
     * @Returns Outputs the final template output
     * Fetches the final template output, and echoes it to the browser.
     *
     * Returns a numeral array containing the names of all
     * added variables.
     * @access public
     * @param string $full_path Filename (with path) to the template you want to output
     * @param array $args
     * @throws \Exception
     * @return null
     */
	public function parser($full_path,$args = array())
	{
		if (file_exists($full_path) == false)
		{
			throw new \Exception('View not found in '.$full_path);
		}
		
		$output = $this->getOutput($full_path,$args);
		return isset($output) ? $output : false;		
	}
	
	public function fetch($path,$args = array()) 
	{
		$path = $this->template_dir . '/' . $path . '.phtml';
			
		if (file_exists($path) == false)
		{
			throw new \Exception('View not found in '.$path);
		}

        $variables = $this->variables;
        if(!empty($args))
            $variables = array_merge($variables,$args);

        $output = $this->getOutput($path,$variables);
		return isset($output) ? $output : false;
	}
	
	public function renderLayout($content_path, $layout_path = null)
	{
        $this->variables['main_content'] = $this->fetch($content_path);

        if($this->_disableLayout == true)
        {
            $this->content_html = $this->variables['main_content'];
            return $this->content_html;
        }

		if(is_null($layout_path))
            $layout_path = $this->layout_dir . "/{$this->_default_layout_path}/default.phtml";
		else
            $layout_path = $this->layout_dir . '/' . $layout_path . '.phtml';
			
		if (file_exists($layout_path) == false)
		{
			throw new \Exception('Layout not found in '.$layout_path);
		}

        $this->content_html = $this->getOutput($layout_path,$this->variables);
		return isset($this->content_html) ? $this->content_html : false;
    }

	/**
	 * @Returns Fetch the template output, and return it
	 * @param string $template_file Filename (with path) to the template to be processed
     * @param array() $args
     * @throws \Exception
	 * @return string Returns a string on success, and FALSE on failure
	 * @access private
	 */
	private function getOutput($template_file,$args = array())
	{
		// -- Khong can khi dang o trong view => $this la chinh no --
		// $args['_view'] = $this;
		
		/*** extract all the variables ***/
		extract($args);
	
		if (file_exists($template_file))
		{
			ob_start();
			include($template_file);
			$output = ob_get_contents();
			ob_end_clean();
		}
		else
		{
			throw new \Exception("The template file '$template_file' does not exist");
		}
		return !empty($output) ? $output : false;
	}

} /*** end of class ***/

?>
