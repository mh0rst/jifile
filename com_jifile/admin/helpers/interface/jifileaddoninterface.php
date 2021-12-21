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

/**
 * Interface of JiFile Components
 *
 * @category   Joomla1.6!
 * @package    com_jifile
 * @subpackage helpers/interface
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @copyright  Copyright (C) 2011 isApp.it - All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
interface JiFileAddonInterface
{
	/**
	 * Return FALSE if addon present an error
	 * 
	 * @return bool
	 */
	public function reportCheck();
	
	/**
	 * Return list of report check
	 * 
	 * @return array
	 */
	public function getListReportCheck();
		 
}
?>