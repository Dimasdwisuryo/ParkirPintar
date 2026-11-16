$ErrorActionPreference = "Stop"
$maxAttempts = 12
$healthy = $false
$url = if ($env:SMOKE_URL) { $env:SMOKE_URL } else { "http://127.0.0.1:8000/health" }

for ($i = 1; $i -le $maxAttempts; $i++) {
    try {
        $response = Invoke-RestMethod -Uri $url -TimeoutSec 5
        if ($response.status -eq 'healthy') {
            $healthy = $true
            Write-Host "Smoke Test sukses: aplikasi healthy pada percobaan ke-$i."
            break
        }
    }
    catch {
        # Container mungkin masih proses inisialisasi, tunggu retry berikutnya
    }
    Write-Host "Menunggu aplikasi siap: $i/$maxAttempts..."
    Start-Sleep -Seconds 5
}

if (-not $healthy) {
    Write-Error "Smoke Test gagal: endpoint /health belum healthy."
    exit 1
}

exit 0
