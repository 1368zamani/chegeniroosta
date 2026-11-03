<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: 'Vazirmatn', Tahoma, sans-serif; background: #f1f5f9; color: #1e293b; line-height: 1.8; }
.wrap { max-width: 720px; margin: 2rem auto; padding: 0 1rem; }
.card { background: #fff; border-radius: 14px; box-shadow: 0 4px 20px rgba(0,0,0,.06); padding: 2rem; }
.back { display: inline-block; margin-bottom: 1rem; color: #2e7d32; text-decoration: none; font-size: .9rem; }
.back:hover { text-decoration: underline; }
h1 { color: #2e7d32; font-size: 1.5rem; margin-bottom: .5rem; }
.subtitle { color: #64748b; font-size: .9rem; margin-bottom: 1.5rem; }
.field { margin-bottom: 1.1rem; }
label { display: block; font-size: .9rem; margin-bottom: .4rem; font-weight: 500; }
.req { color: #dc2626; }
input, select, textarea { width: 100%; padding: .7rem .85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 1rem; font-family: inherit; background: #f8fafc; transition: border .15s, box-shadow .15s; }
input:focus, select:focus, textarea:focus { outline: none; border-color: #2e7d32; background: #fff; box-shadow: 0 0 0 3px rgba(46,125,50,.15); }
textarea { min-height: 130px; resize: vertical; }
button, .btn { width: 100%; padding: .85rem; background: #2e7d32; color: #fff; border: none; border-radius: 8px; font-size: 1rem; font-family: inherit; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-block; text-align: center; transition: background .15s; }
button:hover, .btn:hover { background: #1b5e20; color: #fff; }
.btn-secondary { background: #f1f5f9; color: #2e7d32; }
.btn-secondary:hover { background: #e2e8f0; color: #1b5e20; }
.errors { background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; border-radius: 8px; padding: .75rem 1rem; margin-bottom: 1rem; font-size: .875rem; }
.errors ul { list-style: none; }
.hint { font-size: .8rem; color: #64748b; margin-top: .25rem; }
.actions { display: flex; gap: .5rem; margin-top: 1rem; flex-wrap: wrap; }
.actions a { flex: 1; min-width: 140px; }
.track-code { display: inline-block; background: #f1f8e9; border: 2px dashed #2e7d32; color: #1b5e20; padding: .75rem 1.5rem; border-radius: 10px; font-size: 1.4rem; font-weight: 700; letter-spacing: 1px; direction: ltr; margin: 1rem 0; }
.status-badge { display: inline-block; padding: .35rem .9rem; border-radius: 20px; font-size: .85rem; font-weight: 600; }
.status-submitted { background: #e2e8f0; color: #475569; }
.status-reviewing, .status-assigned, .status-processing { background: #fef3c7; color: #92400e; }
.status-resolved { background: #d1fae5; color: #065f46; }
.status-rejected { background: #fee2e2; color: #991b1b; }
.info-row { display: flex; padding: .6rem 0; border-bottom: 1px solid #e2e8f0; }
.info-row:last-child { border-bottom: none; }
.info-row .k { width: 140px; color: #64748b; font-size: .9rem; }
.info-row .v { flex: 1; font-weight: 500; }
</style>
