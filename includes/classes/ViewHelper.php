<?php
class ViewHelper
{

  public static function capture($file_path, $data = array()) {
    ob_start();
    extract($data);
    require $file_path;
    $output = ob_get_contents();
    ob_end_clean();
    return $output;
  }

  public static function layouts_path($name) {

  }

  public static function render($options = array()) {
    $layout = array_key_exists('layout', $options) ? $options['layout'] : 'application';
  }

}
