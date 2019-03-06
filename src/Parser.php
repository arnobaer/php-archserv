<?php namespace ArchServ;

/*
 * Simple ArchServ parser for PHP
 * Copyright (C) 2016-2019  Bernhard Arnold
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

// ArchServ format parser
class Parser {

	// Regex capture groups
	// 2: context
	// 3: group
	// 4: type
	// 5: index
	// 6: x
	// 7: y
	// 8: z
	// 10: code
	const RegExPattern = '%(?m)^(:?(\d\d\d\d)([A-Z])(\d\d)(\d\d\d)\s+)?(\-?\d+\.\d+)\s+(\-?\d+\.\d+)\s+(\-?\d+\.\d+)(:?\s+([\-\w]+))?\s*$%';

	// Mapping node to GeoJson geometry type
	const GeoJsonTypes = array(
		Node::Nail => 'MultiPoint',
		Node::Height => 'MultiPoint',
		Node::SinglePoint => 'MultiPoint',
		Node::LineOpen => 'MultiLineString',
		Node::LineClosed => 'MultiPolygon',
		Node::SplineOpen => 'MultiLineString',
		Node::SplineClosed => 'MultiPolygon',
		Node::Arch => 'MultiPoint',
		Node::SingleFind => 'MultiPoint',
		Node::PhotogrammetryPoint => 'MultiPoint',
		Node::FixedPoint => 'MultiPoint',
		Node::BorderOpen => 'MultiLineString',
		Node::BorderClosed => 'MultiPolygon'
	);

	// Parse Arch Serv compatible input,
	// returns list of node groups.
	public function parse($text, $projection=null) {
		// TODO optional coordinate projection passing a proj4php object
		$nodes = $this->parse_nodes($text);
		return $this->group_nodes($nodes);
	}

	public function parse_tree($text, $projection=null) {
		$groups = $this->parse($text, $projection);
		$contexts = array();
		foreach ($groups as $group) {
			$context = $group->context;
			$type = $group->type;
			if (!array_key_exists($context, $contexts)) {
				$contexts[$context] = array();
			}
			if (!array_key_exists($type, $contexts[$context])) {
				$contexts[$context][$type] = array();
			}
			$contexts[$context][$type][] = $group;
		}
		return $contexts;
	}

	public function parse_geojson($text, $projection=null) {
		$contexts = $this->parse_tree($text, $projection);
		$features = array();
		foreach ($contexts as $context => $types) {
			foreach ($types as $type => $groups) {
				$geometryType = self::GeoJsonTypes[$type];
				$coordinates = array();
				switch ($geometryType) {
					case 'MultiPoint': {
						foreach ($groups as $group) {
							foreach ($group as $node) {
								$coordinates[] = array($node->x, $node->y, $node->z);
							}
						}
					} break;
					case 'MultiLineString': {
						foreach ($groups as $group) {
							$block = array();
							foreach ($group as $node) {
								$block[] = array($node->x, $node->y, $node->z);
							}
							$coordinates[] = $block;
						}
					} break;
					case 'MultiPolygon': {
						foreach ($groups as $group) {
							$block = array();
							foreach ($group as $node) {
								$block[] = array($node->x, $node->y, $node->z);
							}
							if (sizeof($block)) {
								$block[] = $block[0];
							}
							$coordinates[] = array($block);
						}
					} break;
					default: throw new \ErrorException("unsupported type: {$geometryType}");
				} // switch
				$geometry = array(
					'type' => $geometryType,
					'coordinates' => $coordinates
				);
				$features[] = array(
					'type' => 'Feature',
					'geometry' => $geometry,
					'properties' => array(
						'context' => $node->context,
						'type' => $node->type,
						'code' => $node->code
					)
				);
			}
		}
		return array(
			'type' => 'FeatureCollection',
			'features' => $features
		);
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
				$context = (integer) $match[2],
				$group = $match[3],
				$type = (integer) $match[4],
				$index = (integer) $match[5],
				$x = (float) $match[6],
				$y = (float) $match[7],
				$z = (float) $match[8],
				$code = $match[10]
			);
		}
		return $nodes;
	}

	protected function group_nodes($nodes) {
		$groups = array();
		foreach ($nodes as $node) {
			$group = end($groups);
			// Create first group or create new group for differnt node
			if (false === $group || false === $group->match($node)) {
				$groups[] = new Group($node->context, $node->group, $node->type, $node->code);
				$group = end($groups);
			}
			$group->append($node);
		}
		return $groups;
	}

} // class Parser
