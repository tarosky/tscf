/* global TSCF:false */

angular.module( 'tscf' ).directive( 'tscfItems', [ '$http', '$window', 'ui', function( $http, $window, ui ) {
	'use strict';

	return {
		restrict: "E",
		replace: true,
		scope: {
			fields: '=',
			i: '=',
		},

		templateUrl: TSCF.template( 'items' ),
		link: function( $scope, $elem, attr ) {
			$scope.cols = TSCF.cols;

			$scope.types = [];
			$scope.childTypes = [];

			$scope.toggle = function( target ) {
				ui.toggle( target );
			};

			for ( var prop in TSCF.types ) {
				if ( TSCF.types.hasOwnProperty( prop ) ) {
					$scope.types.push( {
						name: prop,
						label: TSCF.types[ prop ],
					} );
					$scope.childTypes.push( {
						name: prop,
						label: TSCF.types[ prop ],
					} );
				}
			}

			/**
			 * Ensure iterator fields property exists (recursive)
			 * iterator 繰り返しフィールドにおいて、ネスト時にフィールドを必ず配列にするための再起的な補正処理
			 *
			 * @param {Object} field
			 */
			function ensureIteratorFields( field ) {
				if ( ! field || ! field.type ) {
					return;
				}
				if ( 'iterator' === field.type ) {
					if ( ! field.fields ) {
						field.fields = [];
					} else {
						for ( var idx = 0; idx < field.fields.length; idx++ ) {
							ensureIteratorFields( field.fields[ idx ] );
						}
					}
				}
			}

			// ページロード時に既存の iterator フィールドについても fields プロパティを補完する
			if ( Array.isArray( $scope.fields ) ) {
				for ( var fi = 0; fi < $scope.fields.length; fi++ ) {
					ensureIteratorFields( $scope.fields[ fi ] );
				}
			}

			/**
			 * Fill field property
			 *
			 * @param {Object} target
			 * @param {Object} field
			 */
			function fillProp( target, field ) {
				var prop;

				// name と label はユーザー入力を優先して、サーバ定義に無くても消さない
				for ( prop in target ) {
					if (
						'type' !== prop &&
						'name' !== prop &&
						'label' !== prop &&
						! field.hasOwnProperty( prop )
					) {
						delete target[ prop ];
					}
				}

				// Add unsatisfied property.
				for ( prop in field ) {
					if ( ! target.hasOwnProperty( prop ) ) {
						target[ prop ] = field[ prop ];
					}
				}
			}

			/**
			 * Update field type definition
			 * サーバから定義を取得して、1つの field オブジェクトを更新する
			 *
			 * @param {Object} field
			 */
			function updateFieldType( field ) {
				if ( ! field || ! field.type ) {
					return;
				}

				$http( {
					method: 'GET',
					url: TSCF.endpoint.field + '&field=' + field.type,
				} ).then(
					function( response ) {
						var def = response.data.field;
						fillProp( field, def );
					},
					function( response ) {
						// Error.
					}
				).then( function() {
					// Always.
				} );
			}

			/**
			 * Change field type
			 * 任意の階層の field の type が変わったときに呼ぶ汎用関数
			 *
			 * @param {Object} field
			 */
			$scope.changeFieldType = function( field ) {
				if ( ! field ) {
					return;
				}

				// custom タイプはそのまま
				if ( 'custom' === field.type ) {
					return;
				}

				// 先にサーバ定義を反映
				updateFieldType( field );

				// iterator の場合は、最後に必ず fields を用意しておく
				if ( 'iterator' === field.type && ! Array.isArray( field.fields ) ) {
					field.fields = [];
				}
			};

			/**
			 * Add Field
			 * 任意の階層の fields 配列に新しいフィールドを追加する
			 *
			 * @param {Object[]} fields
			 */
			$scope.addField = function( fields ) {
				if ( ! Array.isArray( fields ) ) {
					return;
				}

				var newField = {
					name: '',
					label: '',
					type: 'text',
				};

				fields.push( newField );

				// 追加したフィールドに対してデフォルトプロパティを適用
				updateFieldType( newField );
			};

			/**
			 * Remove field
			 * 任意の fields 配列から index 番目を削除
			 *
			 * @param {Object[]} fields
			 * @param {Number} index
			 */
			$scope.removeFieldAt = function( fields, index ) {
				if ( ! Array.isArray( fields ) ) {
					return;
				}
				if ( index < 0 || index >= fields.length ) {
					return;
				}
				if ( $window.confirm( TSCF.message.delete ) ) {
					fields.splice( index, 1 );
				}
			};

			/**
			 * Move field
			 * 任意の fields 配列の中で、index の要素を step 分だけ移動
			 *
			 * @param {Object[]} fields
			 * @param {Number} index
			 * @param {Number} step
			 */
			$scope.moveField = function( fields, index, step ) {
				if ( ! Array.isArray( fields ) ) {
					return;
				}
				var next = index + step;
				if ( next < 0 || next >= fields.length ) {
					return;
				}
				var tmp = angular.copy( fields[ next ] );
				fields[ next ] = fields[ index ];
				fields[ index ] = tmp;
			};
		},
	};
} ] );

/**
 * iterator
 * 繰り返しフィールド
 */
angular.module( 'tscf' ).directive( 'tscfIteratorFields', [ function() {
	'use strict';

	return {
		restrict: 'E',
		scope: true,
		templateUrl: TSCF.template( 'items-iterator-fields' ),
		link: function( $scope, $elem, attr ) {
			// fields で渡された式を監視して、このスコープの fields に反映する
			$scope.$watch(
				function() {
					return $scope.$eval( attr.fields );
				},
				function( value ) {
					$scope.fields = value;
				}
			);
		},
	};
} ] );
