#!/bin/bash

BASE_URL="http://127.0.0.1:8000"
echo "================================================="
echo "🚀 AUTOMATED SMOKE TEST - FASHIONPOS"
echo "================================================="
echo "Checking core endpoints..."
echo ""

check_endpoint() {
    local endpoint=$1
    local name=$2
    
    # We use -s for silent, -o /dev/null to discard body, and -w to get HTTP status
    status=$(curl -s -o /dev/null -w "%{http_code}" "${BASE_URL}${endpoint}")
    
    if [ "$status" == "200" ]; then
        echo "✅ [PASS] $name ($endpoint) - HTTP 200 OK"
    elif [ "$status" == "302" ]; then
        echo "✅ [PASS] $name ($endpoint) - HTTP 302 Redirect (Expected for Auth/Guests)"
    else
        echo "❌ [FAIL] $name ($endpoint) - HTTP $status"
    fi
}

# Run tests
check_endpoint "/login" "Login Page"
check_endpoint "/pin-login" "PIN Login Page"
check_endpoint "/dashboard" "Dashboard"
check_endpoint "/pos" "POS App"
check_endpoint "/inventory/products" "Product Inventory"
check_endpoint "/inventory/categories" "Categories"
check_endpoint "/inventory/stock" "Stock List"
check_endpoint "/purchase/orders" "Purchase Orders"
check_endpoint "/assets" "Assets Management"
check_endpoint "/reports/sales" "Sales Reports"
check_endpoint "/users" "User Management"

echo ""
echo "================================================="
echo "Automated testing finished."
