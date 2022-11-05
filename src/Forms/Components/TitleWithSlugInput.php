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
        null|Closure|string $visitLinkLabel = null,
        null|Closure $visitLinkRoute = null,

        // Title
        string|Closure|null $titleLabel = null,
        string|null $titlePlaceholder = null,
        array|Closure|null $titleExtraInputAttributes = null,
        array $titleRules = [
            'required',
            'string',
        ],
        array $titleRuleUniqueParameters = [],
        bool|Closure $titleIsReadonly = false,

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

        /** Input: "Title" */

        $textInput = TextInput::make($titleField)
            ->disabled($titleIsReadonly)
            ->autofocus()
            ->required()
            ->reactive()
            ->disableAutocomplete()
            ->rules($titleRules)
            ->extraInputAttributes($titleExtraInputAttributes ?? ['class' => 'text-xl'])
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

        /** Input: "Slug" (+ view) */

        $slugInput = SlugInput::make($slugField)

            // Custom SlugInput methods
            ->slugInputVisitLinkRoute($visitLinkRoute)
            ->slugInputVisitLinkLabel($visitLinkLabel)
            ->slugInputContext(fn($context) => $context === 'create' ? 'create' : 'edit')
            ->slugInputRecordSlug(fn(?Model $record) => $record?->$slugField)
            ->slugInputModelName(fn(?Model $record) => Str::of(class_basename($record))->title())
            ->slugInputLabelPrefix($slugLabel)
            ->slugInputTitleField($titleField)
            ->slugInputBasePath($basePath)
            ->slugInputBaseUrl($baseHost)
            ->slugInputShowUrl($showHost)

            // Default TextInput methods
            ->readonly($slugReadonly)
            ->required()
            ->reactive()
            ->disableAutocomplete()
            ->disableLabel()
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

        /** Input: "Slug Auto Update Disabled" (Hidden) */

        $hiddenInputSlugAutoUpdateDisabled = Hidden::make('slug_auto_update_disabled')
            ->dehydrated(false);

        /** Group */

        return Group::make()
            ->schema([
                $textInput,
                $slugInput,
                $hiddenInputSlugAutoUpdateDisabled,
            ]);
    }

    /** Fallback slugifier, over-writable with slugSlugifier parameter. */
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
