<?php

class WP_REST_Request {
  public function get_body() {
    return json_encode([
      'some-key' => 'some-value',
    ]);
  }
}