<div class="fields" ng-if="fields">
	<h4>
		<button ng-click="addField(fields)"><?php _e( 'Add Field', 'tscf' ); ?></button>
		<?php _e( 'Fields', 'tscf' ); ?>
	</h4>

	<div class="tscfe-field-child" ng-repeat="(idx, f) in fields">
		<div class="tscfe-field-item-meta">
			<label class="block">
				<span>
					<?php _e( 'Label', 'tscf' ); ?>
					<span class="required">*</span>
				</span>
				<input type="text" ng-model="f.label"/>
			</label>
			<label class="block">
				<span>
					<?php _e( 'Key', 'tscf' ); ?>
					<span class="required">*</span>
				</span>
				<input type="text" ng-model="f.name"/>
			</label>
			<label class="block">
				<span>
					<?php _e( 'Type', 'tscf' ); ?>
					<span class="required">*</span>
				</span>
				<select ng-model="f.type" ng-change="changeFieldType(f)"
						ng-options="type.name as type.label for type in childTypes"></select>
			</label>

			<div class="tscfe-field-item-controller">
				<button class="button-delete" ng-click="removeFieldAt(fields, idx)" title="<?php esc_attr_e( 'Remove Field', 'tscf' ) ?>">
					<span class="dashicons dashicons-no"></span>
				</button>
				<button class="button-up" ng-if="0 < idx" title="<?php esc_attr_e( 'Move down', 'tscf' ) ?>" ng-click="moveField(fields, idx, -1)">
					<span class="dashicons dashicons-arrow-up-alt2"></span>
				</button>
				<button class="button-down" ng-if="fields.length - 1 != idx" title="<?php esc_attr_e( 'Move up', 'tscf' ) ?>" ng-click="moveField(fields, idx, 1)">
					<span class="dashicons dashicons-arrow-down-alt2"></span>
				</button>
			</div>
		</div>

		<!-- 再帰: f が iterator の場合は、その子フィールド配列 f.fields に対して同じコンポーネントを再適用 -->
		<tscf-iterator-fields ng-if="'iterator' == f.type" fields="f.fields"></tscf-iterator-fields>
	</div>
</div>
