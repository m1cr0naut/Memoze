<?php
// This PHP file implements the Memoze HTML user interface.
  session_start();
  $is_logged_in = array_key_exists("username", $_SESSION);
  session_commit();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Lab 4</title>

  <style>
    [data-inactive] { display: none; }
  </style>

  <script>
    var App = {
      active_pane: <?php echo ($is_logged_in ? "\"memo-pane\"" : "\"login-pane\"") ?>,
    };
  </script>
</head>

<body>
  <h1>MEMOZE - your cloud-based PostIt Note :)</h1>
  <noscript>Please enable JavaScript in order to use this application.</noscript>

  <div id="login-pane" data-inactive>
    <form id='loginForm'>
      <fieldset>
        <legend>Log In</legend>
        <br>Username:
        <input type='text' name='username'>
        <br><br>Password:
        <input type='password' name='password'>
        <br><br>
        <input type='submit' value='Log In'>
      </fieldset>
    </form>

    <form id='registrationForm'>
      <fieldset>
        <legend>Register</legend>
        <br>Username:
        <input type='text' name='username'>
        <br><br>Password:
        <input type='password' name='password'>
        <br><br>
        <input type='submit' value='Register'>
      </fieldset>
    </form>
  </div>

  <div id="memo-pane" data-inactive>
    <form id='memoForm'>
      <fieldset>
        <legend>Write a Memo</legend>
        <br>Email Address To Send To:
          <input type='text' name='email-to'>
          <br><br>Time To Send Memo:
          <select name='timestamp'></select>
        <br><br>Memo:
        <br><textarea rows='6' cols='40' name='memo'></textarea>
        <p><input type='submit' value='Submit'></p>
      </fieldset>
    </form>
    <form id='logoutForm'>
      <fieldset>
        <legend>Log Out</legend>
        <p><input type='submit' value='Log Out'></p>
      </fieldset>
    </form>
  </div>

  <script src="/apiserver.js"></script>
  <script src="/dates.js"></script>
  <script src="/main.js"></script>
</body>
</html>

