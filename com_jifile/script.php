<?php
/**
* @subpackage	com_jifile
* @author		Antonio Di Girolamo & Giampaolo Losito
* @copyright	Copyright (C) 2011 isApp.it. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @link		http://jifile.isapp.it
*/
defined ( '_JEXEC' ) or die ( 'Restricted access' );

jimport( 'joomla.filesystem.folder' );

class com_jifileInstallerScript
{
	private $path_jifile;
	private $path_ifile;
	private $path_zend;
	private $jifile_ver_old;
	
	public function __construct() {
		$this->path_jifile 	= JPATH_ADMINISTRATOR.'/components/com_jifile';
		$this->path_ifile 	= $this->path_jifile.'/libraries/ifile';
		$this->path_zend 	= $this->path_jifile.'/libraries/ifile/Zend';
		
		$this->path_ifile_old = JPATH_LIBRARIES.'/ifile';
		$this->path_zend_old = JPATH_LIBRARIES.'/ifile/Zend';
		
		$this->jifile_ver_old = 0;
	}
	 
	/**
	 * method to uninstall the component
	 *
	 * @return void
	 */
	function uninstall($parent) 
	{
		include_once $this->path_jifile.'/version.php';
		include_once $this->path_jifile.'/helpers/jifilehelper.php';;

		jifilehelper::checkVersion(0);
		
		//cancello solo per versione vecchia, anche se in teoria dovrebbe essere stata cancellata dall'update
		if(JFolder::exists($this->path_ifile_old) && !JFolder::delete($this->path_ifile_old)) {
			?>
			<p style="font-size: 200%">
				Uninstalling of iFile Library failed! Manually delete the iFile library in the libraries folder of joomla!
			</p>
			<?php 
		}
	}
	
	function preflight( $type, $parent ) {
		if ( $type == 'update' ) {
			
			//recupero versione attuale di JiFile
			if (file_exists($this->path_jifile.'/version.php')) {
				require_once $this->path_jifile.'/version.php';
				$this->jifile_ver_old = JIFILEVER;
			}
			
			//se esiste ifile in libreries salvo la config e cancello
			if(JFolder::exists($this->path_ifile_old)) {
				rename($this->path_ifile_old.'/config/IFileConfig.xml', $this->path_jifile.'/IFileConfig.xml');
				JFolder::delete($this->path_ifile_old);
			} else {
				//salvo la vecchia config se versione minore di 2.3
			//	rename($this->path_ifile.'/config/IFileConfig.xml', $this->path_jifile.'/IFileConfig.xml');
			}
		}
	}
	
	function update($parent) {
		$res = true;
		//se la versione attuale e' minore di 2.0 installo sql della 2.0
		if (version_compare($this->jifile_ver_old, '2.0', '<')) {
			$res =  $this->installSql20($parent);
		}
		// se la versione e' minore della 2.3 cancella la cartella di IFILE
		return $res;
	}
	
	function installSql20($parent) {
		$sqlfile = JPATH_ADMINISTRATOR . '/components/' . $parent->get('element') . '/sql/updates/mysql/2.0.sql';
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
		
		$db = JFactory::getDBO();
		
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
		return true;
	}
	
	function postflight($type, $parent)
	{
		include_once $this->path_jifile.'/version.php';
		?>
		<style type="text/css">
			.adminform tr th {
				display: none;
			}
			#jifile-installer {
				margin: 10px auto;
				padding: 8px;
				width: 700px;
				min-height: 48px;
				background-color: #fff;
				border: 1px solid #ccc;
				-webkit-border-radius: 10px;
				-moz-border-radius: 10px;
				border-radius: 10px;
			}

