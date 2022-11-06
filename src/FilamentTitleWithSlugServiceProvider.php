<?php

namespace Camya\Filament;

use Filament\PluginServiceProvider;
use Spatie\LaravelPackageTools\Package;

class FilamentTitleWithSlugServiceProvider extends PluginServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-title-with-slug')
            ->hasConfigFile()
            ->hasViews()
            ->hasTranslations();
    }

    protected function getStyles(): array
    {
        return [
            'filament-title-with-slug-styles' => __DIR__.'/../resources/dist/filament-title-with-slug.css',
        ];
    }
}
