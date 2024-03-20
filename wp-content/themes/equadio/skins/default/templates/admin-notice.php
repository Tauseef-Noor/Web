<?php
/**
 * The template to display Admin notices
 *
 * @package EQUADIO
 * @since EQUADIO 1.0.1
 */

$equadio_theme_slug = get_option( 'template' );
$equadio_theme_obj  = wp_get_theme( $equadio_theme_slug );
?>
<div class="equadio_admin_notice equadio_welcome_notice update-nag">
	<?php
	// Theme image
	$equadio_theme_img = equadio_get_file_url( 'screenshot.jpg' );
	if ( '' != $equadio_theme_img ) {
		?>
		<div class="equadio_notice_image"><img src="<?php echo esc_url( $equadio_theme_img ); ?>" alt="<?php esc_attr_e( 'Theme screenshot', 'equadio' ); ?>"></div>
		<?php
	}

	// Title
	?>
	<h3 class="equadio_notice_title">
		<?php
		echo esc_html(
			sprintf(
				// Translators: Add theme name and version to the 'Welcome' message
				__( 'Welcome to %1$s v.%2$s', 'equadio' ),
				$equadio_theme_obj->get( 'Name' ) . ( EQUADIO_THEME_FREE ? ' ' . __( 'Free', 'equadio' ) : '' ),
				$equadio_theme_obj->get( 'Version' )
			)
		);
		?>
	</h3>
	<?php

	// Description
	?>
	<div class="equadio_notice_text">
		<p class="equadio_notice_text_description">
			<?php
			echo str_replace( '. ', '.<br>', wp_kses_data( $equadio_theme_obj->description ) );
			?>
		</p>
		<p class="equadio_notice_text_info">
			<?php
			echo wp_kses_data( __( 'Attention! Plugin "ThemeREX Addons" is required! Please, install and activate it!', 'equadio' ) );
			?>
		</p>
	</div>
	<?php

	// Buttons
	?>
	<div class="equadio_notice_buttons">
		<?php
		// Link to the page 'About Theme'
		?>
		<a href="<?php echo esc_url( admin_url() . 'themes.php?page=equadio_about' ); ?>" class="button button-primary"><i class="dashicons dashicons-nametag"></i> 
			<?php
			echo esc_html__( 'Install plugin "ThemeREX Addons"', 'equadio' );
			?>
		</a>
		<?php		
		// Dismiss this notice
		?>
		<a href="#" data-notice="admin" class="equadio_hide_notice"><i class="dashicons dashicons-dismiss"></i> <span class="equadio_hide_notice_text"><?php esc_html_e( 'Dismiss', 'equadio' ); ?></span></a>
	</div>
</div>
