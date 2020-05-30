<div class='wrap'>
  <h1>Posts Unique View Counter Settings</h1>

  <form id='puvc_settings' method='post' action='options.php'>
    <?php settings_fields('puvc_plugin_settings'); ?>

    <table class='form-table'>
      <tr>
        <th><label for='puvc_hide_count'>Hide Count</label></th>
        <td>
          <label for='puvc_hide_count'>
            <?php $puvc_hide_count = get_option('puvc_hide_count') === 'puvc_hide_count' ? 'checked="checked"' : ''; ?>
            <input name='puvc_hide_count' type='checkbox' id='puvc_hide_count' value='puvc_hide_count' <?php echo $puvc_hide_count; ?>> Hide count
          </label>
          <p class='description'>This will hide the view count from the post page.</p>
          <p class='description'>Useful if you only want the view count to only be seen by the admin rather than the public.</p>
        </td>
      </tr>
    </table>

    <?php submit_button(); ?>
  </form>
</div>
