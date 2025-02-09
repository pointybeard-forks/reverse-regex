# Reverse Regex

Use Regular Expressions to generate strings.

- [Reverse Regex](#reverse-regex)
  - [Installation](#installation)
  - [Usage](#usage)
    - [Notes When Writing Regular Expressions](#notes-when-writing-regular-expressions)
    - [Supported Syntax](#supported-syntax)
  - [About](#about)
    - [Requirements](#requirements)
  - [Support](#support)
  - [Author](#author)
  - [Acknowledgments](#acknowledgments)
  - [License](#license)

## Installation

This library is installed via [Composer](http://getcomposer.org/). To install, use `composer require pointybeard/reverse-regex` in your application.

## Usage

```php
<?php

declare(strict_types=1);

use pointybeard\ReverseRegex\Lexer;
use pointybeard\ReverseRegex\Random\SimpleRandom;
use pointybeard\ReverseRegex\Parser;
use pointybeard\ReverseRegex\Generator\Scope;

require "vendor/autoload.php";

$pattern = "[a-z0-9]{10}"; // 10 random letters and numbers

$lexer = new Lexer($pattern);
$random = new SimpleRandom();
$parser = new Parser($lexer, new Scope(), new Scope());
$generator = $parser->parse()->getResult();

$result = '';

$parser = new Parser($lexer,new Scope(),new Scope());

var_dump($generator->generate($result, $random));
// string(10) "j2ydisgoks"

```

See <https://github.com/pointybeard-forks/reverse-regex/tree/master/examples> for more examples.

### Notes When Writing Regular Expressions

1. Escape all meta-characters i.e. if you need to escape the character in a regex you will need to escape here.
2. Not all meta-characters are suppported see list below.
3. Use `\X{####}` to specify unicode value use `[\X{####}-\X{####}]` to specify range.
4. Unicdoe `\p` not supported, I could not find a port of [UCD](http://www.unicode.org/ucd/) to php, maybe in the future support be added.
5. Quantifiers are applied to left most group, literal or character class.
6. Beware of the `+` and `*` quantifers they apply a possible maxium number of occurances up to `PHP_INT_MAX`.

### Supported Syntax

| Example  | Description | Resulting String |
| ------------- | ------------- | ------------- |
| `(abcf)` | Support literals this would generate string | 'abcf' |
| `\((abcf)\)` | Escape meta characters as you normally would in a regex | '(abcf)' |
| `[a-z]` | Character Classes are supported | 'a' |
| `a{5}` | Quantifiers supported always last group or literal or character class | 'aaaaa' |
| `a{1,5}` | Range Quantifiers supported | 'aa' |
| `a\|b\|c` | Alternation supported pick one of three at random | 'b' |
| `a\|(y\|d){5}` | Groups supported with alternation and quantifiers | 'ddddd', 'a', or 'yyyyy' |
| `\d` | Digit shorthand equ [0-9] | '1' |
| `\w` | word character shorthand equ [a-zA-Z0-9_] | 'j' |
| `\W` | Non word character shorthand equ [^a-zA-Z0-9_] | 'j' |
| `\s` | White space shorthand ASCII only | ' ' |
| `\S` | Non White space shorthand ASCII only | 'i' |
| `.` | Dot all ASCII characters | '\$' |
| `* + ?` | Short hand quantifiers, recommend not use them |  |
| `\X{00FF}[\X{00FF}-\X{00FF}]` | Unicode ranges |  |
| `\xFF[\xFF-\xFF]` | Hex ranges |  |

## About

### Requirements

- This library works with PHP 8.2 or above.

## Support

If you believe you have found a bug, please report it using the [GitHub issue tracker][ext-issues].

## Author

- Alannah Kearney (<https://github.com/pointybeard>) - Fixed a few things after library was abandoned
- See also the list of [contributors][ext-contributor] who participated in this project

## Acknowledgments

This project is a fork of [ReverseRegex](https://github.com/icomefromthenet/ReverseRegex) by [icomefromthenet](https://github.com/icomefromthenet).

Special thanks to the original author for their foundational work on this library.

## License

"reverse-regex" is released under the MIT License. See [LICENCE][doc-LICENCE] for details.

[doc-LICENCE]: http://www.opensource.org/licenses/MIT
[ext-issues]: https://github.com/pointybeard-forks/reverse-regex/issues
[ext-contributor]: https://github.com/pointybeard-forks/reverse-regex/contributors
