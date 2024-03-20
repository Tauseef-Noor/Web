<?php
/**
 * The template to display the widgets area in the header
 *
 * @package EQUADIO
 * @since EQUADIO 1.0
 */

// Header sidebar
$equadio_header_name    = equadio_get_theme_option( 'header_widgets' );
$equadio_header_present = ! equadio_is_off( $equadio_header_name ) && is_active_sidebar( $equadio_header_name );
if ( $equadio_header_present ) {
	equadio_storage_set( 'current_sidebar', 'header' );
	$equadio_header_wide = equadio_get_theme_option( 'header_wide' );
	ob_start();
	if ( is_active_sidebar( $equadio_header_name ) ) {
		dynamic_sidebar( $equadio_header_name );
	}
	$equadio_widgets_output = ob_get_contents();
	ob_end_clean();
	if ( ! empty( $equadio_widgets_output ) ) {
		$equadio_widgets_output = preg_replace( "/<\/aside>[\r\n\s]*<aside/", '</aside><aside', $equadio_widgets_output );
		$equadio_need_columns   = strpos( $equadio_widgets_output, 'columns_wrap' ) === false;
		if ( $equadio_need_columns ) {
			$equadio_columns = max( 0, (int) equadio_get_theme_option( 'header_columns' ) );
			if ( 0 == $equadio_columns ) {
				$equadio_columns = min( 6, max( 1, equadio_tags_count( $equadio_widgets_output, 'aside' ) ) );
			}
			if ( $equadio_columns > 1 ) {
				$equadio_widgets_output = preg_replace( '/<aside([^>]*)class="widget/', '<aside$1class="column-1_' . esc_attr( $equadio_columns ) . ' widget', $equadio_widgets_output );
			} else {
				$equadio_need_columns = false;
			}
		}
		?>
		<div class="header_widgets_wrap widget_area<?php echo ! empty( $equadio_header_wide ) ? ' header_fullwidth' : ' header_boxed'; ?>">
			<?php do_action( 'equadio_action_before_sidebar_wrap', 'header' ); ?>
			<div class="header_widgets_inner widget_area_inner">
				<?php
				if ( ! $equadio_header_wide ) {
					?>
					<div class="content_wrap">
					<?php
				}
				if ( $equadio_need_columns ) {
					?>
					<div class="columns_wrap">
					<?php
				}
				do_action( 'equadio_action_before_sidebar', 'header' );
				equadio_show_layout( $equadio_widgets_output );
				do_action( 'equadio_action_after_sidebar', 'header' );
				if ( $equadio_need_columns ) {
					?>
					</div>	<!-- /.columns_wrap -->
					<?php
				}
				if ( ! $equadio_header_wide ) {
					?>
					</div>	<!-- /.content_wrap -->
					<?php
				}
				?>
			</div>	<!-- /.header_widgets_inner -->
			<?php do_action( 'equadio_action_after_sidebar_wrap', 'header' ); ?>
		</div>	<!-- /.header_widgets_wrap -->
		<?php
	}
}
