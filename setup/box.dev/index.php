<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Vagrant Box</title>
  <link rel="stylesheet" href="assets/css/app.css"/>
  <link rel="shortcut icon" href="assets/images/favicon.png">
</head>
<body class="hack">

<?php include_once 'includes/utils.php'; ?>

<div class="main container">
  <h1>driv-vagrant - <?php echo get_box_name(); ?></h1>

  <div class="alert alert-info uptime">
    <span>Uptime: </span><span class="uptime-result"></span>
  </div>

  <h2>Sites</h2>
  <ul>
    <?php foreach ( get_sites() as $site ) {
      echo '<li><a href="' . $site . '">' . $site . '</a></li>';
    }
    ?>
  </ul>

  <h2>Tools</h2>
  <ul>
    <?php foreach ( $tools_urls as $key => $tool ) {
      $url = create_url( $tool );
      echo '<li><a href="' . $url . '">' . $key . '</a></li>';
    } ?>
    <li><a href="phpinfo.php">phpinfo()</a></li>
  </ul>

  <footer>
    <a href="https://github.com/drivdigital/driv-vagrant">Contribute on
      GitHub</a> /
    <a href="https://github.com/drivdigital/driv-vagrant/wiki">Documentation</a>
  </footer>

</div>
<script src="assets/js/vendor/fetch.js"></script>
<script src="assets/js/app.js"></script>

</body>
</html>