<?php

namespace Database\Seeders;

use App\Models\Division;
use App\Models\User;
use App\Models\IncomingLetter;
use App\Models\OutgoingLetter;
use App\Models\Disposition;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 0. Seed Settings
        $this->call(SettingSeeder::class);

        // 1. Create Roles
        $roles = [
            'Admin',
            'Operator Divisi Umum',
            'Operator Divisi',
            'Pimpinan',
        ];

        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        // 2. Create Divisions for Dinas Kominfo
        $divSekretariat = Division::create([
            'name' => 'Sekretariat',
            'code' => 'SET',
            'description' => 'Bagian Sekretariat Dinas Kominfo',
        ]);

        $divAptika = Division::create([
            'name' => 'Bidang Aplikasi Informatika (APTIKA)',
            'code' => 'APTIKA',
            'description' => 'Bidang Aplikasi Informatika',
        ]);

        $divIKP = Division::create([
            'name' => 'Bidang Informasi dan Komunikasi Publik (IKP)',
            'code' => 'IKP',
            'description' => 'Bidang Informasi dan Komunikasi Publik',
        ]);

        $divStatistik = Division::create([
            'name' => 'Bidang Statistik dan Persandian',
            'code' => 'STAT',
            'description' => 'Bidang Statistik dan Persandian',
        ]);

        // 3. Create Users & Assign Roles

        // Admin (Super User)
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@kominfo.go.id',
            'password' => Hash::make('password'),
            'division_id' => $divSekretariat->id,
        ]);
        $admin->assignRole('Admin');

        // Operator Umum (Bisa kelola semua surat)
        $opUmum = User::create([
            'name' => 'Operator Sekretariat',
            'email' => 'sekretariat@kominfo.go.id',
            'password' => Hash::make('password'),
            'division_id' => $divSekretariat->id,
        ]);
        $opUmum->assignRole('Operator Divisi Umum');

        // Operator APTIKA
        $opAptika = User::create([
            'name' => 'Operator APTIKA',
            'email' => 'aptika@kominfo.go.id',
            'password' => Hash::make('password'),
            'division_id' => $divAptika->id,
        ]);
        $opAptika->assignRole('Operator Divisi');

        // Operator IKP
        $opIKP = User::create([
            'name' => 'Operator IKP',
            'email' => 'ikp@kominfo.go.id',
            'password' => Hash::make('password'),
            'division_id' => $divIKP->id,
        ]);
        $opIKP->assignRole('Operator Divisi');

        // Operator Statistik
        $opStatistik = User::create([
            'name' => 'Operator Statistik',
            'email' => 'statistik@kominfo.go.id',
            'password' => Hash::make('password'),
            'division_id' => $divStatistik->id,
        ]);
        $opStatistik->assignRole('Operator Divisi');

        // Pimpinan (Kepala Dinas)
        $pimpinan = User::create([
            'name' => 'Kepala Dinas Kominfo',
            'email' => 'kadis@kominfo.go.id',
            'password' => Hash::make('password'),
            'division_id' => $divSekretariat->id,
        ]);
        $pimpinan->assignRole('Pimpinan');

        // 4. Generate a Valid Dummy PDF
        // This is a minimal valid PDF base64 string
        $pdfBase64 = 'JVBERi0xLjcKCjEgMCBvYmogICUKPDwKICAvVHlwZSAvQ2F0YWxvZwogIC9QYWdlcyAyIDAgUgo+PgplbmRvYmoKCjIgMCBvYmogICUKPDwKICAvVHlwZSAvUGFnZXMKICAvS2lkcyBbMyAwIFJdCiAgL0NvdW50IDEKPj4KZW5kb2JqCgozIDAgb2JqICAlCjw8CiAgL1R5cGUgL1BhZ2UKICAvUGFyZW50IDIgMCBSCiAgL01lZGlhQm94IFswIDAgNTk1LjI4IDg0MS44OV0KICAvUmVzb3VyY2VzIDw8CiAgICAvRm9udCA8PAogICAgICAvRjEgNCAwIFIKICAgID4+CiAgPj4KICAvQ29udGVudHMgNSAwIFIKPj4KZW5kb2JqCgo0IDAgb2JqICAlCjw8CiAgL1R5cGUgL0ZvbnQKICAvU3VidHlwZSAvVHlwZTUKICAvQmFzZUZvbnQgL0hlbHZldGljYQo+PgplbmRvYmoKCjUgMCBvYmogICUKPDwKICAvTGVuZ3RoIDQ0Cj4+CnN0cmVhbQpCVEQKL0YxIDE4IFRmCjEwMCA3MDAgVGQKKER1bW15IFBERiBEb2N1bWVudCkgVGoKRVQKZW5kc3RyZWFtCmVuZG9iagoKeHJlZgowIDYKMDAwMDAwMDAwMCA2NTUzNSBmIAowMDAwMDAwMDEwIDAwMDAwIG4gCjAwMDAwMDAwNjAgMDAwMDAgbiAKMDAwMDAwMDExNyAwMDAwMCBuIAowMDAwMDAwMjIzIDAwMDAwIG4gCjAwMDAwMDAzMTEgMDAwMDAgbiAKdHJhaWxlcgo8PAogIC9TaXplIDYKICAvUm9vdCAxIDAgUgo+PgpzdGFydHhyZWYKMzgwCiUlRU9GCg==';
        $pdfPath = 'letters/dummy_surat.pdf';
        
        Storage::disk('public')->put($pdfPath, base64_decode($pdfBase64));

        // 5. Create Dummy Data for Testing (Incoming Letters - Surat Masuk)
        $incoming1 = IncomingLetter::create([
            'agenda_number' => 'AGN/KOMINFO/2026/001',
            'mail_number' => '005/123/BAPPEDA/2026',
            'mail_date' => Carbon::now()->subDays(5)->format('Y-m-d'),
            'received_date' => Carbon::now()->subDays(3)->format('Y-m-d'),
            'origin' => 'BAPPEDA Provinsi',
            'subject' => 'Undangan Rapat Koordinasi SPBE',
            'file_path' => $pdfPath,
            'status' => 'disposition',
            'created_by' => $opUmum->id,
        ]);

        $incoming2 = IncomingLetter::create([
            'agenda_number' => 'AGN/KOMINFO/2026/002',
            'mail_number' => '800/456/SETDA/2026',
            'mail_date' => Carbon::now()->subDays(3)->format('Y-m-d'),
            'received_date' => Carbon::now()->subDays(2)->format('Y-m-d'),
            'origin' => 'Sekretariat Daerah',
            'subject' => 'Surat Edaran Disiplin Pegawai',
            'file_path' => $pdfPath,
            'status' => 'done',
            'created_by' => $opUmum->id,
        ]);

        $incoming3 = IncomingLetter::create([
            'agenda_number' => 'AGN/KOMINFO/2026/003',
            'mail_number' => '480/789/HUMAS/2026',
            'mail_date' => Carbon::now()->subDays(2)->format('Y-m-d'),
            'received_date' => Carbon::now()->subDays(1)->format('Y-m-d'),
            'origin' => 'Humas Pemprov',
            'subject' => 'Permohonan Liputan Media Acara Gubernur',
            'file_path' => $pdfPath,
            'status' => 'disposition',
            'created_by' => $opUmum->id,
        ]);

        $incoming4 = IncomingLetter::create([
            'agenda_number' => 'AGN/KOMINFO/2026/004',
            'mail_number' => '900/101/BPS/2026',
            'mail_date' => Carbon::now()->subDays(1)->format('Y-m-d'),
            'received_date' => Carbon::now()->format('Y-m-d'),
            'origin' => 'Badan Pusat Statistik',
            'subject' => 'Permintaan Data Sektoral Daerah Triwulan I',
            'file_path' => $pdfPath,
            'status' => 'done', // Changed from 'new'
            'created_by' => $opUmum->id,
        ]);

        $incoming5 = IncomingLetter::create([
            'agenda_number' => 'AGN/KOMINFO/2026/005',
            'mail_number' => '100/222/SETDA/2026',
            'mail_date' => Carbon::now()->format('Y-m-d'),
            'received_date' => Carbon::now()->format('Y-m-d'),
            'origin' => 'Sekretaris Daerah',
            'subject' => 'Pemberitahuan Apel Gabungan ASN',
            'file_path' => $pdfPath,
            'status' => 'new',
            'created_by' => $opUmum->id,
        ]);

        // 6. Create Dummy Data for Outgoing Letters (Surat Keluar)
        OutgoingLetter::create([
            'mail_number' => '045.2/001/KOMINFO/2026',
            'recipient' => 'Dinas Pendidikan',
            'mail_date' => Carbon::now()->subDays(4)->format('Y-m-d'),
            'subject' => 'Pemberitahuan Pemeliharaan Jaringan Internet',
            'division_id' => $divAptika->id,
            'file_path' => $pdfPath,
            'created_by' => $opAptika->id,
        ]);

        OutgoingLetter::create([
            'mail_number' => '045.2/002/KOMINFO/2026',
            'recipient' => 'Kementerian Kominfo RI',
            'mail_date' => Carbon::now()->subDays(2)->format('Y-m-d'),
            'subject' => 'Laporan Rekapitulasi SPBE Daerah',
            'division_id' => $divAptika->id,
            'file_path' => $pdfPath,
            'created_by' => $opAptika->id,
        ]);

        OutgoingLetter::create([
            'mail_number' => '481/003/KOMINFO/2026',
            'recipient' => 'Seluruh OPD Kota/Kab',
            'mail_date' => Carbon::now()->subDays(1)->format('Y-m-d'),
            'subject' => 'Undangan Sosialisasi Keamanan Informasi',
            'division_id' => $divIKP->id,
            'file_path' => $pdfPath,
            'created_by' => $opIKP->id,
        ]);

        OutgoingLetter::create([
            'mail_number' => '800/004/KOMINFO/2026',
            'recipient' => 'Badan Kepegawaian Daerah',
            'mail_date' => Carbon::now()->format('Y-m-d'),
            'subject' => 'Pengajuan Kebutuhan ASN Formasi IT',
            'division_id' => $divSekretariat->id,
            'file_path' => $pdfPath,
            'created_by' => $opUmum->id,
        ]);

        // 7. Create Disposition Dummy Data
        Disposition::create([
            'incoming_letter_id' => $incoming1->id,
            'from_division_id' => $divSekretariat->id,
            'to_division_id' => $divAptika->id,
            'notes' => 'Tolong segera tindaklanjuti undangan rapat SPBE ini dan siapkan bahannya.',
            'due_date' => Carbon::now()->addDays(2)->format('Y-m-d'),
            'status' => 'pending',
            'created_by' => $pimpinan->id,
        ]);

        Disposition::create([
            'incoming_letter_id' => $incoming3->id,
            'from_division_id' => $divSekretariat->id,
            'to_division_id' => $divIKP->id,
            'notes' => 'Tugaskan tim peliputan untuk mengkover acara Gubernur.',
            'due_date' => Carbon::now()->addDays(1)->format('Y-m-d'),
            'status' => 'process',
            'created_by' => $pimpinan->id,
        ]);

        Disposition::create([
            'incoming_letter_id' => $incoming2->id,
            'from_division_id' => $divSekretariat->id,
            'to_division_id' => $divSekretariat->id,
            'notes' => 'Arsip dan distribusikan edaran ini ke seluruh bidang.',
            'due_date' => Carbon::now()->format('Y-m-d'),
            'status' => 'done',
            'created_by' => $pimpinan->id,
        ]);
    }
}

