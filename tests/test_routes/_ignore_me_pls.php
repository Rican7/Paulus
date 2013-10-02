<?php

$this->get(
    '/?',
    function () {
        throw new \Exception('bad!');
    }
);
