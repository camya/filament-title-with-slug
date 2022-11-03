<?php

namespace Camya\FilamentTitleWithSlug\Forms\Fields;

use Closure;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Str;

class SlugInput extends TextInput
{
    protected string $view = 'filament-title-with-slug::forms.fields.slug-input';

    protected string|Closure|null $mode = null;

    protected string|Closure $basePath = '/';

    protected string|Closure|null $baseUrl = null;

    protected bool $showUrl = true;

    protected bool $cancelled = false;

    protected Closure $recordSlug;

    protected string $titleField;

    protected bool|Closure $readonly = false;

    protected string $labelPrefix;

    protected Closure|null $previewLinkRoute = null;

    public function previewLinkRoute(Closure|null $previewLinkRoute): static
    {
        $this->previewLinkRoute = $previewLinkRoute;

        return $this;
    }

    public function getPreviewLinkRoute(): string|null
    {
        return $this->evaluate($this->previewLinkRoute);
    }

    public function labelPrefix(string|null $labelPrefix): static
    {
        $this->labelPrefix = $labelPrefix ?? trans('filament-title-with-slug::package.permalink_label');

        return $this;
    }

    public function getLabelPrefix(): string
    {
        return $this->evaluate($this->labelPrefix);
    }

    public function readonly(bool|Closure $readonly): static
    {
        $this->readonly = $readonly;

        return $this;
    }

    public function getReadonly(): string
    {
        return $this->evaluate($this->readonly);
    }

    public function mode(string|Closure|null $mode): static
    {
        $this->mode = $mode;

        return $this;
    }

    public function recordSlug(Closure $recordSlug)
    {
        $this->recordSlug = $recordSlug;

        return $this;
    }

    public function getRecordSlug(): ?string
    {
        return $this->evaluate($this->recordSlug);
    }

    public function titleField(string $titleField): static
    {
        $this->titleField = $titleField;

        return $this;
    }

    public function getTitleField(): string
    {
        return $this->titleField;
    }

    public function getRecordUrl(): ?string
    {
        if (! $this->getRecordSlug()) {
            return null;
        }

        $previewLinkRoute = $this->getPreviewLinkRoute();

        return $previewLinkRoute
            ? $this->getPreviewLinkRoute()
            : $this->getBaseUrl().$this->getBasePath().$this->evaluate($this->recordSlug);
    }

    public function basePath(string|Closure $path): static
    {
        $this->basePath = $path ?: $this->basePath;

        return $this;
    }

    public function baseUrl(string|Closure|null $url): static
    {
        $this->baseUrl = $url ?: config('app.url');

        return $this;
    }

    public function getBaseUrl()
    {
        return Str::of($this->evaluate($this->baseUrl))->rtrim('/');
    }

    public function showUrl(bool $showUrl): static
    {
        $this->showUrl = $showUrl;

        return $this;
    }

    public function getShowUrl(): ?bool
    {
        return $this->showUrl;
    }

    public function getMode(): ?string
    {
        return $this->evaluate($this->mode);
    }

    public function getFullBaseUrl(): ?string
    {
        return $this->showUrl
            ? $this->getBaseUrl().$this->getBasePath()
            : $this->getBasePath();
    }

    public function getBasePath()
    {
        return $this->evaluate($this->basePath);
    }
}
