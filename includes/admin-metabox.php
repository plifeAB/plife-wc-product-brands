<?php 

if ( !defined("ABSPATH") ) {
    exit;
}
class PlifeProductBrandsMetaBox {
    function __construct()
    {
        add_action( 'add_meta_boxes', array($this,'create_custom_meta_box' ));
        add_action( 'save_post', array($this,'save_custom_content_meta_box'), 10, 1 );
        // add_filter( 'woocommerce_product_tabs', array($this,'custom_product_tabs' ));
    }
    // Adding a custom Meta container to admin products pages
    function create_custom_meta_box()
    {
        add_meta_box(
            'custom_product_meta_box',
            __( 'Additional Product Information <em>(optional)</em>', 'cmb' ),
            array($this,'add_custom_content_meta_box'),
            'product',
            'side',
            'default'
        );
    }
    //  Custom metabox content in admin product pages
    function add_custom_content_meta_box( $post ){
        $prefix = '_bhww_'; // global $prefix;
        $ingredients = get_post_meta($post->ID, $prefix.'ingredients_wysiwyg', true) ? get_post_meta($post->ID, $prefix.'ingredients_wysiwyg', true) : '';
        $benefits = get_post_meta($post->ID, $prefix.'benefits_wysiwyg', true) ? get_post_meta($post->ID, $prefix.'benefits_wysiwyg', true) : '';
        $args['textarea_rows'] = 6;
        echo '<p>'.__( 'Ingredients', 'cmb' ).'</p>';
        wp_editor( $ingredients, 'ingredients_wysiwyg', $args );
        echo '<p>'.__( 'Benefits', 'cmb' ).'</p>';
        wp_editor( $benefits, 'benefits_wysiwyg', $args );
        echo '<input type="hidden" name="custom_product_field_nonce" value="' . wp_create_nonce() . '">';
    }
    //Save the data of the Meta field
    function save_custom_content_meta_box( $post_id ) {
        $prefix = '_bhww_'; // global $prefix;
        // We need to verify this with the proper authorization (security stuff).
        // Check if our nonce is set.
        if ( ! isset( $_POST[ 'custom_product_field_nonce' ] ) ) {
            return $post_id;
        }
        $nonce = $_REQUEST[ 'custom_product_field_nonce' ];
        //Verify that the nonce is valid.
        if ( ! wp_verify_nonce( $nonce ) ) {
            return $post_id;
        }
        // If this is an autosave, our form has not been submitted, so we don't want to do anything.
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return $post_id;
        }
        // Check the user's permissions.
        if ( 'product' == $_POST[ 'post_type' ] ){
            if ( ! current_user_can( 'edit_product', $post_id ) )
                return $post_id;
        } else {
            if ( ! current_user_can( 'edit_post', $post_id ) )
                return $post_id;
        }
        // Sanitize user input and update the meta field in the database.
        update_post_meta( $post_id, $prefix.'ingredients_wysiwyg', wp_kses_post($_POST[ 'ingredients_wysiwyg' ]) );
        update_post_meta( $post_id, $prefix.'benefits_wysiwyg', wp_kses_post($_POST[ 'benefits_wysiwyg' ]) );
    }
    // Create custom tabs in product single pages
    function custom_product_tabs( $tabs ) {
        global $post;
        $product_ingredients = get_post_meta( $post->ID, '_bhww_ingredients_wysiwyg', true );
        $product_benefits    = get_post_meta( $post->ID, '_bhww_benefits_wysiwyg', true );
        if ( ! empty( $product_ingredients ) )
            $tabs['ingredients_tab'] = array(
                'title'    => __( 'Ingredients', 'woocommerce' ),
                'priority' => 45,
                'callback' => 'ingredients_product_tab_content'
            );
        if ( ! empty( $product_benefits ) )
            $tabs['benefits_tab'] = array(
                'title'    => __( 'Benefits', 'woocommerce' ),
                'priority' => 50,
                'callback' => 'benefits_product_tab_content'
            );
        return $tabs;
    }
    // Add content to custom tab in product single pages (1)
    function ingredients_product_tab_content() {
        global $post;
        $product_ingredients = get_post_meta( $post->ID, '_bhww_ingredients_wysiwyg', true );
        if ( ! empty( $product_ingredients ) ) {
            echo '<h2>' . __( 'Product Ingredients', 'woocommerce' ) . '</h2>';
            // Updated to apply the_content filter to WYSIWYG content
            echo apply_filters( 'the_content', $product_ingredients );
        }
    }
    // Add content to custom tab in product single pages (2)
    function benefits_product_tab_content() {
        global $post;
        $product_benefits = get_post_meta( $post->ID, '_bhww_benefits_wysiwyg', true );
        if ( ! empty( $product_benefits ) ) {
            echo '<h2>' . __( 'Product Benefits', 'woocommerce' ) . '</h2>';
            // Updated to apply the_content filter to WYSIWYG content
            echo apply_filters( 'the_content', $product_benefits );
        }
    }
}
new PlifeProductBrandsMetaBox();