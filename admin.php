<?php
/** @var \Tarosky\TSCF\Bootstrap $this */
defined( 'ABSPATH' ) or die( 'Do not load directly!' );
?>

<div class="wrap" ng-app="tscf" id="tscf-editor">

	<div class="tscfe" ng-controller="tscfEditor" ng-cloak>

		<h2>
			<span class="dashicons dashicons-hammer"></span>
			<?php esc_html_e( 'Tarosky Custom Field config file editor', 'tscf' ) ?>
		</h2>

		<hr/>

		<div class="tscfe-main">

			<div class=fscfe-field-list>
				<div class="tscfe-field" ng-repeat="(i, setting) in settings" ng-hide="i !== index">
					<tscf-field groups="settings" i="i" current-index="index"></tscf-field>
				</div>
			</div>
		</div>

		<div class="tscfe-side">

			<tscf-alert errors="errors"></tscf-alert>

			<button class="button" ng-click="addGroup()"><?php esc_html_e( 'Add Field Group', 'tscf' ) ?></button>

			<button class="button-primary" ng-click="saveFields()"><?php esc_html_e( 'Save', 'tscf' ) ?></button>

			<tscf-nav settings="settings" index="index"></tscf-nav>

		</div>

	</div><!-- tscfe -->


</div><!-- //.wrap -->
