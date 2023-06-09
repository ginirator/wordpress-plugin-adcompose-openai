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

require_once __DIR__ . '/vendor/autoload.php';

function adcompose_openai_scripts() {
	// Use plugins_url() function to include your style file from your plugin.
    wp_enqueue_style('adcompose_styles', plugins_url('assets/css/styles.css', __FILE__));

    wp_register_script('adcompose-openai-script', plugins_url('assets/js/scripts.js', __FILE__), array('jquery'), '1.0', true);

    wp_localize_script('adcompose-openai-script', 'adcompose_openai_object', array(
		'plugin_url' => plugins_url('', __FILE__),
        'ajax_url' => admin_url('admin-ajax.php')
    ));

    wp_enqueue_script('adcompose-openai-script');
}
add_action('wp_enqueue_scripts', 'adcompose_openai_scripts');

function adcompose_openai_form() {
    ob_start();
    ?>

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

    <?php
    return ob_get_clean();
}
add_shortcode('adcompose', 'adcompose_openai_form');

function adcompose_openai_handle_request() {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
	$dotenv->load();

	$apiKey = $_ENV['OPENAI_API_KEY'];
	$apiModel = $_ENV['OPENAI_MODEL'];
	$apiMaxTokens = intval($_ENV['OPENAI_MAX_TOKENS']);

	if ($_SERVER["REQUEST_METHOD"] == "POST") {

		$productName = $_POST['productName'];
		$productDesc = $_POST['productDesc'];
		$productTarget = $_POST['productTarget'];

		if (empty($productName) || empty($productDesc) || empty($productTarget)) {
			echo "Please fill all the fields";
			exit;
		}

		$prompt = "Create max 50 words of an advertising copy for a product.
		Use Product Name \"{$productName}\",
		Description \"{$productDesc}\",
		Target Market \"{$productTarget}\"";

		$data = [
			'prompt' => $prompt,
			'max_tokens' => $apiMaxTokens,
		];

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, 'https://api.openai.com/v1/engines/' . $apiModel . '/completions');

		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			'Content-Type: application/json',
			'Authorization: Bearer ' . $apiKey
		]);

		$response = curl_exec($ch);

		if (curl_errno($ch)) {
			echo 'Error:' . curl_error($ch);
		}

		curl_close($ch);

		$responseData = json_decode($response, true);

		if (isset($responseData['choices'][0]['text'])) {
			echo trim($responseData['choices'][0]['text']);
		} else {
			echo "There was a problem with the AI generation. Please try again.",var_dump($responseData);
		}

		exit;
	}

}
add_action('wp_ajax_nopriv_adcompose_openai_handle_request', 'adcompose_openai_handle_request');
add_action('wp_ajax_adcompose_openai_handle_request', 'adcompose_openai_handle_request');
