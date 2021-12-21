/**
* @subpackage	com_jifile
* @author		Antonio Di Girolamo & Giampaolo Losito
* @copyright	Copyright (C) 2011 isApp.it. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @link		http://jifile.isapp.it
*/
function addFields() {
	
	this.arrayField = {
			'filename': '1', 
			'title': '1',
			'subject': '1',
			'description': '1',
			'creator': '1',
			'author': '1',
			'keywords': '1',
			'created': '1',
			'modified': '1',
			'body': '1'
	};
	this.arrayFieldIfile = {
			'key': '1',
			'name': '1',
			'extensionfile': '1',
			'path': '1',
			'introtext': '1',
			'class': '1'
	}
	this.pw = null;
	this.string = new Array();
	this.addFieldHtml = null;
	
	this.init = function() {
        this.setPW();
    }
	this.setPW = function() {
		this.pw = (window.opener == null) ? window.parent : window.opener;
    }
	this.setAddFieldHtml = function(htmlElement) {
		this.addFieldHtml = htmlElement;
	}
	this.getAddFieldHtml = function() {
		return this.addFieldHtml;
	}
	this.loading = function(act, error) {
		if(act == 'start') {
			jQuery('#send').attr("disabled","disabled");
			jQuery('#ifileOverlay').show();
			jQuery('#ifileLoaderOverlay').show();
			jQuery('#ifileLoader').show();
		} else if(act == 'end') {
			jQuery('#ifileOverlay').hide();
			jQuery('#ifileLoaderOverlay').hide();
			jQuery('#ifileLoader').hide();
			
			if(error){
				jQuery('#send').removeAttr("disabled");
			} else {
				jQuery('#close').html(this.string['close']);
				setTimeout('objAddField.pw.jQuery.colorbox.close()', 2500);
			}
		}
	}
	this.loadString = function(string) {
		this.string = string;
	}
	this.onComplete = function(data) {
		if(data.result == 'false') {
			this.loading('end', true);
			jQuery('#result').addClass('error');
			img = '<div class="result_img error_img" title="'+this.string['error']+'"/>';
		} else {
			this.loading('end', false);
			jQuery('#result').addClass('success');
			img = '<div class="result_img success_img" title="'+this.string['success']+'"/>';
			
			this.pw.startCheck(data.i, true);
		}
		jQuery('#result').html(data.message+img);
	}
	this.addField = function() {
		nome = jQuery('#tmp_name').val();
		tipo = jQuery('#tmp_typefield').val();
		jQuery('#msgErr').html('').removeClass('error alert alert-error success alert alert-success');
		
		if(nome == '') {
			jQuery('#msgErr').html(this.string['field_required']).addClass('error alert alert-error');
		} else if(this.arrayFieldIfile[nome]) {
			jQuery('#msgErr').html(this.string['reserved_field']).addClass('error alert alert-error');
		} else if(this.arrayField[nome]) {
			jQuery('#msgErr').html(this.string['already_exists']).addClass('error alert alert-error');
		} else {
			tr = jQuery(document.createElement('tr')).attr({id: 'tr_'+nome, "class": 'contentAddField'});
			tdLabel = jQuery(document.createElement('td')).attr({width: '120'});
			tdInput = jQuery(document.createElement('td'));
			label 	= jQuery(document.createElement('label')).attr({"for": nome}).text(nome);
			input 	= jQuery(document.createElement('input')).attr({id: nome, type: 'text', name: 'fields[add]['+nome+'|@@|'+tipo+']', size: '40', value: ''});
			button 	= jQuery(document.createElement('img')).attr({src:'../administrator/components/com_jifile/images/remove.png', "class": "remove", title:this.string['remove'], alt:this.string['remove'], onClick: "removeField('"+nome+"')"});
			tdLabel.append(label);
			tdInput.append(input).append(button);
			tr.append(tdLabel).append(tdInput);
			jQuery('.contentAddField').last().after(tr);
			//tr.inject('contentAddField', 'after');
			jQuery('#msgErr').html(this.string['success']).addClass('success alert alert-success');
			this.arrayField[nome] = tr;
		}
		jQuery('#tmp_name').val('');
		jQuery('#tmp_name').focus();
	}
	this.removeField = function(id) {
		if(this.arrayField[id]) {
			jQuery(("#tr_"+id)).remove();
			delete this.arrayField[id];
		}
	}
	
	this.init();
}

var objAddField = new addFields();

jQuery(document).ready(function() {
	
	jQuery('#openAddfield').click(function () {
		jQuery('#ifileOverlay').show('fast');
		jQuery('#addField').show('fast');
		jQuery('#ifileOverlay').animate({ opacity: 0.7 }, 800);
		jQuery('#tmp_name').focus();
	});
	
	jQuery('#ifileOverlay, #cboxClose').click(function () {
		jQuery('#ifileOverlay').hide('fast');
		jQuery('#addField').hide('fast');
	});
	
	jQuery('#send').click(function () {
		objAddField.loading('start', false);
		jQuery.post('index.php?option=com_jifile', jQuery('#adminFormManual').serialize(),
		function(data){
			objAddField.onComplete(data);
		}, 'json');
	});
});

function addField() {
	objAddField.addField();
}
function removeField(id) {
	objAddField.removeField(id);
}
