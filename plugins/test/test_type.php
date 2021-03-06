<?php

$field_type = "test";

$field_types[$field_type]['edit'] = function($name, $schema, $value = null, $focus = false) {
   global $pdo;
   $focus = $focus ? 'autofocus' : '';
   $value = htmlspecialchars($value);
   $content = <<<HTML
   TEST<input type="text" name="$name" value="$value" placeholder="$name" $focus>
HTML;
   return $content;
};

$field_types[$field_type]['encode'] = function($schema, $value, $values, $iteration) {
   return $value;
};

$field_types[$field_type]['decode'] = function($schema, $value, $focus_link = '') {
   $value = htmlspecialchars($value);
   return <<<HTML
   <a href="$focus_link" class="text_dark">$value</a>
HTML;
};

$field_types[$field_type]['decode_list'] = function($schema, $value) {
   return htmlspecialchars($value);
};

$field_types[$field_type]['decode_raw'] = function($schema, $value, $focus_link = '') {
   return $value;
};