<?php
/**
 * The template to display menu in the footer
 *
 * @package EQUADIO
 * @since EQUADIO 1.0.10
 */

// Footer menu
$equadio_menu_footer = equadio_get_nav_menu( 'menu_footer' );
if ( ! empty( $equadio_menu_footer ) ) {
	?>
	<div class="footer_menu_wrap">
		<div class="footer_menu_inner">
			<?php
			equadio_show_layout(
				$equadio_menu_footer,
				'<nav class="menu_footer_nav_area sc_layouts_menu sc_layouts_menu_default"'
					. ' itemscope="itemscope" itemtype="' . esc_attr( equadio_get_protocol( true ) ) . '//schema.org/SiteNavigationElement"'
					. '>',
				'</nav>'
			);
			?>
		</div>
	</div>
	<?php
}
