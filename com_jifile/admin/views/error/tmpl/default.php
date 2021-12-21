<?php 
/**
* @subpackage	com_jifile
* @author		Antonio Di Girolamo & Giampaolo Losito
* @copyright	Copyright (C) 2011 isApp.it. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @link		http://jifile.isapp.it
*/
defined('_JEXEC') or die('Restricted access'); 
?>
<script type="text/javascript">
	var objVer = new updateIFile();
	string = new Array();
	string['error_ifile'] = '<?php echo JText::_( 'JIFILE_DOWNLOAD_IFILE_AJAX_ERROR' ); ?>';
	objVer.loadString(string);
</script>

<div id="updateIFile" style="font-size: 16px;">
	<div class="progress">
		<?php echo JText::_( 'JIFILE_DOWNLOAD_IFILE_PROGRESS' ); ?>
		<img src="components/com_jifile/images/loadingAnimation.gif" alt="Loading" />
	</div>
</div>
<div id="gotoJIfile" style="display: none;">
	<a href="index.php?option=com_jifile"><?php echo JText::_( 'CONTROL_PANEL' ); ?></a>
	<span class="ifile_update_timer"></span>
</div>