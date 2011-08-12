<?php
/**
 * Tokenizes a PHP file and provides documentation information about the contents.
 * @author Charles Pick
 * @package packages.docs
 */
class APHPTokenizer extends CComponent {
	/**
	 * Holds the global namespace
	 * @var ANamespaceDoc
	 */
	protected $_globalNamespace;
	
	
	/**
	 * Reads a PHP file and returns a list of contents
	 * @param string $filename The full path to the file
	 * @return ANamespacedoc a list of contents
	 */
	public function readFile($filename) {
		$tokens = token_get_all(file_get_contents($filename));
		if ($this->_globalNamespace === null) {
			$this->_globalNamespace = new ANamespaceDoc;
		}
		
		$state = new APHPTokenizerState($this->_globalNamespace, $filename);
		$state->tokens = $tokens;
		$state->parse();
		
		return $this->_globalNamespace;
	}

	
	



	/**
	 * Parses a list of tokens and builds a documentation model for them
	 * @param array $contents The namespace to populate
	 * @param array $tokens A list of tokens to parse
	 * @return ANamespaceDoc the updated namespace
	 */
	public function parseTokens(ANamespaceDoc $contents, $tokens) {
		$state = new APHPTokenizerState($contents);
		$class = null;
		$function = null;
		$method = null;
		$property = null;
		$declaration = null;
		$docComment = null;
		$lastString = null;
		$parameter = null;
		$namespace = null;
		$currentLine = 1;
		for($i = array_shift(array_keys($tokens)); $i < count($tokens); $i++ ) {
			$token = $tokens[$i];
			if (is_array($token)) {
				$currentLine = $token[2];
				if ($state->inPropertyAssignment) {
					$property->value .= $token[1];
					continue;
				} 
				elseif ($state->inParameterAssignment) {
					$parameter->value .= $token[1];
					continue;
				} 
				switch($token[0]) {
					case T_NAMESPACE:
						$state->inNamespaceDeclaration = true;
						$namespace = new ANamespaceDoc;
						$namespace->startLine = $currentLine;
						break;
					case T_ABSTRACT:
						if (!$state->inClassDeclaration && $state->inClass) {
							// this is a method declaration
							if ($method === null) {
								$method = new AClassMethodDoc;
								$method->class = $class;
								$method->startLine = $currentLine;
								if ($docComment !== null) {
									$method->comment = $docComment;
									$docComment = null; // reset the doc comment
								}
							}
							$method->isAbstract = true;
							$state->inMethodDeclatation = true;
						}
						else {
							$state->inClassDeclaration = true;
							if ($class === null) {
								$class = new AClassDoc;
								$class->startLine = $currentLine;
								if ($docComment !== null) {
									$class->comment = $docComment;
									$docComment = null; // reset the doc comment
								}
							}
							$class->isAbstract = true;
						}
						$state->inDeclaration = true;
						break;
					case T_FINAL:
						if (!$state->inClassDeclaration && $state->inClass) {
							// this is a method declaration
							if ($method === null) {
								$method = new AClassMethodDoc;
								$method->class = $class;
								$method->startLine = $currentLine;
								if ($docComment !== null) {
									$method->comment = $docComment;
									$docComment = null; // reset the doc comment
								}
							}
							$method->isFinal = true;
							$state->inMethodDeclatation = true;
						}
						else {
							$state->inClassDeclaration = true;
							if ($class === null) {
								$class = new AClassDoc;
								$class->startLine = $currentLine;
								if ($docComment !== null) {
									$class->comment = $docComment;
									$docComment = null; // reset the doc comment
								}
							}
							$class->isFinal = true;
						}
						$state->inDeclaration = true;
						break;
					case T_CLASS:
						$state->inDeclaration = true;
						$state->inClassDeclaration = true;
						if ($class === null) {
							$class = new AClassDoc;
							$class->startLine = $currentLine;
							if ($docComment !== null) {
								$class->comment = $docComment;
								$docComment = null; // reset the doc comment
							}
						}
						break;
					case T_INTERFACE:
						$state->inDeclaration = true;
						$state->inClassDeclaration = true;
						if (is_object($class)) {
							$interface = new AInterfaceDoc;
							foreach($class as $attribute => $value) {
								$interface->{$attribute} = $value;
							}
							$class = $interface;
						}
						else {
							$class = new AInterfaceDoc;
						}
						if ($docComment !== null) {
							$class->comment = $docComment;
							$docComment = null; // reset the doc comment
						}
						break;
					case T_IMPLEMENTS:
						$state->inDeclaration = true;
						$state->inImplementsDeclaration = true;
						$state->inExtendsDeclaration = false;
						// see if we need to update the state
						break;
					case T_EXTENDS:
						$state->inDeclaration = true;
						$state->inExtendsDeclaration = true;
						$state->inImplementsDeclaration = false;
						// see if we need to update the state
						break;
					case T_PUBLIC:
					case T_VAR: // fall through, var == public
						$state->inDeclaration = true;
						if ($method === null) {
							// we're in a declaration but we don't know what kind yet
							if ($declaration === null) {
								$declaration = array();
							}
							$declaration['isPublic'] = true;
							
						}
						else {
							$method->isPublic = true;
						}
						break;
					case T_PRIVATE:
						$state->inDeclaration = true;
						if ($method === null) {
							// we're in a declaration but we don't know what kind yet
							if ($declaration === null) {
								$declaration = array();
							}
							$declaration['isPrivate'] = true;
						}
						else {
							$method->isPrivate = true;
						}
						break;
					case T_PROTECTED:
						$state->inDeclaration = true;
						if ($method === null) {
							// we're in a declaration but we don't know what kind yet
							if ($declaration === null) {
								$declaration = array();
							}
							$declaration['isProtected'] = true;
						}
						else {
							$method->isProtected = true;
						}
						
						break;
					case T_STATIC:
						if (!$state->inMethod && !$state->inFunction) {
							$state->inDeclaration = true;
							if ($method === null) {
								// we're in a declaration but we don't know what kind yet
								if ($declaration === null) {
									$declaration = array();
								}
								$declaration['isStatic'] = true;
							}
							else {
								$method->isStatic = true;
							}
						}
						break;
					case T_GLOBAL:
						if (!$state->inMethod && !$state->inFunction) {
							$variable = new AVariableDoc;
							$state->inDeclaration = true;
						}
						break;
					case T_FUNCTION:
						
						if (!$state->inFunction && !$state->inMethod) {
							if ($state->inClass) {
								// this is a method
								if ($method === null) {
									$method = new AClassMethodDoc($declaration);
									$method->startLine = $currentLine;
								}
								if ($docComment !== null) {
									$method->comment = $docComment;
									$docComment = null;
								}
								$state->inMethodDeclaration = true;
							}
							else {
								
								// this is a function
								if ($function === null) {
									$function = new AFunctionDoc($declaration);
									$function->startLine = $currentLine;
								}
								if ($docComment !== null) {
									$function->comment = $docComment;
									$docComment = null;
								}
								$state->inFunctionDeclaration = true;
							}
						}
						break;
					case T_STRING: 
						if ($state->inNamespaceDeclaration) {
							$namespace->name = trim($token[1]);
							$state->inNamespaceDeclaration = false;
							$state->inNamespace = true;
						}
						elseif ($state->inExtendsDeclaration) {
							$class->extends = trim($token[1]);
						}
						elseif ($state->inImplementsDeclaration) {
							$class->implements[] = trim($token[1]);
						}
						elseif ($state->inClassDeclaration) {
							$class->name = trim($token[1]);
						}
						elseif ($state->inMethodDeclaration) {
							// typehint?				
							if ($method->name === null) {
								$method->name = trim($token[1]);
							}
							else {
								$lastString = trim($token[1]);
							}
						}
						elseif ($state->inFunctionDeclaration) {
							// typehint?
							if ($function->name === null) {
								$function->name = trim($token[1]);
							}
							else {
								$lastString = trim($token[1]);
							}
						}
						break;
					case T_VARIABLE:
						
						if ($state->inMethodDeclaration) {
							
							$parameter = new AParameterDoc;
							$parameter->name = substr(trim($token[1]),1);
							
							$parameter->type = $lastString;
							$method->parameters->add($parameter->name,$parameter);
							$lastString = null;
						}
						elseif ($state->inFunctionDeclaration) {
							$parameter = new AParameterDoc;
							$parameter->name = substr(trim($token[1]),1);
							$parameter->type = $lastString;
							$function->parameters->add($parameter->name,$parameter);
							$lastString = null;
						}
						elseif (!$state->inFunction && !$state->inMethod) {
							if ($state->inClass) {
								// this is a property
								$property = new AClassPropertyDoc($declaration);
								$property->name = substr(trim($token[1]),1);
								$property->comment = $docComment;
								$property->startLine = $currentLine;
								$docComment = null;
								$state->inPropertyDeclaration = true;
							}
							else {
								// this could be a global variable
							}
						}
						break;
					case T_DOC_COMMENT:
						if (!$state->inFunctionDeclaration && !$state->inMethodDeclaration) {
							$docComment = $token[1];
						}
						break;
						
				}
			}
			else {
				if ($state->inPropertyAssignment && $token != ";") {
					$property->value .= $token;
					break;
				} 
				
				switch($token) {
					case "=":
						if ($state->inParameterAssignment) {
							$parameter->value .= $token;
							break;
						} 
						elseif ($state->inPropertyDeclaration) {
							$declaration['value'] = "";
							$state->inPropertyAssignment = true;
						}
						elseif($state->inFunctionDeclaration || $state->inMethodDeclaration) {
							$state->inParameterAssignment = true;
						}
						break;
					case "{":
						$state->curlyBracketStack->push(true);
						if ($state->curlyBracketStack->count() == 1) {
							if ($state->inNamespaceDeclaration) {
								$state->inNamespace = true;
								$state->inNamespaceDeclaration = false;
								$state->inDeclaration = false;
							}
							elseif($state->inImplementsDeclaration) {
								$state->inImplementsDeclaration = false;
								$state->inClass = true;
								$state->inDeclaration = false;
							}
							elseif($state->inExtendsDeclaration) {
								$state->inExtendsDeclaration = false;
								$state->inClass = true;
								$state->inDeclaration = false;
							}
							elseif ($state->inClassDeclaration) {
								$state->inClassDeclaration = false;
								$state->inClass = true;
								$state->inDeclaration = false;
							}
							elseif ($state->inFunctionDeclaration) {
								$state->inFunction = true;
								$state->inFunctionDeclaration = false;
								$state->inDeclaration = false;
							}
						}
						elseif ($state->curlyBracketStack->count() == 2) {
							if($state->inImplementsDeclaration) {
								$state->inImplementsDeclaration = false;
								$state->inClass = true;
								$state->inDeclaration = false;
							}
							elseif($state->inExtendsDeclaration) {
								$state->inExtendsDeclaration = false;
								$state->inClass = true;
								$state->inDeclaration = false;
							}
							elseif ($state->inClassDeclaration) {
								$state->inClassDeclaration = false;
								$state->inClass = true;
								$state->inDeclaration = false;
							}
							elseif ($state->inFunctionDeclaration) {
								$state->inFunction = true;
								$state->inFunctionDeclaration = false;
								$state->inDeclaration = false;
							}
							elseif ($state->inMethodDeclaration) {
								
								$state->inMethod = true;
								$state->inMethodDeclaration = false;
								$state->inDeclaration = false;
							}
						}
						
						break;
					case "}":
						$state->curlyBracketStack->pop();
						
						if ($state->inNamespace) {
							if ($state->curlyBracketStack->count() == 0) {
								// at the end of a namespace, clean up
								$namespace->endLine = $currentLine;
								$contents->namespaces->add($namespace->name,$namespace);
								$namespace = null;
								$state->inNamespace = false;
							}
							elseif ($state->inClass && $state->curlyBracketStack->count() == 1) {
								// at the end of a class, clean up
								$class->endLine = $currentLine;
								$namespace->classes->add($class->name,$class);
								$class = null;
								$state->inClass = false;
							}
							else if ($state->inMethod && $state->curlyBracketStack->count() == 2) {
								// at the end of a method, clean up
								$method->endLine = $currentLine;
								$class->methods->add($method->name,$method);
								$method = null;
								$state->inMethod = false;
							}
							else if ($state->inFunction && $state->curlyBracketStack->count() == 1) {
								// at the end of a function, clean up
								$function->endLine = $currentLine;
								$namespace->functions->add($function->name,$function);
								$function = null;
								$state->inFunction = false;
							}
						}
						elseif ($state->inClass && $state->curlyBracketStack->count() == 0) {
							// at the end of a class, clean up
							$class->endLine = $currentLine;
							$contents->classes->add($class->name,$class);
							$class = null;
							$state->inClass = false;
						}
						else if ($state->inMethod && $state->curlyBracketStack->count() == 1) {
							// at the end of a method, clean up
							$method->endLine = $currentLine;
							$class->methods->add($method->name,$method);
							$method = null;
							$state->inMethod = false;
						}
						else if ($state->inFunction && $state->curlyBracketStack->count() == 0) {
							// at the end of a function, clean up
							$function->endLine = $currentLine;
							$contents->functions->add($function->name,$function);
							$function = null;
							$state->inFunction = false;
							$state->inFunctionDeclaration = false;
							$state->inParameterAssignment = false;
						}
						else {
						}
						// see if we need to update the state
						break;
					case "[":
						
						$state->squareBracketStack->push(true);
						if ($state->inParameterAssignment) {
							$parameter->value .= $token;
						} 
						break;
					case "]":
						
						$state->squareBracketStack->pop();
						if ($state->inParameterAssignment) {
							$parameter->value .= $token;
						} 
						break;
					case "(":
						$state->parensStack->push(true);
						if ($state->inParameterAssignment) {
							$parameter->value .= $token;
						} 
						break;
					case ")":
						$state->parensStack->pop();
						// see if we need to update the state
						if ($state->parensStack->count() == 0 && $state->inPropertyDeclaration) {
							$property->endLine = $currentLine;
							$class->properties->add($property->name,$property);
							$property = null;
							$declaration = null;
							$state->inDeclaration = false;
							$state->inPropertyDeclaration = false;
							$state->inPropertyAssignment = false;
						}
						elseif ($state->parensStack->count() > 0 && $state->inParameterAssignment) {
							$parameter->value .= $token;
						} 
						break;
					case ";":
						// see if this is the end of a declaration
						if ($state->inPropertyDeclaration) {
							$property->endLine = $currentLine;
							$class->properties->add($property->name,$property);
							$property = null;
							$declaration = null;
							$state->inDeclaration = false;
							$state->inPropertyDeclaration = false;
							$state->inPropertyAssignment = false;
						}
						
						break;
					case ",":
						
						if ($state->parensStack->count() == 1 && $state->inParameterAssignment) {
							$state->inParameterAssignment = false;
							
						}
						elseif ($state->inParameterAssignment) {
							$parameter->value .= $token;
						} 
						break;
					default: 
						if ($state->inParameterAssignment) {
							$parameter->value .= $token;
						} 
						break;
				}
			}
		}
		
		return $contents;
	}
	
}

