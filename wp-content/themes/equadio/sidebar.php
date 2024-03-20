<?php
/**
 * The Sidebar containing the main widget areas.
 *
 * @package EQUADIO
 * @since EQUADIO 1.0
 */

if ( equadio_sidebar_present() ) {
	
	$equadio_sidebar_type = equadio_get_theme_option( 'sidebar_type' );
	if ( 'custom' == $equadio_sidebar_type && ! equadio_is_layouts_available() ) {
		$equadio_sidebar_type = 'default';
	}
	
	// Catch output to the buffer
	ob_start();
	if ( 'default' == $equadio_sidebar_type ) {
		// Default sidebar with widgets
		$equadio_sidebar_name = equadio_get_theme_option( 'sidebar_widgets' );
		equadio_storage_set( 'current_sidebar', 'sidebar' );
		if ( is_active_sidebar( $equadio_sidebar_name ) ) {
			dynamic_sidebar( $equadio_sidebar_name );
		}
	} else {
		// Custom sidebar from Layouts Builder
		$equadio_sidebar_id = equadio_get_custom_sidebar_id();
		do_action( 'equadio_action_show_layout', $equadio_sidebar_id );
	}
	$equadio_out = trim( ob_get_contents() );
	ob_end_clean();
	
	// If any html is present - display it
	if ( ! empty( $equadio_out ) ) {
		$equadio_sidebar_position    = equadio_get_theme_option( 'sidebar_position' );
		$equadio_sidebar_position_ss = equadio_get_theme_option( 'sidebar_position_ss' );
		?>
		<div class="sidebar widget_area
			<?php
			echo ' ' . esc_attr( $equadio_sidebar_position );
			echo ' sidebar_' . esc_attr( $equadio_sidebar_position_ss );
			echo ' sidebar_' . esc_attr( $equadio_sidebar_type );

			if ( 'float' == $equadio_sidebar_position_ss ) {
				echo ' sidebar_float';
			}
			$equadio_sidebar_scheme = equadio_get_theme_option( 'sidebar_scheme' );
			if ( ! empty( $equadio_sidebar_scheme ) && ! equadio_is_inherit( $equadio_sidebar_scheme ) ) {
				echo ' scheme_' . esc_attr( $equadio_sidebar_scheme );
			}
			?>
		" role="complementary">
			<?php

			// Skip link anchor to fast access to the sidebar from keyboard
			?>
			<a id="sidebar_skip_link_anchor" class="equadio_skip_link_anchor" href="#"></a>
			<?php

			do_action( 'equadio_action_before_sidebar_wrap', 'sidebar' );

			// Button to show/hide sidebar on mobile
			if ( in_array( $equadio_sidebar_position_ss, array( 'above', 'float' ) ) ) {
				$equadio_title = apply_filters( 'equadio_filter_sidebar_control_title', 'float' == $equadio_sidebar_position_ss ? esc_html__( 'Show Sidebar', 'equadio' ) : '' );
				$equadio_text  = apply_filters( 'equadio_filter_sidebar_control_text', 'above' == $equadio_sidebar_position_ss ? esc_html__( 'Show Sidebar', 'equadio' ) : '' );
				?>
				<a href="#" class="sidebar_control" title="<?php echo esc_attr( $equadio_title ); ?>"><?php echo esc_html( $equadio_text ); ?></a>
				<?php
			}
			?>
			<div class="sidebar_inner">
				<?php
				do_action( 'equadio_action_before_sidebar', 'sidebar' );
				equadio_show_layout( preg_replace( "/<\/aside>[\r\n\s]*<aside/", '</aside><aside', $equadio_out ) );
				do_action( 'equadio_action_after_sidebar', 'sidebar' );
				?>
			</div><!-- /.sidebar_inner -->
			<?php

			do_action( 'equadio_action_after_sidebar_wrap', 'sidebar' );

			?>
		</div><!-- /.sidebar -->
		<div class="clearfix"></div>
		<?php
	}
}
