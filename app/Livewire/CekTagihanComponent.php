<?php

namespace App\Livewire;

use App\Models\Siswa;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class CekTagihanComponent extends Component
{
    public $nik;
    public $pesan = '';
    public $cek = false;
    public $success = false;
    public $punya_tagihan = true;

    public function render()
    {
        return view('livewire.cek-tagihan-component');
    }

    public function cekTagihan()
    {
        $this->validate(
            [
                'nik' => 'required'
            ],
            [
                'nik.required' => 'Mohon mengisi NIK / NISN terlebih dahulu'
            ]
        );
        $siswa = Siswa::where('nik', trim($this->nik))
            ->orWhere('nisn', trim($this->nik))
            ->first();
        if (!$siswa) {
            throw ValidationException::withMessages(['nik' => 'Maaf, Data Siswa tidak ditemukan']);
        } else {
            $this->cek = true;
            if ($siswa->telepon != '') {
                if (!$siswa->tagihan->count()) {
                    $this->pesan = 'Data tagihan siswa <span class="font-bold">' . $siswa->nama . '</span> Kelas ' . $siswa->kelas->nama . ' tidak ditemukan. Harap hubungi bagian Keuangan ' . config('custom.lembaga.' . $siswa->lembaga_id) . ' jika terdapat kesalahan.';
                    $this->punya_tagihan = false;
                } else {
                    $nomor = $siswa->telepon;
                    $rincian = '';
                    $no = 1;
                    $total = 0;
                    foreach ($siswa->tagihan as $t) {
                        if (!$t->isLunas()) {
                            $rincian .= $no . '. ' . $t->kas->nama . ' ' . $t->keterangan . ' Rp ' . number_format($t->jumlah, thousands_separator: '.') . PHP_EOL;
                            $total += $t->jumlah;
                            $no++;
                        }
                    }
                    if ($total > 0) {
                        $pesan = \App\Services\WhatsappService::prosesPesan(
                            $siswa,
                            [
                                'lembaga' => config('custom.lembaga.' . $siswa->lembaga_id),
                                'kontak.nama' => config('custom.kontak_lembaga.' . $siswa->lembaga_id . '.kontak'),
                                'tagihan.rincian' => $rincian,
                                'tagihan.total' => 'Rp ' . number_format($total, thousands_separator: '.'),
                            ],
                            'tagihan.daftar'
                        );
                        $response = \App\Services\WhatsappService::kirimWa(
                            nama: $siswa->nama,
                            nomor: $nomor,
                            pesan: $pesan,
                            sessionId: \App\Services\WhatsappService::getSessionId($siswa)
                        );
                        if ($response['status'] == 'success') {
                            $this->success = true;
                            $this->pesan = 'Tagihan siswa <span class="font-bold">' . $siswa->nama . '</span> Kelas ' . $siswa->kelas->nama . ' telah dikirimkan ke ' . substr($nomor, 0, 4) . '*****' . substr($nomor, -3);
                        } else {
                            $this->success = false;
                            $this->pesan = $response['message'];
                        }
                    } else {
                        $this->pesan = 'Semua tagihan siswa <span class="font-bold">' . $siswa->nama . '</span> Kelas ' . $siswa->kelas->nama . ' sudah LUNAS. Terima kasih';
                        $this->punya_tagihan = false;
                    }
                }
            } else {
                throw ValidationException::withMessages(['nik' => 'Maaf, Nomor WhatsApp Siswa belum diisi.  Harap hubungi Petugas di Kantor ' . config('custom.lembaga')[$siswa->lembaga_id] . ' pada jam kerja']);
            }
        }
    }

    public function resetForm()
    {
        $this->reset([
            'nik',
            'cek',
            'punya_tagihan',
            'pesan'
        ]);
    }
}
