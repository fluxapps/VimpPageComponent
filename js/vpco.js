VimpPageComponent = {
	overwriteResetButton: function(name, url) {
		console.log('ok');
		$('input[name="cmd[resetFilter]"]').replaceWith('<a class="btn btn-default" href="' + url + '">' + name + '</a>');
	}
}