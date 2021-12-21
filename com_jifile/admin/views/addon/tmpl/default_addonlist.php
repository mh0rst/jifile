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

<?php JHTML::_('behavior.tooltip'); ?>

<form enctype="multipart/form-data" action="<?php echo JRoute::_('index.php?option=com_jifile');?>" method="post" name="adminForm" id="adminForm">
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="task" value="" />
	<table class="adminlist table table-striped table-hover">
		<thead>
			<tr>
				<th width="1%">
					<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
				</th>				
				<th width="3%">
					<?php echo JText::_('JIFILE_ICON'); ?>					
				</th>
				<th width="15%" class="center">
					<?php echo JText::_('JIFILE_NAME'); ?>					
				</th>
				<th width="10%" class="center">
					<?php echo JText::_('JIFILE_CONTEXT'); ?>
				</th>
				<th width="10%" class="center">
					<?php echo JText::_('TITLE'); ?>
				</th>
				<th width="30%">
					<?php echo JText::_('DESCRIPTION'); ?>
				</th>
				<th width="10%">
					<?php echo JText::_('CREATION_DATE'); ?>
				</th>
				<th width="10%">
					<?php echo JText::_('AUTHOR'); ?>
				</th>
				<th width="7%">
					<?php echo JText::_('JIFILE_VERSION'); ?>
				</th>
				<th width="2%">
					<?php echo JText::_('JIFILE_ACTIVE'); ?>
				</th>	
				<th width="2%">
					<?php echo JText::_('JIFILE_ADDON_CHECK_REPORT'); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="11">
					<?php //echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php foreach ($this->addon as $i => $item) :
		
			$manifest = $this->getManifestInfo($item);
//			$ordering	= ($listOrder == 'ordering');
//			$item->cat_link = JRoute::_('index.php?option=com_categories&extension=com_banners&task=edit&type=other&cid[]='. $item->catid);
//			$canCreate	= $user->authorise('core.create',		'com_banners.category.'.$item->catid);
//			$canEdit	= $user->authorise('core.edit',			'com_banners.category.'.$item->catid);
//			$canCheckin	= $user->authorise('core.manage',		'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
//			$canChange	= $user->authorise('core.edit.state',	'com_banners.category.'.$item->catid) && $canCheckin;
			?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="center">
					<?php
						if ($item['core'] == 0 || $item['delete'] == 1) { 
							echo JHtml::_('grid.id', $i, $item['id']);
						}
					?>
				</td>
				<td class="center">
					<?php
					// if image exists 
					if (!empty($item['image'])) {
						$image = $item['image'];
						//echo "<img width=\"16px\" height=\"16px\" src=\"".$item['image']."\" alt=\"".JText::_($item['title'])."\" />";	
					} else {
						$image = "components/com_jifile/images/icons/star_64.png";
					} 
					
					echo "<img width=\"16px\" height=\"16px\" src=\"".$image."\" alt=\"".JText::_($item['title'])."\" />";
					?>
				</td>				
				<td class="center">
					<?php echo $item['addon']; ?>
				</td>
				<td class="center">
					<?php echo $item['context']; ?>
				</td>
				<td class="center">
					<?php echo JText::_($item['title']); ?>
				</td>
				<td>
					<?php echo JText::_($item['description']); ?>
				</td>
				<td class="small">
					<?php echo JHtml::_('date', $item['dtinstall'], JText::_('DATE_FORMAT_LC1')); ?>
				</td>
				<td class="small">
					<span class="editlinktip hasTip" title="<?php echo addslashes(htmlspecialchars($manifest->authorInfo)); ?>">
						<?php echo JText::_($item['author']); ?>
					</span>
				</td>
				<td>
					<span class="badge badge-info"><?php echo JText::_($item['version']); ?></span>
				</td>
				<td class="center">
					<?php						
						if ($item['core'] == 0) { 
							echo JHtml::_('jgrid.published', $item['published'], $i, 'addon.');
						} else {
							//echo JHtml::_('jgrid.published', 0, $i, 'addon.', false);
							if (AdapterForJoomlaVersion::getInstance()->is(AdapterForJoomlaVersion::JOOMLA_3X)) {
								echo "<i class=\"icon-publish\"></i>";
							} else {
								echo "<span class=\"jgrid\"><span class=\"state protected\"></span></span>";
							}
						}
					?>
				</td>
				<td class="center">
					<?php	
						if ($item['checkReport'] == false) {
							if (AdapterForJoomlaVersion::getInstance()->is(AdapterForJoomlaVersion::JOOMLA_3X)) {
								$icon = "<i class=\"icon-warning\"></i>";
							} else {
								$icon = "<span class=\"jgrid\"><span class=\"state warning\"></span></span>";
							}
							
							
							echo "<a href=\"index.php?option=com_jifile&task=addon.checkreport&view=checkreport&tmpl=component&plugin={$item['id']}\" 
									 onclick=\"jQuery.colorbox({ href: this.href, width: '85%', height: '85%', iframe: true }); return false;\">								
									 {$icon}
								  </a>";						
						} else {
							echo "&nbsp";
						}
					?>
				</td>
			<?php endforeach; ?>
		</tbody>
	</table>
</form>