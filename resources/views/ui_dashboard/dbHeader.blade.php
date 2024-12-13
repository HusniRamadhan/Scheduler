<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Halaman @yield('title')</h1>
            </div>
            <div class="col-sm-6">
                {{-- Header Admin --}}
                @if (Auth::user()->role == 'admin')
                    <ol class="breadcrumb float-sm-right">
                        @if (\Request::route()->getName() == 'admin')
                            <li class="breadcrumb-item active">Home</li>
                        @elseif (\Request::route()->getName() != 'admin')
                            <li class="breadcrumb-item"><a href="/admin">Home</a></li>
                            @if (\Request::route()->getName() == 'adminProfile')
                                <li class="breadcrumb-item active">Profile</li>
                            @endif
                            @if (\Request::route()->getName() == 'adminPraLirs')
                                <li class="breadcrumb-item active">Pra Lirs</li>
                            @endif
                            @if (\Request::route()->getName() == 'adminScheduling')
                                <li class="breadcrumb-item active">Penjadwalan</li>
                            @endif
                        @endif
                    </ol>
                    {{-- Header User --}}
                @elseif (Auth::user()->role == 'member')
                    <ol class="breadcrumb float-sm-right">
                        @if (\Request::route()->getName() == 'user')
                            <li class="breadcrumb-item active">Home</li>
                        @elseif (\Request::route()->getName() != 'user')
                            <li class="breadcrumb-item"><a href="/admin">Home</a></li>
                            @if (\Request::route()->getName() == 'userProfile')
                                <li class="breadcrumb-item active">Profile</li>
                            @endif
                            @if (\Request::route()->getName() == 'userPraLirs')
                                <li class="breadcrumb-item active">Pra Lirs</li>
                            @endif
                            @if (\Request::route()->getName() == 'userInput')
                                <li class="breadcrumb-item active">Input Pra Lirs</li>
                            @endif
                            @if (\Request::route()->getName() == 'userSubject')
                                <li class="breadcrumb-item active">Mata Kuliah</li>
                            @endif
                        @endif
                    </ol>
                @endif
            </div>
        </div>
    </div>
</div>
