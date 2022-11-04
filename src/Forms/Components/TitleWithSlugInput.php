<?php

namespace Camya\FilamentTitleWithSlug\Forms\Components;

use Camya\FilamentTitleWithSlug\Forms\Fields\SlugInput;
use Closure;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TitleWithSlugInput
{
    public static function make(

        string|null $titleField = null,
        string|null $slugField = null,

        // Url
        string|Closure $basePath = '/',
        string|Closure|null $baseHost = null,
        bool $showHost = true,
        null|Closure $visitLinkRoute = null,
        null|Closure|string $visitLinkLabel = null,

        // Title
        string|Closure|null $titleLabel = null,
        string|null $titlePlaceholder = null,
        string $titleClass = '',
        array $titleRules = [
            'required',
            'string',
        ],
        array $titleRuleUniqueParameters = [],
        bool|Closure $titleReadonly = false,

        // Slug
        string|null $slugLabel = null,
        array $slugRules = [
            'required',
            'string',
        ],
        array $slugRuleUniqueParameters = [],
        bool|Closure $slugReadonly = false,
        null|Closure $slugSlugifier = null,
        string|Closure|null $slugRuleRegex = '/^[a-z0-9\-\_]*$/',

    ): Group {

        $titleField = $titleField ?? config('filament-title-with-slug.title_field');
        $slugField = $slugField ?? config('filament-title-with-slug.slug_field');

        $textInput = TextInput::make($titleField)
            ->disabled($titleReadonly)
            ->autofocus()
            ->required()
            ->reactive()
            ->disableAutocomplete()
            ->rules($titleRules)
            ->extraInputAttributes(['class' => $titleClass ?: 'text-xl font-semibold'])
            ->beforeStateDehydrated(fn(TextInput $component, $state) => $component->state(trim($state)))
            ->afterStateUpdated(

                function ($state, Closure $set, Closure $get, string $context) use ($slugSlugifier, $slugField) {
                    $slugAutoUpdateDisabled = $get('slug_auto_update_disabled');

                    if ($context === 'edit') {
                        $slugAutoUpdateDisabled = true;
                    }

                    if (! $slugAutoUpdateDisabled && filled($state)) {
                        $set($slugField, self::slugify($slugSlugifier, $state));
                    }
                }

            );

        if ($titlePlaceholder !== '') {
            $textInput->placeholder($titlePlaceholder ?: fn() => Str::of($titleField)->title());
        }

        if (! $titleLabel) {
            $textInput->disableLabel();
        }

        if ($titleLabel) {
            $textInput->label($titleLabel);
        }

        if ($titleRuleUniqueParameters) {
            $textInput->unique(...$titleRuleUniqueParameters);
        }

        $slugInput = SlugInput::make($slugField)
            ->visitLinkRoute($visitLinkRoute)
            ->visitLinkLabel($visitLinkLabel)
            ->readonly($slugReadonly)
            ->required()
            ->reactive()
            ->disableAutocomplete()
            ->mode(fn($context) => $context === 'create' ? 'create' : 'edit')
            ->recordSlug(fn(?Model $record) => $record?->$slugField)
            ->disableLabel()
            ->labelPrefix($slugLabel)
            ->titleField($titleField)
            ->basePath($basePath)
            ->baseUrl($baseHost)
            ->showUrl($showHost)
            ->regex($slugRuleRegex)
            ->rules($slugRules)
            ->unique(ignorable: fn(?Model $record) => $record)
            ->afterStateUpdated(

                function ($state, Closure $set, Closure $get) use ($slugSlugifier, $titleField, $slugField) {
                    $text = trim($state) === ''
                        ? $get($titleField)
                        : $get($slugField);

                    $set('slug', self::slugify($slugSlugifier, $text));

                    $set('slug_auto_update_disabled', true);
                }

            );

        $slugRuleUniqueParameters
            ? $slugInput->unique(...$slugRuleUniqueParameters)
            : $slugInput->unique(ignorable: fn(?Model $record) => $record);

        $hiddenInputSlugAutoUpdateDisabled = Hidden::make('slug_auto_update_disabled')
            ->dehydrated(false);

        return Group::make()
            ->schema([
                $textInput,
                $slugInput,
                $hiddenInputSlugAutoUpdateDisabled,
            ]);
    }

    /** TitleWithSlug::make(slugifier: fn($string) => Str::slug($string.' - category')) */
    protected static function slugify(Closure|null $slugifier, string|null $text): string
    {
        if (! trim($text)) {
            return '';
        }

        return is_callable($slugifier)
            ? $slugifier($text)
            : Str::slug($text);
    }
}
