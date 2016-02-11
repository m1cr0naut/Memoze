<?php
// This PHP file implements Memoze automailing functionality.

// our MySQL credentials
define("DB_USERNAME", "comp484-lab4");
define("DB_PASSWORD", "7B5qDQY2GFnZgvU3");
define("DB_NAME", "comp484-lab4");

$db = new mysqli("localhost", DB_USERNAME, DB_PASSWORD, DB_NAME);
if ($db->connect_errno) {
  echo "Failed 0\n";
  return;
}

// check for Memoze to deliver
$query = $db->prepare("SELECT msgid, email_address, message FROM message WHERE NOT sent AND timestamp <= UNIX_TIMESTAMP(NOW())*1000");
if (!$query) {
  // Unexpected failure
  echo "Failed 1\n";
  return;
}

$sent_emails = array();

$msgid = NULL;
$recipient = NULL;
$message = NULL;
$query->bind_result($msgid, $recipient, $message);
$query->execute();
while ($query->fetch()) {
  $sent_emails[] = $msgid;
  mail($recipient, "You got a Memoze!", $message, "From: comp484@beta.jonathan.com");
}
$query->close();

// set sent status on sent Memoze to true
foreach ($sent_emails as $msgid) {
  $query = $db->prepare("UPDATE message SET sent = 1 WHERE msgid = ?");
  if (!$query) {
    // Unexpected failure
    continue;
  }
  $query->bind_param("s", $msgid);
  $query->execute();
  $query->close();
}

echo var_dump($sent_emails);
