<?php
/**
 * Shortcode: AI Chat (Elementor support)
 *
 * @package ThemeREX Addons
 * @since v2.22.0
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }

use TrxAddons\AiHelper\OpenAi;
use TrxAddons\AiHelper\Lists;
use TrxAddons\AiHelper\Utils;

// Elementor Widget
//------------------------------------------------------
if ( ! function_exists('trx_addons_sc_chat_add_in_elementor')) {
	add_action( trx_addons_elementor_get_action_for_widgets_registration(), 'trx_addons_sc_chat_add_in_elementor' );
	function trx_addons_sc_chat_add_in_elementor() {
		
		if ( ! class_exists( 'TRX_Addons_Elementor_Widget' ) ) return;	

		class TRX_Addons_Elementor_Widget_Chat extends TRX_Addons_Elementor_Widget {

			/**
			 * Widget base constructor.
			 *
			 * Initializing the widget base class.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @param array      $data Widget data. Default is an empty array.
			 * @param array|null $args Optional. Widget default arguments. Default is null.
			 */
			public function __construct( $data = [], $args = null ) {
				parent::__construct( $data, $args );
				$this->add_plain_params( array(
					'temperature' => 'size',
					'max_tokens' => 'size',
					'offset_x' => 'size+unit',
					'offset_y' => 'size+unit',
					'title_image' => 'url',
					'new_chat_image' => 'url',
					'assistant_icon_size' => 'size+unit',
					'assistant_image' => 'url',
					'user_icon_size' => 'size+unit',
					'user_image' => 'url',
					'button_image' => 'url',
					'popup_button_size' => 'size+unit',
					'popup_button_image' => 'url',
					'popup_button_image_opened' => 'url',
				) );
			}

			/**
			 * Retrieve widget name.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @return string Widget name.
			 */
			public function get_name() {
				return 'trx_sc_chat';
			}

			/**
			 * Retrieve widget title.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @return string Widget title.
			 */
			public function get_title() {
				return __( 'AI Helper Chat', 'trx_addons' );
			}

			/**
			 * Get widget keywords.
			 *
			 * Retrieve the list of keywords the widget belongs to.
			 *
			 * @since 2.27.2
			 * @access public
			 *
			 * @return array Widget keywords.
			 */
			public function get_keywords() {
				return [ 'ai', 'helper', 'chat', 'conversation', 'messages' ];
			}

			/**
			 * Retrieve widget icon.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @return string Widget icon.
			 */
			public function get_icon() {
				return 'eicon-text';
			}

			/**
			 * Retrieve the list of categories the widget belongs to.
			 *
			 * Used to determine where to display the widget in the editor.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @return array Widget categories.
			 */
			public function get_categories() {
				return ['trx_addons-elements'];
			}

			/**
			 * Register widget controls.
			 *
			 * Adds different input fields to allow the user to change and customize the widget settings.
			 *
			 * @since 1.6.41
			 * @access protected
			 */
			protected function register_controls() {

				// Detect edit mode
				$is_edit_mode = trx_addons_elm_is_edit_mode();
				$models = ! $is_edit_mode ? array() : Lists::get_list_ai_chat_models();
				$models_flowise = ! $is_edit_mode ? array() : array_values( array_filter( array_keys( $models ), function( $key ) { return Utils::is_flowise_ai_model( $key ); } ) );

				// Register controls
				$this->start_controls_section(
					'section_sc_chat',
					[
						'label' => __( 'AI Helper Chat', 'trx_addons' ),
					]
				);

				$this->add_control(
					'type',
					[
						'label' => __( 'Layout', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'render_type' => 'template',
						'options' => apply_filters('trx_addons_sc_type', Lists::get_list_ai_chat_layouts(), 'trx_sc_chat'),
						'default' => 'default'
					]
				);

				$this->add_control(
					'position',
					[
						'label' => __( 'Position', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => trx_addons_get_list_sc_fixed_positions( true ),
						'default' => 'br',
						'prefix_class' => 'sc_chat_position_',
						'condition' => [
							'type' => 'popup'
						]
					]
				);

				$this->add_responsive_control(
					'offset_x',
					[
						'label' => __( 'Offset X', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 1,
							'unit' => 'em'
						],
						'size_units' => [ 'em', 'px', '%' ],
						'range' => [
							'em' => [
								'min' => 0,
								'max' => 10,
								'step' => 0.1
							],
							'px' => [
								'min' => 0,
								'max' => 100,
								'step' => 1
							],
							'%' => [
								'min' => 0,
								'max' => 100,
								'step' => 1
							]
						],
						'selectors' => [
							'{{WRAPPER}} .sc_chat_popup' => '--trx-addons-ai-helper-popup-offset-x: {{SIZE}}{{UNIT}};',
						],
						'condition' => [
							'type' => 'popup'
						]
					]
				);

				$this->add_responsive_control(
					'offset_y',
					[
						'label' => __( 'Offset Y', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 1,
							'unit' => 'em'
						],
						'size_units' => [ 'em', 'px', '%' ],
						'range' => [
							'em' => [
								'min' => 0,
								'max' => 10,
								'step' => 0.1
							],
							'px' => [
								'min' => 0,
								'max' => 100,
								'step' => 1
							],
							'%' => [
								'min' => 0,
								'max' => 100,
								'step' => 1
							]
						],
						'selectors' => [
							'{{WRAPPER}} .sc_chat_popup' => '--trx-addons-ai-helper-popup-offset-y: {{SIZE}}{{UNIT}};',
						],
						'condition' => [
							'type' => 'popup'
						]
					]
				);

				$this->add_control(
					'prompt',
					[
						'label' => __( 'Default prompt', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::TEXT,
						'default' => ''
					]
				);

				$this->add_control(
					"default_text_description",
					[
						'raw' => __( 'If the following fields are empty - the default text will be displayed in the corresponding places. To disable it - you can specify "#" sign in the field.', 'trx_addons' ),
						'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
						'type' => \Elementor\Controls_Manager::RAW_HTML,
						// 'separator' => 'before',
					]
				);

				$this->add_control(
					'title_text',
					[
						'label' => __( 'Title text', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::TEXT,
						'default' => ''
					]
				);

				$this->add_control(
					'new_chat_text',
					[
						'label' => __( 'New chat text', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::TEXT,
						'default' => ''
					]
				);

				$this->add_control(
					'placeholder_text',
					[
						'label' => __( 'Placeholder', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::TEXT,
						'default' => ''
					]
				);

				$this->add_control(
					'button_text',
					[
						'label' => __( 'Button text', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::TEXT,
						'default' => ''
					]
				);

				$this->add_control(
					'tags_position',
					[
						'label' => __( 'Tags', 'trx_addons' ),
						'label_block' => false,
						'separator' => 'before',
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => Lists::get_list_ai_chat_tags_positions(),
						'default' => 'none'
					]
				);

				$this->add_control(
					'tags_label',
					[
						'label' => __( 'Tags label', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::TEXT,
						'default' => '',
						'condition' => [
							'tags_position!' => 'none'
						]
					]
				);

				$this->add_control(
					'tags',
					[
						'label' => __( 'Tags list', 'trx_addons' ),
						'label_block' => true,
						'type' => \Elementor\Controls_Manager::REPEATER,
						'default' => apply_filters('trx_addons_sc_param_group_value', array(), 'trx_sc_chat'),
						'fields' => apply_filters('trx_addons_sc_param_group_params', [
							[
								'name' => 'title',
								'label' => __( 'Title', 'trx_addons' ),
								'label_block' => false,
								'type' => \Elementor\Controls_Manager::TEXT,
								'placeholder' => __( "Tag's title", 'trx_addons' ),
								'default' => ''
							],
							[
								'name' => 'prompt',
								'label' => __( 'Prompt', 'trx_addons' ),
								'label_block' => false,
								'type' => \Elementor\Controls_Manager::TEXT,
								'placeholder' => __( "Prompt", 'trx_addons' ),
								'default' => ''
							],
						], 'trx_sc_chat' ),
						'title_field' => '{{{ title }}}',
						'condition' => [
							'tags_position!' => 'none'
						]
					]
				);

				$this->end_controls_section();

				// Section: Chat settings
				$this->start_controls_section(
					'section_sc_chat_settings',
					[
						'label' => __( 'Chat Settings', 'trx_addons' ),
					]
				);

				$this->add_control(
					'premium',
					[
						'label' => __( 'Premium Mode', 'trx_addons' ),
						'label_block' => false,
						'description' => __( 'Enables you to set a broader range of limits for text generation, which can be used for a paid text generation service. The limits are configured in the global settings.', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'return_value' => '1',
					]
				);

				$this->add_control(
					'show_limits',
					[
						'label' => __( 'Show limits', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'return_value' => '1',
					]
				);

				$this->add_control(
					'save_history',
					[
						'label' => __( 'Remember on reload', 'trx_addons' ),
						'label_block' => false,
						'description' => __( 'Remember the chat history on page reload? Attention! To memorize chat messages, you should specify its ID on the tab "Advanced".', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'return_value' => '1',
					]
				);

				$this->add_control(
					'open_on_load',
					[
						'label' => __( 'Open on page load', 'trx_addons' ),
						'label_block' => false,
						'description' => __( 'Open a chat popup on page load.', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'return_value' => '1',
						'condition' => [
							'type' => 'popup'
						]
					]
				);

				$this->add_control(
					'model',
					[
						'label' => __( 'Model', 'trx_addons' ),
						'label_block' => false,
						'separator' => 'before',
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => $models,
						'default' => trx_addons_get_option( 'ai_helper_text_model_default', '' )
					]
				);

				$this->add_control(
					'flowise_override',
					[
						'label' => __( 'Override config JSON', 'trx_addons' ),
						'label_block' => true,
						'type' => \Elementor\Controls_Manager::TEXTAREA,
						'default' => '',
						'description' => __( 'If you want to override the default config JSON for the Flowise AI chatflow, you can do it here. The JSON should be a valid JSON object.', 'trx_addons' ),
						'condition' => [
							'model' => $models_flowise
						]
					]
				);

				$this->add_control(
					'system_prompt',
					[
						'label' => __( 'System prompt (Context)', 'trx_addons' ),
						'label_block' => true,
						'description' => __( 'These are instructions for the AI Model describing how it should generate text. If you leave this field empty - the System Prompt specified in the plugin options will be used.', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::TEXTAREA,
						'rows' => 5,
						'default' => ''
					]
				);

				$this->add_responsive_control(
					'temperature',
					[
						'label' => __( 'Temperature', 'trx_addons' ),
						'description' => __('What sampling temperature to use, between 0 and 2. Higher values like 0.8 will make the output more random, while lower values like 0.2 will make it more focused and deterministic.', 'trx_addons'),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => (float)trx_addons_get_option( 'ai_helper_sc_tgenerator_temperature' ),
							'unit' => 'px'
						],
						'size_units' => [ 'px' ],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 2,
								'step' => 0.1
							]
						],
					]
				);

				$this->add_responsive_control(
					'max_tokens',
					[
						'label' => __( 'Max. tokens per request', 'trx_addons' ),
						'description' => __('How many tokens can be used per one request to the API? If you leave this field empty - the value specified in the plugin options will be used.', 'trx_addons'),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 0,
							'unit' => 'px'
						],
						'size_units' => [ 'px' ],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => ! $is_edit_mode ? Utils::get_default_max_tokens() : Utils::get_max_tokens( 'sc_chat' ),
								'step' => 100
							]
						],
					]
				);

				$this->end_controls_section();

				$this->start_controls_section(
					'section_sc_chat_window_style',
					[
						'label' => __( 'Chat window', 'trx_addons' ),
						'tab' => \Elementor\Controls_Manager::TAB_STYLE
					]
				);

				$this->add_control(
					"chat_bg_color",
					[
						'label' => __( 'Background color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'selectors' => [
							'{{WRAPPER}} .sc_chat_content' => 'background-color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					"chat_bd_color",
					[
						'label' => __( 'Border color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'selectors' => [
							'{{WRAPPER}} .sc_chat_content' => 'border-color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'chat_bd_width',
					[
						'label' => __( 'Border width', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => '',
							'unit' => 'px'
						],
						'size_units' => [ 'px' ],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 10,
								'step' => 1
							]
						],
						'selectors' => [
							'{{WRAPPER}} .sc_chat_content' => 'border-width: {{SIZE}}{{UNIT}}; border-style: solid;',
						],
					]
				);

				$this->add_control(
					'chat_bd_radius',
					[
						'label' => __( 'Border radius', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => '',
							'unit' => 'px'
						],
						'size_units' => [ 'px' ],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 100,
								'step' => 1
							]
						],
						'selectors' => [
							'{{WRAPPER}} .sc_chat_content' => '--trx-addons-ai-helper-chat-content-border-radius: {{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Box_Shadow::get_type(),
					[
						'name' => 'chat_shadow',
				 		'label' => esc_html__( 'Shadow', 'elementor' ),
						'selector' => '{{WRAPPER}} .sc_chat_content',
					]
				);
		
				$this->add_control(
					"limits_text_color",
					[
						'label' => __( 'Limits text color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'selectors' => [
							'{{WRAPPER}} .sc_chat_limits' => 'color: {{VALUE}};',
						],
					]
				);

				$this->end_controls_section();

				$this->start_controls_section(
					'section_sc_chat_title_style',
					[
						'label' => __( 'Chat title', 'trx_addons' ),
						'tab' => \Elementor\Controls_Manager::TAB_STYLE
					]
				);

				$this->add_control(
					"title_separator",
					[
						'label' => __( 'Chat Title', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::HEADING,
						'separator' => 'before',
					]
				);

				$this->add_control(
					"title_text_color",
					[
						'label' => __( 'Text color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'selectors' => [
							'{{WRAPPER}} .sc_chat_form_field_prompt_label' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					"title_bg_color",
					[
						'label' => __( 'Background color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'selectors' => [
							'{{WRAPPER}} .sc_chat_form_field_prompt_label' => 'background-color: {{VALUE}};',
						],
					]
				);

				$params = trx_addons_get_icon_param( 'title_icon' );
				$params = trx_addons_array_get_first_value( $params );
				unset( $params['name'] );
				$this->add_control( 'title_icon', $params );

				$this->add_control(
					"title_icon_color",
					[
						'label' => __( 'Icon color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'selectors' => [
							'{{WRAPPER}} .sc_chat_form_title_icon' => 'color: {{VALUE}};',
							'{{WRAPPER}} .sc_chat_form_title svg' => 'fill: {{VALUE}};',
						],
					]
				);

				$this->add_control( 'title_image',
					[
						'label' => esc_html__( 'Image', 'elementor' ),
						'type' => \Elementor\Controls_Manager::MEDIA,
						'media_types' => [ 'image', 'svg' ],
					]
				);

				$this->add_control(
					"new_chat_separator",
					[
						'label' => __( 'Link "New chat"', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::HEADING,
						'separator' => 'before',
					]
				);

				$this->add_control(
					"new_chat_text_color",
					[
						'label' => __( 'Link color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'selectors' => [
							'{{WRAPPER}} .sc_chat_form_start_new' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					"new_chat_text_hover",
					[
						'label' => __( 'Link hover', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'selectors' => [
							'{{WRAPPER}} .sc_chat_form_start_new:hover' => 'color: {{VALUE}};',
						],
					]
				);

				$params = trx_addons_get_icon_param( 'new_chat_icon' );
				$params = trx_addons_array_get_first_value( $params );
				unset( $params['name'] );
				$this->add_control( 'new_chat_icon', $params );

				$this->add_control(
					"new_chat_icon_color",
					[
						'label' => __( 'Icon color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'selectors' => [
							'{{WRAPPER}} .sc_chat_form_start_new_icon' => 'color: {{VALUE}};',
							'{{WRAPPER}} .sc_chat_form_start_new svg' => 'fill: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					"new_chat_icon_hover",
					[
						'label' => __( 'Icon hover', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'selectors' => [
							'{{WRAPPER}} .sc_chat_form_start_new:hover .sc_chat_form_start_new_icon' => 'color: {{VALUE}};',
							'{{WRAPPER}} .sc_chat_form_start_new:hover svg' => 'fill: {{VALUE}};',
						],
					]
				);

				$this->add_control( 'new_chat_image',
					[
						'label' => esc_html__( 'Image', 'elementor' ),
						'type' => \Elementor\Controls_Manager::MEDIA,
						'media_types' => [ 'image', 'svg' ],
					]
				);

				$this->end_controls_section();

				$this->start_controls_section(
					'section_sc_chat_assistant_style',
					[
						'label' => __( 'Assistant messages', 'trx_addons' ),
						'tab' => \Elementor\Controls_Manager::TAB_STYLE
					]
				);

				// $this->add_control(
				// 	"assistant_separator",
				// 	[
				// 		'label' => __( 'Assistant message', 'trx_addons' ),
				// 		'type' => \Elementor\Controls_Manager::HEADING,
				// 		'separator' => 'before',
				// 	]
				// );

				$this->add_control(
					"assistant_text_color",
					[
						'label' => __( 'Text color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'selectors' => [
							'{{WRAPPER}} .sc_chat_list_item_assistant .sc_chat_list_item_content' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					"assistant_bg_color",
					[
						'label' => __( 'Background color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'selectors' => [
							'{{WRAPPER}} .sc_chat_list_item_assistant .sc_chat_list_item_content' => 'background-color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					"assistant_bd_color",
					[
						'label' => __( 'Border color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'selectors' => [
							'{{WRAPPER}} .sc_chat_list_item_assistant .sc_chat_list_item_content' => 'border-color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'assistant_bd_width',
					[
						'label' => __( 'Border width', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => '',
							'unit' => 'px'
						],
						'size_units' => [ 'px' ],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 10,
								'step' => 1
							]
						],
						'selectors' => [
							'{{WRAPPER}} .sc_chat_list_item_assistant .sc_chat_list_item_content' => 'border-width: {{SIZE}}{{UNIT}}; border-style: solid;',
						],
					]
				);

				$this->add_control(
					'assistant_bd_radius',
					[
						'label' => __( 'Border radius', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => '',
							'unit' => 'px'
						],
						'size_units' => [ 'px' ],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 100,
								'step' => 1
							]
						],
						'selectors' => [
							'{{WRAPPER}} .sc_chat_list_item_assistant .sc_chat_list_item_content' => '--trx-addons-ai-helper-chat-items-border-radius: {{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Box_Shadow::get_type(),
					[
						'name' => 'assistant_shadow',
				 		'label' => esc_html__( 'Shadow', 'elementor' ),
						'selector' => '{{WRAPPER}} .sc_chat_list_item_assistant .sc_chat_list_item_content,'
									. '{{WRAPPER}} .sc_chat_list_item_assistant .sc_chat_list_item_avatar',
					]
				);

				$this->add_control(
					"assistant_time_color",
					[
						'label' => __( 'Time color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'selectors' => [
							'{{WRAPPER}} .sc_chat_list_item_assistant .sc_chat_list_item_time' => 'color: {{VALUE}};',
						],
					]
				);

				$params = trx_addons_get_icon_param( 'assistant_icon' );
				$params = trx_addons_array_get_first_value( $params );
				unset( $params['name'] );
				$this->add_control( 'assistant_icon', $params );

				$this->add_control(
					'assistant_icon_size',
					[
						'label' => __( 'Icon size', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 3,
							'unit' => 'em'
						],
						'size_units' => [ 'em', 'px' ],
						'range' => [
							'em' => [
								'min' => 1,
								'max' => 10,
								'step' => 0.1
							],
							'px' => [
								'min' => 10,
								'max' => 100,
								'step' => 1
							]
						],
						'selectors' => [
							'{{WRAPPER}} .sc_chat_list_item_assistant .sc_chat_list_item_avatar' => '--trx-addons-ai-helper-chat-items-icon-size: {{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->add_control(
					"assistant_icon_color",
					[
						'label' => __( 'Icon color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'selectors' => [
							'{{WRAPPER}} .sc_chat_list_item_assistant .sc_chat_list_item_avatar' => 'color: {{VALUE}};',
							'{{WRAPPER}} .sc_chat_list_item_assistant .sc_chat_list_item_svg svg' => 'fill: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					"assistant_icon_bg_color",
					[
						'label' => __( 'Icon background color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'selectors' => [
							'{{WRAPPER}} .sc_chat_list_item_assistant .sc_chat_list_item_avatar' => 'background-color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					"assistant_icon_bd_color",
					[
						'label' => __( 'Icon border color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'selectors' => [
							'{{WRAPPER}} .sc_chat_list_item_assistant .sc_chat_list_item_avatar' => 'border-color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'assistant_icon_bd_width',
					[
						'label' => __( 'Icon border width', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => '',
							'unit' => 'px'
						],
						'size_units' => [ 'px' ],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 10,
								'step' => 1
							]
						],
						'selectors' => [
							'{{WRAPPER}} .sc_chat_list_item_assistant .sc_chat_list_item_avatar' => 'border-width: {{SIZE}}{{UNIT}}; border-style: solid;',
						],
					]
				);

				$this->add_control(
					'assistant_icon_bd_radius',
					[
						'label' => __( 'Icon border radius', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => '',
							'unit' => 'px'
						],
						'size_units' => [ 'px' ],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 100,
								'step' => 1
							]
						],
						'selectors' => [
							'{{WRAPPER}} .sc_chat_list_item_assistant .sc_chat_list_item_avatar' => '--trx-addons-ai-helper-chat-items-icon-border-radius: {{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->add_control( 'assistant_image',
					[
						'label' => esc_html__( 'Image', 'elementor' ),
						'type' => \Elementor\Controls_Manager::MEDIA,
						'media_types' => [ 'image', 'svg' ],
					]
				);

				$this->end_controls_section();

				$this->start_controls_section(
					'section_sc_chat_user_style',
					[
						'label' => __( 'User messages', 'trx_addons' ),
						'tab' => \Elementor\Controls_Manager::TAB_STYLE
					]
				);

				// $this->add_control(
				// 	"user_separator",
				// 	[
				// 		'label' => __( 'User message', 'trx_addons' ),
				// 		'type' => \Elementor\Controls_Manager::HEADING,
				// 		'separator' => 'before',
				// 	]
				// );

				$this->add_control(
					"user_text_color",
					[
						'label' => __( 'Text color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'selectors' => [
							'{{WRAPPER}} .sc_chat_list_item_user .sc_chat_list_item_content' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					"user_bg_color",
					[
						'label' => __( 'Background color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'selectors' => [
							'{{WRAPPER}} .sc_chat_list_item_user .sc_chat_list_item_content' => 'background-color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					"user_bd_color",
					[
						'label' => __( 'Border color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'selectors' => [
							'{{WRAPPER}} .sc_chat_list_item_user .sc_chat_list_item_content' => 'border-color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'user_bd_width',
					[
						'label' => __( 'Border width', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => '',
							'unit' => 'px'
						],
						'size_units' => [ 'px' ],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 10,
								'step' => 1
							]
						],
						'selectors' => [
							'{{WRAPPER}} .sc_chat_list_item_user .sc_chat_list_item_content' => 'border-width: {{SIZE}}{{UNIT}}; border-style: solid;',
						],
					]
				);

				$this->add_control(
					'user_bd_radius',
					[
						'label' => __( 'Border radius', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => '',
							'unit' => 'px'
						],
						'size_units' => [ 'px' ],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 100,
								'step' => 1
							]
						],
						'selectors' => [
							'{{WRAPPER}} .sc_chat_list_item_user .sc_chat_list_item_content' => '--trx-addons-ai-helper-chat-items-border-radius: {{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Box_Shadow::get_type(),
					[
						'name' => 'user_shadow',
				 		'label' => esc_html__( 'Shadow', 'elementor' ),
						'selector' => '{{WRAPPER}} .sc_chat_list_item_user .sc_chat_list_item_content,'
									. '{{WRAPPER}} .sc_chat_list_item_user .sc_chat_list_item_avatar',
					]
				);

				$this->add_control(
					"user_time_color",
					[
						'label' => __( 'Time color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'selectors' => [
							'{{WRAPPER}} .sc_chat_list_item_user .sc_chat_list_item_time' => 'color: {{VALUE}};',
						],
					]
				);

				$params = trx_addons_get_icon_param( 'user_icon' );
				$params = trx_addons_array_get_first_value( $params );
				unset( $params['name'] );
				$this->add_control( 'user_icon', $params );

				$this->add_control(
					'user_icon_size',
					[
						'label' => __( 'Icon size', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 3,
							'unit' => 'em'
						],
						'size_units' => [ 'em', 'px' ],
						'range' => [
							'em' => [
								'min' => 1,
								'max' => 10,
								'step' => 0.1
							],
							'px' => [
								'min' => 10,
								'max' => 100,
								'step' => 1
							]
						],
						'selectors' => [
							'{{WRAPPER}} .sc_chat_list_item_user .sc_chat_list_item_avatar' => '--trx-addons-ai-helper-chat-items-icon-size: {{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->add_control(
					"user_icon_color",
					[
						'label' => __( 'Icon color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'selectors' => [
							'{{WRAPPER}} .sc_chat_list_item_user .sc_chat_list_item_avatar' => 'color: {{VALUE}};',
							'{{WRAPPER}} .sc_chat_list_item_user .sc_chat_list_item_svg svg' => 'fill: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					"user_icon_bg_color",
					[
						'label' => __( 'Icon background color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'selectors' => [
							'{{WRAPPER}} .sc_chat_list_item_user .sc_chat_list_item_avatar' => 'background-color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					"user_icon_bd_color",
					[
						'label' => __( 'Icon border color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'selectors' => [
							'{{WRAPPER}} .sc_chat_list_item_user .sc_chat_list_item_avatar' => 'border-color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'user_icon_bd_width',
					[
						'label' => __( 'Icon border width', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => '',
							'unit' => 'px'
						],
						'size_units' => [ 'px' ],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 10,
								'step' => 1
							]
						],
						'selectors' => [
							'{{WRAPPER}} .sc_chat_list_item_user .sc_chat_list_item_avatar' => 'border-width: {{SIZE}}{{UNIT}}; border-style: solid;',
						],
					]
				);

				$this->add_control(
					'user_icon_bd_radius',
					[
						'label' => __( 'Icon border radius', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => '',
							'unit' => 'px'
						],
						'size_units' => [ 'px' ],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 100,
								'step' => 1
							]
						],
						'selectors' => [
							'{{WRAPPER}} .sc_chat_list_item_user .sc_chat_list_item_avatar' => '--trx-addons-ai-helper-chat-items-icon-border-radius: {{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->add_control( 'user_image',
					[
						'label' => esc_html__( 'Image', 'elementor' ),
						'type' => \Elementor\Controls_Manager::MEDIA,
						'media_types' => [ 'image', 'svg' ],
					]
				);

				$this->end_controls_section();

				$this->start_controls_section(
					'section_sc_chat_prompt_style',
					[
						'label' => __( 'Prompt Field', 'trx_addons' ),
						'tab' => \Elementor\Controls_Manager::TAB_STYLE
					]
				);

				// $this->add_control(
				// 	"prompt_separator",
				// 	[
				// 		'label' => __( 'Prompt field', 'trx_addons' ),
				// 		'type' => \Elementor\Controls_Manager::HEADING,
				// 		'separator' => 'before',
				// 	]
				// );

				$this->add_control(
					"prompt_text_color",
					[
						'label' => __( 'Text color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'selectors' => [
							'{{WRAPPER}} .sc_chat_form_field_prompt_text' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					"placeholder_text_color",
					[
						'label' => __( 'Placeholder color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'selectors' => [
							'{{WRAPPER}} .sc_chat_form_field_prompt_text::placeholder' => 'color: {{VALUE}};',
							'{{WRAPPER}} .sc_chat_form_field_prompt_text::-moz-placeholder' => 'color: {{VALUE}};',
							'{{WRAPPER}} .sc_chat_form_field_prompt_text::-webkit-input-placeholder' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					"prompt_bg_color",
					[
						'label' => __( 'Background color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'selectors' => [
							'{{WRAPPER}} .sc_chat_form_field_prompt_text' => 'background-color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					"prompt_bd_color",
					[
						'label' => __( 'Border color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'selectors' => [
							'{{WRAPPER}} .sc_chat_form_field_prompt_text' => 'border-color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'prompt_bd_width',
					[
						'label' => __( 'Border width', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => '',
							'unit' => 'px'
						],
						'size_units' => [ 'px' ],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 10,
								'step' => 1
							]
						],
						'selectors' => [
							'{{WRAPPER}} .sc_chat_form_field_prompt_text' => 'border-width: {{SIZE}}{{UNIT}}; border-style: solid;',
						],
					]
				);

				$this->add_control(
					'prompt_bd_radius',
					[
						'label' => __( 'Border radius', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => '',
							'unit' => 'px'
						],
						'size_units' => [ 'px' ],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 100,
								'step' => 1
							]
						],
						'selectors' => [
							'{{WRAPPER}} .sc_chat_form_field_prompt_text' => '--trx-addons-ai-helper-chat-fields-border-radius: {{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Box_Shadow::get_type(),
					[
						'name' => 'prompt_shadow',
				 		'label' => esc_html__( 'Shadow', 'elementor' ),
						'selector' => '{{WRAPPER}} .sc_chat_form_field_prompt_text',
					]
				);

				$this->end_controls_section();


				$this->start_controls_section(
					'section_sc_chat_tags_style',
					[
						'label' => __( 'Tags', 'trx_addons' ),
						'tab' => \Elementor\Controls_Manager::TAB_STYLE
					]
				);

				$this->add_control(
					"tags_regular_separator",
					[
						'label' => __( 'Regular state', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::HEADING,
//						'separator' => 'before',
					]
				);

				$this->add_control(
					"tags_text_color",
					[
						'label' => __( 'Text color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'selectors' => [
							'{{WRAPPER}} .sc_chat_form_field_tags_item' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					"tags_bg_color",
					[
						'label' => __( 'Background Color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'selectors' => [
							'{{WRAPPER}} .sc_chat_form_field_tags_item' => 'background-color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					"tags_bd_color",
					[
						'label' => __( 'Border Color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'selectors' => [
							'{{WRAPPER}} .sc_chat_form_field_tags_item' => 'border-color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'tags_bd_width',
					[
						'label' => __( 'Border width', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => '',
							'unit' => 'px'
						],
						'size_units' => [ 'px' ],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 10,
								'step' => 1
							]
						],
						'selectors' => [
							'{{WRAPPER}} .sc_chat_form_field_tags_item' => 'border-width: {{SIZE}}{{UNIT}}; border-style: solid;',
						],
					]
				);

				$this->add_control(
					'tags_bd_radius',
					[
						'label' => __( 'Border radius', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => '',
							'unit' => 'px'
						],
						'size_units' => [ 'px' ],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 100,
								'step' => 1
							]
						],
						'selectors' => [
							'{{WRAPPER}} .sc_chat_form_field_tags_item' => '--trx-addons-ai-helper-chat-button-border-radius: {{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Box_Shadow::get_type(),
					[
						'name' => 'tags_shadow',
				 		'label' => esc_html__( 'Shadow', 'elementor' ),
						'selector' => '{{WRAPPER}} .sc_chat_form_field_tags_item',
					]
				);

				$this->add_control(
					"tags_hover_separator",
					[
						'label' => __( 'Hover state', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::HEADING,
						'separator' => 'before',
					]
				);

				$this->add_control(
					"tags_text_hover",
					[
						'label' => __( 'Text color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'selectors' => [
							'{{WRAPPER}} .sc_chat_form_field_tags_item:hover' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					"tags_bg_hover",
					[
						'label' => __( 'Background Color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'selectors' => [
							'{{WRAPPER}} .sc_chat_form_field_tags_item:hover' => 'background-color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					"tags_bd_hover",
					[
						'label' => __( 'Border Color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'selectors' => [
							'{{WRAPPER}} .sc_chat_form_field_tags_item:hover' => 'border-color: {{VALUE}};',
						],
					]
				);

				$this->end_controls_section();

				$this->start_controls_section(
					'section_sc_chat_prompt_button_style',
					[
						'label' => __( 'Button "Send"', 'trx_addons' ),
						'tab' => \Elementor\Controls_Manager::TAB_STYLE
					]
				);

				$this->add_control(
					"button_regular_separator",
					[
						'label' => __( 'Regular state', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::HEADING,
//						'separator' => 'before',
					]
				);

				$this->add_control(
					"button_text_color",
					[
						'label' => __( 'Text color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'selectors' => [
							'{{WRAPPER}} .sc_chat_form_field_prompt_button' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					"button_bg_color",
					[
						'label' => __( 'Background Color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'selectors' => [
							'{{WRAPPER}} .sc_chat_form_field_prompt_button' => 'background-color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					"button_bd_color",
					[
						'label' => __( 'Border Color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'selectors' => [
							'{{WRAPPER}} .sc_chat_form_field_prompt_button' => 'border-color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'button_bd_width',
					[
						'label' => __( 'Border width', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => '',
							'unit' => 'px'
						],
						'size_units' => [ 'px' ],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 10,
								'step' => 1
							]
						],
						'selectors' => [
							'{{WRAPPER}} .sc_chat_form_field_prompt_button' => 'border-width: {{SIZE}}{{UNIT}}; border-style: solid;',
						],
					]
				);

				$this->add_control(
					'button_bd_radius',
					[
						'label' => __( 'Border radius', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => '',
							'unit' => 'px'
						],
						'size_units' => [ 'px' ],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 100,
								'step' => 1
							]
						],
						'selectors' => [
							'{{WRAPPER}} .sc_chat_form_field_prompt_button' => '--trx-addons-ai-helper-chat-button-border-radius: {{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Box_Shadow::get_type(),
					[
						'name' => 'button_shadow',
				 		'label' => esc_html__( 'Shadow', 'elementor' ),
						'selector' => '{{WRAPPER}} .sc_chat_form_field_prompt_button',
					]
				);

				$params = trx_addons_get_icon_param( 'button_icon' );
				$params = trx_addons_array_get_first_value( $params );
				unset( $params['name'] );
				$this->add_control( 'button_icon', $params );

				$this->add_control(
					"button_icon_color",
					[
						'label' => __( 'Icon color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'selectors' => [
							'{{WRAPPER}} .sc_chat_form_field_prompt_button .sc_chat_form_field_prompt_button_icon' => 'color: {{VALUE}};',
							'{{WRAPPER}} .sc_chat_form_field_prompt_button .sc_chat_form_field_prompt_button_svg svg' => 'fill: {{VALUE}};',
						],
					]
				);

				$this->add_control( 'button_image',
					[
						'label' => esc_html__( 'Image', 'elementor' ),
						'type' => \Elementor\Controls_Manager::MEDIA,
						'media_types' => [ 'image', 'svg' ],
					]
				);

				$this->add_control(
					"button_hover_separator",
					[
						'label' => __( 'Hover state', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::HEADING,
						'separator' => 'before',
					]
				);

				$this->add_control(
					"button_text_hover",
					[
						'label' => __( 'Text color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'selectors' => [
							'{{WRAPPER}} .sc_chat_form_field_prompt_button:not(.sc_chat_form_field_prompt_button_disabled):hover' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					"button_icon_hover",
					[
						'label' => __( 'Icon color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'selectors' => [
							'{{WRAPPER}} .sc_chat_form_field_prompt_button:not(.sc_chat_form_field_prompt_button_disabled):hover .sc_chat_form_field_prompt_button_icon' => 'color: {{VALUE}};',
							'{{WRAPPER}} .sc_chat_form_field_prompt_button:not(.sc_chat_form_field_prompt_button_disabled):hover .sc_chat_form_field_prompt_button_svg svg' => 'fill: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					"button_bg_hover",
					[
						'label' => __( 'Background Color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'selectors' => [
							'{{WRAPPER}} .sc_chat_form_field_prompt_button:not(.sc_chat_form_field_prompt_button_disabled):hover' => 'background-color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					"button_bd_hover",
					[
						'label' => __( 'Border Color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'selectors' => [
							'{{WRAPPER}} .sc_chat_form_field_prompt_button:not(.sc_chat_form_field_prompt_button_disabled):hover' => 'border-color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					"button_disabled_separator",
					[
						'label' => __( 'Disabled state', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::HEADING,
						'separator' => 'before',
					]
				);

				$this->add_control(
					"button_text_disabled",
					[
						'label' => __( 'Text color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'selectors' => [
							'{{WRAPPER}} .sc_chat_form_field_prompt_button_disabled' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					"button_icon_disabled",
					[
						'label' => __( 'Icon color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'selectors' => [
							'{{WRAPPER}} .sc_chat_form_field_prompt_button_disabled .sc_chat_form_field_prompt_button_icon' => 'color: {{VALUE}};',
							'{{WRAPPER}} .sc_chat_form_field_prompt_button_disabled .sc_chat_form_field_prompt_button_svg svg' => 'fill: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					"button_bg_disabled",
					[
						'label' => __( 'Background Color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'selectors' => [
							'{{WRAPPER}} .sc_chat_form_field_prompt_button_disabled' => 'background-color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					"button_bd_disabled",
					[
						'label' => __( 'Border Color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'selectors' => [
							'{{WRAPPER}} .sc_chat_form_field_prompt_button_disabled' => 'border-color: {{VALUE}};',
						],
					]
				);

				$this->end_controls_section();

				$this->start_controls_section(
					'section_sc_chat_popup_button_style',
					[
						'label' => __( 'Popup Button', 'trx_addons' ),
						'tab' => \Elementor\Controls_Manager::TAB_STYLE
					]
				);

				$this->add_control(
					"popup_button_regular_separator",
					[
						'label' => __( 'Regular state', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::HEADING,
//						'separator' => 'before',
					]
				);

				$this->add_control(
					'popup_button_size',
					[
						'label' => __( 'Button size', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 3,
							'unit' => 'em'
						],
						'size_units' => [ 'em', 'px' ],
						'range' => [
							'em' => [
								'min' => 1,
								'max' => 10,
								'step' => 0.1
							],
							'px' => [
								'min' => 10,
								'max' => 100,
								'step' => 1
							]
						],
						'selectors' => [
							'{{WRAPPER}} .sc_chat_popup_button' => '--trx-addons-ai-helper-popup-button-size: {{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->add_control(
					"popup_button_bg_color",
					[
						'label' => __( 'Background Color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'selectors' => [
							'{{WRAPPER}} .sc_chat_popup_button' => 'background-color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					"popup_button_bd_color",
					[
						'label' => __( 'Border Color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'selectors' => [
							'{{WRAPPER}} .sc_chat_popup_button' => 'border-color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'popup_button_bd_width',
					[
						'label' => __( 'Border width', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => '',
							'unit' => 'px'
						],
						'size_units' => [ 'px' ],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 10,
								'step' => 1
							]
						],
						'selectors' => [
							'{{WRAPPER}} .sc_chat_popup_button' => 'border-width: {{SIZE}}{{UNIT}}; border-style: solid;',
						],
					]
				);

				$this->add_control(
					'popup_button_bd_radius',
					[
						'label' => __( 'Border radius', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => '50',
							'unit' => 'px'
						],
						'size_units' => [ 'px' ],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 100,
								'step' => 1
							]
						],
						'selectors' => [
							'{{WRAPPER}} .sc_chat_popup_button' => '--trx-addons-ai-helper-popup-button-border-radius: {{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Box_Shadow::get_type(),
					[
						'name' => 'popup_button_shadow',
				 		'label' => esc_html__( 'Shadow', 'elementor' ),
						'selector' => '{{WRAPPER}} .sc_chat_popup_button',
					]
				);

				$params = trx_addons_get_icon_param( 'popup_button_icon' );
				$params = trx_addons_array_get_first_value( $params );
				unset( $params['name'] );
				$this->add_control( 'popup_button_icon', $params );

				$this->add_control(
					"popup_button_icon_color",
					[
						'label' => __( 'Icon color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'selectors' => [
							'{{WRAPPER}} .sc_chat_popup_button .sc_chat_popup_button_icon' => 'color: {{VALUE}};',
							'{{WRAPPER}} .sc_chat_popup_button .sc_chat_popup_button_svg svg' => 'fill: {{VALUE}};',
						],
					]
				);

				$this->add_control( 'popup_button_image',
					[
						'label' => esc_html__( 'Image', 'elementor' ),
						'type' => \Elementor\Controls_Manager::MEDIA,
						'media_types' => [ 'image', 'svg' ],
					]
				);

				$this->add_control(
					"popup_button_hover_separator",
					[
						'label' => __( 'Hover state', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::HEADING,
						'separator' => 'before',
					]
				);

				$this->add_control(
					"popup_button_bg_hover",
					[
						'label' => __( 'Background Color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'selectors' => [
							'{{WRAPPER}} .sc_chat_popup_button:hover' => 'background-color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					"popup_button_bd_hover",
					[
						'label' => __( 'Border Color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'selectors' => [
							'{{WRAPPER}} .sc_chat_popup_button:hover' => 'border-color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					"popup_button_icon_hover",
					[
						'label' => __( 'Icon color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'selectors' => [
							'{{WRAPPER}} .sc_chat_popup_button:hover .sc_chat_popup_button_icon' => 'color: {{VALUE}};',
							'{{WRAPPER}} .sc_chat_popup_button:hover .sc_chat_popup_button_svg svg' => 'fill: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					"popup_button_opened_separator",
					[
						'label' => __( 'Opened state', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::HEADING,
						'separator' => 'before',
					]
				);

				$params = trx_addons_get_icon_param( 'popup_button_icon_opened' );
				$params = trx_addons_array_get_first_value( $params );
				unset( $params['name'] );
				$this->add_control( 'popup_button_icon_opened', $params );

				$this->add_control( 'popup_button_image_opened',
					[
						'label' => esc_html__( 'Image', 'elementor' ),
						'type' => \Elementor\Controls_Manager::MEDIA,
						'media_types' => [ 'image', 'svg' ],
					]
				);

				$this->end_controls_section();

				$this->add_title_param();
			}

			/**
			 * Render widget's template for the editor.
			 *
			 * Written as a Backbone JavaScript template and used to generate the live preview.
			 *
			 * @since 1.6.41
			 * @access protected
			 */
			protected function content_template() {
				if ( ! Utils::is_chat_api_available() ) {
					trx_addons_get_template_part( 'templates/tpe.sc_placeholder.php',
						'trx_addons_args_sc_placeholder',
						apply_filters( 'trx_addons_filter_sc_placeholder_args', array(
							'sc' => 'trx_sc_chat',
							'title' => __('AI Chat is not available - token for access to the API for text generation is not specified', 'trx_addons'),
							'class' => 'sc_placeholder_with_title'
						) )
					);
				} else {
					trx_addons_get_template_part(TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/chat/tpe.chat.php',
						'trx_addons_args_sc_chat',
						array('element' => $this)
					);
				}
			}
		}
		
		// Register widget
		trx_addons_elm_register_widget( 'TRX_Addons_Elementor_Widget_Chat' );
	}
}
