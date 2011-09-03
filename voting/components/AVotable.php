<?php
/**
 * Allows models to be voted up or down by users.
 * @author Charles Pick
 * @package application.modules.admin.components
 */
class AVotable extends CActiveRecordBehavior implements IAVotable {
	/**
	 * Whether the current user has voted for this item or not
	 * @var boolean
	 */
	protected $_userHasVoted;
	
	/**
	 * Contains the vote score if the current user has voted.
	 * Usually -1 or 1
	 * @var integer
	 */
	protected $_userVoteScore;
	
	/**
	 * Contains the user vote model
	 * @var AVote
	 */
	protected $_userVote;
	
	/**
	 * Attaches the behavior to the model
	 * @param CComponent $component The model to attach to
	 */
	public function attach($component) {
		parent::attach($component);
		
		$this->owner->metaData->addRelation("totalVotes",$this->totalVotesRelation());
		$this->owner->metaData->addRelation("totalUpvotes",$this->totalUpvotesRelation());
		$this->owner->metaData->addRelation("totalDownvotes",$this->totalDownvotesRelation());
		$this->owner->metaData->addRelation("totalVoteScore",$this->totalVoteScoreRelation());
		$this->owner->metaData->addRelation("userHasVoted",$this->userHasVotedRelation());
		$this->owner->metaData->addRelation("userVote",$this->userVoteRelation());
		$this->owner->metaData->addRelation("votes",$this->votesRelation());
	}
	
	/**
	 * Gets the id of the object being moderated.
	 * @return integer the id of the object being moderated.
	 */
	public function getId() {
		return $this->owner->primaryKey;
	}
	
	/**
	 * Gets the name of the class that is being moderated.
	 * @return string the owner model class name
	 */
	public function getClassName() {
		return get_class($this->owner);
	}
	
	
	
	/**
	 * Gets the data provider for this set of votes
	 * @return CActiveDataProvider The dataProvider that retrieves the votes
	 */
	public function getVoteDataProvider() {
		$dataProvider = new CActiveDataProvider(AVote::model()->ownedBy($this->owner));
		return $dataProvider;
	}
	
	/**
	 * Adds a vote to the owner model
	 * @param Vote $vote The vote to add
	 * @param boolean $runValidation Whether to run validation or not
	 * @return boolean Whether the save succeeded or not
	 */
	public function addVote(AVote $vote, $runValidation = true) {
		
		$vote->ownerModel = $this->getClassName();
		$vote->ownerId = $this->getId();
		return $vote->save($runValidation);
	}
	
	/**
	 * Upvotes the owner model.
	 * If {@link AVoteManager::requiresLogin} is set to true and
	 * the current user is not logged in, false will be return.
	 * @return boolean whether the save was successful or not
	 */
	public function upVote() {
		$attributes = array(
			"ownerModel" => $this->getClassName(),
			"ownerId" => $this->getId(),
		);
		if (Yii::app()->getModule('voting')->requiresLogin) {
			if (Yii::app()->user->isGuest) {
				return false;
			}
			$attributes['voterId'] = Yii::app()->user->id;
		}
		else {
			$attributes['voterIP'] = $_SERVER['REMOTE_ADDR'];
			$attributes['voterUserAgent'] = $_SERVER['HTTP_USER_AGENT'];
		}
		$model = AVote::model()->findByAttributes($attributes);
		if (!is_object($model)) { 
			$model = new AVote;
			foreach($attributes as $key => $value) {
				$model->{$key} = $value;
			}
		}		
		$model->score = 1;
		return $model->save();
	}
	
	/**
	 * Downvotes the owner model.
	 * If {@link AVoteManager::requiresLogin} is set to true and
	 * the current user is not logged in, false will be return.
	 * @return boolean whether the save was successful or not
	 */
	public function downVote() {
		$attributes = array(
			"ownerModel" => $this->getClassName(),
			"ownerId" => $this->getId(),
		);
		if (Yii::app()->getModule('voting')->requiresLogin) {
			if (Yii::app()->user->isGuest) {
				return false;
			}
			$attributes['voterId'] = Yii::app()->user->id;
		}
		else {
			$attributes['voterIP'] = $_SERVER['REMOTE_ADDR'];
			$attributes['voterUserAgent'] = $_SERVER['HTTP_USER_AGENT'];
		}
		$model = AVote::model()->findByAttributes($attributes);
		if (!is_object($model)) { 
			$model = new AVote;
			foreach($attributes as $key => $value) {
				$model->{$key} = $value;
			}
		}		
		$model->score = -1;
		return $model->save();
	}
	
	/**
	 * Resets the vote for the current user for the owner model.
	 * If {@link AVoteManager::requiresLogin} is set to true and
	 * the current user is not logged in, false will be return.
	 * @return boolean whether the delete was successful or not
	 */
	public function resetVote() {
		$attributes = array(
			"ownerModel" => $this->getClassName(),
			"ownerId" => $this->getId(),
		);
		if (Yii::app()->getModule('voting')->requiresLogin) {
			if (Yii::app()->user->isGuest) {
				return false;
			}
			$attributes['voterId'] = Yii::app()->user->id;
		}
		else {
			$attributes['voterIP'] = $_SERVER['REMOTE_ADDR'];
			$attributes['voterUserAgent'] = $_SERVER['HTTP_USER_AGENT'];
		}
		$model = AVote::model()->findByAttributes($attributes);
		if (is_object($model)) { 
			return $model->delete();
		}
		
	}
	
