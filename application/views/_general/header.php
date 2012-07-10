<!DOCTYPE html>
<html>
	<head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
		<title></title>

        <?php /* embed fonts */ ?>
		<link href='//fonts.googleapis.com/css?family=Open+Sans:300' rel='stylesheet' type='text/css'/>

        <?php /* stylesheets */ ?>
        <link href="<?= _res('stylesheets/screen.css') ?>" media="screen, projection" rel="stylesheet" type="text/css" />
        <!--[if IE]>
        <link href="<?= _res('stylesheets/ie.css') ?>" media="screen, projection" rel="stylesheet" type="text/css" />
        <![endif]-->

        <?php /* grab google CDN's jQuery, with a protocol relative URL; fall back to local if offline */ ?>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="<?= _res('js/vendor/jquery-1.7.2.min.js') ?>"><\/script>')</script>

		<!--[if lt IE 9]>
		<script src="<?= _res('js/vendor/html5shiv') ?>"></script>
		<![endif]-->
	</head>
	<body>
