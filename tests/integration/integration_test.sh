#!/bin/sh

# Integration tests for MartialHub application
# Tests HTTP endpoints and basic application flows

# Default: Docker network (for running inside container)
# Override with: BASE_URL=http://localhost:8080 if running from Mac/Linux host
BASE_URL="${BASE_URL:-http://martialhub-web-1:80}"

PASSED=0
FAILED=0

# Colors for output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo "========================================="
echo "MartialHub Integration Tests"
echo "========================================="
echo ""

# Test 1: GET /login - should return 200
echo -n "Test 1: GET /login (should return 200)... "
response=$(curl -s -o /dev/null -w "%{http_code}" "$BASE_URL/login")
if [ "$response" = "200" ]; then
    echo -e "${GREEN}PASSED${NC}"
    PASSED=$((PASSED + 1))
else
    echo -e "${RED}FAILED${NC} (got $response)"
    FAILED=$((FAILED + 1))
fi

# Test 2: GET /register - should return 200
echo -n "Test 2: GET /register (should return 200)... "
response=$(curl -s -o /dev/null -w "%{http_code}" "$BASE_URL/register")
if [ "$response" = "200" ]; then
    echo -e "${GREEN}PASSED${NC}"
    PASSED=$((PASSED + 1))
else
    echo -e "${RED}FAILED${NC} (got $response)"
    FAILED=$((FAILED + 1))
fi

# Test 3: GET /events - should return 200
echo -n "Test 3: GET /events (should return 200)... "
response=$(curl -s -o /dev/null -w "%{http_code}" "$BASE_URL/events")
if [ "$response" = "200" ]; then
    echo -e "${GREEN}PASSED${NC}"
    PASSED=$((PASSED + 1))
else
    echo -e "${RED}FAILED${NC} (got $response)"
    FAILED=$((FAILED + 1))
fi

# Test 4: GET /rankings - should return 200
echo -n "Test 4: GET /rankings (should return 200)... "
response=$(curl -s -o /dev/null -w "%{http_code}" "$BASE_URL/rankings")
if [ "$response" = "200" ]; then
    echo -e "${GREEN}PASSED${NC}"
    PASSED=$((PASSED + 1))
else
    echo -e "${RED}FAILED${NC} (got $response)"
    FAILED=$((FAILED + 1))
fi

# Test 5: GET /profile - should redirect to login (302 or 403)
echo -n "Test 5: GET /profile without auth (should redirect/403)... "
response=$(curl -s -o /dev/null -w "%{http_code}" "$BASE_URL/profile")
if [ "$response" = "302" ] || [ "$response" = "403" ]; then
    echo -e "${GREEN}PASSED${NC}"
    PASSED=$((PASSED + 1))
else
    echo -e "${RED}FAILED${NC} (got $response, expected 302 or 403)"
    FAILED=$((FAILED + 1))
fi

# Test 6: GET /nonexistent - should return 404
echo -n "Test 6: GET /nonexistent (should return 404)... "
response=$(curl -s -o /dev/null -w "%{http_code}" "$BASE_URL/nonexistent")
if [ "$response" = "404" ]; then
    echo -e "${GREEN}PASSED${NC}"
    PASSED=$((PASSED + 1))
else
    echo -e "${RED}FAILED${NC} (got $response)"
    FAILED=$((FAILED + 1))
fi

# Test 7: POST /register without CSRF - should return 403
echo -n "Test 7: POST /register without CSRF (should return 403)... "
response=$(curl -s -o /dev/null -w "%{http_code}" -X POST "$BASE_URL/register" \
    -d "email=test@test.com&password=test123")
if [ "$response" = "403" ]; then
    echo -e "${GREEN}PASSED${NC}"
    PASSED=$((PASSED + 1))
else
    echo -e "${RED}FAILED${NC} (got $response)"
    FAILED=$((FAILED + 1))
fi

# Test 8: GET /admin-users without admin role - should return 403
echo -n "Test 8: GET /adminUsers without auth (should return 403)... "
response=$(curl -s -o /dev/null -w "%{http_code}" "$BASE_URL/adminUsers")
if [ "$response" = "403" ]; then
    echo -e "${GREEN}PASSED${NC}"
    PASSED=$((PASSED + 1))
else
    echo -e "${RED}FAILED${NC} (got $response)"
    FAILED=$((FAILED + 1))
fi

echo ""
echo "========================================="
echo "Test Summary"
echo "========================================="
echo -e "${GREEN}Passed: $PASSED${NC}"
echo -e "${RED}Failed: $FAILED${NC}"
echo ""

if [ $FAILED -eq 0 ]; then
    echo -e "${GREEN}All tests passed!${NC}"
    exit 0
else
    echo -e "${YELLOW}Some tests failed${NC}"
    exit 1
fi
