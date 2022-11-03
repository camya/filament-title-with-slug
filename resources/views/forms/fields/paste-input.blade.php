<x-forms::field-wrapper
    :component="$getFieldWrapperView()"
    :id="$getId()"
    :label="$getLabel()"
    :label-sr-only="$isLabelHidden()"
    :helper-text="$getHelperText()"
    :hint="$getHint()"
    :hint-action="$getHintAction()"
    :hint-color="$getHintColor()"
    :hint-icon="$getHintIcon()"
    :required="$isRequired()"
    :state-path="$getStatePath()"
    class="filament-paste-input-wrapper"
>

    <div
        x-data="{
            state: $wire.entangle('{{ $getStatePath() }}'),
            placeholderText: '{{ $getPlaceholder() }}',
            update: function(data) {
                this.state = data;
                $nextTick(() => $refs.field.value = '');
                $refs.field.setAttribute('placeholder', 'Data imported...')
                setTimeout(() => $refs.field.setAttribute('placeholder', this.placeholderText), 2000)
            }
        }"
    >

        <input
            wire:ignore
            {{ $getExtraInputAttributeBag()->class(['border-dotted bg-gray-50 border-2 border-gray-400 placeholder-gray-500 block w-full transition duration-75 rounded-lg shadow-sm focus:border-primary-600 focus:ring-1 focus:ring-inset focus:ring-primary-600 disabled:opacity-70', 'dark:bg-gray-700 dark:text-white' => config('forms.dark_mode'), 'border-gray-300' => !$errors->has($getStatePath()), 'dark:border-gray-600' => !$errors->has($getStatePath()) && config('forms.dark_mode'), 'border-danger-600 ring-danger-600' => $errors->has($getStatePath())]) }}

            {!! ($placeholder = $getPlaceholder()) ? "placeholder=\"{$placeholder}\"" : null !!}
            x-on:paste="update($event.clipboardData.getData('Text'))"
            x-on:click.outside="$refs.field.value = ''"
            x-ref="field"
            type="text"
        >

        {{--        <textarea--}}
        {{--            class="w-full" rows="10"--}}
        {{--            type="hidden" {{ $applyStateBindingModifiers('wire:model') }}="{{ $getStatePath() }}"></textarea>--}}

        {{--        <pre--}}
        {{--            x-text="clipboardData"--}}
        {{--            class="mt-4 text-xs"--}}
        {{--        ></pre>--}}

    </div>

    @if (($suffixAction = $getSuffixAction()) && (! $suffixAction->isHidden()))
        {{ $suffixAction }}
    @endif

</x-forms::field-wrapper>
