// Loads on course_meta.php
jQuery(document).ready(function() {
//conditional code for Awarding body levels on course_meta.php
  		function toggleAward (){
	  		if (jQuery('select#awarding_body').val() !== 'none'){
	  			jQuery("#award_level").css("display","block");
	  		} else {
	  			jQuery("#award_level").css("display","none");
	  		}
	  	}	
	  	toggleAward ();

  		jQuery('select#awarding_body').change(function(){
  			toggleAward ();
  		});

  		// add some code to check that ONE course category and at least one training provider has been selected

 });