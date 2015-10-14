<?php

echo <<<EOT
------------------------------ PHLisP -----------------------------\n
EOT;

function lisp($expression) {

	$special 	= Native::special(); 

	$reducers = Native::reducers();

	$funcs 		= Native::funcs();

	// only to check if first value IS a known function.
	$accepted = array_merge($special, $reducers, $funcs, Native::getdef());

	if (is_string($expression[0]) && // short circuit if not string.
		(function_exists($expression[0]) || 
			array_key_exists($expression[0], $accepted) ||
			array_key_exists($expression[0], Native::getdef()))) {
		$fn = $expression[0];
	}

	if (array_key_exists($fn, $special)) {
		return call_user_func_array($special[$fn], array_slice($expression, 1));
	}

	if ($fn === 'def') {
		return call_user_func_array($fn, array_slice($expression, 1));
	}

	is_array($expression) && $args = array_map(function($arg) use ($fn, $reducers) {
		if (is_array($arg)) {
			return lisp($arg);
		}
		elseif (array_key_exists($arg, Native::getdef()) && $dfs = Native::getdef()) {
			return $dfs[$arg];
		} else {
			return $arg;
		}
	}, array_slice($expression, count($fn)));
  
	if (array_key_exists($fn, $reducers)) {
		return array_reduce($args, $reducers[$fn]);
	}
	elseif (array_key_exists($fn, Native::getdef()) && $dfs = Native::getdef()) {
		return call_user_func_array($dfs[$fn], $args);
	}
	elseif (array_key_exists($fn, $funcs)) {
		return call_user_func_array($funcs[$fn], $args);
	}
	elseif ($fn && func_args_are_arrays($args)) {
  	return call_user_func_array($fn, $args);
	}
	elseif ($signature = has_known_signature_type($fn)) {
		return call_user_func("call_user_func{$signature}", $fn, $args);
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

function func_args_are_arrays($args) {
	return array_filter($args, 'is_array') === $args;
}

function is_closure($fn) {
  return is_object($fn) && ($fn instanceof Closure);
}

function has_known_signature_type($fn) {
	$known = array(
		'implode' => '_array',
	);
	return !empty($known[$fn]) ? $known[$fn] : false;
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
			},
			'def' => function($name, $value) {
				$value = !is_object($value) && $value[0] === 'fn' ? lisp($value) : $value;
				self::def($name, $value);
			},
			'fn' => function() {
				$args = func_get_args();
				$signature = $args[0];
				$body = array_slice($args, 1);

				return function() use ($signature, $body) {
					$passed = func_get_args();
					$named = array_combine($signature, $passed);
					array_walk_recursive($body, function(&$val, $key) use ($named) {
						$val = !empty($named[$val]) ? $named[$val] : $val;
					});
					return call_user_func('lisp', $body);
				};
			},
			'defn' => function() {
				$special = self::special();
				$args = func_get_args();
				$name = $args[0];
				$value = call_user_func_array($special['fn'], array_slice($args, 1));
				return call_user_func_array($special['def'], array($name, $value));
			}
		);
	}

	public function funcs() {
		return array(
			'puts' => function() {
				printf(implode("\n", func_get_args()) . "\n");
			},
			'=' => function($a, $b) {
				return $a === $b;
			},
			'>' => function($a, $b) {
				return $a > $b;
			},
			'<' => function($a, $b) {
				return $a < $b;
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

$file = !empty($argv[1]) ? $argv[1] . '.php' : '';

include $file;
