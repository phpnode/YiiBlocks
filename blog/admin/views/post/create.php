<?php
/**
 * A view used to create new {@link ABlogPost} models
 * @var ABlogPost $model The ABlogPost model to be inserted
 */

$this->breadcrumbs=array(
	"Blog" => array("/admin/blog/post/index"),
	"Posts" => array("/admin/blog/post/index"),
	($model->isNewRecord ? "New Blog Post" : "Update Page")
);
$this->beginWidget("AAdminPortlet",array(

									  "menuItems" => array(
										  array(
												"label" => "Posts",
												"url" => array("/admin/blog/post/index"),
											),
									),
									  "title" => "New Blog Post"
								   ));
$this->renderPartial("_form",array("model" => $model));
$this->endWidget();