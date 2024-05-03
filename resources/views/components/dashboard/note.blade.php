@props(['price', 'label', 'action'])

<div {{ $attributes->merge(['class' => 'flex justify-between ']) }}>
    <span class="grow">
        @isset($label)
            {{ $label }}
        @endisset
    </span>
    @isset($price)
        <span class="mr-2"> {{ Number::currency($price, 'EUR', 'fr') }}</span>
    @endisset
    @isset($action)
        {{ $action }}
    @endisset
</div>
