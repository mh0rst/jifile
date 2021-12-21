<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_templates
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;
?>
<div id="ifile">
<form id="form-xpdfrc" action="<?php echo JRoute::_('index.php?option=com_jifile'); ?>" method="post" name="adminForm" id="source-form" class="form-validate">
	<fieldset>		
		<legend><?php echo JText::_('JIFILE_EDIT_XPDFRC'); ?></legend>
		<?php if (!property_exists($this->xpdfrc, 'error')) { ?>
			<div style="float: right;">		
				<button class="btn btn-success" id="sendxpdfrc" type="button"><?php echo JText::_( 'Save' ); ?></button>
				<button class="btn" id="close" onclick="objwritexpdfrc.pw.jQuery.colorbox.close();" type="button"><?php echo JText::_( 'Close' ); ?></button>
			</div>
		<?php } ?>
		<div id="result" class="result"> </div>				
		<div class="clr"></div>		
		<?php if (!property_exists($this->xpdfrc, 'error')) { ?>
			<hr/>
			<textarea class="filed span12" name="xpdfrc" id="content" rows="12" cols="100"><?php echo $this->xpdfrc->source; ?></textarea>
			<input type="hidden" name="task" value="config.savexpdfrc" />		
		<?php } else { ?>
			<div class="result_img error_img" title="Error'"><?php echo $this->xpdfrc->error; ?></div>
		<?php } ?>
		<?php echo JHtml::_('form.token'); ?>
	</fieldset>
</form>
</div>