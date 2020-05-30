<?php
/*
  Plugin Name: Posts Unique View Counter
  Plugin URI: https://www.davidangulo.xyz/portfolio/posts-unique-view-counter/
  Description: Easily count unqiue views of your posts.
  Version: 1.3.0.0
  Author: David Angulo
  Author URI: https://www.davidangulo.xyz/
  Requires at least: 4.8.5
  Tested Up to: 5.4.1
  License: GPL2
*/

/*
  Copyright 2020 David Angulo (email: hello@davidangulo.xyz)

  This program is free software; you can redistribute it and/or
  modify it under the terms of the GNU General Public License
  as published by the Free Software Foundation; either version 2
  of the License, or (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.
*/

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
require_once('constants.php');
require_once('classes/posts_unique_view_counter.php');

$posts_unique_view_counter = new PostsUniqueViewCounter;
