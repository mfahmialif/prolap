@push('script')
    <script>
        var stateDzDeleteLocalEdit{{ $id }} = false;
        var uploadedImageMapDokumenEdit{{ $id }} = {};
        var nFileUploadDokumenEdit{{ $id }} = 1;
        var myDropzoneDokumenEdit{{ $id }} = null;

        if (window.Dropzone && !Dropzone.discovered) {
            Dropzone.autoDiscover = false;
        }

        createDropZoneEdit{{ $id }}();

        function createDropZoneEdit{{ $id }}() {
            uploadedImageMapDokumenEdit{{ $id }} = {};
            nFileUploadDokumenEdit{{ $id }} = 1;
            myDropzoneDokumenEdit{{ $id }} = new Dropzone($('#image-dropzone-edit{{ $id }}').get(0), {
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
                    return `(${nFileUploadDokumenEdit{{ $id }}++}) ${file}`;
                },
                init: function() {
                    this.on("sending", function(file, xhr, formData) {
                        formData.append("nama", "{{ $posko->nama }}");
                    });
                },
                success: function(file, response) {
                    $('#form_edit{{ $id }}').append(
                        '<input type="text" name="dokumen[]" class="dokumen" value="' +
                        response.name +
                        '">')
                    uploadedImageMapDokumenEdit{{ $id }}[file.upload.filename] = response.name;
                    saveDokumenSisa(response.name);
                    $('#form_submit_edit{{ $id }}').prop("disabled", false);
                },
                removedfile: function(file) {
                    file.previewElement.remove()

                    if (stateDzDeleteLocalEdit{{ $id }}) {
                        var name = ''
                        if (typeof file.file_name !== 'undefined') {
                            name = file.file_name
                        } else {
                            name = uploadedImageMapDokumenEdit{{ $id }}[file.upload.filename]
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
                                $('#form_edit{{ $id }}').find(
                                        'input[name="dokumen[]"][value="' + name + '"]')
                                    .remove();
                                deleteDokumenSisa(response.name);
                            }
                        });
                    } else {
                        $('#form_edit{{ $id }}').find(
                                'input[name="dokumen[]"]')
                            .remove();
                    }
                },
                uploadprogress: function(file, progress, bytesSent) {
                    if (file.previewElement) {
                        var progressElement = file.previewElement.querySelector("[data-dz-uploadprogress]");
                        progressElement.style.width = progress + "%";
                    }
                    $('#form_submit_edit{{ $id }}').prop("disabled", true);
                }
            });
            myDropzoneDokumenEdit{{ $id }}.on("addedfile", function(file) {
                stateDzDeleteLocalEdit{{ $id }} = true;
                file.previewElement.querySelector('[data-dz-name]').textContent = file.upload.filename;
            });
        }
    </script>
@endpush
