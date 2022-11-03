<x-forms::field-wrapper
    :id="$getId()"
    :label="$getLabel()"
    :label-sr-only="$isLabelHidden()"
    :helper-text="$getHelperText()"
    :hint="$getHint()"
    :hint-icon="$getHintIcon()"
    :required="$isRequired()"
    :state-path="$getStatePath()"
    class="-mt-3 filament-seo-slug-input-wrapper"
>
    <div
        x-data="{
            mode: '{{ $getMode() }}', // edit or create
            state: $wire.entangle('{{ $getStatePath() }}'), // current slug value
            statePersisted: '', // slug value received from db
            stateInitial: '', // slug value before modification
            editing: false,
            modified: false,
            initModification: function() {

                this.stateInitial = this.state;

                if(!this.statePersisted) {
                    this.statePersisted = this.state;
                }

                this.editing = true;

            },
            submitModification: function() {

                if(!this.stateInitial) {
                    this.state = '';
                }
                else {
                    this.state = this.stateInitial;
                }

                this.detectModification();

                this.editing = false;

           },
           cancelModification: function() {

                this.stateInitial = this.state;

                this.detectModification();

                this.editing = false;

           },
           resetModification: function() {

                this.stateInitial = this.statePersisted;

                this.detectModification();

           },
           detectModification: function() {

                this.modified = this.stateInitial !== this.statePersisted;

           },
        }"
        x-on:submit.document="modified = false"
    >

        <div
            {{ $attributes->merge($getExtraAttributes())->class(['flex mx-1 items-center justify-between group text-sm filament-forms-text-input-component']) }}
        >

            @if($getReadonly())

                <span class="flex">
                    <span class="mr-1">{{ $getLabelPrefix() }}</span>
                    <span class="text-gray-400">{{ $getFullBaseUrl() }}</span>
                    <span class="text-gray-400 font-semibold">{{ $getState() }}</span>
                </span>

                <a
                    href="{{ $getRecordUrl() }}"
                    target="_blank"
                    class="
                        filament-link cursor-pointer text-sm text-primary-600 underline
                        inline-flex items-center justify-center space-x-1
                        hover:text-primary-500
                        dark:text-primary-500 dark:hover:text-primary-400
                    "
                >

                    <span>{{ trans('filament-title-with-slug::package.permalink_link_visit') }}</span>

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"
                        />
                    </svg>

                </a>

            @else

                <span>

                    <span class="mr-1">{{ $getLabelPrefix() }}</span>

                    <span
                        x-text="!editing ? '{{ $getFullBaseUrl() }}' : '{{ $getBasePath() }}'"
                        class="text-gray-400"
                    ></span>

                    <a
                        href="#"
                        type="button"
                        title="{{ trans('filament-title-with-slug::package.permalink_action_edit') }}"
                        x-on:click.prevent="initModification()"
                        x-show="!editing"
                        class="
                            cursor-pointer
                            font-semibold text-gray-400
                            inline-flex items-center justify-center
                            hover:underline hover:text-primary-500
                            dark:hover:text-primary-400
                        "
                    >
                        <span>&shy;{{ $getState() }}</span>

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="
                                ml-1 h-4 w-4
                                text-primary-600 dark:text-primary-500
                            "
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                          <path
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                          />
                        </svg>

                    </a>

                </span>

                <div
                    class="flex-1 mx-2"
                    x-show="editing"
                    style="display: none;"
                >

                    <input
                        type="text"
                        x-model="stateInitial"
                        x-bind:disabled="!editing"
                        x-on:keydown.enter="submitModification()"
                        x-on:keydown.escape="cancelModification()"
                        {!! ($autocomplete = $getAutocomplete()) ? "autocomplete=\"{$autocomplete}\"" : null !!}
                        id="{{ $getId() }}"
                        {!! ($placeholder = $getPlaceholder()) ? "placeholder=\"{$placeholder}\"" : null !!}
                        {!! $isRequired() ? 'required' : null !!}
                        {{ $getExtraInputAttributeBag()->class(['block w-full transition duration-75 rounded-lg shadow-sm focus:border-primary-600 focus:ring-1 focus:ring-inset focus:ring-primary-600 disabled:opacity-70', 'dark:bg-gray-700 dark:text-white' => config('forms.dark_mode'), 'border-gray-300' => !$errors->has($getStatePath()), 'dark:border-gray-600' => !$errors->has($getStatePath()) && config('forms.dark_mode'), 'border-danger-600 ring-danger-600' => $errors->has($getStatePath())]) }}
                    />

                    <input
                        type="hidden"
                        {{ $applyStateBindingModifiers('wire:model') }}="{{ $getStatePath() }}"
                    />

                </div>

                <div
                    x-show="editing"
                    class="flex space-x-4"
                    style="display: none;"
                >

                    <x-filament::button
                        x-ref="updateButton"
                        color="gray"
                        x-on:click="submitModification()"
                        title="{{ trans('filament-title-with-slug::package.permalink_action_update') }}"
                    >
                        {{ trans('filament-title-with-slug::package.permalink_action_update') }}
                    </x-filament::button>

                    <x-filament::link
                        x-show="mode === 'edit' && state !== statePersisted"
                        x-on:click="resetModification()"
                        class="cursor-pointer ml-4"
                        icon="heroicon-o-refresh"
                        color="gray"
                        size="sm"
                        title="{{ trans('filament-title-with-slug::package.permalink_action_reset') }}"
                    >
                        <span class="sr-only">{{ trans('filament-title-with-slug::package.permalink_action_reset') }}</span>
                    </x-filament::link>

                    <x-filament::link
                        x-on:click="cancelModification()"
                        class="cursor-pointer"
                        icon="heroicon-o-x"
                        color="gray"
                        size="sm"
                        title="{{ trans('filament-title-with-slug::package.permalink_action_cancel') }}"
                    >
                        <span class="sr-only">{{ trans('filament-title-with-slug::package.permalink_action_cancel') }}</span>
                    </x-filament::link>

                </div>

                <span
                    x-show="mode === 'edit'"
                    class="flex items-center space-x-2"
                >

                <span
                    x-show="!editing && modified"
                    class="text-sm text-success-600"
                >{{ trans('filament-title-with-slug::package.permalink_status_edited') }}</span>

                <template x-if="!editing">

                    <a

                        href="{{ $getRecordUrl() }}"
                        target="_blank"
                        class="filament-link inline-flex items-center justify-center space-x-1 hover:underline focus:outline-none focus:underline text-sm text-primary-600 hover:text-primary-500 dark:text-primary-500 dark:hover:text-primary-400 cursor-pointer"
                    >

                        <span
                            x-text="
                            modified
                                ? '{{ trans('filament-title-with-slug::package.permalink_link_visit_current') }}'
                                : '{{ trans('filament-title-with-slug::package.permalink_link_visit') }}'
                            "
                        ></span>

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                          <path
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"
                          />
                        </svg>

                    </a>

                </template>

            </span>

            @endif

        </div>

    </div>

</x-forms::field-wrapper>
