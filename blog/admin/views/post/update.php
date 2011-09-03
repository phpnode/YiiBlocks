<?php
/**
 * A view used to create new {@link ABlogPost} models
 * @uses ABlogPost $model The ABlogPost model to be inserted
 */

$this->breadcrumbs=array(
	"Blog" => array("/admin/blog/post/index"),
	"Posts" => array("/admin/blog/post/index"),
	"Edit Blog Post"
);
$this->beginWidget("AAdminPortlet",array(

									  "menuItems" => array(
										  array(
												"label" => "Posts",
												"url" => array("/admin/blog/post/index"),
											),
									),
									  "title" => "Edit Blog Post"
								   ));
$this->renderPartial("_form",array("model" => $model));
$this->endWidget();