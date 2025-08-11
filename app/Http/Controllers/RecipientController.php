<?php

namespace App\Http\Controllers;

use App\Models\Recipient;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;

class RecipientController extends Controller
{
    public function index()
    {
        $recipients = Recipient::paginate(20);
        return view('recipients.index', compact('recipients'));
    }

    public function create()
    {
        return view('recipients.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'child_name' => 'required|string|max:255',
            'Ayah_name' => 'required|string|max:255',
            'Ibu_name' => 'required|string|max:255',
            'birth_place' => 'required|string|max:255',
            'birth_date' => 'required|date',
            'school_level' => 'required|string|max:255',
            'school_name' => 'required|string|max:255',
            'address' => 'required|string',
            'class' => 'required|string|max:255',
            'shoe_size' => 'required|string|max:10',
            'shirt_size' => 'required|string|max:10',
        ]);

        // Generate unique QR code
        $qrCode = $this->generateUniqueQrCode();

        $recipient = Recipient::create(array_merge($request->all(), [
            'qr_code' => $qrCode
        ]));

        return redirect()->route('recipients.index')
            ->with('success', 'Data penerima berhasil ditambahkan dengan QR Code: ' . $qrCode);
    }

    public function show(Recipient $recipient)
    {
        return view('recipients.show', compact('recipient'));
    }

    public function edit(Recipient $recipient)
    {
        return view('recipients.edit', compact('recipient'));
    }

    public function update(Request $request, Recipient $recipient)
    {
        $request->validate([
            'child_name' => 'required|string|max:255',
            'Ayah_name' => 'required|string|max:255',
            'Ibu_name' => 'required|string|max:255',
            'birth_place' => 'required|string|max:255',
            'birth_date' => 'required|date',
            'school_level' => 'required|string|max:255',
            'school_name' => 'required|string|max:255',
            'address' => 'required|string',
            'class' => 'required|string|max:255',
            'shoe_size' => 'required|string|max:10',
            'shirt_size' => 'required|string|max:10',
        ]);

        $recipient->update($request->all());

        return redirect()->route('recipients.index')
            ->with('success', 'Data penerima berhasil diperbarui');
    }

    public function destroy(Recipient $recipient)
    {
        $recipient->delete();
        return redirect()->route('recipients.index')
            ->with('success', 'Data penerima berhasil dihapus');
    }

    public function generateQrCode(Recipient $recipient)
    {
        $encryptedCode = base64_encode($recipient->qr_code . '|' . $recipient->id);

        $qrCode = QrCode::size(200)
            ->format('png')
            ->generate($encryptedCode);

        return response($qrCode, 200)
            ->header('Content-Type', 'image/png');
    }

    public function printQrCode(Recipient $recipient)
    {
        $encryptedCode = base64_encode($recipient->qr_code . '|' . $recipient->id);

        $pdf = Pdf::loadView('recipients.qr-print', compact('recipient', 'encryptedCode'));

        return $pdf->stream('qr-code-' . $recipient->qr_code . '.pdf');
    }

    public function scanQr()
    {
        return view('recipients.scan');
    }

    // public function printAllQrCodes()
    // {
    //     $recipients = Recipient::all();

    //     if ($recipients->isEmpty()) {
    //         return back()->with('error', 'Tidak ada data penerima.');
    //     }

    //     // Folder sementara untuk simpan PDF
    //     $tempDir = storage_path('app/temp_qr_codes');
    //     if (!file_exists($tempDir)) {
    //         mkdir($tempDir, 0777, true);
    //     }

    //     $zipFile = storage_path('app/qr_codes_all.zip');
    //     $zip = new ZipArchive;
    //     if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {

    //         foreach ($recipients as $recipient) {
    //             $encryptedCode = base64_encode($recipient->qr_code . '|' . $recipient->id);

    //             // Generate PDF
    //             $pdf = Pdf::loadView('recipients.qr-print', compact('recipient', 'encryptedCode'));

    //             $pdfFileName = 'qr-code-' . $recipient->qr_code . '.pdf';
    //             $pdfPath = $tempDir . '/' . $pdfFileName;

    //             file_put_contents($pdfPath, $pdf->output());

    //             // Masukkan ke ZIP
    //             $zip->addFile($pdfPath, $pdfFileName);
    //         }

    //         $zip->close();
    //     } else {
    //         return back()->with('error', 'Gagal membuat file ZIP.');
    //     }

    //     // Hapus file PDF sementara
    //     foreach (glob($tempDir . '/*.pdf') as $file) {
    //         unlink($file);
    //     }
    //     rmdir($tempDir);

    //     // Download ZIP
    //     return response()->download($zipFile)->deleteFileAfterSend(true);
    // }


    public function verifyQr(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|string'
        ]);

        try {
            $qrInput = $request->qr_code;

            $recipient = Recipient::where('qr_code', $qrInput)->first();

            if (!$recipient) {
                return response()->json(['error' => 'QR Code tidak ditemukan'], 404);
            }

            // Tambahkan pengecekan: harus sudah registrasi
            if (!$recipient->registrasi) {
                return response()->json(['error' => 'Penerima belum registrasi'], 403);
            }

            return response()->json([
                'success' => true,
                'recipient' => $recipient,
                'status' => $recipient->distribution_status
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'QR Code tidak valid: ' . $e->getMessage()], 400);
        }
    }

    public function distribute(Request $request, Recipient $recipient)
    {
        try {
            $recipient->update([
                'is_distributed' => true,
                'distributed_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Status penyaluran berhasil diperbarui',
                'is_fully_distributed' => true,
                'recipient_id' => $recipient->id
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage()
            ], 500);
        }
    }



    public function generateReceipt(Recipient $recipient)
    {
        if (!$recipient->is_distributed) {
            return redirect()->back()->with('error', 'Penyaluran belum selesai');
        }

        $encryptedCode = base64_encode($recipient->qr_code . '|' . $recipient->id);

        $pdf = Pdf::loadView('recipients.receipt', compact('recipient', 'encryptedCode'));

        return $pdf->stream('bukti-penerimaan-' . $recipient->qr_code . '.pdf');
    }

    public function generateSignatureForm(Recipient $recipient)
    {
        if (!$recipient->is_distributed) {
            return redirect()->back()->with('error', 'Penyaluran belum selesai');
        }

        $encryptedCode = base64_encode($recipient->qr_code . '|' . $recipient->id);

        $pdf = Pdf::loadView('recipients.signature-form', compact('recipient', 'encryptedCode'));

        return $pdf->stream('form-tanda-tangan-' . $recipient->qr_code . '.pdf');
    }

    public function generateReport()
    {
        $totalRecipients = Recipient::count();
        $distributedCount = Recipient::where('is_distributed', true)->count();
        $pendingCount = $totalRecipients - $distributedCount;


        $recipients = Recipient::orderBy('created_at', 'desc')->get();
        $distributedRecipients = Recipient::where('is_distributed', true)
            ->orderBy('distributed_at', 'desc')->get();

        $pdf = Pdf::loadView('recipients.report', compact(
            'totalRecipients',
            'distributedCount',
            'pendingCount',
            'recipients',
            'distributedRecipients'
        ));

        return $pdf->stream('laporan-bansos-pendidikan-' . date('Y-m-d') . '.pdf');
    }

    private function generateUniqueQrCode()
    {
        do {
            // Get the next available number
            $lastRecipient = Recipient::orderBy('id', 'desc')->first();
            $nextNumber = $lastRecipient ? $lastRecipient->id + 1 : 1;

            $qrCode = 'CBP' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        } while (Recipient::where('qr_code', $qrCode)->exists());

        return $qrCode;
    }

    public function verifyQrRegistration(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|string'
        ]);

        try {
            $qrInput = $request->qr_code;

            // Cari penerima berdasarkan QR Code
            $recipient = Recipient::where('qr_code', $qrInput)->first();

            if (!$recipient) {
                return response()->json(['error' => 'QR Code tidak ditemukan'], 404);
            }

            // Jika sudah registrasi, beri info
            if ($recipient->registrasi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Penerima ini sudah terdaftar (registrasi sudah dilakukan).',
                    'recipient' => $recipient
                ], 200);
            }

            // Kalau belum, kirim data untuk ditampilkan di halaman registrasi
            return response()->json([
                'success' => true,
                'recipient' => $recipient
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'QR Code tidak valid: ' . $e->getMessage()], 400);
        }
    }

    public function markRegistered(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|string'
        ]);

        try {
            $recipient = Recipient::where('qr_code', $request->qr_code)->first();

            if (!$recipient) {
                return response()->json(['error' => 'QR Code tidak ditemukan'], 404);
            }

            if ($recipient->registered) {
                return response()->json(['error' => 'Penerima ini sudah terdaftar sebelumnya'], 400);
            }

            // Update status registrasi
            $recipient->registrasi = true;
            $recipient->save();

            return response()->json([
                'success' => true,
                'message' => 'Registrasi berhasil disimpan',
                'recipient' => $recipient
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal memperbarui registrasi: ' . $e->getMessage()], 400);
        }
    }
}
