jQuery(document).ready(function ($) {
	console.log( adcompose_openai_object.plugin_url + '/assets/images/loading.gif' );
	$('#ad-input').submit(function(event) {
		event.preventDefault();

		const productName = $('#productName').val();
		const productDesc = $('#productDesc').val();
		const productTarget = $('#productTarget').val();

		if (!productName || !productDesc || !productTarget) {
			alert("Please fill all the fields");
			return;
		}

		$('#ad-input').html('<img src="' + adcompose_openai_object.plugin_url + '/assets/images/loading.gif" alt="Loading...">');


		$.ajax({
			url: adcompose_openai_object.ajax_url,
			method: 'POST',
			data: {
				action: 'adcompose_openai_handle_request',
				productName: productName,
				productDesc: productDesc,
				productTarget: productTarget
			},
			success: function(data) {
				$('#ad-input').attr('style', 'display: none;');
				$('#ad-output').attr('style', 'display: block;');
				$('#ad-output-copy').html(data);
			},
			error: function(jqXHR, textStatus, errorThrown) {
				$('#ad-output').html('There was an error: ' + errorThrown);
			}
		});
	});
});
