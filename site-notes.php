<?php
/*
Plugin Name: Site Notes
Plugin URI:  https://wordpress.org/plugins/site-notes/
Description: A plugin that adds a note box to  your posts and pages which can be viewed in the admin bar
Version:     2.0.0
Author:      KC Computing
Author URI:  https://profiles.wordpress.org/ktc_88
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: site-notes
*/



/**
 *  On activation create table to save notes
 */
function sn_install() {
    global $wpdb;
    $table_name = $wpdb->prefix . "notes"; 
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE $table_name (
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      note text NOT NULL,
      note_date text NOT NULL,
      UNIQUE KEY id (id)
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
}
register_activation_hook( __FILE__, 'sn_install' );



/**
 * Enqueue scripts and styles to front end.
 */
function sn_front_end_scripts() {
    wp_register_style('sn_styles', plugin_dir_url( __FILE__ ) . 'css/style.css'); 
    wp_register_script('sn_scripts', plugin_dir_url( __FILE__ ) . 'js/script.js', array(), true );
}
add_action( 'wp_enqueue_scripts', 'sn_front_end_scripts' );



/*--------------------------------------------------------------
    Add note meta box to post/page/cpt
--------------------------------------------------------------*/
function sn_note_init() {
    $post_types = array ( 'post', 'page'); // Create an array to display metabox in both posts and pages
    foreach( $post_types as $post_type ) {
        add_meta_box(
            "note_textarea",  // id 
            "Page Notes",     // title
            "sn_page_notes", // call back
            $post_type, 
            "side",           // context 
            "high"            // priority
        );
    }    
}
add_action("admin_init", "sn_note_init");



function sn_page_notes($post) {
    $custom = get_post_custom($post->ID);
    $page_options = $custom["note"][0];
    $note_value = get_post_meta($post->ID, 'note', true); 
    ?><textarea name="note" style="width: 100%; <?php if($note_value){echo 'background-color: #FFEE00;';} ?>"><?php echo $note_value; ?></textarea><?php
}



function sn_save_note($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return; // Check if this is an autosave or a revision
    if (!isset($_POST["note"]) || !is_numeric($post_id)) return; // Verify if this is a valid post ID
    $note = sanitize_text_field($_POST["note"]); // Sanitize and validate the "note" field
    update_post_meta($post_id, "note", $note); // Update the post meta field
}
add_action('save_post', 'sn_save_note');



/*--------------------------------------------------------------
    Add note meta box to admin bar on front end
--------------------------------------------------------------*/
function sn_form_in_admin_bar() {
    $admin_bar_notes = get_option('admin_bar_notes');
    if (!is_admin() && (is_page() || is_single()) && $admin_bar_notes != "off" ) {
        global $wp_admin_bar, $post;
        wp_enqueue_style('sn_styles');
        wp_enqueue_script('sn_scripts');
        $note_value     = get_post_meta($post->ID, 'note', true);
        $lock_notes_on  = get_post_meta($post->ID, 'lock_notes_on', true);
        $notes_style    = get_post_meta($post->ID, 'notes-position', true) ? get_post_meta($post->ID, 'notes-position', true) : 'display: none; left: -105px;';
        $textarea_style = get_post_meta($post->ID, 'textarea-size', true);
        if($note_value) {
            $note_alert = ' <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512"><path fill="currentColor" d="M224 0c-18 0-32 14-32 32v19C119 66 64 131 64 208v19c0 47-17 92-48 127l-8 9a32 32 0 0 0 24 53h384a32 32 0 0 0 24-53l-8-9c-31-35-48-80-48-127v-19c0-77-55-142-128-157V32c0-18-14-32-32-32zm45 493c12-12 19-28 19-45H160a64 64 0 0 0 109 45z"/></svg>';
            $have_note  = ' class="have-note"';
        } else {
            $note_alert = '';
            $have_note  = ' class="no-note"';
        }
        $lock_notes_on ? $style = 'display:flex; ' : $style = '';
        $wp_admin_bar->add_menu( array(
            'id'     => 'notes',
            'parent' => 'top-secondary',
            'title'  => '<button id="toggle-note"'.$have_note.'>Notes'.$note_alert.'</button>
                        <form method="post" action="" class="note-box" style="'.$style.$notes_style.' '.$textarea_style.'">
                            <textarea name="note2" id="note2">'.$note_value.'</textarea>
                            <div class="sn-options">
                                <input type="button" name="submit2" id="submit2" value="Save Note" /> 
                                <label><input type="checkbox" id="lock_notes_on" name="lock_notes_on"'.checked( $lock_notes_on, 'lock', false ).' value="lock"> Lock Open</label>
                                <input type="hidden" id="sn_ajax_loc" value="'.plugin_dir_url( __FILE__ ).'" />
                                <input type="hidden" id="sn_post_id" value="'.$post->ID.'" />
                            </div>
                            <div class="sn_drag" aria-label="Drag Handle">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 25 25"><path fill="currentColor" fill-rule="evenodd" d="M9.5 8a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Zm0 6a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Zm1.5 4.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0ZM15.5 8a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Zm1.5 4.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0ZM15.5 20a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Z" clip-rule="evenodd"/></svg>
                                <span id="sn_status" style="display:none;"></span>
                            </div>
                        </form>'
        ));
    }
}
add_action( 'admin_bar_menu', 'sn_form_in_admin_bar' );



/*--------------------------------------------------------------
    WP NOTES DASHBOARD WIDGET
--------------------------------------------------------------*/
function sn_display_notes_dashboard() {
    wp_add_dashboard_widget(
        "sn_notes",             // Widget slug.
        "Dashboard Notes",      // Title.
        "sn_display_notes",      // Display function.
        "sn_display_notes_form" // Add "configure" option to widget
    );
}
add_action("wp_dashboard_setup", "sn_display_notes_dashboard");



function sn_display_notes() { 
    $sn_timezone = get_option('timezone_string');
    ?>

    <style>
    .note_msg {
        display: inline-block;
        padding: 0 1rem;
    }
    #dash_notes .sn-date-box {
        width: 210px;
        min-height: unset;
        background: none;
        border: none;
    }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('add-note').addEventListener('click', function(e) {
                e.preventDefault();
                var notesTable = document.getElementById('notes_table');
                var currentDate = new Date().toLocaleString('en-US', { timeZone: "<?php echo $sn_timezone; ?>" });        
                var newRowHtml = `<tr><td>
                                    <input type="text" name="sn_notes_date[]" value="${currentDate}" readonly class="sn-date-box">
                                    <input type="hidden" name="sn_notes_id[]" value=""><br />
                                    <textarea name="sn_notes_value[]" style="width: 100%;" rows="4"></textarea>
                                  </td></tr>`;
                notesTable.insertAdjacentHTML('beforeend', newRowHtml);
            });

            const timesIcons = document.querySelectorAll('.sn-remove-note');
            timesIcons.forEach(function(icon) {
                icon.addEventListener('click', function() {
                    const closestTr = icon.closest('tr');
                    if (closestTr) {closestTr.remove();}
                });
            });
        });
    </script>

