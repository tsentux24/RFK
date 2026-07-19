import os
import glob

# Files to process
blade_files = glob.glob('resources/views/dashboard/*.blade.php')

for file_path in blade_files:
    with open(file_path, 'r', encoding='utf-8') as f:
        content = f.read()

    # Replace item.realisasi.input_rfk.nama_program with item.realisasi.nama_program
    content = content.replace('item.realisasi.input_rfk.nama_program', 'item.realisasi.nama_program')
    # Same for kode_program
    content = content.replace('item.realisasi.input_rfk.kode_program', 'item.realisasi.kode_program')
    
    # Replace inputRfk.nama_program with (inputRfk.realisasis && inputRfk.realisasis.length > 0 ? inputRfk.realisasis[0].nama_program : '-')
    # Wait, in audit_rfk: const programName = inputRfk ? inputRfk.nama_program : '-';
    # But wait, audit_rfk backend returns `InputRfk` with `nama_program` injected? No, wait!
    
    with open(file_path, 'w', encoding='utf-8') as f:
        f.write(content)

print('Updated realisasi.input_rfk references!')
