<?php

/*
array(
	'id' => 'rmccue',
	'status' => 'confirmed',
	'classes' => 'Soldier, Pyro',
	'signed' => time()
)
*/
// Fire this during init
register_post_type('match', array(
	'label' => __('Matches'),
	'singular_label' => __('Match'),
	'public' => true,
	'show_ui' => true,
	'_builtin' => false,
	'hierarchical' => false,
	'supports' => array('title', 'author', 'comments'),
	'rewrite' => array('slug' => 'match')
));

function wpbo_get_posts( $query ) {
	if ( is_home() || is_feed() )
		$query->set( 'post_type', array( 'post', 'match', 'attachment' ) );

	return $query;
}
add_filter( 'pre_get_posts', 'wpbo_get_posts' );


function wpbo_match_columns($columns) {
	$columns = array(
		'cb' => '<input type="checkbox" />',
		'title' => 'Title',
		'scheduled' => 'Date Scheduled',
		'type' => 'Type',
		'players' => 'Players Signed Up',
		'comments' => '<div class="vers"><img alt="Comments" src="' . esc_url( admin_url( 'images/comment-grey-bubble.png' ) ) . '" /></div>'
	);	
	return $columns;
}
add_filter('manage_edit-match_columns', 'wpbo_match_columns');
 
function shot_link_custom_columns($column) {
	global $post;
	$match = wpbo_match_data($post->ID);

	if(empty($match['server']))
		$match['server'] = 'Server information coming soon!';

	switch($column) {
		case 'type':
			echo $match['type_nice'];
			break;
		case 'players':
			echo $match['num_players'];
			break;
		case 'scheduled':
			echo $match['date_nice'];
			break;
	}
}
add_action('manage_posts_custom_column', 'shot_link_custom_columns');

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

	$type = $_POST['wpbo-type'];
	switch($type) {
		case 'highlander':
			break;
		case 'normal':
			break;
		default:
			$type = 'normal';
			break;
	}
	update_post_meta($post_id, 'wpbo-type', $type);

	$info = $_POST['wpbo-info'];
	$info = htmlspecialchars($info);
	update_post_meta($post_id, 'wpbo-info', $info);

	$date = $_POST['wpbo-date'];
	$date = strtotime($date);
	update_post_meta($post_id, 'wpbo-date', $date);

	$server = $_POST['wpbo-server'];
	$server = htmlspecialchars($server);
	update_post_meta($post_id, 'wpbo-server', $server);

	$serverip = $_POST['wpbo-serverip'];
	$serverip = htmlspecialchars($serverip);
	update_post_meta($post_id, 'wpbo-serverip', $serverip);

	$serverpass = $_POST['wpbo-serverpass'];
	$serverpass = htmlspecialchars($serverpass);
	update_post_meta($post_id, 'wpbo-serverpass', $serverpass);
}
add_action('save_post', 'wpbo_post_callback');

function wpbo_admin() {
	add_meta_box( 'wpbo-matchinfo-box', 'Match Info', 'wpbo_matchinfo_box', 'match', 'normal', 'high' );
	add_meta_box( 'wpbo-people-box', 'People', 'wpbo_people_box', 'match', 'normal', 'high' );
	wp_enqueue_script( 'jquery-ui-datepicker', '/wp-content/themes/wpbo/ui.datepicker.js', array('jquery', 'jquery-ui-core'), '1.7.2' );
	wp_enqueue_script( 'wpbo-admin', '/wp-content/themes/wpbo/admin.js', array('jquery', 'jquery-ui-core', 'jquery-ui-datepicker'), '1.0' );
	wp_enqueue_style('jquery-ui-datepicker', '/wp-content/themes/wpbo/ui.datepicker.css', array(), '1.7.2', 'screen');
}
add_action('admin_menu', 'wpbo_admin');

function wpbo_matchinfo_box( $object, $box ) {
	$info = get_post_meta($object->ID, 'wpbo-info', true);
	$type = get_post_meta($object->ID, 'wpbo-type', true);
	$date = get_post_meta($object->ID, 'wpbo-date', true);
	if(empty($date))
		$date = time();
	$server = get_post_meta($object->ID, 'wpbo-server', true);
	$serverip = get_post_meta($object->ID, 'wpbo-serverip', true);
	$serverpass = get_post_meta($object->ID, 'wpbo-serverpass', true);
?>
	<table class="form-table">
		<tr valign="top">
			<th scope="row"><label for="wpbo-info">Information</label>
			<td><textarea name="wpbo-info" id="wpbo-info" style="height:10em;margin:0;width:98%;" cols="40" rows="1"><?php echo $info ?></textarea>
				<p>Place any information relevant to the players here. For example, map choices for this match or expected times.</p></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="wpbo-type">Type</label></th>
			<td><select name="wpbo-type" id="wpbo-type">
				<option value="highlander"<?php if($type === 'highlander') echo ' selected' ?>>Highlander (9v9)</option>
				<option value="normal"<?php if($type === 'normal') echo ' selected' ?>>Normal (6v6)</option>
			</select></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="wpbo-date">Date</label></th>
			<td><input type="text" name="wpbo-date" id="wpbo-date" value="<?php echo date('Y-m-d', $date) ?>" /></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="wpbo-server">Server Info</label></th>
			<td><input type="text" name="wpbo-server" id="wpbo-server" value="<?php echo $server ?>" /> <p>Put information here until you get an IP/password. Once you have those, they will replace this text in the display.</p></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="wpbo-serverip">Server IP</label></th>
			<td><input type="text" name="wpbo-serverip" id="wpbo-serverip" value="<?php echo $serverip ?>" /></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="wpbo-serverpass">Server Password</label></th>
			<td><input type="text" name="wpbo-serverpass" id="wpbo-serverpass" value="<?php echo $serverpass ?>" /></td>
		</tr>
	</table>
<?php
}

