<?php
/**
 * Copyright (c) 2017. HatchBit & Co.
 */

defined('BASEPATH') OR exit('No direct script access allowed');
?><!doctype html>
<html lang="ja"
<head>
    <meta charset="UTF-8">
    <title>サーバー登録</title>
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
                    <li><a href="/useddomaintools/">トップ</a></li>
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
                            <li class="active"><a href="/useddomaintools/server/upload">登録フォーム</a></li>
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
                    <li><a href="/useddomaintools/server">リスト</a></li>
                    <li class="active"><a href="/useddomaintools/server/upload">登録フォーム</a></li>
                    <li><a href="/useddomaintools/server/deleted">削除済みリスト</a></li>
                </ul>
                <ul class="nav nav-sidebar">
                </ul>
            </div>
            <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
                <h1 class="page-header">サーバー登録</h1>
                <?php if (isset($msg)): ?>
                <?php foreach($msg as $ms): ?>
                <div class="alert alert-<?php echo $ms['kind']; ?>"><?php echo $ms['message']; ?></div>
                <?php endforeach; ?>
                <?php endif; ?>

                <?php if (isset($results)): ?>
                    <h2>アップロードされたサーバーの情報</h2>
                    <table class="table table-striped table-condensed">
                        <thead>
                        <tr>
                            <th>name</th>
                            <th>public_hostname</th>
                            <th>public_ipv4</th>
                            <th>private_hostname</th>
                            <th>private_ipv4</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($results as $item): ?>
                        <tr>
                            <td><?php echo $item['name']; ?></td>
                            <td><?php echo $item['public_hostname']; ?></td>
                            <td><?php echo $item['public_ipv4']; ?></td>
                            <td><?php echo $item['private_hostname']; ?></td>
                            <td><?php echo $item['private_ipv4']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>

                <?php echo form_open_multipart('server/upload');?>
                <input type="hidden" name="MAX_FILE_SIZE" value="20000000">
                <div class="form-group">
                    <label for="csvfile" class="control-label">CSVファイル</label>
                    <input type="file" name="csvfile" id="csvfile">
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-default">CSVファイルアップロード</button>
                </div>
                <?php echo form_close(); ?>
                
            </div>
        </div>
    </div>
</body>
</html>