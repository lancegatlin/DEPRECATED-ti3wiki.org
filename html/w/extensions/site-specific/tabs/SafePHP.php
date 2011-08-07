<?php
$wgExtensionFunctions[] = 'wfSafePHP';

/* register parser hook */
$wgExtensionCredits['parserhook'][] = array(
    'name' => 'SafePHP',
    'author' => 'Lance Gatlin',
    'version' => '0.1',
);

$gSafePHPIP = 'extensions/SafePHP/cache';

function wfSafePHP() {
    global $wgParser, $gFunctionWhitelist, $gPHPParserTokenBlacklist;

	// Hook to for rending caching content
	$wgParser->setHook( 'php', 'SafePHP_TagCacheHook' );

	$gFunctionWhitelist =  array (
		'explode'
		,'implode'
		,'date'
		,'time'
		,'round'
		,'trunc'
		,'rand'
		,'ceil'
		,'floor'
		,'srand'
		,'strtolower'
		,'strtoupper'
		,'substr'
		,'stristr'
		,'strpos'
		,'print'
		,'print_r'
	);
    
	$gPHPParserTokenBlacklist = array (
		T_ABSTRACT	//abstract	Class Abstraction (available since PHP 5.0.0)
		//T_AND_EQUAL	&=	assignment operators
		//T_ARRAY	array()	array(), array syntax
		//T_ARRAY_CAST	(array)	type-casting
		//T_AS	as	foreach
		,T_BAD_CHARACTER	 	//anything below ASCII 32 except \t (0x09), \n (0x0a) and \r (0x0d)
		//T_BOOLEAN_AND	&&	logical operators
		//T_BOOLEAN_OR	||	logical operators
		//T_BOOL_CAST	(bool) or (boolean)	type-casting
		//T_BREAK	break	break
		//T_CASE	case	switch
		,T_CATCH	//catch	Exceptions (available since PHP 5.0.0)
		//T_CHARACTER	 	 
		,T_CLASS	//class	classes and objects
		,T_CLASS_C	//__CLASS__	magic constants (available since PHP 4.3.0)
		,T_CLONE	//clone	classes and objects (available since PHP 5.0.0)
		,T_CLOSE_TAG	// ? > or % >	 
		//T_COMMENT	// or #, and /*  */ in PHP 5	comments
		//T_CONCAT_EQUAL	.=	assignment operators
		//T_CONST	const	 
		//T_CONSTANT_ENCAPSED_STRING	"foo" or 'bar'	string syntax
		//T_CONTINUE	continue	 
		,T_CURLY_OPEN	 	 
		//T_DEC	--	incrementing/decrementing operators
		,T_DECLARE 	//declare	declare
		//T_DEFAULT	default	switch
		,T_DIR	//__DIR__	magic constants (available since PHP 5.3.0)
		//T_DIV_EQUAL	/=	assignment operators
		//T_DNUMBER	0.12, etc	floating point numbers
		//T_DOC_COMMENT	/** */	PHPDoc style comments (available since PHP 5.0.0)
		//T_DO	ndo	do..while
		//T_DOLLAR_OPEN_CURLY_BRACES	${	complex variable parsed syntax
		//T_DOUBLE_ARROW	=>	array syntax
		//T_DOUBLE_CAST	(real), (double) or (float)	type-casting
		//T_DOUBLE_COLON	::	see T_PAAMAYIM_NEKUDOTAYIM below
		,T_ECHO	//echo	echo()
		//T_ELSE	else	else
		//T_ELSEIF	elseif	elseif
		//T_EMPTY	empty	empty()
		//T_ENCAPSED_AND_WHITESPACE	 	 
		,T_ENDDECLARE	//enddeclare	declare, alternative syntax
		//T_ENDFOR	endfor	for, alternative syntax
		//T_ENDFOREACH	endforeach	foreach, alternative syntax
		//T_ENDIF	endif	if, alternative syntax
		//T_ENDSWITCH	endswitch	switch, alternative syntax
		//T_ENDWHILE	endwhile	while, alternative syntax
		//T_END_HEREDOC	 	heredoc syntax
		,T_EVAL	//eval()	eval()
		//T_EXIT	exit or die	exit(), die()
		,T_EXTENDS	//extends	extends, classes and objects
		,T_FILE	//__FILE__	magic constants
		,T_FINAL	//final	Final Keyword (available since PHP 5.0.0)
		//T_FOR	for	for
		//T_FOREACH	foreach	foreach
		//T_FUNCTION	function or cfunction	functions
		,T_FUNC_C	//__FUNCTION__	magic constants (available since PHP 4.3.0)
		,T_GLOBAL	//global	variable scope
		,T_GOTO	//goto	undocumented (available since PHP 5.3.0)
		,T_HALT_COMPILER	//__halt_compiler()	__halt_compiler (available since PHP 5.1.0)
		//T_IF	if	if
		,T_IMPLEMENTS	//implements	Object Interfaces (available since PHP 5.0.0)
		//T_INC	++	incrementing/decrementing operators
		,T_INCLUDE	//include()	include()
		,T_INCLUDE_ONCE	//include_once()	include_once()
		//T_INLINE_HTML	 	 
		//T_INSTANCEOF	instanceof	type operators (available since PHP 5.0.0)
		//T_INT_CAST	(int) or (integer)	type-casting
		,T_INTERFACE	//interface	Object Interfaces (available since PHP 5.0.0)
		//T_ISSET	isset()	isset()
		//T_IS_EQUAL	==	comparison operators
		//T_IS_GREATER_OR_EQUAL	>=	comparison operators
		//T_IS_IDENTICAL	===	comparison operators
		//T_IS_NOT_EQUAL	!= or <>	comparison operators
		//T_IS_NOT_IDENTICAL	!==	comparison operators
		//T_IS_SMALLER_OR_EQUAL	<=	comparison operators
		,T_LINE	//__LINE__	magic constants
		//T_LIST	list()	list()
		//T_LNUMBER	123, 012, 0x1ac, etc	integers
		//T_LOGICAL_AND	and	logical operators
		//T_LOGICAL_OR	or	logical operators
		//T_LOGICAL_XOR	xor	logical operators
		,T_METHOD_C	//__METHOD__	magic constants (available since PHP 5.0.0)
		//T_MINUS_EQUAL	-=	assignment operators
		//T_ML_COMMENT	/* and */	comments (PHP 4 only)
		//T_MOD_EQUAL	%=	assignment operators
		//T_MUL_EQUAL	*=	assignment operators
		,T_NS_C	//__NAMESPACE__	namespaces. Also defined as T_NAMESPACE (available since PHP 5.3.0)
		,T_NEW	//new	classes and objects
		//T_NUM_STRING	 	 
		,T_OBJECT_CAST	//(object)	type-casting
		,T_OBJECT_OPERATOR	//->	classes and objects
		,T_OLD_FUNCTION	//old_function	 
		,T_OPEN_TAG	//< ?php, < ? or < %	escaping from HTML
		,T_OPEN_TAG_WITH_ECHO	// < ?= or < %=	escaping from HTML
		,T_OR_EQUAL	// |=	assignment operators
		,T_PAAMAYIM_NEKUDOTAYIM	//::	::. Also defined as T_DOUBLE_COLON.
		,T_PLUS_EQUAL	//+=	assignment operators
		,T_PRINT	//print()	print()
		,T_PRIVATE	//private	classes and objects (available since PHP 5.0.0)
		,T_PUBLIC	//public	classes and objects (available since PHP 5.0.0)
		,T_PROTECTED	//protected	classes and objects (available since PHP 5.0.0)
		,T_REQUIRE	//require()	require()
		,T_REQUIRE_ONCE	//require_once()	require_once()
		//T_RETURN	return	returning values
		//T_SL	<<	bitwise operators
		//T_SL_EQUAL	<<=	assignment operators
		//T_SR	>>	bitwise operators
		//T_SR_EQUAL	>>=	assignment operators
		,T_START_HEREDOC	//<<<	heredoc syntax
		,T_STATIC	//static	variable scope
		//T_STRING	 	 
		//T_STRING_CAST	(string)	type-casting
		//T_STRING_VARNAME	 	 
		//T_SWITCH	switch	switch
		,T_THROW	//throw	Exceptions (available since PHP 5.0.0)
		,T_TRY	//try	Exceptions (available since PHP 5.0.0)
		//T_UNSET	unset()	unset()
		//T_UNSET_CAST	(unset)	type-casting (available since PHP 5.0.0)
		,T_USE	//use	namespaces (available since PHP 5.3.0)
		//T_VAR	var	classes and objects
		//T_VARIABLE	$foo	variables
		//T_WHILE	while	while, do..while
		//T_WHITESPACE	 	 
		//T_XOR_EQUAL	^=	assignment operators		
	);
}