function wpbo_people_box( $object, $box ) {
	$players = get_post_meta($object->ID, 'wpbo-players', true);
	if($players) {
		wpbo_players_table($players);
	}
	else {
?>
	<p>No-one has signed up yet.</p>
<?php
	}
?>
	<input type="hidden" name="wpbo-nonce" value="<?php echo wp_create_nonce( 'wpbo-meta-box' ); ?>" />
</p>
<?php
}

function wpbo_players_table($players) {
?>
	<table class="widefat">
		<thead>
			<tr>
				<th>Name</th>
				<th>Classes</th>
				<th>Status</th>
				<th>Action</th>
			</tr>
		</thead>
		<tbody>
<?php
		foreach($players as $id => $player) {
			$name = $player['name'];
			if(!empty($player['user']))
				$name .= '(' . get_userdata($player['user'])->user_login . ')';
?>
			<tr id="wpbo-person-<?php echo $id ?>">
				<td><?php echo $name ?></td>
				<td><?php echo $player['classes'] ?></td>
				<td>
					<label><input type="radio" name="wpbo-people[<?php echo $id ?>]" value="confirmed"<?php
						if($player['status'] === 'confirmed') echo ' checked="checked"' ?> /> Confirmed</label>
					<label><input type="radio" name="wpbo-people[<?php echo $id ?>]" value="reserve"<?php
						if($player['status'] === 'reserve') echo ' checked="checked"' ?> /> Reserve</label>
				</td>
				<td><button class="wpbo-delete button" type="button">Delete</button></td>
			</tr>
<?php
		}
?>
		</tbody>
	</table>
	<input type="hidden" id="wpbo-remove-nonce" value="<?php echo wp_create_nonce( 'wpbo-remove' ); ?>" />
<?php
}

/*
function wpbo_ajax_add() {
	if(empty($_POST['id']))
		die('No ID specified');

	if(empty($_POST['name']))
		die('No name specified');

	if(empty($_POST['classes']))
		die('No classes specified');

	if(empty($_POST['status']))
		$_POST['status'] = 'confirmed';

	$uid = null;
	if(!empty($_POST['user_id']))
		$uid = (int) $_POST['user_id'];

	wpbo_add_player((int) $_POST['id'], $_POST['name'], $_POST['classes'], $_POST['status'], $uid);
}
add_action('wp_ajax_wpbo_add', 'wpbo_ajax_add');
add_action('wp_ajax_nopriv_wpbo_add', 'wpbo_ajax_add');
*/

function wpbo_form_callback_add() {
	if(empty($_POST['wpbo-nonce']) || !wp_verify_nonce( $_POST['wpbo-nonce'], 'wpbo-add-form' )) {
		status_header(400);
		die('Nonce validation failed. Try again.');
	}

	if(empty($_POST['wpbo-id'])) {
		status_header(400);
		die('Post ID must be specified.');
	}
	$post_id = (int) $_POST['wpbo-id'];

	if(empty($_POST['wpbo-name']) || empty($_POST['wpbo-class'])) {
		status_header(400);
		die('Name and classes must be specified.');
	}
	$name = $_POST['wpbo-name'];
	$class = $_POST['wpbo-class'];

	$uid = null;
	if(is_user_logged_in())
		$uid = wp_get_current_user()->ID;

	$result = wpbo_add_player($post_id, $name, $class, $uid);

	wp_redirect(get_permalink($post_id));
}
add_action('admin_post_wpbo_add', 'wpbo_form_callback_add');
add_action('admin_post_nopriv_wpbo_add', 'wpbo_form_callback_add');

function wpbo_ajax_callback_remove() {
	if(empty($_POST['wpbo-nonce']) || !wp_verify_nonce( $_POST['wpbo-nonce'], 'wpbo-remove' )) {
		status_header(400);
		die('Nonce validation failed. Try again.');
	}

	if(empty($_POST['wpbo-id'])) {
		status_header(400);
		die('Post ID must be specified.');
	}
	$post_id = (int) $_POST['wpbo-id'];

	if(empty($_POST['wpbo-name'])) {
		status_header(400);
		die('Name must be specified.');
	}
	$name = $_POST['wpbo-name'];

	wpbo_remove_player($post_id, $name);

	die('removed');
}
add_action('wp_ajax_wpbo_remove', 'wpbo_ajax_callback_remove');

