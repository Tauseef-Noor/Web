<?php
namespace TrxAddons\AiHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Markdown\Parser\Parsedown;

/**
 * Class to make queries to the OpenAi API
 */
class GoogleAi extends Singleton {

	/**
	 * The object to log queries to the API
	 *
	 * @access private
	 * 
	 * @var Logger  The object to log queries to the API
	 */
	var $logger = null;
	var $logger_section = 'google-ai';

	/**
	 * The object of the API
	 *
	 * @access private
	 * 
	 * @var api  The object of the API
	 */
	var $api = null;

	/**
	 * Plugin constructor.
	 *
	 * @access protected
	 */
	protected function __construct() {
		parent::__construct();
		$this->logger = Logger::instance();
	}

	/**
	 * Return an object of the API
	 * 
	 * @param string $token  API token for the API
	 * 
	 * @return api  The object of the API
	 */
	public function get_api( $token = '' ) {
		if ( empty( $this->api ) ) {
			if ( empty( $token ) ) {
				$token = $this->get_token();
			}
			if ( ! empty( $token ) ) {
				$this->api = new \ThemeRex\GoogleAi\Gemini( $token );
				$proxy = trx_addons_get_option( 'ai_helper_proxy_google_ai', '' );
				if ( ! empty( $proxy ) ) {
					$this->api->setProxy( $proxy );
				}
			}
		}
		return $this->api;
	}

	/**
	 * Return an API token for the API from the plugin options.
	 * This method is a wrapper for the get_token() method to allow to override it in the child classes.
	 * 
	 * @access public
	 * 
	 * @return string  API key for the API
	 */
	public function get_api_key() {
		return $this->get_token();
	}

	/**
	 * Return an API token for the API from the plugin options
	 * 
	 * @access protected
	 * 
	 * @return string  API token for the API
	 */
	protected function get_token() {
		return trx_addons_get_option( 'ai_helper_token_google_ai', '' );
	}

	/**
	 * Return a model name for the API
	 * 
	 * @access static
	 * 
	 * 
	 * @return string  Model name for the API
	 */
	static function get_model() {
		$default_model = trx_addons_get_option( 'ai_helper_text_model_default', 'google-ai/gemini-pro' );
		return Utils::is_google_ai_model( $default_model ) ? $default_model : 'google-ai/gemini-pro';
	}

	/**
	 * Return a maximum number of tokens in the prompt and response for specified model or from all available models
	 *
	 * @access static
	 * 
	 * @param string $model  Model name (flow id) for the API. If '*' - return a maximum value from all available models
	 * 
	 * @return int  The maximum number of tokens in the prompt and response for specified model or from all models
	 */
	static function get_max_tokens( $model = '' ) {
		$max_tokens = 0;
		if ( ! empty( $model ) ) {
			$model = str_replace( 'google-ai/', '', $model );
			$models = Lists::get_google_ai_chat_models();
			if ( ! empty( $models ) && is_array( $models ) ) {
				foreach ( $models as $k => $v ) {
					if ( $model == '*' ) {
						$max_tokens = max( $max_tokens, (int)$v['max_tokens'] );
					} else {
						if ( $k == $model ) {
							$max_tokens = (int)$v['max_tokens'];
							break;
						}
					}
				}
			}
		}
		return $max_tokens;
	}

	/**
	 * Return a list of available models for the API
	 *
	 * @access public
	 */
	public function list_models( $args = array() ) {
		$args = array_merge( array(
			'token' => $this->get_token(),
		), $args );

		$response = false;

		if ( ! empty( $args['token'] ) ) {
			$api = $this->get_api( $args['token'] );
			$response = $api->listModels();
			if ( is_array( $response ) && ! empty( $response['models'] ) ) {
				$response = $response['models'];
			} else {
				$response = false;
			}
		}

		return $response;
	}

