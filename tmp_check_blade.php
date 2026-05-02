<?php
$path = __DIR__ . '/resources/views/livewire/task-dashboard.blade.php';
$lines = file($path);
$tokens = [];
$pattern = '/@(if|endif|forelse|endforelse)\b/';
foreach ($lines as $i => $line) {
    if (preg_match_all($pattern, $line, $m, PREG_SET_ORDER)) {
        foreach ($m as $mm) {
            $tokens[] = ['type' => $mm[1], 'line' => $i + 1, 'text' => trim($line)];
        }
    }
}
$stack = [];
foreach ($tokens as $t) {
    if ($t['type'] === 'if' || $t['type'] === 'forelse') {
        $stack[] = $t;
    } elseif ($t['type'] === 'endif') {
        if (count($stack) > 0) {
            array_pop($stack);
        } else {
            echo "Unmatched endif at {$t['line']}\n";
        }
    } elseif ($t['type'] === 'endforelse') {
        if (count($stack) > 0 && end($stack)['type'] === 'forelse') {
            array_pop($stack);
        } else {
            echo "Unmatched endforelse at {$t['line']}\n";
        }
    }
}
if (count($stack) === 0) {
    echo "All blocks matched\n";
} else {
    echo "Unmatched stack count: " . count($stack) . "\n";
    foreach ($stack as $s) {
        echo "Unmatched {$s['type']} at line {$s['line']}: {$s['text']}\n";
    }
}

// Print token list for debugging
echo "\nTokens list:\n";
foreach ($tokens as $i => $t) {
    echo ($i + 1) . ": {$t['type']} at {$t['line']}: {$t['text']}\n";
}
