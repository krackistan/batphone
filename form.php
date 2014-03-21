<?php
//start session
session_start();

// prints form
function print_form(){
?>
<p><span class="required">*</span> Required fields</p>
	<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>" id="uploadform" enctype="multipart/form-data">
	
	<p><label for="emailfrom">Sender <span class="required">*</span></label>
	<input name="emailfrom" id="emailfrom" type="text" class="field" value="<?= $_SESSION['bpForm']['emailfrom']; ?>" tabindex="1"/></p>
	
	<p><label for="recipient">Recipient <span class="required">*</span></label>
	<input name="recipient" id="recipient" type="text" class="field" value="<?= $_SESSION['bpForm']['recipient']; ?>" tabindex="2"/></p>

  <!-- remove subject line (may be useful later)
	<p><label for="subject">Subject <span class="required">*</span></label>
	<input name="subject" id="subject" type="text" class="field" value="<?= $_SESSION['bpForm']['subject']; ?>" tabindex="3"/></p>
  -->	

	<p><label for="comments">Message <span class="required">*</span></label>
	<textarea name="comments" id="comments" rows="7" cols="10" class="field" tabindex="4"><?= $_SESSION['bpForm']['comments']; ?></textarea></p>
	
  <!-- remove mms image uploads for now
  <p><label for="attachment">File Upload<br />(1 file only, max file size 1024kb.)</label>
	<input name="attachment" id="attachment" type="file" tabindex="5">
  -->	

	<p><input type="submit" name="submit" id="submit" value="Send"  tabindex="6"/></p>
	<p><input type="hidden" name="submitted"  value="true" /></p>
	</form>
<?php
}

// enquiry form validation

function process_form() {
	// Read POST request params into global vars
	// FILL IN YOUR EMAIL
	$to = trim($_POST['recipient']);
	//$subject = trim($_POST['subject']);
	$recipient = trim($_POST['recipient']);
	$emailfrom = trim($_POST['emailfrom']);
	$comments = stripslashes($_POST['comments']);
	
	// Allowed file types. add file extensions WITHOUT the dot.
	//$allowtypes=array("zip", "rar", "doc", "pdf", "png", "jpg", "gif");
  $allowtypes=array("jpg","jpeg","png","gif");	

	// Require a file to be attached: false = Do not allow attachments true = allow only 1 file to be attached
	$requirefile="false";
	
	// Maximum file size for attachments in KB (not bytes)
	$max_file_size="2048";
	
	// Thank you message
	$thanksmessage="Thank you for submitting your incident report! Your house-level managers have been notified via SMS.";

	$errors = array(); //Initialize error array

	//checks for a name
	if (empty($_POST['recipient']) ) {
		$errors[]='You forgot to enter the recipient address';
		}

	//checks for a subject
	//if (empty($_POST['subject']) ) {
	//	$errors[]='You forgot to enter a subject';
	//	}

	//checks for a message
	if (empty($_POST['comments']) ) {
		$errors[]='Please fill in the message body before sending.';
		}
		
		
	//checks attachment file
	// checks that we have a file
	if((!empty($_FILES["attachment"])) && ($_FILES['attachment']['error'] == 0)) {
			// basename -- Returns filename component of path
			$filename = basename($_FILES['attachment']['name']);
			$ext = substr($filename, strrpos($filename, '.') + 1);
			$filesize=$_FILES['attachment']['size'];
			$max_bytes=$max_file_size*1024;
			
			//Check if the file type uploaded is a valid file type. 
			if (!in_array($ext, $allowtypes)) {
				$errors[]="Invalid extension for your file: <strong>".$filename."</strong>";
				
		// check the size of each file
		} elseif($filesize > $max_bytes) {
				$errors[]= "Your file: <strong>".$filename."</strong> is to big. Max file size is ".$max_file_size."kb.";
			}
			
	} // if !empty FILES

	if (empty($errors)) { //If everything is OK
		
		// send an email
		// Obtain file upload vars
		$fileatt      = $_FILES['attachment']['tmp_name'];
		$fileatt_type = $_FILES['attachment']['type'];
		$fileatt_name = $_FILES['attachment']['name'];
		
		// Headers
		$headers = "From: $emailfrom";
		
		// create a boundary string. It must be unique
		  $semi_rand = md5(time());
		  $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";

		  // Add the headers for a file attachment
		  $headers .= "\nMIME-Version: 1.0\n" .
		              "Content-Type: multipart/mixed;\n" .
		              " boundary=\"{$mime_boundary}\"";

		  // Add a multipart boundary above the plain message
		  $message ="This is a multi-part message in MIME format.\n\n";
		  $message.="--{$mime_boundary}\n";
		  $message.="Content-Type: text/plain; charset=\"iso-8859-1\"\n";
		  $message.="Content-Transfer-Encoding: 7bit\n\n";
		  $message.="".$comments."\n\n";
		
		if (is_uploaded_file($fileatt)) {
		  // Read the file to be attached ('rb' = read binary)
		  $file = fopen($fileatt,'rb');
		  $data = fread($file,filesize($fileatt));
		  fclose($file);

		  // Base64 encode the file data
		  $data = chunk_split(base64_encode($data));

		  // Add file attachment to the message
		  $message .= "--{$mime_boundary}\n" .
		              "Content-Type: {$fileatt_type};\n" .
		              " name=\"{$fileatt_name}\"\n" .
		              //"Content-Disposition: attachment;\n" .
		              //" filename=\"{$fileatt_name}\"\n" .
		              "Content-Transfer-Encoding: base64\n\n" .
		              $data . "\n\n" .
		              "--{$mime_boundary}--\n";
		}
		
		
		// Send the completed message
		

		
		if(!mail($to,$subject,$message,$headers,"-femail@domain.com")) {
			exit("Mail could not be sent. Sorry! An error has occurred, please report this to caznm@bsc.coop\n");
		} else {
			echo '<div id="formfeedback"><h3>Great Success</h3><p>'. $thanksmessage .'</p></div>';
			unset($_SESSION['bpForm']);
			print_form();
			
		} // end of if !mail
		
	} else { //report the errors
		echo '<div id="formfeedback"><h3>Error!</h3><p>The following error(s) have occured:<br />';
		foreach ($errors as $msg) { //prints each error
				echo " - $msg<br />\n";
			} // end of foreach
		echo '</p><p>Please try again</p></div>';
		print_form();
	} //end of if(empty($errors))

} // end of process_form()

?>
