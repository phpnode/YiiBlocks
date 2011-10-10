<?php
/**
 * Reads a stream of PHP tokens and builds a documentation outline
 * @author Charles Pick
 * @package packages.docs
 */
class APHPTokenReader extends AStateMachine {

	/**
	 * Holds a stack of curly brackets {}
	 * @var CStack
	 */
	protected $_curlyBracketStack;
	/**
	 * Holds a stack of parentheses ()
	 * @var CStack
	 */
	protected $_parenthesisStack;

	/**
	 * Holds a stack of square brackets []
	 * @var CStack
	 */
	protected $_squareBracketStack;

	/**
	 * Holds a stack of tokens in the current statement
	 * @var CStack
	 */
	protected $_statementStack;

	/**
	 * Holds a stack of entities
	 * @var CStack
	 */
	protected $_entityStack;

	/**
	 * Holds a stack of states
	 * @var CStack
	 */
	protected $_stateStack;

	/**
	 * The tokens to read
	 * @var array
	 */
	public $tokens = array();


	/**
	 * The current line in the file
	 * @var integer
	 */
	public $currentLine = 0;

	/**
	 * Constructor
	 * @param array $tokens the tokens to read
	 */
	public function __construct(array $tokens) {
		$this->tokens = $tokens;
		$this->init();
	}
	/**
	 * Initializes the token reader and attaches the states
	 */
	public function init() {
		parent::init();
		$this->defaultStateName = APHPTokenReaderState::DEFAULT_STATE;
		$this->addState(new APHPTokenReaderState(APHPTokenReaderState::DEFAULT_STATE,$this));
		$this->addState(new ANamespaceDeclarationState(APHPTokenReaderState::NAMESPACE_DECLARATION,$this));
		$this->addState(new ANamespaceBodyState(APHPTokenReaderState::NAMESPACE_BODY,$this));
		$this->addState(new ANamespaceCurlyBodyState(APHPTokenReaderState::NAMESPACE_CURLY_BODY,$this));
		$this->addState(new AClassDeclarationState(APHPTokenReaderState::CLASS_DECLARATION,$this));
		$this->addState(new AClassBodyState(APHPTokenReaderState::CLASS_BODY,$this));
		$this->addState(new APublicMemberDeclarationState(APHPTokenReaderState::PUBLIC_MEMBER_DECLARATION,$this));
	}


	public function read() {
		$token = next($this->tokens);
		if ($token === false) {
			return false;
		}
		$this->getStatementStack()->push($token);
		if (!is_array($token)) {
			switch($token) {
				case "{":
					$this->getState()->openCurlyBrackets();
					break;
				case "}":
					$this->getState()->closeCurlyBrackets();
					break;
				case "[":
					$this->getState()->openSquareBrackets();
					break;
				case "]":
					$this->getState()->closeSquareBrackets();
					break;
				case "(":
					$this->getState()->openParenthesis();
					break;
				case ")":
					$this->getState()->closeParenthesis();
					break;
				case ";":
					$this->getState()->endStatement();
					break;
			}
		}
		else {
			$this->currentLine = $token[2];
			switch($token[0]) {
				case T_CURLY_OPEN:
				case T_DOLLAR_OPEN_CURLY_BRACES:
					$this->getState()->openCurlyBrackets();
					break;
				case T_WHITESPACE:
				case T_ENCAPSED_AND_WHITESPACE:
					// we need to count the new lines in this string
					$numberOfLines = substr_count($token[1],"\n");
					if ($numberOfLines) {
						$this->currentLine += ($numberOfLines - 1);
					}

					break;
			}
			$this->getState()->parse($token);
		}
		return $token;
	}


	/**
	 * Gets the parenthesis stack
	 * @return CStack the stack
	 */
	public function getParenthesisStack() {
		if ($this->_parenthesisStack === null) {
			$this->_parenthesisStack = new CStack();
		}
		return $this->_parenthesisStack;
	}


	/**
	 * Gets the square bracket stack
	 * @return CStack the stack
	 */
	public function getSquareBracketStack()	{
		if ($this->_squareBracketStack === null) {
			$this->_squareBracketStack = new CStack();
		}
		return $this->_squareBracketStack;
	}

	/**
	 * Gets the curly bracket stack
	 * @return CStack the stack
	 */
	public function getCurlyBracketStack()	{
		if ($this->_curlyBracketStack === null) {
			$this->_curlyBracketStack = new CStack();
		}
		return $this->_curlyBracketStack;
	}

	/**
	 * Gets the statement stack
	 * @return CStack the stack
	 */
	public function getStatementStack()	{
		if ($this->_statementStack === null) {
			$this->_statementStack = new CStack();
		}
		return $this->_statementStack;
	}

	/**
	 * Gets the entity stack
	 * @return CStack the stack
	 */
	public function getEntityStack()	{
		if ($this->_entityStack === null) {
			$this->_entityStack = new CStack();
		}
		return $this->_entityStack;
	}

	/**
	 * Gets the state stack
	 * @return CStack the stack
	 */
	public function getStateStack()	{
		if ($this->_stateStack === null) {
			$this->_stateStack = new CStack();
		}
		return $this->_stateStack;
	}

}