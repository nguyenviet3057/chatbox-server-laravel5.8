@extends('adminlte::page')

@section('title', 'Ngữ cảnh')

@section('css')
    <style>
        /*
        *   Bootstrap 3.4.1 -> 4.6.1
        */
        .p-0 {
            padding: 0;
        }
        .p-1 {
            padding: 0.25rem !important;
        }
        .p-2 {
            padding: 0.5rem !important;
        }
        .m-0 {
            margin: 0;
        }
        .m-1 {
            margin: 0.25rem !important;
        }
        .mb-0 {
            margin-bottom: 0;
        }
        .w-100 {
            width: 100%;
        }
        .h-100 {
            height: 100%;
        }
        .d-none {
            display: none;
        }
        .d-flex {
            display: flex;
        }
        .flex-column {
            flex-direction: column;
        }
        .flex-row {
            flex-direction: row;
        }
        .row {
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;
        }
        .row > * {
            flex-shrink: 0;
            max-width: 100%;
        }
        .col-6 {
            flex: 0 0 auto;
            width: 50%;
        }
        .align-items-center {
            align-items: center;
        }
        .justify-content-center {
            justify-content: center;
        }
        .justify-content-between {
            justify-content: space-between;
        }
        .position-absolute {
            position: absolute;
        }
        .position-relative {
            position: relative;
        }
        .fw-bold {
            font-weight: bold;
        }
        .img-fluid {
            max-width: 100%;
            height: auto;
        }

        /*
        *   Main CSS
        */
        .card .card-header .fa-fw, table tr td:last-child .fa-fw {
            margin-right: 5px;
        }
        .card .card-body {
            padding: 10px;
        }

        .panel-db-tables {
            max-height: 500px;
            overflow: auto;
        }

        table td.instruction-detail {
            /* width: 100%; */
            text-overflow: ellipsis;
            overflow-wrap: break-word;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            height: 2.4em !important;
        }
        table td.faq-question, table td.faq-answer {
            max-width: 100px;
            padding: 8px;
            border: 1px solid #ddd;
            text-align: left;
            -webkit-box-orient: vertical;
            overflow: hidden;
            -webkit-line-clamp: 2;
            text-overflow: ellipsis;
        }

        .btn-fetch, .btn-download, .icon-upload {
            border: 1px solid black;
            background-color: #eeeeee;
        }

        .icon-upload {
            position: relative;
            overflow: hidden;
        }

        input.file-upload {
            position: absolute;
            top: 0;
            right: 0;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }
    </style>
