<?php global $used_categories; ?>
<div class="bv-ui">
	<div class="bv-ui-wrap wrap">
		<div class="bv-ui-row-with-sidenav">
			<div class="bv-ui-sidenav">
				<ul class="bv-ui-vertical-tabs bv-ui-sidenav-hide-md">
					<li class="bv-ui-vertical-tab">
						<a href="?page=brightvessel-categories-expiration">Categories Settings</a>
					</li>
					<li class="bv-ui-vertical-tab current">
						<a href="?page=brightvessel-categories-expiration&bv_section=tokens">Tokens</a>
					</li>
					<li class="bv-ui-vertical-tab">
						<a href="?page=brightvessel-categories-expiration&bv_section=assignations">Assignations</a>
					</li>
				</ul>
			</div>
			<form method="post" action="?page=brightvessel-categories-expiration&bv_section=tokens&nonce=<?=wp_create_nonce('bv_expired_categories_token');?>">
				<div class="bv-ui-box">
					<?php if(isset($_POST['clear_tokens'])): ?>
						<div class="bv-notice-success">
							Tokens cleared succesfully
						</div>
					<?php endif; ?>
					<div class="bv-ui-notice">
						<p>Use this form to clear all tokens assigned to a certain category. This will reset all expiration dates for the posts published under the category you select.</p>
					</div>
					<h2>Clear Tokens</h2>
					<?php if(!empty($used_categories)): ?>
							<table class="form-table">
								<tbody>
									<tr>
										<th scope="row"><label for="clear_tokens">Category</label></th>
										<td>
											<select class="regular-text" name="clear_tokens">
												<?php foreach($used_categories as $category): ?>
													<option value="<?=$category->term_id;?>"><?=$category->name;?></option>
												<?php endforeach; ?>
											</select>
										</td>
									</tr>
								</tbody>
							</table>
							<p class="submit"><input name="submit" id="submit" class="bv-ui-button" value="Clear tokens for this category" type="submit"></p>
					<?php else :?>
						<h4>There are no categories assigned yet. </h4>
					<?php endif; ?>
				</div><!-- end wphb-minification-files -->
			</form>
		</div>
		<?php include_once BV_EXPIRED_CATEGORIES_PATH.'/views/footer.php'; ?>
	</div>	
</div>