<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Web3 Payment System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ethers/6.11.1/ethers.umd.min.js"></script>

    <!-- coin tiker -->
     <script src="https://widgets.coingecko.com/gecko-coin-price-marquee-widget.js"></script>
<gecko-coin-price-marquee-widget locale="en" dark-mode="true" outlined="true" coin-ids="" initial-currency="inr"></gecko-coin-price-marquee-widget>



</head>
<body class="bg-gray-100 p-8 font-sans">
<br>
    <div class="max-w-4xl mx-auto">
        <div class="bg-white p-6 rounded-lg shadow-md mb-8 text-center">
            <h2 class="text-2xl font-bold mb-2">Nexify</h2>
            <p class="text-gray-600 mb-6">0.001 POL (Polygon Amoy)</p>
            
            <button id="payButton" onclick="handlePayment()" class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-6 rounded-lg transition-all">
                🦊 Pay with MetaMask
            </button>
            
            <p id="statusMessage" class="mt-4 text-sm font-semibold text-gray-700"></p>
        </div>



<script src="https://widgets.coingecko.com/gecko-coin-price-chart-widget.js"></script>
<gecko-coin-price-chart-widget locale="en" outlined="true" coin-id="polygon-ecosystem-token" initial-currency="inr"></gecko-coin-price-chart-widget>


        <br>
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-xl font-bold mb-4">Payment History</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="p-3 border-b">Date</th>
                            <th class="p-3 border-b">Sender Wallet</th>
                            <th class="p-3 border-b">Amount</th>
                            <th class="p-3 border-b">Status</th>
                            <th class="p-3 border-b">Tx Hash</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                        <tr class="hover:bg-gray-50">
                            <td class="p-3 border-b">{{ $order->created_at->format('M d, Y H:i') }}</td>
                            <td class="p-3 border-b font-mono text-sm">{{ Str::limit($order->wallet_address, 15) }}...</td>
                            <td class="p-3 border-b">{{ $order->amount }} POL</td>
                            <td class="p-3 border-b text-green-600 font-bold">{{ strtoupper($order->status) }}</td>
                            <td class="p-3 border-b">
                                <a href="https://amoy.polygonscan.com/tx/{{ $order->tx_hash }}" target="_blank" class="text-blue-500 hover:underline">
                                    View Tx ↗
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="p-4 text-center text-gray-500">No payments found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        const AMOY_CHAIN_ID = '0x13882'; // 80002 in hexadecimal
        const RECEIVER_ADDRESS = '0xd1dfA7363C644ca8600EF858F333e8A945eCE372'; // Must match Backend
        const AMOUNT_IN_POL = '0.001';
        
        const payButton = document.getElementById('payButton');
        const statusText = document.getElementById('statusMessage');

        function updateStatus(message, isError = false) {
            statusText.innerText = message;
            statusText.style.color = isError ? 'red' : 'green';
        }

        async function ensurePolygonAmoy() {
            try {
                // Try switching to Amoy
                await window.ethereum.request({
                    method: 'wallet_switchEthereumChain',
                    params: [{ chainId: AMOY_CHAIN_ID }],
                });
            } catch (switchError) {
                // Error code 4902 means the network is not added to MetaMask yet
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
            // 1. Check if MetaMask is installed
            if (typeof window.ethereum === 'undefined') {
                updateStatus('MetaMask is not installed. Please install it to proceed.', true);
                return;
            }

            try {
                payButton.disabled = true;
                payButton.innerText = "⏳ Processing...";
                payButton.classList.add('opacity-50', 'cursor-not-allowed');

                // 2. Ensure Correct Network (Amoy)
                updateStatus('Checking network...');
                await ensurePolygonAmoy();

                // 3. Connect Wallet & Setup Ethers
                const provider = new ethers.BrowserProvider(window.ethereum);
                const signer = await provider.getSigner();
                
                // 4. Send Transaction
                updateStatus('Please confirm the transaction in MetaMask...');
                const tx = await signer.sendTransaction({
                    to: RECEIVER_ADDRESS,
                    value: ethers.parseEther(AMOUNT_IN_POL)
                });

                updateStatus(`Transaction sent! Waiting for confirmation...`);

                // Wait for the transaction to be mined (confirmed) on the blockchain
                const receipt = await tx.wait();
                
                if (receipt.status === 1) {
                    updateStatus('Transaction Confirmed! Verifying with server...');
                    // 5. Send Hash to Backend for Verification
                    await verifyWithBackend(tx.hash);
                } else {
                    throw new Error("Transaction failed on the blockchain.");
                }

            } catch (error) {
                console.error(error);
                // Handle user rejecting the transaction gracefully
                if (error.code === 'ACTION_REJECTED') {
                    updateStatus('Transaction was cancelled by the user.', true);
                } else {
                    updateStatus(error.message || 'An error occurred during payment.', true);
                }
            } finally {
                // Reset UI
                payButton.disabled = false;
                payButton.innerText = "🦊 Pay with MetaMask";
                payButton.classList.remove('opacity-50', 'cursor-not-allowed');
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
                    updateStatus('✅ Payment verified and saved! Reloading...');
                    setTimeout(() => window.location.reload(), 2000);
                } else {
                    updateStatus(`❌ Verification failed: ${result.message}`, true);
                }
            } catch (error) {
                console.error(error);
                updateStatus('❌ Error communicating with the server.', true);
            }
        }
    </script>

<br>
<center>
<footer>&copy; 2026 Nexify. All rights reserved. Made with ❤️</footer>
</center>
</body>
</html>