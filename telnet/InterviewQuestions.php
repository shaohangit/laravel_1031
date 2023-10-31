<?php

namespace Telnet;

class InterviewQuestions extends TelnetBase {
    public function mul(): string {
        return count($this->params) == 2 ? intval($this->params[0]) * $this->params[1] : 0;
    }

    public function incr(): string {
        return count($this->params) == 1 ? (intval($this->params[0]) + 1) : 0;
    }

    public function div(): float {
        return count($this->params) == 2 ? intval($this->params[0]) / intval($this->params[1]) : 0;
    }

    public function convTree(): string {
        return count($this->params) == 1 ? json_encode((new ConvTree($this->params[0]))->getTree()) : '';
    }
}