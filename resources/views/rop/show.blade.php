@extends('dashboard.layouts.pemilik')

@section('dashboard-content')
    <section class="feature-section active" id="section-rop-show">
        {{-- Page Header --}}
        <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1>Detail Laporan Reorder Point</h1>
                <p class="text-[11px] text-slate-500 mt-1 uppercase font-bold tracking-widest">Produk: <span class="text-blue-600">{{ $produk->nama }}</span></p>
            </div>
            <div class="flex flex-col gap-2 items-end">
                <a href="{{ route('rop.index') }}" class="group inline-flex items-center px-4 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 rounded-md text-xs font-bold hover:bg-slate-50 dark:hover:bg-slate-800/60 hover:text-blue-600 dark:hover:text-blue-400 hover:border-blue-200 dark:hover:border-blue-900 transition-all duration-200 shadow-sm w-full md:w-auto justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 transform group-hover:-translate-x-1 transition-transform duration-200" fill="none" viewbox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali ke Daftar
                </a>
                <a href="{{ route('rop.presentation', $produk) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-xs font-bold transition-all duration-200 shadow-sm w-full md:w-auto justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewbox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    Detail Perhitungan Akademik
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
            <div class="bg-white p-5 border border-slate-200 rounded shadow-sm border-l-4 border-l-blue-500">
                <p class="text-[10px] font-bold text-blue-500 uppercase mb-2 tracking-wider">Titik ROP (Reorder Point)</p>
                <div class="flex items-baseline gap-1">
                    <span class="text-2xl font-black text-blue-700">{{ $rop }}</span>
                    <span class="text-xs text-blue-400 font-bold uppercase">Unit</span>
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
            <div class="bg-white p-5 border border-slate-200 rounded shadow-sm cursor-pointer hover:border-blue-300 dark:hover:border-blue-900 transition-all duration-150 leadtime-history-trigger"
                 data-leadtime-history="{{ json_encode($leadTimeHistory) }}"
                 data-leadtime-average="{{ $leadTime }}"
                 title="Klik untuk melihat riwayat penerimaan barang masuk">
                <p class="text-[10px] font-bold text-slate-400 uppercase mb-2 tracking-wider">Lead Time Supplier (L)</p>
                <div class="flex items-baseline justify-between">
                    <div>
                        <span class="text-2xl font-black text-slate-800 dark:text-slate-100">{{ $leadTime }}</span>
                        <span class="text-xs text-slate-500 font-bold uppercase">Hari</span>
                    </div>
                    <span class="text-[10px] text-blue-600 dark:text-blue-400 font-bold underline">Lihat Rincian</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Calculation Logic Section --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Switch Tampilan --}}
                <div class="flex bg-slate-100 dark:bg-slate-950 p-1 rounded-xl w-fit border border-slate-200/50 dark:border-slate-800/80 mb-2">
                    <button type="button" class="rop-tab-btn active px-4 py-2 rounded-lg text-xs font-bold transition-all duration-200 cursor-pointer" data-target="rop-academic">
                        Metode Akademik & Audit Trail
                    </button>
                    <button type="button" class="rop-tab-btn px-4 py-2 rounded-lg text-xs font-bold transition-all duration-200 cursor-pointer text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200" data-target="rop-simple">
                        Langkah Praktis Step-by-Step
                    </button>
                </div>

                {{-- Container 1: Calculation Audit Trail (Academic) --}}
                <div id="rop-academic" class="rop-tab-content space-y-6">
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
                                            <span class="text-[10px] font-mono font-bold text-blue-400 tracking-widest">d &times; L</span>
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
                                            <span class="text-[10px] font-mono font-bold text-blue-400 tracking-widest">Z &times; &sigma; &times; &radic;L</span>
                                            <span class="text-xs font-mono text-slate-500">{{ $zScore }} &times; {{ number_format($standarDeviasi, 2) }} &times; {{ number_format($sqrtLT, 2) }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 text-right font-mono font-bold text-slate-800">
                                        {{ number_format($safetyStock, 2) }}
                                    </td>
                                </tr>

                                {{-- Baris 3: Final ROP --}}
                                <tr class="bg-blue-50/20 cursor-pointer hover:bg-blue-50/40 transition-colors audit-trail-row" 
                                    data-type="rop"
                                    data-usagelt="{{ number_format($usageLT, 2) }}"
                                    data-safetystock="{{ number_format($safetyStock, 2) }}"
                                    data-result="{{ $rop }}"
                                    title="Klik untuk detail perhitungan">
                                    <td class="px-6 py-5">
                                        <p class="text-xs font-black text-blue-900 uppercase tracking-tight">Reorder Point (ROP)</p>
                                        <p class="text-[10px] text-blue-400 mt-1 font-bold italic">(d &times; L) + SS</p>
                                    </td>
                                    <td class="px-6 py-5 text-center border-x border-blue-100">
                                        <div class="flex flex-col gap-1">
                                            <span class="text-[10px] font-mono font-bold text-blue-500 tracking-widest">(d &times; L) + SS</span>
                                            <span class="text-xs font-mono text-blue-700 font-bold">{{ number_format($usageLT, 2) }} + {{ number_format($safetyStock, 2) }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 text-right">
                                        <span class="text-2xl font-black text-blue-900">{{ $rop }}</span>
                                        <span class="text-[10px] font-bold text-blue-400 ml-1 uppercase">Unit</span>
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
                                            <td class="px-4 py-2 border-b border-slate-100 text-right font-mono font-bold text-blue-700">{{ number_format($rataPenjualan, 2) }}</td>
                                        </tr>
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
                                        <tr class="cursor-pointer hover:bg-slate-50/85 transition-colors methodology-row"
                                            data-step="sqdiff"
                                            data-periode="{{ $periode }}"
                                            data-mean="{{ number_format($rataPenjualan, 2) }}"
                                            data-firstqty="{{ $firstQtyVal }}"
                                            data-firstdiff="{{ $firstDiffVal }}"
                                            data-firstsqdiff="{{ $firstSqDiffVal }}"
                                            data-sqdiffs-list="{{ $sqDiffsListVal }}"
                                            data-sqdiffs-vertical-html="{{ $sqDiffsVerticalHtml }}"
                                            data-result="{{ number_format($sumSqDiff, 2) }}"
                                            title="Klik untuk melihat penjelasan detail">
                                            <td class="px-4 py-2 border-b border-slate-100 font-bold text-slate-700">3. Selisih Kuadrat</td>
                                            <td class="px-4 py-2 border-b border-slate-100 text-slate-500 italic">
                                                Total dari 30 kotak harian (&Sigma;(x<sub>i</sub>-d)&sup2;)<br>
                                                <div class="mt-2 p-2 bg-slate-50 dark:bg-slate-950/20 border border-slate-100 dark:border-slate-800/60 rounded">
                                                    <p class="text-[9px] font-bold text-slate-600 dark:text-slate-400 mb-1 tracking-tight underline">Contoh Hitung (Hari 1):</p>
                                                    <p class="text-[9px] font-mono text-blue-600 dark:text-blue-400">
                                                        (x<sub>1</sub> - d)&sup2; = 
                                                        ({{ $firstQtyVal }} - {{ number_format($rataPenjualan, 2) }})&sup2; = 
                                                        {{ $firstSqDiffVal }}
                                                    </p>
                                                </div>
                                                <button type="button" class="mt-2 text-[10px] text-blue-600 dark:text-blue-400 font-bold hover:underline flex items-center gap-1">
                                                    Lihat rincian selisih kuadrat 30 hari
                                                </button>
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
                                            <td class="px-4 py-2 border-b border-slate-100 text-right font-mono font-black text-blue-700">{{ number_format($standarDeviasi, 2) }}</td>
                                        </tr>
                                        <tr class="bg-blue-50/30">
                                            <td class="px-4 py-2 font-bold text-blue-900" colspan="2">Kesimpulan Analisis Variabel Demand</td>
                                            <td class="px-4 py-2 text-right font-mono font-black text-blue-900">Valid & Terverifikasi</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                </div>

                {{-- Container 2: Langkah Praktis Step-by-Step (Simple Detailed) --}}
                <div id="rop-simple" class="rop-tab-content space-y-6 hidden">
                    {{-- Langkah 1: Kebutuhan Selama Pengiriman (Lead Time Demand) --}}
                    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm p-6 dark:bg-slate-900/60 dark:border-slate-800">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="h-8 w-8 rounded-full bg-blue-100 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 flex items-center justify-center font-bold text-sm">1</div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100 uppercase tracking-wide">Langkah 1: Kebutuhan Selama Pengiriman</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Menghitung stok yang terjual saat menunggu barang datang.</p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4 bg-slate-50 dark:bg-slate-950/40 p-4 rounded-xl border border-slate-100 dark:border-slate-800/80">
                            <div>
                                <span class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Rata-rata Penjualan Harian</span>
                                <span class="text-base font-extrabold text-slate-700 dark:text-slate-200">{{ number_format($rataPenjualan, 2) }} <span class="text-xs font-semibold text-slate-400">Unit / Hari</span></span>
                            </div>
                            <div class="border-t md:border-t-0 md:border-l border-slate-200 dark:border-slate-800 pt-3 md:pt-0 md:pl-4">
                                <span class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Waktu Tunggu (Lead Time)</span>
                                <span class="text-base font-extrabold text-slate-700 dark:text-slate-200">{{ $leadTime }} <span class="text-xs font-semibold text-slate-400">Hari</span></span>
                            </div>
                            <div class="border-t md:border-t-0 md:border-l border-slate-200 dark:border-slate-800 pt-3 md:pt-0 md:pl-4">
                                <span class="text-[10px] uppercase font-bold text-blue-400 block mb-1">Kebutuhan Stok</span>
                                <span class="text-base font-extrabold text-blue-600 dark:text-blue-400">{{ number_format($usageLT, 2) }} <span class="text-xs font-semibold text-blue-400">Unit</span></span>
                            </div>
                        </div>
                        
                        <div class="text-xs leading-relaxed text-slate-600 dark:text-slate-400 space-y-2">
                            <p>
                                <strong class="text-blue-800 dark:text-blue-400">Cara Menghitung</strong>: Rata-rata Penjualan Harian dikalikan dengan Waktu Tunggu Supplier.
                            </p>
                            <p class="font-mono bg-slate-50 dark:bg-slate-950/20 p-2.5 rounded border border-slate-100 dark:border-slate-800/60 w-fit">
                                {{ number_format($rataPenjualan, 2) }} unit &times; {{ $leadTime }} hari = <strong class="text-slate-800 dark:text-slate-200">{{ number_format($usageLT, 2) }} unit</strong>
                            </p>
                            <p>
                                Arti operasionalnya, saat Anda memesan barang ke supplier, dibutuhkan waktu **{{ $leadTime }} hari** hingga barang tersebut sampai ke gudang Anda. Selama masa penantian tersebut, pelanggan diprediksi akan membeli sebanyak **{{ number_format($usageLT, 2) }} unit** produk ini.
                            </p>
                        </div>
                    </div>

                    {{-- Langkah 2: Stok Cadangan Pengaman (Safety Stock) --}}
                    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm p-6 dark:bg-slate-900/60 dark:border-slate-800">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="h-8 w-8 rounded-full bg-amber-100 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold text-sm">2</div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100 uppercase tracking-wide">Langkah 2: Stok Cadangan Pengaman</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Cadangan ekstra untuk mengantisipasi ketidakpastian.</p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4 bg-slate-50 dark:bg-slate-950/40 p-4 rounded-xl border border-slate-100 dark:border-slate-800/80">
                            <div>
                                <span class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Toleransi Kehabisan Stok (Z)</span>
                                <span class="text-base font-extrabold text-slate-700 dark:text-slate-200">95% <span class="text-xs font-semibold text-slate-400">(Skor Z: {{ $zScore }})</span></span>
                            </div>
                            <div class="border-t md:border-t-0 md:border-l border-slate-200 dark:border-slate-800 pt-3 md:pt-0 md:pl-4">
                                <span class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Fluktuasi Harian (Std Deviasi)</span>
                                <span class="text-base font-extrabold text-slate-700 dark:text-slate-200">&plusmn; {{ number_format($standarDeviasi, 2) }} <span class="text-xs font-semibold text-slate-400">Unit / Hari</span></span>
                            </div>
                            <div class="border-t md:border-t-0 md:border-l border-slate-200 dark:border-slate-800 pt-3 md:pt-0 md:pl-4">
                                <span class="text-[10px] uppercase font-bold text-amber-500 block mb-1">Stok Cadangan (Safety Stock)</span>
                                <span class="text-base font-extrabold text-amber-600 dark:text-amber-400">{{ number_format($safetyStock, 2) }} <span class="text-xs font-semibold text-amber-400">Unit</span></span>
                            </div>
                        </div>
                        
                        <div class="text-xs leading-relaxed text-slate-600 dark:text-slate-400 space-y-2">
                            <p>
                                <strong class="text-blue-800 dark:text-blue-400">Cara Menghitung</strong>: Faktor Layanan (Z) &times; Fluktuasi Harian (&sigma;) &times; Akar Waktu Tunggu (&radic;L).
                            </p>
                            <p class="font-mono bg-slate-50 dark:bg-slate-950/20 p-2.5 rounded border border-slate-100 dark:border-slate-800/60 w-fit">
                                {{ $zScore }} (Z) &times; {{ number_format($standarDeviasi, 2) }} (&sigma;) &times; {{ number_format($sqrtLT, 2) }} (&radic;L) = <strong class="text-slate-800 dark:text-slate-200">{{ number_format($safetyStock, 2) }} unit</strong>
                            </p>
                            <p>
                                Stok pengaman ini bertindak sebagai **"ban serep"**. Stok ini tidak boleh dijual dalam kondisi normal dan hanya disentuh apabila penjualan tiba-tiba melonjak drastis di atas rata-rata atau supplier terlambat mengirimkan barang. Dengan cadangan sebesar **{{ number_format($safetyStock, 2) }} unit**, Anda meminimalkan risiko toko kehabisan stok produk ini hingga **95%**.
                            </p>
                        </div>
                    </div>

                    {{-- Langkah 3: Titik Pemesanan Kembali (Reorder Point) --}}
                    <div class="bg-blue-50/20 border border-blue-100 dark:border-blue-900/40 rounded-xl overflow-hidden shadow-sm p-6 dark:bg-blue-950/8">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="h-8 w-8 rounded-full bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-300 flex items-center justify-center font-bold text-sm">3</div>
                            <div>
                                <h3 class="text-sm font-bold text-blue-900 dark:text-blue-300 uppercase tracking-wide">Langkah 3: Titik Pemesanan Kembali (ROP)</h3>
                                <p class="text-xs text-blue-500 dark:text-blue-400">Batas minimum stok untuk melakukan order baru.</p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4 bg-white dark:bg-slate-900/60 p-4 rounded-xl border border-blue-100/50 dark:border-blue-900/20">
                            <div>
                                <span class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Kebutuhan Lead Time</span>
                                <span class="text-base font-extrabold text-slate-700 dark:text-slate-200">{{ number_format($usageLT, 2) }} <span class="text-xs font-semibold text-slate-400">Unit</span></span>
                            </div>
                            <div class="border-t md:border-t-0 md:border-l border-slate-200 dark:border-slate-800 pt-3 md:pt-0 md:pl-4">
                                <span class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Stok Cadangan (SS)</span>
                                <span class="text-base font-extrabold text-slate-700 dark:text-slate-200">{{ number_format($safetyStock, 2) }} <span class="text-xs font-semibold text-slate-400">Unit</span></span>
                            </div>
                            <div class="border-t md:border-t-0 md:border-l border-blue-100 dark:border-blue-900/40 pt-3 md:pt-0 md:pl-4">
                                <span class="text-[10px] uppercase font-bold text-blue-500 block mb-1">Titik ROP Akhir</span>
                                <span class="text-2xl font-black text-blue-600 dark:text-blue-300">{{ $rop }} <span class="text-xs font-semibold text-blue-400">Unit</span></span>
                            </div>
                        </div>
                        
                        <div class="text-xs leading-relaxed text-slate-600 dark:text-slate-400 space-y-2">
                            <p>
                                <strong class="text-blue-800 dark:text-blue-400">Cara Menghitung</strong>: Kebutuhan Selama Pengiriman (Langkah 1) ditambah Stok Cadangan Pengaman (Langkah 2).
                            </p>
                            <p class="font-mono bg-white dark:bg-slate-900/60 p-2.5 rounded border border-blue-100/50 dark:border-blue-900/20 w-fit">
                                {{ number_format($usageLT, 2) }} (Langkah 1) + {{ number_format($safetyStock, 2) }} (Langkah 2) = <strong class="text-blue-600 dark:text-blue-300">{{ $rop }} unit</strong> (dibulatkan)
                            </p>
                            <p class="text-slate-700 dark:text-slate-300 font-medium">
                                <strong class="text-blue-800 dark:text-blue-400">Rekomendasi Tindakan</strong>:
                                Ketika stok fisik produk **"{{ $produk->nama }}"** di gudang atau aplikasi Anda menyusut hingga **{{ $rop }} unit**, Anda harus segera membuat pesanan pembelian baru kepada supplier agar stok baru tiba tepat sebelum stok cadangan pengaman (safety stock) Anda habis terpakai.
                            </p>
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
                        <div class="flex justify-between cursor-pointer hover:text-blue-600 dark:hover:text-blue-400 leadtime-history-trigger"
                             data-leadtime-history="{{ json_encode($leadTimeHistory) }}"
                             data-leadtime-average="{{ $leadTime }}"
                             title="Klik untuk melihat riwayat penerimaan barang masuk">
                            <span class="text-xs text-slate-600 font-medium">Lead Time (L)</span>
                            <span class="text-xs font-bold text-slate-900 dark:text-slate-100 underline">{{ $leadTime }} Hari</span>
                        </div>
                        <div class="flex justify-between cursor-pointer hover:text-blue-600 dark:hover:text-blue-400 leadtime-history-trigger"
                             data-leadtime-history="{{ json_encode($leadTimeHistory) }}"
                             data-leadtime-average="{{ $leadTime }}"
                             title="Klik untuk melihat riwayat penerimaan barang masuk">
                            <span class="text-xs text-slate-600 font-medium">Akar Lead Time (&radic;L)</span>
                            <span class="text-xs font-bold text-slate-900 dark:text-slate-100 underline">&radic;{{ $leadTime }} = {{ number_format($sqrtLT, 2) }}</span>
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

        // Tab switching logic
        const tabButtons = document.querySelectorAll('.rop-tab-btn');
        const tabContents = document.querySelectorAll('.rop-tab-content');

        tabButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const target = this.dataset.target;

                // Toggle active class on buttons
                tabButtons.forEach(b => {
                    b.classList.remove('active');
                    b.classList.add('text-slate-500', 'hover:text-slate-700', 'dark:text-slate-400', 'dark:hover:text-slate-200');
                });
                this.classList.add('active');
                this.classList.remove('text-slate-500', 'hover:text-slate-700', 'dark:text-slate-400', 'dark:hover:text-slate-200');

                // Toggle hidden class on contents
                tabContents.forEach(content => {
                    if (content.id === target) {
                        content.classList.remove('hidden');
                        content.classList.add('animate-fade-in');
                    } else {
                        content.classList.add('hidden');
                        content.classList.remove('animate-fade-in');
                    }
                });
            });
        });

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
                        <div class="bg-blue-50 border border-blue-100 rounded-xl p-4">
                            <p class="text-[10px] uppercase font-bold text-blue-400">Tanggal Data</p>
                            <h3 class="text-base font-black text-blue-950 mt-1">${date}</h3>
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
                                <p class="text-xl font-black text-blue-600 mt-1">${sqdiff}</p>
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
                            <div class="bg-blue-50 border border-blue-100 rounded-xl p-4">
                                <p class="text-[10px] uppercase font-bold text-blue-400">Nama Komponen</p>
                                <h3 class="text-base font-black text-blue-950 mt-1">Demand During Lead Time (Permintaan Selama Waktu Tunggu)</h3>
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
                                <p class="text-[10px] uppercase font-bold text-blue-200">Hasil Akhir (d &times; L)</p>
                                <p class="text-3xl font-black mt-1">${result} <span class="text-sm font-bold text-blue-100">Unit</span></p>
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
                            <div class="bg-blue-50 border border-blue-100 rounded-xl p-4">
                                <p class="text-[10px] uppercase font-bold text-blue-400">Nama Komponen</p>
                                <h3 class="text-base font-black text-blue-950 mt-1">Safety Stock (Stok Pengaman)</h3>
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
                                <p class="text-[10px] uppercase font-bold text-blue-200">Hasil Akhir (SS)</p>
                                <p class="text-3xl font-black mt-1">${result} <span class="text-sm font-bold text-blue-100">Unit</span></p>
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
                            <div class="bg-blue-50 border border-blue-100 rounded-xl p-4">
                                <p class="text-[10px] uppercase font-bold text-blue-400">Nama Komponen</p>
                                <h3 class="text-base font-black text-blue-950 mt-1">Reorder Point (Titik Pemesanan Kembali)</h3>
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
                                <p class="text-[10px] uppercase font-bold text-blue-200">Hasil Akhir (ROP)</p>
                                <p class="text-3xl font-black mt-1">${result} <span class="text-sm font-bold text-blue-100">Unit</span></p>
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
                            <div class="bg-blue-50 border border-blue-100 rounded-xl p-4">
                                <p class="text-[10px] uppercase font-bold text-blue-400 font-semibold">Deskripsi Langkah</p>
                                <h3 class="text-base font-black text-blue-950 mt-1">Total Unit Terjual (&Sigma;x<sub>i</sub>)</h3>
                                <p class="text-xs text-slate-500 mt-1">Menjumlahkan seluruh kuantitas penjualan harian selama periode analisis (${periode} hari).</p>
                            </div>
                            
                            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
                                <p class="text-[10px] uppercase font-bold text-slate-400 font-semibold">Persamaan / Formula</p>
                                <p class="text-lg font-mono font-bold text-slate-800 mt-1">&Sigma; x<sub>i</sub> = x<sub>1</sub> + x<sub>2</sub> + ... + x<sub>${periode}</sub></p>
                            </div>
                            
                            <div class="bg-[#1E40AF] text-white rounded-xl p-4 text-center">
                                <p class="text-[10px] uppercase font-bold text-blue-200">Total Terjual (${periode} Hari)</p>
                                <p class="text-3xl font-black mt-1">${sum} <span class="text-sm font-bold text-blue-100">Unit</span></p>
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
                            <div class="bg-blue-50 border border-blue-100 rounded-xl p-4">
                                <p class="text-[10px] uppercase font-bold text-blue-400 font-semibold">Deskripsi Langkah</p>
                                <h3 class="text-base font-black text-blue-950 mt-1">Rata-rata Permintaan Harian (d)</h3>
                                <p class="text-xs text-slate-500 mt-1">Membagi total unit yang terjual dengan jumlah hari dalam periode analisis (${periode} hari).</p>
                            </div>
                            
                            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
                                <p class="text-[10px] uppercase font-bold text-slate-400 font-semibold">Persamaan / Formula</p>
                                <p class="text-lg font-mono font-bold text-slate-800 mt-1">d = &Sigma;x<sub>i</sub> &divide; n</p>
                                <p class="text-xs text-slate-500 mt-1 italic">Di mana: &Sigma;x<sub>i</sub> = ${sum} Unit, dan n = ${periode} Hari</p>
                            </div>
                            
                            <div class="bg-[#1E40AF] text-white rounded-xl p-4 text-center">
                                <p class="text-[10px] uppercase font-bold text-blue-200">Hasil Rata-rata (d)</p>
                                <p class="text-3xl font-black mt-1">${mean} <span class="text-sm font-bold text-blue-100">Unit / Hari</span></p>
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
                    const verticalHtml = this.dataset.sqdiffsVerticalHtml;
                    const result = this.dataset.result;
                    html = `
                        <div class="space-y-4 text-left">
                            <div class="bg-blue-50 dark:bg-blue-950/45 border border-blue-100 dark:border-blue-900/40 rounded-xl p-4">
                                <p class="text-[10px] uppercase font-bold text-blue-400 font-semibold">Deskripsi Langkah</p>
                                <h3 class="text-base font-black text-blue-950 dark:text-blue-200 mt-1">Jumlah Selisih Kuadrat (&Sigma;(x<sub>i</sub> - d)<sup>2</sup>)</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Mengurangi penjualan tiap hari dengan rata-rata, mengkuadratkan hasilnya, lalu menjumlahkan seluruh nilai kuadrat tersebut dari Hari 1 s/d Hari 30.</p>
                            </div>
                            
                            <div class="bg-slate-50 dark:bg-slate-950/20 border border-slate-200 dark:border-slate-800 rounded-xl p-4">
                                <p class="text-[10px] uppercase font-bold text-slate-400 font-semibold">Persamaan / Formula</p>
                                <p class="text-lg font-mono font-bold text-slate-800 dark:text-slate-200 mt-1">&Sigma;(x<sub>i</sub> - d)<sup>2</sup></p>
                                <div class="mt-2 p-3 bg-white dark:bg-slate-900/60 rounded-xl border border-slate-100 dark:border-slate-800/60 text-xs">
                                    <p class="font-bold text-slate-600 dark:text-slate-400 mb-1">Contoh Hari Pertama (x<sub>1</sub> = ${firstqty}):</p>
                                    <p class="font-mono text-blue-600 dark:text-blue-400">(x<sub>1</sub> - d)<sup>2</sup> = (${firstqty} - ${mean})<sup>2</sup> = (${firstdiff})<sup>2</sup> = ${firstsqdiff}</p>
                                </div>
                            </div>
                            
                            <div class="bg-slate-50 dark:bg-slate-950/20 border border-slate-200 dark:border-slate-800 rounded-xl p-4">
                                <p class="text-[10px] uppercase font-bold text-slate-400 font-semibold mb-2">Akumulasi Rincian Perhitungan Harian (${periode} Hari)</p>
                                <div class="max-h-48 overflow-y-auto pr-2 space-y-1.5 custom-scroll">
                                    ${verticalHtml}
                                </div>
                            </div>
                            
                            <div class="bg-[#1E40AF] text-white rounded-xl p-4 text-center shadow-md">
                                <p class="text-[10px] uppercase font-bold text-blue-200">Hasil Jumlah Selisih Kuadrat</p>
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
                            <div class="bg-blue-50 border border-blue-100 rounded-xl p-4">
                                <p class="text-[10px] uppercase font-bold text-blue-400 font-semibold">Deskripsi Langkah</p>
                                <h3 class="text-base font-black text-blue-950 mt-1">Varians Penjualan Harian (s<sup>2</sup>)</h3>
                                <p class="text-xs text-slate-500 mt-1">Membagi jumlah selisih kuadrat dengan total hari dikurang satu (n - 1) menggunakan rumus sampel variance.</p>
                            </div>
                            
                            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
                                <p class="text-[10px] uppercase font-bold text-slate-400 font-semibold">Persamaan / Formula</p>
                                <p class="text-lg font-mono font-bold text-slate-800 mt-1">s<sup>2</sup> = &Sigma;(x<sub>i</sub> - d)<sup>2</sup> &divide; (n - 1)</p>
                                <p class="text-xs text-slate-500 mt-1 italic">Di mana: &Sigma;(x<sub>i</sub> - d)<sup>2</sup> = ${sumsqdiff}, n = ${periode} (sehingga n - 1 = ${nminus1})</p>
                            </div>
                            
                            <div class="bg-[#1E40AF] text-white rounded-xl p-4 text-center">
                                <p class="text-[10px] uppercase font-bold text-blue-200">Hasil Varians (s<sup>2</sup>)</p>
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
                            <div class="bg-blue-50 border border-blue-100 rounded-xl p-4">
                                <p class="text-[10px] uppercase font-bold text-blue-400 font-semibold">Deskripsi Langkah</p>
                                <h3 class="text-base font-black text-blue-950 mt-1">Standar Deviasi Permintaan Harian (&sigma;)</h3>
                                <p class="text-xs text-slate-500 mt-1">Menghitung akar kuadrat dari varians penjualan harian untuk mendapatkan ukuran fluktuasi dalam satuan unit produk yang sama.</p>
                            </div>
                            
                            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
                                <p class="text-[10px] uppercase font-bold text-slate-400 font-semibold">Persamaan / Formula</p>
                                <p class="text-lg font-mono font-bold text-slate-800 mt-1">&sigma; = &radic;s<sup>2</sup></p>
                                <p class="text-xs text-slate-500 mt-1 italic">Di mana: s<sup>2</sup> (Varians) = ${variance}</p>
                            </div>
                            
                            <div class="bg-[#1E40AF] text-white rounded-xl p-4 text-center">
                                <p class="text-[10px] uppercase font-bold text-blue-200">Hasil Standar Deviasi (&sigma;)</p>
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
    .custom-scroll::-webkit-scrollbar { height: 4px; }
    .custom-scroll::-webkit-scrollbar-track { background: #f1f5f9; }
    .custom-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    .custom-scroll::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

    .rop-tab-btn {
        transition: all 0.2s ease-in-out;
    }
    .rop-tab-btn.active {
        background-color: #ffffff;
        color: #1e293b;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    }
    .dark .rop-tab-btn.active {
        background-color: #0f172a;
        color: #f8fafc;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.3);
    }
</style>
@endpush
