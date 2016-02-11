<?php
/*
 * This PHP file implements a PHP/JSON web service API that allows our core
 * Memoze functionality, including user account registration, login and
 * scheduling of Memoze. */

// our MySQL credentials
define("DB_USERNAME", "comp484-lab4");
define("DB_PASSWORD", "7B5qDQY2GFnZgvU3");
define("DB_NAME", "comp484-lab4");

function register_user($username, $password) {
  $db = new mysqli("localhost", DB_USERNAME, DB_PASSWORD, DB_NAME);
  if ($db->connect_errno) {
    header("HTTP/1.1 500 Internal Server Error");
    echo json_encode(array("msg" => "Could not connect to database"));
    return;
  }

  // check if new account name provided already exists
  $query = $db->prepare("SELECT COUNT(*) FROM user WHERE username = ?");
  if (!$query) {
    // Unexpected failure
    header("HTTP/1.1 500 Internal Server Error");
    echo json_encode(array("msg" => "Unexpected failure of type 1"));
    return;
  }

  $query->bind_param("s", $username);
  $query->bind_result($user_count);
  $query->execute();
  $query->store_result();
  $query->fetch();
  $query->free_result();
  $query->close();

  if ($user_count > 0) {
    header("HTTP/1.1 409 Conflict");
    echo json_encode(array("msg" => "User already exists"));
    return;
  }

  // insert new user account into our database
  $query = $db->prepare("INSERT INTO user (username, password) VALUES (?, ?)");
  if (!$query) {
    // Unexpected failure
    header("HTTP/1.1 500 Internal Server Error");
    echo json_encode(array("msg" => "Unexpected failure of type 2"));
    return;
  }

  $query->bind_param("ss", $username, $password);
  $query->execute();
  $query->store_result();
  $success = ($query->affected_rows === 0);
  $query->free_result();
  $query->close();

  if ($success) {
    // Unexpected failure
    header("HTTP/1.1 500 Internal Server Error");
    echo json_encode(array("msg" => "Unexpected failure of type 3"));
    return;
  }

  // locate newly created user's ID for Memoze creation
  $query = $db->prepare("SELECT userid FROM user WHERE username = ?");
  if (!$query) {
    // Unexpected failure
    header("HTTP/1.1 500 Internal Server Error");
    echo json_encode(array("msg" => "Unexpected failure of type 4"));
    return;
  }

  $user_id = NULL;
  $query->bind_param("s", $username);
  $query->bind_result($user_id);
  $query->execute();
  $query->store_result();
  if ($query->num_rows > 0) {
    $query->fetch();
  } else {
    $user_id = NULL;
  }
  $query->free_result();
  $query->close();

  if ($user_id === NULL) {
    header("HTTP/1.1 400 Bad Request");
    echo json_encode(array("msg" => "Unexpected failure of type 5"));
    return;
  }

  session_start();
  $_SESSION["username"] = $user_id;
  session_commit();

  echo json_encode(array("msg" => "Success"));
}

function login_user($username, $password) {
  $db = new mysqli("localhost", DB_USERNAME, DB_PASSWORD, DB_NAME);
  if ($db->connect_errno) {
    header("HTTP/1.1 500 Internal Server Error");
    echo json_encode(array("msg" => "Could not connect to database"));
    return;
  }

  // locate existing user via their credentials
  $query = $db->prepare("SELECT userid FROM user WHERE username = ? AND password = ?");
  if (!$query) {
    // Unexpected failure
    header("HTTP/1.1 500 Internal Server Error");
    echo json_encode(array("msg" => "Unexpected failure of type 1"));
    return;
  }

  $user_id = NULL;
  $query->bind_param("ss", $username, $password);
  $query->bind_result($user_id);
  $query->execute();
  $query->store_result();
  if ($query->num_rows > 0) {
    $query->fetch();
  } else {
    $user_id = NULL;
  }
  $query->free_result();
  $query->close();

  if ($user_id === NULL) {
    header("HTTP/1.1 400 Bad Request");
    echo json_encode(array("msg" => "Invalid credentials"));
    return;
  }

  session_start();
  $_SESSION["username"] = $user_id;
  session_commit();

  echo json_encode(array("msg" => "Success", "userid" => $_SESSION["username"]));
}

