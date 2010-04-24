<?php

// Fire this during init
register_post_type('match', array(
	'label' => __('Matches'),
	'singular_label' => __('Match'),
	'public' => true,
	'show_ui' => true,
	'_builtin' => false,
	'hierarchical' => false,
	'supports' => array('title', 'author'),
	'rewrite' => array('slug' => 'match')
));

function wpbo_get_posts( $query ) {
	if ( is_home() || is_feed() )
		$query->set( 'post_type', array( 'post', 'match', 'attachment' ) );

	return $query;
}
add_filter( 'pre_get_posts', 'wpbo_get_posts' );

/*
function shot_link_columns($columns) {
	$columns = array(
		'cb' => '<input type="checkbox" />',
		'title' => 'Link Title',
		'description' => 'Description',
		'url' => 'URL',
		'comments' => 'Comments',
	);	
	return $columns;
}
add_filter('manage_edit-podcast_columns', 'shot_link_columns');
 
function shot_link_custom_columns($column) {
	global $post;
	if ("ID" == $column) echo $post->ID;
	elseif ("description" == $column) echo $post->post_content;
	elseif ("url" == $column) echo "63:50";
}
add_action('manage_posts_custom_column', 'shot_link_custom_columns');
*/

function wpbo_post_callback( $post_id ) {
	global $post, $shot_types;

	// Verify
	if ( !wp_verify_nonce( $_POST['wpbo-nonce'], 'wpbo-meta-box' ))
		return $post_id;

	// Pages can't have shot data
	if ( 'match' !== $_POST['post_type'] )
		return $post_id;

	// Check permissions
	if ( !current_user_can( 'edit_post', $post_id ))
		return $post_id;

	// SHOT LINK TARGET
	$people = $_POST['wpbo-people'];
	foreach($people as $person_id => $status) {

	}
	$data = htmlspecialchars($data);

	if(get_post_meta($post_id, 'wpbo-people') == "")
		add_post_meta($post_id, 'wpbo-people', $data, true);

	elseif($data != get_post_meta($post_id, 'wpbo-people', true))
		update_post_meta($post_id, 'wpbo-people', $data);
}
add_action('save_post', 'wpbo_post_callback');

function wpbo_admin() {
	add_meta_box( 'wpbo-matchinfo-box', 'Info', 'wpbo_matchinfo_box', 'match', 'normal', 'high' );
	add_meta_box( 'wpbo-people-box', 'People', 'wpbo_people_box', 'match', 'normal', 'high' );
}
add_action('admin_menu', 'wpbo_admin');

function wpbo_matchinfo_box( $object, $box ) {
	$info = get_post_meta($object->ID, 'wpbo-info', true);
?>
	<textarea name="wpbo-info" style="height:4em;margin:0;width:98%;" cols="40" rows="1"><?php echo $info ?></textarea>
	<p>Place any information relevant to the players here. For example, map choices for this match or expected times.</p>
	<label for="wpbo-type">Type:</label>
	<select name="wpbo-type">
		<option value="highlander">Highlander (9v9)</option>
		<option value="normal">Normal (6v6)</option>
	</select>
<?php
}

function wpbo_people_box( $object, $box ) {
	$players = get_post_meta($object->ID, 'wpbo-players', true);
	if($players) {
?>
	<h3>Players</h3>
<?php
	wpbo_players_table($players);
	}
?>
	<p><label for="wpbo-add-person">Name or ID:</label> <input type="text" name="wpbo-add-person" id="wpbo-add-person" value="" /> <button class="button">Add</button></p>
	<p>Note: You must have Javascript enabled</p>
	<input type="hidden" name="wpbo-nonce" value="<?php echo wp_create_nonce( 'wpbo-meta-box' ); ?>" />
</p>
<?php
}

function wpbo_players_table($players) {
?>
	<table>
		<thead>
			<tr>
				<th>Name</th>
				<th>Classes</th>
				<th>Status</th>
			</tr>
		</thead>
		<tbody>
<?php
		foreach($players as $person_id => $status) {
			$person = get_userdata($person_id);
?>
			<tr>
				<td><?php echo $person->display_name ?></td>
				<td>Class?</td>
				<td>
					<label><input type="radio" name="wpbo-people[<?php $person_id ?>]" value="confirmed" /> Confirmed</label>
					<label><input type="radio" name="wpbo-people[<?php $person_id ?>]" value="reserve" /> Reserve</label>
					<label><input type="radio" name="wpbo-people[<?php $person_id ?>]" value="waiting" /> Waiting</label>
				</td>
			</tr>
<?php
		}
?>
		</tbody>
	</table>
<?php
}
/*
function shot_link_target($default = '') {
	global $post;
	$link = get_post_meta($post->ID, 'shot_link_target', true);
	if(empty($link))
		$link = $default;
	return $link;
}

function shot_link_tag() {
	$link = shot_link_target();
	if(empty($link))
		return get_the_title();

	return '<a href="' . $link . '">' . get_the_title() . '</a>';
}

function shot_link_rss_content($content) {
	global $post;
	if(get_post_type($post) == 'link' && shot_link_target(false)) {
		$content .= '<p><a href="' . get_permalink() . '">[Permalink]</a></p>';
		return $content;
	}
	return $content;
}
add_filter('the_content_feed', 'shot_link_rss_content');
add_filter('the_excerpt_rss', 'shot_link_rss_content');

function shot_link_rss_permalink($permalink) {
	global $wp_query, $post;
	if(get_post_type($post) == 'link' && $url = shot_link_target(false)) {
		return $url;
	}
	return $permalink;
}
add_filter('the_permalink_rss', 'shot_link_rss_permalink');*/