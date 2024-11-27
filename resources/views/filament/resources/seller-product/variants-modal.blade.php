@php
    $variants = $product->sellerVariants;
@endphp

<div class="space-y-4">
    @if($variants->isEmpty())
        <div class="text-center py-4">
            <div class="text-gray-500">No variants found for this product.</div>
        </div>
    @else
        <div class="divide-y">
            @foreach($variants as $variant)
                <div class="py-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-medium">{{ $variant->name }}</h3>
                            @if($variant->sku)
                                <p class="text-sm text-gray-500">SKU: {{ $variant->sku }}</p>
                            @endif
                        </div>
                    </div>

                    @if($variant->description)
                        <p class="mt-2 text-sm text-gray-600">{{ $variant->description }}</p>
                    @endif

                    @if($variant->attributes && count($variant->attributes) > 0)
                        <div class="mt-2">
                            <div class="flex flex-wrap gap-2">
                                @foreach($variant->attributes as $key => $value)
                                    <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-1 text-xs font-medium text-gray-600">
                                        {{ $key }}: {{ $value }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($variant->prices->isNotEmpty())
                        <div class="mt-2">
                            <div class="flex flex-wrap gap-2">
                                @foreach($variant->prices as $price)
                                    <span class="inline-flex items-center rounded-md bg-blue-100 px-2 py-1 text-xs font-medium text-blue-700">
                                        {{ $price->currency->code }}: {{ $price->currency->symbol }}{{ $price->amount }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($variant->stocks->isNotEmpty())
                        <div class="mt-2">
                            <div class="flex flex-wrap gap-2">
                                @foreach($variant->stocks as $stock)
                                    <span class="inline-flex items-center rounded-md bg-green-100 px-2 py-1 text-xs font-medium text-green-700">
                                        {{ $stock->location->name }}: {{ $stock->quantity }} units
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>
