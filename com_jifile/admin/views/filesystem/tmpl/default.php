<?php 
/**
* @subpackage	com_jifile
* @author		Antonio Di Girolamo & Giampaolo Losito
* @copyright	Copyright (C) 2011 isApp.it. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @link		http://jifile.isapp.it
*/
defined('_JEXEC') or die('Restricted access');  
if(isset($this->error_pref)) {
	echo $this->error_pref;
} else {
?>

<div style="float: right">
	<p class="field switch">
	    <input type="radio" id="radio1" name="field"  checked />
	    <input type="radio" id="radio2" name="field" />
	    <label for="radio1" class="cb-enable <?php echo jifilehelper::inDebug() ? 'selected' : '' ?>"><span><?php echo JText::_('DEBUG_ENABLE') ?></span></label>
	    <label for="radio2" class="cb-disable <?php echo jifilehelper::inDebug() ? '' : 'selected' ?>"><span><?php echo JText::_('DEBUG_DISABLE') ?></span></label>
	</p>
</div>
<div>
	<p style="color:green;"><small>
		<?php 
			$extAllows = "<strong>".implode(", ", $this->extensionsAllows)."</strong>";
			echo sprintf(JText::_('JIFILE_EXTENSIONS_AUTOINDEX'), $extAllows); 
		?></small>
	</p>
</div>
<form class="form-inline" action="<?php echo JRoute::_('index.php?option=com_jifile&task=filesystem.'); ?>" method="post" name="adminForm" id="adminForm">
	<label for="search"><?php echo JText::_( 'JSEARCH_FILTER_LABEL' ); ?></label>
	<input type="text" name="search" id="search" value="<?php echo $this->listFilter['search']; ?>" size="50" placeholder="<?php echo JText::_( 'FILTER_BY_FILENAME' );?>"/>
	<?php echo $this->listFilter['ext']; ?>
	&nbsp;
	<div class="btn-group">
		<button class="btn" onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
		<button class="btn" onclick="document.getElementById('search').value='';this.form.getElementById('filter_ext').value='*';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
	</div>

<?php if (!empty($this->listfile) || (empty($this->listfile) && !empty($this->dir))) { ?>
<div style="text-align:right; float: right; width: 40%"><?php echo JText::_( 'TOTAL_FILES' ); ?>: <?php echo count($this->listfile); ?></div>


	<ul class="breadcrumb">
		<li><?php echo JText::_( 'CURRENT_DIRECTORY' ); ?>:</li>
		<?php 
			$linkDir = 'index.php?option=com_jifile&task=filesystem.&dir=';
			$current = '';
			if(!empty($this->dir)) {
				?><li><a href="<?php echo $linkDir ?>" title="<?php echo JText::_('OPEN_DIR').' '.$this->basepath ?>"><?php echo jifilehelper::encodingCharset(jifilehelper::getCorrectPath($this->basepath, false, false)) ?></a><span class="divider"><?php echo DS ?></span></li></li><?php 
				$listDir = explode(DS, $this->dir);
				$listDir = array_filter($listDir);
				$current = array_pop($listDir).DS;
				$toDir = '';
				foreach ($listDir as $dir) {
					$toDir .= $dir.DS;
					?><li><a href="<?php echo $linkDir.urlencode($toDir) ?>" title="<?php echo JText::_('OPEN_DIR').' '.$dir.DS ?>"><?php echo jifilehelper::encodingCharset($dir) ?></a><span class="divider"><?php echo DS ?></span></li><?php 
				}
				echo '<li class="active">'.jifilehelper::encodingCharset($current).'</li>';
			} else {
				echo '<li class="active">'.jifilehelper::encodingCharset($this->basepath).'</li>';
			}
		?>
	</ul>
	
	<table id="ifile" class="adminlist table table-hover">
		<thead>				
			<tr>
				<th width="4%"><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this)" /></th>
	            <th width="40%" class="title"><?php echo JText::_( 'Name' );?></th>
	            <th width="18%"><?php echo JText::_( 'JGLOBAL_FIELD_MODIFIED_LABEL' );?></th>
	            <th width="15%"><?php echo JText::_( 'Mime type' );?></th>
	            <th width="11%"><?php echo JText::_( 'Size' );?></th>
	            <th width="12%"><?php echo JText::_( 'Indexed' );?> <img title="<?php echo JText::_( 'Refresh' );?>" id="refreshIndex" src="../administrator/components/com_jifile/images/icon_refresh.png" alt="up" /></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="7">
					<div class="pull-left"><?php echo $this->pagination->getListFooter(); ?></div>
					<?php if (AdapterForJoomlaVersion::getInstance()->is(AdapterForJoomlaVersion::JOOMLA_3X)) { ?>
					<div class="pull-left"><?php echo $this->pagination->getLimitBox(); ?></div>
					<?php } ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php $i = $row = 0;
			foreach ($this->listfile as $key => $file) : 
				$this->loadInfoFile($file);
				$linkDir = false;
				if($this->infofile['mime'] == 'dir') {
					$dir = $this->dir.$this->infofile['name'];
					if($this->infofile['name'] == '..'.DS) {
						//$dir = empty($listDir) ? '' : end($listDir).DS; 
						$dir = implode(DS, $listDir).DS;
					}
					$linkDir 	= 'index.php?option=com_jifile&task=filesystem.&dir='.urlencode($dir);
				}
		?>
			<tr class="<?php echo 'row'.($row%2); ?>">
				<td>
					<?php 
						if(substr($this->infofile['name'], 0, 2) != '..') { 
					?>
						<input type="checkbox" id="cb<?php echo $i; ?>" 
							<?php echo (($this->infofile['mime'] != 'dir' && $this->infofile['indexed'] === 'ajax') ? 'class="checkIndex'.$i.'"' : '') ?> 
							name="file[<?php echo $i; ?>]" value="<?php echo urlencode($file) ?>" onclick="Joomla.isChecked(this.checked);"
							<?php echo (!in_array(strtolower($this->infofile['ext']), $this->extensionsAllows) && $this->infofile['mime'] != 'dir') ? 'disabled' : ''; ?> />
					<?php } ?>
				</td>
				<td>
				<?php if($linkDir) { ?>
					<a class="icon-16-folder iconFile" href="<?php echo $linkDir ?>" title="<?php echo JText::_('OPEN_DIR').' '.$this->infofile['nameview'] ?>"><?php echo $this->infofile['nameview'] ?></a>
				<?php } else { 
					echo '<span class="icon-16-'.strtolower($this->infofile['ext']).' iconFile"><a title="Download" href="index.php?option=com_jifile&task=filesystem.download&filename='.$this->basepath.$this->dir.urlencode($this->infofile['name']).'">'.$this->infofile['nameview'].'</a></span>'; 
				} ?>
				</td>
				<td class="small"><?php echo $this->infofile['date'] ?></td>
				<td class="small"><?php echo $this->infofile['mime'] ?></td>
				<td style="text-align:right">
					<?php if (!empty($this->infofile['size'])) { ?><span class="label label-info"><?php echo $this->infofile['size'] ?></span><?php } ?>
				</td>
				<?php 
					if($this->infofile['mime'] != 'dir') {
						?>
						<td style="text-align:center" class="indexed" id="indexed<?php echo $i ?>">
						<?php
							if($this->infofile['indexed'] === true) {
								?>
								<img src="components/com_jifile/images/tick.png" title="<?php echo JText::_('INDEXED'); ?>" />
								<?php 
							} else {
								?>
								<img src="components/com_jifile/images/publish_x.png" title="<?php echo JText::_('NO_INDEXED'); ?>" /><a 
								href="index.php?option=com_jifile&task=lucene.indexing&view=manualindex&tmpl=component&filename=<?php echo urlencode($this->infofile['filename']) ?>&id=<?php echo $i ?>" 
								onclick="jQuery.colorbox({ href: this.href, width: '85%', height: '85%', iframe: true }); return false;"><img 
								src="components/com_jifile/images/filesave.png" title="<?php echo JText::_('MANUAL_INDEXING'); ?>" />
								</a>
								<?php if (jifilehelper::inDebug()) { ?>
								<a 
								href="index.php?option=com_jifile&task=lucene.index&tmpl=component&file=<?php echo urlencode($this->infofile['filename']) ?>&id=<?php echo $i ?>" 
								onclick="jQuery.colorbox({ href: this.href, width: '65%', height: '65%', iframe: true }); return false;"><img 
								src="components/com_jifile/images/bug.png" title="<?php echo JText::_('Debug'); ?>" /></a>
								<?php }
							}
						?>
						</td>
						<?php
					} else {
						?>
						<td style="text-align:center" class="indexed"></td>
						<?php 
					}
				?>
			</tr>
		<?php 
			$i++;
			$row++;
			endforeach; 
		?>
		</tbody>
	</table>
<?php } elseif(empty($this->basepath)) {
	echo JText::_( 'NO_PATH_CONFIGURED' ).'. '.JText::_('BACK_CONTROL_PANEL');
} else {
	 echo JText::_( 'NO_FILES' );
}
?>
	<input type="hidden" name="dir" value="<?php echo $this->dir ?>" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
<?php } //nessun index path configurato
if (AdapterForJoomlaVersion::getInstance()->is(AdapterForJoomlaVersion::JOOMLA_3X)) {
	echo $this->loadTemplate('indexesmodal');
}
?>
<?php echo jifilehelper::getFirma(); ?>