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

// ArchServ foramt parser
class Parser
{
  const pattern = '%(?m)^(:?(\d\d\d\d)([A-Z])(\d\d)(\d\d\d)\s+)?(\-?\d+\.\d+)\s+(\-?\d+\.\d+)\s+(\-?\d+\.\d+)(:?\s+([\-\w]+))?\s*$%';

  // Regex capture groups
  // 2: context
  // 3: object
  // 4: id
  // 5: index
  // 6: x
  // 7: y
  // 8: z
  // 10: code

  // Parse Arch Serv compatible input,
  // returns list of node groups.
  public function parse($text)
  {
    preg_match_all($this::pattern, $text, $matches, PREG_SET_ORDER);

    // First pass, convert to nodes

    $nodes = array();

    foreach ($matches as &$match)
    {
      // Add new node
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

    // Second pass, sort nodes

    usort($nodes, function($a, $b)
      {
        $a_key = $a->sortkey();
        $b_key = $b->sortkey();
        if($a_key == $b_key)
          return 0;
        return $a_key < $b_key ? -1 : 1;
      }
    );

    // Third pass, group nodes

    $group = NULL;
    $results = array();

    foreach ($nodes as &$node)
    {
      if ($group === NULL)
      {
        $group = new Group($node->context, $node->object, $node->id);
        $group->nodes[] = $node;
      }
      else
      {
        if ($node->context != $group->context or
            $node->object != $group->object or
            $node->id != $group->id)
        {
          $results[] = $group;
          $group = new Group($node->context, $node->object, $node->id);
        }
        $group->nodes[] = $node;
      }
    }

    if ($group !== NULL)
    {
      $results[] = $group;
      $group = NULL;
    }

    return $results;
  }
} // class Parser
