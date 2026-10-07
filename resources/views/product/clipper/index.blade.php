@php
    use App\Models\Role;
    use Illuminate\Support\Str;
@endphp
@extends('layouts.master')

@section('title', $title ?? _trans('keyword.Clipper Products'))

@section('content')

    <div class="flex-grow-1 container-p-y">
        {!! breadcrumb(_trans('keyword.Clipper Products') . ' ' . _trans('keyword.List'), [
            '#' => _trans('keyword.Product') . ' ' . _trans('keyword.Management'),
            'product-clipper/index' => _trans('keyword.Clipper Products') . ' ' . _trans('keyword.List'),
        ]) !!}

        <div class="app-ecommerce-category">
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('productClipper.index') }}">
                        <div class="row g-3 align-items-end">
                            <div class="col-lg-4 col-md-6">
                                <label for="clipper-search" class="form-label">{{ _trans('keyword.Product') }} {{ _trans('keyword.Search') }}</label>
                                <input
                                    id="clipper-search"
                                    type="text"
                                    name="search"
                                    value="{{ request('search') }}"
                                    class="form-control"
                                    placeholder="{{ _trans('keyword.Search') }} {{ _trans('keyword.Product') }}">
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ti ti-search me-1"></i>{{ _trans('keyword.Filter') }}
                                    </button>
                                    @if(request()->filled('search'))
                                        <a href="{{ route('productClipper.index') }}" class="btn btn-label-secondary">
                                            {{ _trans('keyword.Reset') }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Category List Table -->
            <div class="card">
                <div class="card-datatable">
                    <table class="data-table table border-top">
                        <thead>
                        <tr>
                            <th>{{_trans('keyword.SL')}}</th>
                            <th>{{ _trans('keyword.Image') }}</th>
                            <th>{{ _trans('keyword.Name') }}</th>
                            <th>{{ _trans('keyword.Category') }}</th>
                            <th>{{ _trans('keyword.Brand') }}</th>
                            <th>{{ _trans('keyword.Price') }}</th>
                            <th>{{ _trans('keyword.Status') }}</th>
                            <th>{{ _trans('keyword.Author') }}</th>
                            <th width="100px">{{ _trans('keyword.Action') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($productClippers as $key => $clipper)
                            @php
                                $attributeLabels = collect($clipper->attributes ?? [])
                                    ->map(function ($attribute) use ($attributeMap) {
                                        if (is_array($attribute)) {
                                            if (!empty($attribute['name']) && !empty($attribute['value'])) {
                                                return $attribute['name'] . ': ' . $attribute['value'];
                                            }

                                            return $attribute['name']
                                                ?? $attribute['title']
                                                ?? $attribute['value']
                                                ?? collect($attribute)->filter()->implode(': ');
                                        }

                                        if (is_object($attribute)) {
                                            if (!empty($attribute->name) && !empty($attribute->value)) {
                                                return $attribute->name . ': ' . $attribute->value;
                                            }

                                            return $attribute->name
                                                ?? $attribute->title
                                                ?? $attribute->value
                                                ?? null;
                                        }

                                        if (is_numeric($attribute)) {
                                            return $attributeMap[(int) $attribute] ?? (string) $attribute;
                                        }

                                        return filled($attribute) ? (string) $attribute : null;
                                    })
                                    ->filter()
                                    ->values()
                                    ->all();

                                $clipperPayload = json_encode([
                                    'name' => $clipper->name,
                                    'image' => $clipper->thumbnail_img,
                                    'url' => $clipper->product_url,
                                    'category' => $clipper->category?->name ?? '-',
                                    'brand' => $clipper->brand?->name ?? '-',
                                    'price' => $clipper->unit_price ?? '-',
                                    'description' => $clipper->description,
                                    'warranty_policy' => $clipper->warranty_policy,
                                    'attributes' => $clipper->attributes ?? [],
                                    'attribute_labels' => $attributeLabels,
                                    'author' => $clipper->user?->name ?? '-',
                                    'published_status' => $clipper->is_published ? 'Published' : 'Unpublished',
                                    'approval_status' => $clipper->is_approved == 1 ? 'Approved' : ($clipper->is_approved == 0 ? 'Pending' : 'Rejected'),
                                    'migrate_status' => $clipper->is_migrate ? 'Migrated' : 'Not Migrated',
                                    'created_at' => optional($clipper->created_at)->format('d M Y, h:i A'),
                                ], JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_TAG | JSON_HEX_QUOT);
                            @endphp
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>
                                    @if($clipper->thumbnail_img)
                                        <img src="{{ $clipper->thumbnail_img }}" alt="{{ $clipper->name }}" class="img-fluid" style="max-width: 50px; max-height: 50px; object-fit: cover;">
                                    @else
                                        <span class="text-muted">{{ _trans('keyword.No Image') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ $clipper->product_url }}" target="_blank" class="text-decoration-none">
                                        {{ $clipper->name }}
                                    </a>
                                    @if($clipper->description)
                                        <div class="text-muted small mt-1">{{ Str::limit(strip_tags($clipper->description), 80) }}</div>
                                    @endif
                                </td>
                                <td>{{ $clipper->category?->name ?? '-' }}</td>
                                <td>{{ $clipper->brand?->name ?? '-' }}</td>
                                <td>{{ $clipper->unit_price }}</td>
                                <td>
                                    @if($clipper->is_published)
                                        <span class="badge bg-success">{{ _trans('keyword.Published') }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ _trans('keyword.Unpublished') }}</span>
                                    @endif
                                    
                                    @if($clipper->is_approved == 1)
                                        <span class="badge bg-primary">{{ _trans('keyword.Approved') }}</span>
                                    @elseif($clipper->is_approved == 0)
                                        <span class="badge bg-warning">{{ _trans('keyword.Pending') }}</span>
                                    @else
                                        <span class="badge bg-danger">{{ _trans('keyword.Rejected') }}</span>
                                    @endif
                                </td>
                                <td>{{ $clipper->user?->name ?? '-' }}</td>
                                <td>
                                    <div class="d-inline-block text-nowrap">
                                        <button class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical me-2"></i></button>
                                        <div class="dropdown-menu dropdown-menu-end m-0">
                                            <button
                                                type="button"
                                                class="dropdown-item text-primary clipper-detail-trigger"
                                                data-bs-toggle="modal"
                                                data-bs-target="#clipperDetailModal"
                                                data-clipper="{{ $clipperPayload }}">
                                                <i class="ti ti-file-description"></i> {{ _trans('keyword.Details') }}
                                            </button>
                                            <a href="{{ $clipper->product_url }}" target="_blank" class="dropdown-item assign_visitors text-primary" ><i class="ti ti-eye"></i> {{_trans('keyword.View')}}</a>
                                            @if ($clipper->is_migrate == 0)
                                                <a href="{{ route('productClipper.edit',$clipper->id) }}" class="dropdown-item assign_visitors text-primary"><i class="ti ti-files"></i> {{_trans('keyword.Migrate')}}</a>
                                            @else
                                                <a href="{{ route('productClipper.edit',$clipper->id) }}" class="dropdown-item assign_visitors text-primary"><i class="ti ti-files"></i> {{_trans('keyword.Migrate Again')}}</a>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center">{{ _trans('keyword.No data found') }}</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                    <div class="col-md-12">
                        <div class="center text-center" style="display: table; margin-top: 25px; ">
                            {{ $productClippers->appends(request()->query())->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="clipperDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content clipper-detail-modal">
                <div class="modal-header border-0 pb-0">
                    <div>
                        <span class="badge rounded-pill bg-label-primary mb-2">{{ _trans('keyword.Clipper Products') }}</span>
                        <h4 class="modal-title mb-1" id="clipperModalTitle">-</h4>
                        <p class="text-muted mb-0" id="clipperModalCreatedAt">-</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-3">
                    <div class="row g-4">
                        <div class="col-lg-4">
                            <div class="clipper-detail-card h-100">
                                <div class="clipper-detail-image-wrap mb-3">
                                    <img src="" alt="Clipper Product" id="clipperModalImage" class="clipper-detail-image d-none">
                                    <div id="clipperModalImagePlaceholder" class="clipper-image-placeholder">
                                        <i class="ti ti-photo fs-1"></i>
                                        <p class="mb-0">{{ _trans('keyword.No Image') }}</p>
                                    </div>
                                </div>
                                <div class="d-flex flex-wrap gap-2 mb-3">
                                    <span class="badge rounded-pill bg-label-success" id="clipperPublishedBadge">-</span>
                                    <span class="badge rounded-pill bg-label-info" id="clipperApprovalBadge">-</span>
                                    <span class="badge rounded-pill bg-label-warning" id="clipperMigrateBadge">-</span>
                                </div>
                                <div class="clipper-meta-list">
                                    <div class="clipper-meta-item">
                                        <span>{{ _trans('keyword.Price') }}</span>
                                        <strong id="clipperModalPrice">-</strong>
                                    </div>
                                    <div class="clipper-meta-item">
                                        <span>{{ _trans('keyword.Category') }}</span>
                                        <strong id="clipperModalCategory">-</strong>
                                    </div>
                                    <div class="clipper-meta-item">
                                        <span>{{ _trans('keyword.Brand') }}</span>
                                        <strong id="clipperModalBrand">-</strong>
                                    </div>
                                    <div class="clipper-meta-item">
                                        <span>{{ _trans('keyword.Author') }}</span>
                                        <strong id="clipperModalAuthor">-</strong>
                                    </div>
                                </div>
                                <a href="#" target="_blank" id="clipperModalUrl" class="btn btn-primary w-100 mt-3">
                                    <i class="ti ti-external-link me-1"></i>{{ _trans('keyword.View') }}
                                </a>
                            </div>
                        </div>
                        <div class="col-lg-8">
                            <div class="clipper-detail-card mb-4">
                                <h5 class="clipper-section-title">{{ _trans('keyword.Description') }}</h5>
                                <div id="clipperModalDescription" class="clipper-richtext-empty">No description available.</div>
                            </div>
                            <div class="clipper-detail-card mb-4">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                                    <h5 class="clipper-section-title mb-0">{{ _trans('keyword.Attribute') }}</h5>
                                    <span class="badge bg-label-secondary" id="clipperModalAttributeCount">0</span>
                                </div>
                                <div id="clipperModalAttributes" class="d-flex flex-wrap gap-2">
                                    <span class="text-muted">No attributes available.</span>
                                </div>
                            </div>
                            <div class="clipper-detail-card">
                                <h5 class="clipper-section-title">{{ _trans('keyword.Privacy Policy') }}</h5>
                                <div id="clipperModalPrivacyPolicy" class="clipper-richtext-empty">No privacy policy available.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('styles')
    <style>
        .clipper-detail-modal {
            border: 0;
            border-radius: 24px;
            background: linear-gradient(180deg, #f8fbff 0%, #ffffff 100%);
            box-shadow: 0 24px 80px rgba(26, 71, 122, 0.18);
        }

        .clipper-detail-card {
            background: #fff;
            border: 1px solid #e8eef6;
            border-radius: 20px;
            padding: 1.25rem;
            box-shadow: 0 10px 30px rgba(37, 63, 97, 0.08);
        }

        .clipper-detail-image-wrap {
            background: linear-gradient(135deg, #eef6ff 0%, #f7fbff 100%);
            border-radius: 18px;
            overflow: hidden;
            min-height: 260px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .clipper-detail-image {
            width: 100%;
            height: 260px;
            object-fit: cover;
        }

        .clipper-image-placeholder {
            color: #7b8da6;
            text-align: center;
        }

        .clipper-meta-list {
            display: grid;
            gap: 0.85rem;
        }

        .clipper-meta-item {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid #edf2f7;
        }

        .clipper-meta-item span {
            color: #7b8da6;
            font-size: 0.85rem;
        }

        .clipper-meta-item strong {
            color: #22304a;
            text-align: right;
        }

        .clipper-section-title {
            color: #22304a;
            margin-bottom: 0.85rem;
        }

        .clipper-richtext-empty,
        .clipper-richtext-content {
            color: #4b5d79;
            line-height: 1.7;
        }

        .clipper-richtext-empty {
            background: #f8fbff;
            border: 1px dashed #d8e4f2;
            border-radius: 14px;
            padding: 1rem;
        }

        .clipper-attribute-badge {
            background: #eef6ff;
            color: #29558a;
            border: 1px solid #d3e5fa;
            border-radius: 999px;
            padding: 0.45rem 0.85rem;
            font-size: 0.85rem;
            font-weight: 600;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const detailButtons = document.querySelectorAll('.clipper-detail-trigger');
            const modalTitle = document.getElementById('clipperModalTitle');
            const modalCreatedAt = document.getElementById('clipperModalCreatedAt');
            const modalImage = document.getElementById('clipperModalImage');
            const modalImagePlaceholder = document.getElementById('clipperModalImagePlaceholder');
            const modalPrice = document.getElementById('clipperModalPrice');
            const modalCategory = document.getElementById('clipperModalCategory');
            const modalBrand = document.getElementById('clipperModalBrand');
            const modalAuthor = document.getElementById('clipperModalAuthor');
            const modalUrl = document.getElementById('clipperModalUrl');
            const modalDescription = document.getElementById('clipperModalDescription');
            const modalPrivacyPolicy = document.getElementById('clipperModalPrivacyPolicy');
            const modalAttributes = document.getElementById('clipperModalAttributes');
            const modalAttributeCount = document.getElementById('clipperModalAttributeCount');
            const publishedBadge = document.getElementById('clipperPublishedBadge');
            const approvalBadge = document.getElementById('clipperApprovalBadge');
            const migrateBadge = document.getElementById('clipperMigrateBadge');

            const setRichText = (element, html, emptyText) => {
                if (html && html.trim() !== '') {
                    element.innerHTML = html;
                    element.classList.remove('clipper-richtext-empty');
                    element.classList.add('clipper-richtext-content');
                } else {
                    element.textContent = emptyText;
                    element.classList.remove('clipper-richtext-content');
                    element.classList.add('clipper-richtext-empty');
                }
            };

            const normalizeAttributes = (clipper) => {
                if (Array.isArray(clipper.attribute_labels) && clipper.attribute_labels.length) {
                    return clipper.attribute_labels.filter(Boolean);
                }

                if (!Array.isArray(clipper.attributes)) {
                    return [];
                }

                return clipper.attributes.map((attribute) => {
                    if (attribute && typeof attribute === 'object') {
                        if (attribute.name && attribute.value) {
                            return `${attribute.name}: ${attribute.value}`;
                        }

                        return attribute.name
                            || attribute.title
                            || attribute.value
                            || Object.values(attribute).filter(Boolean).join(': ');
                    }

                    return attribute;
                }).filter(Boolean);
            };

            detailButtons.forEach((button) => {
                button.addEventListener('click', function () {
                    const clipper = JSON.parse(this.dataset.clipper || '{}');
                    const attributes = normalizeAttributes(clipper);

                    modalTitle.textContent = clipper.name || '-';
                    modalCreatedAt.textContent = clipper.created_at || '-';
                    modalPrice.textContent = clipper.price || '-';
                    modalCategory.textContent = clipper.category || '-';
                    modalBrand.textContent = clipper.brand || '-';
                    modalAuthor.textContent = clipper.author || '-';
                    modalUrl.href = clipper.url || '#';
                    publishedBadge.textContent = clipper.published_status || '-';
                    approvalBadge.textContent = clipper.approval_status || '-';
                    migrateBadge.textContent = clipper.migrate_status || '-';

                    if (clipper.image) {
                        modalImage.src = clipper.image;
                        modalImage.classList.remove('d-none');
                        modalImagePlaceholder.classList.add('d-none');
                    } else {
                        modalImage.src = '';
                        modalImage.classList.add('d-none');
                        modalImagePlaceholder.classList.remove('d-none');
                    }

                    setRichText(modalDescription, clipper.description, 'No description available.');
                    setRichText(modalPrivacyPolicy, clipper.warranty_policy, 'No privacy policy available.');

                    modalAttributes.innerHTML = '';
                    modalAttributeCount.textContent = attributes.length;

                    if (attributes.length) {
                        attributes.forEach((attribute) => {
                            const badge = document.createElement('span');
                            badge.className = 'clipper-attribute-badge';
                            badge.textContent = String(attribute);
                            modalAttributes.appendChild(badge);
                        });
                    } else {
                        modalAttributes.innerHTML = '<span class="text-muted">No attributes available.</span>';
                    }
                });
            });
        });
    </script>
@endpush
