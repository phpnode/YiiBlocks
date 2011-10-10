<?php
/**
 * Shows the user's account page
 * @var AUser $model The user model
 */
$this->layout = "//layouts/column2";
$this->menu = array(
	array(
		"label" => "Test",
		"url" => array("/site/index")
	),
);
?>
<article class='user'>

	<?php
	$this->widget("packages.users.portlets.AUserImagePortlet",array(
		"user" => $model,
	));
	$this->beginWidget("packages.users.portlets.AUserDetailsPortlet",array(
		"user" => $model
	));
	?>
	<h1>Your Account</h1>
	<p>Your account details:</p>
	<?php
	$this->endWidget();
	?>
</article>