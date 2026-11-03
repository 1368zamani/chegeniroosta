@extends('admin.users.layout')

@section('content')

<style>
    .tools-bar { display: flex; flex-wrap: wrap; gap: 15px; margin-bottom: 20px; justify-content: space-between; align-items: center; background: #fff; padding: 15px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
    .search-box { flex: 1; min-width: 250px; padding: 10px 15px; border: 1px solid #cfd8dc; border-radius: 8px; font-family: inherit; font-size: 14px; outline: none; transition: 0.3s; }
    .search-box:focus { border-color: #2e7d32; box-shadow: 0 0 0 3px rgba(46,125,50,0.1); }
    
    .filter-group { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; margin-bottom: 15px; background: #fff; padding: 10px 15px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
    .filter-group span { font-weight: bold; color: #555; font-size: 14px; margin-left: 10px; }
    
    .tab-btn { background: #e0e0e0; border: none; padding: 8px 16px; border-radius: 20px; cursor: pointer; font-family: inherit; font-size: 14px; transition: 0.3s; color: #455a64; }
    .tab-btn:hover { background: #cfd8dc; }
    .tab-btn.active { background: #2e7d32; color: white; font-weight: bold; box-shadow: 0 2px 5px rgba(46,125,50,0.3); }
    .tab-btn.role-active { background: #1565c0; color: white; }
    
    .empty-state { text-align: center; padding: 30px; color: #78909c; font-style: italic; display: none; background: #fff; border-radius: 10px; }
</style>

<div class="page-title">
    <h2>👥 کاربران سامانه</h2>
</div>

<div style="margin-bottom:20px;">
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
        ➕ افزودن کاربر
    </a>
</div>

<!-- Search Box -->
<div class="tools-bar">
    <input type="text" id="searchInput" class="search-box" placeholder="🔍 جستجوی نام، ایمیل یا نام روستا...">
</div>

<!-- Section Tabs -->
<div class="filter-group">
    <span>بخش‌ها:</span>
    <button class="tab-btn section-btn active" data-section="all">همه</button>
    <button class="tab-btn section-btn" data-section="markazi">بخش مرکزی</button>
    <button class="tab-btn section-btn" data-section="visian">بخش ویسیان</button>
    <button class="tab-btn section-btn" data-section="shahivand">بخش شاهیوند</button>
</div>

<!-- Role Tabs -->
<div class="filter-group">
    <span>نقش‌ها:</span>
    <button class="tab-btn role-btn active" data-role="all">همه کاربران</button>
    <button class="tab-btn role-btn" data-role="village_admin">مدیران روستا</button>
    <button class="tab-btn role-btn" data-role="fee_financial">کارشناسان مالی</button>
    <button class="tab-btn role-btn" data-role="fee_technical">کارشناسان عمرانی</button>
</div>

<div class="card">
    <table id="usersTable">
        <thead>
            <tr>
                <th>نام</th>
                <th>ایمیل</th>
                <th>نقش</th>
                <th>بخش</th>
                <th>روستا</th>
                <th>عملیات</th>
            </tr>
        </thead>

        <tbody id="usersTableBody">
            @foreach($users as $user)
                @php
                    // تشخیص بخش
                    $sectionName = $user->section->name ?? '';
                    $sectionKey = 'other';
                    if (str_contains($sectionName, 'مرکزی')) $sectionKey = 'markazi';
                    elseif (str_contains($sectionName, 'ویسیان')) $sectionKey = 'visian';
                    elseif (str_contains($sectionName, 'شاهیوند')) $sectionKey = 'shahivand';

                    // تشخیص نقش
                    $roleKey = $user->role; // پیش‌فرض
                    if ($user->role === 'fee_officer') {
                        if ($user->officer_type === 'financial') $roleKey = 'fee_financial';
                        elseif ($user->officer_type === 'technical') $roleKey = 'fee_technical';
                        else $roleKey = 'fee_other';
                    }
                    
                    // نقش برای جستجو
                    $roleLabel = $user->role_label ?? '-';
                    $villageName = $user->village->name ?? '';
                @endphp
                
                <tr data-section="{{ $sectionKey }}" data-role="{{ $roleKey }}" data-search="{{ strtolower($user->name . ' ' . $user->email . ' ' . $sectionName . ' ' . $villageName . ' ' . $roleLabel) }}">
                    <td>{{ $user->name }}</td>
                    <td dir="ltr" style="text-align:right;">{{ $user->email }}</td>
                    <td>{{ $roleLabel }}</td>
                    <td>{{ $user->section->name ?? '-' }}</td>
                    <td>{{ $user->village->name ?? '-' }}</td>
                    <td>
                        <a href="{{ route('admin.users.edit',$user->id) }}" class="btn btn-primary btn-sm">
                            ✏️ ویرایش
                        </a>
                        <form method="POST" action="{{ route('admin.users.destroy',$user->id) }}" style="display:inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('آیا از حذف این کاربر مطمئن هستید؟')">
                                🗑 حذف
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
    <div id="emptyState" class="empty-state">
        هیچ کاربری با این فیلترها یافت نشد.
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const searchInput = document.getElementById('searchInput');
        const sectionBtns = document.querySelectorAll('.section-btn');
        const roleBtns = document.querySelectorAll('.role-btn');
        const tableRows = document.querySelectorAll('#usersTableBody tr');
        const emptyState = document.getElementById('emptyState');
        
        let currentSection = 'all';
        let currentRole = 'all';
        let currentSearch = '';

        function filterTable() {
            let visibleCount = 0;
            
            tableRows.forEach(row => {
                const sectionMatch = (currentSection === 'all' || row.dataset.section === currentSection);
                const roleMatch = (currentRole === 'all' || row.dataset.role === currentRole);
                const searchMatch = (currentSearch === '' || row.dataset.search.includes(currentSearch.toLowerCase()));
                
                if (sectionMatch && roleMatch && searchMatch) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });
            
            emptyState.style.display = (visibleCount === 0 && tableRows.length > 0) ? 'block' : 'none';
        }

        // Search Event
        searchInput.addEventListener('input', (e) => {
            currentSearch = e.target.value.trim();
            filterTable();
        });

        // Section Tabs Event
        sectionBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                sectionBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                currentSection = btn.dataset.section;
                filterTable();
            });
        });

        // Role Tabs Event
        roleBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                roleBtns.forEach(b => b.classList.remove('role-active'));
                btn.classList.add('role-active');
                currentRole = btn.dataset.role;
                filterTable();
            });
        });
    });
</script>

@endsection