	 /**
	 * Send a query to the API
	 *
	 * @access public
	 * 
	 * @param array $args  Query arguments
	 * 
	 * @return array  Response from the API
	 */
	public function query( $args = array(), $params = array() ) {
		$args = array_merge( array(
			'token' => $this->get_token(),
			'model' => $this->get_model(),
			'prompt' => '',
			// 'system_prompt' => '',
			// 'frequency_penalty' => 0,
			// 'presence_penalty' => 0,
		), $args );

		$args['max_tokens'] = ! empty( $args['max_tokens'] )
								? min( $args['max_tokens'], self::get_max_tokens( $args['model'] ) )
								: self::get_max_tokens( $args['model'] );

		$args['messages'] = array();
		if ( ! empty( $args['prompt'] ) ) {
			$args['messages'][] = array(
									'role' => 'user',
									'content' => $args['prompt']
								);
			unset( $args['prompt'] );
			unset( $args['role'] );
		}

		$response = false;

		if ( ! empty( $args['token'] ) && count( $args['messages'] ) > 0 ) {

			$args = $this->prepare_args( $args );

			if ( $args['max_tokens'] > 0 ) {

				$api = $this->get_api( $args['token'] );

				$response = $api->query( $args );
				if ( is_array( $response ) ) {
					$response = $this->prepare_response( $response, $args );
					$this->logger->log( $response, 'query', $args, $this->logger_section );
				} else {
					$response = false;
				}
			}
		}

		return $response;

	}

	/**
	 * Send a chat messages to the API
	 *
	 * @access public
	 * 
	 * @param array $args  Query arguments
	 * 
	 * @return array  Response from the API
	 */
	public function chat( $args = array(), $params = array() ) {
		$args = array_merge( array(
			'token' => $this->get_token(),
			'model' => $this->get_model(),
			'messages' => array(),
			// 'system_prompt' => '',
			// 'frequency_penalty' => 0,
			// 'presence_penalty' => 0,
		), $args );

		$args['max_tokens'] = ! empty( $args['max_tokens'] )
								? min( $args['max_tokens'], self::get_max_tokens( $args['model'] ) )
								: self::get_max_tokens( $args['model'] );

		$response = false;

		if ( ! empty( $args['token'] ) && ! empty( $args['model'] ) && count( $args['messages'] ) > 0 ) {
			$args = $this->prepare_args( $args );
			
			if ( $args['max_tokens'] > 0 ) {
				
				$api = $this->get_api( $args['token'] );

				$response = $api->query( $args );

				if ( is_array( $response ) ) {
					$response = $this->prepare_response( $response, $args );
					$this->logger->log( $response, 'chat', $args, $this->logger_section );
				} else {
					$response = false;
				}
			}
		}

		return $response;

	}

	/**
	 * Convert a response object to the format, compatible with OpenAI API response
	 */
	protected function prepare_response( $response, $args ) {
		if ( ! empty( $response['candidates'][0]['content']['parts'] ) && is_array( $response['candidates'][0]['content']['parts'] ) ) {
			// Combine all parts of the response to one text
			$text = '';
			foreach ( $response['candidates'][0]['content']['parts'] as $part ) {
				if ( ! empty( $part['text'] ) ) {
					$text .= "\n" . $part['text'];
				}
			}
			// Parse the markdown
			if ( ! empty( $text ) ) {
				$parser = new Parsedown();
				$text = $parser->text(  $text );
			}
			// Count tokens
			$prompt_tokens = ! empty( $args['contents'] ) && ! empty( $args['contents'][ count( $args['contents'] ) - 1 ]['parts'][0]['text'] )
								? $this->count_tokens( $args['contents'][ count( $args['contents'] ) - 1 ]['parts'][0]['text'] )
								: 0;
			$completion_tokens = $this->count_tokens( $text );
			// Prepare the response
			$response = array(
				'finish_reason' => 'stop',
				'model' => ! empty( $args['model'] ) ? $args['model'] : __( 'Google Gemini', 'trx_addons' ),
				'usage' => array(
							'prompt_tokens' => $prompt_tokens,
							'completion_tokens' => $completion_tokens,
							'total_tokens' => $prompt_tokens + $completion_tokens,
							),
				'choices' => array(
								array(
									'message' => array(
										'content' => $text,
									)
								)
							)
			);
		}
		return $response;
	}

