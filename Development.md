# TSCF 開発ガイド（SSOT）

本ドキュメントは TSCF（Tarosky Custom Field manager）の開発に関する「単一の正（Single Source of Truth, SSOT）」です。開発で必要な技術情報・手順・構成・注意事項は本書に集約します。
- AI支援向け補助: `.clinerules`（AIの振る舞い方・重複防止ルールなど最小限のメタ情報のみ）
- 本書更新原則: 仕様や手順を変更したら、必ず本書を更新して「正」とします

参考: https://github.com/tarosky/taro-sitemap/blob/main/Development.md

---

## プロジェクト概要

TSCF は、WordPress のカスタムフィールドを効率的に管理するためのプラグインです。

### 解決する課題
- カスタムフィールド運用の複雑化
- JSON 設定の一元管理
- 大規模案件に耐えるスケーラブルな拡張性

### 技術スタック
- PHP: 7.4+（PSR-0 オートローディング, Composer）
- JavaScript: Angular 1.x（安定運用）
- CSS: SCSS（Gulp + dart-sass）
- ビルド: Gulp
- 依存管理: Composer（PHP）/ npm（JS）
- 開発環境: @wordpress/env（Docker）
- コード品質: husky + lint（Git フック）

---

## ディレクトリ構造

```
tscf/
├── .github/                    # GitHub関連設定
│   └── workflows/
│       └── wordpress.yml       # WordPress.orgデプロイ・テスト
├── .husky/                     # Gitフック設定
│   ├── pre-commit              # コミット前フック
│   └── pre-push                # プッシュ前フック（必要に応じ有効化）
├── assets/                     # ソースアセット（ビルド前）
│   ├── html/                   # 管理画面HTMLテンプレート
│   ├── js/src/                 # JavaScript（Angular 1.x）
│   │   ├── modules/            # モジュール
│   │   └── services/           # サービス
│   └── scss/                   # SCSS
├── bin/                        # ビルド/ユーティリティスクリプト
│   ├── build.sh                # プラグインビルド
│   └── clean.sh                # クリーンアップ
├── languages/                  # 翻訳ファイル
│   ├── tscf.pot                # POTテンプレート
│   ├── tscf-ja.po              # 日本語翻訳
│   └── tscf-ja.mo              # コンパイル済み翻訳
├── src/                        # PHPソースコード
│   └── Tarosky/TSCF/
│       ├── Pattern/            # 共通パターン・トレイト
│       ├── UI/                 # UIコンポーネント
│       │   └── Fields/         # フィールドタイプ実装
│       ├── Utility/            # ユーティリティ
│       ├── Bootstrap.php       # 初期化
│       ├── Editor.php          # エディター機能
│       └── Rest.php            # REST API
├── tests/                      # PHPUnit テスト
├── vendor/                     # Composer依存（生成）
├── node_modules/               # npm依存（生成）
├── tscf.php                    # プラグインメイン
├── admin.php                   # 管理画面
├── functions.php               # ヘルパー
├── composer.json               # PHP依存
├── package.json                # JS依存/スクリプト
├── gulpfile.js                 # ビルド設定
├── phpcs.ruleset.xml           # PHP コーディング規約
└── .wp-env.json                # WP 環境設定
```

### 主要ディレクトリの説明
- src/Tarosky/TSCF/Pattern/: 各機能で共通利用される抽象/トレイト
- src/Tarosky/TSCF/UI/Fields/: 各フィールドタイプ（Text/Image/Date 等）
- src/Tarosky/TSCF/Utility/: ヘルパー/ユーティリティ
- assets/js/src/: Angular 1.x の modules/services
- assets/html/: 管理画面テンプレート
- .husky/: Git フック設定（品質管理）

---

## ブランチ運用とコントリビュート

- デフォルトブランチ: master
- masterブランチへの直接push: ブランチ保護ルールで禁止（必ず PR 経由）
- ブランチ命名
  - 機能: feature/<topic>
  - 修正: fix/<topic>
  - 文書: docs/<topic>
  - 雑務: chore/<topic>
- PR 指針
  - 小さく出す / 変更概要・確認方法・影響範囲を明記
  - 必要に応じスクリーンショット/ログ添付
  - Issue を参照し PR 本文に「Closes #<番号>」
- コミットメッセージ例
  - feat: add Foo field
  - fix: select2 cache disabled
  - docs: update README

最短手順:
1) Issue を選定 2) ブランチ作成 3) 変更 4) Lint/Test 5) PR 作成 6) レビュー→マージ

---

## 環境要件と初期セットアップ
いずれもpackage.jsonおよびcomposer.jsonでバージョンを管理。

- Node.js（Voltaでバージョン管理）
- PHP
- Docker Desktop（wp-envを使用）
Dockerの設定では、以下が可能です。

開発に関係するプラグイン・テーマ（検証用、依存関係など）の指定。
PHPのバージョン指定。

依存関係のインストール:
```bash
composer install
npm install
```

