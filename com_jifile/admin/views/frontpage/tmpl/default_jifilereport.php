<?php 
/**
* @subpackage	com_jifile
* @author		Antonio Di Girolamo & Giampaolo Losito
* @copyright	Copyright (C) 2011 isApp.it. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @link		http://jifile.isapp.it
*/
defined('_JEXEC') or die('Restricted access'); 

$ahrefPref = '<a rel="{handler: \'iframe\', size: {x: 875, y: 550}, onClose: function() {}}" href="index.php?option=com_config&amp;view=component&amp;component=com_jifile&amp;path=&amp;tmpl=component" class="modal">'.JText::_('Configure').'</a>';
if (AdapterForJoomlaVersion::getInstance()->is(AdapterForJoomlaVersion::JOOMLA_3X)) {
	$uri = (string) JUri::getInstance();
	$return = urlencode(base64_encode($uri));
	$ahrefPref = '<a href="index.php?option=com_config&amp;view=component&amp;component=com_jifile&amp;path=&amp;return='.$return.'">'.JText::_('Configure').'</a>';
}
?>

<div class="row-fluid">
	<table class="adminlist table table-striped span6">
	  <caption>Filesystem</caption>
	  <thead>
	    <tr>
	      <th style="width: 50%"><?php echo (empty($this->basepath['dir']))  ? $ahrefPref : $this->basepath['dir']; ?></th>
	      <th style="width: 50%"><?php echo ($this->basepath['exists']) ? '<span class="ok label label-success">'.JText::_('Exists').'</span>' : '<span class="nook label label-important">'.JText::_('NOT_EXISTS').'</span>' ?></th>
	    </tr>
	  </thead>
	  <?php if($this->basepath['exists']) { ?>
	  <tbody>
	    <tr>
	      <td><?php echo JText::_('SIZE_ON_DISK'); ?></td>
	      <td><?php echo jifilehelper::getFormatSize($this->basepath['size']['size']); ?></td>
	    </tr>
	    <tr>
	      <td><?php echo JText::_('NUMBER_OF_FILES'); ?></td>
	      <td><?php echo $this->basepath['size']['count']; ?></td>
	    </tr>
	    <tr>
	      <td><?php echo JText::_('NUMBER_OF_DIRECTORIES'); ?></td>
	      <td><?php echo $this->basepath['size']['dircount']; ?></td>
	    </tr>
	  </tbody>
	  <?php } ?>
	</table>
	
	<table class="adminlist table table-striped span6">
	  <caption><?php echo JText::_('index') ?></caption>
	  <thead>
	    <tr>
	      <th style="width: 50%"><?php echo (empty($this->indexpath['dir'])) ? $ahrefPref : $this->indexpath['dir']; ?></th>
	      <th style="width: 50%"><?php echo ($this->indexpath['exists']) ? '<span class="ok label label-success">'.JText::_('Exists').'</span>' : '<span class="nook label label-important">'.JText::_('NOT_EXISTS').'</span>' ?></th>
	    </tr>
	  </thead>
	  <?php if($this->indexpath['exists']) { ?>
	  <tbody>
	    <tr>
	      <td><?php echo JText::_('SIZE_ON_DISK'); ?></td>
	      <td><?php echo jifilehelper::getFormatSize($this->indexpath['size']['size']); ?></td>
	    </tr>
	    <tr>
	      <td><?php echo JText::_('TOTAL_FILES_INCLUDED'); ?></td>
	      <td><?php echo $this->count; ?></td>
	    </tr>
	    <tr>
	      <td><?php echo JText::_('TOTAL_FILES_INDEXED'); ?></td>
	      <td><?php echo $this->numDocs; ?></td>
	    </tr>
	    <tr>
	      <td><?php echo JText::_('TOTAL_DELETED_FILES'); ?></td>
	      <td><?php echo $this->numDelete; ?></td>
	    </tr>
	    <tr>
	      <td><?php echo JText::_('OPTIMIZATION') ?></td>
	      <td><?php echo (!$this->optimize) ? '<span class="ok label label-success">'.JText::_('NOT_NECESSARY').'</span>' : '<span class="nook label label-warning">'.JText::_('Necessary').'</span>'; ?></td>
	    </tr>
	  </tbody>
	  <?php } ?>
	</table>

</div>