/**
 * Holds the tokenizer state.
 * @author Charles Pick
 * @package packages.docs
 */
class APHPTokenizerState extends CComponent {
	/**
	 * The name of the current file being parsed
	 * @var string
	 */
	public $filename;
	/**
	 * The PHP tokens that are being tokenized
	 * @var array
	 */
	public $tokens = array();
	/**
	 * The index of the current token
	 * @var integer
	 */
	public $tokenIndex = 0;
	
	/**
	 * The current line in the file being parsed
	 * @var integer
	 */	
	public $currentLine = 0;
		
	/**
	 * Whether we're in a declaration of some kind
	 * @var boolean
	 */
	public $inDeclaration = false;
	/**
	 * Whether we're in a class or not
	 * @var boolean
	 */	
	public $inClass = false;
	
	/**
	 * Whether we're in a class declaration or not
	 * @var boolean
	 */	
	public $inClassDeclaration = false;
	
	/**
	 * Whether we're in a const assignment or not
	 */
	public $inConstAssignment = false;

	/**
	 * Whether we're in a function or not
	 * @var boolean
	 */
	public $inFunction = false;
	
	/**
	 * Whether we're in a function declaration or not
	 * @var boolean
	 */
	public $inFunctionDeclaration = false;
	
	/**
	 * Whether we're in a method or not
	 * @var boolean
	 */
	public $inMethod = false;
	/**
	 * Whether we're in a method declaration or not
	 * @var boolean
	 */
	public $inMethodDeclaration = false;
	
