<img src="docs/camya_TitleAndSlugField_github-header.jpg" />

# TitleWithSlug - Permalink Input for Filament Form Builder

This [FilamentPHP](https://filamentphp.com/docs/admin/installation) Form Builder package adds a form field to easily add and edit titles with slugs. 

**Features**

- Add the Field and all features are available.
- Slug automatically generates from title (if not manually updated).
- Empty slug regenerates from current title.
- Shows view link to visit persisted url. 
- Undo slug modification.
- Read only mode.

This plugin is inspired by the classic WordPress title & slug implementation.

```php
TitleWithSlugInput::make(
    titleField: 'title', // Your model's field name which stores the title
    slugField: 'slug', // Your model's field name which stores the slug
),
```

<img src="docs/camya_TitleAndSlugField_v1.0.0_demo.gif" width="600" />

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

## Usage

### Example: Change model fields names

The package assumes, that you model fields are named `title` and `slug`.

You can easily change them with the following parameters.


```php
TitleWithSlugInput::make(
    titleField: 'title',
    slugField: 'slug',
),
```

The output looks like this:

<img src="docs/camya_TitleAndSlugField_v1.0.0_usage_case01-01.png" width="600" />
<img src="docs/camya_TitleAndSlugField_v1.0.0_usage_case01-02.png" width="600" />
<img src="docs/camya_TitleAndSlugField_v1.0.0_usage_case01-03.png" width="600" />

### Example: Base path and title placeholder

Here we hide the hostname and add the base path `/blog/`.

Additionally, we change the placeholder text. 

```php
TitleWithSlugInput::make(
    titleField: 'title',
    slugField: 'slug',
    basePath: '/blog/',
    showHost: false,
    titlePlaceholder: 'Blog Title',
),
```

The output looks like this:

<img src="docs/camya_TitleAndSlugField_v1.0.0_usage_case02-01.png" width="600" />
<img src="docs/camya_TitleAndSlugField_v1.0.0_usage_case02-02.png" width="600" />
<img src="docs/camya_TitleAndSlugField_v1.0.0_usage_case02-03.png" width="600" />


### Example: Title above text field & custom slug label

The package automatically inserts a placeholder for the title. If you want to show the regular label above the text field instead, you can configure it.

Also you can set the label for the slug.

```php
TitleWithSlugInput::make(
    titleLabel: 'Product title',
    titlePlaceholder: '',
    slugLabel: 'Slug:'
),
```

The output looks like this:

<img src="docs/camya_TitleAndSlugField_v1.0.0_usage_case03-01.png" width="600" />

### Example: Generate route for View link

This package shows a "View" link for persisted slugs. By default, it simpy concatenates the strings of host + path + slug.

If you want use a `route()` instead, you can configure it like shown below.

```php
TitleWithSlugInput::make(
    previewRoute: fn(?Model $record) => $record?->slug
        ? route('product', ['slug' => $record->slug, 'extra' => true])
        : null,
),
```

### Example: Custom slugifier

This packages uses Laravel's slugifier, `Str::slug()`, but it's possible, to replace it with your own one.

The following generates a slug only with the characters a-z and validates them with a regex.

```php
TitleWithSlugInput::make(
    slugSlugifier: fn($string) => preg_replace( '/[^a-z]/', '', $string),
    slugRuleRegex: '/^[a-z]*$/',
),
```

Hint: You can customize the validation error, see "Custom error messages".

### Example: Add additional validation rules

By default, this package applies the rules `['required','string']` for both, title and slug.

Additionally a unique validation rule is applied to the slug field. (See hint below)

```php
TitleWithSlugInput::make(
    titleRules: [
        'required',
        'string',
    ],
),
```

> HINT: Unique validation rules can be modified only by using the parameters `titleRuleUniqueParameters` and the slug counterpart in order to set the "ignorable" correctly.


## All available parameters

You can call TitleWithSlugInput without any parameters, and it will work and use it's default values.

```php
TitleWithSlugInput::make();
```

Below an example with some overwritten defaults.

```php
TitleWithSlugInput::make(

    titleField: 'title',
    slugField: 'slug',

    // Url
    basePath: '/',
    baseHost: fn() => 'https://www.camya.com',
    showHost: true,
    previewRoute: fn(?Model $record) => $record?->slug
        ? route('post.show', ['slug' => $record->slug])
        : null,

    // Title
    titleLabel: 'The Title',
    titlePlaceholder: 'Post Title',
    titleClass: '',
    titleRules: [
        'required',
        'string',
    ],
    titleRuleUniqueParameters: [
        'callback' => fn(Unique $rule) => $rule->where('is_published', 1),
        'ignorable' => fn(?Model $record) => $record,
    ],
    titleReadonly: fn($context, Closure $get) => $context === 'edit' && $get('is_published'),

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
    slugRuleRegex: '/^[a-z0-9\-\_]*$/',
    slugReadonly: fn($context, Closure $get) => $context === 'edit' && $get('is_published'),
    slugSlugifier: fn($string) => Str::slug($string),

)->columnSpan('full'),
```

## Custom error messages

You can customize error messages in your Filament "EditModel" and "CreateModel" resources by adding the member variable $messages.

```php
protected $messages = [
  'data.slug.regex' => 'Invalid Slug. Use chars (a-z), numbers (0-9), underscore (_), and the dash (-).',
];
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [camya - Andreas Scheibel](https://github.com/camya)

This package was inspired by a filament-addons package by [awcodes](https://github.com/awcodes/filament-addons).

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
