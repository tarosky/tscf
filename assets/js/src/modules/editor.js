angular.module('tscf').controller( 'tscfEditor', [ '$scope', '$http', '$log', function($scope, $http, $log){

  "use strict";

  $scope.index = 0;

  $scope.settings = TSCF.settings;

  $scope.errors = TSCF.errors;

  /**
   * Change index
   *
   * @param i
   */
  $scope.changeIndex = function(i){
    $scope.index = i;
  };

  /**
   * Add Field Group
   */
  $scope.addGroup = function(){
    var index = $scope.settings.length + 2;
    $scope.settings.push({
      name: "new_group_" + index,
      label: TSCF.new + ' ' + index,
      type: "post",
      post_types: [],
      context: 'side',
      priority: "default",
      description: "",
      fields: []
    });
  };

  $scope.saveFields = function(){
    $http({
      method: 'POST',
      url: TSCF.endpoint.save,
      data: $scope.settings
    }).then(
      function(response){
        // Success
        console.log(response);
      },
      function(response){
        // Error
      }
    ).then(function(){
      // Always
    });
  };




} ] );
