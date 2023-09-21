<?php

use Camya\Filament\Forms\Components\TitleWithSlugInput;
use Camya\Filament\Tests\Support\Record;
use Camya\Filament\Tests\Support\TestableForm;
use Illuminate\Database\Eloquent\Model;
use Livewire\Livewire;

it('returns OK if component is used', function () {
    TestableForm::$formSchema = [
        TitleWithSlugInput::make(),
    ];

    $component = Livewire::test(TestableForm::class);

    $component->assertOk();
});

it('fills view correctly with default component parameters', function () {
    TestableForm::$formSchema = [
        TitleWithSlugInput::make(),
    ];

    $component = Livewire::test(TestableForm::class)
        ->set([
            'data.title' => 'Persisted Title',
            'data.slug' => 'persisted-slug',
        ]);

    $component
        ->assertSeeHtml('wire:model.blur="data.title"')
        ->assertSeeHtml('id="data.slug"')
        ->assertSet('data.title', 'Persisted Title')
        ->assertSet('data.slug', 'persisted-slug')
        ->assertSeeHtml('<span class="mr-1">persisted-slug</span>');
});

it('fills view correctly with overwritten component parameters', function () {
    TestableForm::$formSchema = [
        TitleWithSlugInput::make(
            fieldTitle: 'TitleFieldName',
            fieldSlug: 'SlugFieldName',
            urlVisitLinkLabel: '*Visit Link Label*',
            titleLabel: '*Title Label*',
            titlePlaceholder: '*Title Placeholder*',
            slugLabel: '*Slug Label*',
        ),
    ];

    $component = Livewire::test(TestableForm::class)
        ->set([
            'data.TitleFieldName' => 'Persisted Title',
            'data.SlugFieldName' => 'persisted-slug',
        ]);

    $component
        ->assertSeeHtml('wire:model.blur="data.TitleFieldName"')
        ->assertSeeHtml('id="data.SlugFieldName"')
        ->assertSee('*Title Label*')
        ->assertSee('*Slug Label*')
        ->assertSee('*Visit Link Label*')
        ->assertSeeHtml('placeholder="*Title Placeholder*"');
});

it('does not show the visit link if it is set invisible', function () {
    TestableForm::$formSchema = [
        TitleWithSlugInput::make(
            urlVisitLinkVisible: false,
            urlVisitLinkLabel: '*Visit Link Label*',
        ),
    ];

    $component = Livewire::test(TestableForm::class)
        ->set([
            'data.TitleFieldName' => 'Persisted Title',
            'data.SlugFieldName' => 'persisted-slug',
        ]);

    $component->assertDontSee('*Visit Link Label*');
});

it('generates the default visit link from host + path + slug', function () {
    config()->set('filament-title-with-slug.url_host', 'https://www.camya.com');

    TestableForm::$formSchema = [
        TitleWithSlugInput::make(
            urlPath: '/blog/'
        ),
    ];

    $component = Livewire::test(TestableForm::class, [
        'record' => new Record([
            'title' => 'Persisted Title',
            'slug' => 'persisted-slug',
        ]),
    ]);

    $component->assertSeeHtml('https://www.camya.com/blog/persisted-slug');
});

it('generates a custom visit link for subdomain', function () {
    TestableForm::$formSchema = [
        TitleWithSlugInput::make(
            urlPath: '',
            urlHostVisible: false,
            urlVisitLinkRoute: fn (?Model $record) => $record?->slug
                ? 'https://'.$record->slug.'.camya.com'
                : null,
            slugLabelPostfix: '.camya.com',
        ),
    ];

    $component = Livewire::test(TestableForm::class, [
        'record' => new Record([
            'title' => 'My Subdomain',
            'slug' => 'my-subdomain',
        ]),
    ]);

    $component
        ->assertSeeHtml('https://my-subdomain.camya.com')
        ->assertSeeHtml('>.camya.com<');
});

it('allows generating a URL with an empty slug, if slug has no required rule.', function () {
    config()->set('filament-title-with-slug.url_host', 'https://www.camya.com');

    TestableForm::$formSchema = [
        TitleWithSlugInput::make(
            slugRules: [],
        ),
    ];

    $component = Livewire::test(TestableForm::class, [
        'record' => new Record([
            'title' => 'My Homepage',
            'slug' => '/',
        ]),
    ]);

    $component
        ->assertSeeHtml("!editing ? 'https://www.camya.com/' : '/'");
});
