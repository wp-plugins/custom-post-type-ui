<?php
/*
Plugin Name: Custom Post Type UI
Plugin URI: http://webdevstudios.com/support/wordpress-plugins/
Description: Admin panel for creating custom post types in WordPress
Author: WebDevStudios
Version: 0.1.2
Author URI: http://webdevstudios.com/
*/

// Define current version constant
define( 'CPT_VERSION', '0.1.2' );
// Define plugin URL constant
define( 'CPT_URL', get_option('url') . '/wp-admin/options-general.php?page=custom-post-type-ui/custom-post-type-ui.php' );

// create custom plugin settings menu
add_action('admin_menu', 'cpt_plugin_menu');

//call delete post function
add_action( 'admin_init', 'cpt_delete_post_type' );

//call register settings function
add_action( 'admin_init', 'cpt_register_settings' );

function cpt_plugin_menu() {
	//create custom post type menu link
	add_options_page('Custom Post Type UI', 'Custom Post Type UI', 'administrator', __FILE__, 'cpt_settings_page');
}

//register custom post types
$cpt_post_types = get_option('cpt_custom_post_types');

//check if option value is an Array before proceeding
If (is_array($cpt_post_types)) {
	foreach ($cpt_post_types as $cpt_post_type) {

		If (!$cpt_post_type[1]) {
			$cpt_label = stripslashes($cpt_post_type[0]);
		}Else{
			$cpt_label = stripslashes($cpt_post_type[1]);
		}

		register_post_type( $cpt_post_type[0], array(	'label' => __($cpt_label),
			'public' => $cpt_post_type[2],
			'show_ui' => $cpt_post_type[3],
			'_edit_link' => $cpt_post_type[4],
			'capability_type' => $cpt_post_type[5],
			'hierarchical' => $cpt_post_type[6],
			'rewrite' => false,
			'query_var' => $cpt_post_type[8],
			'supports' => $cpt_post_type[9]
		) );
	}
}

//delete custom post type
function cpt_delete_post_type() {
	If(isset($_GET['deltype'])) {
		check_admin_referer('cpt_delete_post_type');
		$delType = intval($_GET['deltype']);
		$cpt_post_types = get_option('cpt_custom_post_types');

		unset($cpt_post_types[$delType]);

		$cpt_post_types = array_values($cpt_post_types);

		update_option('cpt_custom_post_types', $cpt_post_types);

		wp_redirect(CPT_URL);
	}
}

function cpt_register_settings() {
	global $cpt_error;
	
	If (isset($_POST['cpt_edit'])) {
		//edit a custom post type
		check_admin_referer('cpt_add_custom_post_type');

		//custom post type to edit
		$cpt_edit = intval($_POST['cpt_edit']);

		//edit the custom post type
		$cpt_form_fields = $_POST['cpt_custom_post_type'];

		//add support checkbox values to array
		array_push($cpt_form_fields, $_POST['cpt_supports']);

		//load custom posts saved in WP
		$cpt_options = get_option('cpt_custom_post_types');

		If (is_array($cpt_options)) {

			unset($cpt_options[$cpt_edit]);

			//insert new custom post type into the array
			array_push($cpt_options, $cpt_form_fields);

			$cpt_options = array_values($cpt_options);

			//save custom post types
			update_option('cpt_custom_post_types', $cpt_options);

			wp_redirect(CPT_URL);

		}

	}ElseIf(isset($_POST['cpt_submit'])) {
		//create a new custom post type
		check_admin_referer('cpt_add_custom_post_type');

		//retrieve new custom post type values
		$cpt_form_fields = $_POST['cpt_custom_post_type'];

		If (empty($cpt_form_fields[0])) {
			wp_redirect(CPT_URL .'&cpt_error=1');
			exit();
		}

		//add support checkbox values to array
		array_push($cpt_form_fields, $_POST['cpt_supports']);

		//load custom posts saved in WP
		$cpt_options = get_option('cpt_custom_post_types');

		//check if option exists, if not create an array for it
		If (!is_array($cpt_options)) {
			$cpt_options = array();
		}

		//insert new custom post type into the array
		array_push($cpt_options, $cpt_form_fields);

		//save new custom post type array in the CPT option
		update_option('cpt_custom_post_types', $cpt_options);

		wp_redirect(CPT_URL);
	}
}