開発環境（Docker 上の WordPress）:
```bash
# 起動
npm start

# 初回後/バージョン更新時
npm run update

# 停止
npm stop
```

WordPress 環境:
- 開発サイト: http://localhost:8888
- テストサイト: http://localhost:8889
- WP-CLI: npm run cli -- <コマンド>
- テスト環境 WP-CLI: npm run cli:test -- <コマンド>

---

## 日常のコマンド

ビルド:
```bash
npm run package     # 全アセット
npm run watch       # 監視
```

Lint:
```bash
npm run lint        # JS/CSS
composer lint       # PHP
```

自動修正:
```bash
composer fix        # PHP
```

テスト:
```bash
composer test       # PHP 単体テスト
```

WP-CLI 例:
```bash
npm run cli -- plugin list
npm run cli -- log list
```

---

## Husky（コード品質管理）

概要:
- Git フックによる自動チェックを実施（pre-commit / pre-push）

設定済みフック:
- pre-commit: npm test（JS/CSS lint + PHP lint 相当を実行想定）
- pre-push: 現状は一時的に無効化（将来的に npm run test:full を想定）

動作確認:
```bash
# pre-commit 確認
git commit -m "Test commit"
# → "Husky pre-commit hook is working!" が表示される

# pre-push 確認
git push
# → composer test 等が実行される構成に調整可能
```

将来的な拡張:
- lint-staged 連携
- PHP/JS のコミット前チェック強化

---

## コア・コンセプト

### 強み
TSCF は主に以下の強みを持っています。

1. フィールド定義をJSONファイルで一元管理することができる
2. フィールド定義用のJSONファイルはテーマディレクトリに設置されるため、Git でバージョン管理ができる
3. 管理画面からカスタムフィールドの作成とJSONファイルへの出力ができる


### フィールドタイプの拡張

```php
// src/Tarosky/TSCF/UI/Fields/CustomField.php
class CustomField extends Base {
    protected function input( $name, $value, $field ) {
        // フィールドのHTML出力
    }
    protected function sanitize( $input, $field ) {
        // 入力値のサニタイズ
    }
}
```

原則:
- Base を継承し、input()/sanitize() を実装
- UI（Angular/HTML）と PHP の責務分離
- 翻訳文字列は languages/ に取り込める形で記述

---

## トレードオフと重要な注意事項

設計上のトレードオフ:
- Angular 1.x を採用（安定性優先。将来的な移行は別イシューで検討）
- 設定ファイル駆動（JSON）により一元管理性を得る一方、動的 UI の複雑性が増す

特に慎重に扱う領域:
- gulpfile.js の Sass 設定（dart-sass 前提、gulp-sass@5 系）
- package.json の Sass 系依存（sass / gulp-sass）
- Angular 1.x のモジュール構造（assets/js/src/modules/, services/）
- .husky/ フック設定

よくある誤りの回避:
- node-sass への巻き戻しや互換のない Gulp プラグイン導入は避ける
- 大きな変更は小さな PR に分割し、lint/test を通す

---

## 既知の問題と回避策

Node.js 互換性（解決済み: node-sass → Dart Sass）:
- 置き換え: sass@1.69.0 + gulp-sass@5.1.0
- gulpfile.js は新 API に対応済み
- 通常の npm install で動作

依存の非推奨警告:
- Angular 1.x や一部 Gulp プラグインは非推奨だが、本プロジェクトでは安定運用中

開発時の注意:
- gulpfile.js / package.json（Sass 回り）/ Angular 構造 / .husky 設定の変更は慎重に

---

## リリース

1. PR を master ブランチにマージ
2. GitHub Actions が自動実行:
   - コード品質チェック
   - アセットビルド
   - readme.txt 生成（README.md から）
   - バージョン更新
3. タグ作成でリリース用 ZIP 生成
4. 必要に応じ WordPress.org へ手動デプロイ

---

## トラブルシューティング / デバッグ

よくある問題:
- npm start でエラー → Node バージョン確認 / `npm install --ignore-scripts --legacy-peer-deps`
- Sass コンパイルエラー → node_modules 再生成（再インストール）
- PHP オートローディング → `composer install` で vendor 再生成
- husky が動かない → .husky 権限/初期化（`npx husky init`）

WP-CLI 例:
```bash
npm run cli -- log list
npm run cli -- plugin list
npm run cli -- db query "SELECT * FROM wp_options WHERE option_name LIKE 'tscf%'"
```

husky 設定の確認:
```bash
npx husky
```

---

## 参考資料

- WordPress Plugin Handbook: https://developer.wordpress.org/plugins/
- Coding Standards: https://developer.wordpress.org/coding-standards/
- Angular 1.x: https://docs.angularjs.org/api
- Gulp: https://gulpjs.com/docs/en/getting-started/quick-start
- Husky: https://typicode.github.io/husky/
- lint-staged: https://github.com/okonet/lint-staged
