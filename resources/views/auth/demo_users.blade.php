<div class="divider my-4">
    <div class="divider-text">Demo users</div>
</div>
<div class="row">
    @forelse ($demo_users as $demo_user)
        <div class="col-6 mb-3">
            <form method="POST" class="loginForm" action="{{ route('login') }}">
                @csrf
                <input type="hidden" name="email" value="{{ $demo_user[0]->email }}">
                <input type="hidden" name="password" value="password">
                <button type="submit"
                    class="btn btn-rounded btn-info text-nowrap w-100">{{ $demo_user[0]->role->name }}</button>
            </form>
        </div>
    @empty
    @endforelse
</div>
