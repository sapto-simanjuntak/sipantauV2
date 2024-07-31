@extends('layouts.default')

@section('content')
    <div class="page-wrapper">
        <div class="page-content">
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="ms-auto">
                    <a href="{{ route('user.create') }}" class="btn btn-primary"><i class="bx bx-undo mr-1"></i></a>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <form action="{{ url('role/' . $role->id . '/give-permissions') }}" method="POST"
                        class="form-horizontal" enctype="multipart/form-data">
                        @method('PUT')
                        @csrf

                        <?php
                        $actionCount = count(\App\Utils\PermissionHelper::ACTIONS);
                        ?>
                        <h4 class="text-center m-3">Hak Akses Per Menu</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered can-hover">
                                <tbody>
                                    @foreach (App\Utils\PermissionHelper::PERMISSIONS as $section => $perms)
                                        <tr class="bg-gray-lighter">
                                            <th colspan="2">{{ strtoupper(str_replace('_', ' ', $section)) }}</th>
                                            <th colspan="{{ $actionCount - 1 }}" class="text-right">Select/Unselect Section
                                            </th>
                                            <th class="text-center">
                                                <div class="checkbox form-check form-switch"
                                                    style="display: inline-block; margin: 0 5px;">
                                                    <label>
                                                        <input type="checkbox" class="toggle-section form-check-input"
                                                            data-section="{{ $section }}" />
                                                        <span class="fa fa-check"></span>
                                                    </label>
                                                </div>
                                            </th>
                                        </tr>
                                        <tr class="bg-gray-lighter">
                                            <th colspan="2"></th>
                                            @foreach (App\Utils\PermissionHelper::ACTIONS as $act)
                                                <th class="text-center">
                                                    <div class="checkbox form-check form-switch"
                                                        style="display: inline-block; margin: 0 5px;">
                                                        <label>
                                                            <input type="checkbox" class="toggle-column form-check-input"
                                                                data-column="{{ $act }}"
                                                                data-section="{{ $section }}" />
                                                            <span class="fa fa-check"></span>
                                                        </label>
                                                    </div>
                                                </th>
                                            @endforeach
                                        </tr>

                                        <tr class="bg-gray-lighter">
                                            <th>#</th>
                                            <th>Modul</th>
                                            @foreach (\App\Utils\PermissionHelper::ACTIONS as $action)
                                                <th class="text-center">{{ ucwords($action) }}</th>
                                            @endforeach
                                        </tr>

                                        @foreach ($perms as $index => $perm)
                                            <?php $permName = $perm; ?>

                                            <tr data-section="{{ $section }}">
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ ucwords(str_replace('_', ' ', $permName)) }}</td>

                                                @foreach (App\Utils\PermissionHelper::ACTIONS as $act)
                                                    <?php $permFullName = $permName . '-' . $act; ?>
                                                    <?php $hasPerm = in_array($permFullName, $rolePermissions); ?>
                                                    <td>
                                                        <div class="checkbox form-check form-switch">
                                                            <label>
                                                                <input name="permission[]" type="checkbox"
                                                                    value="{{ $permFullName }}"
                                                                    class="{{ $act }} form-check-input"
                                                                    {{ $hasPerm ? 'checked' : '' }} />
                                                                <span class="fa fa-check"></span>
                                                            </label>
                                                        </div>
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-lg btn-success w-100">Update</button>
                        </div>
                    </form>


                    {{-- <form action="{{ url('role/' . $role->id . '/give-permissions') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="nama">Permission Role <b>{{ $role->name }}</b> </label>
                            <div class="row">
                                @foreach ($permissions as $permission)
                                    <div class="col-md-2">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="form-check form-switch form-check-success">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    name="permission[]" value="{{ $permission->name }}"
                                                    {{ in_array($permission->id, $rolePermissions) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="flexSwitchCheckSuccess">
                                                    {{ $permission->name }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <button type="submit" class="btn btn-warning">Update</button>
                        </div>
                    </form> --}}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('after-js')
    <script>
        $(function() {
            $(".toggle-section").change(function() {
                var $this = $(this);
                var section = $this.attr('data-section');
                var tr = $('tr[data-section="' + section + '"]');
                if (this.checked) {
                    tr.find('input[type="checkbox"]').prop('checked', true);
                } else {
                    tr.find('input[type="checkbox"]').prop('checked', false);
                }
            });

            $(".toggle-column").change(function() {
                var $this = $(this);
                var section = $this.attr('data-section');
                var column = $this.attr('data-column');
                var tr = $('tr[data-section="' + section + '"]');
                if (this.checked) {
                    tr.find('input[type="checkbox"].' + column).prop('checked', true);
                } else {
                    tr.find('input[type="checkbox"].' + column).prop('checked', false);
                }
            });

            $(".toggle-all").change(function() {
                var body = $('body');
                if (this.checked) {
                    body.find('input[type="checkbox"]').prop('checked', true);
                } else {
                    body.find('input[type="checkbox"]').prop('checked', false);
                }
            });
        });
    </script>
@endpush
