# 勤怠管理アプリ

本アプリケーションは、Laravelを用いて開発した勤怠管理システムです。

Laravel Fortifyによる認証機能を実装し、一般ユーザー向けの打刻・勤怠管理機能と、管理者向けの勤怠管理・申請承認機能を提供しています。

出勤・退勤・休憩打刻、勤怠修正申請、月次勤怠一覧、CSV出力、メール認証機能など、実務を想定した機能を実装しています。

---

## 主な機能

### 一般ユーザー

- 会員登録
- ログイン / ログアウト
- メール認証
- 出勤・退勤・休憩打刻
- 勤怠一覧確認
- 勤怠修正申請
- 申請一覧確認

### 管理者

- ログイン / ログアウト
- 日次勤怠一覧確認
- 勤怠修正
- スタッフ一覧確認
- スタッフ別月次勤怠一覧確認
- CSV出力
- 修正申請承認

---

## 環境構築

### リポジトリ取得

```bash
git clone git@github.com:tsumika0524/attendances.git
cd attendances
```

### Dockerコンテナ起動

```bash
docker-compose up -d --build
```

### Laravel初期設定

```bash
docker exec -it attendances bash

cd src

composer install
npm install

cp .env.example .env
php artisan key:generate
```

### データベース構築

```bash
php artisan migrate --seed
```

### フロントエンド環境構築

```bash
npm run dev
```

### 動作確認

ブラウザで以下にアクセスしてください。

```txt
http://localhost
```

以下の画面が表示されれば環境構築成功です。

- ログイン画面
- 勤怠管理画面

### DB設定

`.env` に以下を設定してください。

```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass
```

---

## メール認証

Mailhog を使用しています。

### Mailhog URL

```txt
http://localhost:8025
```

会員登録後の認証メールを確認できます。

---

## 開発環境URL

| 画面 | URL |
|------|-----|
| 会員登録画面（一般ユーザー） | http://localhost/register |
| ログイン画面（一般ユーザー） | http://localhost/login |
| 出勤登録画面（一般ユーザー） | http://localhost/attendance |
| 勤怠一覧画面（一般ユーザー） | http://localhost/attendance/list |
| 勤怠詳細画面（一般ユーザー） | http://localhost/attendance/detail/{id} |
| 申請一覧画面（一般ユーザー） | http://localhost/stamp_correction_request/list |
| ログイン画面（管理者） | http://localhost/admin/login |
| 勤怠一覧画面（管理者） | http://localhost/admin/attendance/list |
| 勤怠詳細画面（管理者） | http://localhost/admin/attendance/{id} |
| スタッフ一覧画面（管理者） | http://localhost/admin/staff/list |
| スタッフ別勤怠一覧画面（管理者） | http://localhost/admin/attendance/staff/{id} |
| 申請一覧画面（管理者） | http://localhost/admin/stamp_correction_request/list |
| 修正申請承認画面（管理者） | http://localhost/admin/stamp_correction_request/approve/{attendance_correct_request_id} |

---

## テスト

### 実装済み Feature テスト

- RegisterTest
- LoginTest
- ClockInTest
- ClockOutTest
- BreakTest
- AttendanceListTest
- AttendanceDetailTest
- AdminAttendanceListTest
- AdminAttendanceDetailTest
- AdminStampCorrectionTest

### テスト実行

```bash
php artisan test
```

---

## 使用技術

| 技術 | バージョン |
|------|-----------|
| PHP | 8.4.13 |
| Laravel | 8.83.29 |
| MySQL | 8.0.26 |
| nginx | 1.21.1 |
| jQuery | 3.7.1 |
| Docker | Docker Compose |
| 認証 | Laravel Fortify |
| メール認証 | Mailhog |
| テスト | PHPUnit |

---

## ER図

![ER図](src/docs/ER図.png)