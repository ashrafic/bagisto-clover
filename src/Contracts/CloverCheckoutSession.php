<?php

namespace Webkul\Clover\Contracts;

interface CloverCheckoutSession
{
    public const STATUS_NEW = 'new';

    public const STATUS_PAID = 'paid';

    public const STATUS_FAILED = 'failed';

    public const STATUS_CANCELED = 'canceled';

    public const STATUS_PROCESSED = 'processed';

    public const VERIFIED_VIA_WEBHOOK = 'webhook';

    public const VERIFIED_VIA_REDIRECT = 'redirect';
}