	/**
	 * Prepare args for the API: limit the number of tokens
	 *
	 * @access private
	 * 
	 * @param array $args  Query arguments
	 * 
	 * @return array  Prepared query arguments
	 */
	private function prepare_args( $args = array() ) {
		if ( ! empty( $args['messages'] ) && is_array( $args['messages'] ) ) {
			$tokens_total = 0;
			$args['contents'] = array();
			foreach ( $args['messages'] as $k => $message ) {
				// If it's a first message - add a system prompt to the message
				if ( ! empty( $args['system_prompt'] ) ) {
					if ( count( $args['messages'] ) == 1 ) {
						$message['content'] = $args['system_prompt'] . "\n" . $message['content'];
					}
				}
				// Remove all HTML tags
				//$message['content'] = strip_tags( $message['content'] );
				// Remove duplicate newlines
				$message['content'] = preg_replace( '/[\\r\\n]{2,}/', "\n", $message['content'] );
				// Remove all Gutenberg block comments
				$message['content'] = preg_replace( '/<!--[^>]*-->/', '', $message['content'] );
				// Count tokens
				$tokens_total += $this->count_tokens( $message['content'] );
				// Save the message
				$args['messages'][ $k ]['content'] = $message['content'];
				// Save the message to the contents array
				$args['contents'][] = array(
					'role' => empty( $message['role'] ) || $message['role'] == 'user' ? 'user' : 'model',
					'parts' => array(
						array(
							'text' => $message['content']
						)
					)
				);
			}
			// Remove messages
			unset( $args['messages'] );
			// Remove a system prompt
			unset( $args['system_prompt'] );
			// Limit the number of tokens
			$args['max_tokens'] = max( 0, $args['max_tokens'] - $tokens_total );
			// Add 'generationConfig' to the args
			$args['generationConfig'] = array();
			if ( ! empty( $args['max_tokens'] ) ) {
				$args['generationConfig']['maxOutputTokens'] = $args['max_tokens'];
			}
			if ( ! empty( $args['temperature'] ) ) {
				$args['generationConfig']['temperature'] = $args['temperature'];
				unset( $args['temperature'] );
			}
			if ( ! empty( $args['top_p'] ) ) {
				$args['generationConfig']['topP'] = $args['top_p'];
				unset( $args['top_p'] );
			}
			if ( ! empty( $args['top_k'] ) ) {
				$args['generationConfig']['topK'] = $args['top_k'];
				unset( $args['top_k'] );
			}
		}
		// Remove a prefix 'google-ai/' from the model name
		if ( ! empty( $args['model'] ) ) {
			$args['model'] = str_replace( 'google-ai/', '', $args['model'] );
		}
		return $args;
	}

	/**
	 * Calculate the number of tokens for the API
	 * 
	 * @access private
	 * 
	 * @param string $text  Text to calculate
	 * 
	 * @return int  Number of tokens for the API
	 */
	private function count_tokens( $text ) {
		$tokens = 0;

		// Way 1: Get number of words and multiply by coefficient		
		// $words = count( explode( ' ', $text ) );
		// $coeff = strpos( $text, '<!-- wp:' ) !== false ? $this->blocks_to_tokens_coeff : $this->words_to_tokens_coeff;
		// $tokens = round( $words * $coeff );

		// Way 2: Get number of tokens via utility function with tokenizer
		// if ( ! function_exists( 'gpt_encode' ) ) {
		// 	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/vendors/gpt3-encoder/gpt3-encoder.php';
		// }
		// $tokens = count( (array) gpt_encode( $text ) );

		// Way 3: Get number of tokens via class tokenizer (same algorithm)
		$tokens = count( (array) \Rahul900day\Gpt3Encoder\Encoder::encode( $text ) );

		return $tokens;
	}

}
