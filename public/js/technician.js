/**
 * ========================================
 * TECHNICIAN DASHBOARD - MAIN SCRIPT (FIXED)
 * ========================================
 */

$(function () {
    "use strict";

    // ==========================================
    // CONFIGURATION
    // ==========================================
    const CONFIG = {
        debounceTime: 500,
        animationDuration: 300,
        statsRefreshInterval: 30000,
        routes: {
            index: window.location.pathname,
            stats: "/technician/stats",
            updateStatus: "/technician/ticket/update-status",
        },
    };

    // ==========================================
    // STATE MANAGEMENT
    // ==========================================
    let state = {
        searchTimeout: null,
        currentPage: 1,
        currentStatus: "all",
        currentPriority: "all",
        currentSearch: "",
        isLoading: false,
    };

    // ==========================================
    // INITIALIZATION
    // ==========================================
    function init() {
        setupFlashMessages();
        setupUserMenu();
        setupFilters();
        setupSearch();
        setupPagination();
        setupTicketActions();
        setupNotifications();
        setupKeyboardShortcuts();
        setupOfflineDetection();
        setupPullToRefresh();
        startStatsRefresh();

        console.log(
            "%c🔧 Technician Dashboard Ready",
            "font-size: 16px; font-weight: bold; color: #f97316;",
        );
    }

    // ==========================================
    // FLASH MESSAGES
    // ==========================================
    function setupFlashMessages() {
        $(".flash-message").each(function () {
            const $msg = $(this);
            setTimeout(() => {
                $msg.addClass("animate__fadeOut");
                setTimeout(() => $msg.remove(), 300);
            }, 5000);
        });
    }

    // ==========================================
    // USER MENU
    // ==========================================
    function setupUserMenu() {
        $("#userMenuToggle").on("click", function (e) {
            e.stopPropagation();
            $("#userDropdown").toggleClass("active");
        });

        $(document).on("click", function (e) {
            if (!$(e.target).closest(".user-menu").length) {
                $("#userDropdown").removeClass("active");
            }
        });

        $("#userDropdown").on("click", function (e) {
            e.stopPropagation();
        });
    }

    // ==========================================
    // FILTERS
    // ==========================================
    function setupFilters() {
        // Status Filter
        $(".filter-tab").on("click", function () {
            if (state.isLoading) return;

            const status = $(this).data("status");

            console.log("Filter clicked:", status); // Debug log

            state.currentStatus = status;
            state.currentPage = 1;

            $(".filter-tab").removeClass("active");
            $(this).addClass("active");

            fetchTickets();
        });

        // Priority Filter
        $(".priority-chip").on("click", function () {
            if (state.isLoading) return;

            const priority = $(this).data("priority");

            console.log("Priority clicked:", priority); // Debug log

            state.currentPriority = priority;
            state.currentPage = 1;

            $(".priority-chip").removeClass("active");
            $(this).addClass("active");

            fetchTickets();
        });
    }

    // ==========================================
    // SEARCH
    // ==========================================
    function setupSearch() {
        $("#searchInput").on("input", function () {
            const keyword = $(this).val();
            $("#searchBox").toggleClass("has-value", keyword.length > 0);

            clearTimeout(state.searchTimeout);
            state.searchTimeout = setTimeout(() => {
                state.currentSearch = keyword;
                state.currentPage = 1;
                fetchTickets();
            }, CONFIG.debounceTime);
        });

        $("#searchClear").on("click", function () {
            state.currentSearch = "";
            $("#searchInput").val("");
            $("#searchBox").removeClass("has-value");
            state.currentPage = 1;
            fetchTickets();
            $("#searchInput").focus();
        });
    }

    // ==========================================
    // PAGINATION
    // ==========================================
    function setupPagination() {
        $(document).on("click", ".pagination a", function (e) {
            e.preventDefault();

            const url = $(this).attr("href");
            if (!url || url === "#") return;

            const page = new URL(url, window.location.origin).searchParams.get(
                "page",
            );
            if (!page) return;

            state.currentPage = parseInt(page);

            // Smooth scroll
            $("html, body").animate(
                {
                    scrollTop: $(".tickets-container").offset().top - 80,
                },
                300,
            );

            fetchTickets();
        });
    }

    // ==========================================
    // TICKET ACTIONS
    // ==========================================
    function setupTicketActions() {
        // Start Work
        $(document).on("click", ".start-work", function () {
            const ticketNumber = $(this).data("ticket");

            Swal.fire({
                title: "Mulai Pengerjaan",
                html: `<p>Mulai mengerjakan tiket <strong style="color: var(--accent-primary);">${ticketNumber}</strong>?</p>`,
                icon: "info",
                showCancelButton: true,
                confirmButtonColor: "var(--accent-primary)",
                cancelButtonColor: "var(--text-muted)",
                confirmButtonText: '<i class="bx bx-play-circle"></i> Mulai!',
                cancelButtonText: "Batal",
            }).then((result) => {
                if (result.isConfirmed) {
                    updateTicketStatus(ticketNumber, "In Progress");
                }
            });
        });

        // Update Status (Complete/Resume)
        $(document).on("click", ".update-status", function () {
            const ticketNumber = $(this).data("ticket");
            const currentTicketStatus = $(this).data("current-status");

            let nextStatus = "";
            let actionText = "";

            if (currentTicketStatus === "In Progress") {
                nextStatus = "Resolved";
                actionText = "Tandai Selesai";
            } else if (currentTicketStatus === "Pending") {
                nextStatus = "In Progress";
                actionText = "Lanjutkan";
            }

            Swal.fire({
                title: actionText,
                html: `<p>Update status tiket <strong style="color: var(--accent-primary);">${ticketNumber}</strong></p>
                       <p>dari <strong>${currentTicketStatus}</strong> ke <strong>${nextStatus}</strong>?</p>`,
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: "var(--accent-primary)",
                cancelButtonColor: "var(--text-muted)",
                confirmButtonText: '<i class="bx bx-check"></i> Ya, Update!',
                cancelButtonText: "Batal",
            }).then((result) => {
                if (result.isConfirmed) {
                    updateTicketStatus(ticketNumber, nextStatus);
                }
            });
        });
    }

    // ==========================================
    // UPDATE TICKET STATUS
    // ==========================================
    function updateTicketStatus(ticketNumber, newStatus) {
        $.ajax({
            url: `${CONFIG.routes.updateStatus}/${ticketNumber}`,
            type: "POST",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            data: {
                status: newStatus,
            },
            success: function (response) {
                Swal.fire({
                    icon: "success",
                    title: "Berhasil!",
                    text:
                        response.message ||
                        `Status tiket ${ticketNumber} berhasil diupdate`,
                    iconColor: "var(--accent-success)",
                    confirmButtonColor: "var(--accent-primary)",
                    timer: 2000,
                    showConfirmButton: false,
                });

                fetchTickets();
                refreshStats();
            },
            error: function (xhr) {
                Swal.fire({
                    icon: "error",
                    title: "Gagal!",
                    text:
                        xhr.responseJSON?.message ||
                        "Gagal mengupdate status tiket",
                    iconColor: "var(--accent-danger)",
                    confirmButtonColor: "var(--accent-primary)",
                });
            },
        });
    }

    // ==========================================
    // FETCH TICKETS - FIXED VERSION
    // ==========================================
    function fetchTickets() {
        if (state.isLoading) {
            console.log("Already loading, skipping...");
            return;
        }

        state.isLoading = true;
        showLoadingState();

        const params = {
            status: state.currentStatus,
            priority: state.currentPriority,
            search: state.currentSearch,
            page: state.currentPage,
        };

        console.log("Fetching tickets with params:", params); // Debug log

        $.ajax({
            url: CONFIG.routes.index,
            method: "GET",
            data: params,
            dataType: "html", // ✅ FIXED: Expect HTML, not JSON
            headers: {
                "X-Requested-With": "XMLHttpRequest",
                Accept: "text/html", // ✅ FIXED: Accept HTML
            },
            timeout: 15000,
            success: function (html) {
                console.log("Tickets loaded successfully"); // Debug log

                state.isLoading = false;
                $("#ticketsList").fadeOut(200, function () {
                    $(this).html(html).fadeIn(CONFIG.animationDuration);
                    updateTicketCount();
                });

                // Refresh stats after loading tickets
                refreshStats();
            },
            error: function (xhr, status, error) {
                state.isLoading = false;

                console.error("Failed to fetch tickets:", {
                    status: xhr.status,
                    statusText: xhr.statusText,
                    error: error,
                    responseText: xhr.responseText,
                });

                showErrorState(xhr, status, error);
            },
        });
    }

    // Make fetchTickets globally available
    window.fetchTickets = fetchTickets;

    // ==========================================
    // REFRESH STATS
    // ==========================================
    function refreshStats() {
        $.get(CONFIG.routes.stats, function (data) {
            console.log("Stats refreshed:", data); // Debug log

            animateNumber("#statAssigned", data.assigned || 0);
            animateNumber("#statInProgress", data.in_progress || 0);
            animateNumber("#statCritical", data.critical || 0);
            animateNumber("#statOverdue", data.overdue || 0);
            animateNumber("#statPending", data.pending || 0);
            animateNumber("#statResolved", data.resolved || 0);

            // Update notification badge
            const urgentCount = (data.overdue || 0) + (data.critical || 0);
            const $badge = $("#notificationBtn .badge");

            if (urgentCount > 0) {
                $badge.text(urgentCount).show();
            } else {
                $badge.hide();
            }
        }).fail(function (xhr) {
            console.warn("Failed to refresh stats:", xhr);
        });
    }

    function startStatsRefresh() {
        setInterval(refreshStats, CONFIG.statsRefreshInterval);
    }

    function animateNumber(selector, targetNumber) {
        const $element = $(selector);
        const currentNumber = parseInt($element.text()) || 0;

        if (currentNumber === targetNumber) return;

        const duration = 500;
        const steps = 20;
        const increment = (targetNumber - currentNumber) / steps;
        let current = currentNumber;
        let step = 0;

        const timer = setInterval(() => {
            step++;
            current += increment;

            if (step >= steps) {
                $element.text(targetNumber);
                clearInterval(timer);
            } else {
                $element.text(Math.round(current));
            }
        }, duration / steps);
    }

    // ==========================================
    // NOTIFICATIONS
    // ==========================================
    function setupNotifications() {
        $("#notificationBtn").on("click", function () {
            const overdue = parseInt($("#statOverdue").text()) || 0;
            const critical = parseInt($("#statCritical").text()) || 0;

            let message = '<div style="text-align: left;">';

            if (overdue > 0) {
                message += `<p style="margin-bottom: 8px;"><i class="bx bx-time-five" style="color: var(--accent-warning);"></i> <strong>${overdue}</strong> tiket overdue</p>`;
            }

            if (critical > 0) {
                message += `<p><i class="bx bx-error-circle" style="color: var(--accent-danger);"></i> <strong>${critical}</strong> tiket critical</p>`;
            }

            if (overdue === 0 && critical === 0) {
                message =
                    '<p style="text-align: center;">Tidak ada tiket urgent saat ini ✅</p>';
            }

            message += "</div>";

            Swal.fire({
                title: "Urgent Tickets",
                html: message,
                icon: "info",
                confirmButtonColor: "var(--accent-primary)",
                confirmButtonText: "OK",
            });
        });
    }

    // ==========================================
    // HELPER FUNCTIONS
    // ==========================================
    function updateTicketCount() {
        const count = $(".ticket-card").length;
        $("#ticketCount").text(count + " tiket");
    }

    function showLoadingState() {
        $("#ticketsList").html(`
            <div class="loading-state" style="text-align: center; padding: 60px 20px;">
                <div class="spinner-container" style="position: relative; width: 60px; height: 60px; margin: 0 auto 20px;">
                    <div style="position: absolute; width: 60px; height: 60px; border: 4px solid var(--border-light); border-top-color: var(--accent-primary); border-radius: 50%; animation: spin 1s linear infinite;"></div>
                    <div style="position: absolute; width: 40px; height: 40px; top: 10px; left: 10px; border: 4px solid var(--border-light); border-top-color: var(--accent-secondary); border-radius: 50%; animation: spin 1.5s linear infinite reverse;"></div>
                </div>
                <p style="color: var(--text-primary); font-size: 14px; font-weight: 600;">Memuat tiket...</p>
                <p style="color: var(--text-muted); font-size: 12px; margin-top: 8px;">Mohon tunggu sebentar</p>
            </div>
        `);
    }

    function showErrorState(xhr, status, error) {
        let errorMessage = "Terjadi kesalahan saat memuat data.";
        let errorDetails = "";

        if (status === "timeout") {
            errorMessage = "Koneksi timeout. Silakan coba lagi.";
            errorDetails = "Request membutuhkan waktu terlalu lama";
        } else if (xhr.status === 0) {
            errorMessage = "Tidak ada koneksi internet.";
            errorDetails = "Periksa koneksi jaringan Anda";
        } else if (xhr.status === 403) {
            errorMessage = "Akses ditolak.";
            errorDetails = "Silakan login kembali";
        } else if (xhr.status === 404) {
            errorMessage = "Halaman tidak ditemukan.";
            errorDetails = "Endpoint tidak tersedia";
        } else if (xhr.status === 500) {
            errorMessage = "Server error.";
            errorDetails = "Silakan hubungi administrator";

            // Try to get error details from response
            try {
                const response = JSON.parse(xhr.responseText);
                if (response.message) {
                    errorDetails = response.message;
                }
            } catch (e) {
                // Response is not JSON, keep default message
            }
        }

        $("#ticketsList").html(`
            <div class="error-state" style="text-align: center; padding: 60px 20px;">
                <div style="width: 80px; height: 80px; margin: 0 auto 20px; border-radius: 50%; background: rgba(239, 68, 68, 0.1); display: flex; align-items: center; justify-content: center;">
                    <i class='bx bx-error-circle' style="font-size: 40px; color: var(--accent-danger);"></i>
                </div>
                <p style="color: var(--text-primary); font-size: 16px; font-weight: 600; margin-bottom: 8px;">${errorMessage}</p>
                <p style="color: var(--text-muted); font-size: 14px; margin-bottom: 20px;">${errorDetails}</p>
                ${xhr.status ? `<p style="color: var(--text-muted); font-size: 12px; margin-bottom: 20px;">Error Code: ${xhr.status}</p>` : ""}
                <button onclick="fetchTickets()" class="empty-action">
                    <i class='bx bx-refresh'></i> Coba Lagi
                </button>
            </div>
        `);
    }

    // ==========================================
    // GLOBAL FUNCTIONS
    // ==========================================
    window.resetFilters = function () {
        state.currentSearch = "";
        state.currentStatus = "all";
        state.currentPriority = "all";
        state.currentPage = 1;

        $("#searchInput").val("");
        $("#searchBox").removeClass("has-value");
        $(".filter-tab").removeClass("active");
        $('.filter-tab[data-status="all"]').addClass("active");
        $(".priority-chip").removeClass("active");
        $('.priority-chip[data-priority="all"]').addClass("active");

        $("html, body").animate(
            {
                scrollTop: $(".tickets-container").offset().top - 80,
            },
            300,
        );

        fetchTickets();
    };

    window.filterByStatus = function (status) {
        state.currentStatus = status;
        state.currentPage = 1;

        $(".filter-tab").removeClass("active");
        $(`.filter-tab[data-status="${status}"]`).addClass("active");

        fetchTickets();
    };

    window.filterByPriority = function (priority) {
        state.currentPriority = priority;
        state.currentPage = 1;

        $(".priority-chip").removeClass("active");
        $(`.priority-chip[data-priority="${priority}"]`).addClass("active");

        fetchTickets();
    };

    // ==========================================
    // OFFLINE DETECTION
    // ==========================================
    function setupOfflineDetection() {
        window.addEventListener("online", function () {
            Swal.fire({
                icon: "success",
                title: "Koneksi Kembali",
                text: "Koneksi internet tersambung kembali",
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
            });
            fetchTickets();
            refreshStats();
        });

        window.addEventListener("offline", function () {
            Swal.fire({
                icon: "error",
                title: "Tidak Ada Koneksi",
                text: "Koneksi internet terputus",
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
            });
        });
    }

    // ==========================================
    // KEYBOARD SHORTCUTS
    // ==========================================
    function setupKeyboardShortcuts() {
        $(document).on("keydown", function (e) {
            // Ctrl/Cmd + K: Focus search
            if ((e.ctrlKey || e.metaKey) && e.key === "k") {
                e.preventDefault();
                $("#searchInput").focus();
            }

            // Escape: Clear search / Close dropdown
            if (e.key === "Escape") {
                if (state.currentSearch) {
                    state.currentSearch = "";
                    $("#searchInput").val("").blur();
                    $("#searchBox").removeClass("has-value");
                    state.currentPage = 1;
                    fetchTickets();
                }
                $("#userDropdown").removeClass("active");
            }
        });
    }

    // ==========================================
    // PULL TO REFRESH (MOBILE)
    // ==========================================
    function setupPullToRefresh() {
        let startY = 0;
        let pulling = false;

        $(window).on("touchstart", function (e) {
            if (window.scrollY === 0) {
                startY = e.touches[0].pageY;
                pulling = true;
            }
        });

        $(window).on("touchmove", function (e) {
            if (!pulling) return;

            const currentY = e.touches[0].pageY;
            const distance = currentY - startY;

            if (distance > 80 && window.scrollY === 0) {
                pulling = false;
                location.reload();
            }
        });

        $(window).on("touchend", function () {
            pulling = false;
        });
    }

    // ==========================================
    // START APPLICATION
    // ==========================================
    init();
});

// ==========================================
// SPIN ANIMATION FOR LOADING
// ==========================================
const style = document.createElement("style");
style.textContent = `
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
`;
document.head.appendChild(style);
