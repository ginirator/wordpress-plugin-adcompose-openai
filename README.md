# AdCompose OpenAI

This is a WordPress plugin that uses the OpenAI API to generate promotional copy for products.

## Installation

1. Download the plugin and install it through the WordPress plugins screen.
2. Activate the plugin.
3. Use the `[adcompose apikey="your-api-key" model="text-davinci-003" maxtokens=100]` shortcode to display the form on your site.

## Usage

1. Enter the product name, description, and target market in the form.
2. Click the "Generate Copy" button to generate promotional copy for the product.

## Environment Configuration

For the plugin to work correctly, it requires an `.env` file in the plugin root directory to store environment-specific variables. This includes the OpenAI `API Key`, `model`, and `maximum tokens`.

Create a `.env` file in the root directory of the plugin with the following content:

```
OPENAI_API_KEY="your-api-key"
OPENAI_MODEL="text-davinci-003"
OPENAI_MAX_TOKENS=100
```

Replace `your-api-key` with your actual OpenAI API key. The OPENAI_MODEL is set to `text-davinci-003` by default. You can use any other model supported by the OpenAI API. OPENAI_MAX_TOKENS specifies the maximum length of the text that the API will generate, and it's set to `100` by default.

Please ensure to keep this file secure and never expose these details publicly.
