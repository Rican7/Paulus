<?php

return function () {
    return [
        'connections' => array(
            'development' => 'mysql://username:password@localhost/development',
            'production' => 'mysql://username:password@localhost/production',
            'test' => 'mysql://username:password@localhost/test',
        ),
        'default_connection' => 'test',
    ];
};
