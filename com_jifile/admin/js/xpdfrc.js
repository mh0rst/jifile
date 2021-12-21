/**
* @subpackage	com_jifile
* @author		Antonio Di Girolamo & Giampaolo Losito
* @copyright	Copyright (C) 2011 isApp.it. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @link		http://jifile.isapp.it
*/
function writexpdfrc () {
	
	this.pw = null;
	
	this.init = function() {
        this.setPW();
    };
    this.setPW = function() {
        this.pw = (window.opener == null) ? window.parent : window;
    };
	
	this.onComplete = function(data) {
		
		if(data.error == '1') {			
			jQuery('#result').addClass('error');
			img = '<div class="result_img error_img" />';
		} else {
			jQuery('#sendxpdfrc').attr("disabled","disabled");
			jQuery('#result').addClass('success');
			img = '<div class="result_img success_img" />';
		}
		jQuery('#result').html(data.message+img);
	};
	
	this.init();
}

var objwritexpdfrc = new writexpdfrc();

jQuery(document).ready(function() {
	jQuery('#sendxpdfrc').click(function () {
		jQuery.post('index.php?option=com_jifile', jQuery('#form-xpdfrc').serialize(),
		function(data){
			objwritexpdfrc.onComplete(data);
		}, 'json');
	});
});