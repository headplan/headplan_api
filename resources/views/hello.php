<?php
$input = $request->get('name', 'World');
echo "Hello " . htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