	/**
	 * Whether we're in a property declaration or not
	 * @var boolean
	 */
	public $inPropertyDeclaration = false;
	
	/**
	 * Whether we're in a property assignment or not
	 * @var boolean
	 */
	public $inPropertyAssignment = false;
	
	/**
	 * Whether we're in a paramenter assignment  or not
	 * @var boolean
	 */
	public $inParameterAssignment = false;
	
	/**
	 * Whether we're in a parameter section or not
	 * @var boolean
	 */
	public $inParameterSection = false;
	
	/**
	 * Whether we're in a parameter declaration or not
	 * @var boolean
	 */
	public $inParameterDeclaration = false;
	
	/**
	 * Whether we're in an implements declaration or not
	 * @var boolean
	 */
	public $inImplementsDeclaration = false;
	
	/**
	 * Whether we're in an extends declaration or not
	 * @var boolean
	 */
	public $inExtendsDeclaration = false;
	
	/**
	 * Whether the current declaration is public or not
	 * @var boolean
	 */
	public $inPublic = false;
	
	/**
	 * Whether the current declaration is private or not
	 * @var boolean
	 */
	public $inPrivate = false;
	
	/**
	 * Whether the current declaration is protected or not
	 * @var boolean
	 */
	public $inProtected = false;
	
	/**
	 * Whether the current declaration is static or not
	 * @var boolean
	 */
	public $inStatic = false;
	
