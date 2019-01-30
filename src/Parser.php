<?php namespace ArchServ;

/*
 * Simple ArchServ parser for PHP
 * Copyright (C) 2016-2018  Bernhard Arnold
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

// ArchServ foramt parser
class Parser {

	// Regex capture groups
	// 2: context
	// 3: object
	// 4: id
	// 5: index
	// 6: x
	// 7: y
	// 8: z
	// 10: code
	const RegExPattern = '%(?m)^(:?(\d\d\d\d)([A-Z])(\d\d)(\d\d\d)\s+)?(\-?\d+\.\d+)\s+(\-?\d+\.\d+)\s+(\-?\d+\.\d+)(:?\s+([\-\w]+))?\s*$%';

	// Parse Arch Serv compatible input,
	// returns list of node groups.
	public function parse($text) {
		$nodes = $this->parse_nodes($text);
		$this->sort_nodes($nodes);
		return $this->group_nodes($nodes);
	}

	protected function parse_matches($text) {
		preg_match_all(self::RegExPattern, $text, $matches, PREG_SET_ORDER);
		return $matches;
	}

	protected function parse_nodes($text) {
		$nodes = array();
		foreach ($this->parse_matches($text) as &$match) {
			// Append new node
			$nodes[] = new Node(
				$context = $match[2],
				$object = $match[3],
				$id = $match[4],
				$index = $match[5],
				$x = $match[6],
				$y = $match[7],
				$z = $match[8],
				$code = $match[10]
			);
		}
		return $nodes;
	}

	protected function sort_nodes($nodes) {
		usort($nodes, function($a, $b) {
			$a_key = $a->sortkey();
			$b_key = $b->sortkey();
			if ($a_key == $b_key) {
				return 0;
			}
			return $a_key < $b_key ? -1 : 1;
		});
	}

	protected function group_nodes($nodes) {
		$group = NULL;
		$results = array();
		foreach ($nodes as &$node) {
			if (NULL === $group) {
				$group = new Group($node->context, $node->object, $node->id);
				$group->nodes[] = $node;
			} else {
				if ($node->context != $group->context ||
						$node->object != $group->object ||
						$node->id != $group->id) {
					$results[] = $group;
					$group = new Group($node->context, $node->object, $node->id);
				}
				$group->nodes[] = $node;
			}
		}
		if (NULL !== $group) {
			$results[] = $group;
			$group = NULL;
		}
		return $results;
	}

} // class Parser
