// On document ready
jQuery(document).ready(function ($) {

	// Log the URL of the loading.gif in the console
	console.log(adcompose_openai_object.plugin_url + '/assets/images/loading.gif');

	// Attach an event handler to the 'submit' event of the form with id 'ad-input'
	$('#ad-input').submit(function (event) {

		// Prevent the form from submitting the default way
		event.preventDefault();

		// Get the values from the form inputs
		const productName = $('#productName').val();
		const productDesc = $('#productDesc').val();
		const productTarget = $('#productTarget').val();

		// Check if all fields have been filled
		if (!productName || !productDesc || !productTarget) {

			// Alert the user if a field is missing
			alert("Please fill all the fields");
			return;
		}

		// Replace the form content with a loading gif while processing
		$('#ad-input').html('<img src="' + adcompose_openai_object.plugin_url + '/assets/images/loading.gif" alt="Loading...">');

		// Make an AJAX request to the server
		$.ajax({
			// Define the URL for the AJAX request
			url: adcompose_openai_object.ajax_url,

			// Set the HTTP method to POST
			method: 'POST',

			// Define the data to be sent in the AJAX request
			data: {
				action: 'adcompose_openai_handle_request',
				productName: productName,
				productDesc: productDesc,
				productTarget: productTarget,
			},

			// Define a function to handle a successful response
			success: function (data) {

				// Hide the input form
				$('#ad-input').attr('style', 'display: none;');

				// Show the output area
				$('#ad-output').attr('style', 'display: block;');

				// Fill the output area with the response data
				$('#ad-output-copy').html(data);
			},

			// Define a function to handle errors
			error: function (jqXHR, textStatus, errorThrown) {

				// Output the error in the ad-output div
				$('#ad-output').html('There was an error: ' + errorThrown);
			}
		});
	});
});
