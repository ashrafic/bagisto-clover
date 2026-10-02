<?php

return [
    'description' => 'Pague de forma segura con su tarjeta de crédito/débito a través de Clover.',
    'title' => 'Clover',
    'shipping' => 'Envío',
    'tax' => 'Impuesto',
    'discount' => 'Descuento',

    'response' => [
        'cart-not-found' => 'Carrito no encontrado o inválido.',
        'cart-processed' => 'Este carrito ya ha sido procesado.',
        'invalid-session' => 'La sesión de pago es inválida.',
        'payment-cancelled' => 'El pago fue cancelado.',
        'payment-failed' => 'Error en el pago.',
        'payment-success' => 'Pago completado exitosamente.',
        'provide-credentials' => 'Por favor proporcione credenciales de Clover válidas.',
        'session-invalid' => 'La sesión de pago ha expirado o es inválida.',
        'session-not-found' => 'Sesión de pago no encontrada.',
        'verification-failed' => 'La verificación del pago falló.',
        'webhook-secret-missing' => 'El secreto de firma del webhook no está configurado.',
    ],

    'configuration' => [
        'clover' => 'Clover',
        'clover-info' => 'Acepte pagos a través de la página de Clover Hosted Checkout. Genere un token de API de comercio electrónico del tipo Hosted Checkout en el panel de comerciantes de Clover y registre la URL del webhook de su tienda (https://your-store.com/clover/webhook) en la página de configuración de Hosted Checkout junto con su secreto de firma.',
        'merchant-id' => 'ID de comerciante',
        'page-config-uuid' => 'UUID de configuración de página',
        'test-merchant-id' => 'ID de comercio de prueba',
        'webhook-secret' => 'Secreto de firma del webhook',
        'webhook-secret-information' => 'Introduzca el secreto de firma generado para la URL del webhook configurada en la página de configuración de Hosted Checkout del panel de comerciantes de Clover.',
    ],
];
