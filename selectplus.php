<?php

if(!function_exists('panel')) return;

$kirby->set('field', 'selectplus', __DIR__ . DS . 'fields' . DS . 'selectplus');


function array_map_assoc($callback, $arr) {
   $remapped = array();

   foreach($arr as $k => $v)
      $remapped += $callback($k, $v);

   return $remapped;
}
