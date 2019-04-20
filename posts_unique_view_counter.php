<?php
/*
  Plugin Name: Posts Unique View Counter
  Plugin URI: https://www.davidangulo.xyz/portfolio/posts-unique-view-counter/
  Description: Upon activation, it will allow you to easily display how many times a post have been uniquely viewed.
  Version: 1.2.0
  Author: David Angulo
  Author URI: https://www.davidangulo.xyz/
  Requires at least: 4.8.5
  Tested Up to: 5.1.1
  License: GPL2
*/

/*
  Copyright 2019 David Angulo (email: hello@davidangulo.xyz)

  This program is free software; you can redistribute it and/or
  modify it under the terms of the GNU General Public License
  as published by the Free Software Foundation; either version 2
  of the License, or (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.
*/


class PostsUniqueViewCounter {

  private $wpdb;
  private $table_name;



  public function __construct() {
    global $wpdb;

    $this->wpdb = $wpdb;
    $this->table_name = $this->wpdb->prefix . 'puvc';

    register_activation_hook(__FILE__, array($this, 'puvc_create_db_table'));
    add_action('template_redirect', array($this, 'puvc_counter_cookie'));
    add_filter('the_content', array($this, 'posts_unique_view_counter'));
    add_filter('manage_posts_columns', array($this, 'puvc_column_head'));
    add_filter('manage_edit-post_sortable_columns', array($this, 'puvc_column_head'));
    add_action('manage_posts_custom_column', array($this, 'puvc_column_content'), 10, 2);
    add_filter('request', array($this, 'puvc_sort_views_column'));
  }

  public function puvc_column_head($columns) {
    $columns['puvc_views'] = 'Views';

    return $columns;
  }

  public function puvc_column_content($column_name, $post_id) {
    if ( $column_name == 'puvc_views' ) {
      echo $this->wpdb->get_var("SELECT puvc_view_count FROM $this->table_name WHERE puvc_id='$post_id'");
    }
  }

  public function puvc_sort_views_column($vars) {
    if ( isset($vars['orderby']) && 'puvc_views' == $vars['orderby'] ) {
      $vars = array_merge($vars, array(
        'meta_key' => 'puvc_views',
        'orderby' => 'meta_value_num'
      ));
    }

    return $vars;
  }

  public function puvc_create_db_table() {
    $sql = "CREATE TABLE `$this->table_name` (
        `puvc_id` int(11) NOT NULL,
        `puvc_title` varchar(220) DEFAULT NULL,
        `puvc_permalink` varchar(220) DEFAULT NULL,
        `puvc_view_count` int(11) DEFAULT '1',
        PRIMARY KEY(puvc_id)
      );
    ";

    if ( $this->wpdb->get_var("SHOW TABLES LIKE '$this->table_name'") != $this->table_name ) {
      require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
      dbDelta($sql);
    }
  }

  public function puvc_counter_cookie() {
    $post_id = get_the_ID();
    $post_permalink = get_the_permalink();
    $post_title = get_the_title();

    if ( is_single() && !is_home() && !isset($_COOKIE[$post_id]) ) {
      setcookie($post_id, $post_permalink, time() + (86400 * 30));

      if( $this->wpdb->get_var("SELECT COUNT(*) FROM $this->table_name WHERE puvc_id='$post_id'") == 0 ) {
        $this->wpdb->query("INSERT INTO $this->table_name(puvc_id,puvc_title,puvc_permalink) VALUES('$post_id','$post_title','$post_permalink')");
      }
      else {
        $this->wpdb->query("UPDATE $this->table_name SET puvc_view_count = puvc_view_count+1 WHERE puvc_id='$post_id'");
      }
    }
  }

  public function posts_unique_view_counter($content) {
    $post_id = get_the_ID();

    if ( is_single() && !is_home() ) {
      $unique_view_count = $this->wpdb->get_var("SELECT puvc_view_count FROM $this->table_name WHERE puvc_id='$post_id'");
      $unique_view_count = "Visitors have accessed this post <span style=\"font-weight: bold;\">$unique_view_count</span> times.<br><br>";
      $content = $unique_view_count.$content;
    }

    return $content;
  }
}

$posts_unique_view_counter = new PostsUniqueViewCounter;

?>
