<?php
namespace TrxAddons\AiHelper;

use TrxAddons\AiHelper\Utils;

if ( ! class_exists( 'Options' ) ) {

	/**
	 * Add options to the ThemeREX Addons Options
	 */
	class Options {

		/**
		 * Constructor
		 */
		function __construct() {
			add_filter( 'trx_addons_filter_options', array( $this, 'add_options' ) );
			add_filter( 'trx_addons_filter_before_show_options', array( $this, 'fill_options' ) );
			add_filter( 'trx_addons_filter_before_show_options', array( $this, 'fix_options' ) );
			add_filter( 'trx_addons_filter_export_options', array( $this, 'remove_token_from_export' ) );
		}

		/**
		 * Add options to the ThemeREX Addons Options
		 * 
		 * @hooked trx_addons_filter_options
		 *
		 * @param array $options  Array of options
		 * 
		 * @return array  	  Modified array of options
		 */
		function add_options( $options ) {
			$is_options_page = trx_addons_get_value_gp( 'page' ) == 'trx_addons_options';

			// Get logs for the AI Helper
			$log_open_ai = $is_options_page ? Logger::instance()->get_log_report( 'open-ai') : '';
			$log_open_ai_assistants = $is_options_page ? Logger::instance()->get_log_report( 'open-ai-assistants') : '';
			$log_sd = $is_options_page ? Logger::instance()->get_log_report( 'stabble-diffusion') : '';
			$log_stability_ai = $is_options_page ? Logger::instance()->get_log_report( 'stability-ai') : '';
			$log_flowise_ai = $is_options_page ? Logger::instance()->get_log_report( 'flowise-ai') : '';
			$log_google_ai = $is_options_page ? Logger::instance()->get_log_report( 'google-ai') : '';

			// Create the object of the StableDiffusion API and set the API server (will be used in the options and when generating images)
			$sd_api_url = '';
			$sd_models_url = '';
			if ( $is_options_page ) {
				$sd_api = StableDiffusion::instance();
				$sd_api->set_api( trx_addons_get_option( 'ai_helper_use_api_stabble_diffusion', 'sd' ) );
				$sd_api_url = $sd_api->get_url( 'settings/api' );
				$sd_models_url = $sd_api->get_url( 'models' );
			}

			trx_addons_array_insert_before( $options, 'users_section', apply_filters( 'trx_addons_filter_options_ai_helper', array(

				// Open panel "AI Helper"
				'ai_helper_section' => array(
					"title" => esc_html__('AI Helper', 'trx_addons'),
					'icon' => 'trx_addons_icon-android',
					"type" => "panel"
				),

				// Common settings
				'ai_helper_section_common' => array(
					"title" => esc_html__('Common settings', 'trx_addons'),
					"icon" => 'trx_addons_icon-tools',
					"type" => "section"
				),
				'ai_helper_trx_ai_assistants_info' => array(
					"title" => esc_html__('AI Assistant', 'trx_addons'),
					"desc" => wp_kses_data( __("AI Assistant display settings in the admin area, as well as extending the support period for using AI Assistant.", 'trx_addons') ),
					"type" => "info"
				),
				'ai_helper_trx_ai_assistants' => array(
					"title" => esc_html__('Allow AI Assistant', 'trx_addons'),
					"desc" => wp_kses_data( __('Allow the display of an intelligent assistant in the admin area that can display and change some theme settings, as well as answer questions related to theme customization.', 'trx_addons') ),
					"std" => "1",
					"type" => "switch",
				),
				'ai_helper_trx_ai_assistants_add_support' => array(
					"title" => esc_html__('Extend support', 'trx_addons'),
					"desc" => wp_kses_data( __('Extend the support period for using AI Assistant.', 'trx_addons') ),
					"caption" => esc_html__('Add a new support key', 'trx_addons'),
					"icon" => 'trx_addons_icon-key',
					"std" => "",
					"callback" => "trx_addons_ai_assistant_add_support",
					"type" => "button",
				),
				'ai_helper_common_info' => array(
					"title" => esc_html__('Common settings', 'trx_addons'),
					"desc" => wp_kses_data( __("Default model for text generations, settings for a selected text processing, etc.", 'trx_addons') ),
					"type" => "info"
				),
				'ai_helper_text_model_default' => array(
					"title" => esc_html__('Default text model', 'trx_addons'),
					"desc" => wp_kses_data( __('Select a text model to use as default for AI actions such as translation, process selected text, etc.', 'trx_addons') )
							. '<br />'
							. wp_kses_data( __("Attention! If the list of models is empty - it means that you have not connected any API for text generation. You need to specify an access token for at least one of the supported APIs - Open AI (preferably), Google AI or Flowise AI.", 'trx_addons') ),
					"std" => "",
					"options" => apply_filters( 'trx_addons_filter_ai_helper_list_models', array_merge( array( '' => __( '- Not selected -', 'trx_addons' ) ), $is_options_page ? Lists::get_list_ai_text_models() : array() ), 'text_model' ),
					"type" => "select",
				),
				// Leave name 'ai_helper_system_prompt_openai' for compatibility with old versions
				'ai_helper_system_prompt_openai' => array(
					"title" => esc_html__('System Prompt', 'trx_addons'),
					"desc" => wp_kses_data( __('System instructions for the AI Helper in the post editor. Serve as a guide for choosing the style of communication on the part of the AI.', 'trx_addons') ),
					"std" => __( 'You are an assistant for writing posts. Return only the result without any additional messages. Format the response with HTML tags.', 'trx_addons' ),
					"type" => "textarea",
					'dependency' => array(
						'ai_helper_text_model_default' => array( 'not_empty' )
					)
				),
				'ai_helper_process_selected_info' => array(
					"title" => esc_html__('Process selected text', 'trx_addons'),
					"desc" => wp_kses_data( __("Select post types and URL masks (optional) to add functionality to process (explain, summarize, translate) selected text", 'trx_addons') ),
					"type" => "info",
					'dependency' => array(
						'ai_helper_text_model_default' => array( 'not_empty' )
					)
				),
				'ai_helper_process_selected' => array(
					"title" => esc_html__('Process selected text', 'trx_addons'),
					"desc" => wp_kses_data( __('Add functionality to process (explain, summarize, translate) selected text for specified post types and/or URLs.', 'trx_addons') ),
					"std" => "1",
					"type" => "switch",
					"dependency" => array(
						'ai_helper_text_model_default' => array( 'not_empty' )
					),
				),
				'ai_helper_process_selected_post_types' => array(
					"title" => esc_html__("Post types", 'trx_addons'),
					"desc" => '',
					"dir" => 'horizontal',
					"std" => array( 'post' => 1 ),
					"options" => array(),
					"type" => "checklist",
					'dependency' => array(
						'ai_helper_process_selected' => array( '1' ),
						'ai_helper_text_model_default' => array( 'not_empty' )
					)
				),
				"ai_helper_process_selected_url_include" => array(
					"title" => esc_html__("URL include", 'trx_addons'),
					"desc" => wp_kses_data( __("URL fragments listed comma-separated or on a new line, matching with which the 'Process selected text' functionality will be enabled.", 'trx_addons') ),
					"std" => "",
					"rows" => 10,
					"type" => "textarea",
					'dependency' => array(
						'ai_helper_process_selected' => array( '1' ),
						'ai_helper_text_model_default' => array( 'not_empty' )
					)
				),
				"ai_helper_process_selected_url_exclude" => array(
					"title" => esc_html__("URL exclude", 'trx_addons'),
					"desc" => wp_kses_data( __("URL fragments listed comma-separated or on a new line, matching with which the 'Process selected text' functionality will be disabled.", 'trx_addons') ),
					"std" => "",
					"rows" => 10,
					"type" => "textarea",
					'dependency' => array(
						'ai_helper_process_selected' => array( '1' ),
						'ai_helper_text_model_default' => array( 'not_empty' )
					)
				),

				// Open AI API settings
				'ai_helper_section_openai' => array(
					"title" => esc_html__('Open AI API', 'trx_addons'),
					"icon" => TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/images/icons/openai.svg',
					"type" => "section"
				),
				'ai_helper_info_openai' => array(
					"title" => esc_html__('Open AI', 'trx_addons'),
					"desc" => wp_kses_data( __("Settings of the AI Helper for Open AI API", 'trx_addons') )
							. ( ! empty( $log_open_ai ) ? wp_kses( $log_open_ai, 'trx_addons_kses_content' ) : '' ),
					"type" => "info"
				),
				'ai_helper_token_openai' => array(
					"title" => esc_html__('Open AI token', 'trx_addons'),
					"desc" => wp_kses( sprintf(
													__('Specify a token to use the OpenAi API. You can generate a token in your personal account using the link %s', 'trx_addons'),
													apply_filters( 'trx_addons_filter_openai_api_key_url',
																	'<a href="' . esc_url( OpenAi::instance()->get_url( 'account/api-keys' ) ) . '" target="_blank">' . esc_url( OpenAi::instance()->get_url( 'account/api-keys' ) ) . '</a>'
																)
												),
										'trx_addons_kses_content'
									),
					"std" => "",
					"type" => "text"
				),
				'ai_helper_proxy_openai' => array(
					"title" => esc_html__('Proxy URL', 'trx_addons'),
					"desc" => wp_kses_data( __('Specify the address of the proxy-server (if need).', 'trx_addons') ),
					"std" => "",
					"type" => "text",
					"dependency" => array(
						"ai_helper_token_openai" => array('not_empty')
					),
				),
				// Deprecated option. Will be removed in the next version.
				// Default text model is added instead and a model selector is added to the shortcode and the post editor.
				/*
				'ai_helper_model_openai' => array(
					"title" => esc_html__('Open AI model', 'trx_addons'),
					"desc" => wp_kses_data( __('Select a text model to use with OpenAi API', 'trx_addons') ),
					"std" => "gpt-3.5-turbo",
					"options" => apply_filters( 'trx_addons_filter_ai_helper_list_models', $is_options_page ? Lists::get_list_openai_chat_models() : array(), 'openai' ),
					"type" => "select",
					"dependency" => array(
						"ai_helper_token_openai" => array('not_empty')
					),
				),
				*/
				'ai_helper_temperature_openai' => array(
					"title" => esc_html__('Temperature', 'trx_addons'),
					"desc" => wp_kses_data( __('Select a temperature to use with OpenAi API queries in the editor.', 'trx_addons') )
							. '<br />'
							. wp_kses_data( __('What sampling temperature to use, between 0 and 2. Higher values like 0.8 will make the output more random, while lower values like 0.2 will make it more focused and deterministic.', 'trx_addons') ),
					"std" => 1.0,
					"min" => 0,
					"max" => 2.0,
					"step" => 0.1,
					"type" => "slider",
					"dependency" => array(
						"ai_helper_token_openai" => array('not_empty')
					),
				),
				'ai_helper_chat_models_openai' => array(
					"title" => esc_html__("List of available chat models", 'trx_addons'),
					"desc" => wp_kses_data( __("Specify id and name (title) for the each new model.", 'trx_addons') ),
					"dependency" => array(
						"ai_helper_token_openai" => array('not_empty')
					),
					"clone" => true,
					"std" => trx_addons_list_from_array( Lists::get_default_openai_chat_models() ),
					"type" => "group",
					"fields" => array(
						"id" => array(
							"title" => esc_html__("Model ID", 'trx_addons'),
							"class" => "trx_addons_column-1_3",
							"std" => "",
							"type" => "text"
						),
						"title" => array(
							"title" => esc_html__("Title", 'trx_addons'),
							"class" => "trx_addons_column-1_3",
							"std" => "",
							"type" => "text"
						),
						"max_tokens" => array(
							"title" => esc_html__("Max.tokens", 'trx_addons'),
							"class" => "trx_addons_column-1_3",
							"std" => 4000,
							"min" => 0,
							"max" => Utils::get_default_max_tokens(),
							"step" => 100,
							"type" => "slider"
						),
					)
				),
				'ai_helper_models_openai' => array(
					"title" => esc_html__("List of available image models", 'trx_addons'),
					"desc" => wp_kses_data( __("Specify id and name (title) for the each new model.", 'trx_addons') ),
					"dependency" => array(
						"ai_helper_token_openai" => array('not_empty')
					),
					"clone" => true,
					"std" => trx_addons_list_from_array( Lists::get_default_openai_models() ),
					"type" => "group",
					"fields" => array(
						"id" => array(
							"title" => esc_html__("Model ID", 'trx_addons'),
							"class" => "trx_addons_column-1_2",
							"std" => "",
							"type" => "text"
						),
						"title" => array(
							"title" => esc_html__("Title", 'trx_addons'),
							"class" => "trx_addons_column-1_2",
							"std" => "",
							"type" => "text"
						),
					)
				),

				// Open AI Assistants API settings
				'ai_helper_section_openai_assistants' => array(
					"title" => esc_html__('Open AI Assistants', 'trx_addons'),
					"icon" => TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/images/icons/openai.png',
					"type" => "section"
				),
				'ai_helper_info_openai_assistants' => array(
					"title" => esc_html__('Open AI Assistants', 'trx_addons'),
					"desc" => wp_kses_data( __("A list of assistants created in the GPT4 Plus user account and available for use as an embedded chatbot and/or model in the AI Chat shortcode.", 'trx_addons') )
							. ( ! empty( $log_open_ai_assistants ) ? wp_kses( $log_open_ai_assistants, 'trx_addons_kses_content' ) : '' ),
					"type" => "info"
				),
				'ai_helper_models_openai_assistants' => array(
					"title" => esc_html__("List of available assistants", 'trx_addons'),
					"desc" => wp_kses_data( __("Specify id and title for the each new assistant.", 'trx_addons') ),
					"dependency" => array(
						"ai_helper_token_openai" => array('not_empty')
					),
					"clone" => true,
					"std" => array(),
					"type" => "group",
					"fields" => array(
						"id" => array(
							"title" => esc_html__("Assistant ID", 'trx_addons'),
							"class" => "trx_addons_column-1_2",
							"std" => "",
							"type" => "text"
						),
						"title" => array(
							"title" => esc_html__("Title", 'trx_addons'),
							"class" => "trx_addons_column-1_2",
							"std" => "",
							"type" => "text"
						),
					)
				),

				// Stable Diffusion API settings
				'ai_helper_section_stabble_diffusion' => array(
					"title" => esc_html__('Stable Diffusion API', 'trx_addons'),
					"icon" => TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/images/icons/stable-diffusion.png',
					"type" => "section"
				),
				'ai_helper_info_stabble_diffusion' => array(
					"title" => esc_html__('Stable Diffusion', 'trx_addons'),
					"desc" => wp_kses_data( __("Settings of the AI Helper for Stable Diffusion API", 'trx_addons') )
							. ( ! empty( $log_sd ) ? wp_kses( $log_sd, 'trx_addons_kses_content' ) : '' ),
					"type" => "info"
				),
				'ai_helper_token_stabble_diffusion' => array(
					"title" => esc_html__('Stable Diffusion token', 'trx_addons'),
					"desc" => wp_kses( sprintf(
													__('Specify a token to use the Stable Diffusion API. You can generate a token in your personal account using the link %s', 'trx_addons'),
													apply_filters( 'trx_addons_filter_stable_diffusion_api_key_url',
																	'<a href="' . esc_url( $sd_api_url ) . '" target="_blank">' . esc_html( $sd_api_url ) . '</a>'
																)
												),
										'trx_addons_kses_content'
									),
					"std" => "",
					"type" => "text"
				),
				'ai_helper_use_api_stabble_diffusion' => array(
					"title" => esc_html__('Use API server', 'trx_addons'),
					"desc" => wp_kses_data( __('Which server will be used for access the API - stablediffusionapi.com (legacy) or modelslab.com (new)?', 'trx_addons') ),
					"std" => "ml",
					"options" => array(
						"sd" => esc_html__("Stable Diffusion", 'trx_addons'),
						"ml" => esc_html__("Models Lab", 'trx_addons'),
					),
					"type" => "radio",
					"dependency" => array(
						"ai_helper_token_stabble_diffusion" => array('not_empty')
					),
				),
				'ai_helper_default_api_stabble_diffusion' => array(
					"title" => esc_html__('Default SD model endpoint', 'trx_addons'),
					"desc" => wp_kses_data( __('Which enpoint will be used for access to the default StableDiffusion model on the ModelsLab API server?', 'trx_addons') ),
					"std" => "v6",
					"options" => array(
						"v3" => esc_html__("V3/V4 (Legacy)", 'trx_addons'),
						"v6" => esc_html__("V6 (New)", 'trx_addons'),
					),
					"type" => "radio",
					"dependency" => array(
						"ai_helper_token_stabble_diffusion" => array('not_empty'),
						"ai_helper_use_api_stabble_diffusion" => array('ml')
					),
				),
				'ai_helper_guidance_scale_stabble_diffusion' => array(
					"title" => esc_html__('Guidance scale', 'trx_addons'),
					"desc" => wp_kses_data( __('Scale for classifier-free guidance.', 'trx_addons') ),
					"std" => 7.5,
					"min" => 1,
					"max" => 20,
					"step" => 0.1,
					"type" => "slider",
					"dependency" => array(
						"ai_helper_token_stabble_diffusion" => array('not_empty')
					),
				),
				'ai_helper_inference_steps_stabble_diffusion' => array(
					"title" => esc_html__('Inference steps', 'trx_addons'),
					"desc" => wp_kses_data( __('Number of denoising steps. Available values: 21, 31, 41, 51.', 'trx_addons') ),
					"std" => 21,
					"min" => 21,
					"max" => 51,
					"step" => 10,
					"type" => "slider",
					"dependency" => array(
						"ai_helper_token_stabble_diffusion" => array('not_empty')
					),
				),
				'ai_helper_autoload_models_stabble_diffusion' => array(
					"title" => esc_html__('Autoload a list of models', 'trx_addons'),
					"desc" => wp_kses_data( __('Automatically load the model list from the API or maintain a manual model list.', 'trx_addons') ),
					"std" => "0",
					"type" => "switch",
					"dependency" => array(
						"ai_helper_token_stabble_diffusion" => array('not_empty')
					),
				),
				'ai_helper_models_stabble_diffusion' => array(
					"title" => esc_html__("List of available models", 'trx_addons'),
					"desc" => wp_kses(
								sprintf(
									__("Specify id and name (title) for the each new model. A complete list of available models can be found at %s", 'trx_addons'),
									'<a href="' . esc_url( $sd_models_url ) . '" target="_blank">' . esc_html( $sd_models_url ) . '</a>'
								),
								'trx_addons_kses_content'
							),
					"dependency" => array(
						"ai_helper_token_stabble_diffusion" => array('not_empty'),
						"ai_helper_autoload_models_stabble_diffusion" => array('0')
					),
					"clone" => true,
					"std" => trx_addons_list_from_array( Lists::get_default_sd_models() ),
					"type" => "group",
					"fields" => array(
						"id" => array(
							"title" => esc_html__("Model ID", 'trx_addons'),
							"class" => "trx_addons_column-1_2",
							"std" => "",
							"type" => "text"
						),
						"title" => array(
							"title" => esc_html__("Title", 'trx_addons'),
							"class" => "trx_addons_column-1_2",
							"std" => "",
							"type" => "text"
						),
					)
				),

				// Stability AI API settings
				'ai_helper_section_stability_ai' => array(
					"title" => esc_html__('Stability AI API', 'trx_addons'),
					"icon" => TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/images/icons/stability-ai.png',
					"type" => "section"
				),
				'ai_helper_info_stability_ai' => array(
					"title" => esc_html__('Stability AI', 'trx_addons'),
					"desc" => wp_kses_data( __("Settings of the AI Helper for Stability AI API", 'trx_addons') )
							. ( ! empty( $log_stability_ai ) ? wp_kses( $log_stability_ai, 'trx_addons_kses_content' ) : '' ),
					"type" => "info"
				),
				'ai_helper_token_stability_ai' => array(
					"title" => esc_html__('Stability AI token', 'trx_addons'),
					"desc" => wp_kses( sprintf(
													__('Specify a token to use the Stability AI API. You can generate a token in your personal account using the link %s', 'trx_addons'),
													apply_filters( 'trx_addons_filter_stability_ai_api_key_url',
																	'<a href="' . esc_url( StabilityAi::instance()->get_url( 'account/keys' ) ) . '" target="_blank">' . esc_url( StabilityAi::instance()->get_url( 'account/keys' ) ) . '</a>'
																)
												),
										'trx_addons_kses_content'
									),
					"std" => "",
					"type" => "text"
				),
				'ai_helper_prompt_weight_stability_ai' => array(
					"title" => esc_html__('Prompt weight', 'trx_addons'),
					"desc" => wp_kses_data( __('A weight of the text prompt.', 'trx_addons') ),
					"std" => 1.0,
					"min" => 0.1,
					"max" => 1.0,
					"step" => 0.1,
					"type" => "slider",
					"dependency" => array(
						"ai_helper_token_stability_ai" => array('not_empty')
					),
				),
				'ai_helper_cfg_scale_stability_ai' => array(
					"title" => esc_html__('Cfg scale', 'trx_addons'),
					"desc" => wp_kses_data( __('How strictly the diffusion process adheres to the prompt text (higher values keep your image closer to your prompt).', 'trx_addons') ),
					"std" => 7,
					"min" => 0,
					"max" => 35,
					"step" => 0.1,
					"type" => "slider",
					"dependency" => array(
						"ai_helper_token_stability_ai" => array('not_empty')
					),
				),
				'ai_helper_diffusion_steps_stability_ai' => array(
					"title" => esc_html__('Diffusion steps', 'trx_addons'),
					"desc" => wp_kses_data( __('Number of diffusion steps to run.', 'trx_addons') ),
					"std" => 50,
					"min" => 10,
					"max" => 150,
					"step" => 10,
					"type" => "slider",
					"dependency" => array(
						"ai_helper_token_stability_ai" => array('not_empty')
					),
				),
				'ai_helper_autoload_models_stability_ai' => array(
					"title" => esc_html__('Autoload a list of models', 'trx_addons'),
					"desc" => wp_kses_data( __('Automatically load the model list from the API or maintain a manual model list.', 'trx_addons') ),
					"std" => "0",
					"type" => "switch",
					"dependency" => array(
						"ai_helper_token_stability_ai" => array('not_empty')
					),
				),
				'ai_helper_models_stability_ai' => array(
					"title" => esc_html__("List of available models", 'trx_addons'),
					"desc" => wp_kses(
								sprintf(
									__("Specify id and name (title) for the each new model. A complete list of available models can be found at %s", 'trx_addons'),
									'<a href="' . esc_url( StabilityAi::instance()->get_url( 'pricing' ) ) . '" target="_blank">' . esc_url( StabilityAi::instance()->get_url( 'pricing' ) ) . '</a>'
								),
								'trx_addons_kses_content'
							),
					"dependency" => array(
						"ai_helper_token_stability_ai" => array('not_empty'),
						"ai_helper_autoload_models_stability_ai" => array('0')
					),
					"clone" => true,
					"std" => trx_addons_list_from_array( Lists::get_default_stability_ai_models() ),
					"type" => "group",
					"fields" => array(
						"id" => array(
							"title" => esc_html__("Model ID", 'trx_addons'),
							"class" => "trx_addons_column-1_2",
							"std" => "",
							"type" => "text"
						),
						"title" => array(
							"title" => esc_html__("Title", 'trx_addons'),
							"class" => "trx_addons_column-1_2",
							"std" => "",
							"type" => "text"
						),
					)
				),

				// Flowise AI API settings
				'ai_helper_section_flowise_ai' => array(
					"title" => esc_html__('Flowise AI API', 'trx_addons'),
					"icon" => TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/images/icons/flowise-ai.png',
					"type" => "section"
				),
				'ai_helper_info_flowise_ai' => array(
					"title" => esc_html__('Flowise AI', 'trx_addons'),
					"desc" => wp_kses_data( __("Settings of the AI Helper for Flowise AI API", 'trx_addons') )
							. ( ! empty( $log_flowise_ai ) ? wp_kses( $log_flowise_ai, 'trx_addons_kses_content' ) : '' ),
					"type" => "info"
				),
				'ai_helper_token_flowise_ai' => array(
					"title" => esc_html__('Flowise AI API key', 'trx_addons'),
					"desc" => wp_kses_data( __('Specify a key to use the Flowise AI API. You can get a key in the Flowise Dashboard - API keys', 'trx_addons') ),
					"std" => "",
					"type" => "text"
				),
				'ai_helper_host_flowise_ai' => array(
					"title" => esc_html__('Flowise AI host URL', 'trx_addons'),
					"desc" => wp_kses_data( __('Specify the address of the server on which Flowise AI is deployed', 'trx_addons') ),
					"std" => "",
					"type" => "text",
					"dependency" => array(
						"ai_helper_token_flowise_ai" => array('not_empty')
					),
				),
				'ai_helper_models_flowise_ai' => array(
					"title" => esc_html__("List of available chat flows", 'trx_addons'),
					"desc" => wp_kses_data( __("Specify id and title for the each new chat flow.", 'trx_addons') ),
					"dependency" => array(
						"ai_helper_token_flowise_ai" => array('not_empty')
					),
					"clone" => true,
					"std" => array(),
					"type" => "group",
					"fields" => array(
						"id" => array(
							"title" => esc_html__("Flow ID", 'trx_addons'),
							"class" => "trx_addons_column-1_3",
							"std" => "",
							"type" => "text"
						),
						"title" => array(
							"title" => esc_html__("Title", 'trx_addons'),
							"class" => "trx_addons_column-1_3",
							"std" => "",
							"type" => "text"
						),
						"max_tokens" => array(
							"title" => esc_html__("Max. tokens", 'trx_addons'),
							"class" => "trx_addons_column-1_3",
							"std" => "4000",
							"type" => "text"
						),
					)
				),

				// Google AI API settings
				'ai_helper_section_google_ai' => array(
					"title" => esc_html__('Google AI API', 'trx_addons'),
					"icon" => TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/images/icons/google-ai.png',
					"type" => "section"
				),
				'ai_helper_info_google_ai' => array(
					"title" => esc_html__('Google AI (Gemini)', 'trx_addons'),
					"desc" => wp_kses_data( __("Settings of the AI Helper for Google AI API", 'trx_addons') )
							. ( ! empty( $log_google_ai ) ? wp_kses( $log_google_ai, 'trx_addons_kses_content' ) : '' ),
					"type" => "info"
				),
				'ai_helper_token_google_ai' => array(
					"title" => esc_html__('Google AI API key', 'trx_addons'),
					"desc" => wp_kses( sprintf(
												__('Specify a token to use the Google AI API. You can generate a token in your personal account using the link %s', 'trx_addons'),
												apply_filters( 'trx_addons_filter_google_ai_api_key_url',
																'<a href="https://makersuite.google.com/app/apikey" target="_blank">https://makersuite.google.com/app/apikey</a>'
															)
											),
									'trx_addons_kses_content'
								),
					"std" => "",
					"type" => "text"
				),
				'ai_helper_proxy_google_ai' => array(
					"title" => esc_html__('Proxy URL', 'trx_addons'),
					"desc" => wp_kses_data( __('Specify the address of the proxy-server (if need).', 'trx_addons') ),
					"std" => "",
					"type" => "text",
					"dependency" => array(
						"ai_helper_token_google_ai" => array('not_empty')
					),
				),
				'ai_helper_autoload_models_google_ai' => array(
					"title" => esc_html__('Autoload a list of models', 'trx_addons'),
					"desc" => wp_kses_data( __('Automatically load the model list from the API or maintain a manual model list.', 'trx_addons') ),
					"std" => "0",
					"type" => "switch",
					"dependency" => array(
						"ai_helper_token_google_ai" => array('not_empty')
					),
				),
				'ai_helper_models_google_ai' => array(
					"title" => esc_html__("List of available AI models", 'trx_addons'),
					"desc" => wp_kses_data( __("Specify id and title for the each new chat model.", 'trx_addons') ),
					"dependency" => array(
						"ai_helper_token_google_ai" => array('not_empty'),
						"ai_helper_autoload_models_google_ai" => array('0')
					),
					"clone" => true,
					"std" => array(),
					"type" => "group",
					"fields" => array(
						"id" => array(
							"title" => esc_html__("Model ID", 'trx_addons'),
							"class" => "trx_addons_column-1_3",
							"std" => "",
							"type" => "text"
						),
						"title" => array(
							"title" => esc_html__("Title", 'trx_addons'),
							"class" => "trx_addons_column-1_3",
							"std" => "",
							"type" => "text"
						),
						"max_tokens" => array(
							"title" => esc_html__("Max. tokens", 'trx_addons'),
							"class" => "trx_addons_column-1_3",
							"std" => "16000",
							"type" => "text"
						),
					)
				),

				// External Chats
				'ai_helper_section_embed_chats' => array(
					"title" => esc_html__('Embed ext. chatbots', 'trx_addons'),
					"icon" => 'trx_addons_icon-code-1',
					"type" => "section"
				),
				'ai_helper_embed_chats_info' => array(
					"title" => esc_html__('External chatbots', 'trx_addons'),
					"desc" => wp_kses(
						__("Specify a scope and a html code for the each new embeddig.", 'trx_addons')
						. '<br />'
						. __("In the <b>'URL contain'</b> field, you can list the parts of the address (each part separated by a comma or on a new line), if any of which matches the current page, this block will be displayed.", 'trx_addons')
						. '<br />'
						. __("In the <b>'HTML code'</b> field, paste the code snippet you received when you created/exported the chatbot in your Flowise AI, VoiceFlow, etc. personal account.", 'trx_addons')
						. '<br />'
						. __("You can also use the shortcode <b>[trx_sc_chat type='popup' ...]</b> to insert 'AI Helper Chat'.", 'trx_addons'),
						'trx_addons_kses_content'
						),
					"type" => "info"
				),
				'ai_helper_embed_chats' => array(
					"title" => esc_html__("List of chatbots", 'trx_addons'),
					"desc" => '',
					"clone" => true,
					"std" => array(),
					"type" => "group",
					"fields" => array(
						"title" => array(
							"title" => esc_html__("Title", 'trx_addons'),
							"class" => "trx_addons_column-1_6",
							"std" => "",
							"type" => "text"
						),
						"scope" => array(
							"title" => esc_html__("Scope", 'trx_addons'),
							"class" => "trx_addons_column-1_6",
							"std" => "admin",
							"options" => array(
								"none" => esc_html__("Disabled", 'trx_addons'),
								"admin" => esc_html__("Admin", 'trx_addons'),
								"frontend" => esc_html__("Frontend", 'trx_addons'),
								"site" => esc_html__("Whole site", 'trx_addons'),
							),
							"dir" => "vertical",
							"type" => "radio"
						),
						"url_contain" => array(
							"title" => esc_html__("URL contain", 'trx_addons'),
							"class" => "trx_addons_column-1_6",
							"std" => "",
							"type" => "textarea"
						),
						"code" => array(
							"title" => esc_html__("HTML code", 'trx_addons'),
							"class" => "trx_addons_column-1_2",
							"std" => "",
							"type" => "textarea"
						),
					)
				),

				// Image Generator
				'ai_helper_section_sc_igenerator' => array(
					"title" => esc_html__('SC Image Generator', 'trx_addons'),
					"icon" => 'trx_addons_icon-format-image',
					"type" => "section"
				),
				'ai_helper_sc_igenerator_common' => array(
					"title" => esc_html__('Shortcode "Image Generator": Common settings', 'trx_addons'),
					"type" => "info"
				),
				'ai_helper_sc_igenerator_api_order' => array(
					'title' => esc_html__( 'API order', 'trx_addons' ),
					'desc' => wp_kses_data( __( 'Turn on/off the available APIs and drag and drop them to specify the sequence', 'trx_addons' ) ),
					"dir" => 'vertical',
					"sortable" => true,
					"std" => array( 'openai' => 1, 'stable-diffusion' => 1, 'stability-ai' => 1 ),
					'options' => $is_options_page ? Lists::get_list_ai_image_apis() : array(),
					"type" => "checklist"
				),
				'ai_helper_sc_igenerator_translate_prompt' => array(
					"title" => esc_html__('Translate prompt', 'trx_addons'),
					"desc" => wp_kses_data( __('Always translate prompt into English. Most models are trained on English language datasets and therefore produce the most relevant results only if the prompt is formulated in English. If you have specified a token for the OpenAi API (see section above) - we can automatically translate prompts into English to improve image generation.', 'trx_addons') ),
					"std" => "1",
					"type" => "switch"
				),
				'ai_helper_sc_igenerator_free' => array(
					"title" => esc_html__('Limits for a Free Mode', 'trx_addons'),
					"type" => "info"
				),
				'ai_helper_sc_igenerator_limits' => array(
					"title" => esc_html__('Use limits', 'trx_addons'),
					"desc" => wp_kses_data( __('Use limits (per hour and per visitor) when generating images.', 'trx_addons') ),
					"std" => "1",
					"type" => "switch"
				),
				'ai_helper_sc_igenerator_limit_per_hour' => array(
					"title" => esc_html__('Images per 1 hour', 'trx_addons'),
					"desc" => wp_kses_data( __('How many images can all visitors generate in 1 hour?', 'trx_addons') ),
					"std" => 12,
					"min" => 0,
					"max" => 1000,
					"type" => "slider",
					"dependency" => array(
						"ai_helper_sc_igenerator_limits" => array(1)
					),
				),
				'ai_helper_sc_igenerator_limit_per_visitor' => array(
					"title" => esc_html__('Requests from 1 visitor', 'trx_addons'),
					"desc" => wp_kses_data( __('How many requests can a single visitor send in 1 hour?', 'trx_addons') ),
					"std" => 2,
					"min" => 0,
					"max" => 100,
					"type" => "slider",
					"dependency" => array(
						"ai_helper_sc_igenerator_limits" => array(1)
					),
				),
				'ai_helper_sc_igenerator_limit_alert' => array(
					"title" => esc_html__('Limits reached alert', 'trx_addons'),
					"desc" => wp_kses_data( __('The message that the visitor will see when the limit of requests or generated images (per hour) is exceeded.', 'trx_addons') )
							. ' ' . wp_kses_data( __('If Premium Mode is used, be sure to provide a link to the paid access page here.', 'trx_addons') ),
					"std" => wp_kses( apply_filters( 'trx_addons_sc_igenerator_limit_alert_default',
								'<h5>' . __( 'Limits are reached!', 'trx_addons' ) . '</h5>'
								. '<p>' . __( 'The limit of the number of requests from a single visitor or the number of images that can be generated per hour has been reached.', 'trx_addons' ) . '</p>'
								. '<p>' . __( 'In order to generate more images, sign up for our premium service.', 'trx_addons' ) . '</p>'
								. '<p><a href="#" class="trx_addons_sc_igenerator_link_premium">' . __( 'Sign Up Now', 'trx_addons' ) . '</a></p>'
							), 'trx_addons_kses_content' ),
					"type" => "text_editor",
					"dependency" => array(
						"ai_helper_sc_igenerator_limits" => array(1)
					),
				),
				'ai_helper_sc_igenerator_info_premium' => array(
					"title" => esc_html__('Limits for a Premium Mode', 'trx_addons'),
					"desc" => wp_kses_data('These options enable you to create a paid image generation service. Set limits for paid usage here. Applied to the Image Generator shortcode with the "Premium Mode" option enabled. Ensure restricted access to pages with this shortcode by providing a link to the paid access page in the alert message above.', 'trx_addons'),
					"type" => "info"
				),
				'ai_helper_sc_igenerator_limits_premium' => array(
					"title" => esc_html__('Use limits', 'trx_addons'),
					"desc" => wp_kses_data( __('Use limits (per hour and per visitor) when generating images.', 'trx_addons') ),
					"std" => "0",
					"type" => "switch"
				),
				'ai_helper_sc_igenerator_limit_per_hour_premium' => array(
					"title" => esc_html__('Images per 1 hour', 'trx_addons'),
					"desc" => wp_kses_data( __('How many images can all unlogged visitors generate in 1 hour?', 'trx_addons') ),
					"std" => 12,
					"min" => 0,
					"max" => 1000,
					"type" => "slider",
					"dependency" => array(
						"ai_helper_sc_igenerator_limits_premium" => array(1)
					),
				),
				'ai_helper_sc_igenerator_limit_per_visitor_premium' => array(
					"title" => esc_html__('Requests from 1 visitor', 'trx_addons'),
					"desc" => wp_kses_data( __('How many requests can a single unlogged visitor send in 1 hour?', 'trx_addons') ),
					"std" => 2,
					"min" => 0,
					"max" => 100,
					"type" => "slider",
					"dependency" => array(
						"ai_helper_sc_igenerator_limits_premium" => array(1)
					),
				),
				'ai_helper_sc_igenerator_levels_premium' => array(
					"title" => esc_html__("User levels with limits", 'trx_addons'),
					"desc" => wp_kses_data( __( 'How many images a user can generate depending on their subscription level. The "Default" limit is used for regular registered users. For more flexible settings, use special plugins to separate access levels.', 'trx_addons' ) ),
					"dependency" => array(
						"ai_helper_sc_igenerator_limits_premium" => array(1)
					),
					"clone" => true,
					"std" => array(),
					"type" => "group",
					"fields" => array(
						"level" => array(
							"title" => esc_html__("Level", 'trx_addons'),
							"class" => "trx_addons_column-1_3",
							"std" => "default",
							"options" => apply_filters( 'trx_addons_filter_sc_igenerator_list_user_levels', array( 'default' => __( 'Default', 'trx_addons' ) ) ),
							"type" => "select"
						),
						"limit" => array(
							"title" => esc_html__("Images limit", 'trx_addons'),
							"class" => "trx_addons_column-1_3",
							"std" => "",
							"type" => "text"
						),
						"per" => array(
							"title" => esc_html__("per", 'trx_addons'),
							"class" => "trx_addons_column-1_3",
							"std" => "day",
							"options" => $is_options_page ? Lists::get_list_periods() : array(),
							"type" => "select"
						),
					)
				),
				'ai_helper_sc_igenerator_limit_alert_premium' => array(
					"title" => esc_html__('Limits reached alert', 'trx_addons'),
					"desc" => wp_kses_data( __('The message that the visitor will see when the limit of requests or generated images (per hour) is exceeded.', 'trx_addons') ),
					"std" => wp_kses( apply_filters( 'trx_addons_sc_igenerator_limit_alert_default_premium',
								'<h5>' . __( 'Limits are reached!', 'trx_addons' ) . '</h5>'
								. '<p>' . __( 'The limit of the number of requests from a single visitor or the number of images that can be generated per hour has been reached.', 'trx_addons' ) . '</p>'
								. '<p>' . __( 'Please, try again later.', 'trx_addons' ) . '</p>'
							), 'trx_addons_kses_content' ),
					"type" => "text_editor",
					"dependency" => array(
						"ai_helper_sc_igenerator_limits_premium" => array(1)
					),
				),

				// Text Generator
				'ai_helper_section_sc_tgenerator' => array(
					"title" => esc_html__('SC Text Generator', 'trx_addons'),
					"icon" => 'trx_addons_icon-doc-text',
					"type" => "section"
				),
				'ai_helper_sc_tgenerator_common' => array(
					"title" => esc_html__('Shortcode "Text Generator": Common settings', 'trx_addons'),
					"type" => "info"
				),
				'ai_helper_sc_tgenerator_api_order' => array(
					'title' => esc_html__( 'API order', 'trx_addons' ),
					'desc' => wp_kses_data( __( 'Turn on/off the available APIs and drag and drop them to specify the sequence', 'trx_addons' ) ),
					"dir" => 'vertical',
					"sortable" => true,
					"std" => array( 'openai' => 1, 'openai-assistants' => 1, 'flowise-ai' => 1, 'google-ai' => 1 ),
					'options' => $is_options_page ? Lists::get_list_ai_chat_apis() : array(),
					"type" => "checklist"
				),
				'ai_helper_sc_tgenerator_temperature' => array(
					"title" => esc_html__('Temperature', 'trx_addons'),
					"desc" => wp_kses_data( __('What sampling temperature to use, between 0 and 2. Higher values like 0.8 will make the output more random, while lower values like 0.2 will make it more focused and deterministic.', 'trx_addons') ),
					"std" => 1,
					"min" => 0,
					"max" => 2,
					"step" => 0.1,
					"type" => "slider"
				),
				'ai_helper_sc_tgenerator_system_prompt' => array(
					"title" => esc_html__('System Prompt', 'trx_addons'),
					"desc" => wp_kses_data( __('System instructions for the text generator, serve as a guide for choosing the style of communication on the part of the AI.', 'trx_addons') ),
					"std" => __( 'You are an assistant for writing posts. Return only the result without any additional messages. Format the response with HTML tags.', 'trx_addons' ),
					"type" => "textarea",
					"dependency" => array(
						"ai_helper_token_openai" => array('not_empty')
					),
				),
				'ai_helper_sc_tgenerator_free' => array(
					"title" => esc_html__('Limits for a Free Mode', 'trx_addons'),
					"type" => "info"
				),
				'ai_helper_sc_tgenerator_limits' => array(
					"title" => esc_html__('Use limits', 'trx_addons'),
					"desc" => wp_kses_data( __('Use limits (per request, per hour and per visitor) when generating text.', 'trx_addons') ),
					"std" => "1",
					"type" => "switch"
				),
				'ai_helper_sc_tgenerator_limit_per_request' => array(
					"title" => esc_html__('Max. tokens per 1 request', 'trx_addons'),
					"desc" => wp_kses_data( __('How many tokens can be used per one request to the API?', 'trx_addons') ),
					"std" => 1000,
					"min" => 0,
					"max" => Utils::get_default_max_tokens(),
					"step" => 100,
					"type" => "slider",
					"dependency" => array(
						"ai_helper_sc_tgenerator_limits" => array(1)
					),
				),
				'ai_helper_sc_tgenerator_limit_per_hour' => array(
					"title" => esc_html__('Requests per 1 hour', 'trx_addons'),
					"desc" => wp_kses_data( __('How many requests can be processed for all visitors in 1 hour?', 'trx_addons') ),
					"std" => 8,
					"min" => 0,
					"max" => 1000,
					"type" => "slider",
					"dependency" => array(
						"ai_helper_sc_tgenerator_limits" => array(1)
					),
				),
				'ai_helper_sc_tgenerator_limit_per_visitor' => array(
					"title" => esc_html__('Requests from 1 visitor', 'trx_addons'),
					"desc" => wp_kses_data( __('How many requests can send a single visitor in 1 hour?', 'trx_addons') ),
					"std" => 2,
					"min" => 0,
					"max" => 100,
					"type" => "slider",
					"dependency" => array(
						"ai_helper_sc_tgenerator_limits" => array(1)
					),
				),
				'ai_helper_sc_tgenerator_limit_alert' => array(
					"title" => esc_html__('Limits reached alert', 'trx_addons'),
					"desc" => wp_kses_data( __('The message that the visitor will see when the limit of requests (per hour) is exceeded.', 'trx_addons') )
								. ' ' . wp_kses_data( __('If Premium Mode is used, be sure to provide a link to the paid access page here.', 'trx_addons') ),
					"std" => wp_kses( apply_filters( 'trx_addons_sc_tgenerator_limit_alert_default',
								'<h5>' . __( 'Limits are reached!', 'trx_addons' ) . '</h5>'
								. '<p>' . __( 'The limit of the number of requests from a single visitor per hour has been reached.', 'trx_addons' ) . '</p>'
								. '<p>' . __( 'In order to generate more texts, sign up for our premium service.', 'trx_addons' ) . '</p>'
								. '<p><a href="#" class="trx_addons_sc_tgenerator_link_premium">' . __( 'Sign Up Now', 'trx_addons' ) . '</a></p>'
							), 'trx_addons_kses_content' ),
					"type" => "text_editor",
					"dependency" => array(
						"ai_helper_sc_tgenerator_limits" => array(1)
					),
				),
				'ai_helper_sc_tgenerator_premium' => array(
					"title" => esc_html__('Limits for a Premium Mode', 'trx_addons'),
					"type" => "info"
				),
				'ai_helper_sc_tgenerator_limits_premium' => array(
					"title" => esc_html__('Use limits', 'trx_addons'),
					"desc" => wp_kses_data( __('Use limits (per request, per hour and per visitor) when generating text.', 'trx_addons') ),
					"std" => "1",
					"type" => "switch"
				),
				'ai_helper_sc_tgenerator_limit_per_request_premium' => array(
					"title" => esc_html__('Max. tokens per 1 request', 'trx_addons'),
					"desc" => wp_kses_data( __('How many tokens can be used per one request to the API?', 'trx_addons') ),
					"std" => 1000,
					"min" => 0,
					"max" => Utils::get_default_max_tokens(),
					"step" => 100,
					"type" => "slider",
					"dependency" => array(
						"ai_helper_sc_tgenerator_limits_premium" => array(1)
					),
				),
				'ai_helper_sc_tgenerator_limit_per_hour_premium' => array(
					"title" => esc_html__('Requests per 1 hour', 'trx_addons'),
					"desc" => wp_kses_data( __('How many requests can be processed for all visitors in 1 hour?', 'trx_addons') ),
					"std" => 8,
					"min" => 0,
					"max" => 1000,
					"type" => "slider",
					"dependency" => array(
						"ai_helper_sc_tgenerator_limits_premium" => array(1)
					),
				),
				'ai_helper_sc_tgenerator_limit_per_visitor_premium' => array(
					"title" => esc_html__('Requests from 1 visitor', 'trx_addons'),
					"desc" => wp_kses_data( __('How many requests can send a single visitor in 1 hour?', 'trx_addons') ),
					"std" => 2,
					"min" => 0,
					"max" => 100,
					"type" => "slider",
					"dependency" => array(
						"ai_helper_sc_tgenerator_limits_premium" => array(1)
					),
				),
				'ai_helper_sc_tgenerator_levels_premium' => array(
					"title" => esc_html__("User levels with limits", 'trx_addons'),
					"desc" => wp_kses_data( __( 'How many requests a user can generate depending on their subscription level. The "Default" limit is used for regular registered users. For more flexible settings, use special plugins to separate access levels.', 'trx_addons' ) ),
					"dependency" => array(
						"ai_helper_sc_tgenerator_limits_premium" => array(1)
					),
					"clone" => true,
					"std" => array(),
					"type" => "group",
					"fields" => array(
						"level" => array(
							"title" => esc_html__("Level", 'trx_addons'),
							"class" => "trx_addons_column-1_3",
							"std" => "default",
							"options" => apply_filters( 'trx_addons_filter_sc_tgenerator_list_user_levels', array( 'default' => __( 'Default', 'trx_addons' ) ) ),
							"type" => "select"
						),
						"limit" => array(
							"title" => esc_html__("Requests limit", 'trx_addons'),
							"class" => "trx_addons_column-1_3",
							"std" => "",
							"type" => "text"
						),
						"per" => array(
							"title" => esc_html__("per", 'trx_addons'),
							"class" => "trx_addons_column-1_3",
							"std" => "day",
							"options" => $is_options_page ? Lists::get_list_periods() : array(),
							"type" => "select"
						),
					)
				),
				'ai_helper_sc_tgenerator_limit_alert_premium' => array(
					"title" => esc_html__('Limits reached alert', 'trx_addons'),
					"desc" => wp_kses_data( __('The message that the visitor will see when the limit of requests (per hour) is exceeded.', 'trx_addons') ),
					"std" => wp_kses( apply_filters( 'trx_addons_sc_tgenerator_limit_alert_default_premium',
								'<h5>' . __( 'Limits are reached!', 'trx_addons' ) . '</h5>'
								. '<p>' . __( 'The limit of the number of requests from a single visitor per hour has been reached.', 'trx_addons' ) . '</p>'
								. '<p>' . __( 'Please, try again later.', 'trx_addons' ) . '</p>'
							), 'trx_addons_kses_content' ),
					"type" => "text_editor",
					"dependency" => array(
						"ai_helper_sc_tgenerator_limits_premium" => array(1)
					),
				),

				// Chat
				'ai_helper_section_sc_chat' => array(
					"title" => esc_html__('SC AI Chat', 'trx_addons'),
					"icon" => 'trx_addons_icon-chat',
					"type" => "section"
				),
				'ai_helper_sc_chat_common' => array(
					"title" => esc_html__('Shortcode "AI Chat": Common settings', 'trx_addons'),
					"type" => "info"
				),
				'ai_helper_sc_chat_api_order' => array(
					'title' => esc_html__( 'API order', 'trx_addons' ),
					'desc' => wp_kses_data( __( 'Turn on/off the available APIs and drag and drop them to specify the sequence', 'trx_addons' ) ),
					"dir" => 'vertical',
					"sortable" => true,
					"std" => array( 'openai' => 1, 'openai-assistants' => 1, 'flowise-ai' => 1, 'google-ai' => 1 ),
					'options' => $is_options_page ? Lists::get_list_ai_chat_apis() : array(),
					"type" => "checklist"
				),
				'ai_helper_sc_chat_temperature' => array(
					"title" => esc_html__('Temperature', 'trx_addons'),
					"desc" => wp_kses_data( __('What sampling temperature to use, between 0 and 2. Higher values like 0.8 will make the output more random, while lower values like 0.2 will make it more focused and deterministic.', 'trx_addons') ),
					"std" => 1,
					"min" => 0,
					"max" => 2,
					"step" => 0.1,
					"type" => "slider"
				),
				'ai_helper_sc_chat_system_prompt' => array(
					"title" => esc_html__('System Prompt', 'trx_addons'),
					"desc" => wp_kses_data( __('System instructions for the chatbot (not included in the list of messages, serve as a guide for choosing the style of communication on the part of the chatbot).', 'trx_addons') ),
					"std" => __( 'You are an assistant for writing posts. Return only the result without any additional messages. Format the response with HTML tags.', 'trx_addons' ),
					"type" => "textarea",
					"dependency" => array(
						"ai_helper_token_openai" => array('not_empty')
					),
				),
				'ai_helper_sc_chat_free' => array(
					"title" => esc_html__('Limits for a Free Mode', 'trx_addons'),
					"type" => "info"
				),
				'ai_helper_sc_chat_limits' => array(
					"title" => esc_html__('Use limits', 'trx_addons'),
					"desc" => wp_kses_data( __('Use limits (per request, per hour and per visitor) when chatting.', 'trx_addons') ),
					"std" => "1",
					"type" => "switch"
				),
				'ai_helper_sc_chat_limit_per_request' => array(
					"title" => esc_html__('Max. tokens per 1 request', 'trx_addons'),
					"desc" => wp_kses_data( __('How many tokens can be used per one request to the chat?', 'trx_addons') ),
					"std" => 1000,
					"min" => 0,
					"max" => Utils::get_default_max_tokens(),
					"step" => 100,
					"type" => "slider",
					"dependency" => array(
						"ai_helper_sc_chat_limits" => array(1)
					),
				),
				'ai_helper_sc_chat_limit_per_hour' => array(
					"title" => esc_html__('Requests per 1 hour', 'trx_addons'),
					"desc" => wp_kses_data( __('How many requests can be processed for all visitors in 1 hour?', 'trx_addons') ),
					"std" => 80,
					"min" => 0,
					"max" => 1000,
					"type" => "slider",
					"dependency" => array(
						"ai_helper_sc_chat_limits" => array(1)
					),
				),
				'ai_helper_sc_chat_limit_per_visitor' => array(
					"title" => esc_html__('Requests from 1 visitor', 'trx_addons'),
					"desc" => wp_kses_data( __('How many requests can send a single visitor in 1 hour?', 'trx_addons') ),
					"std" => 10,
					"min" => 0,
					"max" => 100,
					"type" => "slider",
					"dependency" => array(
						"ai_helper_sc_chat_limits" => array(1)
					),
				),
				'ai_helper_sc_chat_limit_alert' => array(
					"title" => esc_html__('Limits reached alert', 'trx_addons'),
					"desc" => wp_kses_data( __('The message that the visitor will see when the limit of requests (per hour) is exceeded.', 'trx_addons') )
								. ' ' . wp_kses_data( __('If Premium Mode is used, be sure to provide a link to the paid access page here.', 'trx_addons') ),
					"std" => wp_kses( apply_filters( 'trx_addons_sc_chat_limit_alert_default',
								'<h5>' . __( 'Limits are reached!', 'trx_addons' ) . '</h5>'
								. '<p>' . __( 'The limit of the number of requests from a single visitor per hour has been reached.', 'trx_addons' ) . '</p>'
								. '<p>' . __( 'In order to generate more texts, sign up for our premium service.', 'trx_addons' ) . '</p>'
								. '<p><a href="#" class="trx_addons_sc_chat_link_premium">' . __( 'Sign Up Now', 'trx_addons' ) . '</a></p>'
							), 'trx_addons_kses_content' ),
					"type" => "text_editor",
					"dependency" => array(
						"ai_helper_sc_chat_limits" => array(1)
					),
				),
				'ai_helper_sc_chat_premium' => array(
					"title" => esc_html__('Limits for a Premium Mode', 'trx_addons'),
					"type" => "info"
				),
				'ai_helper_sc_chat_limits_premium' => array(
					"title" => esc_html__('Use limits', 'trx_addons'),
					"desc" => wp_kses_data( __('Use limits (per request, per hour and per visitor) when chatting.', 'trx_addons') ),
					"std" => "1",
					"type" => "switch"
				),
				'ai_helper_sc_chat_limit_per_request_premium' => array(
					"title" => esc_html__('Max. tokens per 1 request', 'trx_addons'),
					"desc" => wp_kses_data( __('How many tokens can be used per one request to the chat?', 'trx_addons') ),
					"std" => 1000,
					"min" => 0,
					"max" => Utils::get_default_max_tokens(),
					"step" => 100,
					"type" => "slider",
					"dependency" => array(
						"ai_helper_sc_chat_limits_premium" => array(1)
					),
				),
				'ai_helper_sc_chat_limit_per_hour_premium' => array(
					"title" => esc_html__('Requests per 1 hour', 'trx_addons'),
					"desc" => wp_kses_data( __('How many requests can be processed for all visitors in 1 hour?', 'trx_addons') ),
					"std" => 80,
					"min" => 0,
					"max" => 1000,
					"type" => "slider",
					"dependency" => array(
						"ai_helper_sc_chat_limits_premium" => array(1)
					),
				),
				'ai_helper_sc_chat_limit_per_visitor_premium' => array(
					"title" => esc_html__('Requests from 1 visitor', 'trx_addons'),
					"desc" => wp_kses_data( __('How many requests can send a single visitor in 1 hour?', 'trx_addons') ),
					"std" => 10,
					"min" => 0,
					"max" => 100,
					"type" => "slider",
					"dependency" => array(
						"ai_helper_sc_chat_limits_premium" => array(1)
					),
				),
				'ai_helper_sc_chat_levels_premium' => array(
					"title" => esc_html__("User levels with limits", 'trx_addons'),
					"desc" => wp_kses_data( __( 'How many requests a user can generate depending on their subscription level. The "Default" limit is used for regular registered users. For more flexible settings, use special plugins to separate access levels.', 'trx_addons' ) ),
					"dependency" => array(
						"ai_helper_sc_chat_limits_premium" => array(1)
					),
					"clone" => true,
					"std" => array(),
					"type" => "group",
					"fields" => array(
						"level" => array(
							"title" => esc_html__("Level", 'trx_addons'),
							"class" => "trx_addons_column-1_3",
							"std" => "default",
							"options" => apply_filters( 'trx_addons_filter_sc_chat_list_user_levels', array( 'default' => __( 'Default', 'trx_addons' ) ) ),
							"type" => "select"
						),
						"limit" => array(
							"title" => esc_html__("Requests limit", 'trx_addons'),
							"class" => "trx_addons_column-1_3",
							"std" => "",
							"type" => "text"
						),
						"per" => array(
							"title" => esc_html__("per", 'trx_addons'),
							"class" => "trx_addons_column-1_3",
							"std" => "day",
							"options" => $is_options_page ? Lists::get_list_periods() : array(),
							"type" => "select"
						),
					)
				),
				'ai_helper_sc_chat_limit_alert_premium' => array(
					"title" => esc_html__('Limits reached alert', 'trx_addons'),
					"desc" => wp_kses_data( __('The message that the visitor will see when the limit of requests (per hour) is exceeded.', 'trx_addons') ),
					"std" => wp_kses( apply_filters( 'trx_addons_sc_chat_limit_alert_default_premium',
								'<h5>' . __( 'Limits are reached!', 'trx_addons' ) . '</h5>'
								. '<p>' . __( 'The limit of the number of requests from a single visitor per hour has been reached.', 'trx_addons' ) . '</p>'
								. '<p>' . __( 'Please, try again later.', 'trx_addons' ) . '</p>'
							), 'trx_addons_kses_content' ),
					"type" => "text_editor",
					"dependency" => array(
						"ai_helper_sc_chat_limits_premium" => array(1)
					),
				),

				'ai_helper_section_end' => array(
					"type" => "panel_end"
				),
			) ) );

			return $options;
		}

		/**
		 * Fill 'Post types' before show ThemeREX Addons Options
		 * 
		 * @hooked trx_addons_filter_before_show_options
		 *
		 * @param array $options  Array of options
		 * 
		 * @return array  	  Modified array of options
		 */
		function fill_options( $options ) {
			if ( isset( $options['ai_helper_process_selected_post_types'] ) ) {
				$options['ai_helper_process_selected_post_types']['options'] = trx_addons_get_list_posts_types();
			}
			return $options;
		}

		/**
		 * Fix option params in the ThemeREX Addons Options
		 * 
		 * @hooked trx_addons_filter_before_show_options
		 *
		 * @param array $options  Array of options
		 * 
		 * @return array  	  Modified array of options
		 */
		function fix_options( $options ) {
			foreach ( array( 'ai_helper_sc_tgenerator_limit_per_request', 'ai_helper_sc_chat_limit_per_request' ) as $option ) {
				$max_tokens = Utils::get_max_tokens( $option == 'ai_helper_sc_tgenerator_limit_per_request' ? 'sc_tgenerator' : 'sc_chat' );
				foreach ( array( '', '_premium' ) as $suffix ) {
					$name = $option . $suffix;
					if ( ! empty( $options[ $name ]['std'] ) && $options[ $name ]['std'] > $max_tokens ) {
						$options[ $name ]['std'] = $max_tokens;
					}
					if ( ! empty( $options[ $name ]['val'] ) && $options[ $name ]['val'] > $max_tokens ) {
						$options[ $name ]['val'] = $max_tokens;
					}
					if ( ! empty( $options[ $name ]['max'] ) ) {
						$options[ $name ]['max'] = $max_tokens;
					}
				}
			}
			return $options;
		}

		/**
		 * Clear some addon specific options before export
		 * 
		 * @hooked trx_addons_filter_export_options
		 * 
		 * @param array $options  Array of options
		 * 
		 * @return array  	  Modified array of options
		 */
		 function remove_token_from_export( $options ) {
			// List options to reset: 'key' => default_value
			$reset_options = apply_filters( 'trx_addons_filter_ai_helper_options_to_reset', array(
				'ai_helper_token_openai' => array( 'default' => '' ),
				'ai_helper_proxy_openai' => array( 'default' => '' ),
				'ai_helper_models_openai_assistants' => array( 'default' => array() ),
				'ai_helper_token_stabble_diffusion' => array( 'default' => '' ),
				'ai_helper_token_stability_ai' => array( 'default' => '' ),
				'ai_helper_token_flowise_ai' => array( 'default' => '' ),
				'ai_helper_host_flowise_ai' => array( 'default' => '' ),
				'ai_helper_models_flowise_ai' => array( 'default' => array() ),
				'ai_helper_token_google_ai' => array( 'default' => '' ),
				'ai_helper_proxy_google_ai' => array( 'default' => '' ),
				//'ai_helper_embed_chats' => array( 'default' => array(), 'field' => 'code', 'filter' => '<script' ),	// Remove chats if embed code contains <script> tag
				'ai_helper_embed_chats' => array( 'default' => array() ),
			) );
			// Remove log
			if ( isset( $options['trx_addons_ai_helper_log'] ) ) {
				unset( $options['trx_addons_ai_helper_log'] );
			}
			// Reset options
			foreach ( $reset_options as $option => $value ) {
				if ( ! empty( $options['trx_addons_options'][ $option ] ) ) {
					// Remove options by filter
					if ( ! empty( $value['filter'] ) ) {
						if ( is_array( $options['trx_addons_options'][ $option ] ) ) {
							foreach( $options['trx_addons_options'][ $option ] as $k => $v ) {
								if ( ! empty( $v[ $value['field'] ] ) && strpos( $v[ $value['field'] ], $value['filter'] ) !== false ) {
									unset( $options['trx_addons_options'][ $option ][ $k ] );
								}
							}
						} else if ( strpos( $options['trx_addons_options'][ $option ], $value['filter'] ) !== false ) {
							$options['trx_addons_options'][ $option ] = $value['default'];
						}
					// Reset option to default value
					} else {
						$options['trx_addons_options'][ $option ] = $value['default'];
					}
				}
			}
			return $options;
		}
	}
}
