@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}
    <button class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show">
    {{ session('error') }}
    <button class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('warning'))
<div class="alert alert-warning alert-dismissible fade show">
    {{ session('warning') }}
    <button class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('info'))
<div class="alert alert-info alert-dismissible fade show">
    {{ session('info') }}
    <button class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif