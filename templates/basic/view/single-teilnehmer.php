<?php
/**
 * The Template for displaying all single posts.
 *
 * @package Betheme
 * @author Muffin group
 * @link http://muffingroup.com
 */

get_header();

?>
<meta name="description" content="Design Dir Deine mömax Wunsch-Bettwäsche und bekomme sie geschenkt!">
<!-- #Content -->
<div id="Content">
	<div class="content_wrapper clearfix">

		<!-- .sections_group -->
		<div class="sections_group">
			<?php
				while ( have_posts() ){
					the_post();
					$vorname = get_post_meta(get_the_ID(), 'vorname', true);
					?>
					<div class="sf_sub_single_container col-md-8 sf_sub_single_container_big marginBottomBig">
						<div class="sub_single_pic sub_single_pic_big marginTopMedium">
							<div class="tape">
								<img src="<?php echo sfgewinnspiel_url. 'templates/'. sfgewinnspiel_template.'/img/tape.png'; ?>" />
							</div>
							<div class="backgroundBoden">
			        	<img src="/wp-content/plugins/sf-gewinnspiel/templates/basic/img/bg_designer.png">
			        </div>
			        <div class="backgroundBett">
			        	<img src="/wp-content/plugins/sf-gewinnspiel/templates/basic/img/bettmock_mm.jpg">
			        </div>
							<div class="image_container">
								<?php echo the_post_thumbnail("full") ;?>
							</div>
							<div class="sub_single_name">
								<?php echo $vorname;?>
							</div>
						</div>
					</div>
					<?php
				}
				echo '<div class="col-md-4">';
				echo '<center>';
				echo do_shortcode('[mashshare]');
				echo '</center>';
				echo '</div>';
			?>

		</div>

		<!-- .four-columns - sidebar -->
		<?php get_sidebar(); ?>

	</div>
</div>

<?php get_footer();

// Omit Closing PHP Tags
