<?php
/**
 * Admin Settings
 */
?>
<div class="wrap">

  <h1>Webcomics Settings <a class="page-title-action" href="/wp-admin/edit-tags.php?post_type=crash-bam-zowie&taxonomy=<?= $this->plugin_slug . '-issues' ?>">Add Webcomic Series</a></h1>

	<div class="the_series clearfix">

	<?php

  foreach( $terms as $term ) {

			// print_r( $term );

			$meta = get_term_meta( $term->term_id );

			// print_r( $meta );

      $image_id = get_term_meta ( $term->term_id, 'issues-image-id', true );

			print '<section id="' . $term->slug . '-block" class="coi-center-block card row cf">';

			print '<div class="contain">';

				// Title and Description

        if ( $image_id ) {
          print wp_get_attachment_image( $image_id, 'large', false, array( 'style' => 'display:block; max-width:100%; height:auto; margin:1em auto;' ) );
        }

				print '<h2>' . $term->name . ' <a class="button button-small" href="/wp-admin/term.php?taxonomy=' . $this->plugin_slug . '&tag_ID=' . $term->term_id . '">Edit</a></h2>';

				print '<div class="description">' . $term->description . '</div>';

				// Content Counts

				print '<dl class="summary">';

          // Chapters

          print '<dt><strong>Chapters/Issues:</strong></dt>';

          $children_count = wp_count_terms( $term->taxonomy, array( 'child_of' => $term->term_id ) );

					print '<dd>' . intval( wp_count_terms( $term->taxonomy, array( 'child_of' => $term->term_id ) ) ) . '</dd>';

					// Pages

          print '<dt><strong>Pages:</strong></dt>';

          $args = array(
            'post_type' => $this->plugin_slug,
          	'tax_query' => array(
          		array(
          			'taxonomy' => $term->taxonomy,
          			'field'    => 'term_id',
          			'terms'    => $term->term_id,
          		),
          	),
          );
          $the_query = new WP_Query( $args );

					print '<dd>' . intval( $the_query->found_posts ) . '</dd>';

				print '</dl>';

			print '</div>';

			print '</section>';

		}

    ?>

  </div><?php // .the_centers ?>

	<div class="block"><a class="button button-primary" href="/wp-admin/edit-tags.php?post_type=crash-bam-zowie&taxonomy=<?= $this->plugin_slug . '-issues' ?>">Add Webcomic Series</a></div>

</div><?php // .wrap ?>
