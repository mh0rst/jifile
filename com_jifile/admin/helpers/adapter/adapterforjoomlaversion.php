<?php
/**
 * JoomPhotoMobile
 * 
 * @category   Joomla1.6!
 * @package    com_joomphotomobile
 * @author 	   Angelo Costanza, Giampaolo Losito, Antonio Di Girolomo 
 * @copyright  Copyright (C) 2011 isApp.it - All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @version    2.0
 */
// No direct access
defined('_JEXEC') or die('Restricted access');

class AdapterForJoomlaVersion {
	/**	
	 * Instance of AdapterForJoomlaVersion
	 * 
	 * @var AdapterForJoomlaVersion
	 */
	private static $_instance;
	/**
	 * Joomla Version
	 * @var String
	 */
	private $_joomlaVersion = null;
	/**
	 * Joomla!3.x
	 */
	const JOOMLA_3X = 3;
	/**
	 * Joomla!2.x
	 */
	const JOOMLA_2X = 2;
	/**
	 * Joomla!1.x
	 */
	const JOOMLA_1X = 1;
	
	/**
	 * Construct private for Singleton
	 * @return 
	 */
	private function __construct() {
		// for Joomla 2.5.x
		if(!defined('DS')){
			define('DS',DIRECTORY_SEPARATOR);
		}
		// get version of Joomla!
		$this->getJoomlaVersion();
	}
	
	/**
	 * Return instance of AdapterForJoomlaVersion
	 * @return JPhotoMobileFactory
	 */
	static function getInstance() {
		if (self::$_instance == null) {
			self::$_instance = new AdapterForJoomlaVersion();
		}
						
			
		return self::$_instance;
	}  
	
	/**
	 * Return an JModel object
	 * 
	 * @param string $name
	 * @param string $prefix
	 * @return JModel
	 */
	public function getJModel($name, $prefix = '', $config = array()) {
		// initialize
		$jmodel = null;
		// define joomla version
		switch ($this->_joomlaVersion) {
			case AdapterForJoomlaVersion::JOOMLA_3X:
					$jmodel = JModelLegacy::getInstance($name, $prefix, $config);
				break;
			case AdapterForJoomlaVersion::JOOMLA_2X:					
					$jmodel = JModel::getInstance($name, $prefix, $config);
				break;
			default:
				// @TODO
				break;
		}
		
		return $jmodel;
	}
	
	/**
	 * Return an JInstallerManifest object
	 * 
	 * @param string $xmlpath
	 * @return JInstallerManifest 
	 */
	public function getJInstallerManifest($xmlpath) {
		// initialize
		$manifest = null;
		// define joomla version
		switch ($this->_joomlaVersion) {
			case AdapterForJoomlaVersion::JOOMLA_3X:
					jimport('cms.installer.manifest.package');
					$manifest = new JInstallerManifestPackage($xmlpath);
				break;
			case AdapterForJoomlaVersion::JOOMLA_2X:
					jimport('joomla.installer.packagemanifest');
					$manifest = new JPackageManifest($xmlpath);
				break;
			default:
				// @TODO
				break;
		}
		
		return $manifest;
	}
	
	/**
	 * Return Joomla Version (3.x - 2.x - 1.x)
	 * @return integer
	 */
	public function getJoomlaVersion() {
		
		$versionFile = JPATH_LIBRARIES."/cms/version/version.php";

		// workaroud for Joomla 3.8.x, default setting
        $this->_joomlaVersion = AdapterForJoomlaVersion::JOOMLA_3X;

		if ($this->_joomlaVersion == null && file_exists($versionFile)) {
			require_once ($versionFile);
			// get JVersion object
			$version = new JVersion();
			// check joomla version
			if (version_compare($version->getShortVersion(), 3.0, '>=')) {
				$this->_joomlaVersion = AdapterForJoomlaVersion::JOOMLA_3X;
			} elseif (version_compare($version->getShortVersion(), 3.0, '<') && version_compare($version->getShortVersion(), 2.0, '>=')) {
				$this->_joomlaVersion = AdapterForJoomlaVersion::JOOMLA_2X;
			} else {
				// errore versione non supportata
				$this->_joomlaVersion = AdapterForJoomlaVersion::JOOMLA_1X;
			} 
		}
		
		return $this->_joomlaVersion;
	}

	/**
	 * Return true or false if the current version is equal to that passed
	 * @return boolean
	 */
	public function is($version) {
		return ($this->getJoomlaVersion() == $version);
	}
}