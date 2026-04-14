<?php
$name = "john";

if (!is_string($name) || !preg_match('/^[a-zA-Z\s\-\']+$/', $name)) {
    echo "FAILED REGEX\n";
} else {
    echo "PASSED REGEX\n";
}

$name = "luc123";
if (!is_string($name) || !preg_match('/^[a-zA-Z\s\-\']+$/', $name)) {
    echo "FAILED REGEX (expected for luc123)\n";
} else {
    echo "PASSED REGEX (unexpected for luc123)\n";
}
