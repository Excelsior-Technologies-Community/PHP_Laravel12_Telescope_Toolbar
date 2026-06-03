<div id="laravel-telescope-toolbar" style="position: fixed; bottom: 0; left: 0; right: 0; height: 40px; background: #1f2937; color: #f3f4f6; border-top: 2px solid #3b82f6; display: flex; align-items: center; justify-content: space-between; padding: 0 15px; font-family: monospace; font-size: 13px; z-index: 999999; box-shadow: 0 -2px 10px rgba(0,0,0,0.3);">
    
    <div style="display: flex; align-items: center; gap: 20px;">
        <span style="color: #3b82f6; font-weight: bold; display: flex; align-items: center; gap: 5px;">
            <svg style="width:16px;height:16px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            Laravel 12
        </span>
        
        <span title="Execution Time" style="display: flex; align-items: center; gap: 5px;">
            <svg style="width:14px;height:14px;color:#9ca3af" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span style="color: #34d399; font-weight: bold;">{{ $time }} ms</span>
        </span>

        <span title="Peak Memory Usage" style="display: flex; align-items: center; gap: 5px;">
            <svg style="width:14px;height:14px;color:#9ca3af" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            <span style="color: #60a5fa; font-weight: bold;">{{ $memory }} MB</span>
        </span>
    </div>

    <div style="display: flex; align-items: center; gap: 15px;">
        <div style="position: relative; display: inline-block;" class="tb-dropdown">
            <button onclick="toggleLtPanel('lt-query-panel')" style="background: none; border: none; color: #f3f4f6; cursor: pointer; display: flex; align-items: center; gap: 6px; font-family: monospace; font-size: 13px;">
                <svg style="width:14px;height:14px;color:#9ca3af" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.58 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.58 4 8 4s8-1.79 8-4M4 7c0-2.21 3.58-4 8-4s8 1.79 8 4m0 5c0 2.21-3.58 4-8 4s-8-1.79-8-4"/></svg>
                <span>Queries: <strong style="color:#f59e0b;">{{ $queriesCount }}</strong></span>
                @if($duplicateQueries > 0)
                    <span style="background: #ef4444; color: white; padding: 1px 6px; border-radius: 4px; font-size: 11px; font-weight: bold; animation: pulse 2s infinite;">{{ $duplicateQueries }} DUP</span>
                @endif
            </button>
        </div>

        <button onclick="toggleLtPanel('lt-payload-panel')" style="background: #374151; border: 1px solid #4b5563; color: #f3f4f6; cursor: pointer; padding: 4px 10px; border-radius: 4px; font-family: monospace; font-size: 12px; font-weight: bold; display: flex; align-items: center; gap: 4px;">
            <svg style="width:14px;height:14px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
            Payload Toggle
        </button>
        
        <a href="/telescope" target="_blank" style="color: #9ca3af; text-decoration: none; display: flex; align-items: center;" title="Open Telescope Dashboard">
            <svg style="width:16px;height:16px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
        </a>
    </div>
</div>

<div id="lt-query-panel" style="position: fixed; bottom: 40px; left: 0; right: 0; height: 260px; background: #111827; border-top: 1px solid #374151; display: none; z-index: 999998; overflow-y: auto; padding: 15px; font-family: monospace; box-shadow: 0 -5px 15px rgba(0,0,0,0.2);">
    <div style="display:flex;justify-content:between;align-items:center;border-bottom:1px solid #374151;padding-bottom:8px;margin-bottom:10px;">
        <span style="color:#f59e0b;font-weight:bold;">Database Queries History (Last 30)</span>
        <button onclick="closeLtPanels()" style="background:none;border:none;color:#9ca3af;cursor:pointer;font-size:16px;">&times;</button>
    </div>
    @if(empty($queriesList))
        <p style="color:#6b7280;">No database query records found in telescope entries.</p>
    @else
        <table style="width:100%;border-collapse:collapse;font-size:12px;color:#d1d5db;">
            <thead>
                <tr style="text-align:left;color:#9ca3af;border-bottom:1px solid #1f2937;">
                    <th style="padding:6px;">SQL Query Query Statement</th>
                    <th style="padding:6px;width:80px;">Time</th>
                </tr>
            </thead>
            <tbody>
                @foreach($queriesList as $ql)
                    <tr style="border-bottom:1px solid #1f2937;hover:background:#1f2937;">
                        <td style="padding:6px;word-break:break-all;color:#e5e7eb;">{{ $ql['sql'] }}</td>
                        <td style="padding:6px;color:#34d399;">{{ $ql['time'] }}ms</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

<div id="lt-payload-panel" style="position: fixed; bottom: 40px; left: 0; right: 0; height: 280px; background: #111827; border-top: 1px solid #374151; display: none; z-index: 999998; overflow-y: auto; padding: 15px; font-family: monospace; box-shadow: 0 -5px 15px rgba(0,0,0,0.2);">
    <div style="display:flex;justify-content:between;align-items:center;border-bottom:1px solid #374151;padding-bottom:8px;margin-bottom:10px;">
        <span style="color:#60a5fa;font-weight:bold;">Request & Response Payload Data Viewer</span>
        <button onclick="closeLtPanels()" style="background:none;border:none;color:#9ca3af;cursor:pointer;font-size:16px;">&times;</button>
    </div>
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; font-size: 12px;">
        <div>
            <h4 style="color:#9ca3af;margin:5px 0;">HTTP Request Info</h4>
            <p style="margin:4px 0;"><strong style="color:#34d399;">Method:</strong> {{ $payload['method'] }}</p>
            <p style="margin:4px 0;word-break:break-all;"><strong style="color:#34d399;">URL:</strong> {{ $payload['url'] }}</p>
            
            <h4 style="color:#9ca3af;margin:10px 0 5px 0;">Submitted Form Input Payload</h4>
            <pre style="background:#1f2937;padding:8px;border-radius:4px;overflow-x:auto;color:#e5e7eb;margin:0;">{{ count($payload['input']) ? json_encode($payload['input'], JSON_PRETTY_PRINT) : '[] No form input inputs' }}</pre>
        </div>
        <div>
            <h4 style="color:#9ca3af;margin:5px 0;">Active Session Payload Dump</h4>
            <pre style="background:#1f2937;padding:8px;border-radius:4px;max-height:180px;overflow-y:auto;color:#e5e7eb;margin:0;">{{ json_encode($payload['session'], JSON_PRETTY_PRINT) }}</pre>
        </div>
    </div>
</div>

<script>
    function toggleLtPanel(id) {
        const panel = document.getElementById(id);
        const isVisible = panel.style.display === 'block';
        closeLtPanels();
        if (!isVisible) {
            panel.style.display = 'block';
        }
    }
    function closeLtPanels() {
        document.getElementById('lt-query-panel').style.display = 'none';
        document.getElementById('lt-payload-panel').style.display = 'none';
    }
</script>

<style>
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: .6; }
    }
</style>