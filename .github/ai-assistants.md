# AIアシスタントによる GitHub Actions 連携概要

このリポジトリでは、以下の2つのAIアシスタントを GitHub Actions から利用できます。

- [Gemini CLI](https://github.com/google-github-actions/run-gemini-cli)
- [Claude Code](https://github.com/anthropics/claude-code-action)

本ドキュメントは「どうやったら利用できるのか」の運用ガイドです。  

## 注意
- 2025年12月29日現在、Gemini CLIがうまく動いていません。
  - 環境情報を渡しても正しくそれらを受け取ってくれない問題が発生中で、自動レビューがうまく動かない状態です。 
  - 公式リポジトリのissue: https://github.com/google-github-actions/run-gemini-cli/issues/425
  - 対処方法はありますが、公式の対応を待っています。

---

## 1. トリガー

### 自動
#### 1. プルリクエストオープン時の自動レビュー

プルリクエストを作成すると、自動で Claude Code と Gemini CLI による自動レビューが実行されます。

実行結果はプルリクエストのコメントに投稿されます。

#### 2. Issue オープン/再オープン時の自動トリアージ

イシューを作成すると、Issue のタイトル・本文を元にした適切なラベルが Gemini CLI によって自動で付与されます。

ラベル候補は、常に「リポジトリに存在するラベル」にフィルタされます。

#### 3. 定期実行による未ラベル Issue の自動トリアージ

- 対象:
  - 「ラベルがひとつも付いていない」または
  - `status/needs-triage` ラベルが付いている Issue
- 処理:
  - 1時間ごとに、対象 Issue に一括でラベル付け

### 手動

#### 1. コメントから呼び出す

##### `@gemini-cli`、`@claude`

プルリクエストやイシューのコメントに `@gemini-cli` `@claude`を含めた文章を書くと、それぞれ Gemini CLI と Claude Code を呼び出すことができます。

`@gemini-cli` や `@claude` に続く文章が、それぞれ Gemini CLI と Claude Code のプロンプトとして渡されます。

###### 例
```
@gemini-cli 修正したので、もう一度レビューしてください。
```

```
@claude このプルリクエストの内容をジュニアエンジニアにわかりやすいように説明してください。
```

## 2. ガード（セキュリティ）

**外部コントリビュータはAIアシスタントを実行できません**。

- 実行できるのは、`author_association` が以下のいずれか
  - `OWNER`
  - `MEMBER`
  - `COLLABORATOR`

## 3. 実行結果の確認方法

すべての関連 Workflow は、対象の Issue/PR に対して 「受け付けた旨のコメント」や 「レビューコメント / ラベル付与」 を行うように実装されています。

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
- `GEMINI_API_KEY`
  - Gemini CLI 用
- `ANTHROPIC_API_KEY`
  - Claude Code 用

### Variables
- `GEMINI_DEBUG`
  - Gemini CLI 用
  - 値`true`でデバッグON。デフォルトは`false`
