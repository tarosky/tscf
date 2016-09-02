<div>
	<ul class="tscfe-nav" ui-sortable ng-model="settings" ng-hide="!settings.length">
		<li class="tscfe-nav-item" ng-repeat="setting in settings" ng-click="changeIndex($index)"
		    ng-class="{active: $index == index}">
			{{ setting.label.length ? setting.label : TSCF.new }}
		</li>
	</ul>
</div>
