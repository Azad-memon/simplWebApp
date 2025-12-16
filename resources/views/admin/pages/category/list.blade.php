@extends('admin.layouts.master')
@section('title', 'Categories')

@section('content')

<style>
body { background-color: #f8f9fc !important; }
.card {
    border: none;
    border-radius: 12px;
    box-shadow: 0 6px 18px rgba(43,37,112,0.04);
}
.btn-theme {
    background: linear-gradient(135deg, #7366ff, #8c7aff);
    color: #fff;
    border: none;
    padding: 8px 14px;
    border-radius: 8px;
    font-weight: 600;
}
.btn-theme:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(115,102,255,0.12);
}
.text-theme { color: #7366ff !important; }

.category-tree {
    background: #fff;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.tree-label {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.35rem 0;
    border-left: 2px solid #f0efff;
    padding-left: 1rem;
}

.tree-label strong { color: #2b2470; }
.tree-node { margin-left: 1rem; }

.toggle-icon {
    width: 12px;
    color: #7366ff;
}

.toggle-label {
    cursor: pointer;
}

.actions .btn {
    padding: 0.35rem 0.6rem;
    border-radius: 8px;
}

#searchInput {
    border-radius: 8px;
    border: 1px solid #e6e3ff;
    padding: 8px 10px;
}

.tree-label:hover {
    background: #f9f9ff;
    border-radius: 8px;
}

.nested { display: none; }
.nested.active { display: block; }
</style>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0 text-theme"><i class="fa fa-list me-2"></i> Category Management</h4>
            <small class="text-muted">Organize and manage nested categories</small>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button id="refreshCategories" class="btn btn-outline-secondary" title="Refresh">
                <i class="fa fa-sync"></i>
            </button>
            <button id="add-category" class="btn btn-theme">
                <i class="fa fa-plus me-1"></i> Add Category
            </button>
        </div>
    </div>

<div class="card p-3">
    <div class="category-tree">
        <input type="text" class="form-control mb-3" placeholder="Search categories..." id="searchInput" />
        <ul class="list-unstyled" id="category-root"></ul>
    </div>
</div>


</div>

@php $categoriesdata=[]; @endphp
@foreach($nestedCategories as $category)
@php $categoriesdata[]=$category @endphp
@endforeach

<script>
const categoriesdata = @json($categoriesdata);

function renderTree(data, parentElement, level = 0, isRoot = false) {
    data.forEach((item, i) => {
        const li = document.createElement('li');
        const hasChildren = item.children && item.children.length > 0;
        const icon = hasChildren
            ? `<i class="fas fa-chevron-${isRoot && i === 0 ? 'down' : 'right'} toggle-icon"></i>`
            : '';

        const viewDetailsUrl = "{{ route('admin.categories.view-details', '') }}/" + item.id;
        const deleteUrl = "{{ route('admin.category.delete', '') }}/" + item.id;

        li.innerHTML = `
            <div class="tree-label ${level > 0 ? 'tree-node' : ''}">
                <span class="${hasChildren ? 'toggle-label' : ''}" style="${hasChildren ? 'cursor: pointer;' : ''}">
                    ${icon}
                    <strong>${item.name}</strong>
                </span>
                <div class="actions">
                    <a href="${viewDetailsUrl}" class="btn btn-sm btn-outline-success" title="View Details">
                        <i class="fas fa-eye"></i>
                    </a>
                    <button class="btn btn-sm btn-outline-dark edit-category" data-id="${item.id}" title="Edit">
                        <i class="fa fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger delete-btn" data-action="${deleteUrl}" title="Delete">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            </div>
        `;

        if (hasChildren) {
            const ul = document.createElement('ul');
            ul.classList.add('nested', 'list-unstyled', 'tree-node');
            if (isRoot && i === 0) ul.classList.add('active');
            renderTree(item.children, ul, level + 1);
            li.appendChild(ul);
        }

        parentElement.appendChild(li);
    });
}

const rootElement = document.getElementById("category-root");
renderTree(categoriesdata, rootElement, 0, true);

document.getElementById("category-root").addEventListener("click", function (e) {
    const toggleSpan = e.target.closest(".toggle-label");
    if (!toggleSpan) return;

    const li = toggleSpan.closest("li");
    const ul = li.querySelector(":scope > ul.nested");
    const icon = toggleSpan.querySelector("i");

    if (ul) {
        ul.classList.toggle("active");
        if (icon && icon.classList.contains("fa-chevron-right")) {
            icon.classList.replace("fa-chevron-right", "fa-chevron-down");
        } else if (icon && icon.classList.contains("fa-chevron-down")) {
            icon.classList.replace("fa-chevron-down", "fa-chevron-right");
        }
    }
});

document.getElementById("searchInput").addEventListener("input", function () {
    const keyword = this.value.toLowerCase();
    const allLi = document.querySelectorAll("#category-root li");

    allLi.forEach(li => {
        const text = li.querySelector("strong")?.textContent.toLowerCase() || "";
        const match = text.includes(keyword);
        li.style.display = match ? "" : "none";

        if (match) {
            let parent = li.parentElement.closest("li");
            while (parent) {
                parent.style.display = "";
                const nested = parent.querySelector(":scope > ul.nested");
                if (nested) nested.classList.add("active");
                const icon = parent.querySelector(".toggle-icon");
                if (icon) {
                    icon.classList.add("fa-chevron-down");
                    icon.classList.remove("fa-chevron-right");
                }
                parent = parent.parentElement.closest("li");
            }
        }
    });
});

document.getElementById('refreshCategories')?.addEventListener('click', () => location.reload());
</script>

@endsection
