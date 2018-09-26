# php-archserv

Simple and forgiving ArchServ parser for PHP

## Examples

```php
// Create parser
$parser = new \ArchServ\Parser();

// Parse input text to node groups
$groups = parser->parse($text);

// Iterate over groups and nodes
foreach ($parser->parse($text) as $group) {
  foreach ($group->nodes as $node) {
    echo sprintf("%s %s\n", $node->sortkey(), $node->code);
  }
}

// Dump groups in native archserv format
foreach ($parser->parse($text) as $group) {
  echo $group;
}
```
