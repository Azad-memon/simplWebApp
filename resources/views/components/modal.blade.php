<style>
    .modal .modal-loader {
  position: absolute;
  inset: 0; /* top:0; right:0; bottom:0; left:0; */
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(255,255,255,0.75);
  z-index: 1055; /* above modal body but below modal-backdrop */
}
</style>
<!-- place this in your modal component - make sure modal-body has position-relative -->
<div class="modal fade" id="{{ $id }}" tabindex="-1" role="dialog" aria-labelledby="{{ $id }}Label" aria-hidden="true">
  <div class="modal-dialog modal-lg {{ $size }}" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="{{ $id }}Label">{{ $title }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body position-relative">
        {{ $slot }}

        <!-- modal loader: hidden by default via d-none -->
        <div class="modal-loader d-none" aria-hidden="true">
          <div class="spinner-border text-primary" role="status" style="width:3rem; height:3rem;">
            <span class="visually-hidden">Loading...</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
