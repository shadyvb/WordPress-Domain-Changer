<?php

function capture_output($file_path) {
  ob_start();
  $output = ob_get_contents();
  ob_end_clean();
  return $output;
}

function wpdc_path($join = "") {
  WPDC_PATH . (!empty($join) ? ('/' . $join) : '');
}

function includes_path($join = "") {
  WPDC_PATH . (!empty($join) ? ('/' . $join) : '');
}

function get_views_path($join = "") {
  VIEWS_PATH . (!empty($join) ? ('/' . $join) : '');
}

function get_assets_path($join = "") {
  ASSETS_PATH . (!empty($join) ? ('/' . $join) : '');
}

function get_classes_path($join = "") {
  CLASSES_PATH . (!empty($join) ? ('/' . $join) : '');
}

// View Helpers
function partial($name, $data = array()) {
  $path = get_views_path('_' . $name . '.php');

}

function view($name) {

}

function get_action_url($action) {
  WPDC_URL . '/index.php?action=' + urlencode($action);
}