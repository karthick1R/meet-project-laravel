@extends('layouts.app')

@section('title', 'Access Keys')

@section('content')

<style>
.license-glass {
    background: #f8fafc;
    border: 1px solid #e5e7eb;
    border-radius: 20px;
    padding: 24px;
    box-shadow: 0 20px 50px rgba(0,0,0,0.12);
    animation: fadeSlide .8s ease forwards;
}

@keyframes fadeSlide {
    from { opacity: 0; transform: translateY(20px); }
    to   { opacity: 1; transform: translateY(0); }
}

.status-pill {
    border-radius: 999px;
    padding: 6px 14px;
    font-size: 0.85rem;
    font-weight: 600;
}

.btn-gradient {
    background: linear-gradient(135deg, #6530ff, #4f46e5);
    border: none;
    color: #fff;
    border-radius: 14px;
    font-weight: 600;
    padding: 8px 18px;
    transition: .3s;
}

.btn-gradient:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(79,70,229,0.35);
}

.table tbody tr:hover {
    background: #f1f5f9;
}
</style>

<div class="container py-4">
    <div class="license-glass">

        <!-- HEADER -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-dark mb-1">Access Keys</h2>
                <p class="text-muted mb-0">
                    Manage user access and registration links.
                </p>
            </div>

            <a href="{{ route('product-key.index') }}" class="btn btn-gradient">
                <i class="fas fa-plus me-2"></i>
                Create New Access
            </a>
        </div>

        <!-- TABLE -->
        <div class="table-responsive">
            <table class="table align-middle">
                <thead class="table-light">
                    <tr class="text-uppercase text-muted">
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Access Key</th>
                        <th>Status</th>
                        <th>Payment</th>
                        <th>Created</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($productKeys as $productKey)
                        <tr>
                            <td>{{ $productKey->email }}</td>
                            <td>{{ $productKey->phone }}</td>
                            <td class="fw-bold">
                                {{ $productKey->product_key }}
                            </td>
                            <td>
                                <span class="status-pill badge {{ $productKey->is_active ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $productKey->is_active ? 'Active' : 'Disabled' }}
                                </span>
                            </td>
                            <td>
                                @if($productKey->payment_status === 'completed')
                                    <span class="status-pill badge bg-success">Paid</span>
                                @elseif($productKey->payment_status === 'pending')
                                    <span class="status-pill badge bg-warning">Pending</span>
                                @else
                                    <span class="status-pill badge bg-danger">Failed</span>
                                @endif
                            </td>
                            <td>
                                {{ $productKey->created_at->format('M d, Y H:i') }}
                            </td>
                            <td class="text-end">

                                <!-- TOGGLE -->
                                <form action="{{ route('admin.product-keys.toggle', $productKey) }}"
                                      method="POST"
                                      class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-gradient">
                                        {{ $productKey->is_active ? 'Disable' : 'Enable' }}
                                    </button>
                                </form>

                                <!-- RESEND -->
                                @if($productKey->payment_status === 'completed')
                                    <form action="{{ route('admin.product-keys.resend', $productKey) }}"
                                          method="POST"
                                          class="d-inline ms-2">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-secondary">
                                            Resend Link
                                        </button>
                                    </form>
                                @endif

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                No access keys created yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- PAGINATION -->
        <div class="mt-3">
            {{ $productKeys->links() }}
        </div>

    </div>
</div>

@endsection
