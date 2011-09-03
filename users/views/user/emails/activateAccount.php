<?php
/**
 * An email message that contains a link to activate the user's account
 * @uses AEmail $this The email being sent
 * @uses AUser $user The user being emailed
 */

 $this->subject = "Please activate your account";
 ?>
<p>Hello<?php echo (isset($user->name) ? " ".$user->name : ""); ?>,</p>
<p>To activate your <?php echo Yii::app()->name; ?> account, please click the following link, or copy and paste it into your web browser:</p>
<p><?php
$url = Yii::app()->createAbsoluteUrl("/users/user/activateAccount",array("id" => $user->id,"key" => $user->activationCode));
echo CHtml::link($url,$url);
?></p>

<p>Thanks,<br />The <?php echo Yii::app()->name; ?> Team</p>
 
