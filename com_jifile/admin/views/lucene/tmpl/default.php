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
<script type="text/javascript">
window.addEvent('domready', function() {
	$$('#ifile tr').addEvent('click',function(event) {
		if ((event.target || event.srcElement).type !== 'checkbox') {
			checkb = this.getElement('input[name^=indexId]');
			if(checkb) {
				checkb.click();
			}
		}
	});
});
function submitbutton(pressbutton) {
	submitform(pressbutton);
}
function resetForm(dom, elem) {
	
	jQuery('input[name=filter_order]').val('');
	jQuery('input[name=filter_order_Dir]').val('');
	jQuery('input[name=invioform]').val('1');
	if(elem == 'order') {
		return;
	}
	
	jQuery('#search').val('');
	<?php 
		foreach ($this->pluginLucene as $plugin) {
			$getFiltersName = $plugin->getFiltersName();
			
			if (!empty($getFiltersName) && is_array($getFiltersName)) {
				foreach ($getFiltersName as $filtername) {
					echo "jQuery('#{$filtername}').val('');";	
				}
			}
		}
	?>
	jQuery('#filter_field').val('');
	jQuery('#filter_searchphrases').val('');
	
	jQuery('#adminForm').submit();
}
</script>
<div>
<?php

$toolbar = JToolBar::getInstance('toolbar-addon');
echo $toolbar->render();

$totFields = count($this->fieldView)+3; //+1 checkbox +1 detail +1 Id lucene
echo 'Report '.JText::_('index').':';
?>
<ul>
	<li><?php echo JText::_('TOTAL_FILES_INCLUDED').': <span class="badge badge-info">'.$this->count.'</span>'; ?></li>
	<li><?php echo JText::_('TOTAL_FILES_INDEXED').': <span class="badge badge-info">'.$this->numDocs.'</span>'; ?></li>
	<li><?php echo JText::_('TOTAL_DELETED_FILES').': <span class="badge badge-info">'.$this->numDelete.'</span>'; ?></li>
	<li><?php 
			$optimize = ($this->optimize) ? '<span class="ok label label-success">'.JText::_( 'NOT_NECESSARY' ).'</span>' : '<span class="nook label label-warning">'.JText::_( 'Necessary' ).'</span>';
			echo JText::_('Optimization').': '.$optimize; 
		?>
	</li>
</ul>
</div>
<form class="form-inline" action="<?php echo JRoute::_('index.php?option=com_jifile'); ?>" method="post" name="adminForm" id="adminForm">
	<input name="invioform" value="" type="hidden"/>
	<!--table class="table table-striped" border="1"-->
	<table>
		<tr>
			<td nowrap="nowrap" width="2%">
				<label for="search"><?php echo JText::_( 'JSEARCH_FILTER_LABEL' ); ?></label>
				<?php echo $this->listFilter['fields']; ?>
			</td>
			<td width="2%">
				<input type="text" name="search" id="search" value="<?php echo $this->listFilter['search']; ?>" size="50" class="text_area" />
			</td>
			<td width="2%">
				<?php echo $this->listFilter['searchphrase']; ?>
			</td>
			<?php if (!empty($this->pluginLucene)) : ?>
				<td align="right">
					<table>
						<tr style="width:100%;float:right;">
							
							<?php foreach ($this->pluginLucene as $plugin) { ?>
								<td>
									<?php $plugin->printFilter() ?>
								</td>
							<?php } ?>
						</tr>
					</table>
				</td>
			<?php else : ?>
				<td width="50%">&nbsp;</td>
			<?php endif ?>
			<?php 
			/*
			<td width="2%">
				<div class="btn-group">
					<button class="btn" onclick="resetForm(this, 'order');this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
					<button class="btn" onclick="resetForm(this);"><?php echo JText::_( 'Reset' ); ?></button>
				</div>
			</td>
			*/
			?>		
		</tr>
		
	</table>
	<table>
		<tr>
			<td>
				<div class="btn-group">
					<button class="btn" onclick="resetForm(this, 'order');this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
					<button class="btn" onclick="resetForm(this);"><?php echo JText::_( 'Reset' ); ?></button>
				</div>
			</td>
		</tr>
	</table>
	
	
