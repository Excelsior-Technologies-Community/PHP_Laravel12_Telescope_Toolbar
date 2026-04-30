<div style="
    position:fixed;
    bottom:0;
    left:0;
    right:0;
    background:#111;
    color:#fff;
    padding:12px;
    font-size:13px;
    z-index:9999;
    display:flex;
    justify-content:space-between;
    flex-wrap:wrap;
">
    <div>
        <strong>🚀 Telescope Toolbar</strong> |
        <a href="/telescope" style="color:#00ff99;">Open Telescope</a>
    </div>

    <div>
        ⏱ Time: {{ $executionTime }} ms |
        🧠 Memory: {{ $memoryUsage }} MB |
        📡 {{ $method }} |
        🔗 {{ $url }} |
        ✅ Status: {{ $status }}
    </div>

    <div>
        🕒 {{ $time }}
    </div>
</div>