	/**
	 * Whether the current declaration is final or not
	 * @var boolean
	 */
	public $inFinal = false;
	
	/**
	 * Whether we're in an interface or not
	 * @var boolean
	 */
	public $inInterface = false;
	/**
	 * Whether the current declaration is abstract or not
	 * @var boolean
	 */
	public $inAbstract = false;
	
	/**
	 * Holds the curly bracket stack "{}"
	 * @var CStack
	 */
	public $curlyBracketStack;
	/**
	 * The current class
	 * @var AClassDoc
	 */
	public $class;
	/**
	 * Holds the square bracket stack "[]"
	 * @var CStack
	 */
	public $squareBracketStack;
	
	/**
	 * Holds the parenthesis stack "()"
	 * @var CStack
	 */
	public $parensStack;
	
	/**
	 * Whether we're in a namespace or not
	 * @var boolean
	 */
	public $inNamespace = false;
	
	
	/**
	 * Whether we're in a namespace declaration or not
	 * @var boolean
	 */
	public $inNamespaceDeclaration = false;
	
	/**
	 * Holds the namespace stack
	 * @var CStack
	 */
	public $namespaceStack;	
	
	/**
	 * The (global) namespace to populate
	 * @var ANamespaceDoc
	 */
	public $contents;
	
	/**
	 * The last PHP doc comment
	 * @var string
	 */
	public $lastComment;
	
	/**
	 * Constructor
	 * @param ANamespaceDoc $contents The (global) namespace to populate
	 * @param string $filename The filename
	 */
	public function __construct(ANamespaceDoc $contents, $filename = null) {
		$this->contents = $contents;
		$this->curlyBracketStack = new CStack();
		$this->squareBracketStack = new CStack();
		$this->parensStack = new CStack();
		$this->namespaceStack = new CStack();
		$this->filename = $filename;
	}
	
