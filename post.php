add_action( 'init', 'register_rudra_portfolio' );

function register_rudra_portfolio() {

    $labels = array(
        'name' => __( 'Portfolios', 'rudra_pfl' ),
        'singular_name' => __( 'Portfolio Item', 'rudra_pfl' ),
        'add_new' => __( 'Add New', 'rudra_pfl' ),
        'add_new_item' => __( 'Add New Portfolio Item', 'rudra_pfl' ),
        'edit_item' => __( 'Edit Portfolio Item', 'rudra_pfl' ),
        'new_item' => __( 'New Portfolio Item', 'rudra_pfl' ),
        'view_item' => __( 'View Portfolio Item', 'rudra_pfl' ),
        'search_items' => __( 'Search Portfolio Items', 'rudra_pfl' ),
        'not_found' =>  __( 'No portfolio items found', 'rudra_pfl' ),
        'not_found_in_trash' => __( 'No portfolio items found in Trash', 'rudra_pfl' ),
        'parent_item_colon' => __( 'Parent Portfolio:', 'rudra_pfl' ),
        'menu_name' => __( 'Portfolios', 'rudra_pfl' ),
      
    );

    $args = array(
        'labels' => $labels,
        'hierarchical' => true,
        'description' => __( 'Custom Post Type - Portfolio Pages', 'rudra_pfl' ),
        'supports' => array( 'title', 'editor', 'excerpt', 'thumbnail','tag'),
        'taxonomies' => array( 'rudra-portfolio-category','post_tag' ),
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_position' => 20,
        'menu_icon' => plugins_url('images/portfolio.png', __FILE__),
        'show_in_nav_menus' => true,
        'publicly_queryable' => true,
        'exclude_from_search' => false,
        'has_archive' => true,
        'query_var' => true,
        'can_export' => true,
        'rewrite' => true,
        'capability_type' => 'post'
    );

    register_post_type( 'rudra-portfolio', $args );

    // "Portfolio Categories" Custom Taxonomy
    $labels = array(
    	'name' => __( 'Portfolio Categories', 'rudra_pfl' ),
    	'singular_name' => __( 'Portfolio Category', 'rudra_pfl' ),
    	'search_items' =>  __( 'Search Portfolio Categories', 'rudra_pfl' ),
    	'all_items' => __( 'All Portfolio Categories', 'rudra_pfl' ),
    	'parent_item' => __( 'Parent Portfolio Category', 'rudra_pfl' ),
    	'parent_item_colon' => __( 'Parent Portfolio Category:', 'rudra_pfl' ),
    	'edit_item' => __( 'Edit Portfolio Category', 'rudra_pfl' ),
    	'update_item' => __( 'Update Portfolio Category', 'rudra_pfl' ),
    	'add_new_item' => __( 'Add New Portfolio Category', 'rudra_pfl' ),
    	'new_item_name' => __( 'New Portfolio Category Name', 'rudra_pfl' ),
    	'menu_name' => __( 'Portfolio Categories', 'rudra_pfl' )
    	
    );

    $args = array(
    	'hierarchical' => true,
    	'labels' => $labels,
    	'show_ui' => true,
    	'query_var' => true,
    
    	'rewrite' => array( 'slug' => 'rudra-portfolio-category' )
    );

    register_taxonomy( 'rudra-portfolio-category', array( 'rudra-portfolio' ), $args );

}
add_filter('manage_edit-rudra-portfolio_columns', 'add_new_portfolio_columns');

function add_new_portfolio_columns($gallery_columns) {
     
    $new_columns['title'] = _x('Project Name', 'column name');
    $new_columns['author'] = __('Author');
    $new_columns['portfolio_categories'] = __('Categories');
    $new_columns['tags'] = __('Tags');
 
    $new_columns['date'] = _x('Date', 'column name');
 
    return $new_columns;
}

add_action( 'manage_rudra-portfolio_posts_custom_column', 'my_manage_portfolio_columns', 10, 2 );

