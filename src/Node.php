<?php namespace ArchServ;

/*
 * Simple and forgiving ArchServ parser for PHP
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

// Represents a single node/line of ArchServ format
class Node {

	// Valid IDs
	const MeasurePoint = 0;
	const Height = 1;
	const SinglePoint = 11;
	const LineOpen = 2;
	const LineClosed = 3;
	const SplineOpen = 4;
	const SplineClosed = 5;
	const Arch = 6;
	const SingleFind = 71;
	const PhotogrammetryPoint = 81;
	const FixedPoint = 91;
	const BorderOpen = 92;
	const BorderClosed = 93;

	public $context;
	public $object;
	public $id;
	public $index;
	public $x;
	public $y;
	public $z;
	public $code;

	public function __construct($context, $object, $id, $index, $x, $y, $z, $code) {
		$this->context = (int) $context;
		$this->object = $object;
		$this->id = (int) $id;
		$this->index = (int) $index;
		$this->x = (float) $x;
		$this->y = (float) $y;
		$this->z = (float) $z;
		$this->code = $code;
	}

	// Returns string key for sorting
	public function sortkey() {
		return sprintf("%04d%s%02d%03d", $this->context, $this->object, $this->id, $this->index);
	}

	// Returns string representation
	public function __toString() {
		return sprintf("%s\t\t%.3f\t%.3f\t%.3f\t%s", $this->sortkey(), $this->x, $this->y, $this->z, $this->code);
	}

} // class Node
