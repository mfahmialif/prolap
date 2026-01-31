<div class="card" id="card_file_{{ $id }}">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6 d-flex align-items-center">
                @if ($dokumen)
                    <span class="bg-success p-2 rounded">
                        <i class="fas fa-folder-open text-warning"></i>
                    </span>
                @else
                    <span class="bg-danger p-2 rounded">
                        <i class="fas fa-folder-open text-warning"></i>
                    </span>
                @endif
                <div class="ml-3">
                    <h5 class="mb-0 pb-0">{{ $nama }}</h5>
                    @if ($dokumen)
                        <small class="fw-bold text-success">Sudah Upload</small>
                        - <a href="{{ $link }}" class="text-secondary text-decoration-none"
                            target="_blank"><u>Lihat
                                Berkas
                                <i class="fas
            fa-external-link-alt"></i></u></a>
                        <br>
                        <small><span class="text-danger">*</span>Silahkan upload ulang jika
                            ingin
                            merubah file
                        </small>
                    @else
                        <small class="fw-bold text-danger">*Wajib</small>
                        <small class="fw-bold text-danger">| Belum Upload</small>
                    @endif
                </div>
            </div>
            <div class="col-md-6 d-flex align-items-end justify-content-end py-2">
                @if ($dokumen)
                    <form action="" onsubmit="deleteData(event)" method="POST">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="id" value="{{ $id }}">
                        <input type="hidden" name="nama" value="{{ $nama }}">
                        <button type="submit" class="btn btn-sm btn-danger mr-2">
                            <i class="fas fa-trash"></i>
                            Delete
                        </button>
                    </form>
                @endif
                <button class="btn btn-sm btn-success btnDoc" data-toggle="modal" data-id="{{ $id }}"
                    data-ket="{{ $ket }}" data-ext="{{ $extension }}" data-nama="{{ $nama }}"><i class="fas fa-upload"></i>
                    Upload
                    File</button>
            </div>
        </div>
    </div>
</div>
