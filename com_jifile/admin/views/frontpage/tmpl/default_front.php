<?php 
/**
* @subpackage	com_jifile
* @author		Antonio Di Girolamo & Giampaolo Losito
* @copyright	Copyright (C) 2011 isApp.it. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @link		http://jifile.isapp.it
*/
defined('_JEXEC') or die('Restricted access'); ?>

<div class="row-fluid" id="jifileFront">
<!-- Start Management Addon -->
<?php
	// chunk addon array in tree element for array
	$addons = (array_chunk($this->addon, 3));

	foreach ($addons as $addon) {
		foreach ($addon as $values) {
			// define access (ACL) 
			if (empty($values['rules']) || $this->canDo->get($values['rules'])) {
				list($context, $id) = explode(".", $values['addon']);
				// after tree box left, create un clear
					echo "\n<div class=\"icon span1\">";
						// management type of link (component - external link)
						switch ($values['type']) {
							case '0': // component Joomla!												
								// create href
								$strHref = $this->getHrefAddon($values);								
								
								echo "<a id=\"{$id}\" href=\"index.php?{$strHref}\" title=".JText::_($values['title'])." ";
								// define onclick
								if (!empty($values['onclick'])) {
									echo " onclick=\"".$values['onclick']."\"";
								}
								echo " >"; // close anchor
								break;
							case '1': // external link
								echo "<a id=\"{$id}\" href=\"".$values['link']."\"";
								if (!empty($values['target'])) {
									echo "target=\"".$values['target']."\"";
								}
								// define onclick
								if (!empty($values['onclick'])) {
									echo " onclick=\"".$values['onclick']."\"";
								}
								echo " >"; // close anchor
								break;
							default:
								// @TODO define message												
								break;
						}
						
						// if image exists 
						if (!empty($values['image'])) {
							echo '<img id="'.$id.'" src="'.$values['image'].'" alt="'.JText::_($values['title']).'" />';	
						} 
						
						echo "<br />";
						echo "<span>".JText::_($values['title'])."</span>";
						echo "</a>";
					echo "</div>";
			} // end if rules
		} // end foreach addon
	} // end foreach addons
?>
<!-- End management Addon -->
</div>