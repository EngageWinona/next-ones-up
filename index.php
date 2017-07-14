<!DOCTYPE html>
<html class="mdc-typography">
  <head>
    <title>Material Components for the web</title>
    <meta name="google-signin-scope" content="profile email">
    <meta name="google-signin-client_id" content="<?php echo $client_id ?>">
    <script src="https://apis.google.com/js/platform.js" async defer></script>
    <link rel="stylesheet"
          href="node_modules/material-components-web/dist/material-components-web.css">
    <style>
  	  #sortable { list-style-type: none; margin: auto; padding: 0; width: 95%; padding-top: 10px}
  	  #sortable li { margin: 0 5px 5px 5px; padding: 5px; font-size: 1.2em; height: 1.5em; overflow: hidden; white-space: nowrap;}
  	  html>body #sortable li { height: 1.5em; line-height: 1.2em; }
  	  .ui-state-highlight { height: 1.5em; line-height: 1.2em; }

      .mdc-toolbar {
        background-color: black;
      }
      .mdc-toolbar__section--align-end {
        margin-right: 16px;
      }

      .sign-out {
        color: white;
      }

      .mdc-card {
        margin: auto;
        margin-top: 75px;
        width: 90%;
      }

      /* initialize it off screen. */
      .loading .example .mdc-snackbar { transform: translateY(100%); }

      /* Override style for hero example. */
      .hero .mdc-snackbar {
        position: fixed;
        left: auto;
      }
      .hero .mdc-snackbar--active {
        transform: none;
      }
      .MyClass {
        display: none;
      }
    </style>
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  	<script>
    $( function() {
      $( "#sortable" ).sortable({
        placeholder: "ui-state-highlight"
      });
      $( "#sortable" ).disableSelection();
    } );
    </script>
  </head>
  <body>
    <header class="mdc-toolbar mdc-toolbar--fixed">
      <div class="mdc-toolbar__row">
        <section class="mdc-toolbar__section mdc-toolbar__section--align-start">
          <span class="mdc-toolbar__title">Next Ones Up</span>
        </section>
        <section class="mdc-toolbar__section mdc-toolbar__section--align-end">
          <div class="g-signin2" id="g-signin2" data-onsuccess="onSignIn" data-theme="dark"></div>
          <a class="sign-out" id="sign-out" href="#" onclick="signOut();">Sign out</a>
        </section>
      </div>
    </header>
    <?php

      include_once("credentials.php");

    	$all_issues = 'https://mischief-studios.atlassian.net/rest/api/2/search?jql=project%20%3D%20IDEAS%20ORDER%20BY%20created%20ASC';

    	$curlSession = curl_init($all_issues);
    	curl_setopt($curlSession, CURLOPT_USERPWD, "$username:$password");
    	curl_setopt($curlSession, CURLOPT_BINARYTRANSFER, true);
      curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curlSession, CURLOPT_SSL_VERIFYPEER, false);
    	curl_setopt($curlSession, CURLOPT_SSL_VERIFYHOST, false);
    	curl_setopt($curlSession, CURLOPT_CUSTOMREQUEST, "GET");
    	curl_setopt($curlSession, CURLOPT_HTTPHEADER, array(
    		'Accept: application/json',
    		'Content-Type: application/json')
    	);

    	$jiraReturn = json_decode(curl_exec($curlSession));


    	echo '<main class="mdc-toolbar-fixed-adjust"><div class="mdc-card"><section class="mdc-card__actions mdc-card__actions--vertical"><ul id="sortable" class="mdc-list mdc-list--two-line">';

    	foreach ($jiraReturn->issues as $key => $value) {
    		echo ' <li class="ui-state-default mdc-list-item"><span class="mdc-list-item__text">' . $value->fields->summary . '<span class="mdc-list-item__text__secondary">' . $value->fields->description . '</span></span></li>';
    	}

    	// echo '<pre>';
    	// print_r($jiraReturn->issues);
    	// echo '</pre>';

    ?>
    <!-- <main class="mdc-toolbar-fixed-adjust">
      <ul class="mdc-list">
        <li class="mdc-list-item">Single-line item</li>
        <li class="mdc-list-item">Single-line item</li>
        <li class="mdc-list-item">Single-line item</li>

         -->
        </ul>
       </section>
      </div>
      <section class="hero">
       <div id="mdc-snackbar"
            class="mdc-snackbar mdc-snackbar--active"
            aria-live="assertive"
            aria-atomic="true"
            aria-hidden="true">
         <div class="mdc-snackbar__text">You are signed in</div>
       </div>
     </section>
    </main>
    <script src="node_modules/material-components-web/dist/material-components-web.js"></script>
    <script>mdc.autoInit()</script>
    <script>
      var toolbar = mdc.toolbar.MDCToolbar.attachTo(document.querySelector('.mdc-toolbar'));
      toolbar.fixedAdjustElement = document.querySelector('.mdc-toolbar-fixed-adjust');
    </script>
    <script>
      function onSignIn(googleUser) {
        // Useful data for your client-side scripts:
        var profile = googleUser.getBasicProfile();
        console.log("ID: " + profile.getId()); // Don't send this directly to your server!
        console.log('Full Name: ' + profile.getName());
        console.log('Given Name: ' + profile.getGivenName());
        console.log('Family Name: ' + profile.getFamilyName());
        console.log("Image URL: " + profile.getImageUrl());
        console.log("Email: " + profile.getEmail());

        // The ID token you need to pass to your backend:
        var id_token = googleUser.getAuthResponse().id_token;
        console.log("ID Token: " + id_token);
        document.getElementById('g-signin2').style.display = 'none';
        document.getElementById('sign-out').style.display = 'block';
      }

      function signOut() {
        var auth2 = gapi.auth2.getAuthInstance();
        auth2.signOut().then(function () {
          console.log('User signed out.');
          document.getElementById('sign-out').style.display = 'none';
          document.getElementById('g-signin2').style.display = 'block';
          document.getElementById("mdc-snackbar").classList.remove("mdc-snackbar--active");
        });
      }

      window.onload = function() {
        var auth2 = gapi.auth2.getAuthInstance();
        if (auth2.isSignedIn.get() == true) {
          // document.getElementById('g-signin2').style.display = 'none';
          // document.getElementById('sign-out').style.display = 'block';
          // document.getElementById("mdc-snackbar").classList.remove("mdc-snackbar--active");
        } else {

          // document.getElementById('sign-out').style.display = 'none';
          // document.getElementById('g-signin2').style.display = 'block';
          // document.getElementById("mdc-snackbar").classList.remove("mdc-snackbar--active");
        }
      }

    </script>
  </body>
</html>
