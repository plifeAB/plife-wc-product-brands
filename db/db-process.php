<?php
if(! defined("ABSPATH")) {
    exit;
}
class plifeProductBrandsDbProcess {
    function get_all_posts()
    {
        global $wpdb;
        $table_home_page_builder = $wpdb->prefix . 'plife_wc_product_brands';
        $sql = "SELECT * FROM $table_home_page_builder;";
        $posts = $wpdb->get_results($sql);
        return $posts;
    }
    function add_post($data)
    {
        global $wpdb;
        $table_home_page_builder = $wpdb->prefix . 'plife_wc_product_brands';
        $wpdb->insert($table_home_page_builder,$data,array('%s','%s','%s'));
    }
    function edit_post($data,$id)
    {
        global  $wpdb;
        $table_home_page_builder = $wpdb->prefix . 'plife_wc_product_brands';
        $wpdb->update(
            $table_home_page_builder,
            $data,
            array('id' => $id)
        );
    }
    function remove_post($id)
    {
        global $wpdb;
        $table_home_page_builder = $wpdb->prefix . 'plife_wc_product_brands';
        $wpdb->delete(
            $table_home_page_builder,
            array("id" => $id)
        );
    }
    function remove_post_group($group)
    {
        global $wpdb;
        $table_home_page_builder = $wpdb->prefix . 'plife_wc_product_brands';
        $wpdb->delete(
            $table_home_page_builder,
            array("grp" => $group)
        );
    }
    function get_post_by_id($id)
    {
        global $wpdb;
        $table_home_page_builder = $wpdb->prefix . 'plife_wc_product_brands';
        $query = $wpdb->prepare("SELECT * FROM $table_home_page_builder WHERE id = %d", array($id));
        $result = $wpdb->get_results($query);
        return $result;
    }
    function get_post_all_group()
    {
        global $wpdb;
        $table_home_page_builder = $wpdb->prefix . 'plife_wc_product_brands';
        $query = "SELECT * FROM $table_home_page_builder GROUP BY grp ";
        $result = $wpdb->get_results($query);
        return $result;
    }
    function get_post_by_group($group)
    {
        global $wpdb;
        $table_home_page_builder = $wpdb->prefix . 'plife_wc_product_brands';
        $query = $wpdb->prepare("SELECT * FROM $table_home_page_builder WHERE grp = %s", array($group));
        $result = $wpdb->get_results($query);
        return $result;
    }
    function get_post_by_title($title)
    {
        global $wpdb;
        $table_home_page_builder = $wpdb->prefix . 'plife_wc_product_brands';
        $result = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_home_page_builder WHERE title = %s", array($title)));
        //$result = $wpdb->get_results($query);
        if(count($result) != 0) {
            return $result[0];
        }
        return false;
    }
    function get_content($content) 
    {
        return wp_unslash(wp_kses_stripslashes( $content ? $content->content : ''));
    }
}