function my_manage_portfolio_columns( $column, $post_id ) {
	global $post;

	switch( $column ) {
		/* If displaying the 'genre' column. */
		case 'portfolio_categories' :

			/* Get the genres for the post. */
			$terms = get_the_terms( $post_id, 'rudra-portfolio-category' );
			
			/* If terms were found. */
			if ( !empty( $terms ) ) {

				$out = array();

				/* Loop through each term, linking to the 'edit posts' page for the specific term. */
				foreach ( $terms as $term ) {
					$out[] = sprintf( '<a href="%s">%s</a>',
						esc_url( add_query_arg( array( 'post_type' => $post->post_type, 'rudra-portfolio-category' => $term->slug ), 'edit.php' ) ),
						esc_html( sanitize_term_field( 'name', $term->name, $term->term_id, 'rudra-portfolio-category', 'display' ) )
					);
				}

				/* Join the terms, separating them with a comma. */
				echo join( ', ', $out);
			}

			/* If no terms were found, output a default message. */
			else {
				_e( 'No Genres' );
			}

			break;

		/* Just break out of the switch statement for everything else. */
		default :
			break;
	}
}





/*-----------------------------------------------------------------------------------*/
/* Add the Meta Box in Portfolio admin - url, quote, portfolio images for slider  */
/*-----------------------------------------------------------------------------------*/

function add_custom_meta_box_pfl() {
    add_meta_box(
		'rudra_portfolio_meta_box', // $id
         __( 'Portfolio Settings', 'rudra_pfl' ), // $title
		'show_custom_meta_box_pfl', // $callback
		'rudra-portfolio', // $page
		'normal', // $context
		'high'); // $priority
}
add_action('add_meta_boxes', 'add_custom_meta_box_pfl');

// Field Array
$prefix = 'custom_';
$custom_meta_fields = array(
	array(
		'label'	=> __( 'URL', 'rudra_pfl' ),
		'desc'	=> __( 'Enter URL of your clients site e.g. www.google.com (optional)', 'rudra_pfl' ),
		'id'	=> $prefix.'rudra-portfolio-url',
		'type'	=> 'text'
	),
	
	array(
		'label'	=> __( 'Technologies Used', 'rudra_pfl' ),
		'desc'	=> __( "Enter project's Client (optional)", 'rudra_pfl' ),
		'id'	=> $prefix.'rudra-portfolio-tech',
		'type'	=> 'text'
	),
	array(
		'label'	=> __( 'Responsive or not', 'rudra_pfl' ),
		'desc'	=> __( "check if the  project is responsive", 'rudra_pfl' ),
		'id'	=> $prefix.'rudra-portfolio-resp',
		'type'	=> 'checkbox'
	),
	array(
		'label'	=> __( 'Browser supported', 'rudra_pfl' ),
		'desc'	=> __( "Browser supported for the project", 'rudra_pfl' ),
		'id'	=> $prefix.'rudra-portfolio-browser',
		'type'	=> 'checkboxs',
		'values'=>array("Chrome","Firefox","Internet Explorer","opera","safari")
	),
	array(
		'label'	=> __( 'OS supported', 'rudra_pfl' ),
		'desc'	=> __( "Operating systems supported for the project", 'rudra_pfl' ),
		'id'	=> $prefix.'rudra-portfolio-os',
		'type'	=> 'checkboxs',
		'values'=>array("Linux","Windows","Mac")
	),

);



