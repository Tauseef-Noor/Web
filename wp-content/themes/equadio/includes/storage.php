<?php
/**
 * Theme storage manipulations
 *
 * @package EQUADIO
 * @since EQUADIO 1.0
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) {
	exit; }

// Get theme variable
if ( ! function_exists( 'equadio_storage_get' ) ) {
	function equadio_storage_get( $var_name, $default = '' ) {
		global $EQUADIO_STORAGE;
		return isset( $EQUADIO_STORAGE[ $var_name ] ) ? $EQUADIO_STORAGE[ $var_name ] : $default;
	}
}

// Set theme variable
if ( ! function_exists( 'equadio_storage_set' ) ) {
	function equadio_storage_set( $var_name, $value ) {
		global $EQUADIO_STORAGE;
		$EQUADIO_STORAGE[ $var_name ] = $value;
	}
}

// Check if theme variable is empty
if ( ! function_exists( 'equadio_storage_empty' ) ) {
	function equadio_storage_empty( $var_name, $key = '', $key2 = '' ) {
		global $EQUADIO_STORAGE;
		if ( ! empty( $key ) && ! empty( $key2 ) ) {
			return empty( $EQUADIO_STORAGE[ $var_name ][ $key ][ $key2 ] );
		} elseif ( ! empty( $key ) ) {
			return empty( $EQUADIO_STORAGE[ $var_name ][ $key ] );
		} else {
			return empty( $EQUADIO_STORAGE[ $var_name ] );
		}
	}
}

// Check if theme variable is set
if ( ! function_exists( 'equadio_storage_isset' ) ) {
	function equadio_storage_isset( $var_name, $key = '', $key2 = '' ) {
		global $EQUADIO_STORAGE;
		if ( ! empty( $key ) && ! empty( $key2 ) ) {
			return isset( $EQUADIO_STORAGE[ $var_name ][ $key ][ $key2 ] );
		} elseif ( ! empty( $key ) ) {
			return isset( $EQUADIO_STORAGE[ $var_name ][ $key ] );
		} else {
			return isset( $EQUADIO_STORAGE[ $var_name ] );
		}
	}
}

// Delete theme variable
if ( ! function_exists( 'equadio_storage_unset' ) ) {
	function equadio_storage_unset( $var_name, $key = '', $key2 = '' ) {
		global $EQUADIO_STORAGE;
		if ( ! empty( $key ) && ! empty( $key2 ) ) {
			unset( $EQUADIO_STORAGE[ $var_name ][ $key ][ $key2 ] );
		} elseif ( ! empty( $key ) ) {
			unset( $EQUADIO_STORAGE[ $var_name ][ $key ] );
		} else {
			unset( $EQUADIO_STORAGE[ $var_name ] );
		}
	}
}

// Inc/Dec theme variable with specified value
if ( ! function_exists( 'equadio_storage_inc' ) ) {
	function equadio_storage_inc( $var_name, $value = 1 ) {
		global $EQUADIO_STORAGE;
		if ( empty( $EQUADIO_STORAGE[ $var_name ] ) ) {
			$EQUADIO_STORAGE[ $var_name ] = 0;
		}
		$EQUADIO_STORAGE[ $var_name ] += $value;
	}
}

// Concatenate theme variable with specified value
if ( ! function_exists( 'equadio_storage_concat' ) ) {
	function equadio_storage_concat( $var_name, $value ) {
		global $EQUADIO_STORAGE;
		if ( empty( $EQUADIO_STORAGE[ $var_name ] ) ) {
			$EQUADIO_STORAGE[ $var_name ] = '';
		}
		$EQUADIO_STORAGE[ $var_name ] .= $value;
	}
}

// Get array (one or two dim) element
if ( ! function_exists( 'equadio_storage_get_array' ) ) {
	function equadio_storage_get_array( $var_name, $key, $key2 = '', $default = '' ) {
		global $EQUADIO_STORAGE;
		if ( empty( $key2 ) ) {
			return ! empty( $var_name ) && ! empty( $key ) && isset( $EQUADIO_STORAGE[ $var_name ][ $key ] ) ? $EQUADIO_STORAGE[ $var_name ][ $key ] : $default;
		} else {
			return ! empty( $var_name ) && ! empty( $key ) && isset( $EQUADIO_STORAGE[ $var_name ][ $key ][ $key2 ] ) ? $EQUADIO_STORAGE[ $var_name ][ $key ][ $key2 ] : $default;
		}
	}
}

// Set array element
if ( ! function_exists( 'equadio_storage_set_array' ) ) {
	function equadio_storage_set_array( $var_name, $key, $value ) {
		global $EQUADIO_STORAGE;
		if ( ! isset( $EQUADIO_STORAGE[ $var_name ] ) ) {
			$EQUADIO_STORAGE[ $var_name ] = array();
		}
		if ( '' === $key ) {
			$EQUADIO_STORAGE[ $var_name ][] = $value;
		} else {
			$EQUADIO_STORAGE[ $var_name ][ $key ] = $value;
		}
	}
}

// Set two-dim array element
if ( ! function_exists( 'equadio_storage_set_array2' ) ) {
	function equadio_storage_set_array2( $var_name, $key, $key2, $value ) {
		global $EQUADIO_STORAGE;
		if ( ! isset( $EQUADIO_STORAGE[ $var_name ] ) ) {
			$EQUADIO_STORAGE[ $var_name ] = array();
		}
		if ( ! isset( $EQUADIO_STORAGE[ $var_name ][ $key ] ) ) {
			$EQUADIO_STORAGE[ $var_name ][ $key ] = array();
		}
		if ( '' === $key2 ) {
			$EQUADIO_STORAGE[ $var_name ][ $key ][] = $value;
		} else {
			$EQUADIO_STORAGE[ $var_name ][ $key ][ $key2 ] = $value;
		}
	}
}

// Merge array elements
if ( ! function_exists( 'equadio_storage_merge_array' ) ) {
	function equadio_storage_merge_array( $var_name, $key, $value ) {
		global $EQUADIO_STORAGE;
		if ( ! isset( $EQUADIO_STORAGE[ $var_name ] ) ) {
			$EQUADIO_STORAGE[ $var_name ] = array();
		}
		if ( '' === $key ) {
			$EQUADIO_STORAGE[ $var_name ] = array_merge( $EQUADIO_STORAGE[ $var_name ], $value );
		} else {
			$EQUADIO_STORAGE[ $var_name ][ $key ] = array_merge( $EQUADIO_STORAGE[ $var_name ][ $key ], $value );
		}
	}
}

// Add array element after the key
if ( ! function_exists( 'equadio_storage_set_array_after' ) ) {
	function equadio_storage_set_array_after( $var_name, $after, $key, $value = '' ) {
		global $EQUADIO_STORAGE;
		if ( ! isset( $EQUADIO_STORAGE[ $var_name ] ) ) {
			$EQUADIO_STORAGE[ $var_name ] = array();
		}
		if ( is_array( $key ) ) {
			equadio_array_insert_after( $EQUADIO_STORAGE[ $var_name ], $after, $key );
		} else {
			equadio_array_insert_after( $EQUADIO_STORAGE[ $var_name ], $after, array( $key => $value ) );
		}
	}
}

// Add array element before the key
if ( ! function_exists( 'equadio_storage_set_array_before' ) ) {
	function equadio_storage_set_array_before( $var_name, $before, $key, $value = '' ) {
		global $EQUADIO_STORAGE;
		if ( ! isset( $EQUADIO_STORAGE[ $var_name ] ) ) {
			$EQUADIO_STORAGE[ $var_name ] = array();
		}
		if ( is_array( $key ) ) {
			equadio_array_insert_before( $EQUADIO_STORAGE[ $var_name ], $before, $key );
		} else {
			equadio_array_insert_before( $EQUADIO_STORAGE[ $var_name ], $before, array( $key => $value ) );
		}
	}
}

// Push element into array
if ( ! function_exists( 'equadio_storage_push_array' ) ) {
	function equadio_storage_push_array( $var_name, $key, $value ) {
		global $EQUADIO_STORAGE;
		if ( ! isset( $EQUADIO_STORAGE[ $var_name ] ) ) {
			$EQUADIO_STORAGE[ $var_name ] = array();
		}
		if ( '' === $key ) {
			array_push( $EQUADIO_STORAGE[ $var_name ], $value );
		} else {
			if ( ! isset( $EQUADIO_STORAGE[ $var_name ][ $key ] ) ) {
				$EQUADIO_STORAGE[ $var_name ][ $key ] = array();
			}
			array_push( $EQUADIO_STORAGE[ $var_name ][ $key ], $value );
		}
	}
}

// Pop element from array
if ( ! function_exists( 'equadio_storage_pop_array' ) ) {
	function equadio_storage_pop_array( $var_name, $key = '', $defa = '' ) {
		global $EQUADIO_STORAGE;
		$rez = $defa;
		if ( '' === $key ) {
			if ( isset( $EQUADIO_STORAGE[ $var_name ] ) && is_array( $EQUADIO_STORAGE[ $var_name ] ) && count( $EQUADIO_STORAGE[ $var_name ] ) > 0 ) {
				$rez = array_pop( $EQUADIO_STORAGE[ $var_name ] );
			}
		} else {
			if ( isset( $EQUADIO_STORAGE[ $var_name ][ $key ] ) && is_array( $EQUADIO_STORAGE[ $var_name ][ $key ] ) && count( $EQUADIO_STORAGE[ $var_name ][ $key ] ) > 0 ) {
				$rez = array_pop( $EQUADIO_STORAGE[ $var_name ][ $key ] );
			}
		}
		return $rez;
	}
}

// Inc/Dec array element with specified value
if ( ! function_exists( 'equadio_storage_inc_array' ) ) {
	function equadio_storage_inc_array( $var_name, $key, $value = 1 ) {
		global $EQUADIO_STORAGE;
		if ( ! isset( $EQUADIO_STORAGE[ $var_name ] ) ) {
			$EQUADIO_STORAGE[ $var_name ] = array();
		}
		if ( empty( $EQUADIO_STORAGE[ $var_name ][ $key ] ) ) {
			$EQUADIO_STORAGE[ $var_name ][ $key ] = 0;
		}
		$EQUADIO_STORAGE[ $var_name ][ $key ] += $value;
	}
}

// Concatenate array element with specified value
if ( ! function_exists( 'equadio_storage_concat_array' ) ) {
	function equadio_storage_concat_array( $var_name, $key, $value ) {
		global $EQUADIO_STORAGE;
		if ( ! isset( $EQUADIO_STORAGE[ $var_name ] ) ) {
			$EQUADIO_STORAGE[ $var_name ] = array();
		}
		if ( empty( $EQUADIO_STORAGE[ $var_name ][ $key ] ) ) {
			$EQUADIO_STORAGE[ $var_name ][ $key ] = '';
		}
		$EQUADIO_STORAGE[ $var_name ][ $key ] .= $value;
	}
}

// Call object's method
if ( ! function_exists( 'equadio_storage_call_obj_method' ) ) {
	function equadio_storage_call_obj_method( $var_name, $method, $param = null ) {
		global $EQUADIO_STORAGE;
		if ( null === $param ) {
			return ! empty( $var_name ) && ! empty( $method ) && isset( $EQUADIO_STORAGE[ $var_name ] ) ? $EQUADIO_STORAGE[ $var_name ]->$method() : '';
		} else {
			return ! empty( $var_name ) && ! empty( $method ) && isset( $EQUADIO_STORAGE[ $var_name ] ) ? $EQUADIO_STORAGE[ $var_name ]->$method( $param ) : '';
		}
	}
}

// Get object's property
if ( ! function_exists( 'equadio_storage_get_obj_property' ) ) {
	function equadio_storage_get_obj_property( $var_name, $prop, $default = '' ) {
		global $EQUADIO_STORAGE;
		return ! empty( $var_name ) && ! empty( $prop ) && isset( $EQUADIO_STORAGE[ $var_name ]->$prop ) ? $EQUADIO_STORAGE[ $var_name ]->$prop : $default;
	}
}
