# AIアシスタントによる GitHub Actions 連携概要

このリポジトリでは、[Claude Code](https://github.com/anthropics/claude-code-action) を GitHub Actions から利用できます。

共通ワークフロー [`tarosky/workflows`](https://github.com/tarosky/workflows) を参照しており、他のリポジトリと同じ設定で動作します。

本ドキュメントは「どうやったら利用できるのか」の運用ガイドです。

---

## 1. トリガー

### 自動

#### プルリクエストの自動レビュー

`Test Plugin` ワークフローが成功すると、自動で Claude Code による PR レビューが実行されます。

実行結果はプルリクエストのコメントに投稿されます。

#### 週次 Issue トリアージ

毎週月曜 01:00 UTC に、ラベルなし・または未トリアージの Issue に対して自動でラベルが付与されます。

`workflow_dispatch` から手動実行することもできます。

### 手動

#### `@claude` をコメントで使用する

プルリクエストやイシューのコメントに `@claude` を含めた文章を書くと、Claude Code を呼び出すことができます。

`@claude` に続く文章が Claude Code のプロンプトとして渡されます。

##### 例

```
@claude このプルリクエストの内容をジュニアエンジニアにわかりやすいように説明してください。
```

#### `@claude auto-review` で手動レビュー

プルリクエストのコメントに `@claude auto-review` と書くと、手動で PR レビューを実行できます。

---

## 2. ガード（セキュリティ）

**外部コントリビュータはAIアシスタントを実行できません**。

- 実行できるのは、`author_association` が以下のいずれか
  - `OWNER`
  - `MEMBER`
  - `COLLABORATOR`

---

## 3. 実行結果の確認方法

すべての関連 Workflow は、対象の Issue/PR に対して「レビューコメント / ラベル付与」を行うように実装されています。

詳細なログは、各 Workflow の GitHub Actions 画面から参照できます。

### コメントが返ってこないとき

**各 Workflow の GitHub Actions を再実行してください。**

ときどき、以下のようなことが起きます。そのようなときは GitHub Actions を再実行すると直る可能性が高いので、試してみてください。
- AIアシスタントの処理の途中でエラーが起きて処理が止まる
- 処理が完了してもコメントがされない
- コメントをする許可を求めて処理が止まる

---

## 4. 環境設定

以下の環境変数を使用しています。

### Secrets
- `ANTHROPIC_API_KEY`
  - Claude Code 用（Organization Secret として設定済み）
