<?php 
/**
* @subpackage	com_jifile
* @author		Antonio Di Girolamo & Giampaolo Losito
* @copyright	Copyright (C) 2011 isApp.it. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @link		http://jifile.isapp.it
*/
defined('_JEXEC') or die('Restricted access'); 
$jAdapter = AdapterForJoomlaVersion::getInstance();
?>  

<?php echo $this->loadTemplate('j'.$jAdapter->getJoomlaVersion()); ?>

<script type="text/javascript">
<!--
jQuery(document).ready(function() {
	string = new Array();
	string['error'] = '<?php echo JText::_( 'Error' ); ?>';
	string['success'] = '<?php echo JText::_( 'Success' ); ?>';
	string['update'] = '<?php echo JText::_( 'Update' ); ?>';
	string['loading'] = '<?php echo JText::_( 'Loading' ); ?>';
	string['cancel'] = '<?php echo JText::_( 'Cancel' ); ?>';
	string['canceled'] = '<?php echo JText::_( 'Canceled' ); ?>';
	string['op_canceled'] = '<?php echo JText::_( 'OPERATION_CANCELED' ); ?>';
	string['unknown_error'] = '<?php echo JText::_( 'UNKNOWN_ERROR' ); ?>';
	string['no_file_selected'] = '<?php echo JText::_( 'NO_FILE_SELECTED' ); ?>';
	objIndexing.loadString(string);
});
//-->
</script>
