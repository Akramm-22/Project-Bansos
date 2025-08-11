@extends('layouts.app')

@section('title', 'Edit Penerima')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header">
                <h5 class="mb-0">Edit Data Penerima</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('recipients.update', $recipient) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="child_name" class="form-label">Nama Anak <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('child_name') is-invalid @enderror"
                                   id="child_name" name="child_name" value="{{ old('child_name', $recipient->child_name) }}" required>
                            @error('child_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
    <div class="col-md-6 mb-3">
        <label for="Ayah_name" class="form-label">Nama Ayah <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('Ayah_name') is-invalid @enderror"
               id="Ayah_name" name="Ayah_name" value="{{ old('Ayah_name', $recipient->Ayah_name) }}" required>
        @error('Ayah_name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="Ibu_name" class="form-label">Nama Ibu <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('Ibu_name') is-invalid @enderror"
               id="Ibu_name" name="Ibu_name" value="{{ old('Ibu_name', $recipient->Ibu_name) }}" required>
        @error('Ibu_name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="birth_place" class="form-label">Tempat Lahir <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('birth_place') is-invalid @enderror"
                                   id="birth_place" name="birth_place" value="{{ old('birth_place', $recipient->birth_place) }}" required>
                            @error('birth_place')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="birth_date" class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('birth_date') is-invalid @enderror"
                                   id="birth_date" name="birth_date" value="{{ old('birth_date', $recipient->birth_date->format('Y-m-d')) }}" required>
                            @error('birth_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="school_level" class="form-label">Tingkat Sekolah <span class="text-danger">*</span></label>
                            <select class="form-select @error('school_level') is-invalid @enderror"
                                    id="school_level" name="school_level" required>
                                <option value="">Pilih Tingkat</option>
                                <option value="SD" {{ old('school_level', $recipient->school_level) == 'SD' ? 'selected' : '' }}>SD</option>
                                <option value="SMP" {{ old('school_level', $recipient->school_level) == 'SMP' ? 'selected' : '' }}>SMP</option>
                                <option value="SMA" {{ old('school_level', $recipient->school_level) == 'SMA' ? 'selected' : '' }}>SMA</option>
                                <option value="SMK" {{ old('school_level', $recipient->school_level) == 'SMK' ? 'selected' : '' }}>SMK</option>
                            </select>
                            @error('school_level')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="school_name" class="form-label">Nama Sekolah <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('school_name') is-invalid @enderror"
                                   id="school_name" name="school_name" value="{{ old('school_name', $recipient->school_name) }}" required>
                            @error('school_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Alamat <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('address') is-invalid @enderror"
                                  id="address" name="address" rows="3" required>{{ old('address', $recipient->address) }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('recipients.show', $recipient) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
