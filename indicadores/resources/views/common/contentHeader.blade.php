<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-start mb-0">{{ $pageTitle }}</h2>
                @if(isset($pageSubtitle))
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">{{ $pageSubtitle }}</li>
                    </ol>
                @endif
            </div>
        </div>
    </div>
</div>