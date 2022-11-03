<?php

namespace Camya\FilamentTitleWithSlug\Forms\Fields;

use Closure;
use Filament\Forms\Components\TextInput;

class PasteInput extends TextInput
{
    protected string $view = 'filament-title-with-slug::forms.fields.paste-input';

    public static function make(string $name): static
    {
        return parent::make($name);
    }

    protected string | Closure | null $placeholder = 'Click and paste machine readable data... (Cmd + V / Strg V)';
}
