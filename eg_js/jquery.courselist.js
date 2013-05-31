jQuery(document).ready(function() {

//popovers
	jQuery('.award_body').on('click',function() {
	 		jQuery('.popover').hide();
		}

	);

	jQuery('.container h4').click(function(){
		jQuery('.popover').hide();
	});

	jQuery (".award_body").popover({
		html : true,
		content: function() {
     	 return jQuery(this).next(".popover_content_wrapper").html();
    	}

	});

	// jQuery (".modal_window").draggable({
	// 	handle: ".modal-header"
	// });

//sendmails
	jQuery ('.sendmail').on('click', function() {
							//find which form we're working with
					var tid=this.id;
					//validate and send 
					var recipient = jQuery('#'+tid+' .recipient').val();
					var subject = jQuery('#'+tid+' .subject').val();
					var course_info = jQuery('#'+tid+' .course_info').val();
					var sender_name = jQuery('#'+tid+' input.sender_name').val();
					var sender_phone = jQuery('#'+tid+' input.sender_phone').val();
					var sender_email = jQuery('#'+tid+' input.sender_email').val();
					var sender_company = jQuery('#'+tid+' input.sender_company').val();
					var sender_company_size = jQuery('#'+tid+' .sender_company_size').val();
					var message = jQuery('#'+tid+' textarea.message').val();
					var nonce = jQuery('#'+tid+' input.nonce').val();

					if (recipient == "") {
						jQuery('#'+tid+' p.error_msg').hide();
						jQuery('#'+tid+' p.recipient_error').attr('style', 'display:block;');
						jQuery('#'+tid+' input.recipient').focus();	
						return false;					
					}

					if (sender_name == "") {
						jQuery('#'+tid+' p.error_msg').hide();
						jQuery('#'+tid+' p.name_error').attr('style', 'display:block;');
						jQuery('#'+tid+' input.sender_name').focus();
						return false;
					}
					if (sender_phone == "") {
						jQuery('#'+tid+' p.error_msg').hide();
						jQuery('#'+tid+' p.phone_error').attr('style', 'display:block;');
						jQuery('#'+tid+' input.sender_phone').focus();
						return false;
					}
					if (sender_email == "") {
						jQuery('#'+tid+' p.error_msg').hide();
						jQuery('#'+tid+' p.email_error').attr('style', 'display:block;');
						jQuery('#'+tid+' input.sender_email').focus();
						return false;
					}
					if (sender_company == "") {
						jQuery('#'+tid+' p.error_msg').hide();
						jQuery('#'+tid+' p.company_error').attr('style', 'display:block;');
						jQuery('#'+tid+' input.sender_company').focus();
						return false;
					}
					if (message == "") {
						jQuery('#'+tid+' p.error_msg').hide();
						jQuery('#'+tid+' p.message_error').attr('style', 'display:block;');
						jQuery('#'+tid+' textarea.message').focus();
						return false;
					}															
					if (jQuery ('#'+tid+' .self_send').prop('checked')) {
						var sg_self_send = 'send';
					} else {
						var sg_self_send = 'nosend';
					} 

					jQuery ('#'+tid+' fieldset').attr('style','display:none;');
					jQuery ('#'+tid+' .ajaxsending').attr('style','display:block;');
					jQuery ('#'+tid+' .ajaxsending').append ('<p class="sending">Please wait while your message is sent</p>').delay(50).append (' . ');
					
					var data = {
						action: "sendprovidermail",
						recipient: recipient,
						subject: subject,
						course_info: course_info,
						sender_name: sender_name,
						sender_phone: sender_phone,
						sender_email: sender_email,
						sender_company: sender_company,
						sender_company_size: sender_company_size,
						message: message,
						self_send: sg_self_send,
						nonce: nonce
					};
					jQuery.post(ajax_object.ajax_url, data, function(response) {
						jQuery ('#'+tid+' .contact-form').hide();
						jQuery ('#'+tid+' .providers').hide();
						jQuery ('#'+tid+' .ajaxsending').attr('style','display:none;');
						jQuery ('#'+tid+' .ajaxsent').attr('style','display:block;');
						jQuery ('#'+tid+' .ajaxsent').append (response);
						jQuery ('#'+tid+' .btn.sendmail').hide();
						jQuery ('#'+tid+' .also_sent').hide();
						jQuery ('#'+tid+' .btn.btn-danger').text('OK');
					});
					return false;


					// end validate and send
	}

	);

});