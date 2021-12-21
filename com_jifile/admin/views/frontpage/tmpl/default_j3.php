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
<div class="row-fluid">
	<!-- Begin Content -->
	<div class="span10">
		<?php echo JHtml::_('bootstrap.startTabSet', 'jifileTab', array('active' => 'front')); ?>

			<?php echo JHtml::_('bootstrap.addTab', 'jifileTab', 'front', '<span class="icon-home"></span> '.JText::_('CONTROL_PANEL', true)); ?>
				<?php echo $this->loadTemplate('front'); ?>
			<?php echo JHtml::_('bootstrap.endTab'); ?>

			<?php echo JHtml::_('bootstrap.addTab', 'jifileTab', 'jifilereport', '<span class="icon-file"></span> '.JText::_('Report JiFile', true)); ?>
				<?php echo $this->loadTemplate('jifilereport'); ?>
			<?php echo JHtml::_('bootstrap.endTab'); ?>

			<?php echo JHtml::_('bootstrap.addTab', 'jifileTab', 'reportcheck', '<span class="icon-eye-open"></span> '.JText::_('Report check system', true)); ?>
				<?php echo $this->loadTemplate('reportcheck'); ?>
			<?php echo JHtml::_('bootstrap.endTab'); ?>

			<?php echo JHtml::_('bootstrap.addTab', 'jifileTab', 'update', '<span class="icon-download"></span> '.JText::_('CHECK_UPDATE', true)); ?>
				<?php echo $this->loadTemplate('update'); ?>
			<?php echo JHtml::_('bootstrap.endTab'); ?>

			<?php echo JHtml::_('bootstrap.addTab', 'jifileTab', 'donation', '<span class="icon-plus icon-thumbs-up"></span> '.JText::_('DONATE', true)); ?>
				<?php echo $this->loadTemplate('donation'); ?>
			<?php echo JHtml::_('bootstrap.endTab'); ?>
			
			<?php echo JHtml::_('bootstrap.addTab', 'jifileTab', 'help', '<span class="icon-question-sign"></span> '.JText::_('JIFILE_HELP', true)); ?>
				<?php echo $this->loadTemplate('help'); ?>
			<?php echo JHtml::_('bootstrap.endTab'); ?>

		<?php echo JHtml::_('bootstrap.endTabSet'); ?>
	</div>
	<!-- End Content -->
</div>