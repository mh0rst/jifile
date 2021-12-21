<?php
/**
 * JiFile
 * 
 * @category   Joomla1.6!
 * @package    com_jifile
 * @author 	   Giampaolo Losito, Antonio Di Girolomo 
 * @copyright  Copyright (C) 2011 isApp.it - All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version    1.0
 */
defined( '_JEXEC' ) or die( 'Restricted access' );
/** JiFileLucenePluginAbstract */
require_once('jifileaddonabstract.php');


/**
 * Factory, for instance object of JiFile Lucene Plugins 
 *
 * @category   Joomla1.6!
 * @package    com_jifile
 * @subpackage helpers/interface
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @copyright  Copyright (C) www.isapp.it
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
class JiFilePluginFactory
{
	/**
	 * Instance of JiFilePluginFactory
	 * 
	 * @var JiFilePluginFactory
	 */
	private static $_instance;
	
	/**
	 * Registry of addon instance  
	 * 
	 * @var JiFilePluginFactory
	 */
	private $_registry;
	
	/**
	 * Construct private for Singleton
	 * @return 
	 */
	private function __construct() {}
	
	/**
	 * Return instance of JiFilePluginFactory
	 * @return JiFilePluginFactory
	 */
	static function getInstance() {		
		if (self::$_instance == null) 
			self::$_instance = new JiFilePluginFactory();
		return self::$_instance;
	}  
	
	/**
	 * Return Addon instance if exists in registry
	 * otherwise return false
	 * 
	 * @param string $addonName addon name
	 * @return mixed
	 */
	public function getRegistry($addonName) {
		if (isset($this->_registry[$addonName])) {
			return $this->_registry[$addonName];
		}
		return false;
	} 
	
	/**
	 * Set Addon instance if not exists in registry
	 * 
	 * @param string $addonName addon name
	 * @param object $addon addon instance
	 * @return void
	 */
	private function setRegistry($addonName, $addon) {
		if (!isset($this->_registry[$addonName])) {
			$this->_registry[$addonName] = $addon;
		}
	}
		
	/**
	 * Return Addon instance 
	 * @param array $addon addon information
	 * @return JiFilePluginInterface, JiFileLucenePluginInterface, JiFileAddonInterface
	 */ 
	public function getJifileAddon($addon) {
		// inizialize
		$addonInstance = null;
		
		// verify if exists addon instance
		$addonInstance = $this->getRegistry($addon['addon']);
		if ($addonInstance != false) {
			return $addonInstance; 
		}
		
		// Get Lucene Plugin 
		if ($addon['context'] == 'plugin' && $addon['type'] == 0) {
			$addonInstance = $this->getJifilePlugin($addon);	
			if (!empty($addonInstance)) {
				$this->setRegistry($addon['addon'], $addonInstance);
			}
		// Get Lucene Addon
		} elseif ($addon['context'] == 'admin' && $addon['type'] == 2 && $addon['plugin'] == 'lucene') {
			$addonInstance = $this->getLucenePlugin($addon);	
			if (!empty($addonInstance)) {
				$this->setRegistry($addon['addon'], $addonInstance);
			}
		// Component
		} elseif (($addon['context'] == 'admin' || $addon['context'] == 'site') && $addon['type'] == 0) {
			$addonInstance = $this->getJifileComponent($addon);	
			if (!empty($addonInstance)) {
				$this->setRegistry($addon['addon'], $addonInstance);
			}
		}
		
		return $addonInstance;
	}
	
	/**
	 * Get Path of Addon
	 * @param array $addon
	 * @return string
	 */
	public function getPathAddon(&$addon) {
		$addonPath = array();
		
		// Get Lucene Plugin 
		if ($addon['context'] == 'plugin' && $addon['type'] == 0) {
			// Explode the context.name command.
			list ($type, $name) = explode('.', $addon['addon']);
			$context = $addon['plugin'];
			
			// web 	
			switch($addon['context']) {
				case 'admin':
					$addonPath['web'] = '../administrator/components/com_jifile/addon/plugins/'.strtolower($context).'/'.strtolower($name).'/';
					break;
				case 'site':
					$addonPath['web'] = '../components/com_jifile/addon/plugins/'.strtolower($context).'/'.strtolower($name).'/';
					break;
			}						
			// Path Addon
			$addonPath['path']  = JIFILE_ADDON_PLUGIN_PATH.'/'.strtolower($context).'/'.strtolower($name).'/';	
						
		// Get Lucene Addon
		} elseif ($addon['context'] == 'admin' && $addon['type'] == 2 && $addon['plugin'] == 'lucene') {
			// Explode the controller.task command.
			list ($type, $task) = explode('.', $addon['task']);
			// web
			switch($addon['context']) {
				case 'admin':
					$addonPath['web'] = '../administrator/components/com_jifile/addon/'.strtolower($type).'/';
					break;
				case 'site':
					$addonPath['web'] = '../components/com_jifile/addon/'.strtolower($type).'/';
					break;
			}
			// Path Addon
			$addonPath['path']  = JIFILE_ADDON_PATH.'/'.$type.'/';
		// Component
		} elseif (($addon['context'] == 'admin' || $addon['context'] == 'site') && $addon['type'] == 0) {
			if ($addon['core'] == 0) {
				// Explode the controller.task command.
				list ($type, $task) = explode('.', $addon['task']);
				// web
				switch($addon['context']) {
					case 'admin':
						$addonPath['web'] = '../administrator/components/com_jifile/addon/'.strtolower($type).'/';
						break;
					case 'site':
						$addonPath['web'] = '../components/com_jifile/addon/'.strtolower($type).'/';
						break;
				}
				// Addon path
				$addonPath['path']  = JIFILE_ADDON_PATH.'/'.$type.'/';
			} else {
				// Explode the context.name command.				
				list ($context, $name) = explode('.', $addon['addon']);
					
				switch($addon['context']) {
					case 'admin':
						$addonPath['web'] = '../administrator/components/com_jifile/';
						$addonPath['path']  = JPATH_COMPONENT_ADMINISTRATOR.'/';
						break;
					case 'site':
						$addonPath['web'] = '../components/com_jifile/';
						$addonPath['path']  = JPATH_COMPONENT_SITE.'/';
						break;
				}	
			}					
		}
		
		return $addonPath;
	}
	
	/**
	 * Return new instance of Addon Object
	 * 
	 * @return JiFilePluginInterface
	 */
	public function getJifileComponent(&$plugin)
	{
				
		$className = '';
		// Explode the context.name command.
		$name = explode(".", $plugin['addon']);
		// define class name
		foreach ($name as $value) {
			$className .= ucfirst(strtolower($value));
		}
		
		// Explode the controller.task command.
		list ($type, $task) = explode('.', $plugin['task']);
				
		// File name
		$pathFile  = JIFILE_ADDON_PATH.'/'.$type.'/'.strtolower($plugin['addon']).'.php';
		
		// Check file exists 
		if (!file_exists($pathFile)) {
			throw new Exception(sprintf(JText::_('JIFILE_LUCENEPLUGINS_NOTFOUND'), $plugin['addon']));
		} 
		
		// require file
		require_once($pathFile);

		// Reflection		
		$reflection = new ReflectionClass($className);
		$found = false;
		// get interface of class
		$interfaces = $reflection->getInterfaces();
		// Check if class implements JiFileAddonInterface interface			
		foreach($interfaces as $interface) {
			if ($interface->getName() == 'JiFileAddonInterface') 
			{
				$found = true;
				break;	
			}				 
		} 
		if(!$found) {
			throw new Exception(JText::_('JIFILE_LUCENEPLUGIN_NOTIMPLEMENT_INTERFACE'));
		}
		
		// create instance
		$pluginInstance = $reflection->newInstance();
		// set plugin informations
		$pluginInstance->setPlugin($plugin); 
		 
		// return new instance of object
		return $pluginInstance;		
	}
	
	
	/**
	 * Return new instance of Addon Object
	 * 
	 * @return JiFilePluginInterface
	 */
	public function getJifilePlugin($plugin)
	{
				
		$className = '';		
		// Explode the context.name command.
		list ($type, $name) = explode('.', $plugin['addon']);
		$context = $plugin['plugin'];
		
		$className .= "plg".ucfirst(strtolower($context)).ucfirst(strtolower($name));
		
		// Path File
		$pathFile  = JIFILE_ADDON_PLUGIN_PATH.'/'.strtolower($context).'/'.strtolower($name).'/'.strtolower($name).'.php';
		
		// Check file exists 
		if (!file_exists($pathFile)) {
			throw new Exception(sprintf(JText::_('JIFILE_LUCENEPLUGINS_NOTFOUND'), $name));
		} 
		
		// require file
		require_once($pathFile);

		// Reflection		
		$reflection = new ReflectionClass($className);
		$found = false;
		// get interface of class
		$interfaces = $reflection->getInterfaces();
		// Check if class implements JiFilePluginInterface interface			
		foreach($interfaces as $interface) {
			if ($interface->getName() == 'JiFilePluginInterface') 
			{
				$found = true;
				break;	
			}				 
		} 
		if(!$found) {
			throw new Exception(JText::_('JIFILE_LUCENEPLUGIN_NOTIMPLEMENT_INTERFACE'));
		}
		
		// create instance
		$pluginInstance = $reflection->newInstance();
		// set plugin informations
		$pluginInstance->setPlugin($plugin); 
		 
		// return new instance of object
		return $pluginInstance; 
		
	}
	
	/**
	 * Return new instance of Lucene Plugin Object
	 * 
	 * @return JiFileLucenePluginInterface
	 */
	public function getLucenePlugin($plugin)
	{
		
		$className = '';
		$name = explode(".", $plugin['addon']);
		// Explode the controller.task command.
		list ($type, $task) = explode('.', $plugin['task']);
		
		foreach ($name as $value) {
			$className .= ucfirst(strtolower($value));
		}
				
		// File name
		$pathFile  = JIFILE_ADDON_PATH.'/'.$type.'/'.strtolower($plugin['addon']).'.php';
		
		// Check file exists 
		if (!file_exists($pathFile)) {
			throw new Exception(sprintf(JText::_('JIFILE_LUCENEPLUGINS_NOTFOUND'), $plugin['addon']));
		} 
		
		// require file
		require_once($pathFile);

		// Reflection		
		$reflection = new ReflectionClass($className);
		$found = false;
		// get interface of class
		$interfaces = $reflection->getInterfaces();
		// Check if class implements JiFileLucenePluginInterface interface			
		foreach($interfaces as $interface) {
			if ($interface->getName() == 'JiFileLucenePluginInterface') 
			{
				$found = true;
				break;	
			}				 
		} 
		if(!$found) {
			throw new Exception(JText::_('JIFILE_LUCENEPLUGIN_NOTIMPLEMENT_INTERFACE'));
		}
		
		// create instance
		$pluginInstance = $reflection->newInstance();
		// set plugin informations
		$pluginInstance->setPlugin($plugin); 
		 
		// return new instance of object
		return $pluginInstance; 
		
	}
}
?>