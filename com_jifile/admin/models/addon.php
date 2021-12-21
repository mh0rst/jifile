<?php
/**
* @subpackage	com_jifile
* @author		Antonio Di Girolamo & Giampaolo Losito
* @copyright	Copyright (C) 2011 isApp.it. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @link		http://jifile.isapp.it
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.model' );
jimport('joomla.filesystem.folder');

class JifileModelAddon extends JModelLegacy {
	
	/**
	 * @var object JTable object
	 */
	protected $_table = null;
	
	protected $_paths = array();
	
	protected $_upgrade = null;
	/**
	 * True if existing files can be overwritten
	 */
	protected $_overwrite = false;
	
	protected $_plugin = false;
	
	public $manifest = null;
	
	/**
	 * Stack of installation steps
	 */
	protected $_stepStack = array();
	
	 
	/**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param       type    The table type to instantiate
     * @param       string  A prefix for the table class name. Optional.
     * @param       array   Configuration array for model. Optional.
     * @return      JTable  A database object
     * @since       2.5
     */
    public function getTable($type = 'Addon', $prefix = 'JiFileTable', $config = array()) 
    {
            return JTable::getInstance($type, $prefix, $config);
    }
	
	/**
	 * Set Addon if Published or UnPublisher 
	 * @param array $cid
	 * @param int $state
	 * @return void
	 */
	public function publish ($cid, $state) {
		$table = $this->getTable();
		$table->publish($cid, $state);
	}
	
	/**
	 * Return list of Addon for Admin Frontend
	 * @param array $filters [optional] 
	 * @param array $ordering [optional] 
	 * @param int $offset [optional] 
	 * @param int $limit [optional] 
	 * @return array
	 */	
	public function getAddon($filters = array(), $ordering = array(), $offset = 0, $limit = 0) {
		$tableAddon = JTable::getInstance('Addon', 'JifileTable');
		return $tableAddon->getAddon($filters, $ordering, $offset, $limit);
	}
	
	
	public function setPath($name, $value)
	{
		$this->_paths[$name] = $value;
	}
		
	public function getPath($name, $default = null)
	{
		return (!empty($this->_paths[$name])) ? $this->_paths[$name] : $default;
	}
	
	/**
	 * Pushes a step onto the installer stack for rolling back steps
	 */
	public function pushStep($step)
	{
		$this->_stepStack[] = $step;
	}
	
	public function generateManifestCache()
	{
		return json_encode(jifilehelper::parseXMLInstallFile($this->getPath('manifest')));
	}
	
	function install($package = null)
	{
		if (is_null($package)) {
			$package = $this->_getPackageFromUpload();
		}

		$app	= JFactory::getApplication();
		
		// Was the package unpacked?
		if (!$package) {
			$this->setError(JText::_('JIFILE_UNABLE_TO_FIND_INSTALL_PACKAGE'));
			//$app->setUserState('com_jifile.addon', JText::_('JIFILE_UNABLE_TO_FIND_INSTALL_PACKAGE'));
			return false;
		}
	
		// Install the package
		if (!$this->_readPackage($package['dir'])) {
			// There was an error installing the package
			//$msg = JText::sprintf('JIFILE_INSTALL_ERROR', JText::_('JIFILE_TYPE_TYPE_'.strtoupper($package['type'])));
			$this->setError(JText::_( 'JIFILE_INSTALL_ERROR' ));
			$result = false;
		} else {
			// Package installed sucessfully
			//$msg = JText::sprintf('JIFILE_INSTALL_SUCCESS', JText::_('JIFILE_TYPE_TYPE_'.strtoupper($package['type'])));
			$result = true;
		}
	
		// Set some model state values
	/*
		$app->enqueueMessage($msg);
		$this->setState('name', $installer->get('name'));
		$this->setState('result', $result);
		$app->setUserState('com_installer.message', $installer->message);
		$app->setUserState('com_installer.extension_message', $installer->get('extension_message'));
		$app->setUserState('com_installer.redirect_url', $installer->get('redirect_url'));
	*/
		
		if (!is_file($package['packagefile'])) {
			$config = JFactory::getConfig();
			$package['packagefile'] = $config->get('tmp_path') . '/' . $package['packagefile'];
		}
		
		if ($result) {
			$pluginPackage = null;
			if ($plugin = $this->manifest->plugin) {
				$pluginFile = $package['dir'].'/'.$plugin;
				if (file_exists($pluginFile)) {
					
					//reset obj attribute
					$this->_paths = $this->_stepStack = array();
					$this->_upgrade = $this->_overwrite = $this->manifest = null;
					$this->set('name', '');
					$this->set('element', '');
					$this->set('manifest_script', '');
					
					//install plugin
					$pluginPackage = $this->unpack($pluginFile);
					$this->install($pluginPackage);
				}
			}
		}
		
		// Cleanup the install files
		JInstallerHelper::cleanupInstall($package['packagefile'], $package['extractdir']);
	
		return $result;
	}
	
	public function uninstall($id)
	{
		// Initialise variables.
		$db = $this->getDbo();
		$row = null;
		$retval = true;
		
		$row = $this->getTable();
		
		if (!$row->load((int) $id))
		{
			JError::raiseWarning(100, JText::_('JLIB_INSTALLER_ERROR_COMP_UNINSTALL_ERRORUNKOWNEXTENSION'));
			return false;
		}
		
		// Is the component we are trying to uninstall a core one?
		// Because that is not a good idea...
		if (!$row->delete)
		{
			JError::raiseWarning(100, JText::_('JLIB_INSTALLER_ERROR_COMP_UNINSTALL_WARNCORECOMPONENT'));
			return false;
		}
		
		list($context, $name) = explode('.', $row->addon);

		// Get the admin and site paths for the component
		
		if ($row->context == 'plugin') {
			$this->setPath('extension_administrator', JPath::clean(JPATH_ADMINISTRATOR . '/components/com_jifile/addon/plugins/'.$row->plugin.'/'.$name));
		} else {
			$this->setPath('extension_site', JPath::clean(JPATH_SITE . '/components/com_jifile/addon/'.$name));
			$this->setPath('extension_administrator', JPath::clean(JPATH_ADMINISTRATOR . '/components/com_jifile/addon/'.$name));
		}
		
		$this->setPath('extension_root', $this->getPath('extension_administrator')); // copy this as its used as a common base
		
		/**
		 * ---------------------------------------------------------------------------------------------
		 * Manifest Document Setup Section
		 * ---------------------------------------------------------------------------------------------
		 */
		
		// Find and load the XML install file for the component
		$this->setPath('source', $this->getPath('extension_administrator'));
		
		// Get the package manifest object
		// We do findManifest to avoid problem when uninstalling a list of extension: getManifest cache its manifest file
		$this->findManifest();
		
		if (!$this->manifest)
		{
			// Make sure we delete the folders if no manifest exists
			JFolder::delete($this->getPath('extension_administrator'));
			JFolder::delete($this->getPath('extension_site'));
		
			// Raise a warning
			JError::raiseWarning(100, JText::_('JLIB_INSTALLER_ERROR_COMP_UNINSTALL_ERRORREMOVEMANUALLY'));
		
			// Return
			return false;
		}
		
		/**
		 * ---------------------------------------------------------------------------------------------
		 * Installer Trigger Loading and Uninstall
		 * ---------------------------------------------------------------------------------------------
		 */
		// If there is an manifest class file, lets load it; we'll copy it later (don't have dest yet)
		$scriptFile = (string) $this->manifest->scriptfile;
		
		if ($scriptFile)
		{
			$manifestScriptFile = $this->getPath('source') . '/' . $scriptFile;
		
			if (is_file($manifestScriptFile))
			{
				// load the file
				include_once $manifestScriptFile;
			}
		
			// Set the class name
			$classname = $name . 'InstallerScript';
		
			if (class_exists($classname))
			{
				// create a new instance
				$this->manifestClass = new $classname($this);
				// and set this so we can copy it later
				$this->set('manifest_script', $scriptFile);
		
				// Note: if we don't find the class, don't bother to copy the file
			}
		}
		
		ob_start();
		ob_implicit_flush(false);
		
		// run uninstall if possible
		if ($this->manifestClass && method_exists($this->manifestClass, 'uninstall'))
		{
			$this->manifestClass->uninstall($this);
		}
		
		$msg = ob_get_contents();
		ob_end_clean();
		
		/**
		 * ---------------------------------------------------------------------------------------------
		 * Database Processing Section
		 * ---------------------------------------------------------------------------------------------
		 */
		
		/*
		 * Let's run the uninstall queries for the component
		* If Joomla CMS 1.5 compatible, with discrete sql files - execute appropriate
		* file for utf-8 support or non-utf support
		*/
		// Try for Joomla 1.5 type queries
		// Second argument is the utf compatible version attribute
		if (isset($this->manifest->uninstall->sql))
		{
			$utfresult = $this->parseSQLFiles($this->manifest->uninstall->sql);
		
			if ($utfresult === false)
			{
				// Install failed, rollback changes
				JError::raiseWarning(100, JText::sprintf('JLIB_INSTALLER_ERROR_COMP_UNINSTALL_SQL_ERROR', $db->stderr(true)));
				$retval = false;
			}
		}
		
		/**
		 * ---------------------------------------------------------------------------------------------
		 * Filesystem Processing Section
		 * ---------------------------------------------------------------------------------------------
		 */
		
		// Let's remove those language files and media in the JROOT/images/ folder that are
		// associated with the component we are uninstalling
		//$this->removeFiles($this->manifest->languages);
		//$this->removeFiles($this->manifest->administration->languages, 1);
		/*
		// Remove the schema version
		$query = $db->getQuery(true);
		$query->delete()->from('#__jifileaddon')->where('id = ' . $id);
		$db->setQuery($query);
		$db->execute();
		
		// Check for errors.
		if ($db->getErrorNum())
		{
			JError::raiseWarning(100, JText::_('JLIB_INSTALLER_ERROR_COMP_UNINSTALL_FAILED_DELETE_CATEGORIES'));
			$this->setError($db->getErrorMsg());
			$retval = false;
		}
		*/
		// Now we need to delete the installation directories. This is the final step in uninstalling the component.
		// Delete the component site directory
		if (is_dir($this->getPath('extension_site')))
		{
			if (!JFolder::delete($this->getPath('extension_site')))
			{
				JError::raiseWarning(100, JText::_('JLIB_INSTALLER_ERROR_COMP_UNINSTALL_FAILED_REMOVE_DIRECTORY_SITE'));
				$retval = false;
			}
		}
	
		// Delete the component admin directory
		if (is_dir($this->getPath('extension_administrator')))
		{
			if (!JFolder::delete($this->getPath('extension_administrator')))
			{
				JError::raiseWarning(100, JText::_('JLIB_INSTALLER_ERROR_COMP_UNINSTALL_FAILED_REMOVE_DIRECTORY_ADMIN'));
				$retval = false;
			}
		}
	
		// Now we will no longer need the extension object, so let's delete it and free up memory
		$row->delete($row->id);
		unset($row);
	
		return ($retval !== FALSE) ? $name : $retval;
	}
	
	protected function _getPackageFromUpload()
	{
		// Get the uploaded file information
		$userfile = JRequest::getVar('install_package', null, 'files', 'array');
	
		// Make sure that file uploads are enabled in php
		if (!(bool) ini_get('file_uploads')) {
			JError::raiseWarning('', JText::_('COM_INSTALLER_MSG_INSTALL_WARNINSTALLFILE'));
			return false;
		}
	
		// Make sure that zlib is loaded so that the package can be unpacked
		if (!extension_loaded('zlib')) {
			JError::raiseWarning('', JText::_('COM_INSTALLER_MSG_INSTALL_WARNINSTALLZLIB'));
			return false;
		}
	
		// If there is no uploaded file, we have a problem...
		if (!is_array($userfile)) {
			JError::raiseWarning('', JText::_('COM_INSTALLER_MSG_INSTALL_NO_FILE_SELECTED'));
			return false;
		}
	
		// Check if there was a problem uploading the file.
		if ($userfile['error'] || $userfile['size'] < 1) {
			JError::raiseWarning('', JText::_('COM_INSTALLER_MSG_INSTALL_WARNINSTALLUPLOADERROR'));
			return false;
		}
	
		// Build the appropriate paths
		$config		= JFactory::getConfig();
		$tmp_dest	= $config->get('tmp_path') . '/' . $userfile['name'];
		$tmp_src	= $userfile['tmp_name'];
	
		// Move uploaded file
		jimport('joomla.filesystem.file');
		$uploaded = JFile::upload($tmp_src, $tmp_dest);
	
		// Unpack the downloaded package file
		$package = $this->unpack($tmp_dest);
	
		return $package;
	}
	
	public function unpack($p_filename)
	{
		jimport('joomla.filesystem.archive');
		// Path to the archive
		$archivename = $p_filename;
	
		// Temporary folder to extract the archive into
		$tmpdir = uniqid('install_');
	
		// Clean the paths to use for archive extraction
		$extractdir = JPath::clean(dirname($p_filename) . '/' . $tmpdir);
		$archivename = JPath::clean($archivename);
	
		// Do the unpacking of the archive
		$result = JArchive::extract($archivename, $extractdir);
	
		if ($result === false)
		{
			return false;
		}
	
		/*
		 * Let's set the extraction directory and package file in the result array so we can
		* cleanup everything properly later on.
		*/
		$retval['extractdir'] = $extractdir;
		$retval['packagefile'] = $archivename;
	
		/*
		 * Try to find the correct install directory.  In case the package is inside a
		* subdirectory detect this and set the install directory to the correct path.
		*
		* List all the items in the installation directory.  If there is only one, and
		* it is a folder, then we will set that folder to be the installation folder.
		*/
		$dirList = array_merge(JFolder::files($extractdir, ''), JFolder::folders($extractdir, ''));
	
		if (count($dirList) == 1)
		{
			if (JFolder::exists($extractdir . '/' . $dirList[0]))
			{
				$extractdir = JPath::clean($extractdir . '/' . $dirList[0]);
			}
		}
	
		/*
		 * We have found the install directory so lets set it and then move on
		* to detecting the extension type.
		*/
		$retval['dir'] = $extractdir;
		return $retval;
	}
	
	public function setupInstall()
	{
		// We need to find the installation manifest file
		if (!$this->findManifest())
		{
			return false;
		}
	/*
		// Load the adapter(s) for the install manifest
		$type = (string) $this->manifest->attributes()->type;
	
		// Lazy load the adapter
		if (!isset($this->_adapters[$type]) || !is_object($this->_adapters[$type]))
		{
			if (!$this->setAdapter($type))
			{
				return false;
			}
		}
	*/
		return true;
	}
	
	public function findManifest()
	{
		// Get an array of all the XML files from the installation directory
		$xmlfiles = JFolder::files($this->getPath('source'), '.xml$', 1, true);
		
		// If at least one XML file exists
		if (!empty($xmlfiles))
		{
	
			foreach ($xmlfiles as $file)
			{
				// Is it a valid Joomla installation manifest file?
				$manifest = $this->isManifest($file);
	
				if (!is_null($manifest))
				{
					// If the root method attribute is set to upgrade, allow file overwrite
					if ((string) $manifest->attributes()->method == 'upgrade')
					{
						$this->_upgrade = true;
						$this->_overwrite = true;
					}

					// If is plugin
					if ((string) $manifest->attributes()->type == 'plugin')
					{
						$this->_plugin = true;
					}
	
					// Set the manifest object and path
					$this->manifest = $manifest;
					$this->setPath('manifest', $file);
	
					// Set the installation source path to that of the manifest file
					$this->setPath('source', dirname($file));
	
					return true;
				}
			}
	
			// None of the XML files found were valid install files
			JError::raiseWarning(1, JText::_('JLIB_INSTALLER_ERROR_NOTFINDJOOMLAXMLSETUPFILE'));
	
			return false;
		}
		else
		{
			// No XML files were found in the install folder
			JError::raiseWarning(1, JText::_('JLIB_INSTALLER_ERROR_NOTFINDXMLSETUPFILE'));
			return false;
		}
	}
	
	public function isManifest($file)
	{
		// Initialise variables.
		$xml = JFactory::getXML($file);
	
		// If we cannot load the XML file return null
		if (!$xml)
		{
			return null;
		}
	
		if ($xml->getName() != 'addon')
		{
			return null;
		}
	
		// Valid manifest file return the object
		return $xml;
	}
	
	public function isUpgrade()
	{
		return $this->_upgrade;
	}
	
	public function isPlugin()
	{
		return $this->_plugin;
	}
	
	/**
	 * Get the allow overwrite switch
	 */
	public function isOverwrite()
	{
		return $this->_overwrite;
	}
	
	protected function _readPackage($path) {
	
		if ($path && JFolder::exists($path)) {
			$this->setPath('source', $path);
		} else {
			$this->setError(JText::_('JLIB_INSTALLER_ABORT_NOINSTALLPATH'));
			return false;
		}
	
		if (!$this->setupInstall())
		{
			$this->setError(JText::_('JLIB_INSTALLER_ABORT_DETECTMANIFEST'));
			return false;
		}
		
		$methodInstaller = 'install';
		// Get a database connector object
		$db = $this->getDbo();

		//addon id inizialize
		$aid = 0;
		//set name cleaned
		$name = strtolower(JFilterInput::getInstance()->clean((string) $this->manifest->name, 'cmd'));
		$this->manifest->name = $name;
		
		$this->set('name', $name);
		$this->set('element', $name);
		
		if ($this->isPlugin()) {
			$group = (string) $this->manifest->attributes()->group;
			$this->setPath('extension_administrator', JPath::clean(JPATH_ADMINISTRATOR . '/components/com_jifile/addon/plugins/'.$group.'/'.$name));
		} else {
			$this->setPath('extension_site', JPath::clean(JPATH_SITE . '/components/com_jifile/addon/'.$name));
			$this->setPath('extension_administrator', JPath::clean(JPATH_ADMINISTRATOR . '/components/com_jifile/addon/'.$name));
		}
	
		// copy this as its used as a common base
		$this->setPath('extension_root', $this->getPath('extension_administrator'));
	
		// Filesystem Processing Section
		// if new check
		if (file_exists($this->getPath('extension_site')) || file_exists($this->getPath('extension_administrator')))
		{
			$row = $this->getTable();
			$addon = strtolower($this->manifest->context).'.'.$name;
			$row = $row->find(array('addon' => $addon), array('id', 'version'));
			if ($row)  {
				$aid = $row[0]->id;
			}
			
			if ($this->isUpgrade() && $aid) {
				$methodInstaller = 'update';
			} else {
				if (file_exists($this->getPath('extension_site')))
				{
					// If the site exists say so.
					$this->setError(JText::sprintf('JLIB_INSTALLER_ERROR_COMP_INSTALL_DIR_SITE', $this->getPath('extension_site')));
				}
				if (file_exists($this->getPath('extension_administrator')))
				{
					// If the admin exists say so
					$this->setError(JText::sprintf('JLIB_INSTALLER_ERROR_COMP_INSTALL_DIR_ADMIN', $this->getPath('extension_administrator')));
				}
				return false;
			}
		}
		
		// If there is an manifest class file, lets load it; we'll copy it later (don't have dest yet)
		$manifestScript = (string) $this->manifest->scriptfile;
		
		if ($manifestScript)
		{
			$manifestScriptFile = $this->getPath('source') . '/' . $manifestScript;
		
			if (is_file($manifestScriptFile))
			{
				// Load the file
				include_once $manifestScriptFile;
			}
		
			// Set the class name
			$classname = $this->get('element') . 'InstallerScript';

			if (class_exists($classname))
			{
				// Create a new instance
				$this->manifestClass = new $classname($this);
		
				// And set this so we can copy it later
				$this->set('manifest_script', $manifestScript);
			}
		}
		
		// Run preflight if possible (since we know we're not an update)
		ob_start();
		ob_implicit_flush(false);
		
		if ($this->manifestClass && method_exists($this->manifestClass, 'preflight'))
		{
			if ($this->manifestClass->preflight($methodInstaller, $this) === false)
			{
				// Install failed, rollback changes
				$this->abort(JText::_('JLIB_INSTALLER_ABORT_COMP_INSTALL_CUSTOM_INSTALL_FAILURE'));
				return false;
			}
		}
		
		// Create msg object; first use here
		$msg = ob_get_contents();
		ob_end_clean();
		
		if (isset($this->manifest->site)) {
			// If the component directory does not exist, let's create it
			$created = false;
			
			if (!file_exists($this->getPath('extension_site')))
			{
				if (!$created = JFolder::create($this->getPath('extension_site')))
				{
					$this->setError(JText::sprintf('JLIB_INSTALLER_ERROR_COMP_INSTALL_FAILED_TO_CREATE_DIRECTORY_SITE', $this->getPath('extension_site')));
					return false;
				}
			}
			
			// Since we created the component directory and will want to remove it if we have to roll back
			// the installation, let's add it to the installation step stack
			
			if ($created)
			{
				$this->pushStep(array('type' => 'folder', 'path' => $this->getPath('extension_site')));
			}
			
			// Copy site files
			if ($this->parseFiles($this->manifest->site->files, 'site') === false)
			{
				// Install failed, rollback any changes
				$this->abort();
			
				return false;
			}
		}
		
		if (isset($this->manifest->admin)) {
			// If the component admin directory does not exist, let's create it
			$created = false;
			
			if (!file_exists($this->getPath('extension_administrator')))
			{
				if (!$created = JFolder::create($this->getPath('extension_administrator')))
				{
					$this->setError(JText::sprintf('JLIB_INSTALLER_ERROR_COMP_INSTALL_FAILED_TO_CREATE_DIRECTORY_ADMIN',$this->getPath('extension_administrator')));
					return false;
				}
			}
			
			/*
			 * Since we created the component admin directory and we will want to remove it if we have to roll
			* back the installation, let's add it to the installation step stack
			*/
			if ($created)
			{
				$this->pushStep(array('type' => 'folder', 'path' => $this->getPath('extension_administrator')));
			}
			
			// Copy admin files
			if ($this->parseFiles($this->manifest->admin->files, 'admin', 1) === false)
			{
				// Install failed, rollback any changes
				$this->abort();
			
				return false;
			}
		}
		
		//$this->parseLanguages($this->manifest->site->languages, 'site');
		//$this->parseLanguages($this->manifest->admin->languages, 'admin', 1);
		
		// If there is a manifest script, let's copy it.
		if ($this->get('manifest_script'))
		{
			$path = array();
			$path['src'] = $this->getPath('source') . '/' . $this->get('manifest_script');
			$path['dest'] = $this->getPath('extension_administrator') . '/' . $this->get('manifest_script');

			if (!file_exists($path['dest']) || $this->isOverwrite())
			{
				if (!$this->copyFiles(array($path)))
				{
					// Install failed, rollback changes
					$this->abort(JText::_('JLIB_INSTALLER_ABORT_COMP_INSTALL_MANIFEST'));
		
					return false;
				}
			}
		}
		
		/*
		 * ---------------------------------------------------------------------------------------------
		* Database Processing Section
		* ---------------------------------------------------------------------------------------------
		*/
		
		if ($methodInstaller == 'install') {
			// Run the install queries for the component
			if (isset($this->manifest->install->sql))
			{
				$result = $this->parseSQLFiles($this->manifest->install->sql);
			
				if ($result === false)
				{
					// Install failed, rollback changes
					$this->abort(JText::sprintf('JLIB_INSTALLER_ABORT_COMP_INSTALL_SQL_ERROR', $db->stderr(true)));
			
					return false;
				}
			}
		} else {
			/*
			 * Let's run the update queries for the component
			*/
			$version = $row[0]->version; 

			if ($this->manifest->update)
			{
				$result = $this->parseSchemaUpdates($this->manifest->update->schemas, $aid, $version);
			
				if ($result === false)
				{
					// Install failed, rollback changes
					$this->abort(JText::sprintf('JLIB_INSTALLER_ABORT_COMP_UPDATE_SQL_ERROR', $db->stderr(true)));
			
					return false;
				}
			}
		}
		
		
		/**
		 * ---------------------------------------------------------------------------------------------
		 * Custom Installation Script Section
		 * ---------------------------------------------------------------------------------------------
		 */
		
		/*
		 * If we have an install script, let's include it, execute the custom
		* install method, and append the return value from the custom install
		* method to the installation message.
		*/
		ob_start();
		ob_implicit_flush(false);
		
		$retManifestClass = true;
		if ($this->manifestClass && method_exists($this->manifestClass, $methodInstaller))
		{
			if ($this->manifestClass->$methodInstaller($this) === false)
			{
				$retManifestClass = false;
			}
		}
		
		if (!$retManifestClass) {
			// Install failed, rollback changes
			$this->abort(JText::_('JLIB_INSTALLER_ABORT_COMP_INSTALL_CUSTOM_INSTALL_FAILURE'));
			
			return false;
		}
		
		// Append messages
		$msg .= ob_get_contents();
		ob_end_clean();
		
		/**
		 * ---------------------------------------------------------------------------------------------
		 * Finalization and Cleanup Section
		 * ---------------------------------------------------------------------------------------------
		 */
		$this->saveAddon($aid);
		
		// We will copy the manifest file to its appropriate place.
		if (!$this->copyManifest())
		{
			// Install failed, rollback changes
			$this->abort(JText::_('JLIB_INSTALLER_ABORT_COMP_INSTALL_COPY_SETUP'));
			return false;
		}
		
		// And now we run the postflight
		ob_start();
		ob_implicit_flush(false);
		
		if ($this->manifestClass && method_exists($this->manifestClass, 'postflight'))
		{
			$this->manifestClass->postflight($methodInstaller, $this);
		}
		
		// Append messages
		$msg .= ob_get_contents();
		ob_end_clean();
		
		if ($msg != '')
		{
			$this->set('extension_message', $msg);
		}
		
		return true;
	}
	
	public function parseFiles($element, $folder, $cid = 0, $oldFiles = null, $oldMD5 = null)
	{
		// Get the array of file nodes to process; we checked whether this had children above.
		if (!$element || !count($element->children()))
		{
			// Either the tag does not exist or has no children (hence no files to process) therefore we return zero files processed.
			return 0;
		}
	
		// Initialise variables.
		$copyfiles = array();
	
		// Get the client info
		$client = JApplicationHelper::getClientInfo($cid);
	
		/*
		 * Here we set the folder we are going to remove the files from.
		*/
		if ($client)
		{
			$pathname = 'extension_' . $client->name;
			$destination = $this->getPath($pathname);
		}
		else
		{
			$pathname = 'extension_root';
			$destination = $this->getPath($pathname);
		}
	
		// Here we set the folder we are going to copy the files from.
	
		// Does the element have a folder attribute?
		//
		// If so this indicates that the files are in a subdirectory of the source
		// folder and we should append the folder attribute to the source path when
		// copying files.
	
		if ($folder && file_exists($this->getPath('source') . '/' . $folder))
		{
			$source = $this->getPath('source') . '/' . $folder;
		}
		else
		{
			$source = $this->getPath('source');
		}
	
		// Work out what files have been deleted
		if ($oldFiles && ($oldFiles instanceof SimpleXMLElement))
		{
			$oldEntries = $oldFiles->children();
	
			if (count($oldEntries))
			{
				$deletions = $this->findDeletedFiles($oldEntries, $element->children());
	
				foreach ($deletions['folders'] as $deleted_folder)
				{
					JFolder::delete($destination . '/' . $deleted_folder);
				}
	
				foreach ($deletions['files'] as $deleted_file)
				{
					JFile::delete($destination . '/' . $deleted_file);
				}
			}
		}
	
		// Copy the MD5SUMS file if it exists
		if (file_exists($source . '/MD5SUMS'))
		{
			$path['src'] = $source . '/MD5SUMS';
			$path['dest'] = $destination . '/MD5SUMS';
			$path['type'] = 'file';
			$copyfiles[] = $path;
		}
	
		// Process each file in the $files array (children of $tagName).
		foreach ($element->children() as $file)
		{
			$path['src'] = $source . '/' . $file;
			$path['dest'] = $destination . '/' . $file;
	
			// Is this path a file or folder?
			$path['type'] = ($file->getName() == 'folder') ? 'folder' : 'file';
	
			// Before we can add a file to the copyfiles array we need to ensure
			// that the folder we are copying our file to exits and if it doesn't,
			// we need to create it.
	
			if (basename($path['dest']) != $path['dest'])
			{
				$newdir = dirname($path['dest']);
	
				if (!JFolder::create($newdir))
				{
					JError::raiseWarning(1, JText::sprintf('JLIB_INSTALLER_ERROR_CREATE_DIRECTORY', $newdir));
					return false;
				}
			}
	
			// Add the file to the copyfiles array
			$copyfiles[] = $path;
		}
	
		return $this->copyFiles($copyfiles);
	}
	
	public function copyFiles($files, $overwrite = null)
	{
		// To allow for manual override on the overwriting flag, we check to see if
		// the $overwrite flag was set and is a boolean value.  If not, use the object
		// allowOverwrite flag.
	
		if (is_null($overwrite) || !is_bool($overwrite))
		{
			$overwrite = $this->_overwrite;
		}
	
		/*
		 * $files must be an array of filenames.  Verify that it is an array with
		* at least one file to copy.
		*/
		if (is_array($files) && count($files) > 0)
		{
	
			foreach ($files as $file)
			{
				// Get the source and destination paths
				$filesource = JPath::clean($file['src']);
				$filedest = JPath::clean($file['dest']);
				$filetype = array_key_exists('type', $file) ? $file['type'] : 'file';
	
				if (!file_exists($filesource))
				{
					/*
					 * The source file does not exist.  Nothing to copy so set an error
					* and return false.
					*/
					JError::raiseWarning(1, JText::sprintf('JLIB_INSTALLER_ERROR_NO_FILE', $filesource));
	
					return false;
				}
				elseif (($exists = file_exists($filedest)) && !$overwrite)
				{
	
					// It's okay if the manifest already exists
					if ($this->getPath('manifest') == $filesource)
					{
						continue;
					}
	
					// The destination file already exists and the overwrite flag is false.
					// Set an error and return false.
	
					JError::raiseWarning(1, JText::sprintf('JLIB_INSTALLER_ERROR_FILE_EXISTS', $filedest));
	
					return false;
				}
				else
				{
					// Copy the folder or file to the new location.
					if ($filetype == 'folder')
					{
						if (!(JFolder::copy($filesource, $filedest, null, $overwrite)))
						{
							JError::raiseWarning(1, JText::sprintf('JLIB_INSTALLER_ERROR_FAIL_COPY_FOLDER', $filesource, $filedest));
							return false;
						}
	
						$step = array('type' => 'folder', 'path' => $filedest);
					}
					else
					{
						if (!(JFile::copy($filesource, $filedest, null)))
						{
							JError::raiseWarning(1, JText::sprintf('JLIB_INSTALLER_ERROR_FAIL_COPY_FILE', $filesource, $filedest));
	
							return false;
						}
	
						$step = array('type' => 'file', 'path' => $filedest);
					}
	
					/*
					 * Since we copied a file/folder, we want to add it to the installation step stack so that
					* in case we have to roll back the installation we can remove the files copied.
					*/
					if (!$exists)
					{
						$this->_stepStack[] = $step;
					}
				}
			}
		}
		else
		{
			// The $files variable was either not an array or an empty array
			return false;
		}
	
		return count($files);
	}
	
	public function parseSQLFiles($element)
	{
		if (!$element || !count($element->children()))
		{
			// The tag does not exist.
			return 0;
		}

		// Initialise variables.
		$queries = array();
		$db = & $this->_db;
		$dbDriver = strtolower($db->name);

		if ($dbDriver == 'mysqli')
		{
			$dbDriver = 'mysql';
		}
		elseif($dbDriver == 'sqlsrv')
		{
			$dbDriver = 'sqlazure';
		}

		// Get the name of the sql file to process
		$sqlfile = '';

		foreach ($element->children() as $file)
		{
			$fCharset = (strtolower($file->attributes()->charset) == 'utf8') ? 'utf8' : '';
			$fDriver = strtolower($file->attributes()->driver);

			if ($fDriver == 'mysqli')
			{
				$fDriver = 'mysql';
			}
			elseif($fDriver == 'sqlsrv')
			{
				$fDriver = 'sqlazure';
			}

			if ($fCharset == 'utf8' && $fDriver == $dbDriver)
			{
				$sqlfile = $this->getPath('extension_root') . '/' . $file;

				// Check that sql files exists before reading. Otherwise raise error for rollback
				if (!file_exists($sqlfile))
				{
					JError::raiseWarning(1, JText::sprintf('JLIB_INSTALLER_ERROR_SQL_FILENOTFOUND', $sqlfile));

					return false;
				}

				$buffer = file_get_contents($sqlfile);

				// Graceful exit and rollback if read not successful
				if ($buffer === false)
				{
					JError::raiseWarning(1, JText::_('JLIB_INSTALLER_ERROR_SQL_READBUFFER'));

					return false;
				}

				// Create an array of queries from the sql file
				$queries = JInstallerHelper::splitSql($buffer);

				if (count($queries) == 0)
				{
					// No queries to process
					return 0;
				}

				// Process each query in the $queries array (split out of sql file).
				foreach ($queries as $query)
				{
					$query = trim($query);

					if ($query != '' && $query[0] != '#')
					{
						$db->setQuery($query);

						if (!$db->execute())
						{
							JError::raiseWarning(1, JText::sprintf('JLIB_INSTALLER_ERROR_SQL_ERROR', $db->stderr(true)));

							return false;
						}
					}
				}
			}
		}

		return (int) count($queries);
	}
	
	public function parseSchemaUpdates($schema, $eid, $version)
	{
		$files = array();
		$update_count = 0;
	
		// Ensure we have an XML element and a valid extension id
		if ($eid && $schema)
		{
			$db = JFactory::getDBO();
			$schemapaths = $schema->children();
	
			if (count($schemapaths))
			{
				$dbDriver = strtolower($db->name);
	
				if ($dbDriver == 'mysqli')
				{
					$dbDriver = 'mysql';
				}
				elseif ($dbDriver == 'sqlsrv')
				{
					$dbDriver = 'sqlazure';
				}
	
				$schemapath = '';
				foreach ($schemapaths as $entry)
				{
					$attrs = $entry->attributes();
					if ($attrs['type'] == $dbDriver)
					{
						$schemapath = $entry;
						break;
					}
				}
	
				if (strlen($schemapath))
				{
					$files = str_replace('.sql', '', JFolder::files($this->getPath('extension_root') . '/' . $schemapath, '\.sql$'));
					usort($files, 'version_compare');
	
					if (!count($files))
					{
						return false;
					}
	
					/*
					$query = $db->getQuery(true);
					$query->select('version_id')
					->from('#__schemas')
					->where('extension_id = ' . $eid);
					$db->setQuery($query);
					$version = $db->loadResult();
					*/
	
					if ($version)
					{
						// We have a version!
						foreach ($files as $file)
						{
							if (version_compare($file, $version) > 0)
							{
								$buffer = file_get_contents($this->getPath('extension_root') . '/' . $schemapath . '/' . $file . '.sql');
	
								// Graceful exit and rollback if read not successful
								if ($buffer === false)
								{
									JError::raiseWarning(1, JText::_('JLIB_INSTALLER_ERROR_SQL_READBUFFER'));
	
									return false;
								}
	
								// Create an array of queries from the sql file
								$queries = JInstallerHelper::splitSql($buffer);
	
								if (count($queries) == 0)
								{
									// No queries to process
									continue;
								}
	
								// Process each query in the $queries array (split out of sql file).
								foreach ($queries as $query)
								{
									$query = trim($query);
									if ($query != '' && $query[0] != '#')
									{
										$db->setQuery($query);
	
										if (!$db->execute())
										{
											JError::raiseWarning(1, JText::sprintf('JLIB_INSTALLER_ERROR_SQL_ERROR', $db->stderr(true)));
	
											return false;
										}
	
										$update_count++;
									}
								}
							}
						}
					}
	
					/*
					// Update the database
					$query = $db->getQuery(true);
					$query->delete()
					->from('#__schemas')
					->where('extension_id = ' . $eid);
					$db->setQuery($query);
	
					if ($db->execute())
					{
						$query->clear();
						$query->insert($db->quoteName('#__schemas'));
						$query->columns(array($db->quoteName('extension_id'), $db->quoteName('version_id')));
						$query->values($eid . ', ' . $db->quote(end($files)));
						$db->setQuery($query);
						$db->execute();
					}
					*/
				}
			}
		}
	
		return $update_count;
	}
	
	public function copyManifest($cid = 1)
	{
		// Get the client info
		$client = JApplicationHelper::getClientInfo($cid);
	
		$path['src'] = $this->getPath('manifest');
	
		if ($client)
		{
			$pathname = 'extension_' . $client->name;
			$path['dest'] = $this->getPath($pathname) . '/' . basename($this->getPath('manifest'));
		}
		else
		{
			$pathname = 'extension_root';
			$path['dest'] = $this->getPath($pathname) . '/' . basename($this->getPath('manifest'));
		}
	
		return $this->copyFiles(array($path), true);
	}
	
	public function abort($msg = null, $type = null)
	{
		$retval = true;
		$step = array_pop($this->_stepStack);
	
		// Raise abort warning
		if ($msg)
		{
			JLog::add($msg, JLog::WARNING, 'jerror');
		}
	
		while ($step != null)
		{
			switch ($step['type'])
			{
				case 'file':
					// Remove the file
					$stepval = JFile::delete($step['path']);
					break;
	
				case 'folder':
					// Remove the folder
					$stepval = JFolder::delete($step['path']);
					break;
	
				case 'query':
					// Placeholder in case this is necessary in the future
					// $stepval is always false because if this step was called it invariably failed
					$stepval = false;
					break;
	
				default:
					$stepval = false;
					break;
			}
	
			// Only set the return value if it is false
			if ($stepval === false)
			{
				$retval = false;
			}
	
			// Get the next step and continue
			$step = array_pop($this->_stepStack);
		}
	
		$conf = JFactory::getConfig();
		$debug = $conf->get('debug');
	
		if ($debug)
		{
			throw new RuntimeException('Installation unexpectedly terminated: ' . $msg, 500);
		}
	
		return $retval;
	}
	
	public function saveAddon($aid = 0) {
		$xmlData = $this->manifest;
		
		$table = $this->getTable();
	
		$date = JFactory::getDate();
	
		$table->id 			= $aid;
		$table->addon 		= strtolower($xmlData->context).'.'.$xmlData->name;
		$table->core 		= 0;
		$table->context 	= strtolower($xmlData->context);
		$table->image 		= (string) $xmlData->image;
		$table->type 		= (string) $xmlData->type;
		$table->option 		= (string) $xmlData->option;
		$table->task 		= (string) $xmlData->task;
		$table->view 		= (string) $xmlData->view;
		$table->template 	= (string) $xmlData->template;
		$table->onclick 	= (string) $xmlData->onclick;
		$table->link 		= (string) $xmlData->link;
		$table->target 		= (string) $xmlData->target;
		$table->title 		= (string) $xmlData->title;
		$table->description = (string) $xmlData->description;
		$table->dtinstall 	= $date->toSql();
		$table->ordering 	= $this->getNextOrder();
		$table->author 		= (string) $xmlData->author;
		$table->version 	= (string) $xmlData->version;
		$table->plugin 		= (string) $this->manifest->attributes()->group;
		$table->manifest_cache	= $this->generateManifestCache();
	
		if (!$table->store()) {
			$this->setError($table->getError());
			return false;
		}
	
		return true;
	}
	
	public function getNextOrder() {
		$db		= $this->getDbo();
		
		$query = $db->getQuery(true);
		
		$query
			->select(array('max(ordering)+1 as ordering'))
			->from('#__jifileaddon');
		
		$db->setQuery($query);
		
		$results = $db->loadObjectList();

		return $results[0]->ordering;
	} 
}