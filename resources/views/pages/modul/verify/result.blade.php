{{-- resources/views/pages/modul/verify/result.blade.php --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification Result - RSUD Cilincing</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

        * {
            font-family: 'Inter', sans-serif;
        }

        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .checkmark-animation {
            animation: checkmark 0.8s cubic-bezier(0.65, 0, 0.45, 1);
        }

        @keyframes checkmark {
            0% {
                transform: scale(0) rotate(-45deg);
                opacity: 0;
            }

            50% {
                transform: scale(1.2) rotate(-45deg);
                opacity: 1;
            }

            100% {
                transform: scale(1) rotate(0);
                opacity: 1;
            }
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

        .shake-animation {
            animation: shake 0.5s;
        }

        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            25% {
                transform: translateX(-10px);
            }

            75% {
                transform: translateX(10px);
            }
        }

        .print-hidden {
            display: none;
        }

        @media print {
            .no-print {
                display: none;
            }

            .print-hidden {
                display: block;
            }
        }
    </style>
</head>

<body class="bg-gray-50">

    {{-- Header --}}
    <div class="gradient-bg text-white py-8 no-print">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">
                        <i class="fas fa-shield-alt mr-2"></i>
                        Signature Verification
                    </h1>
                    <p class="text-white text-opacity-75 text-sm">RSUD Cilincing - IT Department</p>
                </div>
                <a href="{{ route('verify.index') }}"
                    class="bg-white bg-opacity-20 hover:bg-opacity-30 px-6 py-2 rounded-lg transition-all">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Verify
                </a>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="container mx-auto px-4 py-12">
        <div class="max-w-4xl mx-auto">

            @php
                $statusConfig = [
                    'valid' => [
                        'bg' => 'bg-green-50',
                        'border' => 'border-green-500',
                        'icon' => 'fa-check-circle',
                        'icon_color' => 'text-green-500',
                        'title_color' => 'text-green-800',
                        'badge_bg' => 'bg-green-500',
                        'gradient' => 'from-green-400 to-emerald-600',
                    ],
                    'invalid' => [
                        'bg' => 'bg-red-50',
                        'border' => 'border-red-500',
                        'icon' => 'fa-times-circle',
                        'icon_color' => 'text-red-500',
                        'title_color' => 'text-red-800',
                        'badge_bg' => 'bg-red-500',
                        'gradient' => 'from-red-400 to-rose-600',
                    ],
                    'tampered' => [
                        'bg' => 'bg-orange-50',
                        'border' => 'border-orange-500',
                        'icon' => 'fa-exclamation-triangle',
                        'icon_color' => 'text-orange-500',
                        'title_color' => 'text-orange-800',
                        'badge_bg' => 'bg-orange-500',
                        'gradient' => 'from-orange-400 to-amber-600',
                    ],
                    'not_found' => [
                        'bg' => 'bg-gray-50',
                        'border' => 'border-gray-500',
                        'icon' => 'fa-question-circle',
                        'icon_color' => 'text-gray-500',
                        'title_color' => 'text-gray-800',
                        'badge_bg' => 'bg-gray-500',
                        'gradient' => 'from-gray-400 to-slate-600',
                    ],
                ];

                $config = $statusConfig[$result['status']] ?? $statusConfig['invalid'];
            @endphp

            {{-- Result Card --}}
            <div class="bg-white rounded-2xl shadow-2xl overflow-hidden fade-in">

                {{-- Status Header --}}
                <div class="bg-gradient-to-r {{ $config['gradient'] }} p-8 text-white text-center">
                    <div
                        class="w-24 h-24 bg-white bg-opacity-20 backdrop-blur-sm rounded-full flex items-center justify-center mx-auto mb-4 {{ $result['status'] === 'valid' ? 'checkmark-animation' : ($result['status'] === 'tampered' ? 'shake-animation' : '') }}">
                        <i class="fas {{ $config['icon'] }} text-5xl"></i>
                    </div>

                    <h2 class="text-3xl font-bold mb-2">
                        {{ $result['message'] }}
                    </h2>

                    <div class="inline-block {{ $config['badge_bg'] }} px-4 py-2 rounded-full text-sm font-semibold">
                        STATUS: {{ strtoupper($result['status']) }}
                    </div>
                </div>

                {{-- Details Section --}}
                <div class="p-8">

                    @if ($result['status'] === 'valid' && !isset($result['is_placeholder']))
                        {{-- VALID SIGNATURE - Show full details --}}
                        <div class="space-y-6">

                            <div class="bg-green-50 border-l-4 border-green-500 p-6 rounded-r-xl">
                                <div class="flex items-start">
                                    <i class="fas fa-info-circle text-green-600 text-2xl mr-4 mt-1"></i>
                                    <div>
                                        <h3 class="font-bold text-green-800 mb-2">Verification Successful</h3>
                                        <p class="text-green-700 text-sm leading-relaxed">
                                            This signature has been verified and matches our database records. The
                                            document is authentic and has not been tampered with.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            {{-- Signature Details Grid --}}
                            <div class="grid md:grid-cols-2 gap-6">

                                {{-- Signer Information --}}
                                <div class="bg-gray-50 rounded-xl p-6">
                                    <div class="flex items-center mb-4">
                                        <div
                                            class="bg-blue-500 w-10 h-10 rounded-lg flex items-center justify-center mr-3">
                                            <i class="fas fa-user text-white"></i>
                                        </div>
                                        <h4 class="font-bold text-gray-800">Signer Information</h4>
                                    </div>
                                    <div class="space-y-3">
                                        <div>
                                            <label class="text-xs font-semibold text-gray-500 uppercase">Name</label>
                                            <p class="text-gray-800 font-semibold">
                                                {{ $result['details']['signer_name'] ?? '-' }}
                                            </p>
                                        </div>
                                        <div>
                                            <label class="text-xs font-semibold text-gray-500 uppercase">Role</label>
                                            <p class="text-gray-800">
                                                <span
                                                    class="inline-block bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">
                                                    {{ $result['details']['signer_role'] ?? '-' }}
                                                </span>
                                            </p>
                                        </div>
                                        @if (isset($result['details']['user_id']))
                                            <div>
                                                <label class="text-xs font-semibold text-gray-500 uppercase">User
                                                    ID</label>
                                                <p class="text-gray-800 font-mono">
                                                    {{ $result['details']['user_id'] }}
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- Signature Metadata --}}
                                <div class="bg-gray-50 rounded-xl p-6">
                                    <div class="flex items-center mb-4">
                                        <div
                                            class="bg-purple-500 w-10 h-10 rounded-lg flex items-center justify-center mr-3">
                                            <i class="fas fa-clock text-white"></i>
                                        </div>
                                        <h4 class="font-bold text-gray-800">Signature Metadata</h4>
                                    </div>
                                    <div class="space-y-3">
                                        @if (isset($result['details']['signed_at']))
                                            <div>
                                                <label class="text-xs font-semibold text-gray-500 uppercase">Signed
                                                    At</label>
                                                <p class="text-gray-800 font-semibold">
                                                    {{ $result['details']['signed_at'] }}
                                                </p>
                                            </div>
                                        @endif
                                        @if (isset($result['details']['action']))
                                            <div>
                                                <label
                                                    class="text-xs font-semibold text-gray-500 uppercase">Action</label>
                                                <p class="text-gray-800">
                                                    {{ $result['details']['action'] }}
                                                </p>
                                            </div>
                                        @endif
                                        @if (isset($result['details']['ticket_status']))
                                            <div>
                                                <label class="text-xs font-semibold text-gray-500 uppercase">Ticket
                                                    Status</label>
                                                <p class="text-gray-800">
                                                    <span
                                                        class="inline-block bg-gray-200 text-gray-800 px-3 py-1 rounded-full text-sm font-medium">
                                                        {{ $result['details']['ticket_status'] }}
                                                    </span>
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                            </div>

                            {{-- Additional Info (if available) --}}
                            @if (isset($result['details']['validation_notes']) && $result['details']['validation_notes'])
                                <div class="bg-blue-50 border-l-4 border-blue-500 p-6 rounded-r-xl">
                                    <h4 class="font-bold text-blue-800 mb-2">
                                        <i class="fas fa-comment-alt mr-2"></i>Notes
                                    </h4>
                                    <p class="text-blue-700 text-sm">
                                        {{ $result['details']['validation_notes'] }}
                                    </p>
                                </div>
                            @endif

                            @if (isset($result['details']['closure_notes']) && $result['details']['closure_notes'])
                                <div class="bg-blue-50 border-l-4 border-blue-500 p-6 rounded-r-xl">
                                    <h4 class="font-bold text-blue-800 mb-2">
                                        <i class="fas fa-clipboard-check mr-2"></i>Closure Notes
                                    </h4>
                                    <p class="text-blue-700 text-sm">
                                        {{ $result['details']['closure_notes'] }}
                                    </p>
                                </div>
                            @endif

                            {{-- ✅ WARNING: Must Compare with Document --}}
                            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-6 rounded-r-xl">
                                <h4 class="font-bold text-yellow-800 mb-3 flex items-center">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                    Important Verification Notice
                                </h4>
                                <p class="text-sm text-yellow-700 mb-3">
                                    To ensure document authenticity, <strong>compare the information shown above with
                                        the
                                        printed document</strong>:
                                </p>
                                <ul class="text-sm text-yellow-700 space-y-2 list-disc list-inside">
                                    <li>Verify the signer name matches the document</li>
                                    <li>Verify the signed date/time matches the document</li>
                                    <li>If any information differs, the document may have been tampered with</li>
                                </ul>
                                <div class="mt-4 bg-yellow-100 rounded-lg p-3">
                                    <p class="text-xs text-yellow-800 font-semibold">
                                        ⚠️ If discrepancies are found, report immediately to IT Support: (021)
                                        1234-5678
                                    </p>
                                </div>
                            </div>

                            {{-- ✅ CHECKLIST: What to Verify --}}
                            <div class="bg-blue-50 rounded-xl p-6">
                                <h4 class="font-bold text-blue-800 mb-4 flex items-center">
                                    <i class="fas fa-tasks mr-2"></i>
                                    Document Verification Checklist
                                </h4>

                                <div class="space-y-3">
                                    <label
                                        class="flex items-start space-x-3 cursor-pointer hover:bg-blue-100 p-2 rounded-lg transition-all">
                                        <input type="checkbox" class="mt-1 w-5 h-5 text-blue-600 rounded">
                                        <span class="text-sm text-gray-700">
                                            <strong>Name matches:</strong>
                                            Confirmed "<span
                                                class="text-blue-800 font-semibold">{{ $result['details']['signer_name'] ?? 'N/A' }}</span>"
                                            is shown on the printed
                                            document
                                        </span>
                                    </label>

                                    <label
                                        class="flex items-start space-x-3 cursor-pointer hover:bg-blue-100 p-2 rounded-lg transition-all">
                                        <input type="checkbox" class="mt-1 w-5 h-5 text-blue-600 rounded">
                                        <span class="text-sm text-gray-700">
                                            <strong>Date/Time matches:</strong>
                                            Confirmed "<span
                                                class="text-blue-800 font-semibold">{{ $result['details']['signed_at'] ?? 'N/A' }}</span>"
                                            is shown on the printed
                                            document
                                        </span>
                                    </label>

                                    <label
                                        class="flex items-start space-x-3 cursor-pointer hover:bg-blue-100 p-2 rounded-lg transition-all">
                                        <input type="checkbox" class="mt-1 w-5 h-5 text-blue-600 rounded">
                                        <span class="text-sm text-gray-700">
                                            <strong>Ticket number matches:</strong>
                                            Confirmed ticket number is correct on the document
                                        </span>
                                    </label>

                                    <label
                                        class="flex items-start space-x-3 cursor-pointer hover:bg-blue-100 p-2 rounded-lg transition-all">
                                        <input type="checkbox" class="mt-1 w-5 h-5 text-blue-600 rounded">
                                        <span class="text-sm text-gray-700">
                                            <strong>QR code quality:</strong>
                                            QR code is clear and not manually replaced
                                        </span>
                                    </label>
                                </div>

                                <div class="mt-4 pt-4 border-t border-blue-200">
                                    <p class="text-xs text-blue-700 flex items-center">
                                        <i class="fas fa-shield-alt mr-2"></i>
                                        All items must be checked to confirm document authenticity
                                    </p>
                                </div>
                            </div>

                        </div>
                    @elseif($result['status'] === 'valid' && isset($result['is_placeholder']))
                        {{-- PLACEHOLDER (Pending/In Progress) --}}
                        <div class="space-y-6">

                            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-6 rounded-r-xl">
                                <div class="flex items-start">
                                    <i class="fas fa-hourglass-half text-yellow-600 text-2xl mr-4 mt-1"></i>
                                    <div>
                                        <h3 class="font-bold text-yellow-800 mb-2">Pending Action</h3>
                                        <p class="text-yellow-700 text-sm leading-relaxed">
                                            This signature is in a pending state. The document is authentic, but this
                                            specific action has not been completed yet.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-gray-50 rounded-xl p-6">
                                <div class="space-y-3">
                                    @foreach ($result['details'] as $key => $value)
                                        <div>
                                            <label
                                                class="text-xs font-semibold text-gray-500 uppercase">{{ str_replace('_', ' ', $key) }}</label>
                                            <p class="text-gray-800">{{ $value }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                        </div>
                    @else
                        {{-- INVALID / TAMPERED / NOT FOUND --}}
                        <div class="space-y-6">

                            <div class="{{ $config['bg'] }} border-l-4 {{ $config['border'] }} p-6 rounded-r-xl">
                                <div class="flex items-start">
                                    <i
                                        class="fas {{ $config['icon'] }} {{ $config['icon_color'] }} text-2xl mr-4 mt-1"></i>
                                    <div>
                                        <h3 class="font-bold {{ $config['title_color'] }} mb-2">
                                            {{ $result['message'] }}
                                        </h3>
                                        <p
                                            class="{{ $config['title_color'] }} text-opacity-80 text-sm leading-relaxed">
                                            {{ is_array($result['details']) ? $result['details'][0] ?? 'Unknown error' : $result['details'] }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            @if (isset($result['ticket']))
                                <div class="bg-gray-50 rounded-xl p-6">
                                    <h4 class="font-bold text-gray-800 mb-4">Ticket Information</h4>
                                    <div class="space-y-2 text-sm">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Ticket Number:</span>
                                            <span
                                                class="font-mono font-semibold">{{ $result['ticket']->ticket_number }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Status:</span>
                                            <span class="font-semibold">{{ $result['ticket']->ticket_status }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Created:</span>
                                            <span>{{ $result['ticket']->created_at->format('d M Y, H:i') }} WIB</span>
                                        </div>
                                    </div>
                                </div>
                            @endif

                        </div>
                    @endif

                    {{-- Ticket Reference --}}
                    @if (isset($result['ticket']))
                        <div class="mt-8 pt-6 border-t border-gray-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600">Ticket Reference</p>
                                    <p class="font-mono font-bold text-xl text-gray-800">
                                        {{ $result['ticket']->ticket_number }}
                                    </p>
                                </div>
                                <a href="{{ route('verify.history', $result['ticket']->ticket_number) }}"
                                    class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-xl font-semibold transition-all no-print">
                                    <i class="fas fa-history mr-2"></i>
                                    View History
                                </a>
                            </div>
                        </div>
                    @endif

                </div>
                {{-- END: Details Section --}}

            </div>
            {{-- END: Result Card --}}

            {{-- Action Buttons --}}
            <div class="mt-8 flex gap-4 justify-center no-print">
                <button onclick="window.print()"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-xl font-semibold transition-all shadow-lg hover:shadow-xl">
                    <i class="fas fa-print mr-2"></i>
                    Print Result
                </button>

                <a href="{{ route('verify.index') }}"
                    class="bg-gray-600 hover:bg-gray-700 text-white px-8 py-3 rounded-xl font-semibold transition-all shadow-lg hover:shadow-xl">
                    <i class="fas fa-redo mr-2"></i>
                    Verify Another
                </a>
            </div>

            {{-- Security Notice --}}
            <div class="mt-8 bg-gray-100 rounded-xl p-6 text-center no-print">
                <p class="text-sm text-gray-600">
                    <i class="fas fa-lock mr-2 text-gray-500"></i>
                    This verification was performed on
                    <strong>{{ now()->format('d M Y, H:i:s') }} WIB</strong>
                </p>
                <p class="text-xs text-gray-500 mt-2">
                    All verifications are logged for audit purposes
                </p>
            </div>

        </div>
    </div>

    {{-- Print Header (Hidden on screen, shown on print) --}}
    <div class="print-hidden">
        <div style="text-align: center; padding: 20px; border-bottom: 2px solid #333;">
            <h1 style="font-size: 24px; font-weight: bold; margin-bottom: 10px;">RSUD CILINCING</h1>
            <h2 style="font-size: 18px; margin-bottom: 5px;">Digital Signature Verification Report</h2>
            <p style="font-size: 12px; color: #666;">Generated on {{ now()->format('d M Y, H:i:s') }} WIB</p>
        </div>
    </div>

</body>

</html>
