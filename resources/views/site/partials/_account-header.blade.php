@php $user = auth()->user(); @endphp

<div class="profile-hero">
    <div class="profile-hero-body">
        <div class="profile-avatar-wrap">
            @if ($user->avatar)
                <img src="{{ asset('uploads/'.$user->avatar) }}" alt="{{ $user->name }}" class="profile-avatar">
            @else
                <span class="profile-avatar">{{ Str::of($user->name)->substr(0, 1)->upper() }}</span>
            @endif

            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" id="avatar-form">
                @csrf
                @method('PUT')
                <input type="hidden" name="name" value="{{ $user->name }}">
                <input type="hidden" name="email" value="{{ $user->email }}">
                <input type="hidden" name="phone" value="{{ $user->phone }}">

                <label for="avatar" class="profile-avatar-edit" title="Change photo">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                </label>
                <input id="avatar" type="file" name="avatar" accept="image/*" class="profile-avatar-input">
            </form>
        </div>

        <div class="profile-hero-info">
            <h1 class="profile-hero-name">{{ $user->name }}</h1>
            <p class="profile-hero-email">{{ $user->email }}</p>
        </div>
    </div>
</div>
