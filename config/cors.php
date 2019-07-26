<?php
return [
    /*
    |--------------------------------------------------------------------------
    | Laravel CORS
    |--------------------------------------------------------------------------
    |
    | allowedOrigins, allowedHeaders and allowedMethods can be set to array('*')
    | to accept any value.
    |
    */
   'supportsCredentials' => false,
   'allowedOrigins' => ['*'],
   'allowedHeaders' => ['Content-Type', 'X-Requested-With','X-Api-Key', 'Authorization'],
   'allowedMethods' =>  ['GET', 'POST', 'PUT',  'DELETE'], // ex: ['GET', 'POST', 'PUT',  'DELETE']
   'exposedHeaders' => [],
   'maxAge' => 0,
];