// The Callback Meta Boxes
function show_custom_meta_box_pfl() {
	global $custom_meta_fields, $post;
	// Use nonce for verification
	echo '<input type="hidden" name="custom_meta_box_nonce" value="'.wp_create_nonce(basename(__FILE__)).'" />';

	// Begin the field table and loop
	echo '<table class="form-table">';
	foreach ($custom_meta_fields as $field) {
		// get value of this field if it exists for this post
		$meta = get_post_meta($post->ID, $field['id'], true);
		// begin a table row with
		echo '<tr>
				<th><label for="'.$field['id'].'">'.$field['label'].'</label></th>
				<td>';
				switch($field['type']) {
					// text
					case 'text':
						echo '<input type="text" name="'.$field['id'].'" id="'.$field['id'].'" value="'.$meta.'" size="30" />
								<br /><span class="description">'.$field['desc'].'</span>';
					break;
					// textarea
					case 'textarea':
						echo '<textarea name="'.$field['id'].'" id="'.$field['id'].'" cols="60" rows="4">'.$meta.'</textarea>
								<br /><span class="description">'.$field['desc'].'</span>';
					break;
					
					case 'checkbox':
					if ($meta) { $checked="checked"; }else { $checked="" ;}
				
					echo '<input type="checkbox" name="'.$field['id'].'" id="'.$field['id'].'" value="Mozila" '.$checked.'  size="30" />
								<span class="description">'.$field['desc'].'</span>';
								break;
								
					case 'checkboxs':
					
					foreach($field['values'] as $key=> $repaetval)
					{
						if (!empty($meta[$key])) { $checked="checked"; }else { $checked="" ;}
						echo '<input type="checkbox" name="'.$field['id'].'[]" id="'.$field['id'].'" value="'.str_replace(' ','',$repaetval).'" '.$checked.'  size="30" />
								<span class="">'.$repaetval.'</span>';
					}
					echo "<br /><span class='description'>".$field['desc']."</span>";
								break;			
					// repeatable image
               case 'repeatable':
							echo '<span class="description">'.$field['desc'].'</span><ul id="'.$field['id'].'-repeatable" class="custom_repeatable">';
						$i = 0;
					if ($meta) {
					foreach($meta as $row) {
					    $image = wp_get_attachment_image_src($row, 'thumbnail');
                        $image = $image[0];
						echo '<li><span class="sort hndle">|||</span>
                        <input name="'.$field['id'].'['.$i.']" id="'.$field['id'].'" type="hidden" class="custom_upload_image" value="'.$row.'" />
						<img name="'.$field['id'].'['.$i.']" id="'.$field['id'].'" src="'.$image.'" class="custom_preview_image" alt="" style="width:30px;height:30px;" />
						<input name="'.$field['id'].'['.$i.']" class="custom_upload_image_button button" type="button" value="' . __('Choose Image', 'rudra_pfl') . '" />
						<a class="repeatable-remove button" href="#">' . __('Remove', 'rudra_pfl') . '</a></li>';
					$i++;
					}} else {
					    $row = '';
						$image = wp_get_attachment_image_src($row, 'thumbnail');
						$image = $image[0];
						echo '<li><span class="sort hndle">|||</span>
						<input name="'.$field['id'].'['.$i.']" id="'.$field['id'].'" type="hidden" class="custom_upload_image" value="'.$row.'" />
						<img name="'.$field['id'].'['.$i.']" id="'.$field['id'].'" src="'.$image.'" class="custom_preview_image" alt="" style="width:30px;height:30px;" />
						<input name="'.$field['id'].'['.$i.']" class="custom_upload_image_button button" type="button" value="' . __('Choose Image', 'rudra_pfl') . '" />
						<a class="repeatable-remove button" href="#">' . __('Remove', 'rudra_pfl') . '</a></li>';
					}
					echo '</ul><a class="repeatable-add button" href="#" style="margin-left:250px;">' . __('Add New Image', 'rudra_pfl') . '</a>';
					break;
				} //end switch
		echo '</td></tr>';
	} // end foreach
	echo '</table>'; // end table
}

// Save the Data - Metaboxes
function save_custom_meta_pfl($post_id) {
    global $custom_meta_fields;

    // verify nonce
	// if (!wp_verify_nonce($_POST['custom_meta_box_nonce'], basename(__FILE__)))

    if ( !isset($_POST['custom_meta_box_nonce']) || !wp_verify_nonce( $_POST['custom_meta_box_nonce'], basename(__FILE__) ))
		return $post_id;
	// check autosave
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
		return $post_id;
	// check permissions
	if ('page' == $_POST['post_type']) {
		if (!current_user_can('edit_page', $post_id))
			return $post_id;
		} elseif (!current_user_can('edit_post', $post_id)) {
			return $post_id;
	}

	// loop through fields and save the data
	foreach ($custom_meta_fields as $field) {
		if($field['type'] == 'tax_select') continue;
		$old = get_post_meta($post_id, $field['id'], true);
		$new = $_POST[$field['id']];
		if ($new && $new != $old) {
			update_post_meta($post_id, $field['id'], $new);
		} elseif ('' == $new && $old) {
			delete_post_meta($post_id, $field['id'], $old);
		}
	} // enf foreach


}
add_action('save_post', 'save_custom_meta_pfl');



/*-----------------------------------------------------------------------------------*/
/* Add Page title setting - meta custom box - to the administrative interface.  */
/*-----------------------------------------------------------------------------------*/

add_action( 'add_meta_boxes', 'rudra_add_custom_box_pfl' );

/* Do something with the data entered */
add_action( 'save_post', 'rudra_save_postdata_pfl' );