<?php if (!empty($this->lucene)) : ?>
	<table id="ifile" class="adminlist table table-hover table-striped">
		<thead>
			<tr>
				<th width="2%" class="title">
					<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this)" />
				</th>
				<th width="2%">Id</th>
				<?php if(!empty($this->listFilter['search'])) { $totFields++; ?>
					<th width="2%">score</th>
				<?php 
				}
					foreach ($this->fieldView as $fieldname) {
						if(empty($this->listFilter['search'])) {
							echo '<th>'.JText::_($fieldname).'</th>';
						} else {
							echo '<th>'.JHTML::_('grid.sort',  JText::_($fieldname), $fieldname, $this->listFilter['order_Dir'], $this->listFilter['order'] ).'</th>';
						}
					}
				?>
				<th width="2%"><?php echo JText::_( 'Detail' ); ?></th>
				<?php if (!empty($this->pluginLucene)) : ?>
					<th style="text-align:center;"><?php echo JText::_( 'JIFILE_ACTION' ); ?></th>
				<?php endif ?>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="<?php echo ($totFields+count($this->pluginLucene)) ?>"><?php echo $this->pagination->getListFooter(); ?></td>
			</tr>
		</tfoot>
		<tbody>
		<?php 
		$i = 0;
		foreach ($this->lucene as $key => $doc) : 

				$keyId = $doc['doc']->getFieldValue("key");
		
				if (array_key_exists($keyId, $this->luceneFilters)) {
					//$i++;
					continue;
				}
				?>
					<tr class="<?php echo 'row'.($i%2); $i++; ?>">
						<td style="text-align:center">
						<?php if(!isset($doc['isDeleted'])) { ?>
							<?php /* <input type="checkbox" id="cb<?php echo $i-1; ?>_<?php echo $key; ?>" name="indexId[]" value="<?php echo $key ?>" onclick="isChecked(this.checked);" />  */ ?>
							<input type="checkbox" id="cb<?php echo $key; ?>" name="indexId[]" value="<?php echo $key.'|'.$keyId; ?>" onclick="Joomla.isChecked(this.checked);" />
						<?php } else { ?>
							<img class="detail" src="components/com_jifile/images/icon-16-trash.png" alt="<?php echo JText::_( 'deleted' ); ?>" title="<?php echo JText::_( 'deleted' ); ?>" />
						<?php } ?>
						</td>
						<td style="text-align:center"><?php echo $key ?></td>
						<?php if(isset($doc['score'])) { 
							$bgScore = 100-($doc['score']*100);
							$color = ($bgScore>65) ? '#000000' : '#FFFFFF';
						?>
						<td style="background-color: hsl(0, 0%, <?php echo $bgScore ?>%); color: <?php echo $color ?>"><?php echo $doc['score']; ?></td>
						<?php } ?>						
				<?php foreach ($this->fieldView as $fieldname) { ?>
					<td>
					<?php 
						try {
							if($fieldname == 'name') {
								$filename = jifilehelper::getCorrectFilename($doc['doc']->getFieldValue('path'));
								//$filename = $doc['doc']->getFieldValue('path');
								/*$pathFilename = $doc['doc']->getFieldValue('filename');
								$filename = '';
								if($pos = strpos($pathFilename, $this->basepath)) {
									$filename = substr($pathFilename, $pos+strlen($this->basepath));
								}*/
								$name = jifilehelper::encodingCharset($doc['doc']->getFieldValue($fieldname));
								if($filename) {
									echo '<a title="Downlaod" href="index.php?option=com_jifile&task=filesystem.download&filename='.urlencode($filename).'">'.$name.'</a>';
								} else {
									echo $name.' <i>('.JText::_('FILE_NOT_FOUND').')</i>';
								}
							} else {
								$field = $doc['doc']->getFieldValue($fieldname);
								echo JFilterOutput::cleanText($field);
							}
						} catch (Zend_Search_Lucene_Exception $e) {
							echo '';
						}
					?>
					</td>
			<?php 
				} // close foreach
			?>
					<td style="text-align:center">
					<?php if(!isset($doc['isDeleted'])) { ?>
						<a rel="modalx" href="index.php?option=com_jifile&task=lucene.display&view=filedetail&tmpl=component&id=<?php echo $key ?>">
							<img class="detail" src="components/com_jifile/images/expandall.png" alt="<?php echo JText::_( 'Detail' ); ?>" title="<?php echo JText::_( 'Detail' ); ?>" />
						</a>
					<?php } else { ?>
							<img src="components/com_jifile/images/icon-16-trash.png" alt="<?php echo JText::_( 'deleted' ); ?>" title="<?php echo JText::_( 'deleted' ); ?>" />
					<?php } ?>
					</td>

					<?php if (!empty($this->pluginLucene)) : ?>
					 	<td>
					 		<?php
								foreach ($this->pluginLucene as $plugin) {
									$plugin->printAction($key, $doc);
									echo '&nbsp;&nbsp;&nbsp;';
								} 
							?>
						</td>
					<?php endif ?>

				</tr>				
		<?php endforeach; ?>		
		</tbody>
	</table>
	
	
	
<?php elseif(!empty($this->listFilter['search'])) : ?>
	<?php echo JText::_( 'NO_FILES' ); ?>
<?php endif; ?>

	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->listFilter['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->listFilter['order_Dir']; ?>" />
	<input type="hidden" name="task" value="lucene." />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
<?php } //nessun path configurato ?>
<?php echo jifilehelper::getFirma(); ?>