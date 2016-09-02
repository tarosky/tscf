angular.module('tscf').directive('tscfField', ['$http', '$window', 'ui', function($http, $window, ui){

  "use strict";

  return {
    restrict: "E",
    replace: true,
    scope: {
      groups: '=',
      i: '=',
      currentIndex: '='
    },

    templateUrl: TSCF.template('field'),

    link: function($scope, $elem, attr){

      $scope.postTypes = TSCF.postTypes;

      $scope.context = TSCF.context;

      $scope.priority = TSCF.priority;

      $scope.setting = $scope.groups[$scope.i];

      $scope.toggle = function(target){
        ui.toggle(target);
      };

      /**
       * Checkbox is changed
       */
      $scope.changeCheckbox = function(){
        var types = [];
        jQuery('#post-type-field-' + $scope.i).find('input:checked').each(function(i, input){
          types.push(jQuery(input).val());
        });
        $scope.groups[$scope.i].post_types = types;
      };

      /**
       * Add field to setting
       *
       */
      $scope.addField = function(){
        $http({
          method: 'GET',
          url: TSCF.endpoint.field + '&field=text'
        }).then(
          function(response){
            var field = response.data.field;
            field.type = 'text';
            $scope.groups[$scope.i].fields.push( field );
          },
          function(response){
            // Error
          }
        ).then(function(){
          // Always
        });
      };

      /**
       * Remove element
       *
       */
      $scope.removeField = function(){
        if ( $window.confirm(TSCF.message.delete) ) {
          if ( $scope.i == $scope.currentIndex) {
            $scope.currentIndex = 0;
          }
          $scope.groups.splice($scope.i, 1);
        }
      };
    }
  };
}]);
