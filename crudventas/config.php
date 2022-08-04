<?php
// Config database params
return [
  'db' => [
    'host' => 'localhost',
    'user' => 'root',
    'pass' => '',
    'name' => 'ventas',
    'options' => [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]
  ]
];
