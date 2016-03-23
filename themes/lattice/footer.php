<?php
/**
 * Footer Template.
 *
 * @link http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Lattice
 * @since	1.0
 * @version	1.0
 */
?>
	<footer id="footer" role="contentinfo">
		<div class="inside">
			<?php lattice_social_icons(); ?>
			<p><?php printf( __( '&copy; %1$s %2$s. Powered by %3$sWordPress%4$s.', 'lattice' ), date( 'Y' ), get_bloginfo( 'name', 'display' ), '<a href="http://wordpress.org">', '</a>' ); ?></p>
			<p><?php printf( __( '%1$sLattice Theme%2$s by Sunny Ratilal.', 'lattice' ), '<a href="https://easydigitaldownloads.com/themes/lattice">', '</a>' ); ?></p>
		</div><!-- /.inside -->
	</footer><!-- /#footer -->

</div><!-- /#container -->
<?php wp_footer(); ?>

<?php lattice_shopping_cart(); ?>

<?php lattice_modal(); ?>
</body>
</html>