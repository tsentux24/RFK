import os
import re

views_dir = 'resources/views/dashboard'

manual_format_fn = """
    function formatRupiahManual(angka) {
        if (angka === null || angka === undefined) return '0';
        let parsed = parseFloat(angka);
        if (isNaN(parsed)) return '0';
        let str = Math.round(parsed).toString();
        let isNegative = false;
        if (str.startsWith('-')) {
            isNegative = true;
            str = str.substring(1);
        }
        let sisa = str.length % 3;
        let rupiah = str.substr(0, sisa);
        let ribuan = str.substr(sisa).match(/\d{3}/g);
        if (ribuan) {
            let separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }
        return isNegative ? '-' + rupiah : rupiah;
    }
"""

for filename in os.listdir(views_dir):
    if not filename.endswith('.blade.php'): continue
    filepath = os.path.join(views_dir, filename)
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()

    original_content = content
    # Replace Intl format
    content = re.sub(r"new Intl\.NumberFormat\([^)]+\)\.format\(([^)]+)\)", r"formatRupiahManual(\1)", content)

    # Inject function if changed
    if content != original_content and 'function formatRupiahManual' not in content:
        if '<script>' in content:
            content = content.replace('<script>', '<script>\n' + manual_format_fn, 1)
        elif '<script type="text/javascript">' in content:
            content = content.replace('<script type="text/javascript">', '<script type="text/javascript">\n' + manual_format_fn, 1)

    if content != original_content:
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(content)
        print(f"Updated {filename}")

print("Done updating views.")
