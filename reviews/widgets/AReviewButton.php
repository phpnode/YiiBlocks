<?php
/**
 * Displays a button to add reviews to a given reviewable model.
 * @author Charles Pick
 * @package packages.reviews.widgets
 */
 class AReviewButton extends CWidget {
 	/**
	 * Holds the reviewable model
	 * @var CActiveRecord
	 */
 	public $model;

	/**
	 * The label that should appear on the add review button
	 * @var string
	 */
	public $createLabel = "Add Review";

	/**
	 * The label that should appear on the update review
	 * @var string
	 */
	public $updateLabel = "Edit Review";

	/**
	 * The template to use when displaying the review button and summary.
	 * The following tokens are recognised:
	 * {summary} - The review summary
	 * {button} - The add / update review button
	 * @var string
	 */
	public $template = "{summary} {button}";
	/**
	 * The htmlOptions for the review button.
	 * @var array
	 */
	public $htmlOptions = array("class" => "review button");

	/**
	 * Displays the review button and reviews summary.
	 */
	public function run() {
		$id = $this->getId();
		echo strtr($this->template,array("{summary}" => $this->getSummary(), "{button}" => $this->getButton()));
		echo CHtml::tag("div",array("style" => "display:none;"),CHtml::tag("div",array("id" => $id."-dialog"),"&nbsp;"));
		$script = '$("#'.$id.'").bind("click", function(e){
			$.ajax({
				url: $(this).attr("href"),
				success: function(html) {
					$("#'.$id.'-dialog").html(html).dialog({
						width:700,
						height:600,
						modal: true
					});
				}
			})
			e.preventDefault();
		})';
		Yii::app()->clientScript->registerScript(__CLASS__."#".$id,$script);
	}
	/**
	 * Gets the review button for this reviewable model.
	 * @return string the HTML for the review button
	 */
	public function getButton() {
		$htmlOptions = $this->htmlOptions;
		$htmlOptions['id'] = $this->getId();
		if (is_object($this->model->userReview)) {
			return CHtml::link(Yii::t("packages.reviews", "Edit Review"), array("/reviews/review/update", "ownerModel" => $this->model->asa("AReviewable")->getClassName(), "ownerId" => $this->model->asa("AReviewable")->getId(), "id" => $this->model->userReview->id),$htmlOptions);
		}
		else {
			return CHtml::link(Yii::t("packages.reviews","Add Review"), array("/reviews/review/create", "ownerModel" => $this->model->asa("AReviewable")->getClassName(), "ownerId" => $this->model->asa("AReviewable")->getId()),$htmlOptions);
		}
	}
	/**
	 * Gets the review summary for this reviewable model.
	 * @return string the summary of reviews for this model
	 */
	public function getSummary() {
		$totalReviews = $this->model->totalReviews;
		if ($totalReviews) {
			$summary = Yii::t("packages.reviews","Rated {rating} out of 10 by {people}", array(
					"{rating}" => round($this->model->averageRating,2),
					"{people}" => Yii::t("packages.reviews","n==1#one person|n>1#{n} people",$totalReviews)));
		}
		else {
			$summary = Yii::t("packages.reviews", "No reviews yet");
		}
		return $summary;
	}
 }
