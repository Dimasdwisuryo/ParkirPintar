
$ErrorActionPreference = "Stop"

# Auto-detect path docker executable jika tidak ada di PATH (terutama saat dijalankan service/Jenkins di Windows)
$candidatePaths = @(
    "C:\Users\ACER\AppData\Local\Programs\DockerDesktop\resources\bin",
    "C:\Program Files\Docker\Docker\resources\bin"
)
foreach ($dir in $candidatePaths) {
    if ((Test-Path (Join-Path $dir "docker.exe")) -and ($env:PATH -notlike "*$dir*")) {
        $env:PATH = "$dir;$env:PATH"
        break
    }
}

$imageName = "parkirpintar:test"

$containerName = "parkirpintar-qa-" +
    [guid]::NewGuid().ToString("N").Substring(0, 8)

$reportDir = Join-Path (Get-Location).Path "test-results"
$reportFile = Join-Path $reportDir "junit.xml"
$buildLogsDir = Join-Path (Get-Location).Path "build\logs"
$buildLogsFile = Join-Path $buildLogsDir "junit.xml"

$created = $false
$exitCode = 1

# Buat direktori output sebelum pengujian berjalan agar selalu tersedia
New-Item -ItemType Directory -Force $reportDir | Out-Null
New-Item -ItemType Directory -Force $buildLogsDir | Out-Null

if (Test-Path $reportFile) {
    Remove-Item $reportFile -Force
}
if (Test-Path $buildLogsFile) {
    Remove-Item $buildLogsFile -Force
}

try {
    Write-Host "=== 1. BUILD TEST IMAGE ==="

    docker build --target test -t $imageName .

    if ($LASTEXITCODE -ne 0) {
        throw "Docker test image gagal dibangun."
    }

    Write-Host "=== 2. CREATE TEST CONTAINER ==="

    $testKey = "base64:AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA="

    docker create `
        --name $containerName `
        -e "APP_ENV=testing" `
        -e "APP_KEY=$testKey" `
        -e "DB_CONNECTION=sqlite" `
        -e "DB_DATABASE=:memory:" `
        -e "SESSION_DRIVER=array" `
        -e "CACHE_STORE=array" `
        $imageName `
        sh -c 'mkdir -p /tmp/results build/logs && php vendor/bin/phpunit --log-junit /tmp/results/junit.xml' |
        Out-Null

    if ($LASTEXITCODE -ne 0) {
        throw "Container test gagal dibuat."
    }

    $created = $true

    Write-Host "=== 3. RUN PHPUnit ==="

    docker start $containerName | Out-Null

    if ($LASTEXITCODE -ne 0) {
        throw "Container test gagal dijalankan."
    }

    $result = docker wait $containerName

    if ($LASTEXITCODE -ne 0) {
        throw "Gagal membaca status pengujian."
    }

    $testExitCode = [int]($result | Select-Object -Last 1)

    Write-Host "=== 4. TEST LOG ==="

    docker logs $containerName

    Write-Host "=== 5. COPY JUNIT XML ==="

    $source = "${containerName}:/tmp/results/junit.xml"

    docker cp $source $reportFile

    if ($LASTEXITCODE -ne 0) {
        throw "File JUnit XML tidak dapat disalin."
    }

    if (-not (Test-Path $reportFile)) {
        throw "Laporan JUnit XML tidak ditemukan."
    }

    # Salin juga ke build\logs\junit.xml agar kompatibel dengan kedua lokasi yang didukung CI/CD
    Copy-Item $reportFile $buildLogsFile -Force

    Write-Host "Laporan tersimpan: $reportFile dan $buildLogsFile"

    $exitCode = $testExitCode
}
catch {
    Write-Host "ERROR: $($_.Exception.Message)"
    $exitCode = 1
}
finally {
    if ($created) {
        docker rm -f $containerName | Out-Null
    }
}

if ($exitCode -eq 0) {
    Write-Host "TEST BERHASIL"
}
else {
    Write-Host "TEST GAGAL"
}

exit $exitCode
