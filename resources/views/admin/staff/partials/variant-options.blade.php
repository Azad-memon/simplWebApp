 {{-- Addons & Ingredients Side-by-Side --}}
                    {{-- Right Side: Ingredients --}}
                    <div class="row">

                    <div class="col-md-12 border-end pe-3">
                        {{-- ================== Ingredients Section ================== --}}
                        @if(!empty($ingrediants) && count($ingrediants) > 0)

                            <div class="mb-4">
                                {{-- <h5 class="fw-bold text-primary mb-3">Ingredients</h5> --}}
                                @forelse($ingrediants as $ing)
                                    @php $Main_eng_quantity = $ing->quantity ?? 0;

                                  //  print_r( $ing);
                                    @endphp
                                    @if($ing->ingredientCategory && $ing->status == 1)
                                    @php
                                        $isInactive = $ing->status == 0;
                                    @endphp
                                        <div class="mb-3">
                                            <h6 class="text-capitalize">{{ $ing->ingredientCategory->name }}</h6>
                                            <div class="row g-2">
                                                @php
                                                    $defaultId = $ing->defaultIngredient->ing_id ?? null;
                                                    $ingredients = $ing->ingredientCategory->ingredients->sortByDesc(fn($ingredient) => $ingredient->ing_id == $defaultId);
                                                @endphp

                                                @foreach($ingredients as $ingredient)
                                                    @php
                                                        $variantSizeId = $variant->sizes?->id ?? null;
                                                       // $price = $ingredient->sizes->where('size_id', $variantSizeId)->first()->price ?? 0;
                                                        $price = 0;
                                                        $isDefault = $defaultId && $defaultId == $ingredient->ing_id;
                                                        $inputId = "ingredient-{$ing->ingredientCategory->id}-{$ingredient->ing_id}";
                                                    @endphp
                                                    <div class="col-3">
                                                        <div class="form-check ingredient-item border rounded p-2">
                                                            <input
                                                                type="{{ $ing->type == 'required' ? 'radio' : 'checkbox' }}"
                                                                class="form-check-input ingredient-radio ingredient-{{ $ingredient->ing_id }}"
                                                                name="ingredient_category_{{ $ing->ingredientCategory->id }}{{ $ing->type == 'optional' ? '[]' : '' }}"
                                                                id="{{ $inputId }}"
                                                                value="{{ $ingredient->ing_id }}"
                                                                data-is_default="{{ $isDefault }}"
                                                                data-price="{{ $isDefault ? 0 : $price }}"
                                                                @if($isDefault) checked @endif
                                                                data-quantity="{{ $Main_eng_quantity }}">
                                                            <label class="form-check-label w-100 d-flex justify-content-between align-items-center  @if($isDefault) active @endif"
                                                                   for="{{ $inputId }}">
                                                                <span>{{ $ingredient->ingredient_label ?? $ingredient->ing_name }}</span>
                                                                <span class="ingredient-price text-muted">Rs {{ number_format($isDefault ? 0 : $price, 2) }}</span>
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
                    </div>
                       {{-- Left Side: Addons --}}


                       @if(!empty($addonIngredients) && count($addonIngredients) > 0)
<div class="col-md-12 border-end pe-4">

    <div class="mb-4">
        <h5 class="fw-bold text-primary mb-3">Addons</h5>

        {{-- ================= SINGLE ITEM CATEGORIES (ONE ROW) ================= --}}
        <div class="row g-3 mb-4">
            @foreach($addonIngredients as $addon)
                @if(count($addon['others']) == 1)
                    <div class="col-md-3 col-sm-6 col-12">
                        <div class="mb-3">
                            <h6 class="text-capitalize mb-2">{{ $addon['name'] }}</h6>

                            @foreach($addon['others'] as $opt)
                                 <div class="col-12">
                                                    <div class="form-check addon-item border rounded p-2">
                                                        <input type="checkbox"
                                                               data-category-id="{{ $addon['addon_id'] }}"
                                                               data-addon_cat_id="{{ $addon['cat_id'] }}"
                                                               class="form-check-input addon-checkbox ingredient-{{ $opt['ing_id'] }}"
                                                               name="addon_category_{{ $addon['id'] }}"
                                                               id="addon-{{ $opt['ing_id'] }}"
                                                               value="{{ $opt['ing_id'] }}"
                                                               data-price="{{ $opt['price'] }}"
                                                               data-quantity="{{ $addon['quantity'] }}"
                                                               data-replace="{{ $addon['is_replace'] }}"
                                                               >
                                                        <label class="form-check-label w-100 d-flex justify-content-between"
                                                               for="addon-{{ $opt['ing_id'] }}">
                                                            <span>{{ $opt['name'] }}</span>
                                                            <span class="addon-price text-muted">Rs {{ number_format($opt['price'], 2) }}</span>
                                                        </label>
                                                    </div>
                                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        {{-- ================= MULTI ITEM CATEGORIES (NORMAL FLOW) ================= --}}
        <div class="row g-3">
            @foreach($addonIngredients as $addon)
                @if(count($addon['others']) > 1)
                    <div class="row g-3">
                        <div class="h-100 row g-2">
                            <h6 class="text-capitalize mb-3">{{ $addon['name'] }}</h6>

                            @foreach($addon['others'] as $opt)
                                <div class="col-3">
                                                    <div class="form-check addon-item border rounded p-2">
                                                        <input type="checkbox"
                                                               data-category-id="{{ $addon['addon_id'] }}"
                                                               data-addon_cat_id="{{ $addon['cat_id'] }}"
                                                               class="form-check-input addon-checkbox ingredient-{{ $opt['ing_id'] }}"
                                                               name="addon_category_{{ $addon['id'] }}"
                                                               id="addon-{{ $opt['ing_id'] }}"
                                                               value="{{ $opt['ing_id'] }}"
                                                               data-price="{{ $opt['price'] }}"
                                                               data-quantity="{{ $addon['quantity'] }}"
                                                               data-replace="{{ $addon['is_replace'] }}"
                                                               >
                                                        <label class="form-check-label w-100 d-flex justify-content-between"
                                                               for="addon-{{ $opt['ing_id'] }}">
                                                            <span>{{ $opt['name'] }}</span>
                                                            <span class="addon-price text-muted">Rs {{ number_format($opt['price'], 2) }}</span>
                                                        </label>
                                                    </div>
                                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

    </div>

</div>
@endif


                {{-- ================== Total Price Section ================== --}}
                <div class="alert alert-info d-flex justify-content-between align-items-center mt-4">
                    <span class="fw-bold">Total Price:</span>
                    <span class="fs-5 fw-bold" id="product-total-price" data-base-price="0">Rs:0</span>
                </div>