function logout_user() {
  session_start();
  if (!array_key_exists("username", $_SESSION)) {
    header("HTTP/1.1 400 Bad Request");
    echo json_encode(array("msg" => "User not logged in"));
  }

  unset($_SESSION["username"]);
  session_commit();

  echo json_encode(array("msg" => "Success"));
}

function add_memo($recipient, $message, $delivery_time) {
  session_start();
  if (array_key_exists("username", $_SESSION)) {
    $user_id = $_SESSION["username"];
  } else {
    $user_id = NULL;
  }
  session_commit();

  if ($user_id === NULL) {
    header("HTTP/1.1 400 Bad Request");
    echo json_encode(array("msg" => "Invalid credentials"));
    return;
  }

  // open database connection
  $db = new mysqli("localhost", DB_USERNAME, DB_PASSWORD, DB_NAME);
  if ($db->connect_errno) {
    header("HTTP/1.1 500 Internal Server Error");
    echo json_encode(array("msg" => "Could not connect to database"));
    return;
  }

  // create a new Memo
  $query = $db->prepare("INSERT INTO message (userid, email_address, message, timestamp) VALUES (?, ?, ?, ?)");
  if (!$query) {
    // Unexpected failure
    header("HTTP/1.1 500 Internal Server Error");
    echo json_encode(array("msg" => "Unexpected failure of type 2"));
    return;
  }

  $query->bind_param("issi", $user_id, $recipient, $message, $delivery_time);
  $query->execute();
  $query->store_result();
  $success = ($query->affected_rows > 0);
  $query->free_result();
  $query->close();

  if (!$success) {
    // Unexpected failure
    header("HTTP/1.1 500 Internal Server Error");
    echo json_encode(array("msg" => "Unexpected failure of type 3"));
    return;
  }

  echo json_encode(array("msg" => "Success"));
}

// our API request handler: Decodes the Remote Procedure Calls into regular PHP calls
function route_request() {
  if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("HTTP/1.1 405 Method Not Allowed");
    header("Allow: POST");
    echo "Invalid method";
    exit;
  }

  $json_params = json_decode(file_get_contents("php://input"), true);
  switch ($_GET["action"]) {
  case "register": {
    if (!array_key_exists("username", $json_params) || !array_key_exists("password", $json_params)) {
      header("HTTP/1.1 400 Bad Request");
      echo json_encode(array("msg" => "Missing username and/or password parameters"));
    } else {
      register_user($json_params["username"], $json_params["password"]);
    }
  } break;

  case "login": {
    if (!array_key_exists("username", $json_params) || !array_key_exists("password", $json_params)) {
      header("HTTP/1.1 400 Bad Request");
      echo json_encode(array("msg" => "Missing username and/or password parameters"));
    } else {
      login_user($json_params["username"], $json_params["password"]);
    }
  } break;

  case "memo": {
    if (!array_key_exists("recipient", $json_params)
    ||  !array_key_exists("message", $json_params)
    ||  !array_key_exists("delivery_time", $json_params)
    ) {
      header("HTTP/1.1 400 Bad Request");
      echo json_encode(array("msg" => "Missing recipient, message, and/or delivery_time parameters"));
    } else {
      add_memo($json_params["recipient"], $json_params["message"], $json_params["delivery_time"]);
    }
  } break;

  case "logout": {
    logout_user();
  } break;

  default: {
    header("HTTP/1.1 400 Bad Request");
    echo json_encode(array("msg" => "Invalid API action"));
  } break;
  }
}

route_request();
