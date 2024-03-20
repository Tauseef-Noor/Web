<?php
/**
 * The Header: Logo and main menu
 *
 * @package EQUADIO
 * @since EQUADIO 1.0
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js
									<?php
										// Class scheme_xxx need in the <html> as context for the <body>!
										echo ' scheme_' . esc_attr( equadio_get_theme_option( 'color_scheme' ) );
									?>
										">
<head>
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

	<?php
	if ( function_exists( 'wp_body_open' ) ) {
		wp_body_open();
	} else {
		do_action( 'wp_body_open' );
	}
	do_action( 'equadio_action_before_body' );
	?>

	<div class="body_wrap">

		<div class="page_wrap">
			
			<?php
			$equadio_full_post_loading = ( is_singular( 'post' ) || is_singular( 'attachment' ) ) && equadio_get_value_gp( 'action' ) == 'full_post_loading';
			$equadio_prev_post_loading = ( is_singular( 'post' ) || is_singular( 'attachment' ) ) && equadio_get_value_gp( 'action' ) == 'prev_post_loading';

			// Don't display the header elements while actions 'full_post_loading' and 'prev_post_loading'
			if ( ! $equadio_full_post_loading && ! $equadio_prev_post_loading ) {

				// Short links to fast access to the content, sidebar and footer from the keyboard
				?>
				<a class="equadio_skip_link skip_to_content_link" href="#content_skip_link_anchor" tabindex="1"><?php esc_html_e( "Skip to content", 'equadio' ); ?></a>
				<?php if ( equadio_sidebar_present() ) { ?>
				<a class="equadio_skip_link skip_to_sidebar_link" href="#sidebar_skip_link_anchor" tabindex="1"><?php esc_html_e( "Skip to sidebar", 'equadio' ); ?></a>
				<?php } ?>
				<a class="equadio_skip_link skip_to_footer_link" href="#footer_skip_link_anchor" tabindex="1"><?php esc_html_e( "Skip to footer", 'equadio' ); ?></a>
				
				<?php
				do_action( 'equadio_action_before_header' );

				// Header
				$equadio_header_type = equadio_get_theme_option( 'header_type' );
				if ( 'custom' == $equadio_header_type && ! equadio_is_layouts_available() ) {
					$equadio_header_type = 'default';
				}
				get_template_part( apply_filters( 'equadio_filter_get_template_part', "templates/header-" . sanitize_file_name( $equadio_header_type ) ) );

				// Side menu
				if ( in_array( equadio_get_theme_option( 'menu_side' ), array( 'left', 'right' ) ) ) {
					get_template_part( apply_filters( 'equadio_filter_get_template_part', 'templates/header-navi-side' ) );
				}

				// Mobile menu
				get_template_part( apply_filters( 'equadio_filter_get_template_part', 'templates/header-navi-mobile' ) );

				do_action( 'equadio_action_after_header' );

			}
			?>

			<div class="page_content_wrap"<?php
				if ( $equadio_prev_post_loading ) {
					?> data-single-style="<?php echo esc_attr( equadio_get_theme_option( 'single_style' ) ); ?>"<?php
				}
			?>>
				<?php
				do_action( 'equadio_action_page_content_wrap', $equadio_full_post_loading || $equadio_prev_post_loading );

				// Single posts banner
				if ( apply_filters( 'equadio_filter_single_post_header', is_singular( 'post' ) || is_singular( 'attachment' ) ) ) {
					if ( $equadio_prev_post_loading ) {
						if ( equadio_get_theme_option( 'posts_navigation_scroll_which_block' ) != 'article' ) {
							do_action( 'equadio_action_between_posts' );
						}
					}
					// Single post thumbnail and title
					$equadio_path = apply_filters( 'equadio_filter_get_template_part', 'templates/single-styles/' . equadio_get_theme_option( 'single_style' ) );
					if ( equadio_get_file_dir( $equadio_path . '.php' ) != '' ) {
						get_template_part( $equadio_path );
					}
				}

				// Widgets area above page content
				$equadio_body_style   = equadio_get_theme_option( 'body_style' );
				$equadio_widgets_name = equadio_get_theme_option( 'widgets_above_page' );
				$equadio_show_widgets = ! equadio_is_off( $equadio_widgets_name ) && is_active_sidebar( $equadio_widgets_name );
				if ( $equadio_show_widgets ) {
					if ( 'fullscreen' != $equadio_body_style ) {
						?>
						<div class="content_wrap">
							<?php
					}
					equadio_create_widgets_area( 'widgets_above_page' );
					if ( 'fullscreen' != $equadio_body_style ) {
						?>
						</div><!-- </.content_wrap> -->
						<?php
					}
				}

				// Content area
				?>
				<div class="content_wrap<?php echo 'fullscreen' == $equadio_body_style ? '_fullscreen' : ''; ?>">

					<div class="content">
						<?php
						// Skip link anchor to fast access to the content from keyboard
						?>
						<a id="content_skip_link_anchor" class="equadio_skip_link_anchor" href="#"></a>
						<?php
						// Single posts banner between prev/next posts
						if ( ( is_singular( 'post' ) || is_singular( 'attachment' ) )
							&& $equadio_prev_post_loading 
							&& equadio_get_theme_option( 'posts_navigation_scroll_which_block' ) == 'article'
						) {
							do_action( 'equadio_action_between_posts' );
						}

						// Widgets area inside page content
						equadio_create_widgets_area( 'widgets_above_content' );
