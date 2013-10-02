<?php

$this->get(
    '/?',
    function () {
        echo 'users';
    }
);

$this->get(
    '/[i:id]?',
    function () {
        echo 'a user';
    }
);