	/**
	 * Parses the list of tokens
	 * @return ANamespace the parsed content
	 */
	public function parse() {
		$tokens = $this->tokens;
		$docComment = "";
		for($i = $this->tokenIndex; $i < count($this->tokens); $i++ ) {
			$token = $this->getNextToken();
			if (is_array($token)) {
				$currentLine = $token[2];
				
				switch($token[0]) {
					case T_NAMESPACE:
						$this->tokenIndex--;
						$this->parseNamespace();
						break;
					case T_ABSTRACT:
						$this->tokenIndex--;
						$this->parseClass();
						break;
					case T_FINAL:
						$this->tokenIndex--;
						$this->parseClass();
						break;
					case T_CLASS:
						$this->tokenIndex--;
						
						$this->parseClass();
						break;
					case T_INTERFACE:
						$this->tokenIndex--;
						$this->parseClass();
						break;
					case T_FUNCTION:
						$this->tokenIndex--;
						$this->parseFunction();
						break;
					case T_DOC_COMMENT:
						$this->lastComment = $token[1];
						break;
				}		
				
			}
		
		}
		
		return $this->contents;
	}
	/**
	 * Parses a namespace
	 */
	protected function parseNamespace() {
		$docComment = "";
		for($i = $this->tokenIndex; $i < count($this->tokens); $i++ ) {
			$token = $this->getNextToken();
			if (is_array($token)) {
				$currentLine = $token[2];
				
				switch($token[0]) {
					case T_NAMESPACE:
						$this->inNamespaceDeclaration = true;
						$namespace = new ANamespaceDoc;
						$namespace->filename = $this->filename;
						$namespace->comment = $this->lastComment;
						$namespace->startLine = $currentLine;
						$this->lastComment = null;
						
						break;
					case T_STRING: 
						if ($this->inNamespaceDeclaration) {
							$namespace->name = $token[1];
							if ($this->inNamespace) {
								$this->namespaceStack->peek()->namespaces->add($namespace->name,$namespace);
								$this->namespaceStack->push($namespace);
								$this->namespace = $namespace;
							}
							else {
								$this->contents->namespaces->add($namespace->name,$namespace);
								$this->namespaceStack->push($namespace);
							}
						}
						break;
					case T_ABSTRACT:
						$this->tokenIndex--;
						$this->parseClass();
						break;
					case T_FINAL:
						$this->tokenIndex--;
						$this->parseClass();
						break;
					case T_CLASS:
						$this->tokenIndex--;
						$this->parseClass();
						break;
					case T_INTERFACE:
						$this->tokenIndex--;
						$this->parseClass();
						break;
					case T_FUNCTION:
						$this->tokenIndex--;
						$this->parseFunction();
						break;
					case T_DOC_COMMENT:
						$this->lastComment = $token[1];
						break;
				}		
				
			}
			else {
				switch($token) {
					case ";":
						if ($this->inNamespaceDeclaration) {
							// this is the end of a namespace declaration
							// but the start of a namespace that will affect
							// the whole file
							$this->inNamespaceDeclaration = false;
							$this->inNamespace = true;
						}
						break;
					case "{":
						$this->curlyBracketStack->push($this->currentLine);
						if ($this->inNamespaceDeclaration) {
							// this is the end of a namespace declaration
							// but the start of a namespace
							$this->inNamespaceDeclaration = false;
							$this->inNamespace = true;
						}
						
						break;
					case "}":
						$this->curlyBracketStack->pop();
						if ($this->inNamespace && $this->curlyBracketStack->count() == 0) {
							// this is the end of a namespace
							$namespace = $this->namespaceStack->pop();
							if ($this->namespaceStack->count() == 0) {
								$this->inNamespace = false;
							}
							$namespace->endLine = $state->currentLine;
							$this->lastComment = null;
							return;
						}
						break;
				}
			}
		
		}
		// clean up
		$this->lastComment = null;
		if ($this->inNamespace) {
			
			$namespace = $this->namespaceStack->pop();
			$namespace->endLine = $currentLine;
			if ($this->namespaceStack->count() == 0) {
				$this->inNamespace = false;
			}
		}
	} 
	
