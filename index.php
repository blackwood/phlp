<?php

$txt = <<<EOT
------------------------------ PHLisP -----------------------------
EOT;
var_dump($txt);

class Native {
	
	public $defined = array();

	public function reducers() {
		return array(
			'+' => function($a, $b) {
				$a += $b;
				return $a;
			},
			'-' => function($a, $b) {
				$a -= $b;
				return $a;
			},
			'*' => function($a, $b) {
				$a *= $b;
				return $a;
			},
			'/' => function($a, $b) {
				$a /= $b;
				return $a;
			},
			'.' => function($a, $b) {
				$z = "{$a}{$b}";
				return $z;
			},
			'%' => function($a, $b) {
				$a %= $b;
				return $a;
			}
		);
	}

}

function def($name, $value) {

}

function lisp($expression) {

	$reducers = Native::reducers();

	if (function_exists($expression[0]) || array_key_exists($expression[0], $reducers)) {
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
			return $arg;
		}
	}, array_slice($expression, count($fn))); // get the remaining args.

	if (array_key_exists($fn, $reducers)) {
		return array_reduce($args, $reducers[$fn]);
	}
  elseif ($fn && array_filter($args, 'is_array') === $args) {
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

// lisp(['var_dump', ['+', 1, 2, 3, 4], ['.', 'foo', 'bar', 'baz']]);
// lisp(['var_dump', 'foo',[2,3,4], ['array_merge',[['+',9,10],2],[4,5,6]]]);
// lisp(['var_dump', ['array_merge',[2],['array_merge',[3],[4]]]]);
