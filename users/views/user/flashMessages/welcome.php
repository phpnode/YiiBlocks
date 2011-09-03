<?php
/*
 * A welcome flash message for users who've just registered
 * @uses AUser $user the user who registered
 */
?>
<h3>Thanks for joining</h3>
<p>Welcome to <?php echo Yii::app()->name; ?>.</p>
<?php
if (Yii::app()->getModule("users")->requireActivation) {
	?>
	<p>An email has been sent to <?php echo $user->email; ?> with a link to activate your account.</p>
	<p>Please click the link in the email to activate your account</p>
	<?php
}

