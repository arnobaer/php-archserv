# php-archserv

Simple ArchServ parser for PHP

## Examples

```php
// Create parser
$parser = new \ArchServ\Parser();

// Parse input text to node groups
$groups = parser->parse($text);

// Iterate over groups and nodes
foreach ($groups as $group) {
  foreach ($group as $node) {
    echo sprintf("%s %s\n", $node->sortkey(), $node->code);
  }
}

// Dump groups in native archserv format
foreach ($groups as $group) {
  echo $group;
}
```

## Installation

### Using composer

Append the repository and requirement to your project ```composer.json```

```json
{
    "repositories": [
        {
            "url": "https://github.com/arnobaer/php-archserv.git",
            "type": "git"
        }
    ],
    "require": {
        "arnobaer/php-archserv": "~1.2"
    }
}
```

Install using ```composer``` (providing PSR-4 autoloading).

```bash
composer update
```

### Using git repo

```bash
git clone https://github.com/arnobaer/php-archserv.git
```

```php
include 'path/to/php-archserv/src/Parser.php';
```

License
=======

The php-archserv project is licensed under the GNU General Public License Version 3.
