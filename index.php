<?php

echo <<<EOT
------------------------------ PHLisP -----------------------------\n
EOT;

function fn_is_user_definition($fn, $args) {
	return $fn && $fn === 'def';
}

function fn_args_are_arrays($fn, $args) {
	$fn && array_filter($args, 'is_array') === $args;
}

function function_in_group($fn, $funcgroups) {
	foreach ($funcgroups as $funcgroup) {
		if (array_key_exists($fn, $funcgroup)) {
			return $funcgroup;
		}
	}
	return false;
}

class Native {
	
	public static $def = array();

	public function def($name, $value) {
		self::$def[$name] = $value;
	}

	public function getdef() { 
		return self::$def;
	}

	public function special() {		
		return array(
			'do' => function() {
				$exprs = func_get_args();
				return array_reduce($exprs, function($_, $expr) {
					return lisp($expr);
				}, null);
			},
			'if' => function($condition, $success, $failure) {
				$passed = lisp($condition);
				return lisp($passed ? $success : $failure);
			}
		);
	}

	public function funcs() {
		return array(
			'puts' => function() {
				printf(func_get_args() . "\n");
			},
			'=' => function($a, $b) {
				return $a === $b;
			}
		);
	}

	public function reducers() {
		return array(
			'+' => function($a, $b) {
				$a += $b;
				return $a;
			},
			'-' => function($a, $b) {
				if ($a === null) return $b;
				$a -= $b;
				return $a;
			},
			'*' => function($a, $b) {
				if ($a === null) $a = 1;
				$a *= $b;
				return $a;
			},
			'/' => function($a, $b) {
				if ($a === null) return $b;
				$a /= $b;
				return $a;
			},
			'.' => function($a, $b) {
				$z = "{$a}{$b}";
				return $z;
			},
			'%' => function($a, $b) {
				if ($a === null) return $b;
				$a %= $b;
				return $a;
			}
		);
	}

}

function def($name, $value) {
	Native::def($name, $value);
}

function lisp($expression) {

	$special 	= Native::special(); 

	$reducers = Native::reducers();

	$funcs 		= Native::funcs();

	// only to check if first value IS a known function.
	$accepted = array_merge($special, $reducers, $funcs);

	if (is_string($expression[0]) && // short circuit if not string.
		(function_exists($expression[0]) || array_key_exists($expression[0], $accepted))) {
		$fn = $expression[0];
	}

	if (array_key_exists($fn, $special)) {
		return call_user_func_array($special[$fn], array_slice($expression, 1));
	}

	if (fn_is_user_definition($fn, $args)) {
		return call_user_func_array($fn, array_slice($expression, 1));
	}

	is_array($expression) && $args = array_map(function($arg) use ($fn, $reducers) {
		if (is_array($arg)) {
			return lisp($arg);
		} else {
			if (array_key_exists($arg, Native::getdef()) && $dfs = Native::getdef()) {
				return $dfs[$arg];
			} else {
				return $arg;
			}
		}
	}, array_slice($expression, count($fn)));
  
  if (fn_args_are_arrays($fn, $args)) {
  	return call_user_func_array($fn, $args);
  }
	elseif (array_key_exists($fn, $reducers)) {
		return array_reduce($args, $reducers[$fn]);
	}
	elseif (array_key_exists($fn, $funcs)) {
		return call_user_func_array($funcs[$fn], $args);
	}
	elseif ($fn) {
		return array_map(function($arg) use ($fn) {
			return call_user_func($fn, $arg);
		}, $args);
	} 
	else {
		return $args;
	}

}

// Concatenation
lisp(['var_dump', 
	['.', 'foo', 'bar', 'baz']]);

// Nested functions and arrays as "sequences" instead of forms
lisp(['var_dump', 'foo',[2,3,4], ['array_merge',[['+',9,10],2],[4,5,6]]]);
lisp(['var_dump', ['array_merge',[2],['array_merge',[3],[4],[5]]]]);

// Multiplication, division, modulo, subtraction.
lisp(['var_dump', ['def', 'a', 4],
	['=', ['/', 16, 'a'], 4],
	['=', ['*', 2, 'a'], 8],
	['=', ['-', 2, 'a'], -2],
	['=', ['%', 17, 'a'], 1]]);

// Special form do
lisp(['do',
	['var_dump', ['=', 5, ['+', 3, 2]]],
	['var_dump', ['.', 'ba', 'na', 'na']]]);

// Control flow.
lisp(['if', ['=', 4, 3],
	['var_dump','yep'],
	['var_dump','nope']]);

lisp(['def', 'shout',
  		['fn', ['name', 'planet'],
    	['puts', 'planet', 'name']]],
    	['shout', 'hello', 'world']);

lisp(['var_dump',
	['def', 'b', 5],
	['+', 1, b]]);