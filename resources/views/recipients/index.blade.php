@extends('layouts.app')

@section('title', 'Data Penerima')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{ route('recipients.import') }}" class="btn btn-primary ms-auto">
            <i class="fas fa-plus me-2"></i>Import Excel
        </a>
    </div>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <a href="{{ route('recipients.printAll') }}" class="btn btn-success btn-sm me-2">
                <i class="fas fa-download"></i> Download Semua QR (ZIP)
            </a>

    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>QR Code</th>
                            <th>Nama Anak</th>
                            <th>Nama Ayah</th>
                            <th>Nama Ibu</th>
                            <th>Sekolah</th>
                            <th>Kelas</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recipients as $recipient)
                            <tr>
                                <td>
                                    <span class="badge bg-primary">{{ $recipient->qr_code }}</span>
                                </td>
                                <td>{{ $recipient->child_name }}</td>
                                <td>{{ $recipient->Ayah_name }}</td>
                                <td>{{ $recipient->Ibu_name }}</td>
                                <td>{{ $recipient->school_name }}</td>
                                <td>{{ $recipient->class }}</td>
                                <style>
                                    .status-box {
                                        display: inline-flex;
                                        align-items: center;
                                        gap: 5px;
                                        padding: 4px 10px;
                                        border-radius: 5px;
                                        font-weight: 600;
                                        font-size: 0.85rem;
                                        color: white;
                                        text-decoration: none;
                                    }

                                    .status-red {
                                        background-color: #dc3545;
                                    }

                                    .status-yellow {
                                        background-color: #ffc107;
                                        color: #212529;
                                    }

                                    .status-green {
                                        background-color: #28a745;
                                    }

                                    .status-box i {
                                        font-size: 1rem;
                                    }
                                </style>

                                <td>
                                    @if ($recipient->is_distributed && $recipient->registrasi)
                                        <a href="#" class="status-box status-green">
                                            <i class="bi bi-check-all"></i> Completed
                                        </a>
                                    @elseif ($recipient->registrasi && !$recipient->is_distributed)
                                        <a href="#" class="status-box status-yellow">
                                            <i class="bi bi-check"></i> Sudah registrasi
                                        </a>
                                    @else
                                        <a href="#" class="status-box status-red">
                                            <i class="bi bi-x"></i> Belum registrasi
                                        </a>
                                    @endif
                                </td>

                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('recipients.show', $recipient) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('recipients.edit', $recipient) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('recipients.destroy', $recipient) }}" method="POST"
                                            class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Belum ada data penerima</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center">
                {{ $recipients->links() }}
            </div>
        </div>
    </div>
@endsection
