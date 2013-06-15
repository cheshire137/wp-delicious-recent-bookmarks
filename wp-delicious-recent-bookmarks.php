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
      'Delicious Recent Bookmarks', // Name
      array('description' =>
            __('List your recently added Delicious bookmarks.', 'text_domain'))
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
    $user_name = apply_filters('widget_title', $instance['user_name']);
    echo $before_widget;
    if (!empty($title)) {
      echo $before_title . $title . $after_title;
    }
    if (!empty($user_name)) {
      $url = $this->get_recent_url($user_name);
      $result = $this->get_results($url);
      if (is_array($result) && count($result) > 0) {
        ?>
        <ul class="delicious-recent-bookmarks-list">
          <?php foreach ($result as $link) { ?>
            <li class="delicious-recent-bookmark">
              <a href="<?php echo $link->u; ?>" class="delicious-recent-bookmark-link">
                <?php echo $link->d; ?>
              </a>
              <?php if (is_array($link->t) && count($link->t) > 0) { ?>
                <ul class="delicious-recent-bookmark-tags">
                  <?php foreach ($link->t as $tag) { ?>
                    <li class="delicious-recent-bookmark-tag">
                      <a href="<?php echo $this->get_tag_url($user_name, $tag); ?>" class="delicious-recent-bookmark-tag-link">
                        <?php echo $tag; ?>
                      </a>
                    </li>
                  <?php } ?>
                </ul>
              <?php } ?>
              <span class="delicious-recent-bookmark-date">
                <?php echo $this->get_date_time($link->dt); ?>
              </span>
            </li>
          <?php } ?>
        </ul>
        <?php
      }
    }
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
    } else {
      $title = __('Recent Delicious Bookmarks', 'text_domain');
    }
    if (isset($instance['user_name'])) {
      $user_name = $instance['user_name'];
    } else {
      $user_name = '';
    }
    ?>
    <p>
      <label for="<?php echo $this->get_field_name('title'); ?>">
        <?php _e('Title:'); ?>
      </label>
      <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
    </p>
    <p>
      <label for="<?php echo $this->get_field_name('user_name'); ?>">
        <?php _e('Delicious user name:'); ?>
      </label>
      <input class="widefat" id="<?php echo $this->get_field_id('user_name'); ?>" name="<?php echo $this->get_field_name('user_name'); ?>" type="text" value="<?php echo esc_attr($user_name); ?>" />
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
    $instance['user_name'] = (!empty($new_instance['user_name'])) ? strip_tags($new_instance['user_name']) : '';
    return $instance;
  }

  private function get_date_time($str_date) {
    $date = new DateTime($str_date);
    return $date->format('j F Y g:i A');
  }

  private function get_tag_url($user_name, $tag) {
    return 'https://delicious.com/' . $user_name . '/' . urlencode($tag);
  }

  private function get_recent_url($user_name) {
    return 'http://feeds.delicious.com/v2/json/' . $user_name;
  }

  private function get_results($url) {
    $ch = curl_init($url);
    $options = array(
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_HTTPHEADER => array('Content-type: application/json')
    );
    curl_setopt_array($ch, $options);
    $json_result = curl_exec($ch);
    return json_decode($json_result);
  }
}

function wp_delicious_recent_bookmarks_register_widget() {
  register_widget('WpDeliciousRecentBookmarksWidget');
}

add_action('widgets_init', 'wp_delicious_recent_bookmarks_register_widget');
?>