	/**
	 * Parses a function
	 */
	protected function parseFunction() {
		$docComment = "";
		$curlyBracketStack = new CStack;
		$parensStack = new CStack;
		for($i = $this->tokenIndex; $i < count($this->tokens); $i++ ) {
			$token = $this->getNextToken();
			if (is_array($token)) {
				$currentLine = $token[2];
				
				switch($token[0]) {
					
					case T_FUNCTION:
						if (!$this->inFunction) { // we don't / won't document closures
							$this->inFunctionDeclaration = true;
							$function = new AFunctionDoc;
							$function->filename = $this->filename;
							$function->startLine = $currentLine;
							$function->comment = $this->lastComment;
							if ($this->inNamespace) {
								$function->namespace = $this->namespace;
							}
							else {
								$function->namespace = $this->contents;
							}
							$this->lastComment = null;
							
						}
						break;
					case T_STRING:
						if ($this->inParameterSection) {
							if ($parensStack->count() == 1 && !$this->inParameterDeclaration) {
								// this is a type hint
								$parameter = new AParameterDoc;
								$parameter->type = $token[1];
								$parameter->function = $function;
								$parameter->filename = $this->filename;
								$parameter->startLine = $currentLine;
								$function->parameters->add($parameter);
								$this->inParameterDeclaration = true;
							}
							elseif ($this->inParameterAssignment) {
								$parameter->value .= $token[1];
							}
						}
						elseif ($this->inFunctionDeclaration) {
							// this is the function name
							$function->name = $token[1];
							if ($this->inNamespace) {
								$this->namespaceStack->peek()->functions->add($function->name, $function);
							}
							else {
								$this->contents->functions->add($function->name, $function);
							}
						}
						break;
					case T_VARIABLE:
						if ($this->inParameterSection) {
							if ($parensStack->count() == 1) {
								// this is a parameter
								if (!$this->inParameterDeclaration) {
									$parameter = new AParameterDoc;
									$parameter->function = $function;
									$parameter->filename = $this->filename;
									$parameter->startLine = $currentLine;
								}
								$parameter->name = substr($token[1],1);
								$function->parameters->add($parameter->name,$parameter);
								$this->inParameterDeclaration = true;
							}
						}
						elseif ($this->inParameterAssignment) {
							$parameter->value .= $token[1];
						}
						break;
					case T_RETURN:
						if ($this->tokens[$this->tokenIndex + 1] != ";") {
							$function->returns = true;
						}
						break;
					case T_DOC_COMMENT:
						$this->lastComment = $token[1];
						break;
					default:
						if ($this->inParameterAssignment) {
							$parameter->value .= $token[1];
						}
						break;
				}		
				
			}
			else {
				
				switch($token) {
					case "=":
						if ($this->inParameterDeclaration && $parensStack->count() == 1) {
							$this->inParameterAssignment = true;
						}
						elseif ($this->inParameterAssignment) {
							$parameter->value .= $token;
						}
						break;
					case ",":
						if ($this->inParameterSection && $parensStack->count() == 1) {
							// expect another parameter next
							$this->inParameterDeclaration = false;
							$this->inParameterAssignment = false;
							$parameter->endLine = $currentLine;
						}
						elseif ($this->inParameterAssignment) {
							$parameter->value .= $token;
						}
						break;
					
					case "{":
						$curlyBracketStack->push($this->currentLine);
						if ($this->inFunctionDeclaration) {
							// this is the end of a function declaration
							// but the start of a function
							$this->inFunctionDeclaration = false;
							$this->inFunction = true;
						}
						
						break;
					case "}":
						$curlyBracketStack->pop();
						if ($this->inFunction && $curlyBracketStack->count() == 0) {
							// this is the end of a function
							$this->inFunction = false;
							$function->endLine = $currentLine + 1;
							return;
						}
						break;
					case "(":
						$parensStack->push($this->currentLine);
						if ($this->inFunctionDeclaration && $parensStack->count() == 1) {
							$this->inParameterSection = true;
						}
						elseif ($this->inParameterAssignment) {
							$parameter->value .= $token;
						}
						
						break;
					case ")":
						$parensStack->pop();
						if ($this->inParameterSection && $parensStack->count() == 0) {
							// this is the end of a parameters section
							$this->inParameterSection = false;
							$this->inParameterDeclaration = false;
							$this->inParameterAssignment = false;
							if (isset($parameter)) {
								$parameter->endLine = $currentLine;
							}
						}
						elseif ($this->inParameterAssignment) {
							$parameter->value .= $token;
						}
						break;
					default:
						if ($this->inParameterAssignment) {
							$parameter->value .= $token;
						}
						break;
				}
			}
		
		}
	}
	/**
	 * Parses a class
	 */
	protected function parseClass() {
		$docComment = "";
		$curlyBracketStack = new CStack;
		$parensStack = new CStack;
		$isAbstract = false;
		$isFinal = false;
		
		for($i = $this->tokenIndex; $i < count($this->tokens); $i++ ) {
			$token = $this->getNextToken();
			if (is_array($token)) {
				$currentLine = $token[2];
				
				switch($token[0]) {
					case T_CLASS:
						if (!$this->inClassDeclaration) {
							$this->inClassDeclaration = true;
							$class = new AClassDoc;
							$class->filename = $this->filename;
							$this->class = $class;
							$class->startLine = $currentLine;
							if ($this->inNamespace) {
								$class->namespace = $this->namespace;
							}
							else {
								$class->namespace = $this->contents;
							}
							$class->comment = $this->lastComment;
							$this->lastComment = null;
						}
						break;
					case T_ABSTRACT:
						if (!$this->inClass) {
							
							if (!$this->inClassDeclaration) {
								$this->inClassDeclaration = true;
								$class = new AClassDoc;
								$class->filename = $this->filename;
								$this->class = $class;
								$class->startLine = $currentLine;
								if ($this->inNamespace) {
									$class->namespace = $this->namespace;
								}
								else {
									$class->namespace = $this->contents;
								}
								$class->comment = $this->lastComment;
								
								$this->lastComment = null;
							}
							$class->isAbstract = true;
						}
						else {
							
							$this->inAbstract = true;
						}
						break;
					case T_FINAL:
						if (!$this->inClass) {
							if (!$this->inClassDeclaration) {
								$this->inClassDeclaration = true;
								$class = new AClassDoc;
								$class->filename = $this->filename;
								$this->class = $class;
								$class->startLine = $currentLine;
								if ($this->inNamespace) {
									$class->namespace = $this->namespace;
								}
								else {
									$class->namespace = $this->contents;
								}
								$class->comment = $this->lastComment;
								$this->lastComment = null;
							}
							$class->isFinal = true;
						}
						else {
							$this->inFinal = true;
						}
						break;
					case T_INTERFACE:
						// this is an interface not a class
						if ($this->inClassDeclaration) {
							$interface = new AInterfaceDoc;
							$interface->isAbstract = $class->isAbstract;
							$class = $interface;
							$class->filename = $this->filename;
							$this->class = $class;
						}
						else {
							$this->inClassDeclaration = true;
							$class = new AInterfaceDoc;
							$this->class = $class;
							$class->filename = $this->filename;
							$class->startLine = $currentLine;
							$class->comment = $this->lastComment;
							$this->lastComment = null;
						}
						if ($this->inNamespace) {
							$class->namespace = $this->namespace;
						}
						else {
							$class->namespace = $this->contents;
						}
						$this->inInterface = true;
						break;
					case T_IMPLEMENTS:
						$this->inImplementsDeclaration = true;
						$this->inExtendsDeclaration = false;
						break;
					case T_EXTENDS:
						$this->inExtendsDeclaration = true;
						$this->inImplementsDeclaration = false;
						break;
					case T_CONST:
						$this->tokenIndex--;
						$this->parseConst();
						break;
					case T_PUBLIC:
					case T_VAR:
						$this->inPublic = true;
						break;
					case T_PRIVATE:
						$this->inPrivate = true;
						break;
					case T_PROTECTED:
						$this->inProtected = true;
						break;
					case T_STATIC:
						$this->inStatic = true;
						break;
					case T_FUNCTION:
						$this->tokenIndex--;
						$this->parseMethod();
						break;
					case T_STRING:
						if ($this->inClassDeclaration) {
							if ($this->inImplementsDeclaration) {
								// this is the name of an interface
								$class->implements[] = $token[1];
								
							}
							elseif ($this->inExtendsDeclaration) {
								// this is the name of a parent class
								$class->extends = trim($token[1]);
							}
							else {
								// this is the class/interface name
								$class->name = $token[1];
								
								// add to contents or namespace as appropriate
								if ($class instanceof AClassDoc) {
									if ($this->inNamespace) {
										$this->namespaceStack->peek()->classes->add($class->name, $class);
									}
									else {
										$this->contents->classes->add($class->name, $class);
									}
									
								}
								else {
									
									if ($this->inNamespace) {
										$this->namespaceStack->peek()->interfaces->add($class->name, $class);
									}
									else {
										$this->contents->interfaces->add($class->name, $class);
									}
								}
							}
						}
						break;
					case T_VARIABLE:
						$this->tokenIndex--;
						$this->parseProperty();
						break;
					case T_DOC_COMMENT:
						$this->lastComment = $token[1];
						break;
					
				}		
				
			}
			else {
				
				switch($token) {
					case ";":
						if ($this->inClassDeclaration) {
							// this is the end of a class without a body
							$this->inClass = false;
							$this->inInterface = false;
							$class->endLine = $currentLine + 1;
							return;
						}
						break;				
					case "{":
						$curlyBracketStack->push($this->currentLine);
						if ($this->inClassDeclaration) {
							$this->inClassDeclaration = false;
							$this->inExtendsDeclaration = false;
							$this->inImplementsDeclaration = false;
							$this->inClass = true;
						}
						
						break;
					case "}":
						$curlyBracketStack->pop();
						
						if ($this->inClass && $curlyBracketStack->count() == 0) {
							// this is the end of a class
							$this->inClass = false;
							$this->inInterface = false;
							$class->endLine = $currentLine + 1;
							
							return;
						}
						break;
					
				}
			}
		
		}
	} 
	
