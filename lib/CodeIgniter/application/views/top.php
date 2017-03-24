<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!doctype html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>管理トップページ</title>
    <base href="http://54.204.4.15/useddomaintools">
    <script src="/useddomaintools/lib/jQuery/jquery-3.1.1.min.js"></script>
    <script src="/useddomaintools/lib/bootstrap-3.3.7/js/bootstrap.min.js"></script>
    <link href="/useddomaintools/lib/bootstrap-3.3.7/css/bootstrap.min.css" rel="stylesheet">
    <link href="/useddomaintools/lib/bootstrap-3.3.7/css/bootstrap-theme.min.css" rel="stylesheet">
    <link href="/useddomaintools/css/dashboard.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-inverse navbar-fixed-top">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="/useddomaintools">中古ドメイン精査システム</a>
            </div>
            <div id="navbar" class="navbar-collapse collapse">
                <ul class="nav navbar-nav navbar-left">
                    <li class="active"><a href="/useddomaintools/">トップ</a></li>
                    <li><a href="/useddomaintools/insertdomain">ドメインDB登録</a></li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">アクセスID <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="/useddomaintools/accessid">リスト</a></li>
                            <li><a href="/useddomaintools/accessid/upload">登録フォーム</a></li>
                            <li><a href="/useddomaintools/accessid/deleted">削除済みリスト</a></li>
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">サーバー <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="/useddomaintools/server">リスト</a></li>
                            <li><a href="/useddomaintools/server/upload">登録フォーム</a></li>
                            <li><a href="/useddomaintools/server/deleted">削除済みリスト</a></li>
                        </ul>
                    </li>
                    <li><a href="/useddomaintools/result">精査結果</a></li>
                    <li><a href="/useddomaintools/resetdata">データリセット</a></li>


                </ul>
                <ul class="nav navbar-nav navbar-right">
                </ul>
            </div>
        </div>
    </nav>
    
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-3 col-md-2 sidebar">
                <ul class="nav nav-sidebar">
                    <li class="active"><a href="/useddomaintools/">トップ <span class="sr-only">(*)</span></a></li>
                </ul>
                <ul class="nav nav-sidebar">
                </ul>
            </div>
            <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
                <h1 class="page-header">管理トップ</h1>
                <div class="row">
                    <div class="col-md-12">
                        <h2>自動スケジュール</h2>
                        <ol>
                            <li style="text-decoration: line-through;">毎朝5時に pool.com から ZIPファイルをダウンロードします。（毎朝5時）</li>
                            <li style="text-decoration: line-through;">ダウンロードされた ZIPファイルは、10,000行ごとに分割します。</li>
                            <li style="text-decoration: line-through;">分割されたファイルを毎分1ファイルごとにデータベースに登録します。（毎分）</li>
                            <li>登録されたドメインの HTTPステータスをチェックします。（毎分）</li>
                            <li>HTTPステータス「100以上600未満」のドメインに対し、SEOmoz Site Metrics をチェックします。（7分おき）</li>
                            <li>「Domain Authority 18以上、ueid 5以上」のドメインに対し、SEOmoz link Metrics をチェックします。（7分おき）</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>