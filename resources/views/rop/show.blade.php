@extends('dashboard.layouts.pemilik')

@section('dashboard-content')
    <section class="feature-section active" id="section-rop-show">
        {{-- Page Header --}}
        <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-800 tracking-tight">Detail Laporan Reorder Point</h1>
                <p class="text-[11px] text-slate-500 mt-1 uppercase font-bold tracking-widest">Produk: <span class="text-indigo-600">{{ $produk->nama }}</span></p>
            </div>
            <div>
                <a href="{{ route('rop.index') }}" class="group inline-flex items-center px-4 py-2 bg-white border border-slate-200 text-slate-600 rounded-md text-xs font-bold hover:bg-slate-50 hover:text-indigo-600 hover:border-indigo-200 transition-all duration-200 shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 transform group-hover:-translate-x-1 transition-transform duration-200" fill="none" viewbox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali ke Daftar
                </a>
            </div>
        </div>

        {{-- Top Summary Stats --}}
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-8">
            {{-- Update Terakhir --}}
            <div class="bg-white p-5 border border-slate-200 rounded shadow-sm">
                <p class="text-[10px] font-bold text-slate-400 uppercase mb-2 tracking-wider">Update Terakhir</p>
                <div class="flex items-baseline gap-1">
                    <span class="text-sm font-bold text-slate-700">{{ date('d/m/Y', strtotime($calculatedAt)) }}</span>
                    <span class="text-[10px] text-slate-400 font-mono">{{ date('H:i', strtotime($calculatedAt)) }}</span>
                </div>
            </div>

            {{-- Stok Saat Ini --}}
            <div class="bg-white p-5 border border-slate-200 rounded shadow-sm">
                <p class="text-[10px] font-bold text-slate-400 uppercase mb-2 tracking-wider">Stok Saat Ini</p>
                <div class="flex items-baseline gap-1">
                    <span class="text-2xl font-black text-slate-800">{{ $stock }}</span>
                    <span class="text-xs text-slate-500 font-bold uppercase">Unit</span>
                </div>
            </div>

            {{-- Titik ROP --}}
            <div class="bg-white p-5 border border-slate-200 rounded shadow-sm border-l-4 border-l-indigo-500">
                <p class="text-[10px] font-bold text-indigo-500 uppercase mb-2 tracking-wider">Titik ROP (Reorder Point)</p>
                <div class="flex items-baseline gap-1">
                    <span class="text-2xl font-black text-indigo-700">{{ $rop }}</span>
                    <span class="text-xs text-indigo-400 font-bold uppercase">Unit</span>
                </div>
            </div>

            {{-- Status Persediaan --}}
            <div class="bg-white p-5 border border-slate-200 rounded shadow-sm">
                <p class="text-[10px] font-bold text-slate-400 uppercase mb-2 tracking-wider">Status Persediaan</p>
                <div>
                    @if($stock <= $rop)
                        <span class="px-2 py-1 bg-red-100 text-red-700 rounded text-[10px] font-black uppercase tracking-widest border border-red-200">
                            Bahaya: Reorder
                        </span>
                    @else
                        <span class="px-2 py-1 bg-emerald-100 text-emerald-700 rounded text-[10px] font-black uppercase tracking-widest border border-emerald-200">
                            Aman
                        </span>
                    @endif
                </div>
            </div>

            {{-- Lead Time --}}
            <div class="bg-white p-5 border border-slate-200 rounded shadow-sm">
                <p class="text-[10px] font-bold text-slate-400 uppercase mb-2 tracking-wider">Lead Time Supplier</p>
                <div class="flex items-baseline gap-1">
                    <span class="text-2xl font-black text-slate-800">{{ $leadTime }}</span>
                    <span class="text-xs text-slate-500 font-bold uppercase">Hari</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Calculation Logic Section --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Calculation Audit Trail --}}
                <div class="bg-white border border-slate-200 rounded overflow-hidden mb-8 shadow-sm">
                    <div class="bg-slate-50 px-5 py-3 border-b border-slate-200 flex justify-between items-center">
                        <h2 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Rincian Perhitungan (Audit Trail)</h2>
                        <span class="text-[9px] text-slate-400 font-medium italic">Sumber: Ariyanti dkk. (2025)</span>
                    </div>
                    <div class="p-0">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50/50 border-b border-slate-100">
                                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest w-1/3">Komponen Perhitungan</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest w-1/3 text-center">Persamaan / Formula</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest w-1/3 text-right">Hasil Akhir</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                {{-- Baris 1: Demand Lead Time --}}
                                <tr>
                                    <td class="px-6 py-5">
                                        <p class="text-xs font-bold text-slate-700">Demand During Lead Time</p>
                                        <p class="text-[10px] text-slate-400 mt-1 italic">Rata-rata permintaan harian (d) &times; Lead Time (L)</p>
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        <div class="flex flex-col gap-1">
                                            <span class="text-[10px] font-mono font-bold text-indigo-400 tracking-widest">d &times; L</span>
                                            <span class="text-xs font-mono text-slate-500">{{ number_format($rataPenjualan, 2) }} &times; {{ $leadTime }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 text-right font-mono font-bold text-slate-800">
                                        {{ number_format($usageLT, 2) }}
                                    </td>
                                </tr>

                                {{-- Baris 2: Safety Stock --}}
                                <tr>
                                    <td class="px-6 py-5">
                                        <p class="text-xs font-bold text-slate-700">Safety Stock (SS)</p>
                                        <p class="text-[10px] text-slate-400 mt-1 italic">Z &times; &sigma; &times; &radic;L</p>
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        <div class="flex flex-col gap-1">
                                            <span class="text-[10px] font-mono font-bold text-indigo-400 tracking-widest">Z &times; &sigma; &times; &radic;L</span>
                                            <span class="text-xs font-mono text-slate-500">{{ $zScore }} &times; {{ number_format($standarDeviasi, 2) }} &times; {{ number_format($sqrtLT, 2) }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 text-right font-mono font-bold text-slate-800">
                                        {{ number_format($safetyStock, 2) }}
                                    </td>
                                </tr>

                                {{-- Baris 3: Final ROP --}}
                                <tr class="bg-indigo-50/20">
                                    <td class="px-6 py-5">
                                        <p class="text-xs font-black text-indigo-900 uppercase tracking-tight">Reorder Point (ROP)</p>
                                        <p class="text-[10px] text-indigo-400 mt-1 font-bold italic">(d &times; L) + SS</p>
                                    </td>
                                    <td class="px-6 py-5 text-center border-x border-indigo-100">
                                        <div class="flex flex-col gap-1">
                                            <span class="text-[10px] font-mono font-bold text-indigo-500 tracking-widest">(d &times; L) + SS</span>
                                            <span class="text-xs font-mono text-indigo-700 font-bold">{{ number_format($usageLT, 2) }} + {{ number_format($safetyStock, 2) }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 text-right">
                                        <span class="text-2xl font-black text-indigo-900">{{ $rop }}</span>
                                        <span class="text-[10px] font-bold text-indigo-400 ml-1 uppercase">Unit</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Data Evidence Section --}}
                <div class="bg-white border border-slate-200 rounded overflow-hidden">
                    <div class="bg-slate-50 px-5 py-3 border-b border-slate-200">
                        <h2 class="text-xs font-bold text-slate-700 uppercase">Data Historis Penjualan ({{ $periode }} Hari Terakhir)</h2>
                    </div>
                    <div class="p-5">
                        <div class="flex gap-2 overflow-x-auto pb-4 custom-scroll">
                            @php 
                                $sum = array_sum($dailyData); 
                                $rawMean = count($dailyData) > 0 ? $sum / count($dailyData) : 0;
                                $mean = round($rawMean, 2);
                            @endphp
                            
                            <div class="flex">
                                {{-- Kolom Label Tetap di Kiri --}}
                                <div class="flex-none w-20 flex flex-col gap-1 mr-4">
                                    <div class="h-[38px] flex items-center">
                                        <span class="text-[9px] font-black text-slate-400 uppercase leading-tight">Penjualan<br>(x<sub>i</sub>)</span>
                                    </div>
                                    <div class="h-[26px] flex items-center">
                                        <span class="text-[9px] font-black text-slate-400 uppercase leading-tight">Kuadrat<br>Selisih</span>
                                    </div>
                                    <div class="h-[14px] flex items-center">
                                        <span class="text-[9px] font-black text-slate-400 uppercase">Tanggal</span>
                                    </div>
                                </div>

                                {{-- Kolom Data (Scrollable) --}}
                                <div class="flex gap-2 pb-2">
                                    @foreach($dailyData as $date => $qty)
                                        @php 
                                            $sum += $qty;
                                            $diff = ($qty ?? 0) - $mean;
                                            $sqDiff = round(pow($diff, 2), 2);
                                        @endphp
                                        <div class="flex-none w-12 flex flex-col gap-1 text-center">
                                            {{-- Penjualan --}}
                                            <div class="h-[38px]">
                                                <div class="py-2 border-2 border-slate-300 rounded text-[11px] font-black" 
                                                     style="{{ ($qty ?? 0) > 0 ? 'background-color: #0f172a !important; color: #ffffff !important; border-color: #0f172a !important;' : 'background-color: #f8fafc !important; color: #000000 !important;' }}">
                                                    {{ $qty ?? 0 }}
                                                </div>
                                            </div>

                                            {{-- Selisih Kuadrat --}}
                                            <div class="h-[26px]">
                                                @php 
                                                    $diffText = "(" . ($qty ?? 0) . " - " . number_format($mean, 2) . ")²";
                                                @endphp
                                                <div class="py-1 border border-dashed border-slate-200 rounded text-[9px] font-mono cursor-help {{ $sqDiff > 2 ? 'bg-amber-50 text-amber-700 border-amber-200' : 'text-slate-400' }}"
                                                     title="{{ $diffText }} = {{ number_format($sqDiff, 2) }}">
                                                    {{ number_format($sqDiff, 2) }}
                                                </div>
                                            </div>

                                            {{-- Tanggal --}}
                                            <div class="h-[14px]">
                                                <span class="text-[8px] font-bold block tracking-tighter" style="color: #64748b !important;">
                                                    {{ date('d/m', strtotime($date)) }}
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        {{-- Methodology Audit Table --}}
                        <div class="mt-6 border-t border-slate-100 pt-5">
                            <h3 class="text-[10px] font-bold text-slate-500 uppercase mb-3 tracking-wider">Metodologi & Rincian Perhitungan</h3>
                            <div class="overflow-hidden border border-slate-200 rounded">
                                <table class="w-full text-left border-collapse">
                                    <thead class="bg-slate-50">
                                        <tr>
                                            <th class="px-4 py-2 text-[10px] font-bold text-slate-600 uppercase border-b border-slate-200">Langkah</th>
                                            <th class="px-4 py-2 text-[10px] font-bold text-slate-600 uppercase border-b border-slate-200">Deskripsi / Proses</th>
                                            <th class="px-4 py-2 text-[10px] font-bold text-slate-600 uppercase border-b border-slate-200 text-right">Hasil Kalkulasi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-[11px]">
                                        @php
                                            $mean = $sum / $periode;
                                            $sumSqDiff = 0;
                                            foreach($dailyData as $qty) {
                                                $sumSqDiff += pow(($qty ?? 0) - $mean, 2);
                                            }
                                            $variance = $sumSqDiff / ($periode - 1);
                                        @endphp
                                        <tr>
                                            <td class="px-4 py-2 border-b border-slate-100 font-bold text-slate-700">1. Total Unit</td>
                                            <td class="px-4 py-2 border-b border-slate-100 text-slate-500 italic">Total Penjualan 30 Hari (&Sigma;x<sub>i</sub>)</td>
                                            <td class="px-4 py-2 border-b border-slate-100 text-right font-mono font-bold">{{ $sum }} Unit</td>
                                        </tr>
                                        <tr>
                                            <td class="px-4 py-2 border-b border-slate-100 font-bold text-slate-700">2. Rata-rata (d)</td>
                                            <td class="px-4 py-2 border-b border-slate-100 text-slate-500 italic">Total Unit &divide; 30 Hari</td>
                                            <td class="px-4 py-2 border-b border-slate-100 text-right font-mono font-bold text-indigo-700">{{ number_format($rataPenjualan, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="px-4 py-2 border-b border-slate-100 font-bold text-slate-700">3. Selisih Kuadrat</td>
                                            <td class="px-4 py-2 border-b border-slate-100 text-slate-500 italic">
                                                Total dari 30 kotak harian (&Sigma;(x<sub>i</sub>-d)&sup2;)<br>
                                                <div class="mt-2 p-2 bg-slate-50 rounded border border-slate-100">
                                                    <p class="text-[9px] font-bold text-slate-600 mb-1 tracking-tight underline">Contoh Hitung (Hari 1):</p>
                                                    <p class="text-[9px] font-mono text-indigo-600">
                                                        (x<sub>1</sub> - d)&sup2; = 
                                                        ({{ $dailyData[array_key_first($dailyData)] ?? 0 }} - {{ number_format($rataPenjualan, 2) }})&sup2; = 
                                                        {{ number_format(pow(($dailyData[array_key_first($dailyData)] ?? 0) - $rataPenjualan, 2), 2) }}
                                                    </p>
                                                </div>
                                                <span class="text-[8px] font-mono text-slate-400 block mt-2 leading-relaxed">
                                                    @php 
                                                        $sqDiffs = [];
                                                        foreach($dailyData as $qty) { $sqDiffs[] = number_format(pow(($qty ?? 0) - $rataPenjualan, 2), 2); }
                                                        echo "(" . implode(" + ", $sqDiffs) . ") = " . number_format($sumSqDiff, 2);
                                                    @endphp
                                                </span>
                                            </td>
                                            <td class="px-4 py-2 border-b border-slate-100 text-right font-mono font-bold">{{ number_format($sumSqDiff, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="px-4 py-2 border-b border-slate-100 font-bold text-slate-700">4. Varians (s&sup2;)</td>
                                            <td class="px-4 py-2 border-b border-slate-100 text-slate-500 italic">
                                                Selisih Kuadrat &divide; (n-1)<br>
                                                <span class="text-[9px] font-mono text-slate-400">
                                                    {{ number_format($sumSqDiff, 2) }} &divide; 29 = {{ number_format($variance, 2) }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-2 border-b border-slate-100 text-right font-mono font-bold">{{ number_format($variance, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="px-4 py-2 border-b border-slate-100 font-bold text-slate-700 font-black">5. Standar Deviasi (&sigma;)</td>
                                            <td class="px-4 py-2 border-b border-slate-100 text-slate-500 italic">
                                                Akar Kuadrat dari Varians (&radic;s&sup2;)<br>
                                                <span class="text-[9px] font-mono text-slate-400">
                                                    &radic;{{ number_format($variance, 2) }} = {{ number_format($standarDeviasi, 2) }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-2 border-b border-slate-100 text-right font-mono font-black text-indigo-700">{{ number_format($standarDeviasi, 2) }}</td>
                                        </tr>
                                        <tr class="bg-indigo-50/30">
                                            <td class="px-4 py-2 font-bold text-indigo-900" colspan="2">Kesimpulan Analisis Variabel Demand</td>
                                            <td class="px-4 py-2 text-right font-mono font-black text-indigo-900">Valid & Terverifikasi</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Technical Parameters Section --}}
            <div class="space-y-6">
                <div class="bg-white border border-slate-200 rounded p-5">
                    <h3 class="text-xs font-bold text-slate-700 uppercase mb-4 border-b border-slate-100 pb-2">Parameter Analisis</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between">
                            <span class="text-xs text-slate-600 font-medium">Service Level</span>
                            <span class="text-xs font-bold text-slate-900">95.0%</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-xs text-slate-600 font-medium">Standard Score (Z)</span>
                            <span class="text-xs font-bold text-slate-900">1.65</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-xs text-slate-600 font-medium">Standar Deviasi (&sigma;)</span>
                            <span class="text-xs font-bold text-slate-900">{{ number_format($standarDeviasi, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-xs text-slate-600 font-medium">Periode Analisis</span>
                            <span class="text-xs font-bold text-slate-900">{{ $periode }} Hari</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('css')
<style>
    .custom-scroll::-webkit-scrollbar { height: 4px; }
    .custom-scroll::-webkit-scrollbar-track { background: #f1f5f9; }
    .custom-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    .custom-scroll::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>
@endpush
