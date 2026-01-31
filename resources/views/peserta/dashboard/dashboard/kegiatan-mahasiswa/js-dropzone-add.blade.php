@push('script')
    <script>
        var stateDzDeleteLocal{{ $id }} = false;
        var uploadedImageMapDokumen{{ $id }} = {};
        var nFileUploadDokumen{{ $id }} = 1;
        var myDropzoneDokumen{{ $id }} = null;

        if (window.Dropzone && !Dropzone.discovered) {
            Dropzone.autoDiscover = false;
        }

        createDropZoneAdd{{ $id }}();

        function createDropZoneAdd{{ $id }}() {
            uploadedImageMapDokumen{{ $id }} = {};
            nFileUploadDokumen{{ $id }} = 1;
            myDropzoneDokumen{{ $id }} = new Dropzone($('#image-dropzone{{ $id }}').get(0), {
                url: "{{ route('peserta.dashboard.fileUpload') }}",
                maxFilesize: 10, // MB
                maxFiles: 10,
                addRemoveLinks: true,
                timeout: 180000,
                // autoProcessQueue: false,
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                renameFilename: function(file) {
                    return `(${nFileUploadDokumen{{ $id }}++}) ${file}`;
                },
                init: function() {
                    this.on("sending", function(file, xhr, formData) {
                        formData.append("nama", "{{ $posko->nama }}");
                    });
                },
                success: function(file, response) {
                    $('#form_add{{ $id }}').append(
                        '<input type="text" name="dokumen[]" class="dokumen" value="' +
                        response.name +
                        '">')
                    uploadedImageMapDokumen{{ $id }}[file.upload.filename] = response.name;
                    saveDokumenSisa(response.name);
                    $('#form_submit{{ $id }}').prop("disabled", false);
                },
                removedfile: function(file) {
                    file.previewElement.remove()

                    if (stateDzDeleteLocal{{ $id }}) {
                        var name = ''
                        if (typeof file.file_name !== 'undefined') {
                            name = file.file_name
                        } else {
                            name = uploadedImageMapDokumen{{ $id }}[file.upload.filename]
                        }
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': "{{ csrf_token() }}"
                            },
                            type: "post",
                            url: "{{ route('peserta.dashboard.fileDelete') }}",
                            data: {
                                dokumen: name
                            },
                            success: function(response) {
                                $('#form_add{{ $id }}').find(
                                        'input[name="dokumen[]"][value="' + name + '"]')
                                    .remove();
                                deleteDokumenSisa(response.name);
                            }
                        });
                    } else {
                        $('#form_add{{ $id }}').find(
                                'input[name="dokumen[]"]')
                            .remove();
                    }
                },
                uploadprogress: function(file, progress, bytesSent) {
                    if (file.previewElement) {
                        var progressElement = file.previewElement.querySelector("[data-dz-uploadprogress]");
                        progressElement.style.width = progress + "%";
                    }
                    $('#form_submit{{ $id }}').prop("disabled", true);
                }
            });
            myDropzoneDokumen{{ $id }}.on("addedfile", function(file) {
                stateDzDeleteLocal{{ $id }} = true;
                file.previewElement.querySelector('[data-dz-name]').textContent = file.upload.filename;
            });
        }
    </script>
@endpush