@stop

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title w-100">Ngữ cảnh</h3>
            <a class="btn btn-success" href="{{ route('faq.add') }}"><i class="fa fa-fw fa-plus-circle"></i>Thêm mới</a>
        </div>
        <!-- /.card-header -->
        <div class="card-body panel faqs-table-container">
            <table id="faqs" class="table table-bordered table-striped w-100">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Câu hỏi</th>
                        <th>Trả lời</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($faqs as $faq)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td class="faq-question">{{ $faq->question }}</td>
                        <td class="faq-answer">{{ $faq->answer }}</td>
                        <td>
                            <a href="{{ route('faq.edit', ['id' => $faq->id]) }}"><i class="fa fa-fw fa-edit"></i></a>
                            <a class="delete-btn" data-id="faq-{{ $faq->id }}" role="button"><i class="fa fa-times text-danger"></i></a>
                        </td>
                    </tr>    
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title w-100">Chỉ dẫn phụ</h3>
            <a class="btn btn-success" href="{{ route('instruction.add') }}"><i class="fa fa-fw fa-plus-circle"></i>Thêm mới</a>
        </div>
        <!-- /.card-header -->
        <div class="card-body panel instructions-table-container">
            <table id="instructions" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Chỉ dẫn</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($instructions as $instruction)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td class="instruction-detail">{{ $instruction->instruction }}</td>
                        <td>
                            <a href="{{ route('instruction.edit', ['id' => $instruction->id]) }}"><i class="fa fa-fw fa-edit"></i></a>
                            <a class="delete-btn" data-id="instruction-{{ $instruction->id }}" role="button"><i class="fa fa-times text-danger"></i></a>
                        </td>
                    </tr>    
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title w-100">Dữ liệu cung cấp</h3>
            <button class="btn btn-success" id="update-data-files"><i class="fa fa-fw fa-arrow-up"></i>Cập nhật</button>
        </div>
        <!-- /.card-header -->
        <div class="card-body panel panel-db-tables">
            <table id="db-tables" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Tên file (.json)</th>
                        <th>Dữ liệu từ cơ sở dữ liệu</th>
                        <th>Dữ liệu huấn luyện</th>
                        <th>Tải lên</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($structure as $table_name => $table_column)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $table_name }}</td>
                        <td>
                            <button class='btn btn-fetch' data-table-name="{{ $table_name }}"><i class='fa fa-download fa-fw'></i><span>Lấy dữ liệu</span></button>
                        </td>
                        <td class="file-uploaded" data-table-name="{{ $table_name }}">
                        </td>
                        <td>
                            <i class='fa fa-upload btn icon-upload'>
                                <input class='file-upload' type='file'/>
                            </i>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('js')
    <script>
        $("#faqs").DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": false,
            "info": true,
            "autoWidth": true,
            "responsive": true,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Vietnamese.json"
            },
            "columnDefs": [
                { "width": "15px", "targets": 0 },
                { "width": "70px", "targets": 3 },
            ]
        })
        $("#instructions").DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": false,
            "info": true,
            "autoWidth": true,
            "responsive": true,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Vietnamese.json"
            },
            "columnDefs": [
                { "width": "15px", "targets": 0 },
                { "width": "70px", "targets": 2 },
            ]
        })

        $('.delete-btn').click(function() {
            let id = $(this).data('id');
            let csrfToken = $('input[name="_token"]').val();
            if (id.indexOf('faq-') != -1) {
                id = id.replace('faq-', '');

                let choice = confirm("Bạn chắc chắn muốn xoá ngữ cảnh này?");
                if (choice) {
                    $.post({
                        url: "{{ route('faq.delete.submit') }}",
                        data: {
                            _token: csrfToken,
                            id: id
                        },
                        success: function(result) {
                            switch (result.status) {
                                case 0:
                                    alert("Xóa ngữ cảnh thất bại");
                                    break;
                                case 1:
                                    alert("Xóa ngữ cảnh thành công");
                                    window.location.reload();
                                    break;
                            }
                        },
                        error: function(error) {
                            console.log(error);
                        }
                    });
                }
            } else if (id.indexOf('instruction-') != -1) {
                id = id.replace('instruction-', '');

                let choice = confirm("Bạn chắc chắn muốn xoá chỉ dẫn phụ này?");
                if (choice) {
                    $.post({
                        url: "{{ route('instruction.delete.submit') }}",
                        data: {
                            _token: csrfToken,
                            id: id
                        },
                        success: function(result) {
                            switch (result.status) {
                                case 0:
                                    alert("Xóa chỉ dẫn phụ thất bại");
                                    break;
                                case 1:
                                    alert("Xóa chỉ dẫn phụ thành công");
                                    window.location.reload();
                                    break;
                            }
                        },
                        error: function(error) {
                            console.log(error);
                        }
                    });
                }
            }
        });

        $(document).on('change', '.file-upload', function(event) {
            console.log($(event.target.files)[0])
            let file = $(event.target.files)[0];
            if (file.type === 'application/json') {
                var reader = new FileReader();
                
                reader.onload = function(e) {
                    var contents = e.target.result;
                    $(event.target).closest('tr').children('td:nth-child(4)').empty();
                    $(event.target).closest('tr').children('td:nth-child(4)').append(
                        `<a class='btn btn-download' href='${contents}' download='${file.name}'><i class='fa fa-download fa-fw'></i>${file.name}
                        </a>`
                    );
                };
                
                reader.readAsDataURL(file);
            }
        })
    </script>
    <script type="module">
        // Import modules
        import { initializeApp } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-app.js";
        import { getAnalytics } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-analytics.js";
        import { getAuth, connectAuthEmulator } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-auth.js";
        // import { getDatabase, connectDatabaseEmulator, ref, child, push, get, set, update, serverTimestamp, onValue, off, query, orderByChild, equalTo, limitToLast } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-database.js";
        import { getFirestore, connectFirestoreEmulator, collection, orderBy, getDocs, doc, getDoc, addDoc, setDoc, updateDoc, deleteDoc, serverTimestamp, onSnapshot, query, where } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-firestore.js";
        import { getMessaging, getToken } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-messaging.js";
    
        // Initialize Firebase
        const firebaseConfig = {
            apiKey: "AIzaSyDw2S01aViwowyyJ-A0m7pVTX8OIZF2VJU",
            authDomain: "ziczacapp.firebaseapp.com",
            databaseURL: "https://ziczacapp-default-rtdb.asia-southeast1.firebasedatabase.app",
            projectId: "ziczacapp",
            storageBucket: "ziczacapp.appspot.com",
            messagingSenderId: "1054197522212",
            appId: "1:1054197522212:web:02a6765b198580e53f9db1",
            measurementId: "G-8XQG0CX88E"
        };
        const app = initializeApp(firebaseConfig);
        const analytics = getAnalytics(app);
        // var db = getDatabase(app);
        var auth = getAuth(app);
        var fs = getFirestore(app);

        let assistant_id = "asst_XJdELsXpLgGLPRom0w5H2d4z";

        let header_auth = {
            "Authorization": "Bearer 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"
        };

        let headers = {
            "Authorization": header_auth.Authorization,
            "Content-Type": "application/json",
            "OpenAI-Beta": "assistants=v1"
        };

        function retrieveFileIds() {
            $.get({
                url: `https://api.openai.com/v1/assistants/${assistant_id}/files`,
                headers: headers,
                success: function(response) {
                    console.log(response)
                    if (Array.isArray(response.data)) {
                        response.data.forEach((item, index) => {
                            console.log(item)
                            let file_id = item.id;

                            // Retrieve file
                            $.get({
                                url: `https://api.openai.com/v1/files/${file_id}`,
                                headers: header_auth,
                                success: function(response) {
                                    console.log(response)
                                    let filename = response.filename;

                                    // Retrieve file content => Not allow for assistant files
                                    // $.get({
                                    //     url: `https://api.openai.com/v1/files/${file_id}/content`,
                                    //     headers: header_auth,
                                    //     success: function(response) {
                                    //         console.log(response);
                                    //     }
                                    // })

                                    $("#db-tables").find(`td.file-uploaded[data-table-name="${filename.split('.').slice(0, -1).join('.')}"]`).data('file-id', file_id);
                                    $("#db-tables").find(`td.file-uploaded[data-table-name="${filename.split('.').slice(0, -1).join('.')}"]`).parent().find("td:last-child").append(
                                        `<i class="fa fa-times text-danger btn icon-upload delete-btn delete-datafile" data-file-id="${file_id}" data-table-name="${filename.split('.').slice(0, -1).join('.')}"  >
                                        </i>`
                                    )
                                }
                            })
                        })
                    }
                },
                error: function(err) {
                    console.log(err);
                }
            })
        }

        $(document).ready(function() {
            // Retrieve training data
            $(".file-uploaded").each((index, element) => {
                getDoc(doc(fs, 'data_files', $(element).data("table-name"))).then((data_file) => {
                    if (data_file.exists() && data_file.data().contents != "") {
                        $(element).append(`<a class='btn btn-download' href='${data_file.data().contents}' download='${data_file.id}.json'><i class='fa fa-download fa-fw'></i>${data_file.id}.json</a>`);
                    }
                });
            })

            retrieveFileIds();
        })

        $(document).on('click', '.btn-fetch', function() {
            let btn = $(this);
            let data = {
                _token: $("input[name='_token']").val(),
                table_name: $(this).data("table-name")
            }
            $.post({
                url: "{{ route('instruction.database.data') }}",
                data: data,
                timeout: 5000,
                success: function(result) {
                    console.log(result.data);
                    if (result.status == 1) {   
                        const blob = new Blob([JSON.stringify(result.data, null, 2)], { type: 'application/json' });
                        const url = URL.createObjectURL(blob);

                        $(btn).parent().append(
                            `<a class='btn btn-download' href='${url}' download='${$(btn).data('table-name')}.json'><i class='fa fa-download fa-fw'></i>${$(btn).data('table-name')}.json</a>`
                        );
                        $(btn).remove();
                    }
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        })

        $("#update-data-files").click(function() {
            console.log($("#db-tables").find('tbody tr td.file-uploaded'))
            let file_ids = [];

            let promises = [];
            $("#db-tables").find('tbody tr td.file-uploaded').each((index, element) => {
                let file_id = $(element).data('file-id');
                if ($(element).find('a').length > 0) {
                    setDoc(doc(fs, 'data_files', $(element).data("table-name")), {
                        contents: $(element).find('a').prop("href")
                    });

                    var fileDataUrl = $(element).find('a').prop("href");
                    var contentType = "application/json"; // Đổi thành loại dữ liệu mong muốn nếu cần
                    var base64Data = fileDataUrl.split(",")[1]; // Lấy phần base64 từ data URL

                    // Chuyển đổi dữ liệu base64 thành ArrayBuffer
                    var binaryData = atob(base64Data);
                    var arrayBuffer = new ArrayBuffer(binaryData.length);
                    var uint8Array = new Uint8Array(arrayBuffer);
                    for (var i = 0; i < binaryData.length; i++) {
                        uint8Array[i] = binaryData.charCodeAt(i);
                    }

                    // Tạo đối tượng tệp tin từ ArrayBuffer
                    var file = new File([uint8Array], `${$(element).data("table-name")}.json`, { type: contentType });

                    var formData = new FormData();
                    formData.append("purpose", "assistants");
                    formData.append("file", file);

                    // Upload files
                    promises.push($.ajax({
                        url: "https://api.openai.com/v1/files",
                        headers: header_auth,
                        type: "POST",
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            console.log("Uploaded successfully: ", response);
                            file_ids.push(response.id);
                            if (file_id) {
                                // Delete assistant file
                                // promises.push($.ajax({
                                //     url: `https://api.openai.com/v1/assistants/${assistant_id}/files/${file_id}`,
                                //     headers: headers,
                                //     method: "DELETE",
                                //     success: function(response) {
                                //         console.log("Delete assistant file: ", response);
                                //     },
                                //     error: function(err) {
                                //         console.log("Delete assistant file error: ", err);
                                //     }
                                // }));

                                // Delete file
                                promises.push($.ajax({
                                    url: `https://api.openai.com/v1/files/${file_id}`,
                                    headers: header_auth,
                                    method: "DELETE",
                                    success: function(response) {
                                        console.log("Delete file: ", response);
                                    },
                                    error: function(err) {
                                        console.log("Delete file error: ", err);
                                    }
                                }));
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Error occurred: ", error);
                        }
                    }));
                }
            })

            Promise.all(promises).then(function() {
                console.log("New file ids: ", file_ids);
                $.post({
                    url: `https://api.openai.com/v1/assistants/${assistant_id}`,
                    headers: headers,
                    data: JSON.stringify({
                        file_ids: file_ids
                    }),
                    success: function(response) {
                        console.log("Update assistant files: ", response);
                        retrieveFileIds();
                    },
                    error: function(err) {
                        console.log("Update assistant files error: ", err);
                    }
                })
            })
        })

        $(document).on('click', '.delete-datafile', function() {
            let choice = confirm("Bạn chắc chắn muốn xoá dữ liệu này?");
            if (choice) {
                let file_id = $(this).data('file-id');
                let table_name = $(this).data('table-name');
                $(this).parent().parent().find(`td[data-table-name="${table_name}"]`).empty();
                $(this).remove();
                // console.log($(this).parent().parent().find(`td[data-table-name="${table_name}"]`)) 
                deleteDoc(doc(fs, 'data_files', table_name)).then(() => {
                    $.ajax({
                        url: `https://api.openai.com/v1/files/${file_id}`,
                        headers: header_auth,
                        method: "DELETE",
                        success: function(response) {
                            console.log("Delete file: ", response);
                            $.ajax({
                                url: `https://api.openai.com/v1/assistants/${assistant_id}/files/${file_id}`,
                                headers: headers,
                                method: "DELETE",
                                success: function(response) {
                                    console.log("Delete assistant file: ", response);
                                    retrieveFileIds();
                                },
                                error: function(err) {
                                    console.log("Delete assistant file error: ", err);
                                }
                            })
                        },
                        error: function(err) {
                            console.log("Delete file error: ", err);
                        }
                    })
                });
            }
        })
    </script>
@stop