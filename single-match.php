<?php
/**
 * The Template used to display all single posts
 *
 * @package WordPress
 * @subpackage Twenty Ten
 * @since 3.0.0
 */
?>

<?php get_header(); ?>

		<div id="container">
			<div id="content">

<?php
the_post();
global $current_user;
get_currentuserinfo();
$match = wpbo_match_data($post->ID);
$signed_up = false;
?>

				<div id="nav-above" class="navigation">
					<div class="nav-previous"><?php previous_post_link( '%link', '<span class="meta-nav">&larr;</span> %title' ); ?></div>
					<div class="nav-next"><?php next_post_link( '%link', '%title <span class="meta-nav">&rarr;</span>' ); ?></div>
				</div><!-- #nav-above -->

				<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<h1 class="entry-title"><?php the_title(); ?> &mdash; <?php echo $match['date_nice'] ?></h1>

					<div class="entry-meta">
						<span class="meta-prep meta-prep-author"><?php _e( 'Posted on ', 'twentyten' ); ?></span>
						<a href="<?php the_permalink(); ?>" title="<?php the_time(); ?>" rel="bookmark"><span class="entry-date"><?php echo get_the_date(); ?></span></a>
						<span class="type"><a href="/matches/<?php echo $match['type'] ?>/"><?php echo $match['type_nice'] ?></a></span>
					</div><!-- .entry-meta -->

					<div class="entry-content">
						<?php
							if(!empty($match['server']) || !empty($match['server_ip'])) {
						?>
						<div class="server">
							<h3>Server</h3>
						<?php
								if(!empty($match['server_ip'])) {
									$server = $match['server_ip'];
									$url = 'steam://connect/' . $match['server_ip'];
									if(!empty($match['server_pass'])) {
										$server .= ' &mdash; Password: ' . $match['server_pass'];
										$url .= '/' . $match['server_pass'];
									}
									echo '<p><a href="' . $url . '">' . $server . '</a></p>';
								}
								else {
									echo '<p>' . $match['server'] . '</p>';
								}
						?>
						</div>
						<?php
							}
						?>
						<h3>Type</h3>
						<p><?php echo $match['type_nice'] ?></p>
						<h3>Information</h3>
						<p><?php echo $match['info']; ?></p>
						<h3>Players</h3>
						<div class="people">
							<p class="confirmed"><?php echo $match['num_players'] ?><span> confirmed</span></p>
						<?php
							if($match['needed'] > 0) {
						?>
							<p class="needed"><?php echo $match['needed'] ?><span> needed</span></p>
						<?php
							} elseif($match['reserves'] > 0) {
						?>
							<p class="reserves"><?php echo $match['reserves'] ?><span> reserves</span></p>
						<?php
							}
						?>
						</div>
						<table class="players">
							<thead>
								<tr>
									<th>Player</th>
									<th>Status</th>
									<th>Classes</th>
								</tr>
							</thead>
							<tbody>
							<?php
								if($match['num_players'] < 1) {
							?>
								<tr>
									<td colspan="3">No one yet!</td>
								</tr>
							<?php
								}
								else {
									foreach($match['players'] as $player) {
										$you = false;
										if(!empty($player['user']) && $player['user'] === $current_user->ID) {
											$you = true;
											// We use this later.
											$signed_up = true;
											$name = $player['name'];
											$class = $player['classes'];
										}
							?>
								<tr class="<?php echo $player['status']; if($you) echo ' player-is-me' ?>">
									<td><?php echo $player['name']; if($you) echo ' (That\'s You!)'; ?></td>
									<td><?php echo ucfirst($player['status']) ?></td>
									<td><?php echo $player['classes'] ?></td>
								</tr>
							<?php
									}
								}
							?>
						</table>
						<div class="clearer"></div>
						<div id="sign-up">
							<h3>Sign Up</h3>
							<form action="<?php echo admin_url( 'admin-post.php' );  ?>" method="POST">
						<?php
							if($match['needed'] > 0) {
						?>
								<p>This match still needs <?php echo $match['needed'] ?> players. First in, best dressed!</p>
						<?php
							} else {
						?>
								<p>This match is full. You can still sign up as a reserve though!</p>
						<?php
							}
						?>
							<?php
							if(!is_user_logged_in()) {
								$name = '';
								$class = '';
							?>
								<p>Are you a regular? <a href="<?php echo wp_login_url() ?>">Sign in</a> to pre-fill this form, or
								<a href="<?php echo site_url('wp-login.php?action=register', 'login') ?>">create an account</a>.</p>
								<p>Note: You can only edit your entry if you are logged in.</p>
							<?php
							}
							else {
								if(empty($name))
									$name = get_the_author_meta('wpbo_tf2user', $current_user->ID);
								if(empty($class))
									$class = get_the_author_meta('wpbo_classes', $current_user->ID);
							?>
								<p>Signed in as <?php echo $current_user->display_name ?>. <a href="<?php echo wp_logout_url() ?>">Log out</a>. Set your defaults on your <a href="<?php echo admin_url('profile.php'); ?>#wpbo-section">profile</a>.</p>
							<?php
								if($signed_up) {
							?>
								<p>You have already signed up. To update your details, put your name the same as the one you signed up for, and change your class as needed. You cannot update your name.</p>
							<?php
								}
							}
							?>
								<table class="form-table">
									<tr>
										<th scope="row"><label for="wpbo-name">Name</label></th>
										<td><input type="text" name="wpbo-name" id="wpbo-name" value="<?php echo $name ?>" /></td>
									</tr>
									<tr>
										<th scope="row"><label for="wpbo-class">Class</label></th>
										<td><input type="text" name="wpbo-class" id="wpbo-class" value="<?php echo $class ?>" />
										<p>e.g. "Soldier, Pyro".</p>
									</tr>
								</table>
								<p class="submit">
								<?php
								if(!$signed_up) {
								?>
									<input type="submit" value="Sign Me Up" />
								<?php
								} else {
								?>
									<input type="submit" value="Update My Class" />
								<?php
								}
								?>
								</p>
								<input type="hidden" name="wpbo-nonce" value="<?php echo wp_create_nonce( 'wpbo-add-form' ); ?>" />
								<input type="hidden" name="wpbo-id" value="<?php the_ID(); ?>" />
								<input type="hidden" name="action" value="wpbo_add" />
							</form>
						</div><!-- #sign-up -->
						<div class="clearer"></div>
						<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'twentyten' ), 'after' => '</div>' ) ); ?>
					</div><!-- .entry-content -->

					<div class="entry-utility">
<?php edit_post_link( __( 'Edit', 'twentyten' ), '<span class="edit-link">', '</span>' ); ?>
					</div><!-- .entry-utility -->
				</div><!-- #post-<?php the_ID(); ?> -->

				<div id="nav-below" class="navigation">
					<div class="nav-previous"><?php previous_post_link( '%link', '<span class="meta-nav">&larr;</span> %title' ); ?></div>
					<div class="nav-next"><?php next_post_link( '%link', '%title <span class="meta-nav">&rarr;</span>' ); ?></div>
				</div><!-- #nav-below -->

				<?php comments_template( '', true ); ?>

			</div><!-- #content -->
		</div><!-- #container -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