			#jifile-installer .status-ok {
				color: green;
			}

			#jifile-installer .extension-copyright {
				color: #777;
				display: block;
				margin-top: 12px;
			}

			#jifile-installer .extension-name {
				color: #3db4ff;
				font-family: Arial, Helvetica, sans-serif;
				font-size: 16px;
				font-weight: bold;
			}
			
			#jifile-installer .installer-messages-header {
				margin: 10px 0;
				font-family: Arial, Helvetica, sans-serif;
				font-size: 16px;
				font-weight: bold;
			}

			#jifile-installer table {
				padding: 0;
				margin: 0;
				border: none;
			}

			#jifile-installer table td {
				vertical-align: top;
			}
			
			#jifile-installer .jifile-installer-next {
				float: left;
				margin-right: 10px;
			}

			#jifile-installer .btn {
				display: inline-block;
				*display: inline;
				*zoom: 1;
				padding: 4px 14px;
				margin-bottom: 0;
				font-size: 13px;
				line-height: 18px;
				*line-height: 18px;
				text-align: center;
				vertical-align: middle;
				cursor: pointer;
				color: #333;
				text-shadow: 0 1px 1px rgba(255,255,255,0.75);
				background-color: #f5f5f5;
				background-image: -moz-linear-gradient(top,#fff,#e6e6e6);
				background-image: -webkit-gradient(linear,0 0,0 100%,from(#fff),to(#e6e6e6));
				background-image: -webkit-linear-gradient(top,#fff,#e6e6e6);
				background-image: -o-linear-gradient(top,#fff,#e6e6e6);
				background-image: linear-gradient(to bottom,#fff,#e6e6e6);
				background-repeat: repeat-x;
				filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffffffff', endColorstr='#ffe5e5e5', GradientType=0);
				border-color: #e6e6e6 #e6e6e6 #bfbfbf;
				border-color: rgba(0,0,0,0.1) rgba(0,0,0,0.1) rgba(0,0,0,0.25);
				*background-color: #e6e6e6;
				filter: progid:DXImageTransform.Microsoft.gradient(enabled = false);
				border: 1px solid #bbb;
				*border: 0;
				border-bottom-color: #a2a2a2;
				-webkit-border-radius: 4px;
				-moz-border-radius: 4px;
				border-radius: 4px;
				*margin-left: .3em;
				-webkit-box-shadow: inset 0 1px 0 rgba(255,255,255,.2), 0 1px 2px rgba(0,0,0,.05);
				-moz-box-shadow: inset 0 1px 0 rgba(255,255,255,.2), 0 1px 2px rgba(0,0,0,.05);
				box-shadow: inset 0 1px 0 rgba(255,255,255,.2), 0 1px 2px rgba(0,0,0,.05);
			}
		
			#jifile-installer .btn:hover,
			#jifile-installer .btn:active,
			#jifile-installer .btn.active {
				color: #333;
				text-decoration: none;
				background-color: #e6e6e6;
				*background-color: #d9d9d9;
				background-position: 0 -15px;
				-webkit-transition: background-position .1s linear;
				-moz-transition: background-position .1s linear;
				-o-transition: background-position .1s linear;
				transition: background-position .1s linear;
			}

			#jifile-installer .btn:active,
			#jifile-installer .btn.active {
				background-color: #cccccc \9;
			}
		</style>
		<div id="jifile-installer">
			<table width="95%" cellpadding="0" cellspacing="0">
				<tbody>
				<tr>
					<td width="300px">
						<img src="http://www.isapp.it/images/boxes/Scatola_JIFile_300x300.png" alt=""/>
					</td>
					<td>
						<div>
							<span class="extension-name">JiFile <?php echo JIFILEVER; ?></span><br />
							Indexing and search document PDF, DOC, JPG on the your Joomla!
						</div>

						<div class="extension-copyright">
							&copy; 2011-<?php echo date('Y'); ?> isApp.it 
							(<a target="_blank" href="http://www.isapp.it">www.isapp.it</a> | <a target="_blank" href="http://jifile.isapp.it">http://jifile.isapp.it</a>).<br/>
							All rights reserved!
						</div>

						<div class="installer-messages-header status-ok">
							Installation completed
						</div>

						<div>
							<div class="jifile-installer-next">
								<a href="index.php?option=com_plugins&view=plugins&filter_search=jifile&filter_folder=search" class="btn">
									Enable Search Plugin
								</a>
							</div> 
							<div class="jifile-installer-next">
								<a href="index.php?option=com_jifile" class="btn">
									JiFile Control Panel
								</a>
							</div>
						</div>
					</td>
				</tr>
				</tbody>
			</table>
		</div>
		<?php 
	}
}
