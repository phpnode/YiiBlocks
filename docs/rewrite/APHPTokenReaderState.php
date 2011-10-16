<?php
/**
 * A base class for PHP token reader states
 * @author Charles Pick
 * @package packages.docs
 */
class APHPTokenReaderState extends AState {
	const DEFAULT_STATE = "Default";
	const ABSTRACT_CLASS_DECLARATION = "AbstractClassDeclaration";
	const FINAL_CLASS_DECLARATION = "FinalClassDeclaration";
	const CLASS_DECLARATION = "ClassDeclaration";
	const INTERFACE_DECLARATION = "InterfaceDeclaration";
	const IMPLEMENTS_DECLARATION = "ImplementsDeclaration";
	const EXTENDS_DECLARATION = "ExtendsDeclaration";
	const MEMBER_DECLARATION = "MemberDeclaration";
	const CONST_MEMBER_DECLARATION = "ConstMemberDeclaration";
	const PROPERTY_DECLARATION = "PropertyDeclaration";
	const METHOD_DECLARATION = "MethodDeclaration";
	const FUNCTION_DECLARATION = "FunctionDeclaration";
	const PARAMETER_DECLARATION = "ParameterDeclaration";
	const GLOBAL_DECLARATION = "GlobalDeclaration";
	const NAMESPACE_DECLARATION = "NamespaceDeclaration";
	const NAMESPACE_BODY = "NamespaceBody";
	const NAMESPACE_CURLY_BODY = "NamespaceCurlyBody";
	const CLASS_BODY = "ClassBody";
	const FUNCTION_BODY = "FunctionBody";
	const METHOD_BODY = "MethodBody";
	const VALUE_ASSIGNMENT = "ValueAssignment";
	/**
	 * Parses a given token
	 * @param array $token The token to parse
	 */
	public function parse($token) {
		$owner = $this->getOwner(); /* @var APHPTokenReader $owner */
		switch ($token[0]) {
			case T_NAMESPACE:
				$owner->transition(self::NAMESPACE_DECLARATION);
				break;
			case T_CLASS:
				$owner->transition(self::CLASS_DECLARATION);
				break;
			case T_ABSTRACT:
				$owner->transition(self::ABSTRACT_CLASS_DECLARATION);
				break;
			case T_FINAL:
				$owner->transition(self::FINAL_CLASS_DECLARATION);
				break;
			case T_INTERFACE:
				$owner->transition(self::INTERFACE_DECLARATION);
				break;
			case T_FUNCTION:
				$owner->transition(self::FUNCTION_DECLARATION);
				break;
			case T_GLOBAL:
				$owner->transition(self::GLOBAL_DECLARATION);
				break;
		}
	}

	/**
	 * Opens a set of curly brackets.
	 * Child classes that override this method should call the parent implementation
	 */
	public function openCurlyBrackets() {
		$owner = $this->getOwner(); /* @var APHPTokenReader $owner */
		$owner->getCurlyBracketStack()->push($owner->currentLine);
	}

	/**
	 * Closes a set of curly brackets.
	 * Child classes that override this method should call the parent implementation
	 */
	public function closeCurlyBrackets() {
		$owner = $this->getOwner(); /* @var APHPTokenReader $owner */
		$owner->getCurlyBracketStack()->pop();
	}
	/**
	 * Opens a set of square brackets.
	 * Child classes that override this method should call the parent implementation
	 */
	public function openSquareBrackets() {
		$owner = $this->getOwner(); /* @var APHPTokenReader $owner */
		$owner->getSquareBracketStack()->push($owner->currentLine);
	}

	/**
	 * Closes a set of square brackets.
	 * Child classes that override this method should call the parent implementation
	 */
	public function closeSquareBrackets() {
		$owner = $this->getOwner(); /* @var APHPTokenReader $owner */
		$owner->getSquareBracketStack()->pop();
	}
	/**
	 * Opens a set of parentheses.
	 * Child classes that override this method should call the parent implementation
	 */
	public function openParenthesis() {
		$owner = $this->getOwner(); /* @var APHPTokenReader $owner */
		$owner->getParenthesisStack()->push($owner->currentLine);
	}

	/**
	 * Closes a set of parentheses.
	 * Child classes that override this method should call the parent implementation
	 */
	public function closeParenthesis() {
		$owner = $this->getOwner(); /* @var APHPTokenReader $owner */
		$owner->getParenthesisStack()->pop();
	}

	/**
	 * Triggered when the tokenizer reaches the end of a statement
	 */
	public function endStatement() {
		$owner = $this->getOwner(); /* @var APHPTokenReader $owner */
		$owner->getStatementStack()->clear();
	}
	/**
	 * Triggered when the tokenizer reaches an assignment
	 */
	public function startAssignment() {

	}

}