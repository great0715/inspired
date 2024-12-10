<?php
require_once("assets.php");

function getCurrentUrl()
{
  // Determine the protocol (http or https)
  $protocol = 'http';
  if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
    $protocol = 'https';
  }

  // Get the server name and port
  $host = $_SERVER['HTTP_HOST'];

  // Get the request URI
  $uri = $_SERVER['REQUEST_URI'];
  $uriWithoutScript = preg_replace('/\/[^\/]+\.php/', '', $uri);

  // Construct the full URL
  $currentUrl = $protocol . '://' . $host . $uriWithoutScript . '/';

  return $currentUrl;
}

// Usage

$types = array(
  [
    'name' => 'Conveyance',
    'dir' => 'pick',
    'url' => 'conveyance_pick_import.php'
  ],
  [
    'name' => 'System Fill',
    'dir' => 'system_fill',
    'url' => 'excel_pick_import.php',
    'kind' => 'system'
  ],
  [
    'name' => 'Packing List',
    'dir' => 'packing_list',
    'url' => 'excel_pick_import.php',
    'kind' => 'pack'
  ],
  [
    'name' => 'Build CSV',
    'dir' => 'build',
    'url' => 'excel_pick_import.php',
    'kind' => 'build'
  ],
  [
    'name' => 'Part to Kanban',
    'dir' => 'part_to_kanban',
    'url' => 'import_csv.php',
    'target' => 'part2kanban',
  ]
);

$directory = './uploads/';

foreach ($types as $type) {

  echo $type['name'] . '<br><br>';

  // Get the list of files and directories
  $files = scandir($directory . $type['dir']);

  // Loop through the array and print the files and directories
  foreach ($files as $file) {
    if (pathinfo($file, PATHINFO_EXTENSION) == 'csv') {
      echo '&nbsp;&nbsp;&nbsp;&nbsp' . $file . '&nbsp;&nbsp;&nbsp;&nbsp';

      // Initialize cURL
      $ch = curl_init();

      // Configure cURL options

      curl_setopt($ch, CURLOPT_URL, getCurrentUrl() . $type['url']);
      curl_setopt($ch, CURLOPT_POST, 1);

      // Prepare file for upload
      $cfile = new CURLFile($directory . $type['dir'] . '/' . $file);
      $postData = array(
        'file' => $cfile,
        'kind' => isset($type['kind']) ? $type['kind'] : "",
        'target' => isset($type['target']) ? $type['target'] : ""
      );

      curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

      // Execute the request
      $response = curl_exec($ch);

      // Check for errors
      if ($response === false) {
        echo 'Error';
      } else {
        echo 'Success';
      }
      echo '<br>';
    }
  }

  echo '<br>';
}
?>
<script src="plugins/jquery/jquery.min.js"></script>