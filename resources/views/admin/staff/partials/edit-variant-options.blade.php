{{-- ================== Addons Section ================== --}}
@php
    $cartAddons = is_string($cart->addon_id) ? json_decode($cart->addon_id, true) : $cart->addon_id;
    $cartIngredients = is_string($cart->ing_id) ? json_decode($cart->ing_id, true) : $cart->ing_id;
    $removedIngredientsDetails = is_string($cart->removed_ingredients) ? json_decode($cart->removed_ingredients, true) : $cart->removed_ingredients;
    $selectedAddonIds = collect($cartAddons)->pluck('addon_id')->toArray();
    $selectedIngIds = collect($cartIngredients)->pluck('ing_id')->toArray();
    $selectedRemovedIngIds = collect($removedIngredientsDetails)->pluck('ing_id')->toArray();
@endphp

@if (!empty($addonIngredients) && count($addonIngredients) > 0)
    <div class="mb-4">
        <h6 class="fw-bold">Addons</h6>

        @foreach ($addonIngredients as $addon)
            <div class="mb-3">
                <h6 class="text-capitalize">{{ $addon['name'] }}</h6>
                <div class="row g-2">
                    @foreach ($addon['others'] as $opt)
                        @php
                            $inputId = "addon-{$opt['ing_id']}";
                            $isChecked = in_array($opt['ing_id'], $selectedAddonIds ?? []);
                        @endphp

                        <div class="col-6">
                            <div class="form-check addon-item border rounded p-2">
                                <input type="radio"
                                    class="form-check-input addon-checkbox ingredient-{{ $opt['ing_id'] }}"
                                    name="addon_category_{{ $addon['id'] }}" id="{{ $inputId }}"
                                    value="{{ $opt['ing_id'] }}" data-price="{{ $opt['price'] }}"
                                    @if ($isChecked) checked @endif>
                                <label class="form-check-label w-100 d-flex justify-content-between"
                                    for="{{ $inputId }}">
                                    <span>{{ $opt['name'] }}</span>
                                    <span class="addon-price">{{ number_format($opt['price'], 2) }}</span>
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
@endif


{{-- ================== Ingredients Section ================== --}}
@if (!empty($ingrediants) && count($ingrediants) > 0)
    <div class="mb-4">
        <h6 class="fw-bold">Ingredients</h6>

        @forelse($ingrediants as $ing)
            @if ($ing->ingredientCategory)
                <div class="mb-3">
                    <h6 class="text-capitalize">{{ $ing->ingredientCategory->name }}</h6>
                    <div class="row g-2">
                        @foreach ($ing->ingredientCategory->ingredients as $ingredient)
                            @php
                                $variantSizeId = $variant->sizes?->id ?? null;

                                $price = $ingredient->sizes->where('size_id', $variantSizeId)->first()->price ?? 0;

                                $isDefault =
                                    $ing->defaultIngredient && $ing->defaultIngredient->ing_id == $ingredient->ing_id;
                                $inputId = "ingredient-{$ing->ingredientCategory->id}-{$ingredient->ing_id}";
                                $isChecked = in_array($ingredient->ing_id, $selectedIngIds ?? []);
                            @endphp

                            <div class="col-6">
                                <div class="form-check ingredient-item border rounded p-2">
                                    <input type="{{ $ing->type == 'required' ? 'radio' : 'checkbox' }}"
                                        class="form-check-input ingredient-radio ingredient-{{ $ingredient->ing_id }}"
                                        name="ingredient_category_{{ $ing->ingredientCategory->id }}{{ $ing->type == 'optional' ? '[]' : '' }}"
                                        id="{{ $inputId }}" value="{{ $ingredient->ing_id }}"
                                        is_default="{{ $isDefault }}" data-price="{{ $isDefault ? 0 : $price }}"
                                        @if ($isDefault && $ing->type == 'required') checked
                                      @elseif($isChecked) checked @endif>
                                    <label
                                        class="form-check-label w-100 d-flex justify-content-between align-items-center"
                                        for="{{ $inputId }}">
                                        <span>{{ $ingredient->ing_name }}</span>
                                        <span
                                            class="ingredient-price">{{ number_format($isDefault ? 0 : $price, 2) }}</span>
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @empty
        @endforelse
    </div>
@endif

{{-- ================== Total Price Section ================== --}}

<div class="alert alert-info d-flex justify-content-between align-items-center mt-4">
    <span class="fw-bold">Total Price:</span>
    <span class="fs-5 fw-bold" id="product-total-price" data-base-price="0">Rs:0</span>
</div>
