<?php
/**
 * The template to show mobile menu (used only header_style == 'default')
 *
 * @package EQUADIO
 * @since EQUADIO 1.0
 */
?>
<div class="menu_mobile_overlay"></div>
<div class="menu_mobile menu_mobile_<?php echo esc_attr( equadio_get_theme_option( 'menu_mobile_fullscreen' ) > 0 ? 'fullscreen' : 'narrow' ); ?> scheme_dark">
	<div class="menu_mobile_inner">
		<a class="menu_mobile_close theme_button_close" tabindex="0"><span class="theme_button_close_icon"></span></a>
		<?php

		// Logo
		set_query_var( 'equadio_logo_args', array( 'type' => 'mobile' ) );
		get_template_part( apply_filters( 'equadio_filter_get_template_part', 'templates/header-logo' ) );
		set_query_var( 'equadio_logo_args', array() );

		// Mobile menu
		$equadio_menu_mobile = equadio_get_nav_menu( 'menu_mobile' );
		if ( empty( $equadio_menu_mobile ) ) {
			$equadio_menu_mobile = apply_filters( 'equadio_filter_get_mobile_menu', '' );
			if ( empty( $equadio_menu_mobile ) ) {
				$equadio_menu_mobile = equadio_get_nav_menu( 'menu_main' );
				if ( empty( $equadio_menu_mobile ) ) {
					$equadio_menu_mobile = equadio_get_nav_menu();
				}
			}
		}
		if ( ! empty( $equadio_menu_mobile ) ) {
			$equadio_menu_mobile = str_replace(
				array( 'menu_main',   'id="menu-',        'sc_layouts_menu_nav', 'sc_layouts_menu ', 'sc_layouts_hide_on_mobile', 'hide_on_mobile' ),
				array( 'menu_mobile', 'id="menu_mobile-', '',                    ' ',                '',                          '' ),
				$equadio_menu_mobile
			);
			if ( strpos( $equadio_menu_mobile, '<nav ' ) === false ) {
				$equadio_menu_mobile = sprintf( '<nav class="menu_mobile_nav_area" itemscope="itemscope" itemtype="%1$s//schema.org/SiteNavigationElement">%2$s</nav>', esc_attr( equadio_get_protocol( true ) ), $equadio_menu_mobile );
			}
			equadio_show_layout( apply_filters( 'equadio_filter_menu_mobile_layout', $equadio_menu_mobile ) );
		}

		// Search field
		do_action(
			'equadio_action_search',
			array(
				'style' => 'normal',
				'class' => 'search_mobile',
				'ajax'  => false
			)
		);

		// Social icons
		equadio_show_layout( equadio_get_socials_links(), '<div class="socials_mobile">', '</div>' );
		?>
	</div>
</div>
