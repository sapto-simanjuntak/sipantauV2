{{-- resources/views/verify/history.blade.php --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification History - {{ $ticket->ticket_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

        * {
            font-family: 'Inter', sans-serif;
        }

        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .fade-in {
            animation: fadeIn 0.6s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -8px;
            top: 0;
            bottom: -20px;
            width: 2px;
            background: linear-gradient(to bottom, #e5e7eb, transparent);
        }

        .timeline-item:last-child::before {
            display: none;
        }

        .hover-scale {
            transition: transform 0.2s;
        }

        .hover-scale:hover {
            transform: scale(1.02);
        }
    </style>
</head>

<body class="bg-gray-50">

    {{-- Header --}}
    <div class="gradient-bg text-white py-8">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div>
                    <h1 class="text-3xl font-bold mb-2">
                        <i class="fas fa-history mr-2"></i>
                        Verification History
                    </h1>
                    <p class="text-white text-opacity-75">
                        Complete audit trail for ticket {{ $ticket->ticket_number }}
                    </p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('verify.index') }}"
                        class="bg-white bg-opacity-20 hover:bg-opacity-30 px-6 py-2 rounded-lg transition-all">
                        <i class="fas fa-qrcode mr-2"></i>
                        New Verification
                    </a>
                    <button onclick="window.print()"
                        class="bg-white bg-opacity-20 hover:bg-opacity-30 px-6 py-2 rounded-lg transition-all">
                        <i class="fas fa-print mr-2"></i>
                        Print
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-12">
        <div class="max-w-7xl mx-auto">

            {{-- Ticket Summary Card --}}
            <div class="bg-white rounded-2xl shadow-xl p-8 mb-8 fade-in">
                <div class="flex items-start justify-between flex-wrap gap-6">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="bg-blue-100 p-3 rounded-lg">
                                <i class="fas fa-ticket-alt text-blue-600 text-2xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Ticket Number</p>
                                <p class="text-2xl font-bold text-gray-800 font-mono">
                                    {{ $ticket->ticket_number }}
                                </p>
                            </div>
                        </div>

                        <div class="grid md:grid-cols-2 gap-4 mt-6">
                            <div>
                                <label class="text-xs font-semibold text-gray-500 uppercase block mb-1">Issue
                                    Title</label>
                                <p class="text-gray-800 font-medium">{{ $ticket->issue_title }}</p>
                            </div>
                            <div>
                                <label
                                    class="text-xs font-semibold text-gray-500 uppercase block mb-1">Requester</label>
                                <p class="text-gray-800">{{ $ticket->requester_name }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-gray-500 uppercase block mb-1">Status</label>
                                <span
                                    class="inline-block px-3 py-1 rounded-full text-sm font-semibold
                                    @if ($ticket->ticket_status === 'Closed') bg-gray-200 text-gray-800
                                    @elseif($ticket->ticket_status === 'Resolved') bg-green-100 text-green-800
                                    @elseif($ticket->ticket_status === 'In Progress') bg-blue-100 text-blue-800
                                    @else bg-yellow-100 text-yellow-800 @endif">
                                    {{ $ticket->ticket_status }}
                                </span>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-gray-500 uppercase block mb-1">Created</label>
                                <p class="text-gray-800">{{ $ticket->created_at->format('d M Y, H:i') }} WIB</p>
                            </div>
                        </div>
                    </div>

                    {{-- Stats --}}
                    <div class="bg-gradient-to-br from-blue-50 to-purple-50 rounded-xl p-6 min-w-[200px]">
                        <div class="text-center">
                            <p class="text-sm text-gray-600 mb-2">Total Verifications</p>
                            <p
                                class="text-4xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600">
                                {{ $verifications->total() }}
                            </p>
                            <p class="text-xs text-gray-500 mt-2">
                                <i class="fas fa-clock mr-1"></i>
                                Last: {{ $verifications->first()?->created_at->diffForHumans() ?? 'N/A' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Statistics Dashboard --}}
            <div class="grid md:grid-cols-4 gap-6 mb-8">
                @php
                    $stats = [
                        'valid' => $verifications->where('verification_status', 'valid')->count(),
                        'invalid' => $verifications->where('verification_status', 'invalid')->count(),
                        'tampered' => $verifications->where('verification_status', 'tampered')->count(),
                        'not_found' => $verifications->where('verification_status', 'not_found')->count(),
                    ];
                @endphp

                <div class="bg-white rounded-xl shadow-lg p-6 hover-scale">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-green-100 p-3 rounded-lg">
                            <i class="fas fa-check-circle text-green-600 text-xl"></i>
                        </div>
                        <span class="text-3xl font-bold text-green-600">{{ $stats['valid'] }}</span>
                    </div>
                    <p class="text-sm font-semibold text-gray-700">Valid Verifications</p>
                    <p class="text-xs text-gray-500 mt-1">Successfully verified signatures</p>
                </div>

                <div class="bg-white rounded-xl shadow-lg p-6 hover-scale">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-red-100 p-3 rounded-lg">
                            <i class="fas fa-times-circle text-red-600 text-xl"></i>
                        </div>
                        <span class="text-3xl font-bold text-red-600">{{ $stats['invalid'] }}</span>
                    </div>
                    <p class="text-sm font-semibold text-gray-700">Invalid Attempts</p>
                    <p class="text-xs text-gray-500 mt-1">Failed verification attempts</p>
                </div>

                <div class="bg-white rounded-xl shadow-lg p-6 hover-scale">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-orange-100 p-3 rounded-lg">
                            <i class="fas fa-exclamation-triangle text-orange-600 text-xl"></i>
                        </div>
                        <span class="text-3xl font-bold text-orange-600">{{ $stats['tampered'] }}</span>
                    </div>
                    <p class="text-sm font-semibold text-gray-700">Tampered Detected</p>
                    <p class="text-xs text-gray-500 mt-1">Suspicious modification attempts</p>
                </div>

                <div class="bg-white rounded-xl shadow-lg p-6 hover-scale">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-gray-100 p-3 rounded-lg">
                            <i class="fas fa-question-circle text-gray-600 text-xl"></i>
                        </div>
                        <span class="text-3xl font-bold text-gray-600">{{ $stats['not_found'] }}</span>
                    </div>
                    <p class="text-sm font-semibold text-gray-700">Not Found</p>
                    <p class="text-xs text-gray-500 mt-1">Records not found in system</p>
                </div>
            </div>

            {{-- Role Distribution --}}
            <div class="bg-white rounded-2xl shadow-xl p-8 mb-8">
                <h3 class="text-xl font-bold text-gray-800 mb-6">
                    <i class="fas fa-chart-pie text-purple-600 mr-2"></i>
                    Verification by Role
                </h3>
                <div class="grid md:grid-cols-3 gap-6">
                    @php
                        $roleStats = [
                            'requester' => $verifications->where('verified_role', 'requester')->count(),
                            'validator' => $verifications->where('verified_role', 'validator')->count(),
                            'technician' => $verifications->where('verified_role', 'technician')->count(),
                        ];
                    @endphp

                    <div class="text-center p-6 bg-blue-50 rounded-xl">
                        <div class="bg-blue-500 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-user text-white text-2xl"></i>
                        </div>
                        <p class="text-3xl font-bold text-blue-600 mb-1">{{ $roleStats['requester'] }}</p>
                        <p class="text-sm font-semibold text-gray-700">Requester</p>
                    </div>

                    <div class="text-center p-6 bg-purple-50 rounded-xl">
                        <div class="bg-purple-500 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-user-check text-white text-2xl"></i>
                        </div>
                        <p class="text-3xl font-bold text-purple-600 mb-1">{{ $roleStats['validator'] }}</p>
                        <p class="text-sm font-semibold text-gray-700">Validator</p>
                    </div>

                    <div class="text-center p-6 bg-green-50 rounded-xl">
                        <div class="bg-green-500 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-user-cog text-white text-2xl"></i>
                        </div>
                        <p class="text-3xl font-bold text-green-600 mb-1">{{ $roleStats['technician'] }}</p>
                        <p class="text-sm font-semibold text-gray-700">Technician</p>
                    </div>
                </div>
            </div>

            {{-- Verification Timeline --}}
            <div class="bg-white rounded-2xl shadow-xl p-8">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-800">
                        <i class="fas fa-list text-blue-600 mr-2"></i>
                        Verification Timeline
                    </h3>
                    <span class="text-sm text-gray-600">
                        Showing {{ $verifications->count() }} of {{ $verifications->total() }} records
                    </span>
                </div>

                @if ($verifications->count() > 0)
                    <div class="space-y-4">
                        @foreach ($verifications as $index => $verification)
                            @php
                                $statusIcons = [
                                    'valid' => [
                                        'icon' => 'fa-check-circle',
                                        'color' => 'text-green-600',
                                        'bg' => 'bg-green-100',
                                    ],
                                    'invalid' => [
                                        'icon' => 'fa-times-circle',
                                        'color' => 'text-red-600',
                                        'bg' => 'bg-red-100',
                                    ],
                                    'tampered' => [
                                        'icon' => 'fa-exclamation-triangle',
                                        'color' => 'text-orange-600',
                                        'bg' => 'bg-orange-100',
                                    ],
                                    'not_found' => [
                                        'icon' => 'fa-question-circle',
                                        'color' => 'text-gray-600',
                                        'bg' => 'bg-gray-100',
                                    ],
                                ];
                                $statusConfig =
                                    $statusIcons[$verification->verification_status] ?? $statusIcons['invalid'];

                                $roleIcons = [
                                    'requester' => [
                                        'icon' => 'fa-user',
                                        'color' => 'text-blue-600',
                                        'label' => 'Requester',
                                    ],
                                    'validator' => [
                                        'icon' => 'fa-user-check',
                                        'color' => 'text-purple-600',
                                        'label' => 'Validator',
                                    ],
                                    'technician' => [
                                        'icon' => 'fa-user-cog',
                                        'color' => 'text-green-600',
                                        'label' => 'Technician',
                                    ],
                                ];
                                $roleConfig = $roleIcons[$verification->verified_role] ?? [
                                    'icon' => 'fa-user',
                                    'color' => 'text-gray-600',
                                    'label' => 'Unknown',
                                ];
                            @endphp

                            <div class="relative pl-8 pb-6 timeline-item fade-in"
                                style="animation-delay: {{ $index * 0.1 }}s">
                                {{-- Timeline Dot --}}
                                <div
                                    class="absolute left-0 top-1 w-4 h-4 rounded-full {{ $statusConfig['bg'] }} border-4 border-white shadow-md">
                                </div>

                                {{-- Card --}}
                                <div
                                    class="bg-gray-50 rounded-xl p-6 hover:shadow-lg transition-all cursor-pointer border-l-4
                                    @if ($verification->verification_status === 'valid') border-green-500
                                    @elseif($verification->verification_status === 'tampered') border-orange-500
                                    @elseif($verification->verification_status === 'invalid') border-red-500
                                    @else border-gray-300 @endif">

                                    <div class="flex items-start justify-between flex-wrap gap-4">
                                        <div class="flex-1">
                                            {{-- Header --}}
                                            <div class="flex items-center gap-3 mb-3">
                                                <i
                                                    class="fas {{ $statusConfig['icon'] }} {{ $statusConfig['color'] }} text-xl"></i>
                                                <span
                                                    class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide {{ $statusConfig['bg'] }} {{ $statusConfig['color'] }}">
                                                    {{ $verification->verification_status }}
                                                </span>
                                                <span
                                                    class="px-3 py-1 rounded-full text-xs font-bold bg-gray-200 text-gray-700">
                                                    <i class="fas {{ $roleConfig['icon'] }} mr-1"></i>
                                                    {{ $roleConfig['label'] }}
                                                </span>
                                            </div>

                                            {{-- Details Grid --}}
                                            <div class="grid md:grid-cols-2 gap-4 mt-4">
                                                <div>
                                                    <label
                                                        class="text-xs font-semibold text-gray-500 uppercase block mb-1">
                                                        <i class="fas fa-clock mr-1"></i>Verified At
                                                    </label>
                                                    <p class="text-gray-800 font-medium">
                                                        {{ $verification->created_at->format('d M Y, H:i:s') }} WIB
                                                    </p>
                                                    <p class="text-xs text-gray-500 mt-1">
                                                        {{ $verification->created_at->diffForHumans() }}
                                                    </p>
                                                </div>

                                                <div>
                                                    <label
                                                        class="text-xs font-semibold text-gray-500 uppercase block mb-1">
                                                        <i class="fas fa-network-wired mr-1"></i>IP Address
                                                    </label>
                                                    <p class="text-gray-800 font-mono text-sm">
                                                        {{ $verification->verified_by_ip ?? 'N/A' }}
                                                    </p>
                                                </div>

                                                @if ($verification->verified_by_user_id)
                                                    <div>
                                                        <label
                                                            class="text-xs font-semibold text-gray-500 uppercase block mb-1">
                                                            <i class="fas fa-user mr-1"></i>Verified By
                                                        </label>
                                                        <p class="text-gray-800">
                                                            {{ optional($verification->verifier)->name ?? 'User #' . $verification->verified_by_user_id }}
                                                        </p>
                                                    </div>
                                                @endif

                                                @if ($verification->failure_reason)
                                                    <div class="md:col-span-2">
                                                        <label
                                                            class="text-xs font-semibold text-gray-500 uppercase block mb-1">
                                                            <i class="fas fa-info-circle mr-1"></i>Failure Reason
                                                        </label>
                                                        <p class="text-red-700 text-sm">
                                                            {{ $verification->failure_reason }}
                                                        </p>
                                                    </div>
                                                @endif
                                            </div>

                                            {{-- User Agent (Collapsible) --}}
                                            @if ($verification->verified_by_user_agent)
                                                <details class="mt-4">
                                                    <summary
                                                        class="text-xs text-gray-600 cursor-pointer hover:text-gray-800 font-semibold">
                                                        <i class="fas fa-info-circle mr-1"></i>Technical Details
                                                    </summary>
                                                    <p
                                                        class="text-xs text-gray-600 mt-2 font-mono bg-gray-100 p-3 rounded">
                                                        {{ $verification->verified_by_user_agent }}
                                                    </p>
                                                </details>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-8">
                        {{ $verifications->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500 text-lg">No verification records found</p>
                        <p class="text-gray-400 text-sm">Be the first to verify this ticket's signatures</p>
                    </div>
                @endif
            </div>

            {{-- Security Footer --}}
            <div class="mt-8 bg-gradient-to-r from-blue-50 to-purple-50 rounded-xl p-6 text-center">
                <div class="flex items-center justify-center gap-2 text-gray-700 mb-2">
                    <i class="fas fa-shield-alt text-blue-600"></i>
                    <p class="font-semibold">Secure Audit Trail</p>
                </div>
                <p class="text-sm text-gray-600">
                    All verification attempts are cryptographically logged and cannot be modified or deleted
                </p>
                <p class="text-xs text-gray-500 mt-2">
                    Report generated on {{ now()->format('d M Y, H:i:s') }} WIB
                </p>
            </div>

        </div>
    </div>

    {{-- Footer --}}
    <footer class="bg-gray-800 text-white py-8 mt-12">
        <div class="container mx-auto px-4 text-center">
            <p class="text-gray-400">
                <i class="fas fa-hospital-alt mr-2"></i>
                RSUD Cilincing - IT Department | Digital Signature Verification System
            </p>
            <p class="text-gray-500 text-sm mt-2">
                Secured with enterprise-grade cryptography
            </p>
        </div>
    </footer>

</body>

</html>
