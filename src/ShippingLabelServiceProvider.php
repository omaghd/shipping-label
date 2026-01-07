<?php

declare(strict_types=1);

namespace OmaghD\ShippingLabel;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ShippingLabelServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('shipping-label')
            ->hasAssets();
    }
}
