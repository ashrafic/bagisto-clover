<?php

return [
    'description' => 'Pague com segurança com seu cartão de crédito/débito via Clover.',
    'title' => 'Clover',
    'shipping' => 'Frete',
    'tax' => 'Imposto',
    'discount' => 'Desconto',

    'response' => [
        'cart-not-found' => 'Carrinho não encontrado ou inválido.',
        'cart-processed' => 'Este carrinho já foi processado.',
        'invalid-session' => 'Sessão de pagamento  inválida.',
        'payment-cancelled' => 'Pagamento foi cancelado.',
        'payment-failed' => 'Pagamento falhou.',
        'payment-success' => 'Pagamento concluído com sucesso.',
        'provide-credentials' => 'Por favor, forneça credenciais Clover válidas.',
        'session-invalid' => 'Sessão de pagamento expirou ou é inválida.',
        'session-not-found' => 'Sessão de pagamento não encontrada.',
        'verification-failed' => 'Verificação de pagamento falhou.',
        'webhook-secret-missing' => 'A chave de assinatura do webhook não está configurada.',
    ],

    'configuration' => [
        'api-token' => 'Token de API',
        'api-test-token' => 'Token de API de teste',
        'clover' => 'Clover',
        'clover-info' => 'Aceite pagamentos pela página Clover Hosted Checkout. Gere um token de API de comércio eletrônico do tipo Hosted Checkout no Painel do Comerciante Clover e registre a URL do webhook da loja (https://your-store.com/clover/webhook) na página de configurações do Hosted Checkout junto com a chave de assinatura.',
        'merchant-id' => 'ID do Comerciante',
        'page-config-uuid' => 'UUID de configuração da página',
        'test-merchant-id' => 'ID do comerciante de teste',
        'webhook-secret' => 'Chave de assinatura do webhook',
        'webhook-secret-information' => 'Insira a chave de assinatura gerada para a URL do webhook configurada na página de configurações do Hosted Checkout do Painel do Comerciante Clover.',
    ],
];
