# phlp
phlp - a toy lisp in PHP

Inspired by [Lisp in Your Language](http://danthedev.com/2015/09/09/lisp-in-your-language/), where the author writes a program to pass arrays as list-like structures in JavaScript, I decided to do something similar in PHP, as a challenge to try to learn a little bit more about LISP dialects.

In case its not entirely clear, much like its inspiration, this is a toy used for learning -- never, ever use in production. :)

The syntax ends up looking like this:

```
<?php phlp(['do', /* do will evaluate many statements in sequence */

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

### How to use

Include `index.php` in your script, and invoke using the `phlp()` function call.

An alternative usage is the repl, which loads an interactive shell with the script preloaded.

You can run this "repl" from the root of the repo like so:

    ./bin/repl
    
.