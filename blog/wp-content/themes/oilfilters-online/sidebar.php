<ul id="categories">
	<li class="topCategory">
		<a class="title cat_header" href="#">Archives</a>
		<ul>
			<? wp_get_archives( array(
				'type' => 'monthly',
				'format' => 'custom',
				'before' => '<li class="subCategory level1">',
				'after' => '</li>'
			)); ?>
		</ul>
	</li>
	<li class="topCategory">
		<a class="title cat_header" href="#">Categories</a>
		<ul>
			<? foreach(get_categories() as $cat): ?>
				<li class="subCategory level1">
					<a href="<?=get_category_link($cat->cat_ID)?>"><?=$cat->name?></a>			
				</li>
			<? endforeach; ?>
		</ul>
	</li>
</ul>

<?php
	// A second sidebar for widgets, just because.
	if ( is_active_sidebar( 'secondary-widget-area' ) ) : ?>

		<div id="secondary" class="widget-area" role="complementary">
			<ul class="xoxo">
				<?php dynamic_sidebar( 'secondary-widget-area' ); ?>
			</ul>
		</div><!-- #secondary .widget-area -->

<?php endif; ?>
