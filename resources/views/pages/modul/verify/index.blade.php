{{-- resources/views/verify/index.blade.php --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signature Verification - RSUD Cilincing</title>
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

        .card-hover {
            transition: all 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .scan-animation {
            animation: scan 2s ease-in-out infinite;
        }

        @keyframes scan {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        .pulse-ring {
            animation: pulse-ring 1.5s cubic-bezier(0.215, 0.61, 0.355, 1) infinite;
        }

        @keyframes pulse-ring {
            0% {
                transform: scale(0.8);
                opacity: 1;
            }

            80%,
            100% {
                transform: scale(1.2);
                opacity: 0;
            }
        }
    </style>
</head>

<body class="bg-gray-50">
    {{-- Hero Section --}}
    <div class="gradient-bg text-white py-20">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto text-center">
                {{-- Logo/Icon --}}
                <div class="mb-8 relative inline-block">
                    <div
                        class="absolute inset-0 bg-white opacity-20 rounded-full blur-xl pulse-ring transform scale-150">
                    </div>
                    <div class="relative bg-white bg-opacity-20 backdrop-blur-lg p-8 rounded-full inline-block">
                        <i class="fas fa-shield-alt text-6xl"></i>
                    </div>
                </div>

                {{-- Title --}}
                <h1 class="text-5xl md:text-6xl font-bold mb-6 leading-tight">
                    Digital Signature<br>Verification System
                </h1>

                <p class="text-xl md:text-2xl text-white text-opacity-90 mb-8 leading-relaxed">
                    Verify the authenticity of service request documents<br>
                    with our secure QR code verification system
                </p>

                {{-- Stats --}}
                <div class="flex justify-center gap-8 flex-wrap">
                    <div class="text-center">
                        <div class="text-3xl font-bold">100%</div>
                        <div class="text-sm text-white text-opacity-75">Secure</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold">Instant</div>
                        <div class="text-sm text-white text-opacity-75">Verification</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold">24/7</div>
                        <div class="text-sm text-white text-opacity-75">Available</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="container mx-auto px-4 -mt-16 pb-20">
        <div class="max-w-6xl mx-auto">

            {{-- Verification Methods --}}
            <div class="grid md:grid-cols-2 gap-8 mb-12">

                {{-- Method 1: Scan QR Code --}}
                <div class="bg-white rounded-2xl shadow-xl p-8 card-hover">
                    <div class="text-center">
                        <div
                            class="bg-gradient-to-br from-blue-500 to-purple-600 w-20 h-20 rounded-2xl flex items-center justify-center mx-auto mb-6 scan-animation">
                            <i class="fas fa-qrcode text-3xl text-white"></i>
                        </div>

                        <h3 class="text-2xl font-bold text-gray-800 mb-4">
                            Scan QR Code
                        </h3>

                        <p class="text-gray-600 mb-6 leading-relaxed">
                            Use your mobile device camera to scan the QR code on the document. You'll be redirected
                            automatically to the verification page.
                        </p>

                        <div class="bg-blue-50 border-2 border-blue-200 rounded-xl p-6">
                            <i class="fas fa-mobile-alt text-4xl text-blue-600 mb-3"></i>
                            <p class="text-sm text-blue-800 font-medium">
                                Point your camera at the QR code<br>on the signature section
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Method 2: Manual Verification --}}
                <div class="bg-white rounded-2xl shadow-xl p-8 card-hover">
                    <div class="text-center">
                        <div
                            class="bg-gradient-to-br from-green-500 to-teal-600 w-20 h-20 rounded-2xl flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-keyboard text-3xl text-white"></i>
                        </div>

                        <h3 class="text-2xl font-bold text-gray-800 mb-4">
                            Manual Verification
                        </h3>

                        <p class="text-gray-600 mb-6 leading-relaxed">
                            Enter the ticket number and verification code manually if QR scanning is not available.
                        </p>

                        <button onclick="document.getElementById('manualForm').scrollIntoView({behavior: 'smooth'})"
                            class="w-full bg-gradient-to-r from-green-500 to-teal-600 text-white font-semibold py-4 px-6 rounded-xl hover:shadow-lg transition-all duration-300 transform hover:scale-105">
                            <i class="fas fa-edit mr-2"></i>
                            Enter Details Manually
                        </button>
                    </div>
                </div>

            </div>



            {{-- Security Features --}}
            <div class="mt-12 grid md:grid-cols-3 gap-6">
                <div class="bg-white rounded-xl p-6 text-center shadow-lg">
                    <i class="fas fa-lock text-4xl text-blue-600 mb-4"></i>
                    <h4 class="font-bold text-gray-800 mb-2">Cryptographically Secured</h4>
                    <p class="text-sm text-gray-600">SHA-256 hash ensures tamper-proof signatures</p>
                </div>

                <div class="bg-white rounded-xl p-6 text-center shadow-lg">
                    <i class="fas fa-clock text-4xl text-purple-600 mb-4"></i>
                    <h4 class="font-bold text-gray-800 mb-2">Timestamp Verified</h4>
                    <p class="text-sm text-gray-600">Exact date and time of each signature</p>
                </div>

                <div class="bg-white rounded-xl p-6 text-center shadow-lg">
                    <i class="fas fa-clipboard-check text-4xl text-green-600 mb-4"></i>
                    <h4 class="font-bold text-gray-800 mb-2">Audit Trail</h4>
                    <p class="text-sm text-gray-600">Complete history of all verifications</p>
                </div>
            </div>

            {{-- ✅ TAMBAHKAN INI: Manual Verification Result --}}
            @if (session('verification_result'))
                <div id="verificationResult" class="mt-12 bg-white rounded-2xl shadow-2xl p-8 fade-in">
                    @php
                        $result = session('verification_result');
                        $statusConfig = [
                            'valid' => [
                                'bg' => 'bg-green-50',
                                'border' => 'border-green-500',
                                'icon' => 'fa-check-circle',
                                'icon_color' => 'text-green-500',
                                'gradient' => 'from-green-400 to-emerald-600',
                            ],
                            'invalid' => [
                                'bg' => 'bg-red-50',
                                'border' => 'border-red-500',
                                'icon' => 'fa-times-circle',
                                'icon_color' => 'text-red-500',
                                'gradient' => 'from-red-400 to-rose-600',
                            ],
                            'tampered' => [
                                'bg' => 'bg-orange-50',
                                'border' => 'border-orange-500',
                                'icon' => 'fa-exclamation-triangle',
                                'icon_color' => 'text-orange-500',
                                'gradient' => 'from-orange-400 to-amber-600',
                            ],
                            'not_found' => [
                                'bg' => 'bg-gray-50',
                                'border' => 'border-gray-500',
                                'icon' => 'fa-question-circle',
                                'icon_color' => 'text-gray-500',
                                'gradient' => 'from-gray-400 to-slate-600',
                            ],
                        ];
                        $config = $statusConfig[$result['status']] ?? $statusConfig['invalid'];
                    @endphp

                    <div class="text-center mb-8">
                        <h2 class="text-3xl font-bold text-gray-800 mb-2">
                            <i class="fas fa-clipboard-check text-blue-600 mr-2"></i>
                            Verification Result
                        </h2>
                        <p class="text-gray-600">Manual verification completed</p>
                    </div>

                    {{-- Status Header --}}
                    <div class="bg-gradient-to-r {{ $config['gradient'] }} p-6 rounded-xl text-white text-center mb-6">
                        <div
                            class="w-20 h-20 bg-white bg-opacity-20 backdrop-blur-sm rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas {{ $config['icon'] }} text-4xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold mb-2">{{ $result['message'] }}</h3>
                        <span class="inline-block bg-white bg-opacity-30 px-4 py-2 rounded-full text-sm font-semibold">
                            STATUS: {{ strtoupper($result['status']) }}
                        </span>
                    </div>

                    {{-- Details --}}
                    @if ($result['status'] === 'valid' && isset($result['details']))
                        <div class="bg-gray-50 rounded-xl p-6">
                            <h4 class="font-bold text-gray-800 mb-4">Verification Details</h4>
                            <div class="space-y-3">
                                @foreach ($result['details'] as $key => $value)
                                    @if (!is_array($value))
                                        <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                            <span
                                                class="text-sm font-semibold text-gray-600">{{ ucwords(str_replace('_', ' ', $key)) }}:</span>
                                            <span class="text-sm text-gray-800 font-medium">{{ $value }}</span>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="{{ $config['bg'] }} border-l-4 {{ $config['border'] }} p-6 rounded-r-xl">
                            <p class="text-gray-700">
                                {{ is_array($result['details']) ? $result['details'][0] ?? 'Unknown error' : $result['details'] }}
                            </p>
                        </div>
                    @endif

                    {{-- View Full Result Button --}}
                    <div class="mt-6 text-center">
                        <a href="{{ route('verify.index') }}"
                            class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-xl font-semibold transition-all">
                            <i class="fas fa-redo mr-2"></i>
                            Verify Another Signature
                        </a>
                    </div>
                </div>
            @endif

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

    {{-- ✅ UPDATE SCRIPT INI --}}
    @if (session('verification_result'))
        <script>
            window.addEventListener('load', function() {
                // Show alert
                alert('Verification completed! Check the result below.');

                // Scroll to result section
                const resultSection = document.getElementById('verificationResult');
                if (resultSection) {
                    resultSection.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });

                    // Add highlight animation
                    resultSection.style.animation = 'pulse 1s ease-in-out';
                }
            });
        </script>
    @endif
</body>

</html>
