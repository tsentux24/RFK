import urllib.request
import urllib.parse
import json
import re
from http.cookiejar import CookieJar

cj = CookieJar()
opener = urllib.request.build_opener(urllib.request.HTTPCookieProcessor(cj))
urllib.request.install_opener(opener)

# 1. Get login page to grab CSRF
req = urllib.request.Request('http://127.0.0.1:8000/login')
with urllib.request.urlopen(req) as response:
    html = response.read().decode('utf-8')
    match = re.search(r'<meta name="csrf-token" content="([^"]+)">', html)
    csrf_token = match.group(1) if match else None

if not csrf_token:
    print("Could not find CSRF token on login page")
    exit(1)

# 2. Login as staff
data = urllib.parse.urlencode({
    '_token': csrf_token,
    'email': 'staff1@example.com', # Maybe staff@example.com doesn't exist. Let's find a valid email.
    'password': 'password'
}).encode('utf-8')
req = urllib.request.Request('http://127.0.0.1:8000/login', data=data)
try:
    with urllib.request.urlopen(req) as response:
        html = response.read().decode('utf-8')
except urllib.error.HTTPError as e:
    print("Login error:", e.read().decode('utf-8'))

# Let's bypass login and just print what happens when we GET /staff/dashboard if logged in
req = urllib.request.Request('http://127.0.0.1:8000/dashboard/staff')
try:
    with urllib.request.urlopen(req) as response:
        html = response.read().decode('utf-8')
        match = re.search(r'<meta name="csrf-token" content="([^"]+)">', html)
        csrf_token = match.group(1) if match else None
except urllib.error.HTTPError as e:
    print("Dashboard error:", e.status)
    exit(1)

if not csrf_token:
    print("Not logged in or no csrf token")
    exit(1)

# 4. Store RFK
rfk_data = json.dumps({
    'sumber_dana': 'APBD',
    'tahun_anggaran': 2026,
    'pagu': 1000000,
    'keterangan': 'Test dari python script'
}).encode('utf-8')

req = urllib.request.Request('http://127.0.0.1:8000/dashboard/rfk/store', data=rfk_data, headers={
    'Content-Type': 'application/json',
    'X-CSRF-TOKEN': csrf_token,
    'Accept': 'application/json'
})

try:
    with urllib.request.urlopen(req) as response:
        print("STATUS:", response.status)
        print(response.read().decode('utf-8'))
except urllib.error.HTTPError as e:
    print("ERROR STATUS:", e.status)
    print(e.read().decode('utf-8'))
