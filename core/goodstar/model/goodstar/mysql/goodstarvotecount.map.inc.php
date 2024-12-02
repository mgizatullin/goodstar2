<?php
$xpdo_meta_map['goodStarVoteCount']= array (
  'package' => 'goodstar',
  'version' => '1.1',
  'table' => 'goodstar_vote_count',
  'extends' => 'xPDOSimpleObject',
  'tableMeta' => 
  array (
    'engine' => 'InnoDB',
  ),
  'fields' => 
  array (
    'thread' => '',
    'count' => 0.0,
    'countaverage' => 0,
  ),
  'fieldMeta' => 
  array (
    'thread' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'count' => 
    array (
      'dbtype' => 'decimal',
      'precision' => '10,7',
      'phptype' => 'float',
      'null' => false,
      'default' => 0.0,
    ),
    'countaverage' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'int',
      'null' => false,
      'default' => 0,
    ),
  ),
  'indexes' => 
  array (
    'thread' => 
    array (
      'alias' => 'thread',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'thread' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
  'composites' => 
  array (
    'goodStarVoteCount' => 
    array (
      'class' => 'goodStarVote',
      'local' => 'thread',
      'foreign' => 'thread',
    ),
  ),
  'aggregates' => 
  array (
    'modResource' => 
    array (
      'class' => 'modResource',
      'local' => 'thread',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
