<div class="tscfe-field-items">
	<div class="tscfe-field-item" ng-repeat="(j, field) in fields"
	     ng-class="{ col1: 1 == field.col, col2: 2 == field.col, col3: 3 == field.col, clear: field.clear }">

		<div class="tscfe-field-item-inner">

			<div class="tscfe-field-item-meta">
				<label class="block">
					<span>
						<?php _e( 'Label', 'tscf' ) ?>
						<span class="required">*</span>
					</span>
					<input type="text" ng-model="field.label"/>
				</label>
				<label class="block">
					<span>
						<?php _e( 'Key', 'tscf' ) ?>
						<span class="required">*</span>
					</span>
					<input type="text" ng-model="field.name"/>
				</label>
				<label id="change-type-{{i}}-{{j}}" class="block">
					<span>
						<?php _e( 'Type', 'tscf' ) ?>
						<span class="required">*</span>
					</span>
					<select ng-model="field.type" ng-change="changeFieldType(field)"
					        ng-options="type.name as type.label for type in types"></select>
					<input type="text" placeholder="<?php _e( 'Class Name' ) ?>"
					       ng-hide="'custom' != field.type" ng-model="field.class_name"/>
				</label>
				<label class="block" ng-if="'taxonomy_single' == field.type">
					<span>
						<?php _e( 'Taxonomy', 'tscf' ) ?>
						<span class="required">*</span>
					</span>
					<input type="text" ng-model="field.taxonomy"/>
				</label>
				<label class="block">
					<input type="checkbox"
					       ng-model="field.required"/><?php _e( 'Required', 'tscf' ) ?>
				</label>
				<div class="tscfe-field-item-controller">
					<button class="button-toggle" ng-click="toggle('#toggle-layout-' + i + '-' + j)" title="<?php esc_attr_e( 'Detail', 'tscf' ) ?>">
						<span class="dashicons dashicons-admin-generic"></span>
					</button>
					<button class="button-delete" ng-click="removeFieldAt(fields, j)" title="<?php esc_attr_e( 'Remove Field', 'tscf' ) ?>">
						<span class="dashicons dashicons-no"></span>
					</button>
					<button class="button-up" ng-if="0 < j" title="<?php esc_attr_e( 'Move down', 'tscf' ) ?>" ng-click="moveField(fields, j, -1)">
						<span class="dashicons dashicons-arrow-up-alt2"></span>
					</button>
					<button class="button-down" ng-if="fields.length - 1 != j" title="<?php esc_attr_e( 'Move up', 'tscf' ) ?>" ng-click="moveField(fields, j, 1)">
						<span class="dashicons dashicons-arrow-down-alt2"></span>
					</button>
				</div>
			</div>

			<div id="toggle-layout-{{i}}-{{j}}" class="toggle tscfe-field-item-atts">
				<hr />
				<div class="layout">
					<h4><?php _e( 'Layout', 'tscf' ) ?></h4>
					<label>
						<select ng-model="field.col"
						        ng-options="col.value as col.label for col in cols"></select>
					</label>
					<label>
						<input type="checkbox"
						       ng-model="field.clear"/><?php _e( 'Clear Float', 'tscf' ) ?>
					</label>
				</div>
				<div class="description">
					<h4><?php _e( 'Instruction', 'tscf' ); ?></h4>
					<label class="block" ng-if="field.hasOwnProperty('placeholder')">
						<span><?php _e( 'Place holder', 'tscf' ) ?></span>
						<input type="text" ng-model="field.placeholder"/>
					</label>
					<label class="block" ng-if="field.hasOwnProperty('default')">
						<span><?php _e( 'Default Value', 'tscf' ) ?></span>
						<input type="text" ng-model="field.default"/>
					</label>
					<label class="block" ng-if="field.hasOwnProperty('description')">
						<span><?php _e( 'Description', 'tscf' ) ?></span>
						<textarea rows="2" ng-model="field.description"></textarea>
					</label>
				</div>
				<div class="attributes">
					<h4><?php _e( 'Attributes', 'tscf' ); ?></h4>
					<label class="block" ng-if="field.hasOwnProperty('date_format')">
						<span><?php _e( 'Date Format', 'tscf' ) ?></span>
						<input type="text" ng-model="field.date_format"/>
					</label>
					<label class="block" ng-if="field.hasOwnProperty('separator')">
						<span><?php _e( 'Time Separator', 'tscf' ) ?></span>
						<input type="text" ng-model="field.separator"/>
					</label>
					<label class="block" ng-if="field.hasOwnProperty('time_format')">
						<span><?php _e( 'Time Format', 'tscf' ) ?></span>
						<input type="text" ng-model="field.time_format"/>
					</label>
					<label class="block" ng-if="field.hasOwnProperty('rows')">
						<span><?php _e( 'Rows', 'tscf' ) ?></span>
						<input type="text" ng-model="field.rows"/>
					</label>
					<label class="block" ng-if="field.hasOwnProperty('max')">
						<span><?php _e( 'Max', 'tscf' ) ?></span>
						<input type="text" ng-model="field.max"/>
					</label>
					<label class="block" ng-if="field.hasOwnProperty('min')">
						<span><?php _e( 'Min', 'tscf' ) ?></span>
						<input type="text" ng-model="field.min"/>
					</label>
					<label class="block" ng-if="field.hasOwnProperty('unit')">
						<span><?php _e( 'Unit', 'tscf' ) ?></span>
						<input type="text" ng-model="field.unit"/>
					</label>
					<label class="block" ng-if="field.hasOwnProperty('limit')">
						<span><?php _e( 'Image Limit', 'tscf' ) ?></span>
						<input type="number" ng-model="field.limit"/>
					</label>
					<label class="block" ng-if="field.hasOwnProperty('post_type')">
						<span><?php _e( 'Post Type', 'tscf' ) ?></span>
						<input type="text" ng-model="field.post_type"/>
					</label>
					<label class="block" ng-if="field.hasOwnProperty('language')">
						<span><?php _e( 'Language', 'tscf' ) ?></span>
						<input type="text" ng-model="field.language"/>
					</label>
					<label class="block" ng-if="field.hasOwnProperty('theme')">
						<span><?php _e( 'Theme', 'tscf' ) ?></span>
						<input type="text" ng-model="field.theme"/>
					</label>
				</div>
				<div class="options" ng-if="field.hasOwnProperty('options')">
					<h4><?php _e( 'Options', 'tscf' ) ?></h4>
					<tscf-options options="field.options"></tscf-options>
				</div>
				<div class="fields" ng-if="'iterator' == field.type">
					<tscf-iterator-fields fields="field.fields"></tscf-iterator-fields>
				</div>
			</div>
		</div>
	</div>
</div>
