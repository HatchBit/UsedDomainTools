<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!doctype html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ドメインDB登録</title>
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
                    <li class="active"><a href="/useddomaintools/insertdomain">ドメインDB登録</a></li>
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
                    <li><a href="/useddomaintools/insertdomain">ドメインDB登録 <span class="sr-only">(*)</span></a></li>
                </ul>
                <ul class="nav nav-sidebar">
                </ul>
            </div>
            <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
                <div class="row">
                    <h1 class="page-header">ドメインDB登録</h1>
                </div>
                <div class="row">
                    <h2 id="csv">CSVファイルアップロード</h2>
                    <?php if ($results): ?>
                    <p>ドメインを登録しました。</p>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Domain name</th>
                                <th>Expire date</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php $i = 0; ?>
                        <?php foreach($results as $r): ?>
                        <?php $i++; ?>
                            <tr>
                                <td><?php echo $i; ?></td>
                                <td><?php echo $r['domainname']; ?></td>
                                <td><?php echo $r['expiredatetime']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <p>登録できるデータがありませんでした。</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>