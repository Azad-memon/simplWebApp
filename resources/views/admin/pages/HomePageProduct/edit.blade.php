<x-modal id="bannerModal" title="Update Product">
    <form method="POST" action="{{ route('admin.home.product.update') }}" id="bannerForm">
        @csrf
        <div class="mb-3" id="mediaFields">
           <x-video-upload id="banner_video" name="banner_video"  :value="getImageByType($banner->images, 'banner_video') ?? null" />
        </div>
        <div class="mb-3" id="productSelect">
            <label for="product_id">Select Product</label>
            <select class="form-control" id="product_id" name="product_id">
                <option value="">-- Select Product --</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}" @if($banner->product_id==$product->id) selected @endif>{{ $product->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Save</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Close">Close</button>
        </div>
    </form>
</x-modal>


