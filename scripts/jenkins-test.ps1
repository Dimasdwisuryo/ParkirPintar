
$ErrorActionPreference = "Stop"

$imageName = "parkirpintar:test"

$containerName = "parkirpintar-qa-" +
    [guid]::NewGuid().ToString("N").Substring(0, 8)

$reportDir = Join-Path (Get-Location).Path "test-results"
$reportFile = Join-Path $reportDir "junit.xml"

$created = $false
$exitCode = 1

New-Item -ItemType Directory -Force $reportDir | Out-Null

if (Test-Path $reportFile) {
    Remove-Item $reportFile -Force
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
        sh -c 'mkdir -p /tmp/results && php vendor/bin/phpunit --log-junit /tmp/results/junit.xml' |
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

    Write-Host "Laporan tersimpan: $reportFile"

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
