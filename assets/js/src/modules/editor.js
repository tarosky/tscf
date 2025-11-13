angular.module('tscf').controller( 'tscfEditor', [ '$scope', '$http', '$log', function($scope, $http, $log){

  "use strict";

  $scope.index = 0;

  $scope.settings = TSCF.settings;

  console.log(TSCF);

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
    // JSON出力順と不要キー削除をここで制御する
    var payload = [];
    angular.forEach($scope.settings, function(s){
      var o = {};
      // 基本情報
      o.name  = s.name || '';
      o.label = s.label || '';
      o.type  = s.type || 'post';
      // 出力: post のときのみ post_types、term のときは taxonomies を出力
      if (o.type === 'term') {
        // term のときは taxonomies を出力し、context/priority は出力しない（プロパティ削除）
        o.taxonomies = s.taxonomies || [];
      } else {
        // post のときのみ post_types を出力
        o.post_types = s.post_types || [];
        // post のときは context/priority が設定されていれば出力（未設定ならデフォルト適用のため出力しない）
        if (s.context) {
          o.context = s.context;
        }
        if (s.priority) {
          o.priority = s.priority;
        }
      }
      // 任意項目
      if (s.description) {
        o.description = s.description;
      }
      o.fields = s.fields || [];
      payload.push(o);
    });
    $http({
      method: 'POST',
      url: TSCF.endpoint.save,
      data: payload
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
