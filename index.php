<?php
//start session
session_start();

// Include all the output functions
require_once('form.php'); 

// populate input fields into the session using a sub-array
$_SESSION['myForm'] = $_POST;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">

<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>CZ Batphone</title>
  <link href="main.css" rel="stylesheet"> 
</head>
<body>
  <div class="wrapper">
	  <div class="form-header">
      <h1>BatPhone</h1>
	  </div>
    <div class="report-form">
	  <?php
	  // contact form
	  if (isset($_POST['submitted']) && ('true' == $_POST['submitted'])) { 
		  // checks if the form is submitted and then processes it
    	  process_form(); 
		
	  } else { 
		  // else prints the form
    	  print_form(); 
	  }
	  ?>
  </div>	
</div>
<div class="footer">
  <span class="blurb">BatPhone is a web interface for contacting house level managers at Casa Zimbabwe via SMS.</span>
  <br>
  <a href="https://github.com/krackistan/batphone">Source</a>
  <a href="https://github.com/krackistan/batphone/issues">Bug Reports</a>
  <a href="mailto:tc@ischool.berkeley.edu">Help</a>
</div>
</body>
</html>
<?php session_destroy(); //unset session data ?>
