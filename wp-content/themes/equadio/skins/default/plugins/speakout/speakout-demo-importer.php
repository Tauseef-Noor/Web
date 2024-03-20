<?php
/**
 * Plugin support: SpeakOut! Email Petitions (Importer support)
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.5
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
    wp_die( '-1' );
}

// Check plugin in the required plugins
if ( !function_exists( 'equadio_skin_speakout_importer_required_plugins' ) ) {
    add_filter( 'trx_addons_filter_importer_required_plugins',	'equadio_skin_speakout_importer_required_plugins', 10, 2 );
    function equadio_skin_speakout_importer_required_plugins($not_installed='', $list='') {
        if (strpos($list, 'speakout')!==false && !equadio_exists_speakout() )
            $not_installed .= '<br>' . esc_html__('SpeakOut! Email Petitions', 'equadio');
        return $not_installed;
    }
}

// Set plugin's specific importer options
if ( !function_exists( 'equadio_skin_speakout_importer_set_options' ) ) {
    add_filter( 'trx_addons_filter_importer_options',	'equadio_skin_speakout_importer_set_options' );
    function equadio_skin_speakout_importer_set_options($options=array()) {
        if ( equadio_exists_speakout() && in_array('speakout', $options['required_plugins']) ) {
            $options['additional_options'][]	= 'dk_speakout_options';					// Add slugs to export options for this plugin
            if (is_array($options['files']) && count($options['files']) > 0) {
                foreach ($options['files'] as $k => $v) {
                    $options['files'][$k]['file_with_speakout'] = str_replace('name.ext', 'speakout.txt', $v['file_with_']);
                }
            }
        }
        return $options;
    }
}

// Prevent import plugin's specific options if plugin is not installed
if ( !function_exists( 'equadio_skin_speakout_importer_check_options' ) ) {
    add_filter( 'trx_addons_filter_import_theme_options', 'equadio_skin_speakout_importer_check_options', 10, 4 );
    function equadio_skin_speakout_importer_check_options($allow, $k, $v, $options) {
        if ($allow && (strpos($k, 'dk_speakout_options')===0 ) ) {
            $allow = equadio_exists_speakout() && in_array('speakout', $options['required_plugins']);
        }
        return $allow;
    }
}


// Add checkbox to the one-click importer
if ( !function_exists( 'equadio_skin_speakout_importer_show_params' ) ) {
    add_action( 'trx_addons_action_importer_params', 'equadio_skin_speakout_importer_show_params', 10, 1 );
    function equadio_skin_speakout_importer_show_params($importer) {
        if ( equadio_exists_speakout() && in_array('speakout', $importer->options['required_plugins']) ) {
            $importer->show_importer_params(array(
                'slug' => 'speakout',
                'title' => esc_html__('Import SpeakOut! Email Petitions', 'equadio'),
                'part' => 0
            ));
        }
    }
}

// Import posts
if ( !function_exists( 'equadio_skin_speakout_importer_import' ) ) {
    add_action( 'trx_addons_action_importer_import', 'equadio_skin_speakout_importer_import', 10, 2 );
    function equadio_skin_speakout_importer_import($importer, $action) {
        if ( equadio_exists_speakout() && in_array('speakout', $importer->options['required_plugins']) ) {
            if ( $action == 'import_speakout' ) {
                $importer->response['start_from_id'] = 0;
                $importer->import_dump('speakout', esc_html__('SpeakOut! Email Petitions meta', 'equadio'));
            }
        }
    }
}

// Display import progress
if ( !function_exists( 'equadio_skin_speakout_importer_import_fields' ) ) {
    add_action( 'trx_addons_action_importer_import_fields',	'equadio_skin_speakout_importer_import_fields', 10, 1 );
    function equadio_skin_speakout_importer_import_fields($importer) {
        if ( equadio_exists_speakout() && in_array('speakout', $importer->options['required_plugins']) ) {
            $importer->show_importer_fields(array(
                    'slug'=>'speakout',
                    'title' => esc_html__('SpeakOut! Email Petitions meta', 'equadio')
                )
            );
        }
    }
}

// Export posts
if ( !function_exists( 'equadio_skin_speakout_importer_export' ) ) {
    add_action( 'trx_addons_action_importer_export', 'equadio_skin_speakout_importer_export', 10, 1 );
    function equadio_skin_speakout_importer_export($importer) {
        if ( equadio_exists_speakout() && in_array('speakout', $importer->options['required_plugins']) ) {
            equadio_fpc($importer->export_file_dir('speakout.txt'), serialize( array(
                    'dk_speakout_petitions'				=> $importer->export_dump('dk_speakout_petitions'),
                    'dk_speakout_signatures'	        => $importer->export_dump('dk_speakout_signatures')
                ) )
            );
        }
    }
}

// Display exported data in the fields
if ( !function_exists( 'equadio_speakout_importer_export_fields' ) ) {
    add_action( 'trx_addons_action_importer_export_fields',	'equadio_speakout_importer_export_fields', 10, 1 );
    function equadio_speakout_importer_export_fields($importer) {
        if ( equadio_exists_speakout() && in_array('speakout', $importer->options['required_plugins']) ) {
            $importer->show_exporter_fields(array(
                    'slug'	=> 'speakout',
                    'title' => esc_html__('SpeakOut! Email Petitions', 'equadio')
                )
            );
        }
    }
}
