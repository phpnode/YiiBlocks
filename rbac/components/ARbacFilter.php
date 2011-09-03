<?php
/**
 * Provides role based access control for controllers
 * @author Charles Pick
 * @package packages.rbac
 */
class ARbacFilter extends CFilter {

	/**
	 * Performs the pre action filtering
	 * @param CFilterChain $filterChain the filter chain that the filter is on
	 * @return boolean whether the filtering process should continue and the action should be executed
	 */
	protected function preFilter(CFilterChain $filterChain) {
		$route = $filterChain->controller->getRoute();
		$routeParts = explode("/",$route);
		array_pop($routeParts); // don't need the last one
		$criteria = new CDbCriteria;
		$last = "/";
		$matchOperations = array();
		foreach($routeParts as $part) {
			$part = $last.$part."/";
			$last = $part;
			$matchOperations[] = $last."*";
		}
		$matchOperations[] = "/".$route;
		$matchedCount = 0;
		$operations = Yii::app()->authManager->getOperations();
		foreach($matchOperations as $opName) {
			if (isset($operations[$opName])) {
				if (Yii::app()->user->checkAccess($opName)) {

					return true;
				}
				$matchedCount++;

			}
		}
		if ($matchedCount) {
			$this->accessDenied(Yii::app()->user,Yii::t('yii','You are not authorized to perform this action.'));
			return false;
		}
		else {
			return true;
		}
	}

	/**
	 * Denies access to the user.
	 * This method is invoked when the access check fails
	 * @throws CHttpException if no user is logged in
	 * @param IWebUser $user the web user
	 * @param string $message the message to display
	 */
	protected function accessDenied(IWebUser $user, $message) {
		if ($user->getIsGuest()) {
			$user->loginRequired();
		}
		else {
			throw new CHttpException(403,$message);
		}
	}
}