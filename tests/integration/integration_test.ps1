# Integration tests for MartialHub application (PowerShell)
# Tests HTTP endpoints and basic application flows

$BaseUrl = "http://localhost:8080"
$Passed = 0
$Failed = 0

Write-Host "=========================================" -ForegroundColor Cyan
Write-Host "MartialHub Integration Tests" -ForegroundColor Cyan
Write-Host "=========================================" -ForegroundColor Cyan
Write-Host ""

# Test 1: GET /login - should return 200
Write-Host "Test 1: GET /login (should return 200)... " -NoNewline
try {
    $response = Invoke-WebRequest -Uri "$BaseUrl/login" -UseBasicParsing -ErrorAction Stop
    if ($response.StatusCode -eq 200) {
        Write-Host "PASSED" -ForegroundColor Green
        $Passed++
    } else {
        Write-Host "FAILED (got $($response.StatusCode))" -ForegroundColor Red
        $Failed++
    }
} catch {
    Write-Host "FAILED ($($_.Exception.Message))" -ForegroundColor Red
    $Failed++
}

# Test 2: GET /register - should return 200
Write-Host "Test 2: GET /register (should return 200)... " -NoNewline
try {
    $response = Invoke-WebRequest -Uri "$BaseUrl/register" -UseBasicParsing -ErrorAction Stop
    if ($response.StatusCode -eq 200) {
        Write-Host "PASSED" -ForegroundColor Green
        $Passed++
    } else {
        Write-Host "FAILED (got $($response.StatusCode))" -ForegroundColor Red
        $Failed++
    }
} catch {
    Write-Host "FAILED ($($_.Exception.Message))" -ForegroundColor Red
    $Failed++
}

# Test 3: GET /events - should return 200
Write-Host "Test 3: GET /events (should return 200)... " -NoNewline
try {
    $response = Invoke-WebRequest -Uri "$BaseUrl/events" -UseBasicParsing -ErrorAction Stop
    if ($response.StatusCode -eq 200) {
        Write-Host "PASSED" -ForegroundColor Green
        $Passed++
    } else {
        Write-Host "FAILED (got $($response.StatusCode))" -ForegroundColor Red
        $Failed++
    }
} catch {
    Write-Host "FAILED ($($_.Exception.Message))" -ForegroundColor Red
    $Failed++
}

# Test 4: GET /rankings - should return 200
Write-Host "Test 4: GET /rankings (should return 200)... " -NoNewline
try {
    $response = Invoke-WebRequest -Uri "$BaseUrl/rankings" -UseBasicParsing -ErrorAction Stop
    if ($response.StatusCode -eq 200) {
        Write-Host "PASSED" -ForegroundColor Green
        $Passed++
    } else {
        Write-Host "FAILED (got $($response.StatusCode))" -ForegroundColor Red
        $Failed++
    }
} catch {
    Write-Host "FAILED ($($_.Exception.Message))" -ForegroundColor Red
    $Failed++
}

# Test 5: GET /profile - should redirect to login (302 or 403)
Write-Host "Test 5: GET /profile without auth (should redirect/403)... " -NoNewline
try {
    $response = Invoke-WebRequest -Uri "$BaseUrl/profile" -UseBasicParsing -MaximumRedirection 0 -ErrorAction SilentlyContinue
    if ($response.StatusCode -eq 302 -or $response.StatusCode -eq 403) {
        Write-Host "PASSED" -ForegroundColor Green
        $Passed++
    } else {
        Write-Host "FAILED (got $($response.StatusCode))" -ForegroundColor Red
        $Failed++
    }
} catch {
    # Check if it's a redirect exception
    if ($_.Exception.Response.StatusCode -eq 302 -or $_.Exception.Response.StatusCode -eq 403) {
        Write-Host "PASSED" -ForegroundColor Green
        $Passed++
    } else {
        Write-Host "FAILED ($($_.Exception.Message))" -ForegroundColor Red
        $Failed++
    }
}

# Test 6: GET /nonexistent - should return 404
Write-Host "Test 6: GET /nonexistent (should return 404)... " -NoNewline
try {
    $response = Invoke-WebRequest -Uri "$BaseUrl/nonexistent" -UseBasicParsing -ErrorAction Stop
    Write-Host "FAILED (got $($response.StatusCode), expected 404)" -ForegroundColor Red
    $Failed++
} catch {
    if ($_.Exception.Response.StatusCode -eq 404) {
        Write-Host "PASSED" -ForegroundColor Green
        $Passed++
    } else {
        Write-Host "FAILED ($($_.Exception.Message))" -ForegroundColor Red
        $Failed++
    }
}

# Test 7: POST /register without CSRF - should return 403
Write-Host "Test 7: POST /register without CSRF (should return 403)... " -NoNewline
try {
    $body = @{
        email = "test@test.com"
        password = "test123"
    }
    $response = Invoke-WebRequest -Uri "$BaseUrl/register" -Method Post -Body $body -UseBasicParsing -ErrorAction Stop
    Write-Host "FAILED (got $($response.StatusCode), expected 403)" -ForegroundColor Red
    $Failed++
} catch {
    if ($_.Exception.Response.StatusCode -eq 403) {
        Write-Host "PASSED" -ForegroundColor Green
        $Passed++
    } else {
        Write-Host "FAILED ($($_.Exception.Message))" -ForegroundColor Red
        $Failed++
    }
}

# Test 8: GET /admin-users without admin role - should return 403
Write-Host "Test 8: GET /adminUsers without auth (should return 403)... " -NoNewline
try {
    $response = Invoke-WebRequest -Uri "$BaseUrl/adminUsers" -UseBasicParsing -ErrorAction Stop
    Write-Host "FAILED (got $($response.StatusCode), expected 403)" -ForegroundColor Red
    $Failed++
} catch {
    if ($_.Exception.Response.StatusCode -eq 403) {
        Write-Host "PASSED" -ForegroundColor Green
        $Passed++
    } else {
        Write-Host "FAILED ($($_.Exception.Message))" -ForegroundColor Red
        $Failed++
    }
}

Write-Host ""
Write-Host "=========================================" -ForegroundColor Cyan
Write-Host "Test Summary" -ForegroundColor Cyan
Write-Host "=========================================" -ForegroundColor Cyan
Write-Host "Passed: $Passed" -ForegroundColor Green
Write-Host "Failed: $Failed" -ForegroundColor Red
Write-Host ""

if ($Failed -eq 0) {
    Write-Host "All tests passed!" -ForegroundColor Green
    exit 0
} else {
    Write-Host "Some tests failed" -ForegroundColor Yellow
    exit 1
}
