/* This JavaScript file implements our Remote Procedure Call Gateway. */

(function(window) {
  function asynchronousJSON(uri, request_body, oncomplete) {
    if (!oncomplete) {
      oncomplete = function(body, successful) {};
    }

    var xhr = new XMLHttpRequest();
    xhr.open("POST", uri, true);

    xhr.onreadystatechange = function() {
      if (xhr.readyState !== XMLHttpRequest.DONE) {
        return;
      }

      if (xhr.status === 200) {
        oncomplete(JSON.parse(xhr.responseText), true);
      } else {
        oncomplete(JSON.parse(xhr.responseText), false);
      }
    };

    xhr.send(JSON.stringify(request_body));
  }


  // Bind some RPC invocations to local Javascript function calls
  var Server = {
    register: function(username, password, onsuccess, onfailure) {
      asynchronousJSON("/api.php?action=register", {
        "username": username,
        "password": password,
      }, function(response, success) {
        if (success) {
          onsuccess(response);
        } else {
          onfailure(response);
        }
      });
    },

    login: function(username, password, onsuccess, onfailure) {
      asynchronousJSON("/api.php?action=login", {
        "username": username,
        "password": password,
      }, function(response, success) {
        if (success) {
          onsuccess(response);
        } else {
          onfailure(response);
        }
      });
    },

    logout: function(onsuccess, onfailure) {
      asynchronousJSON("/api.php?action=logout", {
      }, function(response, success) {
        if (success) {
          onsuccess(response);
        } else {
          onfailure(response);
        }
      });
    },

    memo: function(recipient, message, delivery_time, onsuccess, onfailure) {
      asynchronousJSON("/api.php?action=memo", {
        "recipient": recipient,
        "message": message,
        "delivery_time": delivery_time,
      }, function(response, success) {
        if (success) {
          onsuccess(response);
        } else {
          onfailure(response);
        }
      });
    },
  };

  window.APIServer = Server;
})(window);
