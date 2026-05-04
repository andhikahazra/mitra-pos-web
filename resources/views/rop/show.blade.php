@extends('dashboard.layouts.pemilik')

@section('dashboard-content')
    <section class="feature-section active" id="section-rop-show">
        <div class="section-head">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <a href="{{ route('rop.index') }}" class="link-btn more">
                        <svg style="width: 16px; height: 16px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                        Kembali
                    </a>
                </div>
                <h1>Detail Analisis ROP</h1>
                <p>Analisis perhitungan stok produk: <strong>{{ $produk->nama }}</strong></p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="panel-card !p-6">
                <span class="text-[10px] font-bold text-slate-400 uppercase block mb-1">Stok Fisik</span>
                <span class="text-2xl font-bold">{{ $stock }}</span>
            </div>
            <div class="panel-card !p-6">
                <span class="text-[10px] font-bold text-slate-400 uppercase block mb-1">Titik ROP</span>
                <span class="text-2xl font-bold text-indigo-600">{{ $rop }}</span>
            </div>
            <div class="panel-card !p-6">
                <span class="text-[10px] font-bold text-slate-400 uppercase block mb-1">Status</span>
                <div class="mt-1">
                    @if($status === 'aman')
                        <span class="status-pill success">Aman</span>
                    @elseif($status === 'hampir habis')
                        <span class="status-pill warning">Hampir Habis</span>
                    @else
                        <span class="status-pill danger">Harus Restock</span>
                    @endif
                </div>
            </div>
            <div class="panel-card !p-6">
                <span class="text-[10px] font-bold text-slate-400 uppercase block mb-1">Lead Time</span>
                <span class="text-2xl font-bold">{{ $leadTime }} <small class="text-xs font-normal opacity-50">hari</small></span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <article class="panel-card">
                    <div class="toolbar !mb-8">
                        <h2 class="text-lg font-bold">Lembar Kerja Perhitungan</h2>
                    </div>

                    <div class="space-y-8 p-2">
                        {{-- Step 1 --}}
                        <div>
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">1. Demand Lead Time (d &times; L)</h4>
                            <div class="bg-slate-50 p-4 rounded-xl border border-slate-100 font-mono text-sm">
                                <p>{{ number_format($rataPenjualan, 2) }} &times; {{ $leadTime }} = <span class="font-bold text-indigo-600">{{ number_format($usageLT, 2) }}</span></p>
                            </div>
                        </div>

                        {{-- Step 2 --}}
                        <div>
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">2. Safety Stock (Z &times; &sigma; &times; &radic;L)</h4>
                            <div class="bg-slate-50 p-4 rounded-xl border border-slate-100 font-mono text-sm">
                                <p>{{ $zScore }} &times; {{ number_format($standarDeviasi, 2) }} &times; {{ number_format($sqrtLT, 2) }} = <span class="font-bold text-indigo-600">{{ number_format($safetyStock, 2) }}</span></p>
                            </div>
                        </div>

                        {{-- Step 3 --}}
                        <div>
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">3. Reorder Point (Final ROP)</h4>
                            <div class="bg-indigo-600 p-6 rounded-xl text-white shadow-lg">
                                <p class="text-[10px] font-bold uppercase opacity-60 mb-1">Formula: DemandLT + SS</p>
                                <p class="text-3xl font-bold">{{ $rop }} <span class="text-sm font-normal opacity-60">Unit</span></p>
                            </div>
                        </div>
                    </div>
                </article>

                <article class="panel-card">
                    <div class="toolbar !mb-6">
                        <h2 class="text-sm font-bold">Bukti Data Penjualan (30 Hari)</h2>
                        @if($isSample)
                            <span class="status-pill warning !text-[9px]">Simulasi</span>
                        @endif
                    </div>

                    <div class="table-wrap">
                        <div class="flex gap-2 overflow-x-auto pb-4">
                            @php $sum = 0; @endphp
                            @foreach($dailyData as $date => $qty)
                                @php $sum += $qty; @endphp
                                <div class="flex-none w-14 text-center">
                                    <div class="p-2 border rounded-lg {{ $qty > 0 ? 'bg-indigo-50 border-indigo-100 text-indigo-600' : 'bg-slate-50 border-slate-100 text-slate-300' }} font-bold text-xs">
                                        {{ $qty }}
                                    </div>
                                    <span class="text-[9px] text-slate-400 font-bold uppercase block mt-1">{{ date('d/m', strtotime($date)) }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mt-6 flex justify-between items-center bg-slate-50 p-4 rounded-lg text-xs">
                        <div>
                            <p class="text-slate-500 uppercase font-bold tracking-tighter">Total Terjual</p>
                            <p class="text-lg font-bold">{{ $sum }} unit</p>
                        </div>
                        <div class="text-right">
                            <p class="text-slate-500 uppercase font-bold tracking-tighter">Rata-rata (d)</p>
                            <p class="text-lg font-bold text-indigo-600">{{ number_format($sum/30, 2) }}</p>
                        </div>
                    </div>
                </article>
            </div>

            <div class="space-y-6">
                <article class="panel-card bg-slate-900 text-white">
                    <h3 class="text-xs font-bold uppercase tracking-widest mb-6 opacity-40">Landasan Teori</h3>
                    <div class="space-y-4">
                        <p class="text-xs leading-relaxed italic border-l-2 border-white/20 pl-4">
                            "Penerapan metode ROP dan safety stock secara terintegrasi merupakan strategi dalam pengendalian stok untuk mengurangi risiko stockout."
                        </p>
                        <p class="text-[10px] font-bold opacity-60">— Ariyanti dkk. (2025)</p>
                    </div>
                </article>

                <article class="panel-card">
                    <h3 class="text-xs font-bold text-slate-400 uppercase mb-4">Informasi Tambahan</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-slate-500">Service Level</span>
                            <span class="font-bold">95.0%</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-slate-500">Standar Deviasi</span>
                            <span class="font-bold">{{ number_format($standarDeviasi, 2) }}</span>
                        </div>
                    </div>
                </article>
            </div>
        </div>
    </section>
@endsection
