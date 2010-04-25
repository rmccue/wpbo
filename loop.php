<?php
/**
 * The loop that displays posts
 *
 * The loop displays the posts and the post content.  See
 * http://codex.wordpress.org/The_Loop to understand it and
 * http://codex.wordpress.org/Template_Tags to understand
 * the tags used in it.
 *
 * @package WordPress
 * @subpackage Twenty Ten
 * @since 3.0.0
 */
?>

<?php /* Display navigation to next/previous pages when applicable  */ ?>
<?php if ( $wp_query->max_num_pages > 1 ) : ?>
	<div id="nav-above" class="navigation">
		<div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'twentyten' ) ); ?></div>
		<div class="nav-next"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'twentyten' ) ); ?></div>
	</div><!-- #nav-above -->
<?php endif; ?>

<?php /* If there are no posts to display, such as an empty archive page  */ ?>
<?php if ( ! have_posts() ) : ?>
	<div id="post-0" class="post error404 not-found">
		<h1 class="entry-title"><?php _e( 'Not Found', 'twentyten' ); ?></h1>
		<div class="entry-content">
			<p><?php _e( 'Apologies, but no results were found for the requested Archive. Perhaps searching will help find a related post.', 'twentyten' ); ?></p>
			<?php get_search_form(); ?>
		</div><!-- .entry-content -->
	</div><!-- #post-0 -->
<?php endif; ?>

<?php /* Start the Loop  */ ?>
<?php while ( have_posts() ) : the_post(); $match = wpbo_match_data($post->ID); ?>

<?php /* How to Display posts in the Gallery Category  */ ?>
	<?php if ( is_post_type('match') ) : ?>
		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<h2 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'twentyten' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?> &mdash; <?php echo $match['date_nice'] ?></a></h2>

			<div class="entry-meta">
				<span class="meta-prep meta-prep-author"><?php _e( 'Posted on ', 'twentyten' ); ?></span>
				<a href="<?php the_permalink(); ?>" title="<?php the_time(); ?>" rel="bookmark"><span class="entry-date"><?php echo get_the_date(); ?></span></a>
				<span class="type"><a href="/matches/<?php echo $match['type'] ?>/"><?php echo $match['type_nice'] ?></a></span>
			</div><!-- .entry-meta -->

			<div class="entry-content">
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
					?>
						<tr class="<?php echo $player['status'] ?>">
							<td><?php echo $player['name'] ?></td>
							<td><?php echo ucfirst($player['status']) ?></td>
							<td><?php echo $player['classes'] ?></td>
						</tr>
					<?php
							}
						}
					?>
				</table>
				<div class="clearer"></div>
			</div><!-- .entry-content -->

			<div class="entry-utility">
				<?php edit_post_link( __( 'Edit', 'twentyten' ), '<span class="edit-link">', '</span>' ); ?>
			</div><!-- #entry-utility -->
		</div>

<?php /* How to display posts in the asides category */ ?>
	<?php elseif ( in_category( 'asides' ) ) : ?>
		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php if ( is_archive() || is_search() ) : //Only display Excerpts for archives & search ?>
			<div class="entry-summary">
				<?php the_excerpt( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'twentyten' ) ); ?>
			</div><!-- .entry-summary -->
	<?php else : ?>
			<div class="entry-content">
				<?php the_content( __( 'Continue&nbsp;reading&nbsp;<span class="meta-nav">&rarr;</span>', 'twentyten' ) ); ?>
			</div><!-- .entry-content -->
	<?php endif; ?>

			<div class="entry-utility">
				<?php
					printf( __( '<span class="meta-prep meta-prep-author">Posted on </span><a href="%1$s" title="%2$s" rel="bookmark"><span class="entry-date">%3$s</span></a> <span class="meta-sep"> by </span> <span class="author vcard"><a class="url fn n" href="%4$s" title="%5$s">%6$s</a></span>', 'twentyten' ),
						get_permalink(),
						esc_attr( get_the_time() ),
						get_the_date(),
						get_author_posts_url( get_the_author_meta( 'ID' ) ),
						sprintf( esc_attr__( 'View all posts by %s', 'twentyten' ), get_the_author() ),
						get_the_author()
					);
				?>
				<span class="meta-sep"> | </span>
				<span class="comments-link"><?php comments_popup_link( __( 'Leave a comment', 'twentyten' ), __( '1 Comment', 'twentyten' ), __( '% Comments', 'twentyten' ) ); ?></span>
				<?php edit_post_link( __( 'Edit', 'twentyten' ), '<span class="meta-sep">|</span> <span class="edit-link">', '</span>' ); ?>
			</div><!-- #entry-utility -->
		</div><!-- #post-<?php the_ID(); ?> -->

<?php /* How to display all other posts  */ ?>
	<?php else : ?>
		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<h2 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'twentyten' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h2>

			<div class="entry-meta">
				<?php
					printf( __( '<span class="meta-prep meta-prep-author">Posted on </span><a href="%1$s" title="%2$s" rel="bookmark"><span class="entry-date">%3$s</span></a> <span class="meta-sep"> by </span> <span class="author vcard"><a class="url fn n" href="%4$s" title="%5$s">%6$s</a></span>', 'twentyten' ),
						get_permalink(),
						esc_attr( get_the_time() ),
						get_the_date(),
						get_author_posts_url( get_the_author_meta( 'ID' ) ),
						sprintf( esc_attr__( 'View all posts by %s', 'twentyten' ), get_the_author() ),
						get_the_author()
					);
				?>
			</div><!-- .entry-meta -->

	<?php if ( is_archive() || is_search() ) : //Only display Excerpts for archives & search ?>
			<div class="entry-summary">
				<?php the_excerpt( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'twentyten' ) ); ?>
			</div><!-- .entry-summary -->
	<?php else : ?>
			<div class="entry-content">
				<?php the_content( __( 'Continue&nbsp;reading&nbsp;<span class="meta-nav">&rarr;</span>', 'twentyten' ) ); ?>
				<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'twentyten' ), 'after' => '</div>' ) ); ?>
			</div><!-- .entry-content -->
	<?php endif; ?>

			<div class="entry-utility">
				<span class="cat-links"><span class="entry-utility-prep entry-utility-prep-cat-links"><?php echo twentyten_cat_list(); ?></span></span>
				<span class="meta-sep"> | </span>
				<?php $tags_text = twentyten_tag_list(); ?>
				<?php if ( ! empty( $tags_text ) ) : ?>
				<span class="tag-links"><span class="entry-utility-prep entry-utility-prep-tag-links"><?php echo $tags_text; ?></span></span>
				<span class="meta-sep"> | </span>
				<?php endif; //$tags_text ?>
				<span class="comments-link"><?php comments_popup_link( __( 'Leave a comment', 'twentyten' ), __( '1 Comment', 'twentyten' ), __( '% Comments', 'twentyten' ) ); ?></span>
				<?php edit_post_link( __( 'Edit', 'twentyten' ), '<span class="meta-sep">|</span> <span class="edit-link">', '</span>' ); ?>
			</div><!-- #entry-utility -->
		</div><!-- #post-<?php the_ID(); ?> -->

		<?php comments_template( '', true ); ?>

	<?php endif; // if different categories queried ?>
<?php endwhile; ?>

<?php /* Display navigation to next/previous pages when applicable  */ ?>
<?php if (  $wp_query->max_num_pages > 1 ) : ?>
				<div id="nav-below" class="navigation">
					<div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'twentyten' ) ); ?></div>
					<div class="nav-next"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'twentyten' ) ); ?></div>
				</div><!-- #nav-below -->
<?php endif; ?>
