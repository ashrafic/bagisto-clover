<?php

return [
    'description' => 'Paga in modo sicuro con la tua carta di credito/debito tramite Clover.',
    'title' => 'Clover',
    'shipping' => 'Spedizione',
    'tax' => 'Imposta',
    'discount' => 'Sconto',

    'response' => [
        'cart-not-found' => 'Carrello non trovato o non valido.',
        'cart-processed' => 'Questo carrello è già stato elaborato.',
        'invalid-session' => 'La sessione di pagamento non è valida.',
        'payment-cancelled' => 'Il pagamento è stato annullato.',
        'payment-failed' => 'Pagamento fallito.',
        'payment-success' => 'Pagamento completato con successo.',
        'provide-credentials' => 'Si prega di fornire credenziali Clover valide.',
        'session-invalid' => 'La sessione di pagamento è scaduta o non è valida.',
        'session-not-found' => 'Sessione di pagamento non trovata.',
        'verification-failed' => 'Verifica del pagamento fallita.',
        'webhook-secret-missing' => 'La chiave di firma del webhook non è configurata.',
    ],

    'configuration' => [
        'clover' => 'Clover',
        'clover-info' => 'Accetta pagamenti tramite la pagina Clover Hosted Checkout. Genera un token API e-commerce di tipo Hosted Checkout dalla dashboard mercantile Clover e registra l\\\'URL del webhook del negozio (https://your-store.com/clover/webhook) sulla pagina delle impostazioni di Hosted Checkout insieme alla chiave di firma.',
        'merchant-id' => 'ID Commerciante',
        'page-config-uuid' => 'UUID configurazione pagina',
        'test-merchant-id' => 'ID esercente di prova',
        'webhook-secret' => 'Chiave di firma del webhook',
        'webhook-secret-information' => 'Immettere la chiave di firma generata per l\\\'URL del webhook configurato nella pagina delle impostazioni di Hosted Checkout della dashboard mercantile Clover.',
    ],
];
