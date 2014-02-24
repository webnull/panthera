<?php
/**
  * Front controllers related functions
  *
  * @package Panthera\core
  * @author Damian Kęska
  * @license GNU Affero General Public License 3, see license.txt
  */

/**
 * Abstract interface for front controllers
 * 
 * @package Panthera\core
 * @author Damian Kęska
 */


abstract class frontController extends pantheraClass {
    
    // list of required modules
    protected $requirements = array();
    
    // list of required configuration overlays
    protected $overlays = array();
    
    // information for dispatcher to look for this controller name (full class name)
    public static $searchFrontControllerName = '';
    
    // ui.Titlebar integration eg. array('SEO links management', 'routing')
    protected $uiTitlebar = array();
    protected $uiTitlebarObject = null;
    
    /**
     * Initialize front controller
     * 
     * @return object
     */
    
    public function __construct ()
    {
        // run pantheraClass constructor to get Panthera Framework object
        parent::__construct();
        
        if ($this->requirements)
        {
            foreach ($this -> requirements as $module)
            {
                $this -> panthera -> importModule($module);
                
                if (!$this -> panthera -> moduleImported($module))
                {
                    throw new Exception('Cannot preimport required module "' .$module. '"', 234);
                }
            }
        }
        
        if ($this->overlays)
        {
            foreach ($this -> overlays as $overlay)
            {
                $this -> panthera -> config -> loadOverlay($overlay);
            }
        }
        
        if ($this->uiTitlebar)
        {
            if (isset($this->uiTitlebar[1]))
            {
                $name = localize($this->uiTitlebar[0], $this->uiTitlebar[1]);
            } else {
                $name = localize($this->uiTitlebar[0]);
            }
            
            $this -> uiTitlebarObject = new uiTitlebar($name);
        }
        
    }
    
    /**
     * Dummy function
     * 
     * @return null
     */
    
    public function display()
    {
        return '';
    }
    
    /**
     * Represent object as string
     * 
     * @return string
     */
    
    public function __toString()
    {
        return $this->display();
    }
}
