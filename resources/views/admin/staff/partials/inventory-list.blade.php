@if (!empty($inventories))
    @php $serial = 1; @endphp
    @foreach ($inventories as $value)
        @php
            $availableQty = $value->quantity_balance ?? 0;
            $minQty = $value->min_quantity ?? 0;
            $isLowStock = $availableQty <= $minQty;
        @endphp
        <tr>
            <td>{{ $serial++ }}</td>
            <td>
                <div class="gallery my-gallery" itemscope="">
                    <figure class="col-xl-3 col-md-4 col-6 custom-image-container" itemprop="associatedMedia" itemscope>
                        <a class="image-popup-no-margins"
                            href="{{ $value['main_image'] ?? asset('assets/images/no-img.png') }}" itemprop="contentUrl"
                            data-size="800x800">
                            <img class="img-thumbnail custom-img-responsive" alt="{{ ucfirst($value['ing_name']) }}"
                                src="{{ $value['main_image'] ?? asset('assets/images/no-img.png') }}" width="50"
                                height="50" itemprop="thumbnail"
                                style="min-width: 70px;
    height: 80px;
    object-fit: cover;">
                        </a>
                    </figure>
                </div>
            </td>
            <td>{{ $value['ing_name'] }}</td>
            <td>{{ $value->unit ? $value->unit->name : '' }}</td>
            <td>{{ $availableQty }}</td>
            <td>
                @if ($isLowStock)
                    <span class="badge bg-danger">Low Stock</span>
                @else
                    <span class="badge bg-success">In Stock</span>
                @endif
            </td>
            <td>{{ isset($value->branchQuantities[0]) ? $value->branchQuantities[0]->updater->first_name : '' }}</td>
            <td>{{ isset($value->branchQuantities[0]) ? $value->branchQuantities[0]->updated_at->diffForHumans() : $value['updated_at']->diffForHumans() }}
            </td>
            <td class="">
                @if ($value['ing_type'] == 'custom')
                    <a href="#" id="update-branch-ingredient" class="btn btn-primary btn-sm" data-is_pos="true"
                        data-id="{{ $value['ing_id'] }}">
                        <i class="mdi mdi-upload-outline"></i> Update
                    </a>
                @else
                    <input type="number" class="form-control form-control-sm me-2" name="quantity" placeholder="Qty"
                        min="0" step="any" style="width:100px;display: inline;"
                        id="quantity-{{ $value['ing_id'] }}">
                    <button type="button" class="btn btn-dark btn-sm" id="ingredientFormqty"
                        data-branchId="{{ $branchId ?? '' }}" data-ingId="{{ $value['ing_id'] }}">
                        Stock In
                    </button>
                @endif
            </td>
        </tr>
    @endforeach
@endif
