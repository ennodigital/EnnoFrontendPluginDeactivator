<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{link file="backend/_resources/css/bootstrap.min.css"}">
    <link rel="stylesheet" href="{link file="backend/_resources/summernote/summernote.css"}">

    <style>
        body{
            padding-top: 80px
        }
        .enno-table td{
            max-width: 150px;
            white-space: normal !important;
        }
        .enno-table .btn{
            margin-bottom: 5px;
        }
        .enno-help-contact{
            margin-top: 40px;
        }
        .help-block{
            font-style: italic;
        }
        .enno-development{
            display:none !important;
        }
    </style>
</head>
<body role="document">

<!-- Fixed navbar -->
<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a  class="navbar-brand" href="{url controller="EnnoFrontendPluginDeactivator" action="index" __csrf_token=$csrfToken}">Frontend Deactivator</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
                <li{if {controllerAction} === 'index'} class="active"{/if}><a href="{url controller="EnnoFrontendPluginDeactivator" action="index" __csrf_token=$csrfToken}">Übersicht</a></li>
                {* <li{if {controllerAction} === 'settings'} class="active"{/if}><a href="{url controller="EnnoTabs" action="settings" __csrf_token=$csrfToken}">Einstellungen</a></li> *}
                {* <li{if {controllerAction} === 'help'} class="active"{/if}><a href="{url controller="EnnoTabs" action="help" __csrf_token=$csrfToken}">Hilfe</a></li> *}
            </ul>
        </div><!--/.nav-collapse -->
    </div>
</nav>

<div class="container theme-showcase" role="main">
    {block name="content/main"}{/block}
</div> <!-- /container -->

<script type="text/javascript" src="{link file="backend/base/frame/postmessage-api.js"}"></script>
<script type="text/javascript" src="{link file="backend/_resources/js/jquery-2.1.4.min.js"}"></script>
<script type="text/javascript" src="{link file="backend/_resources/js/bootstrap.min.js"}"></script>
<script type="text/javascript" src="{link file="backend/_resources/summernote/summernote.min.js"}"></script>

{block name="content/layout/javascript"}
<script type="text/javascript">
</script>
{/block}
{block name="content/javascript"}{/block}
</body>
</html>
