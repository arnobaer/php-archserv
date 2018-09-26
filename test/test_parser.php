<?php

$root = dirname(dirname(__FILE__));

require_once "$root/src/Group.php";
require_once "$root/src/Node.php";
require_once "$root/src/Parser.php";

function assert_equal($lhs, $rhs) {
	if ($lhs != $rhs) {
		throw ErrorException("ASSERT EQUAL falied ($lhs != $rhs)");
	}
}

$parser = new \ArchServ\Parser();

$text = "
1000A11042 -12.287349 -37.110092 2062.000 QMP
2000B03001 -12.28931039571762 -37.108962939519266 2062.000 AH
2000B03002 -12.289511561393738 -37.10904636178686 2062.000 AH
2000B03003 -12.289530336856842 -37.109193954804425 2062.000 AH
2000B03004 -12.289377450942993 -37.10925170851561 2062.000 AH
2000B03005 -12.289154827594757 -37.10926026461722 2062.000 AH
2000B03006 -12.288680076599121 -37.10923031825734 2062.000 AH
2000B03007 -12.288782000541687 -37.10899930308311 2062.000 AH
2000B03008 -12.288881242275238 -37.1088281802775 2062.000 AH
2000B03009 -12.289015352725983 -37.10869769887841 2062.000 AH
2000B03010 -12.289259433746338 -37.10864208378752 2062.000 AH
2000B03011 -12.28940963745117 -37.10871695024651 2062.000 AH
2000B03012 -12.289431095123291 -37.10884743161243 2062.000 AH
";

$results = $parser->parse($text);

foreach ($results as $group) {
	echo "$group\n";
}

$node = $results[0]->nodes[0];

print_r($node);

assert_equal(1000, $node->context);
assert_equal('A', $node->object);
assert_equal(11, $node->id);
assert_equal(42, $node->index);
assert_equal(-12.287349, $node->x);
assert_equal(-37.110092, $node->y);
assert_equal(2062.0, $node->z);
assert_equal('QMP', $node->code);

echo "done.\n";
