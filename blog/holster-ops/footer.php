<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content
 * after.  Calls sidebar-footer.php for bottom widgets.
 *
 * @package WordPress
 * @subpackage Twenty_Ten
 * @since Twenty Ten 1.0
 */
?>
	</div><!-- #main -->

<div id="footer">
    <div id="footerCenter">
        <ul id="footer-nav">
            <li><a href="http://www.holsterops.com/">Home</a></li>

            <li><a href="http://www.holsterops.com/FAQ">Help/FAQs</a></li>

            <li><a href="http://www.holsterops.com/page.php?page=faq">Privacy</a></li>

            <li><a href="http://www.holsterops.com/page.php?page=contact">Contact Us</a></li>

            <li><a href="http://www.holsterops.com/page.php?page=terms">Terms</a></li>

            <li><a href="http://www.holsterops.com/page.php?page=links">Links</a></li>

            <li>
                <ul id="sites">
                    <li class="topCategory">Our Sites</li>

                    <li><a href="http://www.customeuropeanplates.com">CustomEuropeanPlates.com</a></li>

                    <li><a href="http://www.holsterops.com">Holsterops.com</a></li>

                    <li><a href="http://www.wizdog.com">Wizdog.com</a></li>

                    <li><a href="http://www.OilFiltersOnline.com">OilFiltersOnline.com</a></li>
                </ul>
            </li>
        </ul>

        <ul>
            <li class="topCategory "><a href="holsters">Holsters</a></li>

            <li class="subCategory level1 "><a href="Holsters/Duty">Duty</a></li>

            <li class="subCategory level1 "><a href="Holsters/Concealment">Concealment</a></li>

            <li class="subCategory level1 "><a href="Holsters/Tactical">Tactical</a></li>
        </ul>

        <ul>
            <li class="topCategory "><a href="Featured-Products">Featured Products</a></li>
        </ul>

        <ul>
            <li class="topCategory "><a href="Belts-and-Mag-Cases">Belts and Mag Cases</a></li>

            <li class="subCategory level1 "><a href="Duty-Belts">Belts</a></li>

            <li class="subCategory level1 "><a href="Magazine-Cases">Mag Cases</a></li>

            <li class="subCategory level2 "><a href="Buy-Gun-Holsters?category_id=40">Safariland</a></li>
        </ul>

        <ul>
            <li class="topCategory "><a href="Holster-Accessories">Holster Accessories</a></li>

            <li class="subCategory level1 "><a href="Handgun-Accessories/Quick-Attachment-Systems">Quick Attachment Systems</a></li>

            <li class="subCategory level2 "><a href="Handgun-Accessories/QAS/QLS">QLS</a></li>

            <li class="subCategory level2 "><a href="MLS">MLS</a></li>

            <li class="subCategory level2 "><a href="ELS">ELS</a></li>

            <li class="subCategory level1 "><a href="holster-mounts">Holster Mounts</a></li>
        </ul>

        <ul>
            <li class="topCategory "><a href="Rifle-Accessories">Rifle Accessories</a></li>
        </ul>

        <ul>
            <li class="topCategory "><a href="Handgun-Accessories">Handgun Accessories</a></li>
        </ul>

        <ul>
            <li class="topCategory "><a href="Flashlights-optics">Flashlights and Optics</a></li>

            <li class="subCategory level1 "><a href="Flashlights-and-optics/flashlights">Flashlights</a></li>
        </ul>

        <ul id="manufacturersCat">
            <li class="topCategory "><a href="manufacturers">Manufacturers</a></li>

            <li class="subCategory level1 "><a href="manufacturers/safariland">Safariland</a></li>

            <li class="subCategory level1 "><a href="manufacturers/bianchi">Bianchi</a></li>
        </ul>
    </div>

    <div class="clear"></div>
</div>


<!-- ======== -->

<?php
	/* Always have wp_footer() just before the closing </body>
	 * tag of your theme, or you will break many plugins, which
	 * generally use this hook to reference JavaScript files.
	 */

	wp_footer();
?>
</body>
</html>
