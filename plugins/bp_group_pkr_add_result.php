<?php
/**
 * The class_exists() check is recommended, to prevent problems during upgrade
 * or when the Groups component is disabled
 */
if ( class_exists( 'BP_Group_Extension' ) ) :
  
class Pkr_Add_Result extends BP_Group_Extension {
    /**
     * Here you can see more customization of the config options
     */
    function __construct() {
        $args = array(
            // see https://codex.buddypress.org/developer/group-extension-api/#config
            'slug' => 'add-result',
            'name' => 'Pkr_Add_Result',
            'nav_item_position' => 40,            
            'nav_item_name' => 'Add Result',
            'screens' => array(
                'create' => array(
                    'name' => 'Ruleset',
                    'position' => 90,
                    'slug' => 'ruleset',
                ),
            ),
        );
        parent::init( $args );
    }
 
    function display() {
        locate_template('pkr_templates/add-results.php',true);
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
    function create_screen( $group_id = NULL ) {
        $ruleset = groups_get_groupmeta( $group_id, 'pkr_group_ruleset' );
 
        ?>
        <?php _e('Game scoring ruleset.
                        Choose the type of game and scoring mechanism that will apply to this league. 
                        This cannot be changed once the league is created. In order to use a different ruleset, you will need to create a new league.'); ?>
        Save your plugin setting here: <input type="text" name="pkr_group_ruleset" value="<?php echo esc_attr( $ruleset ) ?>" />
        <?php
    }
 
}
bp_register_group_extension( 'Pkr_Add_Result' );
 
endif;