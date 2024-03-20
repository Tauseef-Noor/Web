<?php
/**
 * Child-Theme functions and definitions
 */

function equadio_child_scripts() {
    wp_enqueue_style( 'equadio-parent-style', get_template_directory_uri(). '/style.css' );
}
add_action( 'wp_enqueue_scripts', 'equadio_child_scripts' );