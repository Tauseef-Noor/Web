<?php
/**
 * The template to display the Author bio
 *
 * @package EQUADIO
 * @since EQUADIO 1.0
 */
?>

<div class="author_info author vcard" itemprop="author" itemscope="itemscope" itemtype="<?php echo esc_attr( equadio_get_protocol( true ) ); ?>//schema.org/Person">

	<div class="author_avatar" itemprop="image">
		<?php
		$equadio_mult = equadio_get_retina_multiplier();
		echo get_avatar( get_the_author_meta( 'user_email' ), 240 * $equadio_mult );
		?>
	</div><!-- .author_avatar -->

	<div class="author_description">
		<h5 class="author_title" itemprop="name"><span class="author_label"><?php esc_html_e( 'About', 'equadio' ); ?></span><span class="fn"><?php the_author(); ?></span></h5>
		<div class="author_bio" itemprop="description">
			<?php echo wp_kses( wpautop( get_the_author_meta( 'description' ) ), 'equadio_kses_content' ); ?>
			<div class="author_links">
				<a class="author_link" href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" rel="author">
													<?php
													// Translators: Add the author's name in the <span>
													printf( esc_html__( 'View all posts by %s', 'equadio' ), '<span class="author_name">' . esc_html( get_the_author() ) . '</span>' );
													?>
				</a>
				<?php do_action( 'equadio_action_user_meta', 'author-bio' ); ?>
			</div>
		</div><!-- .author_bio -->

	</div><!-- .author_description -->

</div><!-- .author_info -->
