@extends('admin.layouts.master')
@section('title', 'Products')

@section('content')
    <style>
        td {
            position: relative;
            overflow: visible !important;
        }

        .dropdown-menu {
            z-index: 9999;
        }
    </style>
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h5>Product List</h5>
            </div>
            <div class="card-body table-responsive">
                <table class="hover dataTable" id="example-style-4" role="grid" aria-describedby="example-style-4_info">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Image</th>
                            {{-- <th>Video</th> --}}
                            <th>Category</th>
                            <th>Product Type</th>
                            <th>Slug</th>
                             <th style="max-width:100px">Last Updated</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $i => $product)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $product->name }}</td>
                                <td>

                                        <div class="gallery my-gallery" itemscope="">

                                                    <figure class="col-xl-3 col-md-4 col-6 custom-image-container"
                                                        itemprop="associatedMedia" itemscope>
                                                        <a class="image-popup-no-margins" href="{{$product->main_image }}"
                                                            itemprop="contentUrl" data-size="800x800">
                                                            <img class="img-thumbnail custom-img-responsive"
                                                                alt="{{ ucfirst($product->name) }}"
                                                                src="{{ $product->main_image }}" width="50" height="50"
                                                                itemprop="thumbnail">
                                                        </a>
                                                        <figcaption itemprop="caption description">
                                                            {{ ucfirst($product->name) }}</figcaption>
                                                    </figure>

                                        </div>

                                </td>
                                <td>{{ $product->category->name ?? '-' }}</td>
                                <td>{{ $product->product_type }}</td>
                                <td>{{ $product->slug }}</td>
                                <td>

                                    {{ $product->updated_at ? $product->updated_at->diffForHumans() : '-' }}
                                </td>
                                <td class="text-nowrap">
                                    <a href="{{ route('badmin.products.view',$product->id) }}" class="btn btn-success edit-addon">
                                    <i class="fa fa-eye"></i>
                                    </a>

                                </td>

                            </tr>
                        @endforeach


                    </tbody>
                    <tfoot>

                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Image</th>
                            <th>Category</th>
                            <th>Product Type</th>
                            <th>Slug</th>
                            <th style="max-width:100px">Last Updated</th>
                            <th>Actions</th>
                        </tr>

                    </tfoot>
                </table>

            </div>
        </div>
    </div>
@endsection
