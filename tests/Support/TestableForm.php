<?php

namespace Camya\Filament\Tests\Support;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;

class TestableForm extends Component implements HasForms
{
    use InteractsWithForms;

    public static array $formSchema = [];

    public array $data = [];

    public $record;

    protected $listeners = ['$refresh'];

    public function render()
    {
        return view('filament-title-with-slug::tests.support.testable-form');
    }

    protected function getFormModel(): Model|string|null
    {
        return $this->record;
    }

    public function getFormSchema(): array
    {
        return static::$formSchema;
    }

    public function getFormStatePath(): ?string
    {
        return 'data';
    }
}
