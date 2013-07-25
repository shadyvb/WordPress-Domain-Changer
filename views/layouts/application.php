<!DOCTYPE html>
<html>
  <head>
    <title>WordPress Domain Changer by Daniel Doezema</title>
    <link rel="stylesheet" type="text/css" href="assets/application.css">
    <script type="text/javascript" language="Javascript" src="assets/application.js"></script>
  </head>
  <body>
    <h1>WordPress Domain Changer <?php require dirname(__FILE__) . '/views/_social_button_github.php' ?></h1>
    <span>By <a href="http://dan.doezema.com" target="_blank">Daniel Doezema</a></span>
    <div class="main">
      <?php if($WPDC->isAuthenticatedSession()): ?>
      <div id="timeout">
        <div>You have <span id="seconds"><?php echo ((int) $_COOKIE[DDWPDC_COOKIE_NAME_EXPIRE] + 5) - time(); ?></span> Seconds left in this session.</div>
        <div id="bar"></div>
      </div>
      <div class="clear"></div>
      <div class="left">
        <form method="post" action="<?php echo basename(__FILE__); ?>">
          <input type="hidden" name="is_change_request" value="1" />
          <h3>Database Connection Settings</h3>
          <blockquote>
            <label for="host">Host</label>
            <div><input type="text" id="host" name="host" value="<?php echo $WPDC->getConfig()->getConstant('DB_HOST'); ?>" /></div>

            <label for="username">User</label>
            <div><input type="text" id="username" name="username" value="<?php echo $WPDC->getConfig()->getConstant('DB_USER'); ?>" /></div>

            <label for="password">Password</label>
            <div><input type="text" id="password" name="password" value="<?php echo $WPDC->getConfig()->getConstant('DB_PASSWORD'); ?>" /></div>

            <label for="database">Database Name</label>
            <div><input type="text" id="database" name="database" value="<?php echo $WPDC->getConfig()->getConstant('DB_NAME'); ?>" /></div>

            <label for="prefix">Table Prefix</label>
            <div><input type="text" id="prefix" name="prefix" value="<?php echo $WPDC->getConfig()->getTablePrefix(); ?>" /></div>
          </blockquote>

          <label for="old_domain">Old Domain</label>
          <div>http://<input type="text" id="old_domain" name="old_domain" value="<?php echo $WPDC->getOldDomain(); ?>" /></div>

          <label for="new_domain">New Domain</label>
          <div>http://<input type="text" id="new_domain" name="new_domain" value="<?php echo $WPDC->getNewDomain(); ?>" /></div>

          <input type="submit" id="submit_button" name="submit_button" value="Change Domain!" />
        </form>
      </div>
      <div class="right">
        <?php echo partial('events'); ?>
      </div>
  <?php else: ?>
    <?php if(isset($_POST['auth_password'])): ?>
    <div class="log error"><strong>Error:</strong> Incorrect password, please try again.</div>
  <?php endif; ?>

  <?php endif; ?>
  </div>
  </body>
</html>