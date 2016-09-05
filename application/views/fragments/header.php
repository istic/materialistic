<!DOCTYPE html>
<html>
  <head>
    <title>[<?PHP echo APPNAME ?>] 
      <?PHP echo isset($title) && $title ? $title : '' ?>
      <?PHP echo isset($subtitle) && $subtitle ? '&mdash; '.$subtitle : '' ?>
    </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="utf-8" />


    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="//code.jquery.com/jquery.js"></script>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.2/css/bootstrap.min.css">
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/bootstrap.css" rel="stylesheet">
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.0.2/js/bootstrap.min.js"></script>

    <!-- Google JSApi -->
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>

    <!-- Tablecloth -->
    <link href="/assets/libraries/tablecloth/css/tablecloth.css" rel="stylesheet">
    <script src="/assets/libraries/tablecloth/js/jquery.tablecloth.js"></script>
    <script src="/assets/libraries/tablecloth/js/jquery.tablesorter.js"></script>

    <!-- Material.Istic.Net -->
    <link href="/assets/css/materialistic.css" rel="stylesheet">
    <script src="/assets/js/materialistic.js"></script>
 

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
    </head>
    <body>
    
    <?PHP echo $navigation ?>

    <div class="container">
