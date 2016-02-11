// This file implements Memoze event handlers.

(function() {
  document.getElementById(App.active_pane).removeAttribute("data-inactive");

  var registrationForm = document.getElementById("registrationForm");
  registrationForm.addEventListener("submit", function(ev) {
    ev.preventDefault();

    var username = this.querySelector("[name=username]").value;
    var password = this.querySelector("[name=password]").value;

    this.querySelector("[name=username]").value = "";
    this.querySelector("[name=password]").value = "";

    APIServer.register(username, password, function(response) {
      document.getElementById("memo-pane").removeAttribute("data-inactive");
      document.getElementById("login-pane").setAttribute("data-inactive", "data-inactive");
      console.log("[OK] ", response);
    }, function(response) {
      alert("Sorry, there was some kind of error.\nSee the console for details.");
      console.log("[ERR]", response);
    });
  });

  var loginForm = document.getElementById("loginForm");
  loginForm.addEventListener("submit", function(ev) {
    ev.preventDefault();

    var username = this.querySelector("[name=username]").value;
    var password = this.querySelector("[name=password]").value;

    this.querySelector("[name=username]").value = "";
    this.querySelector("[name=password]").value = "";

    APIServer.login(username, password, function(response) {
      console.log("[OK] ", response);
      document.getElementById("memo-pane").removeAttribute("data-inactive");
      document.getElementById("login-pane").setAttribute("data-inactive", "data-inactive");
    }, function(response) {
      console.log("[ERR]", response);
      alert("Sorry, there was some kind of error.\nSee the console for details.");
    });
  });

  var logoutForm = document.getElementById("logoutForm");
  logoutForm.addEventListener("submit", function(ev) {
    ev.preventDefault();

    APIServer.logout(function(response) {
      console.log("[OK] ", response);
      document.getElementById("login-pane").removeAttribute("data-inactive");
      document.getElementById("memo-pane").setAttribute("data-inactive", "data-inactive");
    }, function(response) {
      console.log("[ERR]", response);
      alert("Sorry, there was some kind of error.\nSee the console for details.");
    });
  });

  var memoForm = document.getElementById("memoForm");
  memoForm.addEventListener("submit", function(ev) {
    ev.preventDefault();

    var recipient = this.querySelector("[name=email-to]").value;
    var message = this.querySelector("[name=memo]").value;
    var delivery_time = this.querySelector("[name=timestamp]").options[this.querySelector("[name=timestamp]").selectedIndex].value;

    this.querySelector("[name=email-to]").value = "";
    this.querySelector("[name=memo]").value = "";
    this.querySelector("[name=timestamp]").selectedIndex = 0;

    APIServer.memo(recipient, message, delivery_time, function(response) {
      console.log("[OK] ", response);
      alert("Memo saved successfully.");
    }, function(response) {
      console.log("[ERR]", response);
      alert("Sorry, there was some kind of error.\nSee the console for details.");
    });
  });

  var timeSel = document.querySelector("#memoForm [name=timestamp]");
  var ourDates = getDates(48);
  setDates(ourDates, timeSel);
})(window);
