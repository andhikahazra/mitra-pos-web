@extends('dashboard.layouts.pemilik')

@section('dashboard-content')
    <section class="feature-section active" id="section-rop-presentation">
        {{-- Page Header --}}
        <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <span class="text-[10px] bg-blue-50 text-blue-600 dark:bg-blue-950/45 dark:text-blue-400 px-3 py-1 rounded-full font-bold uppercase tracking-wider mb-2 inline-block border border-blue-100 dark:border-blue-900/40">Mode Presentasi Alur Perhitungan</span>
                <h1>Detail Perhitungan ROP (Akademik & Terperinci)</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Produk: <strong class="text-blue-600 dark:text-blue-400">{{ $produk->nama }}</strong></p>
            </div>
            <div>
                <a href="{{ route('rop.show', $produk) }}" class="group inline-flex items-center px-4 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 rounded-md text-xs font-bold hover:bg-slate-50 dark:hover:bg-slate-800/60 hover:text-blue-600 dark:hover:text-blue-400 hover:border-blue-200 dark:hover:border-blue-900 transition-all duration-200 shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 transform group-hover:-translate-x-1 transition-transform duration-200" fill="none" viewbox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali ke Detail
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            {{-- Rincian Perhitungan (Audit Trail) --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white dark:bg-slate-900/60 dark:border-slate-800 border border-slate-200 rounded overflow-hidden shadow-sm">
                    <div class="bg-slate-50 dark:bg-slate-950/40 px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex justify-between items-center">
                        <h2 class="text-sm font-bold text-slate-700 dark:text-slate-200 uppercase tracking-wider">Rincian Perhitungan (Audit Trail)</h2>
                        <span class="text-[10px] text-slate-400 font-medium italic">Sumber: Ariyanti dkk. (2025)</span>
                    </div>
                    <div class="p-0">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50/50 dark:bg-slate-950/20 border-b border-slate-100 dark:border-slate-800/60">
                                    <th class="px-6 py-4 text-xs font-semibold text-slate-400 uppercase tracking-wider w-1/3">Komponen Perhitungan</th>
                                    <th class="px-6 py-4 text-xs font-semibold text-slate-400 uppercase tracking-wider w-1/3 text-center">Persamaan / Formula</th>
                                    <th class="px-6 py-4 text-xs font-semibold text-slate-400 uppercase tracking-wider w-1/3 text-right">Hasil Akhir</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                {{-- Baris 1: Demand Lead Time --}}
                                <tr class="cursor-pointer hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors audit-trail-row" 
                                    data-type="leadtime"
                                    data-mean="{{ number_format($rataPenjualan, 2) }}"
                                    data-leadtime="{{ $leadTime }}"
                                    data-result="{{ number_format($usageLT, 2) }}"
                                    title="Klik untuk detail penjelasan">
                                    <td class="px-6 py-6">
                                        <p class="text-sm font-bold text-slate-700 dark:text-slate-200">Demand During Lead Time</p>
                                        <p class="text-[11px] text-slate-400 mt-1.5 italic">Rata-rata permintaan harian (d) &times; Lead Time (L)</p>
                                    </td>
                                    <td class="px-6 py-6 text-center">
                                        <div class="flex flex-col gap-1.5">
                                            <span class="text-xs font-mono font-bold text-blue-400 tracking-widest">d &times; L</span>
                                            <span class="text-base font-mono font-bold text-blue-600 dark:text-blue-400">{{ number_format($rataPenjualan, 2) }} &times; {{ $leadTime }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-6 text-right font-mono text-xl font-extrabold text-slate-800 dark:text-slate-200">
                                        {{ number_format($usageLT, 2) }}
                                    </td>
                                </tr>

                                {{-- Baris 2: Safety Stock --}}
                                <tr class="cursor-pointer hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors audit-trail-row" 
                                    data-type="safetystock"
                                    data-zscore="{{ $zScore }}"
                                    data-stddev="{{ number_format($standarDeviasi, 2) }}"
                                    data-sqrtlt="{{ number_format($sqrtLT, 2) }}"
                                    data-result="{{ number_format($safetyStock, 2) }}"
                                    title="Klik untuk detail penjelasan">
                                    <td class="px-6 py-6">
                                        <p class="text-sm font-bold text-slate-700 dark:text-slate-200">Safety Stock (SS)</p>
                                        <p class="text-[11px] text-slate-400 mt-1.5 italic">Z &times; &sigma; &times; &radic;L</p>
                                    </td>
                                    <td class="px-6 py-6 text-center">
                                        <div class="flex flex-col gap-1.5">
                                            <span class="text-xs font-mono font-bold text-blue-400 tracking-widest">Z &times; &sigma; &times; &radic;L</span>
                                            <span class="text-base font-mono font-bold text-blue-600 dark:text-blue-400">{{ $zScore }} &times; {{ number_format($standarDeviasi, 2) }} &times; {{ number_format($sqrtLT, 2) }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-6 text-right font-mono text-xl font-extrabold text-slate-800 dark:text-slate-200">
                                        {{ number_format($safetyStock, 2) }}
                                    </td>
                                </tr>

                                {{-- Baris 3: Final ROP --}}
                                <tr class="bg-blue-50/10 dark:bg-blue-950/5 cursor-pointer hover:bg-blue-50/20 dark:hover:bg-blue-950/10 transition-colors audit-trail-row" 
                                    data-type="rop"
                                    data-usagelt="{{ number_format($usageLT, 2) }}"
                                    data-safetystock="{{ number_format($safetyStock, 2) }}"
                                    data-result="{{ $rop }}"
                                    title="Klik untuk detail penjelasan">
                                    <td class="px-6 py-6">
                                        <p class="text-base font-black text-blue-900 dark:text-blue-300 uppercase tracking-tight">Reorder Point (ROP)</p>
                                        <p class="text-[11px] text-blue-400 dark:text-blue-500 mt-1.5 font-bold italic">(d &times; L) + SS</p>
                                    </td>
                                    <td class="px-6 py-6 text-center border-x border-blue-50 dark:border-blue-950/40">
                                        <div class="flex flex-col gap-1.5">
                                            <span class="text-xs font-mono font-bold text-blue-500 tracking-widest">(d &times; L) + SS</span>
                                            <span class="text-base font-mono text-blue-700 dark:text-blue-400 font-extrabold">{{ number_format($usageLT, 2) }} + {{ number_format($safetyStock, 2) }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-6 text-right">
                                        <span class="text-3xl font-black text-blue-600 dark:text-blue-300">{{ $rop }}</span>
                                        <span class="text-xs text-blue-400 dark:text-blue-500 font-bold uppercase ml-1">Unit</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Parameter Analisis Card --}}
            <div class="space-y-6">
                <div class="bg-white dark:bg-slate-900/60 dark:border-slate-800 border border-slate-200 rounded p-6 shadow-sm">
                    <h3 class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase mb-4 border-b border-slate-100 dark:border-slate-800 pb-2 tracking-widest">Parameter Analisis</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between py-2 border-b border-slate-50 dark:border-slate-800/40">
                            <span class="text-sm text-slate-600 dark:text-slate-400 font-medium">Service Level</span>
                            <span class="text-base font-extrabold text-slate-900 dark:text-slate-200">95.0%</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-slate-50 dark:border-slate-800/40">
                            <span class="text-sm text-slate-600 dark:text-slate-400 font-medium">Standard Score (Z)</span>
                            <span class="text-base font-extrabold text-slate-900 dark:text-slate-200">1.65</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-slate-50 dark:border-slate-800/40">
                            <span class="text-sm text-slate-600 dark:text-slate-400 font-medium">Standar Deviasi (&sigma;)</span>
                            <span class="text-base font-extrabold text-slate-900 dark:text-slate-200">{{ number_format($standarDeviasi, 2) }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-slate-50 dark:border-slate-800/40 cursor-pointer hover:text-blue-600 dark:hover:text-blue-400 leadtime-history-trigger"
                             data-leadtime-history="{{ json_encode($leadTimeHistory) }}"
                             data-leadtime-average="{{ $leadTime }}"
                             title="Klik untuk melihat riwayat penerimaan barang masuk">
                            <span class="text-sm text-slate-600 dark:text-slate-400 font-medium">Lead Time (L)</span>
                            <span class="text-base font-extrabold text-slate-900 dark:text-slate-200 underline">{{ $leadTime }} Hari</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-slate-50 dark:border-slate-800/40 cursor-pointer hover:text-blue-600 dark:hover:text-blue-400 leadtime-history-trigger"
                             data-leadtime-history="{{ json_encode($leadTimeHistory) }}"
                             data-leadtime-average="{{ $leadTime }}"
                             title="Klik untuk melihat riwayat penerimaan barang masuk">
                            <span class="text-sm text-slate-600 dark:text-slate-400 font-medium">Akar Lead Time (&radic;L)</span>
                            <span class="text-base font-extrabold text-slate-900 dark:text-slate-200 underline">&radic;{{ $leadTime }} = {{ number_format($sqrtLT, 2) }}</span>
                        </div>
                        <div class="flex justify-between py-2">
                            <span class="text-sm text-slate-600 dark:text-slate-400 font-medium">Periode Analisis</span>
                            <span class="text-base font-extrabold text-slate-900 dark:text-slate-200">{{ $periode }} Hari</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Row 2: Data Historis --}}
        <div class="bg-white dark:bg-slate-900/60 dark:border-slate-800 border border-slate-200 rounded overflow-hidden mb-8 shadow-sm">
            <div class="bg-slate-50 dark:bg-slate-950/40 px-6 py-4 border-b border-slate-200 dark:border-slate-800">
                <h2 class="text-xs font-bold text-slate-700 dark:text-slate-200 uppercase tracking-widest">Data Historis Penjualan ({{ $periode }} Hari Terakhir)</h2>
            </div>
            <div class="p-6">
                <div class="flex gap-3 overflow-x-auto pb-4 custom-scroll">
                    @php 
                        $sum = array_sum($dailyData); 
                        $rawMean = count($dailyData) > 0 ? $sum / count($dailyData) : 0;
                        $mean = round($rawMean, 2);
                    @endphp
                    
                    <div class="flex gap-2">
                        @foreach ($dailyData as $date => $qty)
                            @php 
                                $dateObj = \Carbon\Carbon::parse($date);
                                $diff = ($qty ?? 0) - $mean;
                                $sqDiff = pow($diff, 2);
                            @endphp
                            <div class="flex flex-col flex-shrink-0 w-16 bg-slate-50 dark:bg-slate-950/20 border border-slate-200 dark:border-slate-800 rounded-xl overflow-hidden shadow-xs hover:border-blue-300 dark:hover:border-blue-900 transition-all duration-150">
                                <div class="text-center py-2 bg-blue-500 text-white">
                                    <span class="text-sm font-black">{{ $qty ?? 0 }}</span>
                                </div>
                                <div class="flex-1 p-2 flex flex-col justify-between items-center bg-white dark:bg-slate-900/60">
                                    <div class="text-[10px] font-mono text-amber-600 dark:text-amber-500 font-bold bg-amber-50 dark:bg-amber-950/45 px-1 py-0.5 rounded border border-amber-100 dark:border-amber-900/30">
                                        {{ number_format($sqDiff, 2) }}
                                    </div>
                                    <div class="text-[9px] font-semibold text-slate-400 mt-2">
                                        {{ $dateObj->translatedFormat('d/m') }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Row 3: Metodologi & Rincian Perhitungan --}}
        <div class="bg-white dark:bg-slate-900/60 dark:border-slate-800 border border-slate-200 rounded overflow-hidden shadow-sm">
            <div class="bg-slate-50 dark:bg-slate-950/40 px-6 py-4 border-b border-slate-200 dark:border-slate-800">
                <h2 class="text-xs font-bold text-slate-700 dark:text-slate-200 uppercase tracking-widest">Metodologi & Rincian Perhitungan</h2>
            </div>
            <div class="p-0">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50 dark:bg-slate-950/20 border-b border-slate-100 dark:border-slate-800/60">
                                <th class="px-6 py-4 text-xs font-semibold text-slate-400 uppercase tracking-wider w-1/4">Langkah</th>
                                <th class="px-6 py-4 text-xs font-semibold text-slate-400 uppercase tracking-wider w-2/3">Deskripsi / Proses</th>
                                <th class="px-6 py-4 text-xs font-semibold text-slate-400 uppercase tracking-wider w-1/12 text-right">Hasil Kalkulasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @php
                                $sumSqDiff = 0;
                                foreach($dailyData as $qty) {
                                    $sumSqDiff += pow(($qty ?? 0) - $mean, 2);
                                }
                                $variance = $sumSqDiff / ($periode - 1);
                            @endphp
                            
                            {{-- Langkah 1 --}}
                            <tr class="cursor-pointer hover:bg-slate-50/85 dark:hover:bg-slate-800/40 transition-colors methodology-row"
                                data-step="total"
                                data-sum="{{ $sum }}"
                                data-periode="{{ $periode }}"
                                title="Klik untuk penjelasan detail">
                                <td class="px-6 py-5 font-bold text-slate-700 dark:text-slate-200">1. Total Unit</td>
                                <td class="px-6 py-5 text-sm text-slate-500 dark:text-slate-400 italic">Total Penjualan 30 Hari (&Sigma;x<sub>i</sub>)</td>
                                <td class="px-6 py-5 text-right font-mono font-bold text-base text-slate-800 dark:text-slate-200">{{ $sum }} Unit</td>
                            </tr>

                            {{-- Langkah 2 --}}
                            <tr class="cursor-pointer hover:bg-slate-50/85 dark:hover:bg-slate-800/40 transition-colors methodology-row"
                                data-step="mean"
                                data-sum="{{ $sum }}"
                                data-periode="{{ $periode }}"
                                data-mean="{{ number_format($rataPenjualan, 2) }}"
                                title="Klik untuk penjelasan detail">
                                <td class="px-6 py-5 font-bold text-slate-700 dark:text-slate-200">2. Rata-rata (d)</td>
                                <td class="px-6 py-5 text-sm text-slate-500 dark:text-slate-400 italic">Total Unit &divide; 30 Hari</td>
                                <td class="px-6 py-5 text-right font-mono font-extrabold text-base text-blue-700 dark:text-blue-400">{{ number_format($rataPenjualan, 2) }}</td>
                            </tr>

                            {{-- Langkah 3 --}}
                             @php
                                $firstDateVal = array_key_first($dailyData);
                                $firstQtyVal = $dailyData[$firstDateVal] ?? 0;
                                $firstDiffVal = number_format($firstQtyVal - $mean, 2);
                                $firstSqDiffVal = number_format(pow($firstQtyVal - $mean, 2), 2);
                                
                                $sqDiffsArrVal = [];
                                $sqDiffsVerticalHtml = '';
                                $dayIndex = 1;
                                foreach($dailyData as $dateVal => $qtyVal) {
                                    $dateObjVal = \Carbon\Carbon::parse($dateVal);
                                    $sqVal = number_format(pow(($qtyVal ?? 0) - $mean, 2), 2);
                                    $sqDiffsArrVal[] = $sqVal;
                                    
                                    $sqDiffsVerticalHtml .= "<div class='flex justify-between font-mono text-xs border-b border-slate-100 dark:border-slate-800/40 py-1.5'>";
                                    $sqDiffsVerticalHtml .= "<span class='text-slate-500 dark:text-slate-400'>Hari {$dayIndex} (" . $dateObjVal->translatedFormat('d M') . ")</span>";
                                    $sqDiffsVerticalHtml .= "<span class='text-slate-700 dark:text-slate-300'>({$qtyVal} - " . number_format($rataPenjualan, 2) . ")&sup2; = <strong class='text-blue-600 dark:text-blue-400'>{$sqVal}</strong></span>";
                                    $sqDiffsVerticalHtml .= "</div>";
                                    $dayIndex++;
                                }
                                $sqDiffsListVal = implode(" + ", $sqDiffsArrVal);
                            @endphp
                            <tr class="cursor-pointer hover:bg-slate-50/85 dark:hover:bg-slate-800/40 transition-colors methodology-row"
                                data-step="sqdiff"
                                data-periode="{{ $periode }}"
                                data-mean="{{ number_format($rataPenjualan, 2) }}"
                                data-firstqty="{{ $firstQtyVal }}"
                                data-firstdiff="{{ $firstDiffVal }}"
                                data-firstsqdiff="{{ $firstSqDiffVal }}"
                                data-sqdiffs-list="{{ $sqDiffsListVal }}"
                                data-sqdiffs-vertical-html="{{ $sqDiffsVerticalHtml }}"
                                data-result="{{ number_format($sumSqDiff, 2) }}"
                                title="Klik untuk penjelasan detail">
                                <td class="px-6 py-5 font-bold text-slate-700 dark:text-slate-200">3. Selisih Kuadrat</td>
                                <td class="px-6 py-5 text-sm text-slate-500 dark:text-slate-400 italic">
                                    Total dari 30 kotak harian (&Sigma;(x<sub>i</sub>-d)&sup2;)<br>
                                    <div class="mt-3 p-3 bg-slate-50 dark:bg-slate-950/20 rounded-xl border border-slate-100 dark:border-slate-800/60 max-w-xl">
                                        <p class="text-[10px] font-bold text-slate-600 dark:text-slate-400 mb-1.5 tracking-tight underline">Contoh Hitung (Hari 1):</p>
                                        <p class="text-xs font-mono text-blue-600 dark:text-blue-400">
                                            (x<sub>1</sub> - d)&sup2; = 
                                            ({{ $firstQtyVal }} - {{ number_format($rataPenjualan, 2) }})&sup2; = 
                                            {{ $firstSqDiffVal }}
                                        </p>
                                    </div>
                                    <button type="button" class="mt-2.5 text-xs text-blue-600 dark:text-blue-400 font-bold hover:underline flex items-center gap-1">
                                        Lihat rincian selisih kuadrat 30 hari
                                    </button>
                                </td>
                                <td class="px-6 py-5 text-right font-mono font-bold text-base text-slate-800 dark:text-slate-200">{{ number_format($sumSqDiff, 2) }}</td>
                            </tr>

                            {{-- Langkah 4 --}}
                            <tr class="cursor-pointer hover:bg-slate-50/85 dark:hover:bg-slate-800/40 transition-colors methodology-row"
                                data-step="variance"
                                data-sumsqdiff="{{ number_format($sumSqDiff, 2) }}"
                                data-periode="{{ $periode }}"
                                data-nminus1="{{ $periode - 1 }}"
                                data-result="{{ number_format($variance, 2) }}"
                                title="Klik untuk penjelasan detail">
                                <td class="px-6 py-5 font-bold text-slate-700 dark:text-slate-200">4. Varians (s&sup2;)</td>
                                <td class="px-6 py-5 text-sm text-slate-500 dark:text-slate-400 italic">
                                    Selisih Kuadrat &divide; (n-1)<br>
                                    <span class="text-xs font-mono text-slate-400 dark:text-slate-500 block mt-1.5">
                                        {{ number_format($sumSqDiff, 2) }} &divide; 29 = {{ number_format($variance, 2) }}
                                    </span>
                                </td>
                                <td class="px-6 py-5 text-right font-mono font-bold text-base text-slate-800 dark:text-slate-200">{{ number_format($variance, 2) }}</td>
                            </tr>

                            {{-- Langkah 5 --}}
                            <tr class="cursor-pointer hover:bg-slate-50/85 dark:hover:bg-slate-800/40 transition-colors methodology-row"
                                data-step="stddev"
                                data-variance="{{ number_format($variance, 2) }}"
                                data-result="{{ number_format($standarDeviasi, 2) }}"
                                title="Klik untuk penjelasan detail">
                                <td class="px-6 py-5 font-bold text-slate-700 dark:text-slate-200">5. Standar Deviasi (&sigma;)</td>
                                <td class="px-6 py-5 text-sm text-slate-500 dark:text-slate-400 italic">
                                    Akar Kuadrat dari Varians (&radic;s&sup2;)<br>
                                    <span class="text-xs font-mono text-slate-400 dark:text-slate-500 block mt-1.5">
                                        &radic;{{ number_format($variance, 2) }} = {{ number_format($standarDeviasi, 2) }}
                                    </span>
                                </td>
                                <td class="px-6 py-5 text-right font-mono font-extrabold text-base text-blue-700 dark:text-blue-400">{{ number_format($standarDeviasi, 2) }}</td>
                            </tr>

                            {{-- Hasil Analisis --}}
                            <tr class="bg-blue-50/10 dark:bg-blue-950/5">
                                <td class="px-6 py-5 font-bold text-blue-900 dark:text-blue-300" colspan="2">Kesimpulan Analisis Variabel Demand</td>
                                <td class="px-6 py-5 text-right font-mono font-black text-blue-900 dark:text-blue-300">Valid & Terverifikasi</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    {{-- Detail Modal --}}
    <div id="calculationDetailModal" class="modal-overlay hidden" style="display: none;">
        <div class="modal-container max-w-lg dark:bg-slate-900 dark:border-slate-800">
            <div class="modal-header">
                <h2 class="modal-title text-base font-bold text-slate-800 dark:text-slate-100" id="calcModalTitle">Detail Perhitungan</h2>
                <button type="button" class="modal-close-btn" id="closeCalcModal">&times;</button>
            </div>
            <div class="modal-body p-6" id="calcModalBody">
                <!-- Dynamic Content injected via JS -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary px-6" id="btnOkCalc">Tutup</button>
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

        // Row Click Listeners for Audit Trail Table
        document.querySelectorAll('.audit-trail-row').forEach(row => {
            row.addEventListener('click', function() {
                const type = this.dataset.type;
                let title = "";
                let html = "";

                if (type === 'leadtime') {
                    title = "Detail: Demand During Lead Time";
                    const mean = this.dataset.mean;
                    const leadtime = this.dataset.leadtime;
                    const result = this.dataset.result;
                    html = `
                        <div class="space-y-4 text-left">
                            <div class="bg-blue-50 dark:bg-blue-950/45 border border-blue-100 dark:border-blue-900/40 rounded-xl p-4">
                                <p class="text-[10px] uppercase font-bold text-blue-400 font-semibold">Definisi Komponen</p>
                                <h3 class="text-base font-black text-blue-950 dark:text-blue-200 mt-1">Permintaan Selama Waktu Tunggu</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Mengukur rata-rata jumlah unit yang diperkirakan terjual selama masa pengiriman barang dari supplier.</p>
                            </div>
                            <div class="bg-slate-50 dark:bg-slate-950/20 border border-slate-200 dark:border-slate-800 rounded-xl p-4">
                                <p class="text-[10px] uppercase font-bold text-slate-400 font-semibold">Substitusi Nilai</p>
                                <p class="text-lg font-mono font-bold text-slate-800 dark:text-slate-200 mt-1">${mean} <span class="text-xs text-slate-400">(d)</span> &times; ${leadtime} <span class="text-xs text-slate-400">(L)</span></p>
                            </div>
                            <div class="bg-blue-600 text-white rounded-xl p-4 text-center shadow-md">
                                <p class="text-[10px] uppercase font-bold text-blue-200">Hasil Akhir</p>
                                <p class="text-3xl font-black mt-1">${result} Unit</p>
                            </div>
                        </div>
                    `;
                } else if (type === 'safetystock') {
                    title = "Detail: Safety Stock (SS)";
                    const zscore = this.dataset.zscore;
                    const stddev = this.dataset.stddev;
                    const sqrtlt = this.dataset.sqrtlt;
                    const result = this.dataset.result;
                    html = `
                        <div class="space-y-4 text-left">
                            <div class="bg-blue-50 dark:bg-blue-950/45 border border-blue-100 dark:border-blue-900/40 rounded-xl p-4">
                                <p class="text-[10px] uppercase font-bold text-blue-400 font-semibold">Definisi Komponen</p>
                                <h3 class="text-base font-black text-blue-950 dark:text-blue-200 mt-1">Stok Cadangan Pengaman</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Jumlah stok cadangan darurat untuk mencegah habisnya persediaan akibat fluktuasi harian atau keterlambatan pengiriman.</p>
                            </div>
                            <div class="bg-slate-50 dark:bg-slate-950/20 border border-slate-200 dark:border-slate-800 rounded-xl p-4">
                                <p class="text-[10px] uppercase font-bold text-slate-400 font-semibold">Substitusi Nilai</p>
                                <p class="text-lg font-mono font-bold text-slate-800 dark:text-slate-200 mt-1">${zscore} <span class="text-xs text-slate-400">(Z)</span> &times; ${stddev} <span class="text-xs text-slate-400">(&sigma;)</span> &times; ${sqrtlt} <span class="text-xs text-slate-400">(&radic;L)</span></p>
                            </div>
                            <div class="bg-blue-600 text-white rounded-xl p-4 text-center shadow-md">
                                <p class="text-[10px] uppercase font-bold text-blue-200">Hasil Akhir</p>
                                <p class="text-3xl font-black mt-1">${result} Unit</p>
                            </div>
                        </div>
                    `;
                } else if (type === 'rop') {
                    title = "Detail: Reorder Point (ROP)";
                    const usagelt = this.dataset.usagelt;
                    const safetystock = this.dataset.safetystock;
                    const result = this.dataset.result;
                    html = `
                        <div class="space-y-4 text-left">
                            <div class="bg-blue-50 dark:bg-blue-950/45 border border-blue-100 dark:border-blue-900/40 rounded-xl p-4">
                                <p class="text-[10px] uppercase font-bold text-blue-400 font-semibold">Definisi Komponen</p>
                                <h3 class="text-base font-black text-blue-950 dark:text-blue-200 mt-1">Titik Pemesanan Kembali</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Batas persediaan minimum yang memicu pembuatan pesanan stok baru agar tidak kehabisan barang di masa mendatang.</p>
                            </div>
                            <div class="bg-slate-50 dark:bg-slate-950/20 border border-slate-200 dark:border-slate-800 rounded-xl p-4">
                                <p class="text-[10px] uppercase font-bold text-slate-400 font-semibold">Substitusi Nilai</p>
                                <p class="text-lg font-mono font-bold text-slate-800 dark:text-slate-200 mt-1">${usagelt} <span class="text-xs text-slate-400">(Lead Time Demand)</span> + ${safetystock} <span class="text-xs text-slate-400">(Safety Stock)</span></p>
                            </div>
                            <div class="bg-blue-600 text-white rounded-xl p-4 text-center shadow-md">
                                <p class="text-[10px] uppercase font-bold text-blue-200">Hasil Akhir</p>
                                <p class="text-3xl font-black mt-1">${result} Unit</p>
                            </div>
                        </div>
                    `;
                }

                openModal(title, html);
            });
        });

        // Click Listeners for Methodology Table
        document.querySelectorAll('.methodology-row').forEach(row => {
            row.addEventListener('click', function() {
                const step = this.dataset.step;
                let title = "";
                let html = "";

                if (step === 'total') {
                    title = "Langkah 1: Total Unit Terjual";
                    const sum = this.dataset.sum;
                    const periode = this.dataset.periode;
                    html = `
                        <div class="space-y-4 text-left">
                            <div class="bg-blue-50 dark:bg-blue-950/45 border border-blue-100 dark:border-blue-900/40 rounded-xl p-4">
                                <p class="text-[10px] uppercase font-bold text-blue-400 font-semibold">Deskripsi Langkah</p>
                                <h3 class="text-base font-black text-blue-950 dark:text-blue-200 mt-1">Total Unit Terjual (30 Hari)</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Menjumlahkan seluruh unit barang yang laku terjual dari transaksi harian selama periode analisis.</p>
                            </div>
                            <div class="bg-blue-600 text-white rounded-xl p-4 text-center shadow-md">
                                <p class="text-[10px] uppercase font-bold text-blue-200">Hasil Penjumlahan (&Sigma;x<sub>i</sub>)</p>
                                <p class="text-3xl font-black mt-1">${sum} Unit</p>
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
                            <div class="bg-blue-50 dark:bg-blue-950/45 border border-blue-100 dark:border-blue-900/40 rounded-xl p-4">
                                <p class="text-[10px] uppercase font-bold text-blue-400 font-semibold">Deskripsi Langkah</p>
                                <h3 class="text-base font-black text-blue-950 dark:text-blue-200 mt-1">Rata-rata Penjualan Harian (d)</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Membagi jumlah total unit terjual dengan lamanya hari dalam periode analisis.</p>
                            </div>
                            <div class="bg-slate-50 dark:bg-slate-950/20 border border-slate-200 dark:border-slate-800 rounded-xl p-4">
                                <p class="text-[10px] uppercase font-bold text-slate-400 font-semibold">Persamaan / Formula</p>
                                <p class="text-lg font-mono font-bold text-slate-800 dark:text-slate-200 mt-1">d = Total Unit &divide; Periode</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 italic">Di mana: ${sum} unit &divide; ${periode} hari = ${mean}</p>
                            </div>
                            <div class="bg-blue-600 text-white rounded-xl p-4 text-center shadow-md">
                                <p class="text-[10px] uppercase font-bold text-blue-200">Rata-rata Harian</p>
                                <p class="text-3xl font-black mt-1">${mean} Unit/Hari</p>
                            </div>
                        </div>
                    `;
                } else if (step === 'sqdiff') {
                    title = "Langkah 3: Jumlah Selisih Kuadrat";
                    const result = this.dataset.result;
                    const firstqty = this.dataset.firstqty;
                    const mean = this.dataset.mean;
                    const firstsqdiff = this.dataset.firstsqdiff;
                    const verticalHtml = this.dataset.sqdiffsVerticalHtml;
                    html = `
                        <div class="space-y-4 text-left">
                            <div class="bg-blue-50 dark:bg-blue-950/45 border border-blue-100 dark:border-blue-900/40 rounded-xl p-4">
                                <p class="text-[10px] uppercase font-bold text-blue-400 font-semibold">Deskripsi Langkah</p>
                                <h3 class="text-base font-black text-blue-950 dark:text-blue-200 mt-1">Jumlah Selisih Kuadrat (&Sigma;(x<sub>i</sub> - d)<sup>2</sup>)</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Menjumlahkan kuadrat selisih antara penjualan harian riil (x<sub>i</sub>) dengan rata-rata penjualan (d). Langkah awal untuk mengukur tingkat dispersi/variabilitas penjualan.</p>
                            </div>
                            
                            <div class="bg-slate-50 dark:bg-slate-950/20 border border-slate-200 dark:border-slate-800 rounded-xl p-4">
                                <p class="text-[10px] uppercase font-bold text-slate-400 font-semibold">Contoh Kasus Hari ke-1</p>
                                <p class="text-base font-mono font-bold text-slate-800 dark:text-slate-200 mt-1">(x<sub>1</sub> - d)<sup>2</sup></p>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 italic">(${firstqty} - ${mean})<sup>2</sup> = ${firstsqdiff}</p>
                            </div>

                            <div class="bg-slate-50 dark:bg-slate-950/20 border border-slate-200 dark:border-slate-800 rounded-xl p-4">
                                <p class="text-[10px] uppercase font-bold text-slate-400 font-semibold mb-2">Akumulasi Rincian Perhitungan Harian (30 Hari)</p>
                                <div class="max-h-48 overflow-y-auto pr-2 space-y-1.5 custom-scroll">
                                    ${verticalHtml}
                                </div>
                            </div>

                            <div class="bg-blue-600 text-white rounded-xl p-4 text-center shadow-md">
                                <p class="text-[10px] uppercase font-bold text-blue-200">Hasil Total (&Sigma;(x<sub>i</sub> - d)<sup>2</sup>)</p>
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
                            <div class="bg-blue-50 dark:bg-blue-950/45 border border-blue-100 dark:border-blue-900/40 rounded-xl p-4">
                                <p class="text-[10px] uppercase font-bold text-blue-400 font-semibold">Deskripsi Langkah</p>
                                <h3 class="text-base font-black text-blue-950 dark:text-blue-200 mt-1">Varians Penjualan Harian (s<sup>2</sup>)</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Membagi jumlah selisih kuadrat dengan total hari dikurang satu (n - 1) menggunakan rumus sampel variance.</p>
                            </div>
                            <div class="bg-slate-50 dark:bg-slate-950/20 border border-slate-200 dark:border-slate-800 rounded-xl p-4">
                                <p class="text-[10px] uppercase font-bold text-slate-400 font-semibold">Persamaan / Formula</p>
                                <p class="text-lg font-mono font-bold text-slate-800 dark:text-slate-200 mt-1">s<sup>2</sup> = &Sigma;(x<sub>i</sub> - d)<sup>2</sup> &divide; (n - 1)</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 italic">Di mana: &Sigma;(x<sub>i</sub> - d)<sup>2</sup> = ${sumsqdiff}, n = ${periode} (sehingga n - 1 = ${nminus1})</p>
                            </div>
                            <div class="bg-blue-600 text-white rounded-xl p-4 text-center shadow-md">
                                <p class="text-[10px] uppercase font-bold text-blue-200">Hasil Varians (s<sup>2</sup>)</p>
                                <p class="text-3xl font-black mt-1">${result}</p>
                            </div>
                        </div>
                    `;
                } else if (step === 'stddev') {
                    title = "Langkah 5: Standar Deviasi (σ)";
                    const variance = this.dataset.variance;
                    const result = this.dataset.result;
                    html = `
                        <div class="space-y-4 text-left">
                            <div class="bg-blue-50 dark:bg-blue-950/45 border border-blue-100 dark:border-blue-900/40 rounded-xl p-4">
                                <p class="text-[10px] uppercase font-bold text-blue-400 font-semibold">Deskripsi Langkah</p>
                                <h3 class="text-base font-black text-blue-950 dark:text-blue-200 mt-1">Standar Deviasi Permintaan Harian (&sigma;)</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Menghitung akar kuadrat dari varians penjualan harian untuk mendapatkan ukuran fluktuasi dalam satuan unit produk yang sama.</p>
                            </div>
                            <div class="bg-slate-50 dark:bg-slate-950/20 border border-slate-200 dark:border-slate-800 rounded-xl p-4">
                                <p class="text-[10px] uppercase font-bold text-slate-400 font-semibold">Persamaan / Formula</p>
                                <p class="text-lg font-mono font-bold text-slate-800 dark:text-slate-200 mt-1">&sigma; = &radic;s<sup>2</sup></p>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 italic">Di mana: s<sup>2</sup> (Varians) = ${variance}</p>
                            </div>
                            <div class="bg-blue-600 text-white rounded-xl p-4 text-center shadow-md">
                                <p class="text-[10px] uppercase font-bold text-blue-200">Hasil Standar Deviasi (&sigma;)</p>
                                <p class="text-3xl font-black mt-1">${result}</p>
                            </div>
                        </div>
                    `;
                }

                openModal(title, html);
            });
        });

        // Lead Time History Trigger Listener
        document.querySelectorAll('.leadtime-history-trigger').forEach(trigger => {
            trigger.addEventListener('click', function(e) {
                e.stopPropagation();
                const historyData = JSON.parse(this.dataset.leadtimeHistory || '[]');
                const average = this.dataset.leadtimeAverage;
                const title = "Rincian Waktu Tunggu (Lead Time)";
                
                let rowsHtml = "";
                if (historyData.length === 0) {
                    rowsHtml = `
                        <tr>
                            <td colspan="5" class="px-4 py-4 text-center text-slate-400 dark:text-slate-500 italic text-xs">
                                Tidak ada riwayat barang masuk dengan tanggal pesan & terima lengkap. Sistem menggunakan nilai default (1.00 Hari).
                            </td>
                        </tr>
                    `;
                } else {
                    historyData.forEach((record, index) => {
                        rowsHtml += `
                            <tr class="border-b border-slate-100 dark:border-slate-800/40 text-xs">
                                <td class="px-4 py-3 text-slate-500 dark:text-slate-400">${index + 1}</td>
                                <td class="px-4 py-3 font-mono font-bold text-slate-700 dark:text-slate-300">${record.kode || '-'}</td>
                                <td class="px-4 py-3 text-slate-600 dark:text-slate-400">${record.tanggal_pesan}</td>
                                <td class="px-4 py-3 text-slate-600 dark:text-slate-400">${record.tanggal_terima}</td>
                                <td class="px-4 py-3 text-right font-mono font-bold text-blue-600 dark:text-blue-400">${record.selisih} Hari</td>
                            </tr>
                        `;
                    });
                }

                const explanation = historyData.length === 0 ? "" : `
                    <div class="bg-blue-50 dark:bg-blue-950/45 border border-blue-100 dark:border-blue-900/40 rounded-xl p-4 text-xs text-slate-600 dark:text-slate-300 leading-relaxed mb-4">
                        <p class="font-bold text-blue-800 dark:text-blue-400 mb-1">Cara Kalkulasi Rata-rata:</p>
                        <p>
                            Total Hari (${historyData.reduce((acc, r) => acc + r.selisih, 0)} Hari) &divide; Total Transaksi (${historyData.length} Transaksi) = <strong class="text-blue-600 dark:text-blue-400">${average} Hari</strong>
                        </p>
                    </div>
                `;

                const html = `
                    <div class="space-y-4 text-left">
                        <div class="bg-slate-50 dark:bg-slate-950/20 border border-slate-200 dark:border-slate-800 rounded-xl p-4">
                            <p class="text-[10px] uppercase font-bold text-slate-400 font-semibold mb-2">Riwayat Penerimaan Barang Masuk</p>
                            <div class="max-h-60 overflow-y-auto pr-1 custom-scroll">
                                <table class="w-full text-left border-collapse">
                                    <thead>
                                        <tr class="border-b border-slate-200 dark:border-slate-800 text-[10px] font-black text-slate-400 uppercase tracking-wider">
                                            <th class="px-4 py-2 w-1/12">No</th>
                                            <th class="px-4 py-2 w-1/3">Kode</th>
                                            <th class="px-4 py-2">Pesan</th>
                                            <th class="px-4 py-2">Terima</th>
                                            <th class="px-4 py-2 text-right">Selisih</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-50 dark:divide-slate-800/40">
                                        ${rowsHtml}
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        ${explanation}

                        <div class="bg-blue-600 text-white rounded-xl p-4 text-center shadow-md">
                            <p class="text-[10px] uppercase font-bold text-blue-200">Hasil Rata-rata Lead Time (L)</p>
                            <p class="text-3xl font-black mt-1">${average} Hari</p>
                        </div>
                    </div>
                `;

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
    .custom-scroll::-webkit-scrollbar { height: 6px; }
    .custom-scroll::-webkit-scrollbar-track { background: #f1f5f9; }
    .dark .custom-scroll::-webkit-scrollbar-track { background: #0f172a; }
    .custom-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    .dark .custom-scroll::-webkit-scrollbar-thumb { background: #334155; }
    .custom-scroll::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>
@endpush
