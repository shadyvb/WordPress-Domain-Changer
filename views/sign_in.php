<form id="sign_in" name="sign_in" method="post" action="<?php echo get_action_url('sign_in_form_handler');?>">
  <h3>Authenticate</h3>
  <label for="password">Password</label>
  <input type="password" id="password" name="password" value="" placeholder="Enter your password here..." />
  <input type="submit" id="submit_button" name="submit_button" value="Sign In!" />
</form>