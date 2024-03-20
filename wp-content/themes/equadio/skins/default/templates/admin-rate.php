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
<div class="equadio_admin_notice equadio_rate_notice update-nag">
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
	<h3 class="equadio_notice_title"><a href="<?php echo esc_url( equadio_storage_get( 'theme_rate_url' ) ); ?>" target="_blank">
		<?php
		echo esc_html(
			sprintf(
				// Translators: Add theme name and version to the 'Welcome' message
				__( 'Rate our theme "%s", please', 'equadio' ),
				$equadio_theme_obj->get( 'Name' ) . ( EQUADIO_THEME_FREE ? ' ' . __( 'Free', 'equadio' ) : '' )
			)
		);
		?>
	</a></h3>
	<?php

	// Description
	?>
	<div class="equadio_notice_text">
		<p><?php echo wp_kses_data( __( "We are glad you chose our WP theme for your website. You've done well customizing your website and we hope that you've enjoyed working with our theme.", 'equadio' ) ); ?></p>
		<p><?php echo wp_kses_data( __( "It would be just awesome if you spend just a minute of your time to rate our theme or the customer service you've received from us.", 'equadio' ) ); ?></p>
		<p class="equadio_notice_text_info"><?php echo wp_kses_data( __( '* We love receiving your reviews! Every time you leave a review, our CEO Henry Rise gives $5 to homeless dog shelter! Save the planet with us!', 'equadio' ) ); ?></p>
	</div>
	<?php

	// Buttons
	?>
	<div class="equadio_notice_buttons">
		<?php
		// Link to the theme download page
		?>
		<a href="<?php echo esc_url( equadio_storage_get( 'theme_rate_url' ) ); ?>" class="button button-primary" target="_blank"><i class="dashicons dashicons-star-filled"></i> 
			<?php
			// Translators: Add theme name
			echo esc_html( sprintf( __( 'Rate theme %s', 'equadio' ), $equadio_theme_obj->name ) );
			?>
		</a>
		<?php
		// Link to the theme support
		?>
		<a href="<?php echo esc_url( equadio_storage_get( 'theme_support_url' ) ); ?>" class="button" target="_blank"><i class="dashicons dashicons-sos"></i> 
			<?php
			esc_html_e( 'Support', 'equadio' );
			?>
		</a>
		<?php
		// Link to the theme documentation
		?>
		<a href="<?php echo esc_url( equadio_storage_get( 'theme_doc_url' ) ); ?>" class="button" target="_blank"><i class="dashicons dashicons-book"></i> 
			<?php
			esc_html_e( 'Documentation', 'equadio' );
			?>
		</a>
		<?php
		// Dismiss
		?>
		<a href="#" data-notice="rate" class="equadio_hide_notice"><i class="dashicons dashicons-dismiss"></i> <span class="equadio_hide_notice_text"><?php esc_html_e( 'Dismiss', 'equadio' ); ?></span></a>
	</div>
</div>
