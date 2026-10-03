<?php

use Webkul\Clover\Payment\Clover;
use Webkul\Core\Models\CoreConfig;

beforeEach(function () {
    $this->cloverConfigBackup = CoreConfig::where('code', 'like', 'sales.payment_methods.clover%')->get()->toArray();

    CoreConfig::where('code', 'like', 'sales.payment_methods.clover%')->delete();

    $this->clover = app(Clover::class);
});

afterEach(function () {
    CoreConfig::where('code', 'like', 'sales.payment_methods.clover%')->delete();

    if (! empty($this->cloverConfigBackup)) {
        CoreConfig::insert($this->cloverConfigBackup);
    }
});

it('returns the correct payment method code', function () {
    // Act
    $code = $this->clover->getCode();

    // Assert
    expect($code)->toBe('clover');
});

it('returns the payment method title from configuration', function () {
    // Arrange
    CoreConfig::factory()->create([
        'code' => 'sales.payment_methods.clover.title',
        'value' => 'Clover Payment Gateway',
        'channel_code' => 'default',
        'locale_code' => 'en',
    ]);

    // Act
    $title = $this->clover->getTitle();

    // Assert
    expect($title)->toBe('Clover Payment Gateway');
});

it('returns the payment method description from configuration', function () {
    // Arrange
    CoreConfig::factory()->create([
        'code' => 'sales.payment_methods.clover.description',
        'value' => 'Pay securely using Clover',
        'channel_code' => 'default',
        'locale_code' => 'en',
    ]);

    // Act
    $description = $this->clover->getDescription();

    // Assert
    expect($description)->toBe('Pay securely using Clover');
});

it('returns the API key based on sandbox mode', function () {
    // Arrange
    CoreConfig::factory()->create([
        'code' => 'sales.payment_methods.clover.sandbox',
        'value' => '1',
        'channel_code' => 'default',
    ]);

    CoreConfig::factory()->create([
        'code' => 'sales.payment_methods.clover.api_test_key',
        'value' => 'test_key',
        'channel_code' => 'default',
    ]);

    CoreConfig::factory()->create([
        'code' => 'sales.payment_methods.clover.api_key',
        'value' => 'live_key',
        'channel_code' => 'default',
    ]);

    // Act
    $apiKey = $this->clover->getApiKey();

    // Assert
    expect($apiKey)->toBe('test_key');
});

it('returns the live API key when sandbox mode is disabled', function () {
    // Arrange - Production mode
    CoreConfig::factory()->create([
        'code' => 'sales.payment_methods.clover.sandbox',
        'value' => '0',
        'channel_code' => 'default',
    ]);

    CoreConfig::factory()->create([
        'code' => 'sales.payment_methods.clover.api_test_key',
        'value' => 'test_key',
        'channel_code' => 'default',
    ]);

    CoreConfig::factory()->create([
        'code' => 'sales.payment_methods.clover.api_key',
        'value' => 'live_key',
        'channel_code' => 'default',
    ]);

    // Act
    $apiKey = $this->clover->getApiKey();

    // Assert
    expect($apiKey)->toBe('live_key');
});

it('returns the merchant id based on sandbox mode', function () {
    // Arrange
    CoreConfig::factory()->create([
        'code' => 'sales.payment_methods.clover.sandbox',
        'value' => '1',
        'channel_code' => 'default',
    ]);

    CoreConfig::factory()->create([
        'code' => 'sales.payment_methods.clover.test_merchant_id',
        'value' => 'TEST_MID',
        'channel_code' => 'default',
    ]);

    CoreConfig::factory()->create([
        'code' => 'sales.payment_methods.clover.merchant_id',
        'value' => 'LIVE_MID',
        'channel_code' => 'default',
    ]);

    // Act
    $merchantId = $this->clover->getMerchantId();

    // Assert
    expect($merchantId)->toBe('TEST_MID');
});

it('returns the sandbox API URL when sandbox mode is enabled', function () {
    // Arrange
    CoreConfig::factory()->create([
        'code' => 'sales.payment_methods.clover.sandbox',
        'value' => '1',
        'channel_code' => 'default',
    ]);

    // Act
    $sandboxUrl = $this->clover->getApiUrl();

    // Assert
    expect($sandboxUrl)->toBe('https://apisandbox.dev.clover.com');
});

it('returns the production API URL when sandbox mode is disabled', function () {
    // Arrange
    CoreConfig::factory()->create([
        'code' => 'sales.payment_methods.clover.sandbox',
        'value' => '0',
        'channel_code' => 'default',
    ]);

    // Act
    $productionUrl = $this->clover->getApiUrl();

    // Assert
    expect($productionUrl)->toBe('https://api.clover.com');
});

it('checks if credentials are valid in sandbox mode', function () {
    // Arrange
    CoreConfig::factory()->create([
        'code' => 'sales.payment_methods.clover.sandbox',
        'value' => '1',
        'channel_code' => 'default',
    ]);

    CoreConfig::factory()->create([
        'code' => 'sales.payment_methods.clover.api_test_key',
        'value' => 'test_key',
        'channel_code' => 'default',
    ]);

    CoreConfig::factory()->create([
        'code' => 'sales.payment_methods.clover.test_merchant_id',
        'value' => 'TEST_MID',
        'channel_code' => 'default',
    ]);

    // Act
    $hasValidCredentials = $this->clover->hasValidCredentials();

    // Assert
    expect($hasValidCredentials)->toBeTrue();
});

