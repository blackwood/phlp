# phlp
phlp - a toy lisp in PHP

Inspired by [Lisp in Your Language](http://danthedev.com/2015/09/09/lisp-in-your-language/), where the author writes a program to pass arrays as list-like structures in JavaScript, I decided to do something similar in PHP, as a challenge to try to learn a little bit more about LISP dialects.

In case its not entirely clear, much like its inspiration, this is a toy used for learning -- never, ever use in production. :)

The syntax ends up looking like this:

```
<?php lisp(['do', /* do will evaluate many statements in sequence */

/* math */
['-', 5, 3],
// => 2

['+', 1, 2, 3, 4],
// => 10

/* supports concat operator */
['.', 'foo', 'bar', 'baz'],
// => 'foobarbaz'

/* supports PHP functions */
['var_dump', ['implode', ' - ', ['bacon', 'gravy', 'eggs']]],
// => string(20) "bacon - gravy - eggs"

/* note, like operators, functions try to reduce or to operate on every member of an array by default */
/* 'puts' is shorthand for printf, but trys to stringify and print arrays as well */
['puts', ['implode', ' - ', ['strrev', 'bacon', 'gravy', 'eggs']]],
// => 'nocab - yvarg - sgge'

/* equality */
['if', ['=', 4, 3],
	['puts','yep'],
	['puts','nope']],
// => 'nope'

/* def is for naming vars, fn is for anonymous functions, defn is shorthand for both. */

['def', 'a', 2],
['puts', ['+', 'a', 5]],
// => 7

['def', 'greet',
	['fn', ['planet', 'greeting'],
	['puts', 'greeting', 'planet']]],

['greet', 'world', 'hello'],
// => 'hello'
// => 'world'

['defn', 'yell', 
	['greeting', 'planet'],
	['puts', ['.', ['implode', ', ', ['strtoupper', 'greeting', 'planet']], '!!!']]],
		
['yell', 'hello', 'world'],
// => 'HELLO WORLD!!!'

]);
```

### Running it

Right now, not using composer or anything like that, so you have to simply pass the filename you want to interpret in as an argument. 

For example, I run tests by running:

    php index.php tests
    
On the command line. Note the filename shouldn't have an extension.

Just to clarify, if your path looked something like `/path/to/my/file/where/i/call/the/lisp/function`, that's what you could pass in. Or, if you wanted you could simply include index.php from this repo in your code, and use "lisp()" anywhere after that.