	/**
	 * Parses a class method
	 */
	protected function parseMethod() {
		$docComment = "";
		$curlyBracketStack = new CStack;
		$parensStack = new CStack;
		for($i = $this->tokenIndex; $i < count($this->tokens); $i++ ) {
			$token = $this->getNextToken();
			if (is_array($token)) {
				$currentLine = $token[2];
				
				switch($token[0]) {
					
					case T_FUNCTION:
						if (!$this->inMethod) { // we don't / won't document closures
							$this->inMethodDeclaration = true;
							$method = new AClassMethodDoc;
							$method->startLine = $currentLine;
							$method->comment = $this->lastComment;
							$method->filename = $this->filename;
							$method->class = $this->class;
							$method->isAbstract = $this->inAbstract;
							$method->isProtected = $this->inProtected;
							$method->isPublic = $this->inPublic;
							$method->isPrivate = $this->inPrivate;
							$method->isStatic = $this->inStatic;
							$method->isFinal = $this->inFinal;
							// reset the states
							$this->inAbstract = false;
							$this->inProtected = false;
							$this->inPrivate = false;
							$this->inPublic = false;
							$this->inStatic = false;
							$this->inFinal = false;
							$this->lastComment = null;
							
						}
						break;
					case T_STRING:
						if ($this->inParameterSection) {
							if ($parensStack->count() == 1 && !$this->inParameterDeclaration) {
								// this is a type hint
								$parameter = new AParameterDoc;
								$parameter->function = $method;
								$parameter->type = $token[1];
								$parameter->filename = $this->filename;
								$parameter->startLine = $currentLine;
								$this->inParameterDeclaration = true;
							}
							elseif ($this->inParameterAssignment) {
								$parameter->value .= $token[1];
							}
						}
						elseif ($this->inMethodDeclaration) {
							// this is the method name
							$method->name = $token[1];
							$this->class->methods->add($method->name,$method);
						}
						break;
					case T_VARIABLE:
						if ($this->inParameterSection) {
							if ($parensStack->count() == 1) {
								// this is a parameter
								if (!$this->inParameterDeclaration) {
									$parameter = new AParameterDoc;
									$parameter->function = $method;
									$parameter->startLine = $currentLine;
								}
								$parameter->name = substr($token[1],1);
								$parameter->filename = $this->filename;
								$method->parameters->add($parameter->name,$parameter);
								$this->inParameterDeclaration = true;
							}
						}
						elseif ($this->inParameterAssignment) {
							$parameter->value .= $token[1];
						}
						break;
					case T_CURLY_OPEN:
						$curlyBracketStack->push($this->currentLine);
						break;
					case T_RETURN:
						if ($this->tokens[$this->tokenIndex + 1] != ";") {
							$method->returns = true;
						}
						break;
					case T_DOC_COMMENT:
						$this->lastComment = $token[1];
						break;
					default:
						if ($this->inParameterAssignment) {
							$parameter->value .= $token[1];
						}
						break;
				}		
				
			}
			else {
				
				switch($token) {
					case "=":
						if ($this->inParameterDeclaration && $parensStack->count() == 1) {
							$this->inParameterAssignment = true;
						}
						elseif ($this->inParameterAssignment) {
							$parameter->value .= $token;
						}
						break;
					case ",":
						if ($this->inParameterSection && $parensStack->count() == 1) {
							// expect another parameter next
							$this->inParameterDeclaration = false;
							$this->inParameterAssignment = false;
							$parameter->endLine = $currentLine;
						}
						elseif ($this->inParameterAssignment) {
							$parameter->value .= $token;
						}
						break;
					
					case "{":
					
						$curlyBracketStack->push($this->currentLine);
						if ($this->inMethodDeclaration) {
							// this is the end of a method declaration
							// but the start of a method
							$this->inMethodDeclaration = false;
							$this->inMethod = true;
						}
						
						break;
					case "}":
					
						$curlyBracketStack->pop();
						
						if ($this->inMethod && $curlyBracketStack->count() == 0) {
							// this is the end of a method
							$this->inMethod = false;
							$method->endLine = $currentLine + 1;
							return;
						}
						break;
					case ";":
						if ($this->inMethodDeclaration && $curlyBracketStack->count() == 0) {
							// this is the end of a method
							
							$this->inMethod = false;
							$method->endLine = $currentLine + 1;
							return;
						}
						break;
					case "(":
						$parensStack->push($this->currentLine);
						if ($this->inMethodDeclaration && $parensStack->count() == 1) {
							$this->inParameterSection = true;
						}
						elseif ($this->inParameterAssignment) {
							$parameter->value .= $token;
						}
						
						break;
					case ")":
						$parensStack->pop();
						if ($this->inParameterSection && $parensStack->count() == 0) {
							// this is the end of a parameters section
							$this->inParameterSection = false;
							$this->inParameterDeclaration = false;
							$this->inParameterAssignment = false;
							if (isset($parameter)) {
								$parameter->endLine = $currentLine;
							}
						}
						elseif ($this->inParameterAssignment) {
							$parameter->value .= $token;
						}
						break;
					default:
						if ($this->inParameterAssignment) {
							$parameter->value .= $token;
						}
						break;
				}
			}
		
		}
	}
	/**
	 * Debug function: Dumps the last $limit tokens.
	 * @param integer $limit The total number of tokens to return
	 */
	protected function dumpTokens($limit = 10) {
		echo "<pre>";
		$stop = $this->tokenIndex - $limit;
		if ($stop < 0) {
			$stop = 0;
		}
		$items = array();
		
		for($i = $this->tokenIndex; $i > $stop; $i--) {
			if (!isset($this->tokens[$i])) {
				break;
			}
			$token = $this->tokens[$i];
			if (is_array($token)) {
				$items[] =  "\t".$token[1];
			}
			else {
				$items[] = "\t$token";
			}
		}
		echo implode("\n",array_reverse($items));
		echo "</pre>";
	}
	/**
	 * Parses a class property
	 */
	protected function parseProperty() {
		$docComment = "";
		$curlyBracketStack = new CStack;
		$parensStack = new CStack;
		for($i = $this->tokenIndex; $i < count($this->tokens); $i++ ) {
			$token = $this->getNextToken();
			if (is_array($token)) {
				$currentLine = $token[2];
				
				switch($token[0]) {
					
					case T_VARIABLE:
						
						$this->inPropertyDeclaration = true;
						$property = new AClassPropertyDoc;
						$property->startLine = $currentLine;
						$property->filename = $this->filename;
						$property->comment = $this->lastComment;
						$property->class = $this->class;
						$property->isProtected = $this->inProtected;
						$property->isPublic = $this->inPublic;
						$property->isPrivate = $this->inPrivate;
						$property->isStatic = $this->inStatic;
						$property->name = substr($token[1],1);
						$this->class->properties->add($property->name,$property);
						// reset the states
						$this->inPrivate = false;
						$this->inProtected = false;
						$this->inPublic = false;
						$this->inStatic = false;
						$this->lastComment = null;
							
						break;
					
					case T_DOC_COMMENT:
						$this->lastComment = $token[1];
						break;
					default:
						if ($this->inPropertyAssignment) {
							$property->value .= $token[1];
						}
						break;
				}		
				
			}
			else {
				
				switch($token) {
					case "=":
						$this->inPropertyAssignment = true;
						break;
					
					case ";":
						$this->inPropertyAssignment = false;
						$this->inPropertyDeclaration = false;
						$property->endLine = $currentLine;
						return;
						break;
					default:
						if ($this->inPropertyAssignment) {
							$property->value .= $token;
						}
						break;
				}
			}
		
		}
	}
	/**
	 * Parses a class const
	 */
	protected function parseConst() {
		$docComment = "";
		$parensStack = new CStack;
		for($i = $this->tokenIndex; $i < count($this->tokens); $i++ ) {
			$token = $this->getNextToken();
			if (is_array($token)) {
				$currentLine = $token[2];
				
				switch($token[0]) {
					
					case T_CONST:
						
						$const = new AClassConstantDoc;
						$const->startLine = $currentLine;
						$const->filename = $this->filename;
						$const->comment = $this->lastComment;
						$const->class = $this->class;
						$this->class->constants->add($const->name,$const);
						// reset the states
						$this->lastComment = null;
							
						break;
					case T_STRING:
						if ($this->inConstAssignment) {
							$const->value .= $token[1];
						}
						else {
							$const->name = $token[1];
						}
						break;
					default:
						if ($this->inConstAssignment) {
							$const->value .= $token[1];
						}
						break;
				}		
				
			}
			else {
				
				switch($token) {
					case "=":
						$this->inConstAssignment = true;
						break;
					
					case ";":
						$this->inConstAssignment = false;
						$const->endLine = $currentLine;
						return;
						break;
					default:
						if ($this->inConstAssignment) {
							$const->value .= $token;
						}
						break;
				}
			}
		
		}
	}
	
	
	/**
	 * Gets the next token, or false if there isn't one
	 * @return mixed The next token, either an array or a character
	 */
	public function getNextToken() {
		$this->tokenIndex++;
		if (!isset($this->tokens[$this->tokenIndex])) {
			return false;
		}
		return $this->tokens[$this->tokenIndex];
	}
	/**
	 * Logs the current state with the message
	 * @param string $message The log message
	 */
	public function log($message) {
		$state = array();
		foreach($this as $attribute => $value) {
			if ($value) {
				if ($value instanceof CStack && $value->count() > 0) {
					$state[] = $attribute.": ".$value->count();
				}
				elseif ($value === true) {
					$state[] = $attribute.": ".$value;
				}
			}
		}
		#echo $message." (".implode(", ",$state).")\n";
	}
}
