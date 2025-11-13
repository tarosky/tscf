<?php

namespace Tarosky\TSCF\UI;

/**
 * TermMeta controller
 *
 * Render fields on term edit screen and support saving via Parser::prepare().
 *
 * 本コントローラは {$taxonomy}_edit_form_fields のフック内でインスタンス化され、
 * コンストラクタで行を echo して表示します。保存は Bootstrap 側の edited_{$taxonomy}
 * で Parser::prepare('term', $taxonomy, $term) を呼ぶことで実行されます。
 *
 * @package Tarosky\TSCF\UI
 */
class TermMeta extends Base {

	/**
	 * TermMeta constructor.
	 *
	 * @param \WP_Term $object
	 * @param array    $fields
	 */
	public function __construct( $object, array $fields ) {
		parent::__construct( $object, $fields );
		// 画面描画時のみ出力。保存（edited_{$taxonomy}）などのフック実行中は出力しない
		if ( is_admin() && function_exists( 'current_filter' ) && preg_match( '/_edit_form_fields$/', current_filter() ) ) {
			// ターム編集画面のテーブルに一行として出力
			echo '<tr class="form-field">';
			echo '<th scope="row"><label>' . esc_html( $this->label ) . '</label></th>';
			echo '<td>';
			// 既存のレンダリング（nonce + 各フィールド）を表示
			$this->render();
			echo '</td>';
			echo '</tr>';
		}
	}
}
