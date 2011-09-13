<?php
	$baseUrl = Yii::app()->getModule("admin")->assetBaseUrl;
?><!doctype html>
<!--[if lt IE 7 ]> <html class="no-js ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]>    <html class="no-js ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]>    <html class="no-js ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
  <meta charset="utf-8">

  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

  <title><?php echo CHtml::encode($this->pageTitle); ?></title>
  <meta name="description" content="">
  <meta name="author" content="">

  <meta name="viewport" content="width=device-width, initial-scale=1.0">


  <!-- CSS: implied media="all" -->
  <link rel="stylesheet" href="<?php echo $baseUrl; ?>/css/style.css?v=1">

  <link href='http://fonts.googleapis.com/css?family=Droid+Sans:400,700' rel='stylesheet' type='text/css'>
  <!-- All JavaScript at the bottom, except for Modernizr which enables HTML5 elements & feature detects -->
  <script src="<?php echo $baseUrl; ?>/js/modernizr-1.7.min.js"></script>
	<?php
	$clientScript = Yii::app()->clientScript;
	$clientScript->registerCoreScript("jquery");
	$clientScript->registerCoreScript("jquery.ui");
	$clientScript->registerScriptFile($baseUrl."/js/jquery.cookie.js");
	$clientScript->registerScriptFile($baseUrl."/js/AAdminInterface.js");
	$clientScript->registerScript("runAdmin","AAdminInterface.run()");
	?>
</head>

<body>
	<div id="main" role="main" class='container_12'>
	<?php
		$this->widget("AFlashMessageWidget");
	?>
	<?php echo $content; ?>
    </div>
    <footer class='grid_12 alpha'>
	Copyright &copy; <?php echo date('Y'); ?> by My Company.<br/>
		All Rights Reserved.<br/>
		<?php echo Yii::powered(); ?>
    </footer>
  </div> <!--! end of #container -->

<?php
	$script = <<<JS
	$("article.collapsible header a.collapsible").click(function(e) {
		var widget = $(this).parents("article.collapsible").first();
		if ($(widget).hasClass("collapsed")) {
			$(widget).removeClass("collapsed", 500);
		}
		else {
			$(widget).addClass("collapsed", 500);
		}
		e.preventDefault();
	});

	$("a.toggle").click(function(e) {
		var target = $(this).parent().find(".hideable");

		if ($(target).hasClass("hidden")) {
			$(target).removeClass("hidden");
		}
		else {
			$(target).addClass("hidden");
		}
		e.preventDefault();
	});

JS;
Yii::app()->clientScript->registerScript("collapseButtons",$script);
?>

</body>
</html>