@extends('layouts.app')

@section('title', 'Profil Saya')
@section('page-title', 'Profil Saya')

@section('content')

<div style="max-width:900px;margin:0 auto;">

    @if(session('success'))
        <div class="alert alert-success mb-4" style="background:#dcfce7;border:1px solid #86efac;color:#166534;padding:14px 18px;border-radius:10px;display:flex;align-items:center;gap:10px;">
            <span>✅</span> {{ session('success') }}
        </div>
    @endif

    <div style="display:grid;grid-template-columns:280px 1fr;gap:24px;align-items:start;">

        <!-- Left: Avatar Card -->
        <div class="card" style="text-align:center;padding:32px 20px;">
            <div style="position:relative;display:inline-block;margin-bottom:16px;">
                <img src="{{ auth()->user()->avatar_url }}"
                     alt="{{ $user->name }}"
                     id="avatarPreview"
                     style="width:100px;height:100px;border-radius:50%;object-fit:cover;border:3px solid var(--color-primary);box-shadow:0 4px 12px rgba(37,99,235,.2);">
            </div>
            <div style="font-weight:700;font-size:16px;color:var(--text-primary);">{{ $user->name }}</div>
            <div style="font-size:13px;color:var(--text-muted);margin-bottom:4px;">{{ $user->email }}</div>
            <div class="badge badge-primary" style="margin-bottom:20px;">{{ $user->role?->display_name ?? 'User' }}</div>

            <form method="POST" action="{{ route('profile.avatar') }}" enctype="multipart/form-data">
                @csrf
                <label style="display:block;cursor:pointer;margin-bottom:8px;">
                    <input type="file" name="avatar" id="avatarInput" accept="image/*" style="display:none;" onchange="previewAvatar(this); this.form.submit();">
                    <span class="btn btn-secondary btn-sm" style="width:100%;justify-content:center;">
                        📷 Ganti Foto
                    </span>
                </label>
            </form>

            <div style="margin-top:20px;padding-top:20px;border-top:1px solid var(--border);text-align:left;">
                <div style="font-size:12px;color:var(--text-muted);margin-bottom:8px;">INFO AKUN</div>
                <div style="font-size:13px;color:var(--text-secondary);display:flex;flex-direction:column;gap:6px;">
                    <span>📱 {{ $user->phone ?? '-' }}</span>
                    <span>🕐 Login terakhir: {{ $user->last_login_at?->diffForHumans() ?? 'Belum pernah' }}</span>
                </div>
            </div>
        </div>

        <!-- Right: Forms -->
        <div style="display:flex;flex-direction:column;gap:20px;">

            <!-- Edit Profil -->
            <div class="card">
                <div class="card-header">
                    <h3 style="margin:0;font-size:16px;font-weight:600;">✏️ Edit Informasi Profil</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('PUT')

                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                            <div class="form-group">
                                <label class="form-label">Nama Lengkap <span style="color:var(--color-danger)">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name', $user->name) }}" required>
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label">Email <span style="color:var(--color-danger)">*</span></label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email', $user->email) }}" required>
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="form-group" style="margin-bottom:20px;">
                            <label class="form-label">No. Telepon</label>
                            <input type="text" name="phone" class="form-control"
                                   value="{{ old('phone', $user->phone) }}" placeholder="+62...">
                        </div>

                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </form>
                </div>
            </div>

            <!-- Ganti Password -->
            <div class="card">
                <div class="card-header">
                    <h3 style="margin:0;font-size:16px;font-weight:600;">🔒 Ganti Password</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('profile.password') }}">
                        @csrf
                        @method('PUT')

                        <div class="form-group" style="margin-bottom:16px;">
                            <label class="form-label">Password Lama <span style="color:var(--color-danger)">*</span></label>
                            <input type="password" name="current_password"
                                   class="form-control @error('current_password') is-invalid @enderror" required>
                            @error('current_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;">
                            <div class="form-group">
                                <label class="form-label">Password Baru <span style="color:var(--color-danger)">*</span></label>
                                <input type="password" name="password"
                                       class="form-control @error('password') is-invalid @enderror" required>
                                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label">Konfirmasi Password Baru <span style="color:var(--color-danger)">*</span></label>
                                <input type="password" name="password_confirmation" class="form-control" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Perbarui Password</button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
function previewAvatar(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => document.getElementById('avatarPreview').src = e.target.result;
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

@endsection
