<?php
/*
Plugin Name: Custom Post Type UI
Plugin URI: http://webdevstudios.com/support/wordpress-plugins/
Description: Admin panel for creating custom post types and custom taxonomies in WordPress
Author: WebDevStudios
Version: 0.2
Author URI: http://webdevstudios.com/
*/

// Define current version constant
define( 'CPT_VERSION', '0.2' );
// Define plugin URL constant
define( 'CPT_URL', get_option('siteurl') . '/wp-admin/options-general.php?page=custom-post-type-ui/custom-post-type-ui.php' );

// create custom plugin settings menu
add_action('admin_menu', 'cpt_plugin_menu');

//call delete post function
add_action( 'admin_init', 'cpt_delete_post_type' );

//call register settings function
add_action( 'admin_init', 'cpt_register_settings' );

//process custom taxonomies if they exist
add_action( 'init', 'create_custom_taxonomies', 0 );

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



function create_custom_taxonomies() {
	//register custom taxonomies
	$cpt_tax_types = get_option('cpt_custom_tax_types');
	
	//check if option value is an Array before proceeding
	If (is_array($cpt_tax_types)) {
		foreach ($cpt_tax_types as $cpt_tax_type) {
	
			If (!$cpt_tax_type[1]) {
				$cpt_label = stripslashes($cpt_tax_type[0]);
			}Else{
				$cpt_label = stripslashes($cpt_tax_type[1]);
			}
			
			//register our custom taxonomies
			register_taxonomy( $cpt_tax_type[0], 
				$cpt_tax_type[2], 
				array( 'hierarchical' => $cpt_tax_type[3], 
				'label' => $cpt_label, 
				'show_ui' => $cpt_tax_type[4], 
				'query_var' => $cpt_tax_type[5], 
				'rewrite' => $cpt_tax_type[6] 
			) );

		}	
	}
}

