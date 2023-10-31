<?php

namespace Telnet;

class TelnetBase {
    protected array $params;

    public function __construct(array $params) {
        $this->params = $params;
    }
}