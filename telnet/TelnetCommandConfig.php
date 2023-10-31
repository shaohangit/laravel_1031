<?php

namespace Telnet;

class TelnetCommandConfig {
    private const CONFIG = [
        'mul' => [InterviewQuestions::class, 'mul'],
        'incr' => [InterviewQuestions::class, 'incr'],
        'div' => [InterviewQuestions::class, 'div'],
        'conv_tree' => [InterviewQuestions::class, 'convTree'],
    ];

    public static function getCommand(string $commandName): array {
        return self::CONFIG[$commandName] ?? [];
    }
}