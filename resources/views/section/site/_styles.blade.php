<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: 'Vazirmatn', Tahoma, sans-serif; background: #f7f0e6; color: #2b2119; line-height: 1.8; }
.wrap { max-width: 720px; margin: 2rem auto; padding: 0 1rem; }
.card { background: #fff; border-radius: 14px; box-shadow: 0 4px 20px rgba(0,0,0,.06); padding: 2rem; }
.back { display: inline-block; margin-bottom: 1rem; color: #c94a2b; text-decoration: none; font-size: .9rem; }
.back:hover { text-decoration: underline; }
h1 { color: #c94a2b; font-size: 1.5rem; margin-bottom: .5rem; }
.subtitle { color: #6b5a4a; font-size: .9rem; margin-bottom: 1.5rem; }
.field { margin-bottom: 1.1rem; }
label { display: block; font-size: .9rem; margin-bottom: .4rem; font-weight: 500; }
.req { color: #b3401f; }
input, select, textarea { width: 100%; padding: .7rem .85rem; border: 1px solid #e6d8c3; border-radius: 8px; font-size: 1rem; font-family: inherit; background: #fffdf9; transition: border .15s, box-shadow .15s; }
input:focus, select:focus, textarea:focus { outline: none; border-color: #c94a2b; background: #fff; box-shadow: 0 0 0 3px rgba(201,74,43,.15); }
textarea { min-height: 130px; resize: vertical; }
button, .btn { width: 100%; padding: .85rem; background: #c94a2b; color: #fff; border: none; border-radius: 8px; font-size: 1rem; font-family: inherit; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-block; text-align: center; transition: background .15s; }
button:hover, .btn:hover { background: #8e2f18; color: #fff; }
.btn-secondary { background: #f7f0e6; color: #c94a2b; }
.btn-secondary:hover { background: #efe2d1; color: #8e2f18; }
.errors { background: #fdeceb; border: 1px solid #f6d5d2; color: #8c1d17; border-radius: 8px; padding: .75rem 1rem; margin-bottom: 1rem; font-size: .875rem; }
.errors ul { list-style: none; }
.hint { font-size: .8rem; color: #6b5a4a; margin-top: .25rem; }
.actions { display: flex; gap: .5rem; margin-top: 1rem; flex-wrap: wrap; }
.actions a { flex: 1; min-width: 140px; }
.track-code { display: inline-block; background: #fff3e6; border: 2px dashed #c94a2b; color: #8e2f18; padding: .75rem 1.5rem; border-radius: 10px; font-size: 1.4rem; font-weight: 700; letter-spacing: 1px; direction: ltr; margin: 1rem 0; }
.status-badge { display: inline-block; padding: .35rem .9rem; border-radius: 20px; font-size: .85rem; font-weight: 600; }
.status-submitted { background: #efe2d1; color: #6b5a4a; }
.status-reviewing, .status-assigned, .status-processing { background: #fdf1dc; color: #a8730f; }
.status-resolved { background: #fdeee7; color: #5c2313; }
.status-rejected { background: #fdeceb; color: #991b1b; }
.info-row { display: flex; padding: .6rem 0; border-bottom: 1px solid #efe2d1; }
.info-row:last-child { border-bottom: none; }
.info-row .k { width: 140px; color: #6b5a4a; font-size: .9rem; }
.info-row .v { flex: 1; font-weight: 500; }
</style>