it('checks if credentials are valid in production mode', function () {
    // Arrange
    CoreConfig::factory()->create([
        'code' => 'sales.payment_methods.clover.sandbox',
        'value' => '0',
        'channel_code' => 'default',
    ]);

    CoreConfig::factory()->create([
        'code' => 'sales.payment_methods.clover.api_key',
        'value' => 'live_key',
        'channel_code' => 'default',
    ]);

    CoreConfig::factory()->create([
        'code' => 'sales.payment_methods.clover.merchant_id',
        'value' => 'LIVE_MID',
        'channel_code' => 'default',
    ]);

    // Act
    $hasValidCredentials = $this->clover->hasValidCredentials();

    // Assert
    expect($hasValidCredentials)->toBeTrue();
});

it('returns false if sandbox credentials are missing', function () {
    // Arrange
    CoreConfig::factory()->create([
        'code' => 'sales.payment_methods.clover.sandbox',
        'value' => '1',
        'channel_code' => 'default',
    ]);

    CoreConfig::factory()->create([
        'code' => 'sales.payment_methods.clover.api_test_key',
        'value' => '',
        'channel_code' => 'default',
    ]);

    CoreConfig::factory()->create([
        'code' => 'sales.payment_methods.clover.test_merchant_id',
        'value' => 'TEST_MID',
        'channel_code' => 'default',
    ]);

    // Act
    $hasValidCredentials = $this->clover->hasValidCredentials();

    // Assert
    expect($hasValidCredentials)->toBeFalse();
});

it('returns false if production credentials are missing', function () {
    // Arrange
    CoreConfig::factory()->create([
        'code' => 'sales.payment_methods.clover.sandbox',
        'value' => '0',
        'channel_code' => 'default',
    ]);

    CoreConfig::factory()->create([
        'code' => 'sales.payment_methods.clover.api_key',
        'value' => 'live_key',
        'channel_code' => 'default',
    ]);

    CoreConfig::factory()->create([
        'code' => 'sales.payment_methods.clover.merchant_id',
        'value' => '',
        'channel_code' => 'default',
    ]);

    // Act
    $hasValidCredentials = $this->clover->hasValidCredentials();

    // Assert
    expect($hasValidCredentials)->toBeFalse();
});

it('is not available when credentials are invalid', function () {
    // Arrange
    CoreConfig::factory()->create([
        'code' => 'sales.payment_methods.clover.active',
        'value' => '1',
        'channel_code' => 'default',
    ]);

    CoreConfig::factory()->create([
        'code' => 'sales.payment_methods.clover.sandbox',
        'value' => '1',
        'channel_code' => 'default',
    ]);

    CoreConfig::factory()->create([
        'code' => 'sales.payment_methods.clover.api_test_key',
        'value' => '',
        'channel_code' => 'default',
    ]);

    CoreConfig::factory()->create([
        'code' => 'sales.payment_methods.clover.test_merchant_id',
        'value' => '',
        'channel_code' => 'default',
    ]);

    // Act
    $isAvailable = $this->clover->isAvailable();

    // Assert
    expect($isAvailable)->toBeFalse();
});

it('returns payment method image from config', function () {
    // Arrange
    CoreConfig::factory()->create([
        'code' => 'sales.payment_methods.clover.image',
        'value' => 'clover/custom-logo.png',
        'channel_code' => 'default',
    ]);

    // Act
    $image = $this->clover->getImage();

    // Assert
    expect($image)->toContain('clover/custom-logo.png');
});

it('returns the correct redirect URL', function () {
    // Act
    $url = $this->clover->getRedirectUrl();

    // Assert
    expect($url)->toBe(route('clover.standard.redirect'));
});

it('verifies a valid webhook signature', function () {
    // Arrange
    CoreConfig::factory()->create([
        'code' => 'sales.payment_methods.clover.test_webhook_secret',
        'value' => 'wh_secret_test',
        'channel_code' => 'default',
    ]);

    CoreConfig::factory()->create([
        'code' => 'sales.payment_methods.clover.sandbox',
        'value' => '1',
        'channel_code' => 'default',
    ]);

    $payload = json_encode(['status' => 'APPROVED']);

    $signature = 't=1642599079,v1='.hash_hmac('sha256', '1642599079.'.$payload, 'wh_secret_test');

    // Act
    $isValid = $this->clover->verifyWebhookSignature($signature, $payload);

    // Assert
    expect($isValid)->toBeTrue();
});

it('rejects an invalid webhook signature', function () {
    // Arrange
    CoreConfig::factory()->create([
        'code' => 'sales.payment_methods.clover.test_webhook_secret',
        'value' => 'wh_secret_test',
        'channel_code' => 'default',
    ]);

    CoreConfig::factory()->create([
        'code' => 'sales.payment_methods.clover.sandbox',
        'value' => '1',
        'channel_code' => 'default',
    ]);

    $payload = json_encode(['status' => 'APPROVED']);

    $signature = 't=1642599079,v1='.hash_hmac('sha256', '1642599079.tampered-payload', 'wh_secret_test');

    // Act
    $isValid = $this->clover->verifyWebhookSignature($signature, $payload);

    // Assert
    expect($isValid)->toBeFalse();
});

it('rejects a webhook when no signing secret is configured', function () {
    // Act
    $isValid = $this->clover->verifyWebhookSignature('t=1,v1=abc', '{"status":"APPROVED"}');

    // Assert
    expect($isValid)->toBeFalse();
});
