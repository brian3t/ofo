<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package WordPress
 * @subpackage Twenty_Ten
 * @since Twenty Ten 1.0
 */
?><!DOCTYPE html>
<!--[if lt IE 7 ]> <html lang="en" class="no-js ie6"> <![endif]-->
<!--[if IE 7 ]>    <html lang="en" class="no-js ie7"> <![endif]-->
<!--[if IE 8 ]>    <html lang="en" class="no-js ie8"> <![endif]-->
<!--[if IE 9 ]>    <html lang="en" class="no-js ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html lang="en" class="no-js"> <!--<![endif]-->
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<title><?php
	/*
	 * Print the <title> tag based on what is being viewed.
	 */
	global $page, $paged;

	wp_title( '|', true, 'right' );

	// Add the blog name.
	bloginfo( 'name' );

	// Add the blog description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		echo " | $site_description";

	// Add a page number if necessary:
	if ( $paged >= 2 || $page >= 2 )
		echo ' | ' . sprintf( 'Page %s', max( $paged, $page ) );

	?></title>
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'stylesheet_url' ); ?>" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<script type="text/javascript">var disqus_developer = 1;</script> 
<?php
	/* We add some JavaScript to pages with the comment form
	 * to support sites with threaded comments (when in use).
	 */
	if ( is_singular() && get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );

	/* Always have wp_head() just before the closing </head>
	 * tag of your theme, or you will break many plugins, which
	 * generally use this hook to add elements to <head> such
	 * as styles, scripts, and meta tags.
	 */
	wp_head();
?>
</head>

<body <?php body_class(); ?>>
<div id="lilWayne" class="hfeed">
	<div id="header">
		<div id="headerCenter">
			<a href="http://www.oilfiltersonline.com/"><img id="logoL" src="http://www.oilfiltersonline.com/images/oil-filters/logo.png" alt="Fram Oil Filters and Oil Filter Supplies" title="Fram Oil Filters and Oil Filter Supplies" class="png"></a>
			<span id="freeShipping"><a href="special-offers" title="$5 Standard Shipping on all auto parts orders"><em>$5</em> Standard Shipping Any Order Any Size*</a></span>
			<ul id="corner-nav">
				<li id="viewCart"><a href="http://www.oilfiltersonline.com/basket.php" class="button fixed">View Cart</a><div id="cartContents"></div></li>
				<li><a href="user_home.php" class="button fixed">Your Account</a></li>
			</ul>
			<div id="search_top">
			<form method="get" action="http://www.oilfiltersonline.com/products_search.php">
				<input type="text" size="50" class="input" name="search_string">&nbsp;<input type="submit" value="Search" class="button">
			</form>
			</div>
		</div>
		<div class="topnav">
			<ul id="navigation">
				<li><a href="/" title="Your #1 Source for all of your automotive parts, filters and car accessories!">Home</a> </li>
				<li>
					<a href="http://www.oilfiltersonline.com/products.php" title="The fastest and easiest way to buy car parts and accessories">Categories
					<span>Shop By Category</span></a>
						<ul>
							<li><a title="Filters" href="http://www.oilfiltersonline.com/Filters">Filters</a>
								<ul>
									<li><a href="http://www.oilfiltersonline.com/filters/Oil-Filters" title="Oil Filters">Oil Filters</a></li>
									<li><a href="http://www.oilfiltersonline.com/filters/Air-Filters" title="Air Filters">Air Filters</a></li>
									<li><a href="http://www.oilfiltersonline.com/filters/Fuel-Filters" title="Fuel Filters">Fuel Filters</a></li>
									<li><a href="http://www.oilfiltersonline.com/filters/Transmission-Filters" title="Transmission Filters">Transmission Filters</a></li>
									<li><a href="http://www.oilfiltersonline.com/filters/hydraulic-filters" title="Hydraulic Filters">Hydraulic Filters</a></li>
									<li><a href="http://www.oilfiltersonline.com//filters/coolant-filters" title="Coolant Filters">Coolant Filters</a></li>
								</ul>
							</li>
							<li><a title="Brakes" href="http://www.oilfiltersonline.com/brakes">Brakes</a></li>
							<li><a title="Oil Change Supplies" href="http://www.oilfiltersonline.com/Supplies">Supplies</a></li>
							<li><a title="Oxygen Sensors" href="http://www.oilfiltersonline.com/Oxygen-Sensors">Oxygen Sensors</a></li>
							<li><a title="Spark Plugs" href="http://www.oilfiltersonline.com/Spark-Plugs">Spark Plugs</a></li>
						</ul>
				</li>
				<li>
					<a href="http://www.oilfiltersonline.com/manufacturers" title="Find your car parts by Manufacturer">Manufacturers
					<span>Shop By Manufacturer</span></a>
						<ul>
							<li><a title="Autolite" href="http://www.oilfiltersonline.com/manufacturers/Autolite">Autolite</a></li>
							<li><a title="Bendix" href="http://www.oilfiltersonline.com/manufacturers/Bendix">Bendix</a></li>
							<li><a title="Bosch" href="http://www.oilfiltersonline.com/manufacturers/Bosch">Bosch</a></li>
							<li><a title="Denso" href="http://www.oilfiltersonline.com/manufacturers/Denso">Denso</a></li>
							<li><a title="Fram" href="http://www.oilfiltersonline.com/manufacturers/Fram">FRAM</a></li>
							<li><a title="K and N" href="http://www.oilfiltersonline.com/manufacturers/K-and-N">K&amp;N</a></li>	
						</ul>
				</li>
				<li>
					<a href="http://www.oilfiltersonline.com/page.php?page=recycling" title="Use our Oil Recycling Location Finder to find the nearest oil recycling center">Oil Recycling<br>
					<span>Save The Environment</span></a>
				</li>
				<li>
					<a href="<?=home_url( '/' ); ?>" title="Gain some knowledge">
						Blog
						<span>Gain some knowledge</span>
					</a>
					<?php wp_nav_menu( array('container' => false ) ); ?>
				</li>
				<li>
					<a href="http://www.oilfiltersonline.com/articles.php?category_id=36" title="Ask our expert staff about filters, spark plugs, oxygen sensors and brake pads">Get Help
					<span>Ask A Question</span></a>
						<ul>
							<li><a title="Knowledge Base" href="http://www.oilfiltersonline.com/Filter-Finder-Knowledge-Base">Knowledge Base</a></li>
							<li><a title="Ask A Question" href="http://www.oilfiltersonline.com/support.php">Ask A Question</a></li>
						</ul>
				</li>
			</ul>
		</div>
	</div><!-- End #header -->
	<div id="bodyWrapper" class="tupac">