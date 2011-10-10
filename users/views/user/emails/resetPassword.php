<?php
/**
 * An email message that contains a link to reset the user's password
 * @var AEmail $this The email being sent
 * @var AUser $user The user being emailed
 */

 $this->subject = "Please reset your password";
 ?>
<p>Hello<?php echo (isset($user->name) ? " ".$user->name : "")?>,</p>
<p>To reset your <?php echo Yii::app()->name; ?> password, please click the following link, or copy and paste it into your web browser:</p>
<p><?php
$url = Yii::app()->createAbsoluteUrl("/users/user/resetPassword",array("id" => $user->id,"key" => $user->passwordResetCode));
echo CHtml::link($url,$url);
?></p>
<p>If you received this message in error, please disregard it.</p>
<p>Thanks,<br />The <?php echo Yii::app()->name; ?> Team</p>

