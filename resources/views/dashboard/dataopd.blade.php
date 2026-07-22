@extends('dashboard.layout.app',['title'=>'Data OPD'])
@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Form Input Data OPD -->
            <div class="card mb-4">
                <div class="card-header bg-gradient-light text-black">
                    <h5 class="card-title mb-0"><i class="fas fa-building me-2"></i>Form Input Data OPD</h5>
                </div>
                <div class="card-body">
                        <div class="dismissible fade show" role="alert">
                        </div>


                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>Terjadi kesalahan saat menginput data.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form autocomplete="off" id="opdForm" action="{{ route('opd.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="namaOpd" class="form-label">Nama OPD <span class="text-danger">*</span></label>
                                <input type="text" class="form-control rounded" autocomplete="off" id="namaOpd" name="nama_opd" placeholder="Masukkan Nama OPD" required value="{{ old('nama_opd') }}" maxlength="80">
                                @error('nama_opd')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="kabupatenKota" class="form-label">Kabupaten/Kota <span class="text-danger">*</span></label>
                                <select class="form-select rounded" id="kabupatenKota" name="kabupaten_kota" required>
                                    <option value="" selected disabled>Pilih Kabupaten/Kota</option>
                                    <option value="Kota Ternate" {{ old('kabupaten_kota') == 'Kota Ternate' ? 'selected' : '' }}>Kota Ternate</option>
                                    <option value="Kota Tidore" {{ old('kabupaten_kota') == 'Kota Tidore' ? 'selected' : '' }}>Kota Tidore</option>
                                    <option value="Halmahera Barat" {{ old('kabupaten_kota') == 'Halmahera Barat' ? 'selected' : '' }}>Halmahera Barat</option>
                                    <option value="Halmahera Utara" {{ old('kabupaten_kota') == 'Halmahera Utara' ? 'selected' : '' }}>Halmahera Utara</option>
                                    <option value="Halmahera Selatan" {{ old('kabupaten_kota') == 'Halmahera Selatan' ? 'selected' : '' }}>Halmahera Selatan</option>
                                    <option value="Halmahera Timur" {{ old('kabupaten_kota') == 'Halmahera Timur' ? 'selected' : '' }}>Halmahera Timur</option>
                                    <option value="Halmahera Tengah" {{ old('kabupaten_kota') == 'Halmahera Tengah' ? 'selected' : '' }}>Halmahera Tengah</option>
                                    <option value="Pulau Morotai" {{ old('kabupaten_kota') == 'Pulau Morotai' ? 'selected' : '' }}>Pulau Morotai</option>
                                    <option value="Pulau Taliabu" {{ old('kabupaten_kota') == 'Pulau Taliabu' ? 'selected' : '' }}>Pulau Taliabu</option>
                                </select>
                                @error('kabupaten_kota')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="kategoriSektor" class="form-label">Kategori Sektor <span class="text-danger">*</span></label>
                                <select class="form-select rounded" id="kategoriSektor" name="kategori_sektor" required>
                                    <option value="" selected disabled>Pilih Kategori Sektor</option>
                                    <option value="Pemerintahan" {{ old('kategori_sektor') == 'Pemerintahan' ? 'selected' : '' }}>Pemerintahan</option>
                                    <option value="Kesehatan" {{ old('kategori_sektor') == 'Kesehatan' ? 'selected' : '' }}>Kesehatan</option>
                                    <option value="Pendidikan" {{ old('kategori_sektor') == 'Pendidikan' ? 'selected' : '' }}>Pendidikan</option>
                                    <option value="Infrastruktur" {{ old('kategori_sektor') == 'Infrastruktur' ? 'selected' : '' }}>Infrastruktur</option>
                                    <option value="Sosial" {{ old('kategori_sektor') == 'Sosial' ? 'selected' : '' }}>Sosial</option>
                                    <option value="Lainnya" {{ old('kategori_sektor') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                </select>
                                @error('kategori_sektor')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="noTelepon" class="form-label">No. Telepon/HP OPD <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text rounded-start">+62</span>
                                    <input type="number" class="form-control rounded-end" id="noTelepon" name="no_telepon" placeholder="81234567890" required pattern="[0-9]{9,13}" title="Masukkan nomor telepon tanpa kode negara (contoh: 81234567890)" value="{{ old('no_telepon') }}" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="13" autocomplete="off">
                                </div>
                                <div class="form-text">Format: +62 diikuti 9-13 digit angka (contoh: +6281234567890)</div>
                                @error('no_telepon')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="alamat" class="form-label">Alamat <span class="text-danger">*</span></label>
                                <textarea class="form-control rounded" id="alamat" name="alamat" rows="3" placeholder="Masukkan Alamat Lengkap" autocomplete="off" required>{{ old('alamat') }}</textarea>
                                @error('alamat')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select rounded" id="status" name="status" required>
                                    <option value="Aktif" {{ old('status', 'Aktif') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="Non-Aktif" {{ old('status') == 'Non-Aktif' ? 'selected' : '' }}>Non-Aktif</option>
                                </select>
                                @error('status')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 d-flex justify-content-end gap-2">
                                <button type="reset" class="btn btn-secondary rounded-pill px-4">
                                    <i class="fas fa-redo me-2"></i>Reset
                                </button>
                                <button type="submit" class="btn btn-primary rounded-pill px-4">
                                    <i class="fas fa-save me-2"></i>Simpan Data
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

@include('dashboard.layout.table-opdlist')
@endsection

