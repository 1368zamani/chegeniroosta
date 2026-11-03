<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>مدیریت رمز کاربران</title>

<style>
    body { font-family: tahoma, Arial, sans-serif; background: #f4f6f8; color: #263238; margin: 0; }
    .header { background: #2e7d32; color: white; padding: 20px 30px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
    .header h2 { margin: 0; font-size: 20px; }
    .header a { color: #fff; text-decoration: none; background: rgba(255,255,255,0.2); padding: 8px 15px; border-radius: 6px; font-size: 14px; transition: 0.3s; }
    .header a:hover { background: rgba(255,255,255,0.4); }
    .container { padding: 30px; max-width: 1200px; margin: 0 auto; }
    
    .tools-bar { display: flex; flex-wrap: wrap; gap: 15px; margin-bottom: 20px; justify-content: space-between; align-items: center; background: white; padding: 15px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
    .search-box { flex: 1; min-width: 250px; padding: 10px 15px; border: 1px solid #cfd8dc; border-radius: 8px; font-family: inherit; font-size: 14px; outline: none; transition: 0.3s; }
    .search-box:focus { border-color: #2e7d32; box-shadow: 0 0 0 3px rgba(46,125,50,0.1); }
    
    .tabs { display: flex; gap: 10px; flex-wrap: wrap; }
    .tab-btn { background: #e0e0e0; border: none; padding: 8px 16px; border-radius: 20px; cursor: pointer; font-family: inherit; font-size: 14px; transition: 0.3s; color: #455a64; }
    .tab-btn:hover { background: #cfd8dc; }
    .tab-btn.active { background: #2e7d32; color: white; font-weight: bold; box-shadow: 0 2px 5px rgba(46,125,50,0.3); }

    table { width: 100%; border-collapse: collapse; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
    th { background: #e8f5e9; color: #1b5e20; padding: 15px; text-align: right; font-weight: bold; border-bottom: 2px solid #c8e6c9; }
    td { padding: 12px 15px; border-bottom: 1px solid #eceff1; vertical-align: middle; }
    tr:hover { background-color: #f9fbe7; }
    
    input[type="password"] { padding: 8px; border: 1px solid #cfd8dc; border-radius: 5px; font-family: inherit; width: 150px; }
    button.btn-reset { background: #d32f2f; color: white; border: 0; padding: 8px 15px; border-radius: 6px; cursor: pointer; font-family: inherit; transition: 0.3s; }
    button.btn-reset:hover { background: #b71c1c; }
    
    .empty-state { text-align: center; padding: 30px; color: #78909c; font-style: italic; display: none; }
</style>

</head>

<body>

<div class="header">
    <h2>🔑 مدیریت رمز کاربران</h2>
    <a href="{{ route('admin.dashboard') }}">بازگشت به داشبورد</a>
</div>

<div class="container">

    @if(session('success'))
    <div style="background:#e8f5e9; color:#2e7d32; padding:15px; border-radius:8px; margin-bottom:20px; border-right:4px solid #2e7d32;">
        {{ session('success') }}
    </div>
    @endif

    <div class="tools-bar">
        <input type="text" id="searchInput" class="search-box" placeholder="🔍 جستجوی نام کاربر، نام کاربری یا روستا...">
        
        <div class="tabs">
            <button class="tab-btn active" data-section="all">همه</button>
            <button class="tab-btn" data-section="markazi">بخش مرکزی</button>
            <button class="tab-btn" data-section="visian">بخش ویسیان</button>
            <button class="tab-btn" data-section="shahivand">بخش شاهیوند</button>
            <button class="tab-btn" data-section="other">سایر</button>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>نام</th>
                <th>نام کاربری</th>
                <th>نقش / بخش</th>
                <th>رمز جدید</th>
                <th>عملیات</th>
            </tr>
        </thead>
        <tbody id="userTableBody">
            @foreach($users as $user)
                @php
                    $sectionText = 'نامشخص';
                    $sectionKey = 'other';
                    
                    if ($user->role === 'village_admin' && $user->village) {
                        $sectionText = $user->village->section ?? 'نامشخص';
                        if (str_contains($sectionText, 'مرکزی')) $sectionKey = 'markazi';
                        elseif (str_contains($sectionText, 'ویسیان')) $sectionKey = 'visian';
                        elseif (str_contains($sectionText, 'شاهیوند')) $sectionKey = 'shahivand';
                    } elseif ($user->role === 'section_admin') {
                        $sectionText = 'مدیر بخش';
                        $sectionKey = 'other';
                    }
                    
                    $roleDisplay = $user->role;
                    if ($user->role === 'fee_officer') {
                        $roleDisplay = $user->officer_type === 'financial' ? '👨‍💼 کارشناس مالی' : ($user->officer_type === 'technical' ? '🏗 کارشناس فنی' : 'کارشناس');
                    } elseif ($user->role === 'village_admin') {
                        $roleDisplay = 'مدیر روستا (' . ($user->village->name ?? 'بدون روستا') . ')';
                    } elseif ($user->role === 'section_admin') {
                        $roleDisplay = 'مدیر بخش';
                    } elseif ($user->role === 'super_admin') {
                        $roleDisplay = 'سوپر ادمین';
                    }
                @endphp
                
                <tr data-section="{{ $sectionKey }}" data-search="{{ strtolower($user->name . ' ' . $user->username . ' ' . $roleDisplay) }}">
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->username }}</td>
                    <td>{{ $roleDisplay }} <br><small style="color:#78909c;">{{ $sectionText }}</small></td>
                    <td>
                        <form method="POST" action="{{ route('admin.passwords.reset',$user->id) }}" style="display:flex; gap:10px; margin:0;">
                            @csrf
                            <input type="password" name="password" placeholder="رمز جدید" required>
                            <button type="submit" class="btn-reset">تغییر رمز</button>
                        </form>
                    </td>
                    <td>—</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
    <div id="emptyState" class="empty-state">
        هیچ کاربری با این مشخصات یافت نشد.
    </div>

</div>

<script>
    const searchInput = document.getElementById('searchInput');
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tableRows = document.querySelectorAll('#userTableBody tr');
    const emptyState = document.getElementById('emptyState');
    
    let currentSection = 'all';
    let currentSearch = '';

    function filterTable() {
        let visibleCount = 0;
        
        tableRows.forEach(row => {
            const sectionMatch = (currentSection === 'all' || row.dataset.section === currentSection);
            const searchMatch = (currentSearch === '' || row.dataset.search.includes(currentSearch.toLowerCase()));
            
            if (sectionMatch && searchMatch) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
        
        emptyState.style.display = (visibleCount === 0 && tableRows.length > 0) ? 'block' : 'none';
    }

    // Search event
    searchInput.addEventListener('input', (e) => {
        currentSearch = e.target.value.trim();
        filterTable();
    });

    // Tab event
    tabBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            tabBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            currentSection = btn.dataset.section;
            filterTable();
        });
    });
</script>

</body>
</html>