function wpbo_add_player($post_id, $name, $classes, $user_id = null) {
	$players = get_post_meta($post_id, 'wpbo-players', true);
	$id = sanitize_key($name);

	if(!empty($players[$id]) && (!empty($players[$id]['user']) && $players[$id]['user'] !== $user_id))
		return false;

	$type = get_post_meta($post_id, 'wpbo-type', true);
	if($type === 'highlander')
		$limit = 18;
	else
		$limit = 12;

	if(count($players) >= $limit)
		$status = 'reserve';
	else
		$status = 'confirmed';

	$players[$id] = array(
		'name' => $name,
		'classes' => $classes,
		'status' => $status
	);
	if($user_id !== null)
		$players[$id]['user'] = $user_id;

	update_post_meta($post_id, 'wpbo-players', $players);
}

function wpbo_remove_player($post_id, $name) {
	$players = get_post_meta($post_id, 'wpbo-players', true);
	$id = sanitize_key($name);
	unset($players[$id]);

	update_post_meta($post_id, 'wpbo-players', $players);
}

function wpbo_match_data($id) {
	$match = array(
		'type' => get_post_meta($id, 'wpbo-type', true),
		'date' => get_post_meta($id, 'wpbo-date', true),
		'info' => wpautop(get_post_meta($id, 'wpbo-info', true)),
		'players' => get_post_meta($id, 'wpbo-players', true),
		'server' => get_post_meta($id, 'wpbo-server', true),
		'server_ip' => get_post_meta($id, 'wpbo-serverip', true),
		'server_pass' => get_post_meta($id, 'wpbo-serverpass', true),
	);

	if(empty($match['players']))
		$match['players'] = array();

	if($match['type'] == 'highlander')
		$match['type_nice'] = 'Highlander (9v9)';
	else
		$match['type_nice'] = 'Normal (6v6)';

	if(empty($match['date'])) {
		$match['date_nice'] = 'Unknown Date';
	}
	else
		$match['date_nice'] = date('l, jS \o\f F', $match['date']);

	$match['num_players'] = count($match['players']);
	if($match['type'] === 'highlander')
		$limit = 18;
	else
		$limit = 12;

	$match['needed'] = $limit - $match['num_players'];
	$match['reserves'] = $match['num_players'] - $limit;

	if(empty($match['server']))
		$match['server'] = 'Server information coming soon!';

	return $match;
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

add_action( 'show_user_profile', 'wpbo_profile_fields' );
add_action( 'edit_user_profile', 'wpbo_profile_fields' );

function wpbo_profile_fields( $user ) { ?>
	<h3 id="wpbo-section">Game Information</h3>
	<table class="form-table">
		<tr>
			<th><label for="wpbo-tf2user">TF2 Username</label></th>

			<td>
				<input type="text" name="wpbo-tf2user" id="wpbo-tf2user" value="<?php echo esc_attr( get_the_author_meta( 'wpbo_tf2user', $user->ID ) ); ?>" class="regular-text" /><br />
				<span class="description">Please enter the TF2 name that you most commonly use.</span>
			</td>
		</tr>
		<tr>
			<th><label for="wpbo-classes">Classes</label></th>

			<td>
				<input type="text" name="wpbo-classes" id="wpbo-classes" value="<?php echo esc_attr( get_the_author_meta( 'wpbo_classes', $user->ID ) ); ?>" class="regular-text" /><br />
				<span class="description">Enter the classes you most commonly play. This can be changed for each match.</span>
			</td>
		</tr>
	</table>
<?php }

add_action( 'personal_options_update', 'wpbo_profile_fields_save' );
add_action( 'edit_user_profile_update', 'wpbo_profile_fields_save' );

function wpbo_profile_fields_save( $user_id ) {
	if ( !current_user_can( 'edit_user', $user_id ) )
		return false;

	update_usermeta( $user_id, 'wpbo_tf2user', htmlspecialchars($_POST['wpbo-tf2user']) );
	update_usermeta( $user_id, 'wpbo_classes', htmlspecialchars($_POST['wpbo-classes']) );
}

function wpbo_feed_content($content) {
	if(is_post_type('match')) {
		global $post;

		$type = get_post_meta($post->ID, 'wpbo-type', true);
		$content = '<p>The following ';
		if($type == 'highlander')
			$content .= 'Highlander (9v9)';
		else
			$content .= 'Normal (6v6)';

		$content .= ' PUG is scheduled for ';
		$content .= date('l, jS \o\f F', get_post_meta($post->ID, 'wpbo-date', true));
		$content .= '. </p><p>';

		$content .= get_post_meta($post->ID, 'wpbo-info', true);
		$content .= '</p>';
	}
	return $content;
}
add_filter('the_content', 'wpbo_feed_content');