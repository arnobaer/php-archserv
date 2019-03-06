<?php

$root = dirname(dirname(__FILE__));

require_once "$root/src/Group.php";
require_once "$root/src/Node.php";
require_once "$root/src/Parser.php";

function assert_equal($lhs, $rhs) {
	if ($lhs != $rhs) {
		throw new ErrorException("ASSERT EQUAL falied ($lhs != $rhs)");
	}
}

function assert_node($node, $context, $group, $type, $index, $x, $y, $z, $code) {
	assert_equal($context, $node->context);
	assert_equal($group, $node->group);
	assert_equal($type, $node->type);
	assert_equal($index, $node->index);
	assert_equal($x, $node->x);
	assert_equal($y, $node->y);
	assert_equal($z, $node->z);
	assert_equal($code, $node->code);
}

$parser = new \ArchServ\Parser();

$text = "
# invalid lines and station points will be ignored
SP1\t\t-12.289377450942993\t-37.10899930308311\t2064.042
1000A11042\t\t-12.287349\t-37.110092\t2062.001\tQMP
2001A01001 -12.2893103957 -37.1088260412 0.0 HH
2001A01002 -12.2892326117 -37.1087447578 0.0 HH
2001A01003 -12.2891038656 -37.1088410145 0.0 HH
2001A01004 -12.2892004251 -37.1088944904 0.0 HH
2001A01005 -12.2889482975 -37.1089051856 0.0 HH
2001A01006 -12.2890770435 -37.1089821908 0.0 HH
2001A01007 -12.2892433405 -37.1090292495 0.0 HH
2001A01008 -12.2894337773 -37.1091383401 0.0 HH
2001A01009 -12.2892916203 -37.1091490352 0.0 HH
2001A01010 -12.2891682386 -37.1092260402 0.0 HH
2001A01011 -12.2890716791 -37.1090741692 0.0 HH
2001A01012 -12.2889268398 -37.1090271105 0.0 HH
2001A01013 -12.2888061404 -37.1091896767 0.0 HH
2001A01014 -12.288916111 -37.109136201 0.0 HH
2001A01015 -12.289031446 -37.1091832597 0.0 HH
2000B03001 -12.28931039571762 -37.108962939519266 2061.120 AH
2000B03002 -12.289511561393738 -37.10904636178686 2062.320 AH
2000B03003 -12.289530336856842 -37.109193954804425 2063.000 AH
2000B03004 -12.289377450942993 -37.10925170851561 2063.743 AH
2000B03005 -12.289154827594757 -37.10926026461722 2062.040 AH
2000B03006 -12.288680076599121 -37.10923031825734 2063.854 AH
2000B03007 -12.288782000541687 -37.10899930308311 2064.042 AH
2000C03001 -12.288881242275238 -37.1088281802775 2062.134 AH
2000C03002 -12.289015352725983 -37.10869769887841 2065.844 AH
2000C03003 -12.289259433746338 -37.10864208378752 2063.831 AH
2000C03004 -12.28940963745117 -37.10871695024651 2064.019 AH
# this line will be ignored
2000C03005    -12.289431095123291     -37.10884743161243     2062.450    AH
";

$groups = $parser->parse($text);

echo "::iterating groups...\n";
foreach ($groups as $i => $group) {
	echo "::group[$i], context:{$group->context}, group:{$group->group}, type:{$group->type}\n";
	echo "$group\n";
}

echo "::iterating groups and nodes...\n";
foreach ($groups as $group) {
	foreach ($group as $node) {
		echo "$node\n";
	}
}

// Assert count of parsed groups
assert_equal(sizeof($groups), 4);

// Assert first node of first group
$node = $groups[0]->nodes[0];
print_r($node);
assert_node($node, 1000, 'A', \ArchServ\Node::SinglePoint, 42, -12.287349, -37.110092, 2062.001, 'QMP');

// Assert last node of last group
$node = $groups[3]->nodes[4];
print_r($node);
assert_node($node, 2000, 'C', \ArchServ\Node::LineClosed, 5, -12.289431095123291, -37.10884743161243, 2062.450, 'AH');

print_r($parser->parse_tree($text));

$geojson = $parser->parse_geojson($text);
print_r($geojson);

echo json_encode($geojson) . PHP_EOL;

echo "done.\n";
