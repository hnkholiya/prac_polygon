<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Nexify — Web3 Payments</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Mono:wght@300;400;500&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,300&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ethers/6.11.1/ethers.umd.min.js"></script>
    <script src="https://widgets.coingecko.com/gecko-coin-price-marquee-widget.js"></script>
    <script src="https://widgets.coingecko.com/gecko-coin-price-chart-widget.js"></script>

    <style>
        :root {
            --ease-out-expo: cubic-bezier(0.19, 1, 0.22, 1);
        }

        [data-theme="dark"] {
            --bg-primary: #0a0a0f;
            --bg-card: #111118;
            --bg-card-hover: #16161f;
            --bg-glass: rgba(255,255,255,0.03);
            --bg-glass-hover: rgba(255,255,255,0.06);
            --border: rgba(255,255,255,0.08);
            --border-accent: rgba(255,140,60,0.4);
            --text-primary: #f0f0f8;
            --text-secondary: #8888a8;
            --text-muted: #4a4a6a;
            --accent: #ff8c3c;
            --accent-glow: rgba(255,140,60,0.25);
            --accent-2: #a78bfa;
            --accent-2-glow: rgba(167,139,250,0.15);
            --success: #34d399;
            --error: #f87171;
            --grid-line: rgba(255,255,255,0.03);
            --shadow-card: 0 0 0 1px rgba(255,255,255,0.06), 0 20px 60px rgba(0,0,0,0.5);
            --shadow-btn: 0 0 30px rgba(255,140,60,0.35);
            --header-gradient: linear-gradient(135deg, #ff8c3c, #f97316, #fb923c);
        }

        [data-theme="light"] {
            --bg-primary: #f5f4f0;
            --bg-card: #ffffff;
            --bg-card-hover: #fafaf8;
            --bg-glass: rgba(0,0,0,0.02);
            --bg-glass-hover: rgba(0,0,0,0.04);
            --border: rgba(0,0,0,0.08);
            --border-accent: rgba(234,88,12,0.35);
            --text-primary: #18181b;
            --text-secondary: #71717a;
            --text-muted: #a1a1aa;
            --accent: #ea580c;
            --accent-glow: rgba(234,88,12,0.15);
            --accent-2: #7c3aed;
            --accent-2-glow: rgba(124,58,237,0.1);
            --success: #059669;
            --error: #dc2626;
            --grid-line: rgba(0,0,0,0.04);
            --shadow-card: 0 0 0 1px rgba(0,0,0,0.07), 0 10px 40px rgba(0,0,0,0.08);
            --shadow-btn: 0 8px 25px rgba(234,88,12,0.3);
            --header-gradient: linear-gradient(135deg, #ea580c, #f97316, #fb923c);
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            min-height: 100vh;
            transition: background 0.4s, color 0.4s;
            overflow-x: hidden;
        }

        /* Grid background */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(var(--grid-line) 1px, transparent 1px),
                linear-gradient(90deg, var(--grid-line) 1px, transparent 1px);
            background-size: 40px 40px;
            pointer-events: none;
            z-index: 0;
        }

        .page-wrap {
            position: relative;
            z-index: 1;
            max-width: 900px;
            margin: 0 auto;
            padding: 24px 20px 60px;
        }

        /* ── TOP BAR ────────────────────────── */
        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 32px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border);
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: 22px;
            color: var(--text-primary);
            text-decoration: none;
            letter-spacing: -0.5px;
        }

        .logo-icon {
            width: 32px; height: 32px;
            background: var(--header-gradient);
            border-radius: 8px;
            display: grid;
            place-items: center;
            font-size: 16px;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .network-badge {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: 100px;
            border: 1px solid var(--border);
            background: var(--bg-glass);
            font-family: 'DM Mono', monospace;
            font-size: 11px;
            color: var(--text-secondary);
            font-weight: 500;
        }

        .network-dot {
            width: 6px; height: 6px;
            border-radius: 50%;
            background: var(--success);
            box-shadow: 0 0 6px var(--success);
            animation: pulse-dot 2s ease-in-out infinite;
        }

        @keyframes pulse-dot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.6; transform: scale(0.8); }
        }

        /* ── THEME TOGGLE ───────────────────── */
        .theme-toggle {
            width: 40px; height: 40px;
            border-radius: 10px;
            border: 1px solid var(--border);
            background: var(--bg-glass);
            color: var(--text-secondary);
            cursor: pointer;
            display: grid;
            place-items: center;
            font-size: 17px;
            transition: all 0.2s;
        }

        .theme-toggle:hover {
            background: var(--bg-glass-hover);
            border-color: var(--border-accent);
            color: var(--accent);
        }

        /* ── TICKER ─────────────────────────── */
        .ticker-wrap {
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid var(--border);
            margin-bottom: 28px;
        }

        /* ── PAYMENT CARD ───────────────────── */
        .pay-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 20px;
            box-shadow: var(--shadow-card);
            padding: 40px;
            margin-bottom: 24px;
            position: relative;
            overflow: hidden;
            transition: background 0.4s, box-shadow 0.3s;
        }

        .pay-card::before {
            content: '';
            position: absolute;
            top: -60px; right: -60px;
            width: 200px; height: 200px;
            background: radial-gradient(circle, var(--accent-glow) 0%, transparent 70%);
            pointer-events: none;
        }

        .pay-card-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 32px;
        }

        .pay-title {
            font-family: 'Syne', sans-serif;
            font-size: 26px;
            font-weight: 800;
            letter-spacing: -0.5px;
            line-height: 1.1;
            color: var(--text-primary);
        }

        .pay-title span {
            background: var(--header-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .pay-subtitle {
            font-size: 13px;
            color: var(--text-secondary);
            margin-top: 4px;
            font-weight: 300;
        }

        .amount-display {
            text-align: right;
        }

        .amount-value {
            font-family: 'Syne', sans-serif;
            font-size: 28px;
            font-weight: 700;
            color: var(--text-primary);
        }

        .amount-label {
            font-family: 'DM Mono', monospace;
            font-size: 11px;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 2px;
        }

        /* ── INFO PILLS ─────────────────────── */
        .info-row {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 28px;
        }

        .info-pill {
            display: flex;
            align-items: center;
            gap: 7px;
            padding: 8px 14px;
            background: var(--bg-glass);
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 12px;
            color: var(--text-secondary);
        }

        .info-pill-icon {
            font-size: 14px;
        }

        .info-pill strong {
            color: var(--text-primary);
            font-weight: 500;
        }

        /* ── PAY BUTTON ─────────────────────── */
        .pay-btn {
            width: 100%;
            padding: 16px 28px;
            background: var(--header-gradient);
            color: #fff;
            font-family: 'Syne', sans-serif;
            font-size: 16px;
            font-weight: 700;
            letter-spacing: 0.3px;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: var(--shadow-btn);
            transition: transform 0.2s var(--ease-out-expo), box-shadow 0.2s, opacity 0.2s;
            position: relative;
            overflow: hidden;
        }

        .pay-btn::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(to bottom, rgba(255,255,255,0.1) 0%, transparent 60%);
            border-radius: inherit;
        }

        .pay-btn:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 14px 40px var(--accent-glow);
        }

        .pay-btn:active:not(:disabled) {
            transform: translateY(0);
        }

        .pay-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .metamask-icon {
            font-size: 20px;
        }

        /* ── STATUS MESSAGE ─────────────────── */
        .status-wrap {
            margin-top: 16px;
            min-height: 36px;
            display: flex;
            align-items: center;
        }

        .status-msg {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            width: 100%;
            opacity: 0;
            transform: translateY(6px);
            transition: all 0.3s var(--ease-out-expo);
        }

        .status-msg.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .status-msg.success {
            background: rgba(52, 211, 153, 0.1);
            border: 1px solid rgba(52, 211, 153, 0.2);
            color: var(--success);
        }

        .status-msg.error {
            background: rgba(248, 113, 113, 0.1);
            border: 1px solid rgba(248, 113, 113, 0.2);
            color: var(--error);
        }

        .status-msg.info {
            background: var(--bg-glass);
            border: 1px solid var(--border);
            color: var(--text-secondary);
        }

        /* ── SPINNER ────────────────────────── */
        .spinner {
            width: 16px; height: 16px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
            flex-shrink: 0;
        }

        @keyframes spin { to { transform: rotate(360deg); } }

        /* ── CHART CARD ─────────────────────── */
        .chart-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 20px;
            box-shadow: var(--shadow-card);
            padding: 24px;
            margin-bottom: 24px;
            overflow: hidden;
            transition: background 0.4s;
        }

        .chart-card-title {
            font-family: 'Syne', sans-serif;
            font-size: 14px;
            font-weight: 700;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .chart-card-title::before {
            content: '';
            width: 3px; height: 14px;
            background: var(--header-gradient);
            border-radius: 2px;
        }

        /* ── HISTORY TABLE ──────────────────── */
        .history-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 20px;
            box-shadow: var(--shadow-card);
            overflow: hidden;
            transition: background 0.4s;
        }

        .history-header {
            padding: 24px 28px 20px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .history-title {
            font-family: 'Syne', sans-serif;
            font-size: 18px;
            font-weight: 700;
            letter-spacing: -0.3px;
        }

        .history-count {
            font-family: 'DM Mono', monospace;
            font-size: 11px;
            color: var(--text-muted);
            background: var(--bg-glass);
            border: 1px solid var(--border);
            padding: 3px 10px;
            border-radius: 100px;
        }

        .table-wrap { overflow-x: auto; }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        thead th {
            padding: 12px 20px;
            text-align: left;
            font-family: 'DM Mono', monospace;
            font-size: 10px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: var(--text-muted);
            background: var(--bg-glass);
            border-bottom: 1px solid var(--border);
            white-space: nowrap;
        }

        tbody tr {
            border-bottom: 1px solid var(--border);
            transition: background 0.15s;
        }

        tbody tr:last-child { border-bottom: none; }

        tbody tr:hover { background: var(--bg-glass); }

        tbody td {
            padding: 14px 20px;
            color: var(--text-secondary);
            vertical-align: middle;
        }

        .td-date { color: var(--text-primary); font-weight: 500; font-size: 13px; }
        .td-date small { display: block; font-size: 11px; color: var(--text-muted); font-weight: 300; }

        .td-wallet {
            font-family: 'DM Mono', monospace;
            font-size: 12px;
            color: var(--text-secondary);
        }

        .td-amount {
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 14px;
            color: var(--text-primary);
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 10px;
            font-weight: 700;
            font-family: 'DM Mono', monospace;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .status-badge.confirmed {
            background: rgba(52, 211, 153, 0.1);
            color: var(--success);
            border: 1px solid rgba(52, 211, 153, 0.2);
        }

        .status-badge.pending {
            background: rgba(251, 191, 36, 0.1);
            color: #fbbf24;
            border: 1px solid rgba(251, 191, 36, 0.2);
        }

        .tx-link {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            color: var(--accent);
            text-decoration: none;
            font-family: 'DM Mono', monospace;
            font-size: 11px;
            font-weight: 500;
            padding: 5px 10px;
            border-radius: 6px;
            border: 1px solid var(--border-accent);
            background: var(--accent-glow);
            transition: all 0.2s;
        }

        .tx-link:hover {
            background: rgba(255, 140, 60, 0.2);
            border-color: var(--accent);
        }

        .empty-state {
            padding: 48px 20px;
            text-align: center;
        }

        .empty-icon {
            font-size: 40px;
            margin-bottom: 12px;
            opacity: 0.4;
        }

        .empty-text {
            font-size: 14px;
            color: var(--text-muted);
        }

        /* ── FOOTER ─────────────────────────── */
        footer {
            text-align: center;
            margin-top: 40px;
            font-size: 12px;
            color: var(--text-muted);
            font-weight: 300;
        }

        footer strong {
            color: var(--text-secondary);
            font-weight: 500;
        }

        /* ── ANIMATIONS ─────────────────────── */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .pay-card, .chart-card, .history-card {
            animation: fadeUp 0.5s var(--ease-out-expo) both;
        }

        .chart-card { animation-delay: 0.1s; }
        .history-card { animation-delay: 0.2s; }

        /* ── RESPONSIVE ─────────────────────── */
        @media (max-width: 600px) {
            .pay-card { padding: 24px 20px; }
            .pay-card-header { flex-direction: column; gap: 16px; }
            .amount-display { text-align: left; }
            .history-header { padding: 18px 16px; }
            thead th, tbody td { padding: 10px 14px; }
        }
    </style>
</head>
<body>

<div class="page-wrap">

    <!-- TOP BAR -->
    <div class="topbar">
        <a href="#" class="logo">
            <div class="logo-icon">⬡</div>
            Nexify
        </a>
        <div class="topbar-right">
            <div class="network-badge">
                <span class="network-dot"></span>
                Polygon Amoy
            </div>
            <button class="theme-toggle" id="themeToggle" onclick="toggleTheme()" title="Toggle theme">
                🌙
            </button>
        </div>
    </div>

    <!-- TICKER -->
    <div class="ticker-wrap">
        <gecko-coin-price-marquee-widget locale="en" dark-mode="true" outlined="true" coin-ids="" initial-currency="inr"></gecko-coin-price-marquee-widget>
    </div>

    <!-- PAYMENT CARD -->
    <div class="pay-card">
        <div class="pay-card-header">
            <div>
                <div class="pay-title">Complete <span>Payment</span></div>
                <div class="pay-subtitle">Secure on-chain transaction via MetaMask</div>
            </div>
            <div class="amount-display">
                <div class="amount-value">0.001</div>
                <div class="amount-label">POL Token</div>
            </div>
        </div>

        <div class="info-row">
            <div class="info-pill">
                <span class="info-pill-icon">🔗</span>
                Network: <strong>Amoy Testnet</strong>
            </div>
            <div class="info-pill">
                <span class="info-pill-icon">⛽</span>
                Gas: <strong>~Minimal</strong>
            </div>
            <div class="info-pill">
                <span class="info-pill-icon">🔒</span>
                <strong>Verified</strong> on-chain
            </div>
        </div>

        <button class="pay-btn" id="payButton" onclick="handlePayment()">
            <span class="metamask-icon">🦊</span>
            Pay with MetaMask
        </button>

        <div class="status-wrap">
            <div class="status-msg" id="statusMessage"></div>
        </div>
    </div>

    <!-- CHART CARD -->
    <div class="chart-card">
        <div class="chart-card-title">POL / INR Price Chart</div>
        <gecko-coin-price-chart-widget locale="en" outlined="true" coin-id="polygon-ecosystem-token" initial-currency="inr"></gecko-coin-price-chart-widget>
    </div>

    <!-- HISTORY TABLE -->
    <div class="history-card">
        <div class="history-header">
            <h3 class="history-title">Payment History</h3>
            <span class="history-count">{{ $orders->count() }} records</span>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Sender Wallet</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Transaction</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <td class="td-date">
                            {{ $order->created_at->format('M d, Y') }}
                            <small>{{ $order->created_at->format('H:i:s') }}</small>
                        </td>
                        <td class="td-wallet">{{ Str::limit($order->wallet_address, 16) }}…</td>
                        <td class="td-amount">{{ $order->amount }} <span style="font-size:11px;font-weight:400;color:var(--text-muted)">POL</span></td>
                        <td>
                            <span class="status-badge {{ strtolower($order->status) === 'confirmed' ? 'confirmed' : 'pending' }}">
                                {{ strtolower($order->status) === 'confirmed' ? '✓' : '◷' }}
                                {{ strtoupper($order->status) }}
                            </span>
                        </td>
                        <td>
                            <a href="https://amoy.polygonscan.com/tx/{{ $order->tx_hash }}" target="_blank" class="tx-link">
                                View ↗
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5">
                            <div class="empty-state">
                                <div class="empty-icon">📭</div>
                                <div class="empty-text">No transactions yet. Make your first payment above.</div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <footer>
        &copy; 2026 <strong>Nexify</strong> — All rights reserved. Made with ❤️ on Web3
    </footer>
</div>

<script>
    /* ── THEME ─────────────────────────────────── */
    const html = document.documentElement;
    const themeToggle = document.getElementById('themeToggle');

    function toggleTheme() {
        const isDark = html.getAttribute('data-theme') === 'dark';
        html.setAttribute('data-theme', isDark ? 'light' : 'dark');
        themeToggle.textContent = isDark ? '🌙' : '☀️';
        localStorage.setItem('nexify-theme', isDark ? 'light' : 'dark');
    }

    // Load saved theme
    (function() {
        const saved = localStorage.getItem('nexify-theme') || 'dark';
        html.setAttribute('data-theme', saved);
        themeToggle.textContent = saved === 'dark' ? '🌙' : '☀️';
    })();

    /* ── PAYMENT ────────────────────────────────── */
    const AMOY_CHAIN_ID = '0x13882';
    const RECEIVER_ADDRESS = '0xd1dfA7363C644ca8600EF858F333e8A945eCE372';
    const AMOUNT_IN_POL = '0.001';

    const payButton = document.getElementById('payButton');
    const statusMsg = document.getElementById('statusMessage');

    function updateStatus(message, type = 'info', icon = '') {
        statusMsg.className = `status-msg visible ${type}`;
        statusMsg.innerHTML = type === 'info' && !icon
            ? `<div class="spinner"></div><span>${message}</span>`
            : `<span>${icon || (type === 'success' ? '✓' : type === 'error' ? '✕' : 'ℹ')}</span><span>${message}</span>`;
    }

    function clearStatus() {
        statusMsg.className = 'status-msg';
    }

    async function ensurePolygonAmoy() {
        try {
            await window.ethereum.request({
                method: 'wallet_switchEthereumChain',
                params: [{ chainId: AMOY_CHAIN_ID }],
            });
        } catch (switchError) {
            if (switchError.code === 4902) {
                await window.ethereum.request({
                    method: 'wallet_addEthereumChain',
                    params: [{
                        chainId: AMOY_CHAIN_ID,
                        chainName: 'Polygon Amoy Testnet',
                        nativeCurrency: { name: 'POL', symbol: 'POL', decimals: 18 },
                        rpcUrls: ['https://rpc-amoy.polygon.technology'],
                        blockExplorerUrls: ['https://amoy.polygonscan.com/']
                    }]
                });
            } else {
                throw switchError;
            }
        }
    }

    async function handlePayment() {
        if (typeof window.ethereum === 'undefined') {
            updateStatus('MetaMask is not installed. Please install it to proceed.', 'error');
            return;
        }

        try {
            payButton.disabled = true;
            payButton.innerHTML = `<div class="spinner"></div> Processing...`;

            updateStatus('Checking network...');
            await ensurePolygonAmoy();

            const provider = new ethers.BrowserProvider(window.ethereum);
            const signer = await provider.getSigner();

            updateStatus('Confirm the transaction in MetaMask...');
            const tx = await signer.sendTransaction({
                to: RECEIVER_ADDRESS,
                value: ethers.parseEther(AMOUNT_IN_POL)
            });

            updateStatus('Transaction sent — awaiting confirmation...');
            const receipt = await tx.wait();

            if (receipt.status === 1) {
                updateStatus('Confirmed! Verifying with server...', 'success', '✓');
                await verifyWithBackend(tx.hash);
            } else {
                throw new Error('Transaction failed on the blockchain.');
            }

        } catch (error) {
            console.error(error);
            if (error.code === 'ACTION_REJECTED') {
                updateStatus('Transaction cancelled by user.', 'error');
            } else {
                updateStatus(error.message || 'An error occurred during payment.', 'error');
            }
        } finally {
            payButton.disabled = false;
            payButton.innerHTML = `<span class="metamask-icon">🦊</span> Pay with MetaMask`;
        }
    }

    async function verifyWithBackend(txHash) {
        try {
            const response = await fetch('/api/verify-payment', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ tx_hash: txHash })
            });

            const result = await response.json();

            if (response.ok && result.success) {
                updateStatus('Payment verified and saved! Reloading...', 'success', '✅');
                setTimeout(() => window.location.reload(), 2000);
            } else {
                updateStatus(`Verification failed: ${result.message}`, 'error');
            }
        } catch (error) {
            console.error(error);
            updateStatus('Error communicating with the server.', 'error');
        }
    }
</script>

</body>
</html>