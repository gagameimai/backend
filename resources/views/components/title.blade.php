<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-12 float-right">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">首頁</li>
                    <li class="breadcrumb-item">{{ $title['area'] }}</li>

                    @if (!empty($title['unit']))
                        <li class="breadcrumb-item">{{ $title['unit'] }}</li>
                    @endif

                    @if (!empty($title['depend']))
                        <li class="breadcrumb-item">{{ $title['depend'] }}</li>
                    @endif
                </ol>
            </div>
        </div>
    </div>
</div>
