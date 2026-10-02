<?php

return [
    'description' => 'Pagueu de manera segura amb la vostra targeta de crèdit/dèbit mitjançant Clover.',
    'title' => 'Clover',
    'shipping' => 'Enviament',
    'tax' => 'Impost',
    'discount' => 'Descompte',

    'response' => [
        'cart-not-found' => 'Carret   no trobat o invàlid.',
        'cart-processed' => 'Aquest carretó ja ha estat processat.',
        'invalid-session' => 'La sessió de pagament no és vàlida.',
        'payment-cancelled' => 'El pagament ha estat cancel·lat.',
        'payment-failed' => 'El pagament ha fallat.',
        'payment-success' => 'Pagament completat amb èxit.',
        'provide-credentials' => 'Si us plau, proporcioneu credencials vàlides de Clover.',
        'session-invalid' => 'La sessió de pagament ha expirat o no és vàlida.',
        'session-not-found' => 'Sessió de pagament no trobada.',
        'verification-failed' => 'La verificació del pagament ha fallat.',
        'webhook-secret-missing' => 'La clau de signatura del webhook no està configurada.',
    ],

    'configuration' => [
        'clover' => 'Clover',
        'clover-info' => 'Accepteu pagaments mitjançant la pàgina Clover Hosted Checkout. Genereu un token d\\\'API de comerç electrònic del tipus Hosted Checkout al tauler de comerciants de Clover i registreu l\\\'URL del webhook de la botiga (https://your-store.com/clover/webhook) a la pàgina de configuració de Hosted Checkout juntament amb la clau de signatura.',
        'merchant-id' => 'ID de comerciant',
        'page-config-uuid' => 'UUID de configuració de pàgina',
        'test-merchant-id' => 'ID de comerç de prova',
        'webhook-secret' => 'Clau de signatura del webhook',
        'webhook-secret-information' => 'Introduïu la clau de signatura generada per a l\\\'URL del webhook configurat a la pàgina de configuració de Hosted Checkout del tauler de comerciants de Clover.',
    ],
];