function cpt_settings_page() {
	global $cpt_error;
?>
<div class="wrap">
<h2><?php _e('Manage Custom Post Types', 'cpt-plugin') ?></h2>
<?php
	$cpt_post_types = get_option('cpt_custom_post_types');

	If (is_array($cpt_post_types)) {
		?>
        <table width="100%">
        	<tr>
            	<td><strong>Action</strong></td>
            	<td><strong>Name</strong></td>
                <td><strong>Label</strong></td>
                <td><strong>Public</strong></td>
                <td><strong>Show UI</strong></td>
                <td><strong>Edit Link</strong></td>
                <td><strong>Capability Type</strong></td>
                <td><strong>Hierarchical</strong></td>
                <td><strong>Rewrite</strong></td>
                <td><strong>Query Var</strong></td>
                <td><strong>Supports</strong></td>
            </tr>
        <?php
		$thecounter=0;
		foreach ($cpt_post_types as $cpt_post_type) {
			$del_url = CPT_URL .'&deltype=' .$thecounter;
			$del_url = ( function_exists('wp_nonce_url') ) ? wp_nonce_url($del_url, 'cpt_delete_post_type') : $del_url;

			$edit_url = CPT_URL .'&edittype=' .$thecounter;
			$edit_url = ( function_exists('wp_nonce_url') ) ? wp_nonce_url($edit_url, 'cpt_edit_post_type') : $edit_url;
		?>
        	<tr>
            	<td valign="top"><a href="<?php echo $del_url; ?>">Delete</a> / <a href="<?php echo $edit_url; ?>">Edit</a></td>
            	<td valign="top"><?php echo stripslashes($cpt_post_type[0]); ?></td>
                <td valign="top"><?php echo stripslashes($cpt_post_type[1]); ?></td>
                <td valign="top"><?php echo disp_boolean($cpt_post_type[2]); ?></td>
                <td valign="top"><?php echo disp_boolean($cpt_post_type[3]); ?></td>
                <td valign="top"><?php echo $cpt_post_type[4]; ?></td>
                <td valign="top"><?php echo $cpt_post_type[5]; ?></td>
                <td valign="top"><?php echo disp_boolean($cpt_post_type[6]); ?></td>
                <td valign="top"><?php echo disp_boolean($cpt_post_type[7]); ?></td>
                <td valign="top"><?php echo disp_boolean($cpt_post_type[8]); ?></td>
                <td>
					<?php
					If (is_array($cpt_post_type[9])) {
						foreach ($cpt_post_type[9] as $cpt_supports) {
							echo $cpt_supports .'<br />';
						}
					}
					?>
                </td>
            </tr>
            <tr>
            	<td colspan="11"><hr /></td>
            </tr>
		<?php
		$thecounter++;
		}
		?></table><?php
	}
?>

<?php
//check if we are editing a custom post type or creating a new one
If (isset($_GET['edittype']) && !isset($_GET['cpt_edit'])) {
	check_admin_referer('cpt_edit_post_type');

	//get post type to edit
	$editType = intval($_GET['edittype']);

	//load custom posts saved in WP
	$cpt_options = get_option('cpt_custom_post_types');

	//load custom post type values to edit
	$cpt_post_type_name = $cpt_options[$editType][0];
	$cpt_label = $cpt_options[$editType][1];
	$cpt_public = $cpt_options[$editType][2];
	$cpt_showui = $cpt_options[$editType][3];
	$cpt_edit_link = $cpt_options[$editType][4];
	$cpt_capability = $cpt_options[$editType][5];
	$cpt_hierarchical = $cpt_options[$editType][6];
	$cpt_rewrite = $cpt_options[$editType][7];
	$cpt_query_var = $cpt_options[$editType][8];
	$cpt_supports = $cpt_options[$editType][9];

	$cpt_submit_name = 'Edit Custom Post Type';
}Else{
	$cpt_submit_name = 'Create Custom Post Type';
}

//check for error msg
If (isset($_GET['cpt_error'])) { ?>
    <div class="error">
		<?php echo 'Post type name is a required field.'; ?>
    </div>
<?php } ?>

<?php If (isset($_GET['edittype'])) { ?>
	<h2><?php _e('Edit Custom Post Type', 'cpt-plugin') ?> &middot; <a href="<?php echo CPT_URL; ?>">Reset</a></h2>
<?php }Else{ ?>
	<h2><?php _e('Create New Custom Post Type', 'cpt-plugin') ?> &middot; <a href="<?php echo CPT_URL; ?>">Reset</a></h2>
<?php } ?>
<p>If you are unfamiliar with the options below only fill out the <strong>Post Type Name</strong> and <strong>Label</strong> fields and check which meta boxes to support.  The other settings are set to the most common defaults for custom post types.</p>
<form method="post" action="<?php echo CPT_URL; ?>">
    <?php if ( function_exists('wp_nonce_field') )
		wp_nonce_field('cpt_add_custom_post_type'); ?>
    <?php If (isset($_GET['edittype'])) { ?>
    <input type="hidden" name="cpt_edit" value="<?php echo $editType; ?>" />
    <?php } ?>
    <table class="form-table">
        <tr valign="top">
        <th scope="row"><?php _e('Post Type Name', 'cpt-plugin') ?> <span style="color:red;">*</span></th>
        <td><input type="text" name="cpt_custom_post_type[]" value="<?php If (isset($cpt_post_type_name)) { echo esc_html($cpt_post_type_name); } ?>" /> <a href="#" title="The post type name.  Used to retrieve custom post type content.  Should be short and sweet" style="cursor: help;">?</a> (e.g. bars)</td>
        </tr>

        <tr valign="top">
        <th scope="row"><?php _e('Label', 'cpt-plugin') ?></th>
        <td><input type="text" name="cpt_custom_post_type[]" value="<?php If (isset($cpt_label)) { echo esc_html($cpt_label); } ?>" /> <a href="#" title="Post type label.  Used in the admin menu for displaying post types." style="cursor: help;">?</a> (e.g. Bars)</td>
        </tr>

        <tr valign="top">
        <th scope="row"><?php _e('Public', 'cpt-plugin') ?></th>
        <td>
        	<SELECT name="cpt_custom_post_type[]">
            	<OPTION value="0" <?php If (isset($cpt_public)) { If ($cpt_public == 0 && $cpt_public != '') { echo 'selected="selected"'; } } ?>>False</OPTION>
                <OPTION value="1" <?php If (isset($cpt_public)) { If ($cpt_public == 1 || is_null($cpt_public)) { echo 'selected="selected"'; } }Else{ echo 'selected="selected"'; } ?>>True</OPTION>
            </SELECT> <a href="#" title="Whether posts of this type should be shown in the admin UI" style="cursor: help;">?</a> (default: True)
        </td>
        </tr>

        <tr valign="top">
        <th scope="row"><?php _e('Show UI', 'cpt-plugin') ?></th>
        <td>
        	<SELECT name="cpt_custom_post_type[]">
            	<OPTION value="0" <?php If (isset($cpt_showui)) { If ($cpt_showui == 0 && $cpt_showui != '') { echo 'selected="selected"'; } } ?>>False</OPTION>
                <OPTION value="1" <?php If (isset($cpt_showui)) { If ($cpt_showui == 1 || is_null($cpt_showui)) { echo 'selected="selected"'; } }Else{ echo 'selected="selected"'; } ?>>True</OPTION>
            </SELECT> <a href="#" title="Whether to generate a default UI for managing this post type" style="cursor: help;">?</a> (default: True)
        </td>
        </tr>
        <?php
		//set edit link deafult
		If (isset($cpt_edit_link) && !$cpt_edit_link) {
			$cpt_edit_link = 'post.php?post=%d';
		}
		?>
        <tr valign="top">
        <th scope="row"><?php _e('Edit Link', 'cpt-plugin') ?></th>
        <td><input type="text" name="cpt_custom_post_type[]" value="<?php If (isset($cpt_edit_link)) { echo esc_html($cpt_edit_link); }Else{ echo 'post.php?post=%d'; } ?>" /> <a href="#" title="" style="cursor: help;">?</a></td>
        </tr>

        <tr valign="top">
        <th scope="row"><?php _e('Capability Type', 'cpt-plugin') ?></th>
        <td><input type="text" name="cpt_custom_post_type[]" value="post" value="<?php echo esc_html($cpt_capability); ?>" /> <a href="#" title="The post type to use for checking read, edit, and delete capabilities" style="cursor: help;">?</a></td>
        </tr>

        <tr valign="top">
        <th scope="row"><?php _e('Hierarchical', 'cpt-plugin') ?></th>
        <td>
        	<SELECT name="cpt_custom_post_type[]">
            	<OPTION value="0" <?php If (isset($cpt_hierarchical)) { If ($cpt_hierarchical == 0) { echo 'selected="selected"'; } }Else{ echo 'selected="selected"'; } ?>>False</OPTION>
                <OPTION value="1" <?php If (isset($cpt_hierarchical)) { If ($cpt_hierarchical == 1) { echo 'selected="selected"'; } } ?>>True</OPTION>
            </SELECT> <a href="#" title="Whether the post type is hierarchical" style="cursor: help;">?</a> (default: False)
        </td>
        </tr>

        <tr valign="top">
        <th scope="row"><?php _e('Rewrite', 'cpt-plugin') ?></th>
        <td>
        	<SELECT name="cpt_custom_post_type[]">
            	<OPTION value="0" <?php If (isset($cpt_rewrite)) { If ($cpt_rewrite == 0) { echo 'selected="selected"'; } }Else{ echo 'selected="selected"'; } ?>>False</OPTION>
                <OPTION value="1" <?php If (isset($cpt_rewrite)) { If ($cpt_rewrite == 1) { echo 'selected="selected"'; } } ?>>True</OPTION>
            </SELECT> <a href="#" title="" style="cursor: help;">?</a> (default: False)
        </td>
        </tr>

        <tr valign="top">
        <th scope="row"><?php _e('Query Var', 'cpt-plugin') ?></th>
        <td>
        	<SELECT name="cpt_custom_post_type[]">
            	<OPTION value="0" <?php If (isset($cpt_query_var)) { If ($cpt_query_var == 0) { echo 'selected="selected"'; } }Else{ echo 'selected="selected"'; } ?>>False</OPTION>
                <OPTION value="1" <?php If (isset($cpt_query_var)) { If ($cpt_query_var == 1) { echo 'selected="selected"'; } } ?>>True</OPTION>
            </SELECT> <a href="#" title="" style="cursor: help;">?</a> (default: False)
        </td>
        </tr>

        <tr valign="top">
        <th scope="row"><?php _e('Supports', 'cpt-plugin') ?></th>
        <td>
        	<input type="checkbox" name="cpt_supports[]" value="excerpts" <?php If (is_array($cpt_supports)) { If (in_array('excerpts', $cpt_supports)) echo 'checked="checked"'; } ?> />&nbsp;Excerpts <a href="#" title="Adds the excerpt meta box when creating content for this custom post type" style="cursor: help;">?</a> <br/ >
            <input type="checkbox" name="cpt_supports[]" value="trackbacks" <?php If (is_array($cpt_supports)) { If (in_array('trackbacks', $cpt_supports)) echo 'checked="checked"'; } ?> />&nbsp;Trackbacks <a href="#" title="Adds the trackbacks meta box when creating content for this custom post type" style="cursor: help;">?</a> <br/ >
            <input type="checkbox" name="cpt_supports[]" value="custom-fields" <?php If (is_array($cpt_supports)) { If (in_array('custom-fields', $cpt_supports)) echo 'checked="checked"'; }  ?> />&nbsp;Custom Fields <a href="#" title="Adds the custom fields meta box when creating content for this custom post type" style="cursor: help;">?</a> <br/ >
            <input type="checkbox" name="cpt_supports[]" value="comments" <?php If (is_array($cpt_supports)) { If (in_array('comments', $cpt_supports)) echo 'checked="checked"'; }  ?> />&nbsp;Comments <a href="#" title="Adds the comments meta box when creating content for this custom post type" style="cursor: help;">?</a> <br/ >
            <input type="checkbox" name="cpt_supports[]" value="revisions" <?php If (is_array($cpt_supports)) { If (in_array('revisions', $cpt_supports)) echo 'checked="checked"'; }  ?> />&nbsp;Revisions <a href="#" title="Adds the revisions meta box when creating content for this custom post type" style="cursor: help;">?</a> <br/ >
            <input type="checkbox" name="cpt_supports[]" value="post-thumbnails" <?php If (is_array($cpt_supports)) { If (in_array('post-thumbnails', $cpt_supports)) echo 'checked="checked"'; }  ?> />&nbsp;Post Thumbnails <a href="#" title="Adds the post thumbnails meta box when creating content for this custom post type" style="cursor: help;">?</a> <br/ >
        </td>
        </tr>
    </table>

    <p class="submit">
    <input type="submit" class="button-primary" name="cpt_submit" value="<?php _e($cpt_submit_name, 'cpt-plugin') ?>" />
    </p>

</form>
</div>
			<p class="cp_about"><a target="_blank" href="http://webdevstudios.com/support/forum/custom-post-type-ui/">Custom Post Type UI</a> v<?php echo CPT_VERSION; ?> - <?php _e( 'Copyright', 'collabpress' ) ?> &copy; 2010 - <a href="http://webdevstudios.com/support/forum/custom-post-type-ui/" target="_blank">Please Report Bugs</a> &middot; Follow on Twitter: <a href="http://twitter.com/williamsba" target="_blank">Brad</a> &middot; <a href="http://twitter.com/webdevstudios" target="_blank">WDS</a></p>
<?php
}

function disp_boolean($booText) {
	If ($booText == '0') {
		return 'false';
	}Else{
		return 'true';
	}
}
?>