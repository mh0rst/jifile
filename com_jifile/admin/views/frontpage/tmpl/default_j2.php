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

<table width="100%">
	<tr>
		<td width="45%" valign="top">
			<div id="cpanel">
				<?php echo $this->loadTemplate('front'); ?>
			</div>
		</td>
		<td width="55%" valign="top" align="left" id="ifile">
			<?php
				echo JHtml::_('sliders.start','panel-sliders',array('useCookie'=>'1'));
				echo JHtml::_('sliders.panel', 'Report JiFile', 'cpanel-panel-reportfile');
			?>
				<?php echo $this->loadTemplate('jifilereport'); ?>
			<?php
				echo JHtml::_('sliders.panel', 'Report check system <span id="attention_report_check">'.JText::_('WARNING_REPORT_CHECK').'</span>', 'cpanel-panel-reportcheck');
			?>
				<?php echo $this->loadTemplate('reportcheck'); ?>
			<?php
				echo JHtml::_('sliders.panel', JText::_('CHECK_UPDATE'), 'cpanel-panel-update');
			?>
				<?php echo $this->loadTemplate('update'); ?>
			<?php
				echo JHtml::_('sliders.panel', JText::_('DONATE'), 'cpanel-panel-donate');
			?>
				<?php echo $this->loadTemplate('donation'); ?>
			<?php
				echo JHtml::_('sliders.panel', JText::_('JIFILE_HELP'), 'cpanel-panel-donate');
			?>
				<?php echo $this->loadTemplate('help'); ?>
			<?php 
			echo JHtml::_('sliders.end');
			?>
		</td>
	</tr>
</table>