@php
    $currencyService = app(\App\Services\CurrencyService::class);
    $currencies = $currencyService->getSupportedCurrencies();
    $currentCurrency = $currencyService->getUserCurrency();
@endphp

<div class="dropdown">
    <button class="btn btn-outline-primary dropdown-toggle" type="button" id="currencyDropdown" data-bs-toggle="dropdown" aria-expanded="false">
        @if(isset($currencies[$currentCurrency]))
            {{ $currencies[$currentCurrency]['flag'] }} {{ $currentCurrency }}
        @else
            💱 Currency
        @endif
    </button>
    <ul class="dropdown-menu" aria-labelledby="currencyDropdown">
        @foreach($currencies as $code => $currency)
            <li>
                <a class="dropdown-item currency-option {{ $currentCurrency === $code ? 'active' : '' }}" 
                   href="#" 
                   data-currency="{{ $code }}"
                   onclick="changeCurrency('{{ $code }}'); return false;">
                    {{ $currency['flag'] }} {{ $currency['name'] }} ({{ $currency['symbol'] }})
                </a>
            </li>
        @endforeach
    </ul>
</div>

<script>
if (typeof changeCurrency === 'undefined') {
    function changeCurrency(currency) {
        fetch('{{ route("currency.change") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ currency: currency })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                console.error('Currency change failed:', data);
            }
        })
        .catch(error => {
            console.error('Error changing currency:', error);
        });
    }
}
</script>

<style>
.dropdown-item.active {
    background-color: #667eea;
    color: white;
}
.dropdown-item:hover {
    background-color: #f8f9fa;
}
</style>
