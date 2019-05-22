function onClickTest() {
	var par = 'host=' + $('#ldapHost').val() + '&dn=';
	par += $('#ldapBindDN').val().replace(/\=/g,"%3D");
	par += '&pwd=' + $('#ldapPassword').val().replace(/\=/g,"%3D");

	$.get(createLink('ldap', 'test', par), function(data) {
		$('#testRlt').html(data);
    });
}

function sync() {
	$.get(createLink('ldap', 'sync'), function(ret){
		alert("同步了"+ret+"位用户信息");
	});
}