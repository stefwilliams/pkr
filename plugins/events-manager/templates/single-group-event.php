<?php

/* 

This page is loaded into Buddypress Group Events section 

 */

global $EM_Event;

?>
<h1 class="entry-title"><?php echo $EM_Event->output('#_EVENTNAME'); ?></h1>

<?php
/* @var $EM_Event EM_Event */
echo $EM_Event->output_single();

?>