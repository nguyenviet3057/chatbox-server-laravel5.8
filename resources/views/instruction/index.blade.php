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
    </script>
@stop