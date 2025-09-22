<div class="table-container">
    <div class="tabler-responsive">
        <table class="table">
            <thead>
                {{ $thead }}
            </thead>
            <tbody>
                {{ $tbody }}
            </tbody>
        </table>
    </div>
    @isset($paginator)
    <div class="d-flex justify-content-end mt-4"></div>
    @endisset
</div>
//pageheader
<div class="page-header">
    <div class="page-title align-items-center">
        <div>
            <div class="page-title-icon">
                <i class="bi bi-bag-check"></i>
            </div>
            <div>
                <h2>{{W$title}}</h2>
                <p class="text-muted mb-0">{{$subtitle}}</p>
            </div>
        </div>
        <div>
            {{ $action ?? '' }}
        </div>
    </div>
</div>