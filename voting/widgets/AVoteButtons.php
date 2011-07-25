<?php
/**
 * Displays customisable vote buttons for voteable items.
 * @author Charles Pick
 * @package packages.voting.widgets
 */
class AVoteButtons extends CWidget {
	/**
	 * Holds the voteable model
	 * @var CActiveRecord
	 */
 	public $model;
	
	/**
	 * The label that should appear on the upvote button
	 * @var string
	 */
	public $upvoteLabel = "Upvote";
	
	/**
	 * The label that should appear on the downvote button
	 * @var string
	 */
	public $downvoteLabel = "Downvote";
	
	/**
	 * The label that should appear on the upvote button when the use has up voted.
	 * @var string
	 */
	public $upvotedLabel = "Upvoted";
	
	/**
	 * The label that should appear on the downvote button when the user has downvoted.
	 * @var string
	 */
	public $downvotedLabel = "Downvoted";
	
	/**
	 * The template to use when displaying the vote buttons.
	 * The following tokens are recognised:
	 * <li>{upvote} - The upvote button</li>
	 * <li>{downvote} - The downvote button</li>
	 * <li>{summary} - The summary tag</li>
	 * @var string
	 */
	public $template = "{upvote} {downvote} {summary}";
	/**
	 * The CSS class for the upvote button
	 * @var string
	 */
	public $upvoteClass = "upvote button";
	/**
	 * The CSS class for the downvote button
	 * @var string
	 */
	public $downvoteClass = "downvote button";
	
	/**
	 * The CSS class for the summary span
	 * @var string
	 */
	public $summaryClass = "score";
	
	/**
	 * The template for the summary tag.
	 * The following tokens are recognised:
	 * <li>{upvotes} - The number of upvotes for this item</li>
	 * <li>{downvotes} - The number of downvotes for this item</li>
	 * <li>{score} - The total number of votes for this item</li>
	 * @var string
	 */
	public $summaryTemplate = '<span class="score" title="{upvotes} liked it, {downvotes} didn\'t like it">{score}</span>';
	/**
	 * The htmlOptions for the container tag
	 * @var array
	 */
	public $htmlOptions = array();
	
	/**
	 * The options for the jQuery plugin
	 * @var array
	 */
	public $options = array();
	
	/**
	 * The tag name for the container.
	 * Defaults to "div".
	 * @var string
	 */
	public $tagName = "div";
	
	/**
	 * Displays the vote buttons and reviews summary.
	 */
	public function run() {
		$id = $this->getId();
		$htmlOptions = $this->htmlOptions;
		$htmlOptions['id'] = $id;
		echo CHtml::tag($this->tagName,$htmlOptions,strtr($this->template,array(
				"{summary}" => $this->getSummary(),
				"{upvote}" => $this->getUpvoteButton(),
				"{downvote}" => $this->getDownvoteButton(),
				)));
		$data = array();
		if (Yii::app()->request->enableCsrfValidation) {
			$data[Yii::app()->request->csrfTokenName] = Yii::app()->request->csrfToken;
		}
		$data= CJavaScript::encode($data);
		$options = array(
				"selectors" => array(
						"upvote" => "a.".implode(".",explode(" ",$this->upvoteClass)),
						"downvote" => "a.".implode(".",explode(" ",$this->downvoteClass)),
						"summary" => "span.".implode(".",explode(" ",$this->summaryClass))
					),
				"postData" => array(),
				"labels" => array(
						"upvote" => $this->upvoteLabel,
						"downvote" => $this->downvoteLabel,
						"upvoted" => $this->upvotedLabel,
						"downvoted" => $this->downvotedLabel,
						"summary" => $this->summaryTemplate
					)
			);
		if (Yii::app()->request->enableCsrfValidation) {
			$options['postData'][Yii::app()->request->csrfTokenName] = Yii::app()->request->csrfToken;
		}
		$options = CMap::mergeArray($options,$this->options);
		if (function_exists("json_encode")) {
			$options = json_encode($options);
		}
		else {
			$options = CJSON::encode($options);
		} 
		$script = "$('#$id').AVoteButtons($options);";
		$this->registerScripts();
		Yii::app()->clientScript->registerScript(__CLASS__."#".$id,$script);
	}
	/**
	 * Gets the upvote button for this votable model.
	 * @return string the HTML for the upvote button
	 */
	public function getUpvoteButton() {
		$htmlOptions = array("class" => $this->upvoteClass);
		$htmlOptions['id'] = $this->getId()."-upvote";
		$label = $this->upvoteLabel;
		if ($this->model->userHasVoted && $this->model->userVote->score == 1) {
			if (!isset($htmlOptions['class'])) {
				$htmlOptions['class'] = "";
			}
			$htmlOptions['class'] .= " active";
			$label = $this->upvotedLabel;
		}
		return CHtml::link($label, array("/voting/vote/up", "ownerModel" => $this->model->asa("votable")->getClassName(), "ownerId" => $this->model->asa("votable")->getId()),$htmlOptions);
		
	}

	/**
	 * Gets the downvote button for this votable model.
	 * @return string the HTML for the downvote button
	 */
	public function getDownvoteButton() {
		$htmlOptions = array("class" => $this->downvoteClass);
		$htmlOptions['id'] = $this->getId()."-downvote";
		$label = $this->downvoteLabel;
		if ($this->model->userHasVoted && $this->model->userVote->score == -1) {
			if (!isset($htmlOptions['class'])) {
				$htmlOptions['class'] = "";
			}
			$htmlOptions['class'] .= " active";
			$label = $this->downvotedLabel;
		}
		return CHtml::link($label, array("/voting/vote/down", "ownerModel" => $this->model->asa("votable")->getClassName(), "ownerId" => $this->model->asa("votable")->getId()),$htmlOptions);
	}
	/**
	 * Gets the review summary for this votable model.
	 * @return string the summary of votes for this model
	 */
	public function getSummary() {
		return strtr($this->summaryTemplate, array(
			"{score}" => $this->model->totalVoteScore + 1,
			"{upvotes}" => $this->model->totalUpvotes,
			"{downvotes}" => $this->model->totalDownvotes,
			
		));
		
	}
	
	/**
	 * Registers the required scripts
	 */
	public function registerScripts() {
		$baseUrl = Yii::app()->assetManager->publish(dirname(__FILE__)."/assets/".__CLASS__);
		Yii::app()->clientScript->registerScriptFile($baseUrl."/AVoteButtons.js");
		
	}
}
