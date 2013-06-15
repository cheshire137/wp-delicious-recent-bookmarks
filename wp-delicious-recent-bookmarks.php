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
  const default_date_format = 'j F Y g:i A';

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
    $user_name = $instance['user_name'];
    $count = $instance['count'];
    $count = empty($count) ? 10 : $count;
    $title_link = $instance['title_link'];
    $date_format = $instance['date_format'];
    $date_format = empty($date_format) ? self::default_date_format : $date_format;
    echo $before_widget;
    if (!empty($title)) {
      echo $before_title;
      if ($title_link) {
        ?>
        <a href="<?php echo $this->get_user_link($user_name); ?>" class="delicious-recent-bookmarks-list-title-link">
          <?php echo $title; ?>
        </a>
        <?php
      } else {
        echo $title;
      }
      echo $after_title;
    }
    if (!empty($user_name)) {
      $url = $this->get_recent_url($user_name, $count);
      $result = $this->get_results($url);
      if (is_array($result) && count($result) > 0) {
        ?>
        <ul class="delicious-recent-bookmarks-list">
          <?php foreach ($result as $index => $link) { ?>
            <li class="delicious-recent-bookmark <?php echo $this->get_li_class($index); ?>">
              <a href="<?php echo $link->u; ?>" class="delicious-recent-bookmark-link">
                <?php echo $link->d; ?>
              </a>
              <?php if (is_array($link->t) && count($link->t) > 0) { ?>
                <ul class="delicious-recent-bookmark-tags">
                  <?php foreach ($link->t as $t_index => $tag) { ?>
                    <li class="delicious-recent-bookmark-tag <?php echo $this->get_li_class($t_index); ?>">
                      <a href="<?php echo $this->get_tag_url($user_name, $tag); ?>" class="delicious-recent-bookmark-tag-link">
                        <?php echo $tag; ?>
                      </a>
                    </li>
                  <?php } ?>
                </ul>
              <?php } ?>
              <span class="delicious-recent-bookmark-date">
                <?php echo $this->get_date_time($link->dt, $date_format); ?>
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
    if (isset($instance['count'])) {
      $count = $instance['count'];
    } else {
      $count = 10;
    }
    if (isset($instance['title_link'])) {
      $title_link = $instance['title_link'];
    } else {
      $title_link = true;
    }
    if (isset($instance['date_format'])) {
      $date_format = $instance['date_format'];
    } else {
      $date_format = self::default_date_format;
    }
    ?>
    <p>
      <label for="<?php echo $this->get_field_id('title'); ?>">
        <?php _e('Title:'); ?>
      </label>
      <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
    </p>
    <p>
      <label for="<?php echo $this->get_field_id('user_name'); ?>">
        <?php _e('Delicious user name:'); ?>
      </label>
      <input class="widefat" id="<?php echo $this->get_field_id('user_name'); ?>" name="<?php echo $this->get_field_name('user_name'); ?>" type="text" value="<?php echo esc_attr($user_name); ?>" />
    </p>
    <p>
      <label for="<?php echo $this->get_field_id('count'); ?>">
        <?php _e('Number of links to display:'); ?>
      </label>
      <input class="widefat" id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>" type="number" min="1" step="1" value="<?php echo esc_attr($count); ?>" />
    </p>
    <p>
      <label for="<?php echo $this->get_field_id('date_format'); ?>">
        <?php _e('Date format:'); ?>
      </label>
      <input class="widefat" id="<?php echo $this->get_field_id('date_format'); ?>" name="<?php echo $this->get_field_name('date_format'); ?>" type="text" value="<?php echo esc_attr($date_format); ?>" />
      <small>
        Refer to <a href="http://php.net/manual/en/function.date.php">the PHP manual</a> for format options.
      </small>
    </p>
    <p>
      <label for="<?php echo $this->get_field_id('title_link'); ?>">
        <input type="checkbox" id="<?php echo $this->get_field_id('title_link'); ?>" name="<?php echo $this->get_field_name('title_link'); ?>"<?php echo $title_link ? ' checked="checked"' : '' ?>>
        <?php _e('Link widget title to your Delicious profile?'); ?>
      </label>
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
    $instance['count'] = (!empty($new_instance['count'])) ? intval(strip_tags($new_instance['count'])) : 10;
    $instance['title_link'] = !empty($new_instance['title_link']);
    $instance['date_format'] = (!empty($new_instance['date_format'])) ? strip_tags($new_instance['date_format']) : self::default_date_format;
    return $instance;
  }

  private function get_user_link($user_name) {
    return 'https://delicious.com/' . $user_name;
  }

  private function get_li_class($index) {
    return ($index % 2 == 0) ? 'even' : 'odd';
  }

  private function get_date_time($str_date, $date_format) {
    $date = new DateTime($str_date);
    return $date->format($date_format);
  }

  private function get_tag_url($user_name, $tag) {
    return 'https://delicious.com/' . $user_name . '/' . urlencode($tag);
  }

  private function get_recent_url($user_name, $count) {
    return 'http://feeds.delicious.com/v2/json/' . $user_name . '?count=' .
           $count;
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
  wp_enqueue_style('wp-delicious-recent-bookmarks-widget-style',
                   plugins_url('wp-delicious-recent-bookmarks.css', __FILE__));
  register_widget('WpDeliciousRecentBookmarksWidget');
}

add_action('widgets_init', 'wp_delicious_recent_bookmarks_register_widget');
?>
