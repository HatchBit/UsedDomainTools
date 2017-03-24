<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!doctype html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>精査結果</title>
    <base href="http://54.204.4.15/useddomaintools">
    <script src="/useddomaintools/lib/jQuery/jquery-3.1.1.min.js"></script>
    <script src="/useddomaintools/lib/bootstrap-3.3.7/js/bootstrap.min.js"></script>
    <link href="/useddomaintools/lib/bootstrap-3.3.7/css/bootstrap.min.css" rel="stylesheet">
    <link href="/useddomaintools/lib/bootstrap-3.3.7/css/bootstrap-theme.min.css" rel="stylesheet">
    <link href="/useddomaintools/css/dashboard.css" rel="stylesheet">
    <style type="text/css">
        .resultTbl td,
        .resultTbl th
        {
            white-space: nowrap !important;
        }
    </style>
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
                            <li><a href="/useddomaintools/server/upload">登録フォーム</a></li>
                            <li><a href="/useddomaintools/server/deleted">削除済みリスト</a></li>
                        </ul>
                    </li>
                    <li class="active"><a href="/useddomaintools/result">精査結果</a></li>
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
                    <li class="active"><a href="/useddomaintools/result">精査結果 <span class="sr-only">(*)</span></a></li>
                </ul>
                <ul class="nav nav-sidebar">
                </ul>
            </div>
            <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
                <?php if( ! empty($msg)): ?>
                <?php foreach($msg as $m): ?>
                <div class="alert alert-<?php echo $m['class']; ?>" role="alert"><?php echo $m['text']; ?></div>
                <?php endforeach; ?>
                <?php endif; ?>
                <h1 class="page-header">精査結果</h1>

                <div class="row">
                    <div class="col-md-8">
                        <h2>チェック済みドメインリスト（検索）</h2>
                    </div>
                    <div class="col-md-4">
                    </div>
                </div>
                <?php if( isset($results) && ! empty($results)): ?>
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>domainName</th>
                                    <th>pageTitle</th>
                                    <th>pageURL</th>
                                    <th>Page Authority</th>
                                    <th>Domain Authority</th>
                                    <th>HTTP CODE</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($results as $item): ?>
                                <tr>
                                    <td><?php echo $item['id']; ?></td>
                                    <td><?php echo $item['domainName']; ?></td>
                                    <td><?php echo $item['pageTitle']; ?></td>
                                    <td><?php echo $item['pageURL']; ?></td>
                                    <td><?php echo $item['pageAuthority']; ?></td>
                                    <td><?php echo $item['domainAuthority']; ?></td>
                                    <td><?php echo $item['http_code']; ?></td>
                                </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>