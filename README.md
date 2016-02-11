# Memoze
LAMP-stack application that allows users to create "memoze" (memos) and select a near-future date 
and time to automail them to recipients.  This was the final project for my web engineering elective 
course, done in tandem with classmate Jonathan Castello (https://github.com/Twisol).

My (Benjamin's) principal contributions include mailer implementation, date selection code (dates.js), 
solution front-end (via lab4.php and mailer.php), and MySQL database configuration and administration.

Overview
========

The overall architecture is based around a simple API server and a "fat" client application.
We chose to reduce the amount of PHP to the core necessary functionality.  As such, we have a thin 
database API interface in PHP which communicates using JSON, and offload the remaining functionality 
onto the client.

The client-side implements a single-page application architecture, communicating with the
aforementioned API server and updating the local view appropriately. To handle selection of
memo-sending times, we wrote a set of functions which generates a 24-hour set of 30-minute intervals
and dynamically inserts them into a pre-existing `<SELECT>`.

The mailer iterates over all messages in the message database which are older than NOW() and have
not yet been sent, sends them via mail(), and marks them as sent.

Server-side:
* `api.php`: Provides the JSON-based API mediating transfer between the user and the database.
* `mailer.php`: Iterates through the message database and sends anything which is ready to be sent
  and hasn't already been sent.
* `lab4.php`: User-facing single-page application.

Client-side:
* `dates.js`: Implements dynamic date-dropdown generation
* `apiserver.js`: Provides a simple RPC abstraction layer over AJAX
* `main.js`: Orchestrates user interaction and event handling

Notes
=====
* By using the 'prepared statements' feature of the mysqli driver, malicious users cannot perform
  SQL injection attacks. Thus, all inputs are acceptable.
* We implement a single-page application by having `lab484.php` simply render the HTML, and
  directing all interactive functionality to an `api.php` RPC endpoint.
