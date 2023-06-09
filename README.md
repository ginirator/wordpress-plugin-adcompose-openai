# AdCompose OpenAI

This is a WordPress plugin that uses the OpenAI API to generate promotional copy for products.

## Installation

1. Download the plugin and install it through the WordPress plugins screen.
2. Activate the plugin.
3. Use the `[adcompose apikey="your-openai-api-key" model="text-davinci-003" maxtokens=100]` shortcode to display the form on your site.

Here's a general list of the OpenAI `models` available:

1. `text-davinci-003` - The largest and most capable model available, known for generating creative, nuanced and contextually rich content. It is good for tasks that require deeper understanding or creativity.

2. `text-curie-003` - A smaller model than Davinci but still highly capable. Curie is good for almost any English language task.

3. `text-babbage-003` - A smaller model still, useful for answering questions, writing emails, writing Python code, creating conversational agents, translating languages, simulating characters for video games, and tutoring.

4. `text-ada-003` - The smallest model, useful for similar tasks but where cost and speed are prioritized over nuanced outputs.

The model names can change in future versions, as OpenAI continues to release new models and updates.

About `maxtokens`: This parameter controls the maximum length of the output from the model. The value you set determines how many tokens (which can be as short as one character or as long as one word in English) the model will generate. You might set this to a higher value for longer pieces of text, or a lower value for shorter, more concise outputs.

Please note that generating more tokens consumes more resources and will cost more if you're using a paid API key. Also, note that very long requests may result in timeouts or other errors.

In the context of your application, a maxtokens value of `100` would generally be a good fit, but you may want to adjust it depending on your specific needs.

## Usage

1. Enter the product name, description, and target market in the form.
2. Click the "Generate Copy" button to generate promotional copy for the product.
