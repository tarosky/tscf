<div class="tscfe-option">
    <button ng-click="add()"><?php _e( 'Add Option', 'tscf' ) ?></button>
    <ul ui-sortable ng-model="store" ng-hide="!store.length">
        <li ng-repeat="(index, option) in store">
            <input placeholder="<?php _e( 'Label', 'tscf' ) ?>" type="text" ng-model="option.label" />
            <input placeholder="<?php _e( 'Value', 'tscf' ) ?>" type="text" ng-model="option.value" />
            <button ng-click="remove(index)">x</button>
        </li>
    </ul>
</div>
