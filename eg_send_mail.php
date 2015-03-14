<?php


	function sendprovidermail() {
		$lmw_from = 'Leadership &amp; Management Wales';
		$lmw_email = 'lmw@cardiff.ac.uk';
		$recipient = $_POST ['recipient'];
		$subject = $_POST ['subject'];
		$course_info = $_POST ['course_info'];
		$sender_name = $_POST ['sender_name'];
		$sender_phone = $_POST ['sender_phone'];
		$sender_email = $_POST ['sender_email'];
		$sender_company = $_POST ['sender_company'];
		$sender_company_size = $_POST ['sender_company_size'];
		$message = $_POST ['message'];
		$message = stripslashes($message);
		$self_send = $_POST ['self_send'];
		$nonce = $_POST ['nonce'];
		$noncecheck = check_ajax_referer( 'provider_contact', 'nonce' );


		global $wpdb;

//recipient details
		$recipient_name=get_the_title( $recipient );
		$recipient_address=get_post_meta( $recipient, 'address', true );
		$recipient_address=wpautop( $recipient_address, true );
		$recipient_postcode=get_post_meta( $recipient, 'postcode', true );
		$recipient_telephone=get_post_meta( $recipient, 'telephone', true );
		$recipient_website=get_post_meta( $recipient, 'website', true );
		$recipient_course_url=get_post_meta( $recipient, 'course_url', true );
		$recipient_contact_name=get_post_meta( $recipient, 'contact_name', true );
		$recipient_email=get_post_meta( $recipient, 'contact_email', true );
		$recipient_mobile=get_post_meta( $recipient, 'mobile', true );
		$recipient_regions=get_post_meta( $recipient, 'regions', true );
		$recipient_regions=implode(", ", $recipient_regions);


//main message for all recipients
$fullmessage = <<<STR
<p>$message </p>
<h4>Contact Details:</h4>
<p>Name: $sender_name</p>
<p>Tel: $sender_phone</p>
<p>Email: $sender_email</p>
<p>Company: $sender_company</p>
<p>Company Size: $sender_company_size</p>
STR;

		$headers[]="Content-type: text/html";

//self_send: custom message to sender
		if ($self_send == 'send') {
$sender_message = <<<SMG
<p>This email was sent on your behalf to $recipient_name ($recipient_email):</p>
<hr />
$fullmessage
SMG;
	$headers[]='From: '.$lmw_from.' <'.$lmw_email.'>';
	wp_mail( $sender_email, 'Your message to a Training Provider', $sender_message, $headers);
		}
//end self_send


//LMW email
$lmw_message = <<<LMW
<p>This email was sent from the LMW website to $recipient_name ($recipient_email):</p>
<hr />
$fullmessage
LMW;
		$lmw_headers = $headers;
		$lmw_headers[] = 'From: '.$sender_name.' <'.$sender_email.'>';
		wp_mail( $lmw_email, 'LMW Course List Contact: '.$sender_name.' to '.$recipient_name, $lmw_message, $lmw_headers );
//end LMW email


//TP email
$tpmessage = <<<TPM
<p>The following message was sent via the LMW website:</p>
<hr />
$fullmessage
<hr />
<h3>Your records with LMW</h3>
<p>Please note your details are listed on LMW's Training Provider page as below.</p>
<p>If any of these details are incorrect, please contact <a href="$lmw_email">LMW</a> on $lmw_email.</p>
<br />
<h4>Company details</h4>
<p>Title: $recipient_name</p>
<p>Address: $recipient_address</p>
<p>Postcode: $recipient_postcode</p>
<p>Phone: $recipient_telephone</p>
<p>Main website: $recipient_website</p>
<p>Courses website: $recipient_course_url</p>
<p>Regions covered: $recipient_regions</p>
<br />
<h4>Main contact details</h4>
<p>Contact name: $recipient_contact_name</p>
<p>Contact email: $recipient_email</p>
<p>Contact mobile: $recipient_mobile</p>


<p>COURSE: $course_info</p>
TPM;

		wp_mail($recipient_email, 'Enquiry through LMW website re: '.$course_info, $tpmessage, $lmw_headers);
		//wp_mail($lmw_email, 'Enquiry through LMW website re: '.$course_info, $tpmessage, $lmw_headers);
		wp_mail('williamssp5@cf.ac.uk', 'Enquiry through LMW website TO TP re: '.$course_info, $tpmessage, $lmw_headers);

//end TP email


//wp_mail( $to, $subject, $message, $headers = '', $attachments = array );
		$success = false;
// return values to	sidebar form
		if ($noncecheck == 1) {
			$success = true;
		}

		if($success) {
			echo 'Your message: "'.$subject.'" was successfully sent to '.$recipient_name.'. The Training Provider should contact you directly within the next two weeks.';
		} else {
			echo 'There was a problem. Please try again.';
		}
		die();

	}
	add_action( 'wp_ajax_sendprovidermail', 'sendprovidermail');
?>