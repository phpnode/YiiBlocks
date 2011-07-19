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
	 * Named Scope: Return the vote information with the main query to
	 * save additional vote lookup queries later.
	 * To use this the owner model have the required public properties:
	 * $_userHasVoted,
	 * $_userVoteScore,
	 * $_totalVotes and
	 * $_totalVoteScore
	 * @return CActiveRecord The owner object
	 */
	public function withVoteInfo() {
		if (
			!property_exists($this->owner,"_userHasVoted") ||
			!property_exists($this->owner,"_userVoteScore") ||
			!property_exists($this->owner,"_totalVotes") || 
			!property_exists($this->owner,"_totalVoteScore") 
			) {
			throw new CException(get_class($this->owner)." does not have the required public properties, \$_userHasVoted, \$_userVoteScore, \$_totalVotes and \$_totalVoteScore");
		}
		
		$voteTable = Yii::app()->getModule('voting')->voteTable;
		$id = $this->owner->tableSchema->primaryKey;
		$criteria = new CDbCriteria;
		$criteria->join = " LEFT JOIN $voteTable ON $voteTable.ownerModel = :voteOwnerModel AND $voteTable.ownerId = t.$id";
		$criteria->select = "t.*,
							IF($voteTable.id IS NULL, 0, 1) AS _userHasVoted,
							IF($voteTable.score IS NULL, 0, $voteTable.score) AS _userVoteScore,
							(SELECT COUNT($voteTable.id) + 1 FROM $voteTable WHERE $voteTable.ownerModel = :voteOwnerModel AND $voteTable.ownerId = t.id) as _totalVotes,
							IFNULL((SELECT SUM($voteTable.score) FROM $voteTable WHERE $voteTable.ownerModel = :voteOwnerModel AND $voteTable.ownerId = t.id), 0) + 1 as _totalVoteScore
							";
		
		
		if (Yii::app()->getModule('voting')->requiresLogin) {
			
			$criteria->join .= " AND $voteTable.voterId = :voterId";
			$criteria->params[":voterId"] = Yii::app()->user->id;
		}
		else {
			$criteria->join .= " AND $voteTable.voterIP = :voterIP AND $voteTable.voterUserAgent = :voterUserAgent";
			$criteria->params[":voterIP"] = $_SERVER['REMOTE_ADDR'];
			$criteria->params[":voterUserAgent"] = $_SERVER['HTTP_USER_AGENT'];
		}
		$criteria->params[":voteOwnerModel"] = get_class($this->owner);
		
		$this->owner->getDbCriteria()->mergeWith($criteria);
		
		return $this->owner;
	}
	
	/**
	 * Determine whether the current user has voted for this item or not. 
	 * @return boolean whether the current use has voted or not
	 */
	public function getUserHasVoted() {
		if (Yii::app()->getModule('voting')->requiresLogin && Yii::app()->user->isGuest) {
			return false;
		}
		if (property_exists($this->owner,"_userHasVoted")) {
			$object =& $this->owner;
		}
		else {
			$object =& $this;
		}
		if ($object->_userHasVoted === null) {
			$criteria = new CDbCriteria;
			if (Yii::app()->getModule('voting')->requiresLogin) {
				$criteria->addCondition("voterId = :voterId");
				$criteria->params[":voterId"] = Yii::app()->user->id;
			}
			else {
				$criteria->addCondition("voterIP = :voterIP AND voterUserAgent = :voterUserAgent");
				$criteria->params[":voterIP"] = $_SERVER['REMOTE_ADDR'];
				$criteria->params[":voterUserAgent"] = $_SERVER['HTTP_USER_AGENT'];
			}
			$vote = AVote::model()->ownedBy($this->owner)->find($criteria);
			if (is_object($vote)) {
				$object->_userHasVoted = true;
				if ($object->_userVoteScore === null) {
					$object->_userVoteScore = $vote->score;
				}
			}
			else {
				$object->_userHasVoted = false;
			}
			
		}
		return $object->_userHasVoted;
	}
	/**
	 * Sets whether the user have voted or not.
	 * This is mainly used internally, it does not modify anything permanently.
	 * @param boolean $value Whether the user has voted or not
	 */
	public function setUserHasVoted($value) {
		if (property_exists($this->owner,"_userHasVoted")) {
			$object =& $this->owner;
		}
		else {
			$object =& $this;
		}
		$object->_userHasVoted = (bool) $value;
	}
	
	/**
	 * Get the score given by the user for this item if they have voted.
	 * If they have not voted, returns false
	 * @return mixed integer if the user has voted, otherwise false.
	 */
	public function getUserVoteScore() {
		if (Yii::app()->getModule('voting')->requiresLogin && Yii::app()->user->isGuest) {
			return false;
		}
		if (property_exists($this->owner,"_userHasVoted")) {
			$object =& $this->owner;
		}
		else {
			$object =& $this;
		}
		if ($object->_userVoteScore === null) {
			$criteria = new CDbCriteria;
			if (Yii::app()->getModule('voting')->requiresLogin) {
				$criteria->addCondition("voterId = :voterId");
				$criteria->params[":voterId"] = Yii::app()->user->id;
			}
			else {
				$criteria->addCondition("voterIP = :voterIP AND voterUserAgent = :voterUserAgent");
				$criteria->params[":voterIP"] = $_SERVER['REMOTE_ADDR'];
				$criteria->params[":voterUserAgent"] = $_SERVER['HTTP_USER_AGENT'];
			}
			$vote = AVote::model()->ownedBy($this->owner)->find($criteria);
			if (is_object($vote)) {
				$object->_userVoteScore = $vote->score;
				if ($object->_userHasVoted === null) {
					$object->_userHasVoted = true;
				}
			}
			else {
				$object->_userVoteScore = false;
				if ($object->_userHasVoted === null) {
					$object->_userHasVoted = false;
				}
			}
			
		}
		return $object->_userVoteScore;
	}
	
	
	/**
	 * Sets the user vote score
	 * This is mainly used internally, it does not modify anything permanently.
	 * @param integer $value The score given by the user
	 */
	public function setUserVoteScore($value) {
		if (property_exists($this->owner,"_userHasVoted")) {
			$object =& $this->owner;
		}
		else {
			$object =& $this;
		}
		$object->_userVoteScore = $value;
	}
	
	/**
	 * Gets the total score for this votable item.
	 * @return integer The score
	 */
	public function getTotalVoteScore() {
		if (property_exists($this->owner,"_totalVoteScore")) {
			$object =& $this->owner;
		}
		else {
			$object =& $this;
		}
		if ($object->_totalVoteScore === null) {
			$voteTable = Yii::app()->getModule('voting')->voteTable;
			$id = $this->owner->tableSchema->primaryKey;
			$sql = "SELECT COUNT($voteTable.id) AS _totalVotes, IFNULL(SUM($voteTable.score), 0) + 1  as _totalVoteScore FROM $voteTable WHERE $voteTable.ownerModel = :voteOwnerModel AND $voteTable.ownerId = :voteOwnerId";
			$cmd = Yii::app()->db->createCommand($sql);
			$cmd->bindValues(array(
				":voteOwnerModel" => $this->getClassName(),
				":voteOwnerId" => $this->getId(),
			));
			$row = $cmd->queryRow();
			$object->_totalVoteScore = $row['_totalVoteScore'];
			$object->_totalVotes = $row['_totalVotes'];
		}	
		return $object->_totalVoteScore;	
	}
	
	
	/**
	 * Gets the total number of votes for this item
	 * @return integer The total number of votes for this item
	 */
	public function getTotalVotes() {
		if (property_exists($this->owner,"_totalVoteScore")) {
			$object =& $this->owner;
		}
		else {
			$object =& $this;
		}
		if ($object->_totalVotes === null) {
			$voteTable = Yii::app()->getModule('voting')->voteTable;
			$id = $this->owner->tableSchema->primaryKey;
			$sql = "SELECT COUNT($voteTable.id) AS _totalVotes, IFNULL(SUM($voteTable.score), 0) + 1  as _totalVoteScore FROM $voteTable WHERE $voteTable.ownerModel = :voteOwnerModel AND $voteTable.ownerId = :voteOwnerId";
			$cmd = Yii::app()->db->createCommand($sql);
			$cmd->bindValues(array(
				":voteOwnerModel" => $this->getClassName(),
				":voteOwnerId" => $this->getId(),
			));
			$row = $cmd->queryRow();
			$object->_totalVoteScore = $row['_totalVoteScore'];
			$object->_totalVotes = $row['_totalVotes'];
		}	
		return $object->_totalVotes;	
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
}
