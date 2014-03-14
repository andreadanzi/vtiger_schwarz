function return_account_from_popup(recordid,value,target_fieldname,account_city,account_phone) {
	var form = window.opener.document.EditView;
	if (form) {
		var domnode_id = form.elements[target_fieldname];
		var domnode_display = form.elements[target_fieldname+'_display'];
		if(domnode_id) domnode_id.value = recordid;
		if(domnode_display) domnode_display.value = value;
		if (form.elements['account_city']) {
			form.elements['account_city'].value = account_city;
		}
		if (form.elements['account_phone']) {
			form.elements['account_phone'].value = account_phone;
		}
		return true;
	} else {
		return false;
	}
}