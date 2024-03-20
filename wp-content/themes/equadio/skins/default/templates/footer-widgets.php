<?php
/**
 * The template to display the widgets area in the footer
 *
 * @package EQUADIO
 * @since EQUADIO 1.0.10
 */

// Footer sidebar
$equadio_footer_name    = equadio_get_theme_option( 'footer_widgets' );
$equadio_footer_present = ! equadio_is_off( $equadio_footer_name ) && is_active_sidebar( $equadio_footer_name );
if ( $equadio_footer_present ) {
	equadio_storage_set( 'current_sidebar', 'footer' );
	$equadio_footer_wide = equadio_get_theme_option( 'footer_wide' );
	ob_start();
	if ( is_active_sidebar( $equadio_footer_name ) ) {
		dynamic_sidebar( $equadio_footer_name );
	}
	$equadio_out = trim( ob_get_contents() );
	ob_end_clean();
	if ( ! empty( $equadio_out ) ) {
		$equadio_out          = preg_replace( "/<\\/aside>[\r\n\s]*<aside/", '</aside><aside', $equadio_out );
		$equadio_need_columns = true;   //or check: strpos($equadio_out, 'columns_wrap')===false;
		if ( $equadio_need_columns ) {
			$equadio_columns = max( 0, (int) equadio_get_theme_option( 'footer_columns' ) );			
			if ( 0 == $equadio_columns ) {
				$equadio_columns = min( 4, max( 1, equadio_tags_count( $equadio_out, 'aside' ) ) );
			}
			if ( $equadio_columns > 1 ) {
				$equadio_out = preg_replace( '/<aside([^>]*)class="widget/', '<aside$1class="column-1_' . esc_attr( $equadio_columns ) . ' widget', $equadio_out );
			} else {
				$equadio_need_columns = false;
			}
		}
		?>
		<div class="footer_widgets_wrap widget_area<?php echo ! empty( $equadio_footer_wide ) ? ' footer_fullwidth' : ''; ?> sc_layouts_row sc_layouts_row_type_normal">
			<?php do_action( 'equadio_action_before_sidebar_wrap', 'footer' ); ?>
			<div class="footer_widgets_inner widget_area_inner">
				<?php
				if ( ! $equadio_footer_wide ) {
					?>
					<div class="content_wrap">
					<?php
				}
				if ( $equadio_need_columns ) {
					?>
					<div class="columns_wrap">
					<?php
				}
				do_action( 'equadio_action_before_sidebar', 'footer' );
				equadio_show_layout( $equadio_out );
				do_action( 'equadio_action_after_sidebar', 'footer' );
				if ( $equadio_need_columns ) {
					?>
					</div><!-- /.columns_wrap -->
					<?php
				}
				if ( ! $equadio_footer_wide ) {
					?>
					</div><!-- /.content_wrap -->
					<?php
				}
				?>
			</div><!-- /.footer_widgets_inner -->
			<?php do_action( 'equadio_action_after_sidebar_wrap', 'footer' ); ?>
		</div><!-- /.footer_widgets_wrap -->
		<?php
	}
}
