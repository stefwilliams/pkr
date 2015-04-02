<?php
/**
 * The class_exists() check is recommended, to prevent problems during upgrade
 * or when the Groups component is disabled
 */
if ( class_exists( 'BP_Group_Extension' ) ) :
  
class Pkr_Add_Game extends BP_Group_Extension {
    /**
     * Here you can see more customization of the config options
     */
    function __construct() {
        $args = array(
            // see https://codex.buddypress.org/developer/group-extension-api/#config
            'slug' => 'add-game',
            'name' => 'Pkr_Game',
            'nav_item_position' => 50,
            'nav_item_name' => 'Add Game',
            'screens' => array(
                'edit' => array(
                    'name' => 'Game Defaults',
                    // Changes the text of the Submit button
                    // on the Edit page
                    // 'submit_text' => 'Submit, suckaz',
                ),
                'create' => array(
                    'enabled' => false,
                ),
            ),
        );
        parent::init( $args );
    }
 
    function display() {
        echo do_shortcode('[event_form]' );
    }
 
    function settings_screen( $group_id = NULL ) {
        $buyin = groups_get_groupmeta( $group_id, 'pkr_default_buyin' );
        $start_time = groups_get_groupmeta( $group_id, 'pkr_default_start' );
        $disable_end_date = groups_get_groupmeta( $group_id, 'pkr_disable_end_date' );
        $disable_end_time = groups_get_groupmeta( $group_id, 'pkr_disable_end_time' );
 
        ?>
        <p>Default buy-in (eg, 10.00): £ <input type="text" name="pkr_default_buyin" style="width:initial;" size="3" value="<?php echo esc_attr( $buyin ) ?>" /></p>
        <p>Default start time: <input id="start-time" style="width:initial;" class="em-time-input em-time-start" type="text" size="8" maxlength="8" name="pkr_default_start" value="<?php echo $start_time; ?>" /></p>
        <p>Do not require end time: <input type="checkbox" name="pkr_disable_end_time" value="1" <?php if ($disable_end_time) {
            echo 'checked';
        }?> /></p>
        <p>Do not require end date: <input type="checkbox" name="pkr_disable_end_date" value="1" <?php if ($disable_end_date) {
            echo 'checked';
        }?>/></p>

        
        <?php
        // em_locate_template('forms/event/when.php',true);
    }
 
    function settings_screen_save( $group_id = NULL ) {
        $buyin = isset( $_POST['pkr_default_buyin'] ) ? $_POST['pkr_default_buyin'] : '';
        groups_update_groupmeta( $group_id, 'pkr_default_buyin', $buyin );
        $start_time = isset( $_POST['pkr_default_start'] ) ? $_POST['pkr_default_start'] : '';
        groups_update_groupmeta( $group_id, 'pkr_default_start', $start_time );
        $disable_end_date = isset( $_POST['pkr_disable_end_date'] ) ? $_POST['pkr_disable_end_date'] : '';
        groups_update_groupmeta( $group_id, 'pkr_disable_end_date', $disable_end_date );
        $disable_end_time = isset( $_POST['pkr_disable_end_time'] ) ? $_POST['pkr_disable_end_time'] : '';
        groups_update_groupmeta( $group_id, 'pkr_disable_end_time', $disable_end_time );        
    }
 
    /**
     * create_screen() is an optional method that, when present, will
     * be used instead of settings_screen() in the context of group
     * creation.
     *
     * Similar overrides exist via the following methods:
     *   * create_screen_save()
     *   * edit_screen()
     *   * edit_screen_save()
     *   * admin_screen()
     *   * admin_screen_save()
     */
 
}
bp_register_group_extension( 'Pkr_Add_Game' );
 
endif;