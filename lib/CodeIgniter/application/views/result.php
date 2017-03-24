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
                        <table class="table table-striped">
                            <tbody>
                                <tr>
                                    <th>登録されているドメイン数</th>
                                    <td class="text-right"><?php if($countAllDomains > 0): ?>
                                    <?php echo number_format($countAllDomains); ?>
                                    <?php else: ?>
                                    <?php echo number_format($countAllDomains); ?>
                                    <?php endif; ?></td>
                                </tr>
                                <tr>
                                    <th>処理待ちのドメイン数</th>
                                    <td class="text-right"><?php if($countPendings > 0): ?>
                                    <?php echo number_format($countPendings); ?>
                                    <?php else: ?>
                                    <?php echo number_format($countPendings); ?>
                                    <?php endif; ?></td>
                                </tr>
                                <tr>
                                    <th>HTTPステータスチェック数</th>
                                    <td class="text-right"><?php if($countHttpChecked > 0): ?>
                                    <?php echo number_format($countHttpChecked); ?>
                                    <?php else: ?>
                                    <?php echo number_format($countHttpChecked); ?>
                                    <?php endif; ?></td>
                                </tr>
                                <tr>
                                    <th>SEOmoz Site Metrics チェック数</th>
                                    <td class="text-right"><?php if($countSiteMetChecked > 0): ?>
                                    <?php echo number_format($countSiteMetChecked); ?>
                                    <?php else: ?>
                                    <?php echo number_format($countSiteMetChecked); ?>
                                    <?php endif; ?></td>
                                </tr>
                                <tr>
                                    <th>SEOmoz Link Metrics チェック数</th>
                                    <td class="text-right"><?php if($countLinkMetChecked > 0): ?>
                                    <?php echo number_format($countLinkMetChecked); ?>
                                    <?php else: ?>
                                    <?php echo number_format($countLinkMetChecked); ?>
                                    <?php endif; ?></td>
                                </tr>
                                <tr class="hidden">
                                    <th>被リンクチェック数</th>
                                    <td class="text-right"><?php if($countLinkChecked > 0): ?>
                                    <?php echo number_format($countLinkChecked); ?>
                                    <?php else: ?>
                                    <?php echo number_format($countLinkChecked); ?>
                                    <?php endif; ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-4">
                        &nbsp;
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <h2>チェック済みドメインリスト（検索）</h2>
                        <?php echo form_open('result/cookieset', array('class'=>'form-inline', 'role'=>'form')); ?>
                            <table class="listTbl">
                                <tbody>
                                    <tr>
                                        <th><label>ドメイン追加日</label></th>
                                        <td>
                                            <?php
                                                // 年
                                                $options = array();
                                                $selected = NULL;
                                                for($startYear = 2010; $startYear <= date("Y"); $startYear++)
                                                {
                                                    $options[$startYear] = $startYear."年";
                                                    if(get_cookie('startYear') == $startYear)
                                                    {
                                                        $selected = $startYear;
                                                    }
                                                }
                                                echo form_dropdown('startYear', $options, $selected);
                                            ?>
                                            <?php
                                                // 月
                                                $options = array();
                                                $selected = NULL;
                                                for($startMonth = 1; $startMonth <= 12; $startMonth++)
                                                {
                                                    $options[$startMonth] = $startMonth."月";
                                                    if(get_cookie('startMonth') == $startMonth)
                                                    {
                                                        $selected = $startMonth;
                                                    }
                                                }
                                                echo form_dropdown('startMonth', $options, $selected);
                                            ?>
                                            <?php
                                                // 日
                                                $options = array();
                                                $selected = NULL;
                                                for($startDate = 1; $startDate <= 31; $startDate++)
                                                {
                                                    $options[$startDate] = $startDate."日";
                                                    if(get_cookie('startDate') == $startDate)
                                                    {
                                                        $selected = $startDate;
                                                    }
                                                }
                                                echo form_dropdown('startDate', $options, $selected);
                                            ?>
                                            から
                                            <?php
                                                // 年
                                                $options = array();
                                                $selected = NULL;
                                                for($endYear = 2010; $endYear <= date("Y"); $endYear++)
                                                {
                                                    $options[$endYear] = $endYear."年";
                                                    if(get_cookie('endYear') == $endYear)
                                                    {
                                                        $selected = $endYear;
                                                    }
                                                }
                                                echo form_dropdown('endYear', $options, $selected);
                                            ?>
                                            <?php
                                                // 月
                                                $options = array();
                                                $selected = NULL;
                                                for($endMonth = 1; $endMonth <= 12; $endMonth++)
                                                {
                                                    $options[$endMonth] = $endMonth."月";
                                                    if(get_cookie('endMonth') == $endMonth)
                                                    {
                                                        $selected = $endMonth;
                                                    }
                                                }
                                                echo form_dropdown('endMonth', $options, $selected);
                                            ?>
                                            <?php
                                                // 日
                                                $options = array();
                                                $selected = NULL;
                                                for($endDate = 1; $endDate <= 31; $endDate++)
                                                {
                                                    $options[$endDate] = $endDate."日";
                                                    if(get_cookie('endDate') == $endDate)
                                                    {
                                                        $selected = $endDate;
                                                    }
                                                }
                                                echo form_dropdown('endDate', $options, $selected);
                                            ?>
                                            まで
                                        </td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <th><label>Page Authority (upa)</label></th>
                                        <td><input type="number" name="pageAuthorityMin" value="<?php echo get_cookie('pageAuthorityMin'); ?>" size="4" style="width:12em;" class="form-control text-right">以上
                                            <input type="number" name="pageAuthorityMax" value="<?php echo get_cookie('pageAuthorityMax'); ?>" size="4" style="width:12em;" class="form-control text-right">以下</td>
                                        <td><input type="checkbox" name="pageAuthorityCheck" value="on" <?php if(get_cookie('pageAuthorityCheck') == TRUE): ?>checked="checked"<?php endif; ?>></td>
                                    </tr>
                                    <tr>
                                        <th><label>Domain Authority (pda)</label></th>
                                        <td><input type="number" name="domainAuthorityMin" value="<?php echo get_cookie('domainAuthorityMin'); ?>" size="4" style="width:12em;" class="form-control text-right">以上
                                            <input type="number" name="domainAuthorityMax" value="<?php echo get_cookie('domainAuthorityMax'); ?>" size="4" style="width:12em;" class="form-control text-right">以下</td>
                                        <td><input type="checkbox" name="domainAuthorityCheck" value="on" <?php if(get_cookie('domainAuthorityCheck') == TRUE): ?>checked="checked"<?php endif; ?>></td>
                                    </tr>
                                    <tr>
                                        <th><label>Total Links (uid)</label></th>
                                        <td><input type="number" name="totalLinksMin" value="<?php echo get_cookie('totalLinksMin'); ?>" size="4" style="width:12em;" class="form-control text-right">以上
                                            <input type="number" name="totalLinksMax" value="<?php echo get_cookie('totalLinksMax'); ?>" size="4" style="width:12em;" class="form-control text-right">以下</td>
                                        <td><input type="checkbox" name="totalLinksCheck" value="on" <?php if(get_cookie('totalLinksCheck') == TRUE): ?>checked="checked"<?php endif; ?>></td>
                                    </tr>
                                    <tr>
                                        <th><label>Root Domains Linking (uipl)</label></th>
                                        <td><input type="number" name="linkRootDomainMin" value="<?php echo get_cookie('linkRootDomainMin'); ?>" size="4" style="width:12em;" class="form-control text-right">以上
                                            <input type="number" name="linkRootDomainMax" value="<?php echo get_cookie('linkRootDomainMax'); ?>" size="4" style="width:12em;" class="form-control text-right">以下</td>
                                        <td><input type="checkbox" name="linkRootDomainCheck" value="on" <?php if(get_cookie('linkRootDomainCheck') == TRUE): ?>checked="checked"<?php endif; ?>></td>
                                    </tr>
                                    <tr>
                                        <th><label>SEOmoz Link Met.</label></th>
                                        <td><input type="number" name="seomozLinkMin" value="<?php echo get_cookie('seomozLinkMin'); ?>" size="4" style="width:12em;" class="form-control text-right">以上
                                            <input type="number" name="seomozLinkMax" value="<?php echo get_cookie('seomozLinkMax'); ?>" size="4" style="width:12em;" class="form-control text-right">以下</td>
                                        <td><input type="checkbox" name="seomozLinkCheck" value="on" <?php if(get_cookie('seomozLinkCheck') == TRUE): ?>checked="checked"<?php endif; ?>></td>
                                    </tr>
                                    <tr class="hidden">
                                        <th><label>被リンク数</label></th>
                                        <td><input type="number" name="ueidMin" value="<?php echo get_cookie('ueidMin'); ?>" size="4" style="width:12em;" class="form-control text-right">以上
                                            <input type="number" name="ueidMax" value="<?php echo get_cookie('ueidMax'); ?>" size="4" style="width:12em;" class="form-control text-right">以下</td>
                                        <td><input type="checkbox" name="ueidCheck" value="on" <?php if(get_cookie('ueidCheck') == TRUE): ?>checked="checked"<?php endif; ?>></td>
                                    </tr>
                                    <tr>
                                        <th><label>表示件数</label></th>
                                        <td><input type="number" name="displaymax" value="<?php echo get_cookie('displaymax'); ?>" size="4" style="width:12em;" class="form-control text-right">件表示</td>
                                        <td>&nbsp;</td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3">
                                            <button type="submit" name="mode" value="setcookie" class="btn btn-default">絞込み・リスト表示</button>
                                            <a href="useddomaintools/result/cookieclear">絞込みの解除</a></td>
                                    </tr>
                                </tfoot>
                            </table>
                        <?php echo form_close(); ?>
                    </div>
                    <div class="col-md-4">
                        <p>表示件数は、10,000件以上を指定するとパフォーマンスの低下、もしくは応答エラーを起こす可能性があります。</p>
                        <p>その他のパラメータで絞り込みを行ってください。</p>
                        <p>検索の対象に指定しない場合は、右のチェックボックスを外してください。</p>
                    </div>
                </div>
                <?php if( isset($searchResults) && ! empty($searchResults)): ?>
                <div class="row">
                    <table class="table table-striped table-responsive resultTbl">
                        <thead>
                            <tr>
                                <th>ID.</th>
                                <th>ﾄﾞﾒｲﾝ / URL</th>
                                <th>追加日</th>
                                <th>HTTP</th>
                                <th>Page Authority</th>
                                <th>Domain Authority</th>
                                <th>Total Links</th>
                                <th>Link Root Domain</th>
                                <th>SEOmoz Link Met.</th>
                                <th colspan="6">サムネイル 2017 / 2009 / 2002</th>
                            </tr>
                            <tr>
                                <td colspan="10">
                                    <b>検索結果数:<?php echo count($searchResults); ?></b>
                                    <?php if(count($searchResults) > 0): ?>
                                    <a href="useddomaintools/result/download">
                                        <span class="glyphicon glyphicon-download-alt"></span>
                                        検索結果をダウンロード( CSV )
                                    </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(count($searchResults) == 0): ?>
                            <tr>
                                <td colspan="10"><p>上のフォームから絞り込み条件を見直してみて下さい。</p></td>
                            </tr>
                            <?php else: ?>
                            <?php foreach($searchResults as $sr): ?>
                            <tr>
                                <td><?php echo $sr['domain_id']; ?></td>
                                <td>
                                    <?php echo $sr['domainname']; ?><br>
                                    <?php echo $sr['url']; ?>
                                </td>
                                <td><?php echo $sr['insertdatetime']; ?></td>
                                <td class="text-right">
                                    <?php if($sr['cs_http_code'] == 999): ?>0
                                    <?php elseif($sr['cs_http_code'] == 0): ?>&nbsp;
                                    <?php else: echo $sr['cs_http_code']; ?>
                                    <?php endif; ?>
                                </td>
                                <td class="text-right"><?php echo $sr['pageAuthority']; ?></td>
                                <td class="text-right"><?php echo $sr['domainAuthority']; ?></td>
                                <td class="text-right"><?php echo $sr['totalLinks']; ?></td>
                                <td class="text-right"><?php echo $sr['linkRootDomain']; ?></td>
                                <td class="text-right"><?php echo $sr['LostlinksMet']; ?> - <?php echo $sr['linksMet']; ?></td>
                                <td>
                                    <?php if($sr['url']): ?>
                                    <table class="table table-condensed">
                                        <tr>
                                            <td>
                                                <a href="http://web.archive.org/web/20170000000000/http://<?php echo $sr['url']; ?>">
                                                    <img src="http://capture.heartrails.com/400x300?http://web.archive.org/web/20170000000000/http://<?php echo $sr['url']; ?>" alt="" width="200" height="150" />
                                                </a>
                                            </td>
                                            <td>
                                                <a href="http://web.archive.org/web/20090000000000/http://<?php echo $sr['url']; ?>">
                                                    <img src="http://capture.heartrails.com/400x300?http://web.archive.org/web/20090000000000/http://<?php echo $sr['url']; ?>" alt="" width="200" height="150" />
                                                </a>
                                            </td>
                                            <td>
                                                <a href="http://web.archive.org/web/20020000000000/http://<?php echo $sr['url']; ?>">
                                                    <img src="http://capture.heartrails.com/400x300?http://web.archive.org/web/20020000000000/http://<?php echo $sr['url']; ?>" alt="" width="200" height="150" />
                                                </a>
                                            </td>
                                        </tr>
                                    </table>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
                <?php #echo print_r($_COOKIE, TRUE); ?>
                <?php #echo print_r($whereparam, TRUE); ?>
            </div>
        </div>
    </div>
</body>
</html>