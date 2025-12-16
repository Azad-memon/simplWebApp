<a href="javascript:void(0);"
   class="toggle-user-status"
   data-id="{{ $id }}"
   data-status="{{ $status }}"
   data-url="{{ $url }}"
   title="{{ in_array($status, ['active', 1, '1']) ? 'Active' : 'Inactive' }}">

    <div>
        <label class="switch mb-0">
            <input type="checkbox" class="user-status-checkbox" {{ in_array($status, ['active', 1, '1']) ? 'checked' : '' }}>
            <span class="{{ in_array($status, ['active', 1, '1']) ? 'switch-state bg-success' : 'switch-state bg-danger' }}"></span>
        </label>
    </div>
</a>


