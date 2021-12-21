<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_search
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
?>

<dl id="ifile" class="search-results<?php echo $this->pageclass_sfx; ?>">
<?php 
$href_download = 'index.php?option=com_jifile&task=download&filename=%s&key=%s';
$i = 0;
foreach($this->results as $doc) : 
	$keyId = $doc['doc']->getFieldValue("key");
	$key = base64_encode($keyId);
	$title = $doc['doc']->getFieldValue("name");
	$href = sprintf($href_download, JFilterOutput::stringURLSafe($title), $key);
	$extension = strtoupper($doc['doc']->getFieldValue("extensionfile"));
	try {
		$created = $doc['doc']->getFieldValue('created');
	} catch (Exception $e) {
		$created = null;
	}
	$created = strtotime($created) ? $created : null;
	try {
		//$text = $doc['doc']->getFieldValue('body');
		$text = $this->highlightText($doc['doc']->getFieldValue('body'), $this->searchword, $this->state); 
	} catch (Exception $e) {
		//$text = $doc['doc']->getFieldValue('introtext').'...';
		$text = $this->highlightText($doc['doc']->getFieldValue('introtext'), $this->searchword, $this->state);
	}
?>
	<dt class="result-title">
		<?php echo $this->pagination->limitstart + ++$i.'. ';?>
		<?php if ($href) : ?>
			<a href="<?php echo JRoute::_($href); ?>"<?php /*if ($result->browsernav == 1) :?> target="_blank"<?php endif;*/ ?>>
				<?php echo $this->escape($title); ?>
			</a>
		<?php else:?>
			<?php echo $this->escape($title); ?>
		<?php endif; ?>
	</dt>
	<?php if ($extension) : ?>
		<dd class="result-category">
			<span class="<?php echo $this->pageclass_sfx; ?> icon-16-<?php  echo strtolower($extension) ?> icon"></span>
			<span class="small"><?php echo strtoupper($this->escape($extension)); ?></span>
		</dd>
	<?php endif; ?>
	<dd class="result-text" id="jifile_<?php echo $i ?>">
		<?php echo $text; ?>
	</dd>
	<?php if ($this->params->get('show_date')) : ?>
		<dd class="result-created<?php echo $this->pageclass_sfx; ?>">
			<?php echo JText::sprintf('JGLOBAL_CREATED_DATE_ON', $created); ?>
		</dd>
	<?php endif; ?>
<?php endforeach; ?>
</dl>

<div class="pagination">
	<?php echo $this->pagination->getPagesLinks(); ?>
</div>
