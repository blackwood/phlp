<?php 

include './index.php';

phlp(['do',

['puts', "\nReducible concatenation"],
['var_dump',
	"['.', 'foo', 'bar', 'baz'] === 'foobarbaz'",
	['=', 'foobarbaz', ['.', 'foo', 'bar', 'baz']]],

['puts', "\nArray Merge with nested functions"],
['var_dump', 
	"['array_merge',[['+',9,10],2],[4,5,6]] === [19,2,4,5,6]",
	['=', [19,2,4,5,6], ['array_merge',[['+',9,10],2],[4,5,6]]]],

['puts', "\nArray Merge with nested functions II"],
['var_dump', 
	['array_merge',[2],['array_merge',[3],[4],[5]]]],

['puts', "\nDefining variables"],
['var_dump',
	['def', 'b', 5],
	"['def', 'b', 5]",
	"['+', 1, 'b'] === 6",
	['=', 6, ['+', 1, 'b']]],

['puts', "\nBasic Maths"],
['var_dump', ['def', 'a', 4],
	['=', ['/', 16, 'a'], 4],
	['=', ['*', 2, 'a'], 8],
	['=', ['-', 2, 'a'], -2],
	['=', ['%', 17, 'a'], 1]],

['puts', "\nControl Flow"],
['var_dump', ['=', 'nope', ['if', ['=', 4, 3],
	['current', ['yep']],
	['current', ['nope']]]]],

['puts', "\nDefine a function"],
['defn', 'yell', 
	['greeting', 'planet'],
	['.', ['implode', ', ', ['strtoupper', 'greeting', 'planet']], '!!!']],
['var_dump', ['=', 'HELLO, WORLD!!!', ['current', ['yell', 'hello', 'world']]]],

]);