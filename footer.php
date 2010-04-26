<?php
/**
 * The template used to display the footer
 *
 * Contains the closing of the id=main div and all content
 * after.  Calls sidebar-footer.php for bottom widgets
 *
 * @package WordPress
 * @subpackage Twenty Ten
 * @since 3.0.0
 */
?>

	</div><!-- #main -->

	<div id="footer">
		<div id="colophon">

<?php get_sidebar( 'footer' ); ?>

			<p id="site-info">
				<a href="<?php echo home_url( '/' ) ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a>
			</p>

			<p id="site-generator">
				Proudly powered by <span id="generator-link"><a href="http://wordpress.org/" title="Semantic Personal Publishing Platform" rel="generator">WordPress</a></span>.
			</p>

			<p>Designed and operated by <a href="http://ryanmccue.info/">Ryan McCue</a> for <a href="http://www.reddit.com/r/tf2au">tf2au</a>.</p>

		</div><!-- #colophon -->

		<div id="footer-links">
			<h3>Links</h3>
			<ul>
				<li><a href="http://www.reddit.com/r/tf2au">TF2 Australia Reddit</a></li>
				<li><a href="http://www.reddit.com/r/rac">Reddit Australia Competitive</a></li>
			</ul>
		</div>

		<div class="clearer"></div>
	</div><!-- #footer -->

</div><!-- #wrapper -->

<?php wp_footer(); ?>

</body>
</html>
