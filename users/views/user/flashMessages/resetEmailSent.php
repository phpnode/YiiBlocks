<?php
/*
 * A flash message shown when a password reset email has been sent
 * @uses AUser $user the user who was emailed
 */
?>
<h3>Please check your email</h3>
<p>An email has been sent to <?php echo $user->email; ?> with a link to reset your password.</p>
<p>Click the link in the email to change your password.</p>