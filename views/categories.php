<?php
	global $wpdb, $all_categories;
?>
<div class="bv-ui">
	<div class="bv-ui-wrap wrap">
		<div class="bv-ui-row-with-sidenav">
			<div class="bv-ui-sidenav">
				<ul class="bv-ui-vertical-tabs bv-ui-sidenav-hide-md">
					<li class="bv-ui-vertical-tab current">
						<a href="?page=brightvessel-categories-expiration">Categories Settings</a>
					</li>
					<li class="bv-ui-vertical-tab">
						<a href="?page=brightvessel-categories-expiration&bv_section=tokens">Tokens</a>
					</li>
					<li class="bv-ui-vertical-tab">
						<a href="?page=brightvessel-categories-expiration&bv_section=assignations">Assignations</a>
					</li>
				</ul>
			</div>
			<form method="post" action="?page=brightvessel-categories-expiration&nonce=<?=wp_create_nonce('bv_expired_categories_create');?>">
				<div class="bv-ui-box">

						<?php if(isset($_GET['delete'])): ?>
							<div class="bv-notice-success">
								Entry deleted successfuly
							</div>
						<?php endif; ?>

						<?php if(isset($_POST['edit_cat'])): ?>
							<div class="bv-notice-success">
								Entry edited successfuly
							</div>
						<?php endif; ?>

						<?php if(isset($_POST['category_expiration_id'])): ?>
							<div class="bv-notice-success">
								Entry added successfuly
							</div>
						<?php endif; ?>

						

						<div class="bv-ui-notice">
							<p>These are the current categories which have an expiration date.</p>
						</div>

						<div class="bv-ui-box-header box-title-basic">
						<h3 class="bv-ui-box-title">Categories</h3>
						<?php $categories = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}categories_expiration", OBJECT); ?>

						<?php if(empty($categories)): ?>
								<h4>You did not add any expirations yet.</h4>
						<?php else: ?>	
						<table class="wp-list-table widefat fixed striped posts">
							<thead>
								<tr>
									<th scope="col" id="category" class="manage-column">Category</th>	
									<th scope="col" id="expires_in" class="manage-column">Expires In</th>	
									<th scope="col" id="actions" class="manage-column">Actions</th>	
								</tr>
							</thead>
							<tbody id="the-list">
								<?php foreach($categories as $category): ?>
								<tr>
									<td><?=get_cat_name($category->cat_id);?></td>
									<td><?=$category->expiration;?> days</td>
									<td><a onclick="jQuery('#cat_id').val(<?=$category->cat_id;?>);jQuery('#expiration_days').val(<?=$category->expiration;?>);" href="#TB_inline?width=600&height=200&inlineId=my-content-id" class="thickbox bv-ui-button bv-ui-button-sm">Edit</a> &nbsp; <a href="?page=brightvessel-categories-expiration&delete=<?=$category->cat_id;?>&nonce=<?=wp_create_nonce('bv_expired_categories_delete');?>" onclick="return confirm('Are you sure you want to delete this row?');" class="bv-ui-button bv-ui-button-sm bv-ui-button-error">Delete</a></td>
								</tr>
								<?php endforeach; ?>
							</tbody>

						</table>
						<?php endif; ?>

						<br/>
						<hr/>
						<br/>
						<h2>Add New Category</h2>
						<?php if(!empty($all_categories)): ?>
							<table class="form-table">
								<tbody>
									<tr>
										<th scope="row"><label for="category_expiration_id">Category</label></th>
										<td><select name="category_expiration_id" class="regular-text">
											<?php foreach($all_categories as $category): ?>
												<option value="<?=$category->term_id;?>"><?=$category->name;?></option>
											<?php endforeach; ?>
										</select></td>
									</tr>
									<tr>
										<th scope="row"><label for="category_expiration_expires">Expires In</label></th>
										<td><input name="category_expiration_expires" type="number" style="width:40px;" class="regular-text" value="1"> <small>Days</small></td>
									</tr>
								</tbody>
							</table>
							<p class="submit"><input name="submit" id="submit" class="bv-ui-button" value="Assign an expiration date for this category" type="submit"></p>
						<?php else: ?>
							<h4>All your categories were already assigned.</h4>
						<?php endif; ?>
					</div><!-- end box-title -->
				</div><!-- end wphb-minification-files -->
			</form>
		</div>
		<?php include_once BV_EXPIRED_CATEGORIES_PATH.'/views/footer.php'; ?>
	</div>	
</div>
<?php add_thickbox(); ?>
<div id="my-content-id" style="display:none;">
	<form method="post" action="?page=brightvessel-categories-expiration&nonce=<?=wp_create_nonce('bv_expired_categories_edit');?>">
		 <h2>Change Expiration Date</h2>
	     <table class="form-table">
				<tbody>
					<tr>
						<th scope="row"><label for="blogname">Expires In</label></th>
						<td><input name="edit_expires" id="expiration_days" type="number" style="width:40px;" class="regular-text" value="1"> <small>Days</small></td>
					</tr>
				</tbody>
			</table>
			<input name="edit_cat" type="hidden" id="cat_id" />
			<p class="submit"><input name="submit" id="submit" class="bv-ui-button" value="Change Expiration Date" type="submit"></p>
	</form>
</div>