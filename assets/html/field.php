

<div class="tscfe-field-inner">

	<div class="tscfe-field-controls">
		<button class="button-setting" ng-click="toggle( '#field-meta-' + i)" title="<?php _e( 'Setting', 'tscf' ) ?>">
			<span class="dashicons dashicons-admin-generic"></span>
		</button>
		<button class="button-add" ng-click="addField()" title="<?php _e( 'Add New', 'tscf' ) ?>">
			<span class="dashicons dashicons-plus"></span>
		</button>
		<button class="button-delete" ng-click="removeField()" title="<?php _e( 'Remove', 'tscf' ); ?>">
			<span class="dashicons dashicons-no"></span>
		</button>
	</div>

	<h3 class="tscfe-field-title">
		<span class="tscfe-field-title-number">{{i+1}}</span>
		<span class="tscfe-field-title-label">{{setting.label}}</span>
		<small class="tscfe-field-title-name">{{setting.name}}</small>
		- <span>{{setting.fields.length > 1 ? setting.fields.length + ' <?php _e( 'Fields', 'tscf' ) ?>' : setting.fields.length + ' <?php _e( 'Field', 'tscf' ) ?>' }}</span>
	</h3>

	<div id="field-meta-{{i}}" class="tscfe-field-meta toggle">

		<table class="form-table">
			<tr>
				<th>
					<label for="field-label-{{i}}"><?php _e( 'Label', 'tscf' ) ?><span
							class="required">*</span></label>
				</th>
				<td>
					<input class="regular-text" type="text" id="field-label-{{i}}"
					       ng-model="setting.label" placeholder=""/>
				</td>
			</tr>
			<tr>
				<th>
					<label for="field-name-{{i}}"><?php _e( 'Key', 'tscf' ) ?><span
							class="required">*</span></label>
				</th>
				<td>
					<input class="regular-text" type="text" id="field-name-{{i}}"
					       ng-model="setting.name" placeholder=""/>
					<p class="description">
						<?php _e( 'Should be unique. Only alphanumeric characters or _.', 'tscf' ) ?>
					</p>
				</td>
			</tr>
			<tr>
				<th>
					<label for="field-position-{{i}}"><?php _e( 'Position', 'tscf' ) ?></label>
				</th>
				<td>
					<select id="field-position-{{i}}" ng-model="setting.context"
					        ng-options="p.value as p.label for p in context"></select>
				</td>
			</tr>
			<tr>
				<th>
					<label for="field-priority-{{i}}"><?php _e( 'Priority', 'tscf' ) ?></label>
				</th>
				<td>
					<select id="field-priority-{{i}}" ng-model="setting.priority"
					        ng-options="p.value as p.label for p in priority"></select>
				</td>
			</tr>
			<tr>
				<th>
					<label><?php _e( 'Post Type', 'tscf' ) ?></label>
				</th>
				<td id="post-type-field-{{i}}">
					<label ng-repeat="postType in postTypes" class="tscfe-label-block">
						<input type="checkbox" ng-value="postType.name" ng-click="changeCheckbox(i)"
						       ng-checked="-1 < setting.post_types.indexOf(postType.name)"/>
						{{postType.label}}
					</label>
				</td>
			</tr>
		</table>
	</div>
	<tscf-items fields="setting.fields" i="i"></tscf-items>
</div>
