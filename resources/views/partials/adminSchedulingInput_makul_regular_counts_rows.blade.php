@foreach ($makulColumns as $column)
    <div class="col">
        @foreach ($column as $makul)
            <div class="info-box-content">
                <span class="info-box-text">{{ $makul->mata_kuliah }}: {{ $makul->jumlah_kelas ?? '0' }} Kelas</span>
            </div>
        @endforeach
    </div>
@endforeach
