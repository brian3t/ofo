<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package WordPress
 * @subpackage Twenty_Ten
 * @since Twenty Ten 1.0
 */

get_header(); ?>
    <div id="mainTable">
        <div id="blogContent" role="main" class="mainTableCellCenter">
            <? if (function_exists('bcn_display')): ?>
                <div class="block-products-breadcrumb">
                    <h5 class="breadcrumbs"><a href="/">Home Page</a> &gt; <? bcn_display() ?></h5>
                </div>
            <? endif; ?>
            <?php if (have_posts()):
                while (have_posts()) : the_post(); ?>
                    <?php if (is_archive() || is_search()) : // Only display excerpts for archives and search. ?>
                        <div <?php post_class('summary'); ?>>
                            <h2><a href="<?= get_permalink($id) ?>"><?php the_title(); ?></a></h2>
                            <div class="entry-meta navigator small">
                                <?php twentyten_posted_on(); ?>
                            </div><!-- .entry-meta -->
                            <?php the_excerpt(); ?>
                        </div><!-- .entry-summary -->
                    <?php else : ?>
                        <div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                            <div class="titleTopCenter">
                                <?php if (is_front_page()){ ?>
                                    <h2 class="entry-title"><a href="<?= get_permalink($id) ?>"><?php the_title(); ?></a></h2>
                                <?php } else { ?>
                                    <h1 class="entry-title"><a href="<?= get_permalink($id) ?>"><?php the_title(); ?></a></h1>
                                <?php } ?>
                                <div class="entry-meta navigator">
                                    <?php twentyten_posted_on(); ?>
                                </div><!-- .entry-meta -->
                            </div>
                            <div class="clear"></div>
                            <?php if (has_post_thumbnail()): ?>
                                <div class="post-thumbnail">
                                    <?php the_post_thumbnail(); ?>
                                </div>
                            <?php endif; ?>
                            <div class="entry-content wrapper">
                                <?php the_content(); ?>
                                <?php wp_link_pages(array('before' => '<div class="navigator">' . __('Pages:', 'twentyten'), 'after' => '</div>')); ?>
                                <?php edit_post_link(__('Edit', 'twentyten'), '<span class="edit-link">', '</span>'); ?>
                            </div><!-- .entry-content -->
                        </div><!-- #post-## -->

                        <?php comments_template('', true); ?>
                    <?php endif; ?>

                <?php endwhile; ?>
            <?php endif; ?>
        </div><!-- #content -->
        <div class="mainTableCellRight">
            <?php get_sidebar(); ?>
        </div>
    </div><!-- #container -->
<?php get_footer(); ?>