//delete custom post type or custom taxonomy
function cpt_delete_post_type() {
	//check if we are deleting a custom post type
	If(isset($_GET['deltype'])) {
		check_admin_referer('cpt_delete_post_type');
		$delType = intval($_GET['deltype']);
		$cpt_post_types = get_option('cpt_custom_post_types');

		unset($cpt_post_types[$delType]);

		$cpt_post_types = array_values($cpt_post_types);

		update_option('cpt_custom_post_types', $cpt_post_types);

		wp_redirect(CPT_URL);
	}
	
	//check if we are deleting a custom taxonomy
	If(isset($_GET['deltax'])) {
		check_admin_referer('cpt_delete_tax');
		$delType = intval($_GET['deltax']);
		$cpt_taxonomies = get_option('cpt_custom_tax_types');

		unset($cpt_taxonomies[$delType]);

		$cpt_taxonomies = array_values($cpt_taxonomies);

		update_option('cpt_custom_tax_types', $cpt_taxonomies);

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
	
	If (isset($_POST['cpt_edit_tax'])) {
		//edit a custom taxonomy
		check_admin_referer('cpt_add_custom_taxonomy');

		//custom taxonomy to edit
		$cpt_edit = intval($_POST['cpt_edit_tax']);

		//edit the custom taxonomy
		$cpt_form_fields = $_POST['cpt_custom_tax'];

		//load custom posts saved in WP
		$cpt_options = get_option('cpt_custom_tax_types');

		If (is_array($cpt_options)) {

			unset($cpt_options[$cpt_edit]);

			//insert new custom post type into the array
			array_push($cpt_options, $cpt_form_fields);

			$cpt_options = array_values($cpt_options);

			//save custom post types
			update_option('cpt_custom_tax_types', $cpt_options);

			wp_redirect(CPT_URL);

		}

	}ElseIf(isset($_POST['cpt_add_tax'])) {
		//create new custom taxonomy
		check_admin_referer('cpt_add_custom_taxonomy');
		
		//retrieve new custom taxonomy values
		$cpt_form_fields = $_POST['cpt_custom_tax'];
		
		//verify required fields are filled out
		If (empty($cpt_form_fields[0])) {
			wp_redirect(CPT_URL .'&cpt_error=2');
			exit();
		}Elseif(empty($cpt_form_fields[2])) {
			wp_redirect(CPT_URL .'&cpt_error=3');
			exit();
		}
		
		//load custom taxonomies saved in WP
		$cpt_options = get_option('cpt_custom_tax_types');

		//check if option exists, if not create an array for it
		If (!is_array($cpt_options)) {
			$cpt_options = array();
		}

		//insert new custom taxonomy into the array
		array_push($cpt_options, $cpt_form_fields);

		//save new custom taxonomy array in the CPT option
		update_option('cpt_custom_tax_types', $cpt_options);

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
            	<td><strong><?php _e('Action', 'cpt-plugin');?></strong></td>
            	<td><strong><?php _e('Name', 'cpt-plugin');?></strong></td>
                <td><strong><?php _e('Label', 'cpt-plugin');?></strong></td>
                <td><strong><?php _e('Public', 'cpt-plugin');?></strong></td>
                <td><strong><?php _e('Show UI', 'cpt-plugin');?></strong></td>
                <td><strong><?php _e('Edit Link', 'cpt-plugin');?></strong></td>
                <td><strong><?php _e('Capability Type', 'cpt-plugin');?></strong></td>
                <td><strong><?php _e('Hierarchical', 'cpt-plugin');?></strong></td>
                <td><strong><?php _e('Rewrite', 'cpt-plugin');?></strong></td>
                <td><strong><?php _e('Query Var', 'cpt-plugin');?></strong></td>
                <td><strong><?php _e('Supports', 'cpt-plugin');?></strong></td>
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
<h2><?php _e('Manage Custom Taxonomies', 'cpt-plugin') ?></h2>
<?php
	$cpt_tax_types = get_option('cpt_custom_tax_types');

	If (is_array($cpt_tax_types)) {
		?>
        <table width="100%">
        	<tr>
            	<td><strong><?php _e('Action', 'cpt-plugin');?></strong></td>
            	<td><strong><?php _e('Name', 'cpt-plugin');?></strong></td>
                <td><strong><?php _e('Label', 'cpt-plugin');?></strong></td>
                <td><strong><?php _e('Object Type', 'cpt-plugin');?></strong></td>
                <td><strong><?php _e('Hierarchical', 'cpt-plugin');?></strong></td>
                <td><strong><?php _e('Show UI', 'cpt-plugin');?></strong></td>
                <td><strong><?php _e('Query Var', 'cpt-plugin');?></strong></td>
                <td><strong><?php _e('Rewrite', 'cpt-plugin');?></strong></td>
            </tr>
        <?php
		$thecounter=0;
		foreach ($cpt_tax_types as $cpt_tax_type) {
			$del_url = CPT_URL .'&deltax=' .$thecounter;
			$del_url = ( function_exists('wp_nonce_url') ) ? wp_nonce_url($del_url, 'cpt_delete_tax') : $del_url;

			$edit_url = CPT_URL .'&edittax=' .$thecounter;
			$edit_url = ( function_exists('wp_nonce_url') ) ? wp_nonce_url($edit_url, 'cpt_edit_tax') : $edit_url;
		?>
        	<tr>
            	<td valign="top"><a href="<?php echo $del_url; ?>">Delete</a> / <a href="<?php echo $edit_url; ?>">Edit</a></td>
            	<td valign="top"><?php echo stripslashes($cpt_tax_type[0]); ?></td>
                <td valign="top"><?php echo stripslashes($cpt_tax_type[1]); ?></td>
                <td valign="top"><?php echo stripslashes($cpt_tax_type[2]); ?></td>
                <td valign="top"><?php echo disp_boolean($cpt_tax_type[3]); ?></td>
                <td valign="top"><?php echo disp_boolean($cpt_tax_type[4]); ?></td>
                <td valign="top"><?php echo disp_boolean($cpt_tax_type[5]); ?></td>
                <td valign="top"><?php echo disp_boolean($cpt_tax_type[6]); ?></td>
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

If (isset($_GET['edittax']) && !isset($_GET['cpt_edit'])) {
	check_admin_referer('cpt_edit_tax');

	//get post type to edit
	$editTax = intval($_GET['edittax']);

	//load custom posts saved in WP
	$cpt_options = get_option('cpt_custom_tax_types');

	//load custom post type values to edit
	$cpt_tax_name = $cpt_options[$editTax][0];
	$cpt_tax_label = $cpt_options[$editTax][1];
	$cpt_tax_object_type = $cpt_options[$editTax][2];
	$cpt_tax_hierarchical = $cpt_options[$editTax][3];
	$cpt_tax_showui = $cpt_options[$editTax][4];
	$cpt_tax_query_var = $cpt_options[$editTax][5];
	$cpt_tax_rewrite = $cpt_options[$editTax][6];

	$cpt_tax_submit_name = 'Edit Custom Taxonomy';
}Else{
	$cpt_tax_submit_name = 'Create Custom Taxonomy';
}

//check for error messages
If (isset($_GET['cpt_error']) && $_GET['cpt_error']==1) { ?>
    <div class="error">
		<?php _e('Post type name is a required field.', 'cpt-plugin'); ?>
    </div>
<?php }ElseIf (isset($_GET['cpt_error']) && $_GET['cpt_error']==2) { ?>
    <div class="error">
		<?php _e('Taxonomy name is a required field.', 'cpt-plugin'); ?>
    </div>
<?php }ElseIf (isset($_GET['cpt_error']) && $_GET['cpt_error']==3) { ?>
    <div class="error">
		<?php _e('Object type is a required field.', 'cpt-plugin'); ?>
    </div>
<?php } ?>
<table border="0" cellspacing="10">
	<tr>
    	<td width="50%" valign="top">
			<?php If (isset($_GET['edittype'])) { ?>
                <h2><?php _e('Edit Custom Post Type', 'cpt-plugin') ?> &middot; <a href="<?php echo CPT_URL; ?>"><?php _e('Reset', 'cpt-plugin');?></a></h2>
            <?php }Else{ ?>
                <h2><?php _e('Create New Custom Post Type', 'cpt-plugin') ?> &middot; <a href="<?php echo CPT_URL; ?>"><?php _e('Reset', 'cpt-plugin');?></a></h2>
            <?php } ?>
            <p><?php _e('If you are unfamiliar with the options below only fill out the <strong>Post Type Name</strong> and <strong>Label</strong> fields and check which meta boxes to support.  The other settings are set to the most common defaults for custom post types.', 'cpt-plugin'); ?></p>
            <form method="post" action="<?php echo CPT_URL; ?>">
                <?php if ( function_exists('wp_nonce_field') )
                    wp_nonce_field('cpt_add_custom_post_type'); ?>
                <?php If (isset($_GET['edittype'])) { ?>
                <input type="hidden" name="cpt_edit" value="<?php echo $editType; ?>" />
                <?php } ?>
                <table class="form-table">
                    <tr valign="top">
                    <th scope="row"><?php _e('Post Type Name', 'cpt-plugin') ?> <span style="color:red;">*</span></th>
                    <td><input type="text" name="cpt_custom_post_type[]" tabindex="1" value="<?php If (isset($cpt_post_type_name)) { echo esc_attr($cpt_post_type_name); } ?>" /> <a href="#" title="The post type name.  Used to retrieve custom post type content.  Should be short and sweet" style="cursor: help;">?</a> (e.g. movies)</td>
                    </tr>
            
                    <tr valign="top">
                    <th scope="row"><?php _e('Label', 'cpt-plugin') ?></th>
                    <td><input type="text" name="cpt_custom_post_type[]" tabindex="2" value="<?php If (isset($cpt_label)) { echo esc_attr($cpt_label); } ?>" /> <a href="#" title="Post type label.  Used in the admin menu for displaying post types." style="cursor: help;">?</a> (e.g. Movies)</td>
                    </tr>
            
                    <tr valign="top">
                    <th scope="row"><?php _e('Public', 'cpt-plugin') ?></th>
                    <td>
                        <SELECT name="cpt_custom_post_type[]" tabindex="3">
                            <OPTION value="0" <?php If (isset($cpt_public)) { If ($cpt_public == 0 && $cpt_public != '') { echo 'selected="selected"'; } } ?>>False</OPTION>
                            <OPTION value="1" <?php If (isset($cpt_public)) { If ($cpt_public == 1 || is_null($cpt_public)) { echo 'selected="selected"'; } }Else{ echo 'selected="selected"'; } ?>>True</OPTION>
                        </SELECT> <a href="#" title="Whether posts of this type should be shown in the admin UI" style="cursor: help;">?</a> (default: True)
                    </td>
                    </tr>
            
                    <tr valign="top">
                    <th scope="row"><?php _e('Show UI', 'cpt-plugin') ?></th>
                    <td>
                        <SELECT name="cpt_custom_post_type[]" tabindex="4">
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
                    <td><input type="text" name="cpt_custom_post_type[]" tabindex="5" value="<?php If (isset($cpt_edit_link)) { echo esc_attr($cpt_edit_link); }Else{ echo 'post.php?post=%d'; } ?>" /> <a href="#" title="" style="cursor: help;">?</a></td>
                    </tr>
            
                    <tr valign="top">
                    <th scope="row"><?php _e('Capability Type', 'cpt-plugin') ?></th>
                    <td><input type="text" name="cpt_custom_post_type[]" tabindex="6" value="post" value="<?php echo esc_attr($cpt_capability); ?>" /> <a href="#" title="The post type to use for checking read, edit, and delete capabilities" style="cursor: help;">?</a></td>
                    </tr>
            
                    <tr valign="top">
                    <th scope="row"><?php _e('Hierarchical', 'cpt-plugin') ?></th>
                    <td>
                        <SELECT name="cpt_custom_post_type[]" tabindex="7">
                            <OPTION value="0" <?php If (isset($cpt_hierarchical)) { If ($cpt_hierarchical == 0) { echo 'selected="selected"'; } }Else{ echo 'selected="selected"'; } ?>>False</OPTION>
                            <OPTION value="1" <?php If (isset($cpt_hierarchical)) { If ($cpt_hierarchical == 1) { echo 'selected="selected"'; } } ?>>True</OPTION>
                        </SELECT> <a href="#" title="Whether the post type is hierarchical" style="cursor: help;">?</a> (default: False)
                    </td>
                    </tr>
            
                    <tr valign="top">
                    <th scope="row"><?php _e('Rewrite', 'cpt-plugin') ?></th>
                    <td>
                        <SELECT name="cpt_custom_post_type[]" tabindex="8">
                            <OPTION value="0" <?php If (isset($cpt_rewrite)) { If ($cpt_rewrite == 0 && $cpt_rewrite != '') { echo 'selected="selected"'; } } ?>>False</OPTION>
                            <OPTION value="1" <?php If (isset($cpt_rewrite)) { If ($cpt_rewrite == 1 || is_null($cpt_rewrite)) { echo 'selected="selected"'; } }Else{ echo 'selected="selected"'; } ?>>True</OPTION>
                        </SELECT> <a href="#" title="" style="cursor: help;">?</a> (default: True)
                    </td>
                    </tr>
            
                    <tr valign="top">
                    <th scope="row"><?php _e('Query Var', 'cpt-plugin') ?></th>
                    <td>
                        <SELECT name="cpt_custom_post_type[]" tabindex="9">
                            <OPTION value="0" <?php If (isset($cpt_query_var)) { If ($cpt_query_var == 0 && $cpt_query_var != '') { echo 'selected="selected"'; } } ?>>False</OPTION>
                            <OPTION value="1" <?php If (isset($cpt_query_var)) { If ($cpt_query_var == 1 || is_null($cpt_query_var)) { echo 'selected="selected"'; } }Else{ echo 'selected="selected"'; } ?>>True</OPTION>
                        </SELECT> <a href="#" title="" style="cursor: help;">?</a> (default: True)
                    </td>
                    </tr>
            
                    <tr valign="top">
                    <th scope="row"><?php _e('Supports', 'cpt-plugin') ?></th>
                    <td>
                        <input type="checkbox" name="cpt_supports[]" tabindex="10" value="excerpts" <?php If (is_array($cpt_supports)) { If (in_array('excerpts', $cpt_supports)) echo 'checked="checked"'; } ?> />&nbsp;Excerpts <a href="#" title="Adds the excerpt meta box when creating content for this custom post type" style="cursor: help;">?</a> <br/ >
                        <input type="checkbox" name="cpt_supports[]" tabindex="11" value="trackbacks" <?php If (is_array($cpt_supports)) { If (in_array('trackbacks', $cpt_supports)) echo 'checked="checked"'; } ?> />&nbsp;Trackbacks <a href="#" title="Adds the trackbacks meta box when creating content for this custom post type" style="cursor: help;">?</a> <br/ >
                        <input type="checkbox" name="cpt_supports[]" tabindex="12" value="custom-fields" <?php If (is_array($cpt_supports)) { If (in_array('custom-fields', $cpt_supports)) echo 'checked="checked"'; }  ?> />&nbsp;Custom Fields <a href="#" title="Adds the custom fields meta box when creating content for this custom post type" style="cursor: help;">?</a> <br/ >
                        <input type="checkbox" name="cpt_supports[]" tabindex="13" value="comments" <?php If (is_array($cpt_supports)) { If (in_array('comments', $cpt_supports)) echo 'checked="checked"'; }  ?> />&nbsp;Comments <a href="#" title="Adds the comments meta box when creating content for this custom post type" style="cursor: help;">?</a> <br/ >
                        <input type="checkbox" name="cpt_supports[]" tabindex="14" value="revisions" <?php If (is_array($cpt_supports)) { If (in_array('revisions', $cpt_supports)) echo 'checked="checked"'; }  ?> />&nbsp;Revisions <a href="#" title="Adds the revisions meta box when creating content for this custom post type" style="cursor: help;">?</a> <br/ >
                        <input type="checkbox" name="cpt_supports[]" tabindex="15" value="post-thumbnails" <?php If (is_array($cpt_supports)) { If (in_array('post-thumbnails', $cpt_supports)) echo 'checked="checked"'; }  ?> />&nbsp;Post Thumbnails <a href="#" title="Adds the post thumbnails meta box when creating content for this custom post type" style="cursor: help;">?</a> <br/ >
                    </td>
                    </tr>
                </table>
            
                <p class="submit">
                <input type="submit" class="button-primary" tabindex="16" name="cpt_submit" value="<?php _e($cpt_submit_name, 'cpt-plugin') ?>" />
                </p>
            
            </form>
		</td>
        <td width="50%" valign="top">
        	<?php
			//debug area
			$cpt_options = get_option('cpt_custom_tax_types');
			?>
			<?php If (isset($_GET['edittax'])) { ?>
                <h2><?php _e('Edit Custom Taxonomy', 'cpt-plugin') ?> &middot; <a href="<?php echo CPT_URL; ?>"><?php _e('Reset', 'cpt-plugin');?></a></h2>
            <?php }Else{ ?>
                <h2><?php _e('Create Custom Taxonomy', 'cpt-plugin') ?> &middot; <a href="<?php echo CPT_URL; ?>"><?php _e('Reset', 'cpt-plugin');?></a></h2>
            <?php } ?>
        	<p><?php _e('If you are unfamiliar with the options below only fill out the <strong>Taxonomy Name</strong> and <strong>Object Type</strong> fields.  The other settings are set to the most common defaults for custom taxonomies.', 'cpt-plugin');?></p>
            <form method="post" action="<?php echo CPT_URL; ?>">
                <?php if ( function_exists('wp_nonce_field') )
                    wp_nonce_field('cpt_add_custom_taxonomy'); ?>
                <?php If (isset($_GET['edittax'])) { ?>
                <input type="hidden" name="cpt_edit_tax" value="<?php echo $editTax; ?>" />
                <?php } ?>
                <table class="form-table">
                    <tr valign="top">
                    <th scope="row"><?php _e('Taxonomy Name', 'cpt-plugin') ?> <span style="color:red;">*</span></th>
                    <td><input type="text" name="cpt_custom_tax[]" tabindex="21" value="<?php If (isset($cpt_tax_name)) { echo esc_attr($cpt_tax_name); } ?>" /> <a href="#" title="The taxonomy name.  Used to retrieve custom taxonomy content.  Should be short and sweet" style="cursor: help;">?</a> (e.g. actor)</td>
                    </tr>

                   <tr valign="top">
                    <th scope="row"><?php _e('Label', 'cpt-plugin') ?></th>
                    <td><input type="text" name="cpt_custom_tax[]" tabindex="22" value="<?php If (isset($cpt_tax_label)) { echo esc_attr($cpt_tax_label); } ?>" /> <a href="#" title="Taxonomy label.  Used in the admin menu for displaying custom taxonomy." style="cursor: help;">?</a> (e.g. Actor)</td>
                    </tr>
                    
                   <tr valign="top">
                    <th scope="row"><?php _e('Object Type', 'cpt-plugin') ?> <span style="color:red;">*</span></th>
                    <td><input type="text" name="cpt_custom_tax[]" tabindex="23" value="<?php If (isset($cpt_tax_object_type)) { echo esc_attr($cpt_tax_object_type); } ?>" /> <a href="#" title="What object to attach the custom taxonomy to.  Can be post, page, or link by default.  Can also be any custom post type name." style="cursor: help;">?</a> (e.g. movies)</td>
                    </tr>

                    <tr valign="top">
                    <th scope="row"><?php _e('Hierarchical', 'cpt-plugin') ?></th>
                    <td>
                        <SELECT name="cpt_custom_tax[]" tabindex="24">
                            <OPTION value="0" <?php If (isset($cpt_tax_hierarchical)) { If ($cpt_tax_hierarchical == 0) { echo 'selected="selected"'; } }Else{ echo 'selected="selected"'; } ?>>False</OPTION>
                            <OPTION value="1" <?php If (isset($cpt_tax_hierarchical)) { If ($cpt_tax_hierarchical == 1) { echo 'selected="selected"'; } } ?>>True</OPTION>
                        </SELECT> <a href="#" title="Whether the taxonomy is hierarchical" style="cursor: help;">?</a> (default: False)
                    </td>
                    </tr>

                    <tr valign="top">
                    <th scope="row"><?php _e('Show UI', 'cpt-plugin') ?></th>
                    <td>
                        <SELECT name="cpt_custom_tax[]" tabindex="25">
                            <OPTION value="0" <?php If (isset($cpt_tax_showui)) { If ($cpt_tax_showui == 0 && $cpt_tax_showui != '') { echo 'selected="selected"'; } } ?>>False</OPTION>
                            <OPTION value="1" <?php If (isset($cpt_tax_showui)) { If ($cpt_tax_showui == 1 || is_null($cpt_tax_showui)) { echo 'selected="selected"'; } }Else{ echo 'selected="selected"'; } ?>>True</OPTION>
                        </SELECT> <a href="#" title="Whether to generate a default UI for managing this custom taxonomy" style="cursor: help;">?</a> (default: True)
                    </td>
                    </tr>
                    
                    <tr valign="top">
                    <th scope="row"><?php _e('Query Var', 'cpt-plugin') ?></th>
                    <td>
                        <SELECT name="cpt_custom_tax[]" tabindex="26">
                            <OPTION value="0" <?php If (isset($cpt_tax_query_var)) { If ($cpt_tax_query_var == 0 && $cpt_tax_query_var != '') { echo 'selected="selected"'; } } ?>>False</OPTION>
                            <OPTION value="1" <?php If (isset($cpt_tax_query_var)) { If ($cpt_tax_query_var == 1 || is_null($cpt_tax_query_var)) { echo 'selected="selected"'; } }Else{ echo 'selected="selected"'; } ?>>True</OPTION>
                        </SELECT> <a href="#" title="" style="cursor: help;">?</a> (default: True)
                    </td>
                    </tr>

                    <tr valign="top">
                    <th scope="row"><?php _e('Rewrite', 'cpt-plugin') ?></th>
                    <td>
                        <SELECT name="cpt_custom_tax[]" tabindex="27">
                            <OPTION value="0" <?php If (isset($cpt_tax_rewrite)) { If ($cpt_tax_rewrite == 0 && $cpt_tax_rewrite != '') { echo 'selected="selected"'; } } ?>>False</OPTION>
                            <OPTION value="1" <?php If (isset($cpt_tax_rewrite)) { If ($cpt_tax_rewrite == 1 || is_null($cpt_tax_rewrite)) { echo 'selected="selected"'; } }Else{ echo 'selected="selected"'; } ?>>True</OPTION>
                        </SELECT> <a href="#" title="" style="cursor: help;">?</a> (default: True)
                    </td>
                    </tr>
                    
                </table>
                
                <p class="submit">
                	<input type="submit" class="button-primary" tabindex="28" name="cpt_add_tax" value="<?php _e($cpt_tax_submit_name, 'cpt-plugin') ?>" />
                </p>
            </form>
        </td>
	</tr>
</table>
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