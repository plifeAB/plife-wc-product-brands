<?php

/** 
 * Plugin Name: Plife Product Brands
 * Description: This plugin support woocommerce products brands 
 * Version: 1.0 
 * Author: Plife 
 * Author URI: https://plife.se 
 * License: GPL v2 or later 
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt */

if (!defined("ABSPATH")) {
    exit;
}
class PlifeProductBrands
{
    private $db;
    public function __construct()
    {
        if (is_admin()) {
            include_once "db/db-upgrades.php";
        }
        include_once plugin_dir_path(__FILE__) . "includes/admin-metabox.php";
        add_action('admin_menu', array($this, 'add_products_menu_entry'), 100);

        include_once "db/db-process.php";
        $this->db = new plifeProductBrandsDbProcess();

        add_action('admin_post_nopriv_product_brands_add_brand', array($this, 'prefix_product_brands_add_brand'));
        add_action('admin_post_product_brands_add_brand', array($this, 'prefix_product_brands_add_brand'));

        add_action('admin_enqueue_scripts', array($this, "required_files"));
    }

    function required_files()
    {
        if (is_admin())
            wp_enqueue_media();
    }

    function add_products_menu_entry()
    {
        add_submenu_page(
            'edit.php?post_type=product',
            __('Product Brands'),
            __('Brands'),
            'manage_woocommerce', // Required user capability
            'product-brands',
            array($this, 'generate_product_brands_page')
        );
    }
    function prefix_product_brands_add_brand()
    {
        if (isset($_POST['action']) && $_POST['action'] == "product_brands_add_brand" && isset($_POST['title']) && !empty($_POST['title'])) {
            //echo $_POST['title'] . "<br>";
            //echo $_POST['brand-description'] . "<br>";
            echo $_POST['process_custom_images'];
            $data = array(
                'title'     => isset($_POST['title']) ? $_POST['title'] : '',
                'description'   => isset($_POST['brand-description']) ? $_POST['brand-description'] : '',
                'img' => isset($_POST['process_custom_images']) ? $_POST['process_custom_images'] : '',
            );
            //print_r($data);
            //exit;
            $this->db->add_post($data);
            
        }
        wp_safe_redirect(admin_url("edit.php?post_type=product&page=product-brands"));
    }

    function generate_product_brands_page()
    {
        $this->all_brands();
    }
    function all_brands()
    {
?>
        <style>
            .product-brands-container {
                display: grid;
                grid-template-columns: 30% 70%;
                padding: 1%;
                padding-left: 0px;
            }

            .product-brands-list-item {
                padding-left: 5%;
            }
        </style>
        <div class="header-container">
            <h2>Product Brands</h2>
        </div>
        <hr>
        <div class="product-brands-container">
            <div class="product-brands-new">
                <form method="POST" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                    <p>
                    <div class="form-field">
                        <input type="hidden" name="action" value="product_brands_add_brand">
                        <label for="title"><strong>Title</strong></label>
                        <input type="text" id="title" name="title">
                    </div>
                    </p>
                    <p>
                    <div class="form-field">
                        <label for="brand-description"><strong>Description</strong></label>
                        <?php
                        //$content = $process == 'edit' ? wp_kses_stripslashes($this->db->get_post($id)[0]->content) : '';
                        $content =  '';
                        $editor_id = 'brand-description';
                        $settings = array(
                            'media_buttons' => false,
                            'textarea_name' => 'brand-description',
                            'quicktags' => true,
                            'tinymce' => array(
                                'theme_advanced_buttons1' => 'bold, italic, ul',
                            ),
                            'textarea_rows' => '2'
                        );
                        wp_editor($content, $editor_id, $settings);
                        ?>
                    </div>
                    </p>

                    <p>
                    <div class="form-field">
                        <input type="hidden" value="" class="regular-text process_custom_images" id="process_custom_images" name="process_custom_images" max="" min="1" step="1">
                        <img class="brand-img" src="" width="150px">
                        <p>
                            <button class="set_custom_images button">Set Image</button>
                        </p>
                    </div>
                    </p>
                    <hr>
                    <p>
                    <div class="form-field">
                        <?php submit_button("Save", "primary", "save-button"); ?>
                    </div>
                    </p>
                </form>
                <script>
                    jQuery(document).ready(function() {
                        var $ = jQuery;
                        if ($('.set_custom_images').length > 0) {
                            if (typeof wp !== 'undefined' && wp.media && wp.media.editor) {
                                $('.set_custom_images').on('click', function(e) {
                                    e.preventDefault();
                                    var button = $(this);
                                    var id = button.prev();
                                    wp.media.editor.send.attachment = function(props, attachment) {
                                        //id.val(attachment.id);
                                        $("#process_custom_images").val(attachment.id);
                                        $(".brand-img").attr("src", attachment.url);


                                    };
                                    wp.media.editor.open(button);
                                    return false;
                                });
                            }
                        }
                    });
                </script>
            </div>
            <div class="product-brands-list-item">
                <table class="wp-list-table widefat fixed striped table-view-list pages">
                    <thead>
                        <th style="width:50px;"></th>
                        <th>title</th>
                        <th>Process</th>
                    </thead>
                    <tbody>
                        <?php
                        $i = 1;
                        foreach ($this->db->get_all_posts() as $db_post) {
                            echo '<tr>';
                            echo '<td>' . $i++ . '</td>';
                        ?>
                            <td>
                                <a href="?page=home-page-builder&process=get-group&group=<?php echo $db_post->title; ?>"><?php echo $db_post->title; ?></a>
                            </td>
                            <td>
                                <a href="?page=home-page-builder&process=remove-group&group=<?php echo $db_post->id; ?>" onclick="if (confirm('Delete selected item?')){return true;}else{ return false;}">Remove</a>
                            </td>
                        <?php
                            echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
<?php

    }
}
new PlifeProductBrands();
