<?php namespace ArchServ;

/*
 * Simple and forgiving ArchServ parser for PHP
 * Copyright (C) 2016  Bernhard Arnold
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
class Group
{
  public function __construct($context, $object, $id, $nodes = array())
  {
    $this->context = $context;
    $this->object = $object;
    $this->id = $id;
    $this->nodes = $nodes;
  }
} // class Group
