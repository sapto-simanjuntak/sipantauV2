@push('after-style')
    <link rel="stylesheet" href="{{ asset('theme/assets/plugins/notifications/css/lobibox.min.css') }}" />
@endpush

@push('after-js')
    <script src="{{ asset('theme/assets/plugins/notifications/js/lobibox.min.js') }}"></script>
    <script src="{{ asset('theme/assets/plugins/notifications/js/notifications.min.js') }}"></script>
    <script src="{{ asset('theme/assets/plugins/notifications/js/notification-custom-script.js') }}"></script>
@endpush
