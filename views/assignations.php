<?php
	global $used_categories;
?>
<div class="bv-ui">
	<div class="bv-ui-wrap wrap">
		<div class="bv-ui-row-with-sidenav">
			<div class="bv-ui-sidenav">
				<ul class="bv-ui-vertical-tabs bv-ui-sidenav-hide-md">
					<li class="bv-ui-vertical-tab">
						<a href="?page=brightvessel-categories-expiration">Categories Settings</a>
					</li>
					<li class="bv-ui-vertical-tab">
						<a href="?page=brightvessel-categories-expiration&bv_section=tokens">Tokens</a>
					</li>
					<li class="bv-ui-vertical-tab current">
						<a href="?page=brightvessel-categories-expiration&bv_section=assignations">Assignations</a>
					</li>
				</ul>
			</div>
			<form method="post" action="?page=brightvessel-categories-expiration&bv_section=assignations&nonce=<?=wp_create_nonce('bv_expired_categories_assign');?>">
				<div class="bv-ui-box">
					<?php if(isset($_POST['clear_assignations'])): ?>
						<div class="bv-notice-success">
							Assignations removed succesfully
						</div>
					<?php endif; ?>
						<div class="bv-ui-notice">
						<p>Use this form to delete all assignations for a certain category. This will unassign all posts previously published under the category you have selected. <strong>Please use it with care, there is no way back!</strong>.</p>
					</div>
					<h2>Clear Assignations</h2>
					<?php if(!empty($used_categories)): ?>
					<form method="post">
						<table class="form-table">
							<tbody>
								<tr>
									<th scope="row"><label for="clear_assignations">Category</label></th>
									<td><select name="clear_assignations" class="regular-text">
										<?php foreach($used_categories as $category): ?>
											<option value="<?=$category->term_id;?>"><?=$category->name;?></option>
										<?php endforeach; ?>
									</select></td>
								</tr>
							</tbody>
						</table>
						<p class="submit"><input name="submit" id="submit" class="bv-ui-button bv-ui-button-error" value="Delete assignations for this category" type="submit"></p>
					</form>
					<?php else: ?>
						<h4>There are no categories assigned yet.</h4>
					<?php endif; ?>
				</div><!-- end wphb-minification-files -->
			</form>
		</div>
		<?php include_once BV_EXPIRED_CATEGORIES_PATH.'/views/footer.php'; ?>
	</div>	
</div>