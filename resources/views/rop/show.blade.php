@extends('dashboard.layouts.pemilik')

@section('dashboard-content')
    <section class="feature-section active" id="section-rop-show">
        {{-- Page Header --}}
        <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between border-b border-slate-200 pb-5 gap-4">
            <div>
                <h1 class="text-xl font-bold text-slate-800">Detail Laporan Reorder Point</h1>
                <p class="text-xs text-slate-500 mt-1">Produk: <span class="font-semibold text-slate-700">{{ $produk->nama }}</span></p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <div class="px-4 py-2 bg-slate-50 border border-slate-200 rounded flex flex-col items-end">
                    <p class="text-[9px] text-slate-400 uppercase font-bold tracking-widest leading-none mb-1">Terakhir Dikalkulasi</p>
                    <p class="text-[11px] font-bold text-slate-700 leading-none">{{ $calculatedAt }}</p>
                </div>
                <a href="{{ route('rop.index') }}" class="px-4 py-2 bg-white border border-slate-300 text-slate-700 rounded text-xs font-semibold hover:bg-slate-50 transition-colors shadow-sm">
                    Kembali ke Daftar
                </a>
            </div>
        </div>

        {{-- Top Summary Stats --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white p-5 border border-slate-200 rounded shadow-sm">
                <p class="text-[10px] font-bold text-slate-400 uppercase mb-2">Stok Saat Ini</p>
                <div class="flex items-baseline gap-1">
                    <span class="text-2xl font-bold text-slate-800">{{ $stock }}</span>
                    <span class="text-xs text-slate-500">Unit</span>
                </div>
            </div>
            <div class="bg-white p-5 border border-slate-200 rounded shadow-sm">
                <p class="text-[10px] font-bold text-slate-400 uppercase mb-2">Titik ROP</p>
                <div class="flex items-baseline gap-1">
                    <span class="text-2xl font-bold text-indigo-600">{{ $rop }}</span>
                    <span class="text-xs text-slate-500">Unit</span>
                </div>
            </div>
            <div class="bg-white p-5 border border-slate-200 rounded shadow-sm">
                <p class="text-[10px] font-bold text-slate-400 uppercase mb-2">Status Persediaan</p>
                <div class="mt-1">
                    @if($status === 'aman')
                        <span class="px-2 py-1 bg-emerald-100 text-emerald-700 text-[10px] font-bold rounded uppercase">Aman</span>
                    @elseif($status === 'hampir habis')
                        <span class="px-2 py-1 bg-amber-100 text-amber-700 text-[10px] font-bold rounded uppercase">Siaga</span>
                    @else
                        <span class="px-2 py-1 bg-rose-100 text-rose-700 text-[10px] font-bold rounded uppercase">Kritis</span>
                    @endif
                </div>
            </div>
            <div class="bg-white p-5 border border-slate-200 rounded shadow-sm">
                <p class="text-[10px] font-bold text-slate-400 uppercase mb-2">Lead Time</p>
                <div class="flex items-baseline gap-1">
                    <span class="text-2xl font-bold text-slate-800">{{ $leadTime }}</span>
                    <span class="text-xs text-slate-500">Hari</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Calculation Logic Section --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white border border-slate-200 rounded overflow-hidden">
                    <div class="bg-slate-50 px-5 py-3 border-b border-slate-200">
                        <h2 class="text-xs font-bold text-slate-700 uppercase">Rincian Perhitungan (Audit Trail)</h2>
                    </div>
                    <div class="p-5">
                        <table class="w-full text-xs">
                            <thead class="text-slate-400 border-b border-slate-100">
                                <tr>
                                    <th class="text-left pb-3 font-bold uppercase tracking-wider">Komponen Perhitungan</th>
                                    <th class="text-left pb-3 font-bold uppercase tracking-wider">Persamaan / Formula</th>
                                    <th class="text-right pb-3 font-bold uppercase tracking-wider">Hasil</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                <tr>
                                    <td class="py-4">
                                        <p class="font-bold text-slate-700">Demand During Lead Time</p>
                                        <p class="text-slate-400 mt-0.5">Rata-rata penjualan harian &times; waktu tunggu</p>
                                    </td>
                                    <td class="py-4 font-mono text-slate-500">
                                        {{ number_format($rataPenjualan, 2) }} &times; {{ $leadTime }}
                                    </td>
                                    <td class="py-4 text-right font-bold text-slate-800">
                                        {{ number_format($usageLT, 2) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-4">
                                        <p class="font-bold text-slate-700">Safety Stock</p>
                                        <p class="text-slate-400 mt-0.5">Service Level (95%) &times; &sigma; &times; &radic;L</p>
                                    </td>
                                    <td class="py-4 font-mono text-slate-500">
                                        {{ $zScore }} &times; {{ number_format($standarDeviasi, 2) }} &times; {{ number_format($sqrtLT, 2) }}
                                    </td>
                                    <td class="py-4 text-right font-bold text-slate-800">
                                        {{ number_format($safetyStock, 2) }}
                                    </td>
                                </tr>
                                <tr class="bg-indigo-50/30">
                                    <td class="py-5 pl-4">
                                        <p class="font-bold text-indigo-700 uppercase">Final Reorder Point (ROP)</p>
                                        <p class="text-indigo-400 mt-0.5">Demand Lead Time + Safety Stock</p>
                                    </td>
                                    <td class="py-5 font-mono text-indigo-400">
                                        {{ number_format($usageLT, 2) }} + {{ number_format($safetyStock, 2) }}
                                    </td>
                                    <td class="py-5 pr-4 text-right">
                                        <span class="text-2xl font-bold text-indigo-600">{{ $rop }}</span>
                                        <span class="text-[10px] text-indigo-400 font-bold ml-1">UNIT</span>
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
                                $sum = 0; 
                                $mean = count($dailyData) > 0 ? array_sum($dailyData) / count($dailyData) : 0;
                            @endphp
                            
                            <div class="flex">
                                {{-- Kolom Label Tetap di Kiri --}}
                                <div class="flex-none w-20 flex flex-col gap-1 mr-4">
                                    <div class="h-[38px] flex items-center">
                                        <span class="text-[9px] font-black text-slate-400 uppercase leading-tight">Penjualan<br>(x)</span>
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
                                            $sqDiff = pow($diff, 2);
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
                                            <td class="px-4 py-2 border-b border-slate-100 text-slate-500 italic">Total Penjualan 30 Hari (&Sigma;x)</td>
                                            <td class="px-4 py-2 border-b border-slate-100 text-right font-mono font-bold">{{ $sum }} Unit</td>
                                        </tr>
                                        <tr>
                                            <td class="px-4 py-2 border-b border-slate-100 font-bold text-slate-700">2. Rata-rata (&mu;)</td>
                                            <td class="px-4 py-2 border-b border-slate-100 text-slate-500 italic">Total Unit &divide; 30 Hari</td>
                                            <td class="px-4 py-2 border-b border-slate-100 text-right font-mono font-bold text-indigo-700">{{ number_format($mean, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="px-4 py-2 border-b border-slate-100 font-bold text-slate-700">3. Selisih Kuadrat</td>
                                            <td class="px-4 py-2 border-b border-slate-100 text-slate-500 italic">
                                                Total dari 30 kotak harian (&Sigma;(x-&mu;)&sup2;)<br>
                                                <span class="text-[8px] font-mono text-slate-400 block mt-1 leading-relaxed">
                                                    @php 
                                                        $sqDiffs = [];
                                                        foreach($dailyData as $qty) { $sqDiffs[] = number_format(pow(($qty ?? 0) - $mean, 2), 2); }
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
                                                    &radic;{{ number_format($variance, 2) }} = {{ number_format(sqrt($variance), 2) }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-2 border-b border-slate-100 text-right font-mono font-black text-indigo-700">{{ number_format(sqrt($variance), 2) }}</td>
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
