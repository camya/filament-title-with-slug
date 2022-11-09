<?php

use Camya\Filament\Forms\Components\TitleWithSlugInput;
use Camya\Filament\Tests\Support\TestableForm;
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
        ->assertSeeHtml('wire:model="data.title"')
        ->assertSeeHtml('wire:model="data.slug"')
        ->assertSet('data.title', 'Persisted Title')
        ->assertSet('data.slug', 'persisted-slug')
        ->assertSeeHtml('<span class="mr-1">&shy;persisted-slug</span>');
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
        ->assertSeeHtml('wire:model="data.TitleFieldName"')
        ->assertSeeHtml('wire:model="data.SlugFieldName"')
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
