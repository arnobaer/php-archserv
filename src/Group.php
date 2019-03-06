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

// Represents a group of nodes (lines, points...)
class Group implements \Iterator {

	public $context;
	public $group;
	public $type;
	public $nodes;

	private $position = 0;

	public function __construct($context, $group, $type, $code) {
		$this->context = $context;
		$this->group = $group;
		$this->type = $type;
		$this->code = $code;
		$this->nodes = array();
	}

	public function append(Node $node) {
		$this->nodes[] = $node;
	}

	// Returns true if context, group and type of node matches with group.
	public function match(Node $node) {
		if ($node->context != $this->context)
			return false;
		if ($node->group != $this->group)
			return false;
		if ($node->type != $this->type)
			return false;
		return true;
	}

	public function rewind() {
		$this->position = 0;
	}

	public function current() {
		return $this->nodes[$this->position];
	}

	public function key() {
		return $this->position;
	}

	public function next() {
		++$this->position;
	}

	public function valid() {
		return isset($this->nodes[$this->position]);
	}

	// Returns string representation
	public function __toString() {
		return join(PHP_EOL, $this->nodes);
	}

} // class Group
