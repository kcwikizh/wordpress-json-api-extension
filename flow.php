<?php
/*
Controller name: Flow
Controller description: Fetch posts (developed by grzhan)
*/

class JSON_API_Flow_Controller {

  public function get() {
    global $wpdb;
    global $json_api;
    global $wp_query;

    $posts = [];
    $until = intval($json_api->query->until);
    $count = $json_api->query->count ? intval($json_api->query->count) : 10;
    if (!$until) {
      $json_api->error("Include a 'until' query var.");
    }
    $sql = $wpdb->prepare(
      "SELECT ID FROM $wpdb->posts WHERE ID < %d AND post_type='post' ORDER BY ID DESC LIMIT %d",
      $until, $count);
    $results = $wpdb->get_results($sql);
    foreach ($results as $row) {
      $wp_query->query['p'] = $row->ID;           // HACK
      $post = $json_api->introspector->get_posts(array('p' => $row->ID));
      if (count($post) > 0) {
        array_push($posts, $post[0]);
      }
    }
    return array(
      "posts" => $posts
    );
  }
}
