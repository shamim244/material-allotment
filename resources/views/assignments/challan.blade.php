<html>
<head>
    <meta charset="utf-8">
    <title>Assignment Challan — {{ $worker->name }}</title>
    <style>
        @page { size: A4; margin: 15mm; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; color: #1a1a1a; font-size: 11px; line-height: 1.5; }
        .no-print { display: block; }
        @media print {
            .no-print { display: none !important; }
            body { font-size: 10px; }
        }

        .challan-container { max-width: 800px; margin: 0 auto; padding: 20px; }

        /* Header */
        .challan-header { border-bottom: 3px double #333; padding-bottom: 12px; margin-bottom: 15px; }
        .challan-header h1 { font-size: 20px; text-align: center; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; }
        .challan-header .sub { text-align: center; font-size: 12px; color: #555; margin-top: 4px; }

        /* Info Grid */
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 6px; padding: 12px; background: #fafafa; }
        .info-grid .info-item { display: flex; gap: 6px; }
        .info-grid .label { font-weight: 600; color: #555; min-width: 110px; }
        .info-grid .value { font-weight: 700; color: #111; }

        /* Summary Bar */
        .summary-bar { display: flex; gap: 15px; margin-bottom: 15px; }
        .summary-item { flex: 1; text-align: center; background: #f8f9fa; border: 1px solid #eee; border-radius: 6px; padding: 8px; }
        .summary-item .count { font-size: 22px; font-weight: 800; color: #4f46e5; }
        .summary-item .lbl { font-size: 9px; color: #888; text-transform: uppercase; letter-spacing: 0.5px; }

        /* Meter Table */
        .meter-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .meter-table th { background: #2d3748; color: #fff; padding: 6px 10px; text-align: left; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; }
        .meter-table td { padding: 5px 10px; border-bottom: 1px solid #eee; font-size: 11px; }
        .meter-table tr:nth-child(even) { background: #f9fafb; }
        .meter-table .sno { width: 35px; text-align: center; color: #999; }

        /* Footer */
        .signatures { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-top: 40px; padding-top: 15px; }
        .sig-box { text-align: center; }
        .sig-line { border-top: 1px solid #333; margin-top: 50px; padding-top: 6px; font-weight: 600; font-size: 11px; }
        .sig-role { font-size: 9px; color: #777; }

        .footer-note { text-align: center; margin-top: 20px; padding-top: 10px; border-top: 1px dashed #ccc; font-size: 9px; color: #999; }

        /* Toolbar */
        .toolbar { position: fixed; bottom: 0; left: 0; right: 0; background: #fff; border-top: 1px solid #e5e7eb; padding: 12px 20px; display: flex; align-items: center; justify-content: space-between; gap: 12px; z-index: 100; box-shadow: 0 -2px 10px rgba(0,0,0,0.08); }
        .toolbar select { padding: 6px 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px; }
        .toolbar .btn { padding: 8px 20px; border: none; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; }
        .btn-print { background: #4f46e5; color: #fff; }
        .btn-print:hover { background: #4338ca; }
        .btn-back { background: #f3f4f6; color: #374151; }
        .btn-back:hover { background: #e5e7eb; }
        .btn-all { background: #f59e0b; color: #fff; }
        .btn-all:hover { background: #d97706; }
    </style>
</head>
<body>
    {{-- Toolbar (hidden when printing) --}}
    <div class="toolbar no-print">
        <div style="display: flex; align-items: center; gap: 10px;">
            <a href="{{ route('workers.show', $worker) }}" class="btn btn-back">← Back</a>

            @if($recentBatches->isNotEmpty())
                <select onchange="if(this.value) window.location.href = this.value">
                    <option value="">📦 Switch Batch</option>
                    @foreach($recentBatches as $b)
                        <option value="{{ route('assignments.challan', ['worker' => $worker->id, 'batch' => $b->batch_id]) }}"
                            {{ ($batchInfo['id'] ?? '') === $b->batch_id ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::parse($b->assigned_at)->format('d M Y, h:i A') }} — {{ $b->meter_count }} meters
                        </option>
                    @endforeach
                </select>
            @endif

            <a href="{{ route('assignments.challan', ['worker' => $worker->id, 'view' => 'all']) }}" class="btn btn-all" title="All meters currently with this worker">📋 All Meters</a>
        </div>
        <button onclick="window.print()" class="btn btn-print">🖨️ Print Challan</button>
    </div>

    <div class="challan-container" style="padding-bottom: 70px;">
        {{-- Success flash --}}
        @if(session('success'))
            <div class="no-print" style="background: #ecfdf5; border: 1px solid #6ee7b7; color: #065f46; padding: 10px 14px; border-radius: 6px; margin-bottom: 15px; font-size: 13px; font-weight: 600;">
                {{ session('success') }}
            </div>
        @endif

        {{-- Header --}}
        <div class="challan-header">
            <h1>📋 Material Assignment Challan</h1>
            <div class="sub">Meter Handover Receipt</div>
        </div>

        {{-- Info --}}
        <div class="info-grid">
            <div class="info-item">
                <span class="label">Challan No:</span>
                <span class="value">
                    @if($batchInfo)
                        CH-{{ substr($batchInfo['id'], 0, 8) }}
                    @else
                        CH-ALL-{{ $worker->id }}
                    @endif
                </span>
            </div>
            <div class="info-item">
                <span class="label">Date:</span>
                <span class="value">
                    @if($batchInfo && $batchInfo['date'])
                        {{ \Carbon\Carbon::parse($batchInfo['date'])->format('d M Y, h:i A') }}
                    @else
                        {{ now()->format('d M Y, h:i A') }}
                    @endif
                </span>
            </div>
            <div class="info-item">
                <span class="label">Worker:</span>
                <span class="value">{{ $worker->name }}</span>
            </div>
            <div class="info-item">
                <span class="label">Worker ID:</span>
                <span class="value">#{{ $worker->id }}</span>
            </div>
            <div class="info-item">
                <span class="label">Issued By:</span>
                <span class="value">{{ Auth::user()->name }}</span>
            </div>
            <div class="info-item">
                <span class="label">Type:</span>
                <span class="value">
                    @if($batchInfo)
                        Assignment Batch
                    @else
                        All Active Meters
                    @endif
                </span>
            </div>
        </div>

        {{-- Summary --}}
        <div class="summary-bar">
            <div class="summary-item">
                <div class="count">{{ $meters->count() }}</div>
                <div class="lbl">Total Meters</div>
            </div>
            @if($groupCounts->isNotEmpty())
                @foreach($groupCounts->take(3) as $grp => $cnt)
                <div class="summary-item">
                    <div class="count" style="font-size: 18px;">{{ $cnt }}</div>
                    <div class="lbl">{{ $grp }}</div>
                </div>
                @endforeach
            @endif
        </div>

        @if($meters->isEmpty())
            <div style="text-align: center; padding: 40px; color: #9ca3af; font-size: 14px;">
                <div style="font-size: 40px; margin-bottom: 10px;">📭</div>
                No meters found for this {{ $batchInfo ? 'batch' : 'worker' }}.
            </div>
        @else
            {{-- Meter Table --}}
            <table class="meter-table">
                <thead>
                    <tr>
                        <th class="sno">#</th>
                        <th>Meter Number</th>
                        <th>Group</th>
                        <th>Lot</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($meters as $index => $meter)
                        <tr>
                            <td class="sno">{{ $index + 1 }}</td>
                            <td style="font-weight: 600; font-family: monospace; font-size: 12px;">{{ $meter->meter_number }}</td>
                            <td>{{ $meter->group?->name ?? '—' }}</td>
                            <td>{{ $meter->lot?->lot_number ?? '—' }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $meter->status)) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Signatures --}}
            <div class="signatures">
                <div class="sig-box">
                    <div class="sig-line">{{ Auth::user()->name }}</div>
                    <div class="sig-role">Contractor / Issuer</div>
                </div>
                <div class="sig-box">
                    <div class="sig-line">{{ $worker->name }}</div>
                    <div class="sig-role">Worker / Receiver</div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="footer-note">
                This is a system-generated document. Both parties acknowledge the handover of {{ $meters->count() }} meter(s) as listed above.<br>
                @if($batchInfo)
                    Batch: {{ $batchInfo['id'] }} •
                @endif
                Generated on {{ now()->format('d M Y, h:i A') }} • Material Allotment System
            </div>
        @endif
    </div>
</body>
</html>
