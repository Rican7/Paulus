<?php

$this->get(
    '/?',
    function () {
        echo 'posts';
    }
);

$this->get(
    '/[i:id]?',
    function () {
        echo 'a post';
    }
);
