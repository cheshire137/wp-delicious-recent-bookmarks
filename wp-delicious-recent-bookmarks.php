<?php
/*
Plugin Name: Delicious Recent Bookmarks
Description: Display your recent Delicious bookmarks in a widget.
Version: 0.1
Author: Sarah Vessels
Author URI: http://www.3till7.net/
License: GPLv3

Copyright Sarah Vessels 2013

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

class WpDeliciousRecentBookmarksWidget extends WP_Widget {
  /**
   * Register widget with WordPress.
   */
  public function __construct() {
    parent::__construct(
      'wp_delicious_recent_bookmarks_widget', // Base ID
      'WP_Delicious_Recent_Bookmarks_Widget', // Name
      array('description' =>
            __('Delicious Recent Bookmarks Widget', 'text_domain'))
   );
  }

  /**
   * Front-end display of widget.
   *
   * @see WP_Widget::widget()
   *
   * @param array $args     Widget arguments.
   * @param array $instance Saved values from database.
   */
  public function widget($args, $instance) {
    extract($args);
    $title = apply_filters('widget_title', $instance['title']);
    echo $before_widget;
    if (!empty($title)) {
      echo $before_title . $title . $after_title;
    }
    echo __('Hello, World!', 'text_domain');
    echo $after_widget;
  }

  /**
   * Back-end widget form.
   *
   * @see WP_Widget::form()
   *
   * @param array $instance Previously saved values from database.
   */
  public function form($instance) {
    if (isset($instance['title'])) {
      $title = $instance['title'];
    }
    else {
      $title = __('New title', 'text_domain');
    }
    ?>
    <p>
    <label for="<?php echo $this->get_field_name('title'); ?>"><?php _e('Title:'); ?></label>
    <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
    </p>
    <?php
  }

  /**
   * Sanitize widget form values as they are saved.
   *
   * @see WP_Widget::update()
   *
   * @param array $new_instance Values just sent to be saved.
   * @param array $old_instance Previously saved values from database.
   *
   * @return array Updated safe values to be saved.
   */
  public function update($new_instance, $old_instance) {
    $instance = array();
    $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
    return $instance;
  }
}
add_action('widgets_init', function() {
  register_widget('WpDeliciousRecentBookmarksWidget');
});
?>
