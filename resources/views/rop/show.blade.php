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
                                <tr class="cursor-pointer hover:bg-slate-50/80 transition-colors audit-trail-row" 
                                    data-type="leadtime"
                                    data-mean="{{ number_format($rataPenjualan, 2) }}"
                                    data-leadtime="{{ $leadTime }}"
                                    data-result="{{ number_format($usageLT, 2) }}"
                                    title="Klik untuk detail perhitungan">
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
                                <tr class="cursor-pointer hover:bg-slate-50/80 transition-colors audit-trail-row" 
                                    data-type="safetystock"
                                    data-zscore="{{ $zScore }}"
                                    data-stddev="{{ number_format($standarDeviasi, 2) }}"
                                    data-sqrtlt="{{ number_format($sqrtLT, 2) }}"
                                    data-result="{{ number_format($safetyStock, 2) }}"
                                    title="Klik untuk detail perhitungan">
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
                                <tr class="bg-indigo-50/20 cursor-pointer hover:bg-indigo-50/40 transition-colors audit-trail-row" 
                                    data-type="rop"
                                    data-usagelt="{{ number_format($usageLT, 2) }}"
                                    data-safetystock="{{ number_format($safetyStock, 2) }}"
                                    data-result="{{ $rop }}"
                                    title="Klik untuk detail perhitungan">
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
                                            $diff = ($qty ?? 0) - $mean;
                                            $sqDiff = round(pow($diff, 2), 2);
                                        @endphp
                                        <div class="flex-none w-12 flex flex-col gap-1 text-center cursor-pointer hover:scale-105 transition-all duration-200 daily-data-block"
                                             data-date="{{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}"
                                             data-qty="{{ $qty ?? 0 }}"
                                             data-mean="{{ number_format($mean, 2) }}"
                                             data-diff="{{ number_format($diff, 2) }}"
                                             data-sqdiff="{{ number_format($sqDiff, 2) }}"
                                             title="Klik untuk detail harian">
                                             {{-- Penjualan --}}
                                            <div class="h-[38px]">
                                                <div class="py-2 border border-slate-300 rounded text-[11px] font-black" 
                                                     style="{{ ($qty ?? 0) > 0 ? 'background-color: #1E40AF !important; color: #ffffff !important; border-color: #1E40AF !important;' : 'background-color: #f8fafc !important; color: #64748b !important;' }}">
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
                                        <tr class="cursor-pointer hover:bg-slate-50/85 transition-colors methodology-row"
                                            data-step="total"
                                            data-sum="{{ $sum }}"
                                            data-periode="{{ $periode }}"
                                            title="Klik untuk melihat penjelasan detail">
                                            <td class="px-4 py-2 border-b border-slate-100 font-bold text-slate-700">1. Total Unit</td>
                                            <td class="px-4 py-2 border-b border-slate-100 text-slate-500 italic">Total Penjualan 30 Hari (&Sigma;x<sub>i</sub>)</td>
                                            <td class="px-4 py-2 border-b border-slate-100 text-right font-mono font-bold">{{ $sum }} Unit</td>
                                        </tr>
                                        <tr class="cursor-pointer hover:bg-slate-50/85 transition-colors methodology-row"
                                            data-step="mean"
                                            data-sum="{{ $sum }}"
                                            data-periode="{{ $periode }}"
                                            data-mean="{{ number_format($rataPenjualan, 2) }}"
                                            title="Klik untuk melihat penjelasan detail">
                                            <td class="px-4 py-2 border-b border-slate-100 font-bold text-slate-700">2. Rata-rata (d)</td>
                                            <td class="px-4 py-2 border-b border-slate-100 text-slate-500 italic">Total Unit &divide; 30 Hari</td>
                                            <td class="px-4 py-2 border-b border-slate-100 text-right font-mono font-bold text-indigo-700">{{ number_format($rataPenjualan, 2) }}</td>
                                        </tr>
                                        @php
                                            $firstDateVal = array_key_first($dailyData);
                                            $firstQtyVal = $dailyData[$firstDateVal] ?? 0;
                                            $firstDiffVal = number_format($firstQtyVal - $mean, 2);
                                            $firstSqDiffVal = number_format(pow($firstQtyVal - $mean, 2), 2);
                                            
                                            $sqDiffsArrVal = [];
                                            foreach($dailyData as $qtyVal) {
                                                $sqDiffsArrVal[] = number_format(pow(($qtyVal ?? 0) - $mean, 2), 2);
                                            }
                                            $sqDiffsListVal = implode(" + ", $sqDiffsArrVal);
                                        @endphp
                                        <tr class="cursor-pointer hover:bg-slate-50/85 transition-colors methodology-row"
                                            data-step="sqdiff"
                                            data-periode="{{ $periode }}"
                                            data-mean="{{ number_format($rataPenjualan, 2) }}"
                                            data-firstqty="{{ $firstQtyVal }}"
                                            data-firstdiff="{{ $firstDiffVal }}"
                                            data-firstsqdiff="{{ $firstSqDiffVal }}"
                                            data-sqdiffs-list="{{ $sqDiffsListVal }}"
                                            data-result="{{ number_format($sumSqDiff, 2) }}"
                                            title="Klik untuk melihat penjelasan detail">
                                            <td class="px-4 py-2 border-b border-slate-100 font-bold text-slate-700">3. Selisih Kuadrat</td>
                                            <td class="px-4 py-2 border-b border-slate-100 text-slate-500 italic">
                                                Total dari 30 kotak harian (&Sigma;(x<sub>i</sub>-d)&sup2;)<br>
                                                <div class="mt-2 p-2 bg-slate-50 rounded border border-slate-100">
                                                    <p class="text-[9px] font-bold text-slate-600 mb-1 tracking-tight underline">Contoh Hitung (Hari 1):</p>
                                                    <p class="text-[9px] font-mono text-indigo-600">
                                                        (x<sub>1</sub> - d)&sup2; = 
                                                        ({{ $firstQtyVal }} - {{ number_format($rataPenjualan, 2) }})&sup2; = 
                                                        {{ $firstSqDiffVal }}
                                                    </p>
                                                </div>
                                                <span class="text-[8px] font-mono text-slate-400 block mt-2 leading-relaxed">
                                                    ({{ $sqDiffsListVal }}) = {{ number_format($sumSqDiff, 2) }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-2 border-b border-slate-100 text-right font-mono font-bold">{{ number_format($sumSqDiff, 2) }}</td>
                                        </tr>
                                        <tr class="cursor-pointer hover:bg-slate-50/85 transition-colors methodology-row"
                                            data-step="variance"
                                            data-sumsqdiff="{{ number_format($sumSqDiff, 2) }}"
                                            data-periode="{{ $periode }}"
                                            data-nminus1="{{ $periode - 1 }}"
                                            data-result="{{ number_format($variance, 2) }}"
                                            title="Klik untuk melihat penjelasan detail">
                                            <td class="px-4 py-2 border-b border-slate-100 font-bold text-slate-700">4. Varians (s&sup2;)</td>
                                            <td class="px-4 py-2 border-b border-slate-100 text-slate-500 italic">
                                                Selisih Kuadrat &divide; (n-1)<br>
                                                <span class="text-[9px] font-mono text-slate-400">
                                                    {{ number_format($sumSqDiff, 2) }} &divide; 29 = {{ number_format($variance, 2) }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-2 border-b border-slate-100 text-right font-mono font-bold">{{ number_format($variance, 2) }}</td>
                                        </tr>
                                        <tr class="cursor-pointer hover:bg-slate-50/85 transition-colors methodology-row"
                                            data-step="stddev"
                                            data-variance="{{ number_format($variance, 2) }}"
                                            data-result="{{ number_format($standarDeviasi, 2) }}"
                                            title="Klik untuk melihat penjelasan detail">
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

@section('dashboard-modals')
<div class="modal animate-fade-in" id="calculationDetailModal" aria-hidden="true" style="display: none; background-color: rgba(15, 23, 42, 0.4); backdrop-filter: blur(4px);">
    <div class="modal-card small max-w-xl mx-auto my-8 bg-white rounded-2xl border border-slate-200 p-5 shadow-2xl transition-all" role="dialog" aria-modal="true" aria-labelledby="calcModalTitle">
        <div class="modal-head border-b border-slate-100 pb-3 flex justify-between items-center">
            <h2 id="calcModalTitle" class="text-sm font-bold text-slate-800 uppercase tracking-wider">Detail Komponen</h2>
            <button type="button" id="closeCalcModal" class="text-slate-400 hover:text-slate-600 font-bold text-2xl leading-none">&times;</button>
        </div>
        
        <div id="calcModalBody" class="py-4 space-y-4 text-left">
            <!-- Dinamis lewat JS -->
        </div>
        
        <div class="form-actions mt-2 border-t border-slate-100 pt-3 flex justify-end gap-2">
            <button class="btn btn-primary px-5 py-2 rounded-xl text-sm font-semibold" id="btnOkCalc" type="button">Tutup</button>
        </div>
    </div>
</div>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('calculationDetailModal');
        const modalTitle = document.getElementById('calcModalTitle');
        const modalBody = document.getElementById('calcModalBody');
        const closeBtn = document.getElementById('closeCalcModal');
        const okBtn = document.getElementById('btnOkCalc');

        function openModal(title, html) {
            modalTitle.textContent = title;
            modalBody.innerHTML = html;
            modal.classList.add('open');
            modal.style.display = 'flex';
        }

        function closeModal() {
            modal.classList.remove('open');
            modal.style.display = 'none';
        }

        // 1. Handling Daily Sales Click
        const dailyBlocks = document.querySelectorAll('.daily-data-block');
        dailyBlocks.forEach(block => {
            block.addEventListener('click', function() {
                const date = this.dataset.date;
                const qty = this.dataset.qty;
                const mean = this.dataset.mean;
                const diff = parseFloat(this.dataset.diff);
                const sqdiff = this.dataset.sqdiff;

                let diffText = '';
                let diffColorClass = '';
                if (diff > 0) {
                    diffText = `+${diff.toFixed(2)}`;
                    diffColorClass = 'text-emerald-600';
                } else if (diff < 0) {
                    diffText = `${diff.toFixed(2)}`;
                    diffColorClass = 'text-rose-600';
                } else {
                    diffText = `${diff.toFixed(2)}`;
                    diffColorClass = 'text-slate-600';
                }

                const html = `
                    <div class="space-y-4 text-left">
                        <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-4">
                            <p class="text-[10px] uppercase font-bold text-indigo-400">Tanggal Data</p>
                            <h3 class="text-base font-black text-indigo-950 mt-1">${date}</h3>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-3">
                            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
                                <p class="text-[10px] uppercase font-bold text-slate-400 font-semibold">Penjualan Harian (x<sub>i</sub>)</p>
                                <p class="text-2xl font-black text-slate-800 mt-1">${qty} <span class="text-xs text-slate-500 font-bold">Unit</span></p>
                            </div>
                            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
                                <p class="text-[10px] uppercase font-bold text-slate-400 font-semibold">Rata-rata Penjualan (d)</p>
                                <p class="text-2xl font-black text-slate-800 mt-1">${mean} <span class="text-xs text-slate-500 font-bold">Unit</span></p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-3">
                            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
                                <p class="text-[10px] uppercase font-bold text-slate-400 font-semibold">Selisih Penjualan (x<sub>i</sub> - d)</p>
                                <p class="text-xl font-black mt-1 ${diffColorClass}">${diffText} Unit</p>
                            </div>
                            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
                                <p class="text-[10px] uppercase font-bold text-slate-400 font-semibold">Kuadrat Selisih (x<sub>i</sub> - d)<sup>2</sup></p>
                                <p class="text-xl font-black text-indigo-600 mt-1">${sqdiff}</p>
                            </div>
                        </div>
                        
                        <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 text-xs leading-relaxed text-slate-600">
                            <p class="font-bold text-slate-700 mb-1">ℹ️ Cara Kerja Kuadrat Selisih:</p>
                            <p>
                                Nilai ini menunjukkan seberapa jauh penjualan hari ini menyimpang dari rata-rata penjualan harian. 
                                Semua selisih harian akan dikuadratkan agar bernilai positif, lalu dijumlahkan dan dibagi untuk mendapatkan 
                                <strong>Varians</strong> dan <strong>Standar Deviasi (Tingkat Fluktuasi)</strong> penjualan produk Anda.
                            </p>
                        </div>
                    </div>
                `;
                openModal("Detail Data Penjualan Harian", html);
            });
        });

        // 2. Handling Audit Trail Row Click
        const auditRows = document.querySelectorAll('.audit-trail-row');
        auditRows.forEach(row => {
            row.addEventListener('click', function() {
                const type = this.dataset.type;
                let html = '';
                let title = '';

                if (type === 'leadtime') {
                    title = "Detail Demand During Lead Time";
                    const mean = this.dataset.mean;
                    const leadtime = this.dataset.leadtime;
                    const result = this.dataset.result;
                    html = `
                        <div class="space-y-4 text-left">
                            <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-4">
                                <p class="text-[10px] uppercase font-bold text-indigo-400">Nama Komponen</p>
                                <h3 class="text-base font-black text-indigo-950 mt-1">Demand During Lead Time (Permintaan Selama Waktu Tunggu)</h3>
                            </div>
                            
                            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
                                <p class="text-[10px] uppercase font-bold text-slate-400 font-semibold">Rumus / Formula</p>
                                <p class="text-lg font-mono font-bold text-slate-800 mt-1">d &times; L</p>
                                <p class="text-xs text-slate-500 mt-1 italic">Rata-rata penjualan harian (d) dikalikan dengan Lead Time supplier (L)</p>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-3">
                                <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
                                    <p class="text-[10px] uppercase font-bold text-slate-400 font-semibold">Rata-rata Penjualan (d)</p>
                                    <p class="text-lg font-black text-slate-800 mt-1">${mean} Unit/hari</p>
                                </div>
                                <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
                                    <p class="text-[10px] uppercase font-bold text-slate-400 font-semibold">Lead Time Supplier (L)</p>
                                    <p class="text-lg font-black text-slate-800 mt-1">${leadtime} Hari</p>
                                </div>
                            </div>
                            
                            <div class="bg-[#1E40AF] text-white rounded-xl p-4 text-center">
                                <p class="text-[10px] uppercase font-bold text-indigo-200">Hasil Akhir (d &times; L)</p>
                                <p class="text-3xl font-black mt-1">${result} <span class="text-sm font-bold text-indigo-100">Unit</span></p>
                            </div>
                            
                            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 text-xs leading-relaxed text-slate-600">
                                <p class="font-bold text-slate-700 mb-1">ℹ️ Penjelasan Singkat:</p>
                                <p>
                                    Ini adalah jumlah perkiraan produk yang akan terjual selama masa tunggu sejak Anda memesan barang ke supplier hingga barang tersebut tiba di toko Anda.
                                </p>
                            </div>
                        </div>
                    `;
                } else if (type === 'safetystock') {
                    title = "Detail Safety Stock (Stok Pengaman)";
                    const zscore = this.dataset.zscore;
                    const stddev = this.dataset.stddev;
                    const sqrtlt = this.dataset.sqrtlt;
                    const result = this.dataset.result;
                    html = `
                        <div class="space-y-4 text-left">
                            <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-4">
                                <p class="text-[10px] uppercase font-bold text-indigo-400">Nama Komponen</p>
                                <h3 class="text-base font-black text-indigo-950 mt-1">Safety Stock (Stok Pengaman)</h3>
                            </div>
                            
                            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
                                <p class="text-[10px] uppercase font-bold text-slate-400 font-semibold">Rumus / Formula</p>
                                <p class="text-lg font-mono font-bold text-slate-800 mt-1">Z &times; &sigma; &times; &radic;L</p>
                                <p class="text-xs text-slate-500 mt-1 italic">Service Level (Z) &times; Standar Deviasi (&sigma;) &times; Akar Lead Time (&radic;L)</p>
                            </div>
                            
                            <div class="grid grid-cols-3 gap-2">
                                <div class="bg-slate-50 border border-slate-200 rounded-lg p-3 text-center">
                                    <p class="text-[9px] uppercase font-bold text-slate-400 leading-tight">Service Level (Z)</p>
                                    <p class="text-base font-black text-slate-800 mt-1">${zscore}</p>
                                </div>
                                <div class="bg-slate-50 border border-slate-200 rounded-lg p-3 text-center">
                                    <p class="text-[9px] uppercase font-bold text-slate-400 leading-tight">Std. Deviasi (&sigma;)</p>
                                    <p class="text-base font-black text-slate-800 mt-1">${stddev}</p>
                                </div>
                                <div class="bg-slate-50 border border-slate-200 rounded-lg p-3 text-center">
                                    <p class="text-[9px] uppercase font-bold text-slate-400 leading-tight">Akar Lead Time (&radic;L)</p>
                                    <p class="text-base font-black text-slate-800 mt-1">${sqrtlt}</p>
                                </div>
                            </div>
                            
                            <div class="bg-[#1E40AF] text-white rounded-xl p-4 text-center">
                                <p class="text-[10px] uppercase font-bold text-indigo-200">Hasil Akhir (SS)</p>
                                <p class="text-3xl font-black mt-1">${result} <span class="text-sm font-bold text-indigo-100">Unit</span></p>
                            </div>
                            
                            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 text-xs leading-relaxed text-slate-600">
                                <p class="font-bold text-slate-700 mb-1">ℹ️ Penjelasan Singkat:</p>
                                <p>
                                    Stok pengaman dihitung berdasarkan tingkat fluktuasi penjualan harian Anda (&sigma;) dan lama waktu tunggu supplier (L). 
                                    Ini berfungsi mencegah kehabisan stok (*stockout*) saat terjadi lonjakan permintaan tak terduga atau keterlambatan pengiriman dari supplier.
                                </p>
                            </div>
                        </div>
                    `;
                } else if (type === 'rop') {
                    title = "Detail Reorder Point (ROP)";
                    const usagelt = this.dataset.usagelt;
                    const safetystock = this.dataset.safetystock;
                    const result = this.dataset.result;
                    html = `
                        <div class="space-y-4 text-left">
                            <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-4">
                                <p class="text-[10px] uppercase font-bold text-indigo-400">Nama Komponen</p>
                                <h3 class="text-base font-black text-indigo-950 mt-1">Reorder Point (Titik Pemesanan Kembali)</h3>
                            </div>
                            
                            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
                                <p class="text-[10px] uppercase font-bold text-slate-400 font-semibold">Rumus / Formula</p>
                                <p class="text-lg font-mono font-bold text-slate-800 mt-1">(d &times; L) + SS</p>
                                <p class="text-xs text-slate-500 mt-1 italic">Demand During Lead Time (d &times; L) ditambah Safety Stock (SS)</p>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-3">
                                <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
                                    <p class="text-[10px] uppercase font-bold text-slate-400 font-semibold">Demand During LT (d &times; L)</p>
                                    <p class="text-lg font-black text-slate-800 mt-1">${usagelt} Unit</p>
                                </div>
                                <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
                                    <p class="text-[10px] uppercase font-bold text-slate-400 font-semibold">Safety Stock (SS)</p>
                                    <p class="text-lg font-black text-slate-800 mt-1">${safetystock} Unit</p>
                                </div>
                            </div>
                            
                            <div class="bg-[#1E40AF] text-white rounded-xl p-4 text-center">
                                <p class="text-[10px] uppercase font-bold text-indigo-200">Hasil Akhir (ROP)</p>
                                <p class="text-3xl font-black mt-1">${result} <span class="text-sm font-bold text-indigo-100">Unit</span></p>
                            </div>
                            
                            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 text-xs leading-relaxed text-slate-600">
                                <p class="font-bold text-slate-700 mb-1">ℹ️ Penjelasan Singkat:</p>
                                <p>
                                    Ini adalah level stok minimum. Ketika jumlah stok fisik di toko Anda berkurang hingga menyentuh angka ini, 
                                    <strong>Anda harus segera melakukan pemesanan ulang (restock) ke supplier</strong> agar barang baru tiba tepat sebelum stok pengaman digunakan.
                                </p>
                            </div>
                        </div>
                    `;
                }

                openModal(title, html);
            });
        });

        // 3. Handling Methodology Row Click
        const methodologyRows = document.querySelectorAll('.methodology-row');
        methodologyRows.forEach(row => {
            row.addEventListener('click', function() {
                const step = this.dataset.step;
                let html = '';
                let title = '';

                if (step === 'total') {
                    title = "Langkah 1: Total Unit Penjualan";
                    const sum = this.dataset.sum;
                    const periode = this.dataset.periode;
                    html = `
                        <div class="space-y-4 text-left">
                            <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-4">
                                <p class="text-[10px] uppercase font-bold text-indigo-400 font-semibold">Deskripsi Langkah</p>
                                <h3 class="text-base font-black text-indigo-950 mt-1">Total Unit Terjual (&Sigma;x<sub>i</sub>)</h3>
                                <p class="text-xs text-slate-500 mt-1">Menjumlahkan seluruh kuantitas penjualan harian selama periode analisis (${periode} hari).</p>
                            </div>
                            
                            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
                                <p class="text-[10px] uppercase font-bold text-slate-400 font-semibold">Persamaan / Formula</p>
                                <p class="text-lg font-mono font-bold text-slate-800 mt-1">&Sigma; x<sub>i</sub> = x<sub>1</sub> + x<sub>2</sub> + ... + x<sub>${periode}</sub></p>
                            </div>
                            
                            <div class="bg-[#1E40AF] text-white rounded-xl p-4 text-center">
                                <p class="text-[10px] uppercase font-bold text-indigo-200">Total Terjual (${periode} Hari)</p>
                                <p class="text-3xl font-black mt-1">${sum} <span class="text-sm font-bold text-indigo-100">Unit</span></p>
                            </div>
                            
                            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 text-xs leading-relaxed text-slate-600">
                                <p class="font-bold text-slate-700 mb-1">ℹ️ Tujuan:</p>
                                <p>
                                    Mengetahui volume total penjualan produk Anda dalam 30 hari terakhir. Nilai ini menjadi basis dasar untuk mencari rata-rata penjualan harian.
                                </p>
                            </div>
                        </div>
                    `;
                } else if (step === 'mean') {
                    title = "Langkah 2: Rata-rata Penjualan Harian";
                    const sum = this.dataset.sum;
                    const periode = this.dataset.periode;
                    const mean = this.dataset.mean;
                    html = `
                        <div class="space-y-4 text-left">
                            <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-4">
                                <p class="text-[10px] uppercase font-bold text-indigo-400 font-semibold">Deskripsi Langkah</p>
                                <h3 class="text-base font-black text-indigo-950 mt-1">Rata-rata Permintaan Harian (d)</h3>
                                <p class="text-xs text-slate-500 mt-1">Membagi total unit yang terjual dengan jumlah hari dalam periode analisis (${periode} hari).</p>
                            </div>
                            
                            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
                                <p class="text-[10px] uppercase font-bold text-slate-400 font-semibold">Persamaan / Formula</p>
                                <p class="text-lg font-mono font-bold text-slate-800 mt-1">d = &Sigma;x<sub>i</sub> &divide; n</p>
                                <p class="text-xs text-slate-500 mt-1 italic">Di mana: &Sigma;x<sub>i</sub> = ${sum} Unit, dan n = ${periode} Hari</p>
                            </div>
                            
                            <div class="bg-[#1E40AF] text-white rounded-xl p-4 text-center">
                                <p class="text-[10px] uppercase font-bold text-indigo-200">Hasil Rata-rata (d)</p>
                                <p class="text-3xl font-black mt-1">${mean} <span class="text-sm font-bold text-indigo-100">Unit / Hari</span></p>
                            </div>
                            
                            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 text-xs leading-relaxed text-slate-600">
                                <p class="font-bold text-slate-700 mb-1">ℹ️ Tujuan:</p>
                                <p>
                                    Mendapatkan tingkat konsumsi rata-rata produk Anda setiap harinya. Angka ini digunakan untuk memprediksi seberapa cepat persediaan stok Anda akan berkurang.
                                </p>
                            </div>
                        </div>
                    `;
                } else if (step === 'sqdiff') {
                    title = "Langkah 3: Jumlah Selisih Kuadrat";
                    const mean = this.dataset.mean;
                    const periode = this.dataset.periode;
                    const firstqty = this.dataset.firstqty;
                    const firstdiff = this.dataset.firstdiff;
                    const firstsqdiff = this.dataset.firstsqdiff;
                    const sqdiffs_list = this.dataset.sqdiffsList;
                    const result = this.dataset.result;
                    html = `
                        <div class="space-y-4 text-left">
                            <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-4">
                                <p class="text-[10px] uppercase font-bold text-indigo-400 font-semibold">Deskripsi Langkah</p>
                                <h3 class="text-base font-black text-indigo-950 mt-1">Jumlah Selisih Kuadrat (&Sigma;(x<sub>i</sub> - d)<sup>2</sup>)</h3>
                                <p class="text-xs text-slate-500 mt-1">Mengurangi penjualan tiap hari dengan rata-rata, mengkuadratkan hasilnya, lalu menjumlahkan seluruh nilai kuadrat tersebut dari Hari 1 s/d Hari 30.</p>
                            </div>
                            
                            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
                                <p class="text-[10px] uppercase font-bold text-slate-400 font-semibold">Persamaan / Formula</p>
                                <p class="text-lg font-mono font-bold text-slate-800 mt-1">&Sigma;(x<sub>i</sub> - d)<sup>2</sup></p>
                                <div class="mt-2 p-3 bg-white rounded border border-slate-100 text-xs">
                                    <p class="font-bold text-slate-600 mb-1">Contoh Hari Pertama (x<sub>1</sub> = ${firstqty}):</p>
                                    <p class="font-mono text-indigo-600">(x<sub>1</sub> - d)<sup>2</sup> = (${firstqty} - ${mean})<sup>2</sup> = (${firstdiff})<sup>2</sup> = ${firstsqdiff}</p>
                                </div>
                            </div>
                            
                            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
                                <p class="text-[10px] uppercase font-bold text-slate-400 font-semibold">Akumulasi seluruh Hari (${periode} Hari)</p>
                                <div class="max-h-24 overflow-y-auto font-mono text-[9px] text-slate-500 bg-white border border-slate-100 rounded p-2 leading-relaxed custom-scroll">
                                    (${sqdiffs_list})
                                </div>
                            </div>
                            
                            <div class="bg-[#1E40AF] text-white rounded-xl p-4 text-center">
                                <p class="text-[10px] uppercase font-bold text-indigo-200">Hasil Jumlah Selisih Kuadrat</p>
                                <p class="text-3xl font-black mt-1">${result}</p>
                            </div>
                        </div>
                    `;
                } else if (step === 'variance') {
                    title = "Langkah 4: Varians (s²)";
                    const sumsqdiff = this.dataset.sumsqdiff;
                    const periode = this.dataset.periode;
                    const nminus1 = this.dataset.nminus1;
                    const result = this.dataset.result;
                    html = `
                        <div class="space-y-4 text-left">
                            <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-4">
                                <p class="text-[10px] uppercase font-bold text-indigo-400 font-semibold">Deskripsi Langkah</p>
                                <h3 class="text-base font-black text-indigo-950 mt-1">Varians Penjualan Harian (s<sup>2</sup>)</h3>
                                <p class="text-xs text-slate-500 mt-1">Membagi jumlah selisih kuadrat dengan total hari dikurang satu (n - 1) menggunakan rumus sampel variance.</p>
                            </div>
                            
                            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
                                <p class="text-[10px] uppercase font-bold text-slate-400 font-semibold">Persamaan / Formula</p>
                                <p class="text-lg font-mono font-bold text-slate-800 mt-1">s<sup>2</sup> = &Sigma;(x<sub>i</sub> - d)<sup>2</sup> &divide; (n - 1)</p>
                                <p class="text-xs text-slate-500 mt-1 italic">Di mana: &Sigma;(x<sub>i</sub> - d)<sup>2</sup> = ${sumsqdiff}, n = ${periode} (sehingga n - 1 = ${nminus1})</p>
                            </div>
                            
                            <div class="bg-[#1E40AF] text-white rounded-xl p-4 text-center">
                                <p class="text-[10px] uppercase font-bold text-indigo-200">Hasil Varians (s<sup>2</sup>)</p>
                                <p class="text-3xl font-black mt-1">${result}</p>
                            </div>
                            
                            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 text-xs leading-relaxed text-slate-600">
                                <p class="font-bold text-slate-700 mb-1">ℹ️ Tujuan:</p>
                                <p>
                                    Mengukur penyebaran data penjualan harian di sekitar nilai rata-ratanya. Varians yang besar menunjukkan pola penjualan harian yang sangat tidak menentu/fluktuatif.
                                </p>
                            </div>
                        </div>
                    `;
                } else if (step === 'stddev') {
                    title = "Langkah 5: Standar Deviasi (σ)";
                    const variance = this.dataset.variance;
                    const result = this.dataset.result;
                    html = `
                        <div class="space-y-4 text-left">
                            <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-4">
                                <p class="text-[10px] uppercase font-bold text-indigo-400 font-semibold">Deskripsi Langkah</p>
                                <h3 class="text-base font-black text-indigo-950 mt-1">Standar Deviasi Permintaan Harian (&sigma;)</h3>
                                <p class="text-xs text-slate-500 mt-1">Menghitung akar kuadrat dari varians penjualan harian untuk mendapatkan ukuran fluktuasi dalam satuan unit produk yang sama.</p>
                            </div>
                            
                            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
                                <p class="text-[10px] uppercase font-bold text-slate-400 font-semibold">Persamaan / Formula</p>
                                <p class="text-lg font-mono font-bold text-slate-800 mt-1">&sigma; = &radic;s<sup>2</sup></p>
                                <p class="text-xs text-slate-500 mt-1 italic">Di mana: s<sup>2</sup> (Varians) = ${variance}</p>
                            </div>
                            
                            <div class="bg-[#1E40AF] text-white rounded-xl p-4 text-center">
                                <p class="text-[10px] uppercase font-bold text-indigo-200">Hasil Standar Deviasi (&sigma;)</p>
                                <p class="text-3xl font-black mt-1">${result}</p>
                            </div>
                            
                            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 text-xs leading-relaxed text-slate-600">
                                <p class="font-bold text-slate-700 mb-1">ℹ️ Interpretasi:</p>
                                <p>
                                    Nilai ini mewakili rata-rata fluktuasi penjualan harian Anda. 
                                    Semakin tinggi standar deviasi, semakin besar pula **Safety Stock (stok pengaman)** yang dibutuhkan untuk menghindari kehabisan stok akibat lonjakan permintaan mendadak.
                                </p>
                            </div>
                        </div>
                    `;
                }

                openModal(title, html);
            });
        });

        closeBtn.addEventListener('click', closeModal);
        okBtn.addEventListener('click', closeModal);

        // Close on clicking overlay
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeModal();
            }
        });
    });
</script>

@push('css')
<style>
    .custom-scroll::-webkit-scrollbar { height: 4px; }
    .custom-scroll::-webkit-scrollbar-track { background: #f1f5f9; }
    .custom-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    .custom-scroll::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>
@endpush
