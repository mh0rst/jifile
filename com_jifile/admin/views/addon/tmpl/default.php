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
	submitInstall = function(pressbutton) {
		var form = document.getElementById('adminInstallForm');

		// do field validation
		if (form.install_package.value == ""){
			alert("<?php echo JText::_('JIFILE_MSG_INSTALL_PLEASE_SELECT_A_PACKAGE', true); ?>");
		} else {
			form.submit();
		}
	}
</script>

<form enctype="multipart/form-data" action="<?php echo JRoute::_('index.php?option=com_jifile');?>" method="post" name="adminInstallForm" id="adminInstallForm" class="form-inline">
	<div class="width-100">
		<fieldset class="uploadform">
			<legend><?php echo JText::_('JIFILE_INSTALL_ADDON'); ?></legend>
			<label for="install_package"><?php echo JText::_('JIFILE_INSTALLER_PACKAGE_FILE'); ?></label>
			<input id="install_package" name="install_package" type="file" size="57" />
			<input class="button btn" type="button" value="<?php echo JText::_('JIFILE_INSTALLER_UPLOAD_AND_INSTALL'); ?>" onclick="submitInstall()" />
		</fieldset>
		<div class="clr"></div>
		<input type="hidden" name="type" value="" />
		<input type="hidden" name="task" value="addon.install" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
<div class="clr"></div>
<?php echo $this->loadTemplate('addonlist'); ?>
<div class="clr"></div>
<?php echo jifilehelper::getFirma(); ?>