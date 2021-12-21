<?php 
/**
* @subpackage	com_jifile
* @author		Antonio Di Girolamo & Giampaolo Losito
* @copyright	Copyright (C) 2011 isApp.it. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @link		http://jifile.isapp.it
*/
defined('_JEXEC') or die('Restricted access'); ?>

<script type="text/javascript">
	var objVer = new CheckVer();
	string = new Array();
	string['error'] = '<?php echo JText::_( 'Error' ); ?>';
	string['success'] = '<?php echo JText::_( 'Success' ); ?>';
	string['search_update'] = '<?php echo JText::_( 'SEARCH_UPDATE' ); ?>';
	string['no_update'] = "<?php echo JText::_( 'NO_UPDATE' ); ?>";
	string['update'] = "<?php echo JText::_( 'COM_UPDATE' ); ?>";
	objVer.loadString(string);
</script>

<div id="checkVer"></div>