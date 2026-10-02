<?php

return [
    'description' => 'Payez en toute sécurité avec votre carte de crédit/débit via Clover.',
    'title' => 'Clover',
    'shipping' => 'Livraison',
    'tax' => 'Taxe',
    'discount' => 'Remise',

    'response' => [
        'cart-not-found' => 'Panier introuvable ou invalide.',
        'cart-processed' => 'Ce panier a déjà été traité.',
        'invalid-session' => 'La session de paiement est invalide.',
        'payment-cancelled' => 'Le paiement a été annulé.',
        'payment-failed' => 'Échec du paiement.',
        'payment-success' => 'Paiement effectué avec succès.',
        'provide-credentials' => 'Veuillez fournir des identifiants Clover valides.',
        'session-invalid' => 'La session de paiement a expiré ou est invalide.',
        'session-not-found' => 'Session de paiement introuvable.',
        'verification-failed' => 'La vérification du paiement a échoué.',
        'webhook-secret-missing' => 'La clé de signature du webhook n\'est pas configurée.',
    ],

    'configuration' => [
        'clover' => 'Clover',
        'clover-info' => 'Acceptez les paiements via la page Clover Hosted Checkout. Générez un jeton d\\\'API e-commerce de type Hosted Checkout depuis le tableau de bord marchand Clover et enregistrez l\\\'URL du webhook de la boutique (https://your-store.com/clover/webhook) sur la page des paramètres Hosted Checkout avec sa clé de signature.',
        'merchant-id' => 'ID marchand',
        'page-config-uuid' => 'UUID de configuration de page',
        'test-merchant-id' => 'ID marchand de test',
        'webhook-secret' => 'Clé de signature du webhook',
        'webhook-secret-information' => 'Saisissez la clé de signature générée pour l\\\'URL du webhook configurée sur la page des paramètres Hosted Checkout du tableau de bord marchand Clover.',
    ],
];
