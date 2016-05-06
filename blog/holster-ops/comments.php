<?
$num_comments = get_comments_number();
if ( comments_open() ) {
	if($num_comments == 0) {
		$Ccomments ="No Comments";
	}
	elseif($num_comments > 1) {
		$Ccomments = $num_comments." Comments";
	}
	else {
		$Ccomments ="1 Comment";
	}
}
$write_comments = '<a href="' . get_comments_link() .'">'. $Ccomments.'</a>';
?>
<hr />
<table width="100%" border="0" cellpadding="0" class="middlebg paddingRev3px">
	<tr>
		<td>
			<?=$write_comments?>
		</td>
		<td align="right">
			<table border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td>
						<?php previous_comments_link( '<span class="meta-nav">&larr;</span> Older Comments' ); ?>
					</td>
					<td>
						<?php next_comments_link( 'Newer Comments <span class="meta-nav">&rarr;</span>' ); ?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>

<?php if($comments) : ?>
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <?php foreach($comments as $comment) : ?>
        <tr>
			<td class="hDelimiter">
				<img src="http://www.oilfiltersonline.com/images/tr.gif" alt="">
			</td>
		</tr>
		<tr id="comment-<?php comment_ID(); ?>">
			<td class="padding10px">
				<table width="100%" border="0" cellspacing="0" class="paddingRev5px">
					<tr>
						<?php if ($comment->comment_approved == '0') : ?>
			                <p>Your comment is awaiting approval</p>
			            <?php endif; ?>
					</tr>
					<tr>
						<td align="left" valign="top" class="forumGuest">
							<?php comment_author_link(); ?>
						</td>
						<td align="right">
							<div class="articleDate">
								<?php comment_date(); ?>, <?php comment_time(); ?>
							</div>
						</td>
					</tr>
				</table>
				<table width="100%" border="0" cellspacing="0" class="paddingRev3px">
					<tr>
						<td width="60" align="left" valign="top">
							<?php echo get_avatar($comment, 90, '' ); ?>
						</td>
						<td align="left" valign="top" style="vertical-align:top;">
							<?php comment_text(); ?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
    <?php endforeach; ?>
    </table>
<?php else : ?>  
<?php endif; ?> 
<!-- Comments Form -->

<?php if(comments_open()) : ?>
	<?php if(get_option('comment_registration') && !$user_ID) : ?>
			<p>You must be <a href="<?php echo get_option('siteurl'); ?>/wp-login.php?redirect_to=<?php echo urlencode(get_permalink()); ?>">logged in</a> to post a comment.</p>
	<?php else : ?>
		<form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="review" class="comments">
			<table width="100%" cellspacing="0" align="center" class="padding4px">
				<?php if($user_ID) : ?>
					<tr>
						<td class="usual">
						</td>
						<td class="usual">
							Logged in as <a href="<?php echo get_option('siteurl'); ?>/wp-admin/profile.php"><?php echo $user_identity; ?></a>. - <a href="<?php echo get_option('siteurl'); ?>/wp-login.php?action=logout" title="Log out of this account">Log out &raquo;</a>
						</td>
					</tr>
				<?php else : ?>
					<tr>
						<td class="usual">
							<label for="author">Name <?php if($req) echo "(required)"; ?></label>
						</td>
						<td class="usual">
							<input type="text" name="author" id="author" value="<?php echo $comment_author; ?>" size="22" tabindex="1" />  
						</td>
					</tr>
					<tr>
						<td class="usual">
							<label for="email">Mail (will not be published) <?php if($req) echo "(required)"; ?></label>
						</td>
						<td class="usual">
							<input type="text" name="email" id="email" value="<?php echo $comment_author_email; ?>" size="22" tabindex="2" />  
						</td>
					</tr>
					<tr>
						<td class="usual">
							<label for="url">Website</label>
						</td>
						<td class="usual">
							<input type="text" name="url" id="url" value="<?php echo $comment_author_url; ?>" size="22" tabindex="3" />
						</td>
					</tr>
				<? endif;?>
				<?php do_action('comment_form', $post->ID); ?>
				<tr>
					<td class="usual" style="vertical-align:top; text-align:right;">
						Comment
					</td>
					<td class="usual">
						<textarea name="comment" id="comment" cols="40" rows="5" tabindex="4"></textarea>
					</td>
				</tr>
				<tr>
					<td></td>
					<td>
						<input type="hidden" name="comment_post_ID" value="<?php echo $id; ?>" />
						<span class="submit">
							<input name="submit" type="submit" id="submit" tabindex="5" value="Submit Comment" class="submit" />
						</span>
					</td>
				</tr>
			</table>
		</form>
    <?php endif; ?>
<?php else : ?>  
    <p>The comments are closed.</p>  
<?php endif; ?>