	/**
	 * Named Scope: Orders a list of models by the most highly voted first
	 * @return CActiveRecord The owner object with the scope applied
	 */
	public function mostPopular() {
		$voteTable = Yii::app()->getModule('voting')->voteTable;
		$id = $this->owner->tableSchema->primaryKey;
		$criteria = new CDbCriteria;
		$criteria->select = "t.*, SUM($voteTable.score) AS voteScore";
		$criteria->join = "LEFT JOIN $voteTable ON $voteTable.ownerModel = :voteOwnerModel AND $voteTable.ownerId = t.$id";
		$criteria->group = "t.".$id;
		$criteria->params = array(
				":voteOwnerModel" => $this->getClassName(),
			);
		$criteria->order = "voteScore DESC";
		$this->owner->getDbCriteria()->mergeWith($criteria);
		return $this->owner;
	}
	
	/**
	 * Named Scope: Orders a list of models by the least popular first
	 * @return CActiveRecord The owner object with the scope applied
	 */
	public function leastPopular() {
		$id = $this->owner->tableSchema->primaryKey;
		$voteTable = Yii::app()->getModule('voting')->voteTable;
		$criteria = new CDbCriteria;
		$criteria->select = "t.*, SUM($voteTable.score) AS voteScore";
		$criteria->join = "LEFT JOIN $voteTable ON $voteTable.ownerModel = :voteOwnerModel AND $voteTable.ownerId = t.$id";
		$criteria->group = "t.".$id;
		$criteria->params = array(
				":voteOwnerModel" => $this->getClassName(),
			);
		$criteria->order = "voteScore ASC";
		$this->owner->getDbCriteria()->mergeWith($criteria);
		return $this->owner;
	}
	/**
	 * Gets the configuration for a STAT relation with the total number of votes for this model.
	 * @return array the relation configuration
	 */
	public function totalVotesRelation() {
		return array(
				CActiveRecord::STAT,
				"AVote",
				"ownerId",
				"condition" => "ownerModel = :voteOwnerModel",
				"params" => array(
					":voteOwnerModel" => $this->getClassName()
				),
				"group" => false,
			);
	}
	
	/**
	 * Gets the configuration for a STAT relation with the total number of upvotes for this model.
	 * @return array the relation configuration
	 */
	public function totalUpvotesRelation() {
		return array(
				CActiveRecord::STAT,
				"AVote",
				"ownerId",
				"condition" => "ownerModel = :voteOwnerModel AND score = 1",
				
				"params" => array(
					":voteOwnerModel" => $this->getClassName()
				),
				"group" => false,
			);
	}
	
	/**
	 * Gets the configuration for a STAT relation with the total number of downvotes for this model.
	 * @return array the relation configuration
	 */
	public function totalDownvotesRelation() {
		return array(
				CActiveRecord::STAT,
				"AVote",
				"ownerId",
				"condition" => "ownerModel = :voteOwnerModel AND score = -1",
				
				"params" => array(
					":voteOwnerModel" => $this->getClassName()
				),
				"group" => false,
			);
	}
	
	/**
	 * Gets the configuration for a STAT relation with the total votes score for this model.
	 * @return array the relation configuration
	 */
	public function totalVoteScoreRelation() {
		return array(
				CActiveRecord::STAT,
				"AVote",
				"ownerId",
				"select" => "IFNULL(SUM(score), 0)",
				"condition" => "ownerModel = :voteOwnerModel",
				"params" => array(
					":voteOwnerModel" => $this->getClassName()
				),
				"group" => false,
			);
	}
	
	/**
	 * Gets the configuration for a STAT relation with the  number of votes for this model.
	 * @return array the relation configuration
	 */
	public function userHasVotedRelation() {
		$relation =  array(
				CActiveRecord::STAT,
				"AVote",
				"ownerId",
				"condition" => "ownerModel = :voteOwnerModel",
				"params" => array(
					":voteOwnerModel" => $this->getClassName()
				)
			);
		if (Yii::app()->getModule('voting')->requiresLogin) {
			$relation['condition'] .= " AND voterId = :voterId";
			$relation['params'][":voterId"] = Yii::app()->user->id;
		}
		else {
			$relation['condition'] .= " AND voterIP = :voterIP AND voterUserAgent = :voterUserAgent";
			$relation['params'][":voterIP"] = $_SERVER['REMOTE_ADDR'];
			$relation['params'][":voterUserAgent"] = $_SERVER['HTTP_USER_AGENT'];
		}
		return $relation;
	}
	
	/**
	 * Gets the configuration for a HAS_ONE relation which returns the vote for this item by the current user
	 * @return array the relation configuration
	 */
	public function userVoteRelation() {
		$relation =  array(
				CActiveRecord::HAS_ONE,
				"AVote",
				"ownerId",
				"condition" => "userVote.ownerModel = :voteOwnerModel",
				"params" => array(
					":voteOwnerModel" => $this->getClassName()
				)
			);
		if (Yii::app()->getModule('voting')->requiresLogin) {
			$relation['condition'] .= " AND voterId = :voterId";
			$relation['params'][":voterId"] = Yii::app()->user->id;
		}
		else {
			$relation['condition'] .= " AND voterIP = :voterIP AND voterUserAgent = :voterUserAgent";
			$relation['params'][":voterIP"] = $_SERVER['REMOTE_ADDR'];
			$relation['params'][":voterUserAgent"] = $_SERVER['HTTP_USER_AGENT'];
		}
		return $relation;
	}
	
	/**
	 * Gets the configuration for a HAS_MANY relation which returns the votes for this item
	 * @return array the relation configuration
	 */
	public function votesRelation() {
		$relation =  array(
				CActiveRecord::HAS_MANY,
				"AVote",
				"ownerId",
				"condition" => "votes.ownerModel = :voteOwnerModel",
				"params" => array(
					":voteOwnerModel" => $this->getClassName()
				)
			);
		return $relation;
	}
	
	
}
