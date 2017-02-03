<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!doctype html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>データリセット</title>
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
                    <li><a href="/useddomaintools/result">精査結果</a></li>
                    <li class="active"><a href="/useddomaintools/resetdata">データリセット</a></li>


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
                    <li class="active"><a href="/useddomaintools/resetdata">データリセット <span class="sr-only">(*)</span></a></li>
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
                <h1 class="page-header">データリセット</h1>
                <div class="row">
                    <h2>全てのデータを削除</h2>
                    <p>データベースに登録されているデータを全て削除します。</p>
                    <?php echo form_open('resetdata/allclear', array('class'=>'form', 'role'=>'form'), array('mode'=>'allclear')); ?>
                    <div class="form-group">
                        <button type="submit" class="btn btn-danger" onClick="Javascript:return confirm('DBを空にします。よろしいでしょうか？');return false;">全てのドメインを削除</button>
                    </div>
                    <?php echo form_close(); ?>
                </div>
                <div class="row">
                    <h2>指定した日付以前のデータを削除</h2>
                    <p>指定された日付以前に追加されたドメインのみを削除します。</p>
                    <?php echo form_open('resetdata/deletedate', array('class'=>'form', 'role'=>'form'), array('mode'=>'deletedate')); ?>
                    <div class="form-group">
                        <div class="col-xs-3">
                            <label for="" class="control-label">ドメイン追加日</label>
                        </div>
                        <div class="col-xs-3">
                            <div class="input-group">
                                <input type="text" name="year" class="form-control text-right" placeholder="<?php echo date("Y"); ?>" required />
                                <span class="input-group-addon">年</span>
                            </div>
                        </div>
                        <div class="col-xs-3">
                            <div class="input-group">
                                <input type="text" name="month" class="form-control text-right" placeholder="<?php echo date("n"); ?>" required />
                                <span class="input-group-addon">月</span>
                            </div>
                        </div>
                        <div class="col-xs-3">
                            <div class="input-group">
                                <input type="text" name="day" class="form-control text-right" placeholder="<?php echo date("j"); ?>" required />
                                <span class="input-group-addon">日</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-danger" >指定した日付データを削除</button>
                    </div>
                    <?php echo form_close(); ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>