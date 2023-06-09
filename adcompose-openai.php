<?php
/*
Plugin Name: WordPress AdCompose OpenAI Generator
Plugin URI: https://github.com/ginirator/wordpress-plugin-adcompose-openai
Description: This plugin uses the OpenAI API to generate promotional copy for products.
Version: 1.0
Author: Valeriu Dodon
Author URI: https://ginirator.com/online-tools/adcompose-fast-promotional-copy-generator/
License: GPL2
*/

// Load scripts and styles for the plugin
function adcompose_openai_scripts() {
	// Enqueue CSS file for the plugin
	wp_enqueue_style('adcompose_styles', plugins_url('assets/css/styles.css', __FILE__));

	// Register the JavaScript file with WordPress's script handler
	wp_register_script('adcompose-openai-script', plugins_url('assets/js/scripts.js', __FILE__), array('jquery'), '1.0', true);

	// Localize the script with additional data
	wp_localize_script('adcompose-openai-script', 'adcompose_openai_object', array(
		'plugin_url' => plugins_url('', __FILE__),
		'ajax_url' => admin_url('admin-ajax.php')
	));

	// Enqueue the script to the front-end
	wp_enqueue_script('adcompose-openai-script');
}
add_action('wp_enqueue_scripts', 'adcompose_openai_scripts');

// Define the shortcode function for 'adcompose'
function adcompose_openai_form($atts = []) {
	// Normalize attribute keys, convert to lowercase
	$atts = array_change_key_case((array)$atts, CASE_LOWER);

	// Override default attributes with user attributes
	$adcompose_atts = shortcode_atts([
		'apikey' => '',
		'model' => 'text-davinci-003',
		'maxtokens' => 100,
	], $atts);

	// Error handling for missing API key
	if ( empty($adcompose_atts['apikey']) ) {
		error_log('Error: OpenAI API key not provided in the [adcompose] shortcode parameters.');
		echo '<p style="color: red;">Error: Please provide a valid OpenAI API key as a parameter in the shortcode.</p>';
	} else {
		// Store the attributes as an option
		update_option('adcompose_openai_atts', $adcompose_atts);

		// Begin output buffering
		ob_start();
		?>

		<!-- Begin form HTML -->
		<div id="adcompose-wrapper">
			<h3>Welcome to AdCompose</h3>
			<p>Get promotional copy for your products fast!</p>

			<form id="ad-input">
				<label for="name">Product Name:</label>
				<input type="text" id="productName" name="productName">
				<label for="desc">Product Description:</label>
				<input type="text" id="productDesc" name="productDesc">
				<label for="target">Target Market:</label>
				<input type="text" id="productTarget" name="productTarget">
				<button type="submit" id="submit-button">Generate Copy</button>
			</form>

			<div id="ad-output">
				<h3>Your Advertising Copy:</h3>
				<p id="ad-output-copy" class="generatedContent"></p>
			</div>
		</div>

		<div id="ad-output"></div>
		<!-- End form HTML -->

		<?php
		return ob_get_clean(); // End output buffering and return contents
	}
}
add_shortcode('adcompose', 'adcompose_openai_form'); // Register the shortcode

// Function to handle AJAX requests
function adcompose_openai_handle_request() {
	// Retrieve the stored attributes
	$adcompose_openai_global_atts = get_option('adcompose_openai_atts', array());
	$apiKey = $adcompose_openai_global_atts['apikey'];
	$apiModel = $adcompose_openai_global_atts['model'];
	$apiMaxTokens = intval($adcompose_openai_global_atts['maxtokens']);

	// If it's a POST request
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		// Retrieve form data
		$productName = $_POST['productName'];
		$productDesc = $_POST['productDesc'];
		$productTarget = $_POST['productTarget'];

		// Check for empty fields
		if (empty($productName) || empty($productDesc) || empty($productTarget)) {
			echo "Please fill all the fields";
			exit;
		}

		// Define the prompt for OpenAI
		$prompt = "Create max 50 words of an advertising copy for a product.
		Use Product Name \"{$productName}\",
		Description \"{$productDesc}\",
		Target Market \"{$productTarget}\"";

		// Prepare the data for the API
		$data = [
			'prompt' => $prompt,
			'max_tokens' => $apiMaxTokens,
		];

		// Initialize cURL
		$ch = curl_init();

		// Set cURL options
		curl_setopt($ch, CURLOPT_URL, 'https://api.openai.com/v1/engines/' . $apiModel . '/completions');
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			'Content-Type: application/json',
			'Authorization: Bearer ' . $apiKey
		]);

		// Execute cURL request and get the response
		$response = curl_exec($ch);

		// Check for errors
		if (curl_errno($ch)) {
			echo 'Error:' . curl_error($ch);
		}

		curl_close($ch); // Close the cURL session

		// Decode the response
		$responseData = json_decode($response, true);

		// Check if there is a text response
		if (isset($responseData['choices'][0]['text'])) {
			echo trim($responseData['choices'][0]['text']);
		} else {
			echo "There was a problem with the AI generation. Please try again.",var_dump($responseData);
		}

		exit;
	}
}
add_action('wp_ajax_nopriv_adcompose_openai_handle_request', 'adcompose_openai_handle_request'); // Handle AJAX request for non-authenticated users
add_action('wp_ajax_adcompose_openai_handle_request', 'adcompose_openai_handle_request'); // Handle AJAX request for authenticated users
