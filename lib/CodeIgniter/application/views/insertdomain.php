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
                    <li class="active"><a href="/useddomaintools/insertdomain">ドメインDB登録 <span class="sr-only">(*)</span></a></li>
                    <li><a data-toggle="collapse" data-parent="#accordion" href="/useddomaintools/insertdomain#collapseOne">CSVファイルアップロード</a></li>
                    <li><a data-toggle="collapse" data-parent="#accordion" href="/useddomaintools/insertdomain#collapseTwo">サーバー上のファイルをダウンロード</a></li>
                    <li><a data-toggle="collapse" data-parent="#accordion" href="/useddomaintools/insertdomain#collapseThree">pool.com からダウンロード</a></li>
                </ul>
                <ul class="nav nav-sidebar">
                </ul>
            </div>
            <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
                <div class="row">
                    <h1 class="page-header">ドメインDB登録</h1>
                    <?php if(isset($msg)): ?>
                    <?php foreach($msg as $ms): ?>
                    <div class="alert alert-<?php echo $ms['kind']; ?>"><?php echo $ms['message']; ?></div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="headingOne">
                            <h4 class="panel-title">
                                <a role="button" data-toggle="collapse" data-parent="#accordion" href="/useddomaintools/insertdomain#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                CSVファイルアップロード
                                </a>
                            </h4>
                        </div>
                        <div id="collapseOne" class="panel-collapse collapse <?php if($panel == 'csv'): ?>in<?php endif; ?>" role="tabpanel" aria-labelledby="headingOne">
                            <div class="panel-body">
                                <div class="col-md-6">
                                    <?php echo form_open_multipart('insertdomain/csv');?>
                                        <input type="hidden" name="MAX_FILE_SIZE" value="20000000">
                                        <div class="form-group">
                                            <label for="csvfile">CSVファイル</label>
                                            <input type="file" id="csvfile" name="csvfile">
                                            <p class="help-block">ドメインリストをCSVファイルでアップロードします。</p>
                                        </div>
                                        <div class="form-group">
                                            <div class="radio">
                                                <label>
                                                    <input type="radio" name="encode" id="encode" value="UTF-8" >
                                                    UTF-8
                                                </label>
                                            </div>
                                            <div class="radio">
                                                <label>
                                                    <input type="radio" name="encode" id="encode" value="SJIS-win" checked>
                                                    シフトJIS
                                                </label>
                                            </div>
                                            <p class="help-block">ファイルエンコードを指定してください。</p>
                                        </div>
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-default">CSVファイルアップロード</button>
                                        </div>
                                    <?php echo form_close(); ?>
                                </div>
                                <div class="col-md-6">
                                    <p>登録する CSV のフォーマットは、pool.com のファイルフォーマットに準じます。</p>
                                    <p>（ A列 = ドメイン名、B列 = 有効期限 ）</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="headingTwo">
                            <h4 class="panel-title">
                                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="/useddomaintools/insertdomain#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                サーバー上のファイルをダウンロード
                                </a>
                            </h4>
                        </div>
                        <div id="collapseTwo" class="panel-collapse collapse <?php if($panel == 'pool'): ?>in<?php endif; ?>" role="tabpanel" aria-labelledby="headingTwo">
                            <div class="panel-body">
                                <?php if(isset($zipfilename) OR isset($csvfilename)): ?>
                                <p>サーバーにダウンロード済みのファイルをダウンロードします。</p>
                                <?php else: ?>
                                <p>ファイルがありません。</p>
                                <?php endif; ?>
                                <?php if(isset($zipfilename) && $zipfilename <> ""): ?>
                                <p>
                                <a href="/useddomaintools/insertdomain/download/zip" class="btn btn-default">ZIPファイル</a>
                                <?php if($zipfiledate): ?><span><?php echo $zipfiledate; ?></span><?php endif; ?>
                                </p>
                                <?php endif; ?>
                                <?php if(isset($csvfilename) && $csvfilename <> ""): ?>
                                <p>
                                <a href="/useddomaintools/insertdomain/download/txt" class="btn btn-default">TEXTファイル</a>
                                <?php if($csvfiledate): ?><span><?php echo $csvfiledate; ?></span><?php endif; ?>
                                </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="headingThree">
                            <h4 class="panel-title">
                                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="/useddomaintools/insertdomain#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                pool.com からダウンロード
                                </a>
                            </h4>
                        </div>
                        <div id="collapseThree" class="panel-collapse collapse <?php if($panel == 'download'): ?>in<?php endif; ?>" role="tabpanel" aria-labelledby="headingThree">
                            <div class="panel-body">
                                <p>www.pool.com からZIPファイルをダウンロードします。</p>
                                <p><a href="/useddomaintools/insertdomain/pool/downloadzip" class="btn btn-default">ZIPファイル www.pool.com からダウンロード</a></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>