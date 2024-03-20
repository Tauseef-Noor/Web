<?php
/**
 * The template to display Admin notices
 *
 * @package EQUADIO
 * @since EQUADIO 1.0.64
 */

$equadio_skins_url  = get_admin_url( null, 'admin.php?page=trx_addons_theme_panel#trx_addons_theme_panel_section_skins' );
$equadio_skins_args = get_query_var( 'equadio_skins_notice_args' );

?>
<div class="equadio_admin_notice equadio_skins_notice update-nag">
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
		<?php esc_html_e( 'New skins available', 'equadio' ); ?>
	</h3>
	<?php

	// Description
	$equadio_total      = $equadio_skins_args['update'];	// Store value to the separate variable to avoid warnings from ThemeCheck plugin!
	$equadio_skins_msg  = $equadio_total > 0
							// Translators: Add new skins number
							? '<strong>' . sprintf( _n( '%d new version', '%d new versions', $equadio_total, 'equadio' ), $equadio_total ) . '</strong>'
							: '';
	$equadio_total      = $equadio_skins_args['free'];
	$equadio_skins_msg .= $equadio_total > 0
							? ( ! empty( $equadio_skins_msg ) ? ' ' . esc_html__( 'and', 'equadio' ) . ' ' : '' )
								// Translators: Add new skins number
								. '<strong>' . sprintf( _n( '%d free skin', '%d free skins', $equadio_total, 'equadio' ), $equadio_total ) . '</strong>'
							: '';
	$equadio_total      = $equadio_skins_args['pay'];
	$equadio_skins_msg .= $equadio_skins_args['pay'] > 0
							? ( ! empty( $equadio_skins_msg ) ? ' ' . esc_html__( 'and', 'equadio' ) . ' ' : '' )
								// Translators: Add new skins number
								. '<strong>' . sprintf( _n( '%d paid skin', '%d paid skins', $equadio_total, 'equadio' ), $equadio_total ) . '</strong>'
							: '';
	?>
	<div class="equadio_notice_text">
		<p>
			<?php
			// Translators: Add new skins info
			echo wp_kses_data( sprintf( __( "We are pleased to announce that %s are available for your theme", 'equadio' ), $equadio_skins_msg ) );
			?>
		</p>
	</div>
	<?php

	// Buttons
	?>
	<div class="equadio_notice_buttons">
		<?php
		// Link to the theme dashboard page
		?>
		<a href="<?php echo esc_url( $equadio_skins_url ); ?>" class="button button-primary"><i class="dashicons dashicons-update"></i> 
			<?php
			// Translators: Add theme name
			esc_html_e( 'Go to Skins manager', 'equadio' );
			?>
		</a>
		<?php
		// Dismiss
		?>
		<a href="#" data-notice="skins" class="equadio_hide_notice"><i class="dashicons dashicons-dismiss"></i> <span class="equadio_hide_notice_text"><?php esc_html_e( 'Dismiss', 'equadio' ); ?></span></a>
	</div>
</div>