<?php if(get_option('display_dash_notes')) { echo '<div style="border: 1px solid #ccc; padding: 5px;">'.get_option('display_dash_notes').'</div>'; }  // Pull notes from the widget form ?>

    <form action="" name="dash_notes" id="dash_notes" method="post">
        <table class="sn-dashboard-notes-table" id="notes_table" style="width: 100%;">
            <?php global $wpdb;
            $table_name = $wpdb->prefix . 'notes';
            $get_notes  = $wpdb->get_results("SELECT * FROM $table_name");
            foreach ($get_notes as $note) {
                $note_id    = $note->id;
                $note_value = $note->note;
                $note_date  = $note->note_date;
                echo '<tr id="sn-'.$note_id.'">
                        <td>
                            <a href="?sn_delete='.$note_id.'"><svg xmlns="http://www.w3.org/2000/svg" class="sn-remove-note" height="1em" viewBox="0 0 384 512"><path d="M342.6 150.6a32 32 0 0 0-45.3-45.3L192 210.7 86.6 105.4a32 32 0 0 0-45.3 45.3L146.7 256 41.4 361.4a32 32 0 0 0 45.3 45.3L192 301.3l105.4 105.3a32 32 0 0 0 45.3-45.3L237.3 256l105.3-105.4z"/></svg></a>
                            <input type="text" name="sn_notes_date[]" value="'.$note_date.'" readonly class="sn-date-box">
                            <input type="hidden" name="sn_notes_id[]" value="'.$note_id.'">
                            <textarea style="width: 100%;" name="sn_notes_value[]">'.  $note_value .'</textarea>
                        </td>
                     </tr>';
            } 
            // https://developer.wordpress.org/reference/functions/wp_editor/
            // wp_editor( $note_value, 'sn_notes_value[]', array('tinymce' => true) ) // change line 202 with this?
            ?>
        </table><br />
        <button class="button-secondary" id="add-note" name="add-note">Add Note</button>
        <input type="submit" class="button-primary" name="save_note" value="Save Note" />
        <?php 
        if(isset($_POST['save_note']) && isset($_POST['sn_notes_id'])) {
            echo '<div class="note_msg">Saving...</div>';
            for ($i=0; $i < count($_POST['sn_notes_value']); $i++) { 
                $wpdb->replace($table_name,
                    array(
                        'id' => $_POST['sn_notes_id'][$i],
                        'note' => $_POST['sn_notes_value'][$i],
                        'note_date' => $_POST['sn_notes_date'][$i],
                    ),
                    array('%d','%s','%s',)
                );
            }
            echo '<script>window.location.href = "'.home_url().'/wp-admin";</script>';
        } // END OF IF submit clicked

        if(isset($_GET['sn_delete'])) {
            echo '<div class="note_msg">Deleting...</div>';
            $wpdb->delete( $table_name, array( 'id' => $_GET['sn_delete'] ) );
            echo '<script>window.location.href = "'.home_url().'/wp-admin";</script>';
        } ?>
    </form>
    <?php 
} // End of dashboard notes widget



