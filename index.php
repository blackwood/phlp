<?php

$txt = <<<EOT
------------------------------ PHLisP -----------------------------
EOT;
var_dump($txt);

function array_args_and_function_builtin_or_defined($fn, $args) {
	return $fn && array_filter($args, 'is_array') === $args || $fn === 'def';
}

class Native {
	
	public static $def = array();

	public function def($name, $value) {
		self::$def[$name] = $value;
	}

	public function getdef() { 
		return self::$def;
	}

	public function funcs() {
		return array(
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

	$reducers = Native::reducers();

	$funcs = Native::funcs();

	if (is_string($expression[0]) && // short circuit if not string.
		(function_exists($expression[0]) || 
			array_key_exists($expression[0], $reducers) ||
			array_key_exists($expression[0], $funcs))) {
		$fn = $expression[0];
	}

	// get the rest of the arguments as a "list" or array
	$args =	array_map(function($arg) use ($fn, $reducers) {
		// Loop over remaining args, if next value
		// is array, recur, else, return the arg 
		// for outermost function evaluation.
		if (is_array($arg)) {
			return lisp($arg);
		} else {
			if (array_key_exists($arg, Native::getdef()) && $dfs = Native::getdef()) {
				return $dfs[$arg];
			} else {
				return $arg;
			}
		}
	}, array_slice($expression, count($fn))); // get the remaining args.

	if (array_key_exists($fn, $reducers)) {
		return array_reduce($args, $reducers[$fn]);
	}
	elseif (array_key_exists($fn, $funcs)) {
		return call_user_func_array($funcs[$fn], $args);
	}
  elseif (
  	array_args_and_function_builtin_or_defined($fn, $args)) {
  	return call_user_func_array($fn, $args);
  }
	elseif ($fn) {
		return array_map(function($arg) use ($fn) {
			return call_user_func($fn, $arg);
		}, $args);
	} else {
		return $args;
	}

}

// Concatenation
// lisp(['var_dump', 
// 	['.', 'foo', 'bar', 'baz']]);

// Nested functions and array "sequences"
// lisp(['var_dump', 'foo',[2,3,4], ['array_merge',[['+',9,10],2],[4,5,6]]]);
// lisp(['var_dump', ['array_merge',[2],['array_merge',[3],[4]]]]);

// Multiplication, division, modulo, subtraction.
// lisp(['var_dump', ['def', 'a', 4],
// 	// ['+', 'a', 'a'],['*', 4, 4],
// 	['=', ['/', 16, 'a'], 4],
// 	['=', ['*', 2, 'a'], 8],
// 	['=', ['-', 2, 'a'], -2],
// 	['=', ['%', 17, 'a'], 1]]);

