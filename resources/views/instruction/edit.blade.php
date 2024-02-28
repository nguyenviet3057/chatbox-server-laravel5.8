@extends('adminlte::page')

@section('title', 'Sửa ngữ cảnh')

@section('css')
<style>
    textarea {
        resize: vertical;
    }

    .btn-action .d-flex.justify-content-end:first-child {
        display: flex;
        justify-content: end;
    }
</style>
@stop

@section('content_header')
    <h1>Sửa ngữ cảnh phụ</h1>
@stop

@section('content')
    <div class="panel border border-secondary rounded-lg">
        <form method="POST" action="{{ route('instruction.edit.submit') }}" class="panel-body">
            @csrf
            <input type="number" value="{{ $data->id }}" name="id" hidden>
            <div class="row">
                <div class="col-sm-12 col-md-6 m-2">
                    <div class="d-flex flex-column">
                        <div class="form-group">
                            <label>Sau:</label>
                            <textarea name="instruction" class="form-control" rows="8" placeholder="Enter ...">{{ $data->instruction }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-6 m-2">
                    <div class="d-flex flex-column">
                        <div class="form-group">
                            <label>Trước:</label>
                            <textarea name="instruction" class="form-control" rows="8" placeholder="Enter ..." disabled>{{ $data->instruction }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row btn-action">
                <div class="col-sm-6 d-flex justify-content-end">
                    <button type="submit" class="btn btn-success">Lưu</button>
                </div>
                <div class="col-sm-6">
                    <a href="{{ route('instructions') }}"><button type="button" class="btn btn-warning">Hủy</button></a>
                </div>
            </div>
        </form>
    </div>
@stop

@section('js')
    <script>
        
    </script>
@stop