<?php
/**
 * The main layout for the documentation
 * @uses string $content the content to show
 */
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

  <link rel="stylesheet" href="assets/css/style.css">

  <script src="assets/js/modernizr-1.7.min.js"></script>
  <script src="assets/js/jquery-1.5.1.min.js"></script>
  
</head>

<body>

    <header>
    YourApp Documentation
    </header>
    
	<div id="main" role="main">
 	<nav id='sidebar'>
 		<input id='searchBox' type='text' name='searchbox' placeholder='Search' />
 		<ul id='searchResults'>
 			
 		</ul>
 	</nav>
	<?php echo $content; ?>
    </div>
    <footer>
    <div class='footer'>
  
	<p>Copyright &copy; <?php echo date('Y'); ?> by webappier.com<br/>
	All Rights Reserved.<br/>
	</p>
	<?php echo Yii::powered(); ?>
	</div>
    </footer>





  <!--[if lt IE 7 ]>
    <script src="assets/js/dd_belatedpng.js"></script>
    <script>DD_belatedPNG.fix("img, .png_bg"); // Fix any <img> or .png_bg bg-images. Also, please read goo.gl/mZiyb </script>
  <![endif]-->

 
</div>
<script src="assets/js/docSearch.js"></script>
  <script src="data.js"></script>
</body>
</html>
