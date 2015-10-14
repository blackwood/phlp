<?php lisp(['do',

['var_dump', 
	['.', 'foo', 'bar', 'baz']],

['var_dump', 'foo',[2,3,4], ['array_merge',[['+',9,10],2],[4,5,6]]],
['var_dump', ['array_merge',[2],['array_merge',[3],[4],[5]]]],

['var_dump',
	['def', 'b', 5],
	['+', 1, 'b']],

['var_dump', ['def', 'a', 4],
	['=', ['/', 16, 'a'], 4],
	['=', ['*', 2, 'a'], 8],
	['=', ['-', 2, 'a'], -2],
	['=', ['%', 17, 'a'], 1]],

['do',
	['var_dump', ['=', 5, ['+', 3, 2]]],
	['var_dump', ['.', 'ba', 'na', 'na']]],

['if', ['=', 4, 3],
	['var_dump','yep'],
	['var_dump','nope']],

['def', 'greet',
	['fn', ['planet', 'greeting'],
	['puts', 'greeting', 'planet']]],

['greet', 'world', 'hello'],

['defn', 'yell', 
	['greeting', 'planet'],
	['puts', ['.', ['implode', ', ', ['strtoupper', 'greeting', 'planet']], '!!!']]],
		
['yell', 'hello', 'world'],

]);