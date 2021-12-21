/**
* @subpackage	com_jifile
* @author		Antonio Di Girolamo & Giampaolo Losito
* @copyright	Copyright (C) 2011 isApp.it. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @link		http://jifile.isapp.it
*/

//on dom ready...
jQuery(document).ready(function() {
    _pw = window;
    aform = _pw.document.adminForm;

    jQuery.ajax({
		type: 'POST',
		url: 'index.php?option=com_jifile&task=filesystem.getFile2index',
		data: jQuery(aform).serialize(),
		success: function(data) {
			jQuery.each(data, function(index) {
	            alert(data[index]);
			});
		},
		dataType: 'json'
	});
});