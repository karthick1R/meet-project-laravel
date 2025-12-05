@extends('layouts.app')

@section('title', 'User Management')

@section('content')

    <style>
        .card-glass {
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            border-radius: 20px;
            padding: 24px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.12);
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
    </style>

    <div class="container py-4">
        <div class="card-glass">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="mb-1">User Management</h3>
                    <p class="text-muted mb-0">Manage all system users</p>
                </div>
                <button class="btn btn-gradient" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    <i class="fas fa-plus me-2"></i>Add User
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->phone ?? '-' }}</td>
                                <td>
                                    <span class="status-pill bg-primary text-white">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="status-pill bg-success text-white">Active</span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" onclick="editUser({{ $user->id }})">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    @if($user->id !== auth()->id())
                                        <button class="btn btn-sm btn-outline-danger" onclick="deleteUser({{ $user->id }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No users found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $users->links() }}
        </div>
    </div>

    <!-- MODAL -->
    <div class="modal fade" id="addUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Add User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form id="userForm" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">

                        <input type="hidden" id="userId" name="user_id">

                        <div class="mb-3">
                            <label>Name *</label>
                            <input type="text" class="form-control" name="name" id="userName">
                        </div>

                        <div class="mb-3">
                            <label>Email *</label>
                            <input type="email" class="form-control" name="email" id="userEmail">
                        </div>

                        <div class="mb-3">
                            <label>Phone</label>
                            <input type="text" class="form-control" name="phone" id="userPhone"
                                oninput="this.value=this.value.replace(/[^0-9]/g,'')">
                        </div>

                        <div class="mb-3">
                            <label>Password *</label>
                            <input type="password" class="form-control" name="password" id="userPassword">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <select class="form-control" name="role" id="userRole" required>

                                
                                @if(auth()->check() && auth()->user()->isSuperAdmin())
                                    <option value="admin">Admin</option>
                                @endif

                               
                                @if(auth()->check() && auth()->user()->isAdmin())
                                    <option value="user">User</option>
                                @endif

                            </select>
                        </div>


                        <div class="mb-3">
                            <label>Logo</label>
                            <input type="file" class="form-control" name="logo" accept="image/png,image/jpeg,image/jpg">
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="submit" id="saveBtn" class="btn btn-primary">
                            Save
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function editUser(id) {
            fetch(`/admin/users/${id}`)
                .then(res => res.json())
                .then(data => {
                    document.getElementById('modalTitle').innerText = "Edit User";
                    userId.value = data.id;
                    userName.value = data.name;
                    userEmail.value = data.email;
                    userPhone.value = data.phone;
                    userRole.value = data.role;
                    userPassword.required = false;

                    new bootstrap.Modal(addUserModal).show();
                });
        }

        function deleteUser(id) {
            Swal.fire({
                title: 'Are you sure?',
                icon: 'warning',
                showCancelButton: true
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/admin/users/${id}`, {
                        method: "DELETE",
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire("Deleted!", '', 'success');
                                setTimeout(() => location.reload(), 800);
                            }
                        });
                }
            });
        }

        document.getElementById('userForm').addEventListener('submit', function (e) {
            e.preventDefault();

            if (!userName.value || !userEmail.value || !userRole.value) {
                Swal.fire("Validation Error", "All required fields must be filled.", "warning");
                return;
            }

            if (userPassword.required && userPassword.value.length < 6) {
                Swal.fire("Password Error", "Password must be at least 6 characters.", "warning");
                return;
            }

            let btn = saveBtn;
            btn.disabled = true;
            btn.innerHTML = "Saving...";

            let formData = new FormData(this);
            let userIdVal = userId.value;

            if (userIdVal) {
                formData.append('_method', 'PUT');
            }

            let url = userIdVal ? `/admin/users/${userIdVal}` : `/admin/users`;

            fetch(url, {
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    btn.disabled = false;
                    btn.innerHTML = "Save";

                    if (data.success) {
                        bootstrap.Modal.getInstance(addUserModal).hide();
                        Swal.fire("Success", "User saved successfully", "success");
                        setTimeout(() => location.reload(), 900);
                    } else {
                        Swal.fire("Error", data.message || "Validation failed", "error");
                    }
                });
        });

        addUserModal.addEventListener('hidden.bs.modal', function () {
            userForm.reset();
            userId.value = '';
            modalTitle.innerText = "Add User";
            userPassword.required = true;
        });
    </script>

@endsection