function SafePHP_ParseScript($source) 
{
    global $gFunctionWhitelist, $gPHPParserTokenBlacklist;

	$parseErrors = array();
	$tokens = token_get_all($source);    
	$vcall = '';

	foreach ($tokens as $token) 
	{
		if (is_array($token)) 
		{
			list($id, $string, $linenumber) = $token;
			$parseErrors['debug'][] = array(token_name($id), $string, $linenumber);
			
			if(in_array($id, $gPHPParserTokenBlacklist))
				$parseErrors[] = 'SafePHP Error->Line[' . $linenumber . ']:' . token_name($id) . ' not allowed.';
			
			if($id == T_FUNCTION)
				if(!in_array($string, $gFunctionWhitelist))
					$parseErrors[] = 'SafePHP Error->Line[' . $linenumber . ']:' . $string . ' function not allowed.';
		}
	}
	return $parseErrors;
}

function SafePHP_TagCacheHook($content, $params, &$parser )
{
	$parseErrors = SafePHP_ParseScript($content);
	
	if($params['debug'] == true)
		$retv .= '<pre>' . print_r($parseErrors, true) . "\n" . '</pre>';
	
	if(count($parseErrors) > 0)
	{
		$retv .= implode("\n", $parseErrors);
		return $retv;
	}
		
	foreach ($params as $k => $v)
		$$k = $v;
		
	try 
	{
		$result = @eval($content);
	} 
	catch(Exception $e)
	{
		$retv .= 'SafePHP Exception->' . $e->getMessage() . "\n";
		return $retv;
	}
	
	$retv .= $parser->recursiveTagParse($result);
	return $retv;
}


?>