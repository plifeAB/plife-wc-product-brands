<?php
/** 
 * Plugin Name: Plife Product Brands
 * Description: This plugin support woocommerce products brands 
 * Version: 1.0 
 * Author: Plife 
 * Author URI: https://plife.se 
 * License: GPL v2 or later 
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt */

if ( !defined("ABSPATH") ) {
    exit;
}
class PlifeProductBrands {
    public function __construct()
    {
        if( is_admin() )
        {
            include_once "db/db-upgrades.php";
        }
        include_once plugin_dir_path(__FILE__)."includes/admin-metabox.php";
        add_action( 'admin_menu', array($this,'add_products_menu_entry'), 100 );

        include_once "db/db-process.php";
        $this->db = new plifeProductBrandsDbProcess();

    }

    function add_products_menu_entry() {
        add_submenu_page(
            'edit.php?post_type=product',
            __( 'Product Brands' ),
            __( 'Brands' ),
            'manage_woocommerce', // Required user capability
            'product-brands',
            array($this,'generate_product_brands_page')
        );
    }
    
    function generate_product_brands_page() {
       $this->all_brands();
    }
    function all_brands() 
    {
        ?>
        <div class="header-container">
            <h2>Product Brands</h2>
        </div>
        <hr>
        <div class="home-page-builder-group-container">
            <div class="button-container">
                <a href="?page=home-page-builder&process=add"><input type="button" value="Add New" class="button-primary"> </a>
            </div>
            <hr>
            <table class="wp-list-table widefat fixed striped table-view-list pages">
                <thead>
                <th style="width:50px;"></th>
                <th>title</th>
                <th>Process</th>
                </thead>
                <tbody>
                <?php
                $i=1;
                foreach ($this->db->get_all_posts() as $db_post) {
                    echo '<tr>';
                    echo '<td>'.$i++.'</td>';
                    ?>
                    <td>
                        <a href="?page=home-page-builder&process=get-group&group=<?php echo $db_post->title; ?>"><?php echo $db_post->title; ?></a>
                    </td>
                    <td>
                        <a href="?page=home-page-builder&process=remove-group&group=<?php echo $db_post->id; ?>"
                           onclick="if (confirm('Delete selected item?')){return true;}else{ return false;}">Remove</a>
                    </td>
                    <?php
                    echo '</tr>';
                }
                ?>
                </tbody>
            </table>
        </div>
        <?php

    }

}
new PlifeProductBrands();