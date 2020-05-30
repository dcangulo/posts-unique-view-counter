<?php
class PostsUniqueViewCounter {
  private $wpdb;



  public function __construct() {
    global $wpdb;

    $this->wpdb = $wpdb;

    register_activation_hook(
      PUVC_ROOT_PATH . 'index.php', [$this, 'puvc_create_db_table']
    );
    add_filter('manage_posts_columns', [$this, 'puvc_column_head']);
    add_action(
      'manage_posts_custom_column', [$this, 'puvc_column_content'], 10, 2
    );
    add_action('template_redirect', [$this, 'puvc_counter_cookie']);
    add_filter('the_content', [$this, 'puvc_post_content']);
    add_action('admin_menu', [$this, 'puvc_settings_page']);
    add_action('admin_init', [$this, 'puvc_settings']);
  }

  public function puvc_create_db_table() {
    $query = '
      CREATE TABLE IF NOT EXISTS `' . PUVC_DB_TABLE . '` (
        `puvc_id` int(11) NOT NULL,
        `puvc_view_count` int(11) DEFAULT 1,
        PRIMARY KEY(puvc_id)
      )
    ';

    dbDelta($query);
  }

  public function puvc_column_head($columns) {
    $columns['puvc_views'] = 'Views';

    return $columns;
  }

  public function puvc_column_content($column_name, $post_id) {
    if ($column_name !== 'puvc_views') return;

    $query = "
      SELECT puvc_view_count FROM " . PUVC_DB_TABLE . " WHERE puvc_id='$post_id'
    ";
    $view_count = $this->wpdb->get_var($query) ?: 0;

    echo $view_count;
  }

  public function puvc_counter_cookie() {
    $post_id = get_the_ID();
    $post_permalink = get_the_permalink();

    if (!is_single() || is_home() || isset($_COOKIE[$post_id])) return;

    setcookie($post_id, $post_permalink, PUVC_COOKIE_DURATION);

    $query = "
      INSERT INTO " . PUVC_DB_TABLE . "(puvc_id) VALUES($post_id)
      ON DUPLICATE KEY UPDATE puvc_view_count = puvc_view_count + 1
    ";

    $this->wpdb->query($query);
  }

  public function puvc_post_content($content) {
    if (get_option('puvc_hide_count') === 'puvc_hide_count') return $content;
    if (!is_single() || is_home()) return $content;

    $post_id = get_the_ID();
    $query = "
      SELECT puvc_view_count FROM " . PUVC_DB_TABLE . " WHERE puvc_id='$post_id'
    ";
    $view_count = $this->wpdb->get_var($query) ?: 0;
    $times = $view_count > 1 || $view_count === 0 ? 'times' : 'time';

    include(PUVC_ROOT_PATH . 'views/puvc_view_message.php');

    return $content;
  }

  public function puvc_settings_page() {
    add_options_page(
      'Posts Unique View Counter', 'Posts Unique View Counter',
      'manage_options', 'puvc_plugin_settings',
      [$this, 'puvc_option_page_content']
    );
  }

  public function puvc_option_page_content() {
    include(PUVC_ROOT_PATH . 'views/puvc_settings.php');
  }

  public function puvc_settings() {
    register_setting('puvc_plugin_settings', 'puvc_hide_count');
  }
}
