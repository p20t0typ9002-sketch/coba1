
@auth
    @php
        $user = auth()->user();

        $jumlahBelumDibaca = $user->unreadNotifications()->count();

        $daftarNotifikasi = $user->notifications()
            ->latest()
            ->limit(5)
            ->get();
    @endphp

    <div class="notification-wrapper">
        <details class="notification-dropdown">

            <summary class="notification-trigger" aria-label="Buka notifikasi">
                <svg viewBox="0 0 24 24" width="21" height="21"
                    fill="none" stroke="currentColor" stroke-width="1.8"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/>
                    <path d="M10 21h4"/>
                </svg>

                @if ($jumlahBelumDibaca > 0)
                    <span class="notification-badge">
                        {{ $jumlahBelumDibaca > 99 ? '99+' : $jumlahBelumDibaca }}
                    </span>
                @endif
            </summary>

            <div class="notification-panel">

                <div class="notification-header">
                    <div>
                        <h3>Notifikasi</h3>
                        <p>
                            {{ $jumlahBelumDibaca }} belum dibaca
                        </p>
                    </div>

                    @if ($jumlahBelumDibaca > 0)
                        <form method="POST" action="{{ route('notifications.read-all') }}">
                            @csrf
                            <button type="submit" class="notification-read-all">
                                Baca semua
                            </button>
                        </form>
                    @endif
                </div>

                <div class="notification-list">
                    @forelse ($daftarNotifikasi as $notification)
                        <a
                            href="{{ route('notifications.read', $notification->id) }}"
                            class="notification-item {{ $notification->read_at ? 'is-read' : 'is-unread' }}"
                        >
                            <span class="notification-indicator"></span>

                            <span class="notification-content">
                                <strong>
                                    {{ $notification->data['judul'] ?? 'Notifikasi' }}
                                </strong>

                                <span class="notification-message">
                                    {{ $notification->data['pesan'] ?? '' }}
                                </span>

                                <span class="notification-time">
                                    {{ $notification->created_at->diffForHumans() }}
                                </span>
                            </span>
                        </a>
                    @empty
                        <div class="notification-empty">
                            <span class="notification-empty-icon">
                                <svg viewBox="0 0 24 24" width="28" height="28"
                                    fill="none" stroke="currentColor"
                                    stroke-width="1.5">
                                    <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/>
                                    <path d="M10 21h4"/>
                                </svg>
                            </span>

                            <strong>Belum ada notifikasi</strong>
                            <span>Notifikasi transaksi akan muncul di sini.</span>
                        </div>
                    @endforelse
                </div>

            </div>
        </details>
    </div>

    <style>
        .notification-wrapper {
            position: relative;
            display: inline-flex;
            align-items: center;
            flex-shrink: 0;
            z-index: 100;
        }

        .notification-dropdown {
            position: relative;
        }

        .notification-dropdown summary {
            list-style: none;
        }

        .notification-dropdown summary::-webkit-details-marker {
            display: none;
        }

        .notification-trigger {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 42px;
            height: 42px;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            background: #ffffff;
            color: #475569;
            cursor: pointer;
            transition: background .2s, color .2s, border-color .2s;
        }

        .notification-trigger:hover {
            background: #f1f5f9;
            color: #2563eb;
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 19px;
            height: 19px;
            padding: 0 5px;
            border: 2px solid #ffffff;
            border-radius: 20px;
            background: #dc2626;
            color: #ffffff;
            font-size: 10px;
            font-weight: 700;
            line-height: 1;
        }

        .notification-panel {
            position: absolute;
            top: calc(100% + 12px);
            right: 0;
            width: 360px;
            max-width: min(360px, calc(100vw - 24px));
            overflow: hidden;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            background: #ffffff;
            box-shadow: 0 12px 35px rgba(15, 23, 42, .16);
            color: #1e293b;
            z-index: 9999;
        }

        .notification-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 16px;
            border-bottom: 1px solid #e5e7eb;
        }

        .notification-header h3 {
            margin: 0;
            font-size: 15px;
            font-weight: 700;
            color: #0f172a;
        }

        .notification-header p {
            margin: 4px 0 0;
            font-size: 12px;
            color: #64748b;
        }

        .notification-read-all {
            padding: 6px 9px;
            border: 0;
            border-radius: 7px;
            background: #eff6ff;
            color: #2563eb;
            font-size: 11px;
            font-weight: 600;
            cursor: pointer;
        }

        .notification-read-all:hover {
            background: #dbeafe;
        }

        .notification-list {
            max-height: 360px;
            overflow-y: auto;
        }

        .notification-item {
            display: flex;
            align-items: flex-start;
            gap: 11px;
            padding: 14px 16px;
            border-bottom: 1px solid #f1f5f9;
            color: inherit;
            text-decoration: none;
            transition: background .15s;
        }

        .notification-item:hover {
            background: #f8fafc;
        }

        .notification-item.is-unread {
            background: #eff6ff;
        }

        .notification-item.is-unread:hover {
            background: #dbeafe;
        }

        .notification-indicator {
            width: 8px;
            height: 8px;
            margin-top: 6px;
            flex-shrink: 0;
            border-radius: 50%;
            background: #cbd5e1;
        }

        .notification-item.is-unread .notification-indicator {
            background: #2563eb;
        }

        .notification-content {
            display: flex;
            min-width: 0;
            flex-direction: column;
            gap: 5px;
        }

        .notification-content strong {
            color: #1e293b;
            font-size: 13px;
            font-weight: 700;
            line-height: 1.4;
        }

        .notification-message {
            color: #64748b;
            font-size: 12px;
            line-height: 1.5;
            overflow-wrap: anywhere;
        }

        .notification-time {
            margin-top: 2px;
            color: #94a3b8;
            font-size: 11px;
        }

        .notification-empty {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            padding: 32px 18px;
            text-align: center;
        }

        .notification-empty-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 52px;
            height: 52px;
            margin-bottom: 4px;
            border-radius: 50%;
            background: #f1f5f9;
            color: #94a3b8;
        }

        .notification-empty strong {
            color: #334155;
            font-size: 13px;
        }

        .notification-empty span:last-child {
            color: #94a3b8;
            font-size: 12px;
        }

        .dark .notification-trigger {
            border-color: #374151;
            background: #1f2937;
            color: #e5e7eb;
        }

        .dark .notification-trigger:hover {
            background: #374151;
        }

        .dark .notification-badge {
            border-color: #1f2937;
        }

        .dark .notification-panel {
            border-color: #374151;
            background: #111827;
            color: #f9fafb;
        }

        .dark .notification-header {
            border-color: #374151;
        }

        .dark .notification-header h3,
        .dark .notification-content strong {
            color: #f9fafb;
        }

        .dark .notification-header p,
        .dark .notification-message {
            color: #9ca3af;
        }

        .dark .notification-item {
            border-color: #374151;
        }

        .dark .notification-item:hover {
            background: #1f2937;
        }

        .dark .notification-item.is-unread {
            background: #172554;
        }

        .dark .notification-item.is-unread:hover {
            background: #1e3a8a;
        }

        .dark .notification-empty-icon {
            background: #1f2937;
            color: #9ca3af;
        }

        .dark .notification-empty strong {
            color: #e5e7eb;
        }

        @media (max-width: 480px) {
            .notification-panel {
                position: fixed;
                top: 76px;
                right: 12px;
                left: 12px;
                width: auto;
                max-width: none;
            }
        }
    </style>
@endauth
