<!DOCTYPE html>
<html>

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>

    <h2>Polygon Payment</h2>

    <button onclick="payNow()">Pay with MetaMask</button>

    <script src="https://cdn.jsdelivr.net/npm/ethers@5.7.2/dist/ethers.umd.min.js"></script>

    <script>
        async function payNow() {
            if (!window.ethereum) {
                alert("Install MetaMask");
                return;
            }

            const provider = new ethers.providers.Web3Provider(window.ethereum);
            await provider.send("eth_requestAccounts", []);
            const signer = provider.getSigner();

            const tx = await signer.sendTransaction({
                to: "0xd1dfA7363C644ca8600EF858F333e8A945eCE372",
                value: ethers.utils.parseEther("0.001")
            });

            fetch('/verify', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    tx_hash: tx.hash
                })
            });
        }
    </script>

</body>

</html>