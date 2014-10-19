<?php
//wedusflare framework

$input = isset($_GET['name']) ? $_GET['name'] : 'World';
printf('Hello %s', $input);
