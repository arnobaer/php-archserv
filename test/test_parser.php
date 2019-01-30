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
# invalid lines and station points will be ignored
SP1\t\t-12.289377450942993\t-37.10899930308311\t2064.042
1000A11042\t\t-12.287349\t-37.110092\t2062.001\tQMP
2000B03001 -12.28931039571762 -37.108962939519266 2061.120 AH
2000B03002 -12.289511561393738 -37.10904636178686 2062.320 AH
2000B03003 -12.289530336856842 -37.109193954804425 2063.000 AH
2000B03004 -12.289377450942993 -37.10925170851561 2063.743 AH
2000B03005 -12.289154827594757 -37.10926026461722 2062.040 AH
2000B03006 -12.288680076599121 -37.10923031825734 2063.854 AH
2000B03007 -12.288782000541687 -37.10899930308311 2064.042 AH
2000B03008 -12.288881242275238 -37.1088281802775 2062.134 AH
2000B03009 -12.289015352725983 -37.10869769887841 2065.844 AH
2000B03010 -12.289259433746338 -37.10864208378752 2063.831 AH
2000B03011 -12.28940963745117 -37.10871695024651 2064.019 AH
2000B03012 -12.289431095123291 -37.10884743161243 2062.450 AH
";

$results = $parser->parse($text);

echo "::iterating groups\n";
foreach ($results as $group) {
	echo "$group\n";
}

echo "::iterating groups and nodes\n";
foreach ($results as $group) {
	foreach ($group as $node) {
		echo "$node\n";
	}
}

$node = $results[0]->nodes[0];

print_r($node);

assert_equal(1000, $node->context);
assert_equal('A', $node->object);
assert_equal(11, $node->id);
assert_equal(42, $node->index);
assert_equal(-12.287349, $node->x);
assert_equal(-37.110092, $node->y);
assert_equal(2062.001, $node->z);
assert_equal('QMP', $node->code);

echo "done.\n";
