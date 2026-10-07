@extends('layouts.master')
@section('title', $title ?? __('Permission'))
@section('content')
    <div class="row">
        {!! breadcrumb('Permission', ['role/index' => 'Role', '#' => 'Permission', 'role' => 'Permissions']) !!}

        <div class="col-md-12 mb-3">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 col-md-6 text-mute pl-0">{{_trans('keyword.Assign Permission to')}}
                        <strong>{{ $user->name }}</strong></h5>
                </div>
                <div class="card-body">

                    {{-- @if (@$role->id == 1)
                        <div class="alert alert-danger">
                            <strong>{{_trans('keyword.Warning')}}!</strong> {{_trans("role.You can't change permission of this role")}}.
                        </div>
                    @endif --}}
                    <form action="{{ route('user.permissionUpdate') }}" enctype="multipart/form-data" method="post">
                        @csrf
                        <input type="hidden" name="user_id" value="{{ @$user->id }}">
                        <div class="table-responsive">
                            <table class="table test">
                                <thead>
                                <th>{{ __('module_name') }}</th>
                                <th>{{ __('Permissions') }}</th>
                                </thead>
                                <tbody>

                                @foreach ($permissions as $permission)
                                    <tr>
                                        <td class="text-nowrap fw-medium">{{ $permission->attribute }}</td>
                                        <td>
                                            <div class="d-flex">
                                                @php
                                                    $checkFromSupervisor = !$user->permissions || empty($user->permissions);
                                                    $permissionsToCheck = $checkFromSupervisor
                                                        ? ($user->role ? $user->role->permissions ?? [] : [])
                                                        : $user->permissions;
                                                @endphp

                                                @foreach ($permission->keywords as $key => $keyword)
                                                    <div class="form-check me-3 me-lg-5">
                                                        <input class="common-key" type="checkbox"
                                                               name="permissions[]" value="{{ $keyword }}"
                                                               id="{{ $keyword }}"
                                                            {{ in_array($keyword, $permissionsToCheck) ? 'checked' : '' }} />
                                                        <label class="" for="{{ $keyword }}">
                                                            {{ $key }}
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>

                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <div class="modal-footer">
                                <button class="btn btn-primary mt-3" type="submit">{{_trans('keyword.Update')}}
                                    <span class="loader"></span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).on('click', '.common-key', function() {
            var value = $(this).val();
            var value = value.split("_");
            if (value[1] == 'read') {
                if (!$(this).is(':checked')) {
                    $(this).closest('tr').find('.common-key').prop('checked', false);
                }
            } else {
                if ($(this).is(':checked')) {
                    $(this).closest('tr').find('.common-key').first().prop('checked', true);
                }
            }
        });
    </script>
@endpush