// This callback is fired during display of form inside widget and also during form submission.
function sn_display_notes_form() {
    //if form is submitted 
    if(isset($_POST["dash_notes"])) {
        update_option("display_dash_notes", $_POST["dash_notes"]);
    }
    //form tag and submit button is automatically displayed by the dashboard API 
    $note = get_option('display_dash_notes'); 
    wp_editor( $note, 'dash_notes' ); 
}



/**
 * Register Settings
 */
function sn_register_notes_settings() {
    register_setting('sn_dashboard_options','admin_bar_notes');
}
add_action('admin_init', 'sn_register_notes_settings');



/*--------------------------------------------------------------
    WP NOTES DASHBOARD WIDGET SAVED PAGE/POST NOTES
--------------------------------------------------------------*/
function sn_display_page_notes_dashboard() {
    wp_add_dashboard_widget(
        "sn_notes2",                // Widget slug.
        "Saved Page/Post Notes",    // Title.
        "sn_display_notes2"         // Display function.
    );
}
add_action("wp_dashboard_setup", "sn_display_page_notes_dashboard");

function sn_display_notes2() {   
    $get_page_notes = new WP_Query(array(
        'post_type'     => array( 'post', 'page' ),
        'post_status'   => 'publish',
        'meta_key'      => 'note', 
        'meta_value'    => ' ',
        'meta_compare'  => '!=',
        //'orderby'     => '',
        'order'         => 'ASC',  //DESC
    ));

    // The Loop
    echo '<ul>';
    while ( $get_page_notes->have_posts() ) : $get_page_notes->the_post();
        echo '<li>'; ?>
        <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" target="_blank"><?php the_title(); ?></a><br />
        <?php
        echo '"'.get_post_meta(get_the_ID(), 'note', true).'"'; 
        echo '</li>';
    endwhile;
    echo '</ul>';
    settings_fields('mpn_settings_page_tab1');
    ?>
    <hr />
    <table>
        <tr>
            <td><label for="admin_bar_notes">Hide Notes from Admin Bar:</label></td>
            <td class="grey-box">
                <?php $admin_bar_notes = get_option('admin_bar_notes');
                $admin_bar_notes_checked = ($admin_bar_notes == "off") ? 'checked="checked"' : ''; ?>
                <input type="checkbox" id="admin_bar_notes" name="admin_bar_notes" value="off" <?php echo $admin_bar_notes_checked; ?>" />
                <i class="refresh-busy fa fa-spinner fa-pulse" style="display:none;"></i>
                <span id="sn_status" style="display:none;"></span>
            </td>
        </tr>
    </table>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('admin_bar_notes').addEventListener('change', function () {
            var ID = this.checked ? 'off' : 'on';
            sn_saveOptions(ID);
        });
    });

    // Use Ajax to save the toggle notes in admin bar checkbox
    async function sn_saveOptions(value) {
        try {
            const response = await fetch("<?php echo plugin_dir_url( __FILE__ ); ?>ajax-calls.php", {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `value=${value}`
            });
            if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
        } catch (error) {console.error('Error:', error)}
    }
    </script>
    <?php
}