<img src="docs/camya-filament-title-with-slug_teaser-header.jpg" />

# TitleWithSlugInput - Easy Permalink Slugs for the Filament Form Builder

This [FilamentPHP](https://filamentphp.com/docs/admin/installation) Form Builder package adds a form field to easily add
and edit titles with slugs.

This plugin is inspired by the classic WordPress title & slug implementation.

```php
TitleWithSlugInput::make(
    titleField: 'title', // The name of the field in your model that stores the title.
    slugField: 'slug', // The name of the field in your model that will store the slug
),
```

**Features**

- Auto-generates the slug from the title, if it has not already been manually updated.
- Slug edit form.
- "Visit" link to view the URL.
- Undo the edited slug.
- Fully configurable, see [all available parameters](#all-available-parameters).

This package is developed by [camya.com](https://www.camya.com). You
can [follow us on Twitter](https://twitter.com/camyaCom) for DEV updates.

Watch **[&raquo; Demo Video &laquo;](https://www.youtube.com/watch?v=v-AxZv6M1xs)**

[![Video](docs/examples/camya-filament-title-with-slug_video-placeholder.png)](https://www.youtube.com/watch?v=v-AxZv6M1xs)

FilamentPHP is based on
[Laravel](https://laravel.com/),
[Livewire](https://laravel-livewire.com/),
[AlpineJS](https://alpinejs.dev/),
and
[TailwindCSS](https://tailwindcss.com/). (aka Tall Stack)

## Support us

You can support our work by [donations](https://www.camya.com).

## Installation

You can install the package via composer:

```bash
composer require camya/filament-title-with-slug
```

If needed, you can publish the config file with:

```bash
php artisan vendor:publish --tag="filament-title-with-slug-config"
```

If needed, you can publish the translation files with:

```bash
php artisan vendor:publish --tag="filament-title-with-slug-translations"
```

## Usage & Examples

- [**All available parameters**](#all-available-parameters)


- [Change model fields names](#change-model-fields-names)
- [Change labels, titles, Placeholder, and basePath](#change-labels-titles-Placeholder-and-basePath)
- [Style the "title" input field](#style-the-title-input-field)
- [Add extra validation rules for title or slug](#add-extra-validation-rules-for-title-or-slug)
- [Custom error messages](#custom-error-messages)
- [Custom unique validation rules for title (and slug)](#custom-unique-validation-rules-for-title-and-slug)
- [Generate route for "Visit" link](#generate-route-for-visit-link)
- [Custom slugifier](#custom-slugifier)

### Change model fields names

The package assumes, that you model fields are named `title` and `slug`.

You can easily change them according to your needs.

```php
TitleWithSlugInput::make(
    titleField: 'title',
    slugField: 'slug',
)
```

The output looks like this:

<img src="docs/examples/camya-filament-title-with-slug-docs-case01-labels-01.png" width="600" />
<img src="docs/examples/camya-filament-title-with-slug-docs-case01-labels-02.png" width="600" />

### Change labels, titles, Placeholder, and basePath

It's possible to change all labels on the fly.

In this example, we also add the base path `/books/`.

```php
TitleWithSlugInput::make(
    basePath: '/book/',
    visitLinkLabel: 'Visit Book',
    titleLabel: 'Title',
    titlePlaceholder: 'Insert the title...',
    slugLabel: 'Link:',
)
```

The output looks like this:

<img src="docs/examples/camya-filament-title-with-slug-docs-case02-labels-01.png" width="600" />
<img src="docs/examples/camya-filament-title-with-slug-docs-case02-labels-02.png" width="600" />

### Style the "title" input field

In order to style the "title" input field, you can pass the attributes `class` via `titleExtraInputAttributes`
parameter.

```php
TitleWithSlugInput::make(
    titleExtraInputAttributes: ['class' => 'text-xl font-semibold bg-orange-50'],
)
```

The output looks like this:

<img src="docs/examples/camya-filament-title-with-slug-docs-case03-labels-01.png" width="600" />

### Add extra validation rules for title or slug

You can add additional validation rules by passing in the variables `titleRules` or `slugRules`.

In addition, a unique validation rule is applied to the slug field automatically. In order to modify the unique rule,
read [Custom unique validation rules for title (and slug)](#custom-unique-validation-rules-for-title-and-slug).

```php
TitleWithSlugInput::make(
    titleRules: [
        'required',
        'string',
        'min:3',
        'max:12',
    ],
)
```

You can also [customize the error messages](#custom-error-messages).

### Custom error messages

You can customize the error messages in your EditModel and CreateModel filament resources by adding the $messages member
variable.

```php
protected $messages = [
  'data.slug.regex' => 'Invalid Slug. Use only chars (a-z), numbers (0-9), and the dash (-).',
];
```

### Custom unique validation rules for title (and slug)

Unique validation rules can be modified only by using the parameters `titleRuleUniqueParameters` and
the `slugRuleUniqueParameters` counterpart.

This is needed in order to set Filament's "ignorable" parameter correctly.

```php
TitleWithSlugInput::make(
    titleRuleUniqueParameters: [
        'callback' => fn(Unique $rule) => $rule->where('is_published', 1),
        'ignorable' => fn(?Model $record) => $record,
    ],
)
```

This array is inserted into the input field's `->unique(...[$slugRuleUniqueParameters])` method.

Read Filament's documentation for the [Unique](https://filamentphp.com/docs/2.x/forms/validation#unique) method.

Available array keys:

```php 
'ignorable' (Model | Closure)
'callback' (?Closure)
'ignoreRecord' (bool)
'table' (string | Closure | null)  
'column' (string | Closure | null) 
```

### Generate route for "Visit" link

This package displays a "view" link for persisted slugs. By default, it simply concatenates the strings host + path +
slug.

If you want to use a "route()" instead, you can configure it as shown below.

```php
TitleWithSlugInput::make(
    visitLinkRoute: fn(?Model $record) => $record?->slug
        ? route('post.show', ['slug' => $record->slug])
        : null,
)
```

### Custom slugifier

This package uses Laravel's slugifier, `Str::slug()`, but it is possible to replace it with one of your own.

The following generates a slug with only the characters a-z and validates them with a regex.

```php
TitleWithSlugInput::make(
    slugSlugifier: fn($string) => preg_replace( '/[^a-z]/', '', $string),
    slugRuleRegex: '/^[a-z]*$/',
)
```

Note: You can customize the validation error, see [Custom error messages](#custom-error-messages).

### All available parameters

You can call TitleWithSlugInput without parameters, and it will work and use its default values.

In order to set parameters, you use [PHP8's Named Arguments](https://laravel-news.com/modern-php-features-explained)
syntax.

```php
TitleWithSlugInput::make(
    titleField: 'title',
    slugField: 'slug',
);
```

Below is an example with some defaults overridden.

```php
TitleWithSlugInput::make(

    titleField: 'title',
    slugField: 'slug',

    // Url
    basePath: '/blog/',
    baseHost: 'https://www.camya.com',
    showHost: true,
    visitLinkLabel: 'View',
    visitLinkRoute: fn(?Model $record) => $record?->slug ? route('post.show', ['slug' => $record->slug]): null,

    // Title
    titleLabel: 'The Title',
    titlePlaceholder: 'Post Title',
    titleExtraInputAttributes: ['class' => 'text-xl font-semibold bg-orange-50'],
    titleRules: [
        'required',
        'string',
    ],
    titleRuleUniqueParameters: [
        'callback' => fn(Unique $rule) => $rule->where('is_published', 1),
        'ignorable' => fn(?Model $record) => $record,
    ],
    titleIsReadonly: fn($context, Closure $get) => $context === 'edit' && $get('is_published'),

    // Slug
    slugLabel: 'The Slug: ',
    slugRules: [
        'required',
        'string',
    ],
    slugRuleUniqueParameters: [
        'callback' => fn(Unique $rule) => $rule->where('is_published', 1),
        'ignorable' => fn(?Model $record) => $record,
    ],
    slugIsReadonly: fn($context, Closure $get) => $context === 'edit' && $get('is_published'),
    slugSlugifier: fn($string) => Str::slug($string),
    slugRuleRegex: '/^[a-z0-9\-\_]*$/',

)->columnSpan('full'),
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [camya - Andreas Scheibel](https://github.com/camya) (Developer at  [camya.com](https://www.camya.com))

This package was inspired by a package
by [awcodes](https://github.com/awcodes/) and the work of [spatie](https://github.com/spatie/).

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
