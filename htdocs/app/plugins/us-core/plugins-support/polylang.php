<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * polylang Support
 *
 * @link https://polylang.pro/
 */

 if (! (function_exists ( 'pll_languages_list' ))) {
 	return;
 }
 if ( pll_current_language() != pll_default_language() ) {
   	global $pagenow;
   	// Exception: do not add class on Theme Options page
   	if ( ! ( $pagenow == 'admin.php' AND ! empty( $_GET['page'] ) AND $_GET['page'] == 'us-theme-options' ) ) {
   		function us_admin_add_wpml_nondefault_class( $class ) {
   			return $class . ' us_wpml_non_default';
   		}

   		add_filter( 'admin_body_class', 'us_admin_add_wpml_nondefault_class' );
   	} else {
   		// For Theme Options page adding redirect to default language
   		wp_redirect( admin_url() . 'admin.php?page=us-theme-options&lang=' . pll_default_language() );
   	}
  }
 function  cpt__labels ( $cpts ) {
   $args = array(
 		'public' => TRUE,
 		'publicly_queryable' => TRUE,
 		'_builtin' => FALSE,
 	);
 	$types = get_post_types( $args, 'objects' );
   if ( ! empty ( $types ) AND  function_exists ( 'pll_register_string' ))     {
 		foreach ( $types as $type ){
 		pll_register_string ( 'themes' , $type->name );
 		pll_register_string ( 'themes' , $type->label );
             if ( ! empty ( $type->description )) {
                 pll_register_string ( 'themes' , $type->description);
             }
             foreach ( $type->labels as  $label ) {
                 pll_register_string ( 'themes' , $label );
             }
         }
     }
     return  $cpts ;
 }

add_action('init', 'cpt__labels');
