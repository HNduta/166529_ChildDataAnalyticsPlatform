<section>
    <header class="pb-5 border-b border-[var(--line)]">
        <p class="eyebrow uppercase mb-1">Personal Profile</p>
        <h2 class="text-lg font-extrabold tracking-tight text-[var(--ink)]">
            Profile Information &amp; Photo
        </h2>
        <p class="mt-1 text-xs text-[var(--ink-soft)] leading-relaxed">
            Upload a personal profile picture and keep your account name and email address up to date.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        {{-- Profile Photo Upload Card --}}
        <div>
            <label class="field-label">Profile Picture</label>
            @php
                $userAvatarUrl = $user->profilePictureUrl();
            @endphp
            <div class="p-4 rounded-2xl border border-[var(--line)] bg-[var(--canvas)] flex flex-col sm:flex-row sm:items-center gap-4">
                <div class="w-20 h-20 shrink-0 rounded-2xl border-2 border-[var(--accent-soft)] bg-[var(--accent-tint)] flex items-center justify-center overflow-hidden text-lg font-extrabold text-[var(--accent)] shadow-sm">
                    @if ($userAvatarUrl)
                        <img id="profileAvatarPreviewImg" src="{{ $userAvatarUrl }}" alt="{{ $user->name }} profile picture" class="w-full h-full object-cover">
                        <span id="profileAvatarInitials" class="hidden">{{ $user->initials() }}</span>
                    @else
                        <img id="profileAvatarPreviewImg" src="" alt="{{ $user->name }} profile picture" class="hidden w-full h-full object-cover">
                        <span id="profileAvatarInitials">{{ $user->initials() }}</span>
                    @endif
                </div>

                <div class="flex-1 min-w-0 space-y-2.5">
                    <div class="flex flex-wrap items-center gap-2.5">
                        <label
                            for="profile_picture"
                            class="inline-flex items-center gap-2 bg-white border border-[var(--line)] text-[var(--ink)] hover:border-[var(--ink)] text-xs font-semibold px-3.5 py-2 rounded-lg cursor-pointer transition shadow-sm"
                        >
                            <svg class="w-4 h-4 text-[var(--accent)]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M4 16l4.5-4.5a2 2 0 012.8 0L16 16m-2-2l1.5-1.5a2 2 0 012.8 0L20 14"/>
                                <rect x="3" y="5" width="18" height="14" rx="2"/>
                                <circle cx="8.5" cy="9.5" r="1.5"/>
                            </svg>
                            <span>Choose New Photo</span>
                        </label>

                        <span id="selectedPhotoFilename" class="text-xs text-[var(--ink-soft)] truncate max-w-[220px]">
                            {{ !empty($user->profile_picture) ? 'Current photo active' : 'No file selected' }}
                        </span>

                        <input
                            id="profile_picture"
                            name="profile_picture"
                            type="file"
                            accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                            class="sr-only"
                        />
                    </div>

                    <p class="text-[11.5px] text-[var(--ink-soft)] leading-relaxed">
                        Accepted formats: <strong>JPG, PNG, or WEBP</strong> (maximum 2 MB). Your photo appears across your dashboard and navigation bar.
                    </p>

                    @if (!empty($user->profile_picture))
                        <label class="inline-flex items-center gap-2 text-xs text-[var(--ink-soft)] hover:text-[var(--ink)] cursor-pointer pt-1">
                            <input type="checkbox" name="remove_profile_picture" value="1" class="rounded border-[var(--line)] text-[var(--ink)]">
                            <span>Remove current profile picture and revert to default avatar</span>
                        </label>
                    @endif
                </div>
            </div>

            @error('profile_picture')
                <p class="mt-2 text-xs font-medium text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Full Name --}}
        <div>
            <label for="name" class="field-label">Full Name</label>
            <div class="relative">
                <svg class="w-4 h-4 field-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <circle cx="12" cy="8" r="4"/>
                    <path d="M4 21c0-4 4-6 8-6s8 2 8 6"/>
                </svg>
                <input
                    id="name"
                    name="name"
                    type="text"
                    value="{{ old('name', $user->name) }}"
                    required
                    autofocus
                    autocomplete="name"
                    placeholder="Enter your full name"
                    class="field-input"
                />
            </div>
            @error('name')
                <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Email Address --}}
        <div>
            <label for="email" class="field-label">Email Address</label>
            <div class="relative">
                <svg class="w-4 h-4 field-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <rect x="3" y="5" width="18" height="14" rx="2"/>
                    <path d="M3 7l9 6 9-6"/>
                </svg>
                <input
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email', $user->email) }}"
                    required
                    autocomplete="username"
                    placeholder="name@example.com"
                    class="field-input"
                />
            </div>
            @error('email')
                <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
            @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-3 p-3 rounded-xl bg-[var(--accent-tint)] border border-[var(--accent-soft)] text-xs">
                    <p class="text-[var(--ink)]">
                        {{ __('Your email address is unverified.') }}
                        <button form="send-verification" class="underline font-semibold text-[var(--accent)] hover:opacity-80 ml-1">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-1.5 font-semibold text-emerald-700">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        {{-- Role Summary (Read-Only) --}}
        <div class="flex items-center justify-between bg-[var(--canvas)] border border-[var(--line)] rounded-xl px-4 py-3">
            <div>
                <p class="text-xs font-bold text-[var(--ink)]">Account Role</p>
                <p class="text-[11px] text-[var(--ink-soft)]">Determines your workspace permissions on AutiSync</p>
            </div>
            <span class="text-xs font-semibold bg-white border border-[var(--line)] px-3 py-1 rounded-full">
                {{ ucfirst($user->role ?? 'caregiver') }}
            </span>
        </div>

        {{-- Save Button --}}
        <div class="pt-2 flex flex-wrap items-center gap-3">
            <button
                type="submit"
                class="inline-flex items-center justify-center gap-2 bg-[var(--ink)] text-white text-sm font-semibold px-5 py-2.5 rounded-lg hover:opacity-90 transition"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                <span>Save Profile Changes</span>
            </button>

            @if (session('status') === 'profile-updated')
                <span class="text-xs font-semibold text-emerald-700">
                    Saved!
                </span>
            @endif
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const input = document.getElementById('profile_picture');
            const previewImg = document.getElementById('profileAvatarPreviewImg');
            const initials = document.getElementById('profileAvatarInitials');
            const filenameLabel = document.getElementById('selectedPhotoFilename');

            if (input) {
                input.addEventListener('change', function (e) {
                    const file = e.target.files && e.target.files[0];
                    if (!file) return;

                    if (filenameLabel) {
                        filenameLabel.textContent = file.name;
                    }

                    if (file.type.startsWith('image/') && previewImg) {
                        const reader = new FileReader();
                        reader.onload = function (ev) {
                            previewImg.src = ev.target.result;
                            previewImg.classList.remove('hidden');
                            if (initials) initials.classList.add('hidden');
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }
        });
    </script>
</section>
