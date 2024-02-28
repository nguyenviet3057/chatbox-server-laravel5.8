@extends('adminlte::page')

@section('title', 'Thêm mới ngữ cảnh')

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
    <h1>Thêm mới ngữ cảnh</h1>
@stop

@section('content')
    <div class="panel border border-secondary rounded-lg">
        <form method="POST" action="{{ route('faq.add.submit') }}" class="panel-body">
            @csrf
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Câu hỏi:</label>
                        <textarea name="question" class="form-control" rows="8" placeholder="Enter ..."></textarea>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Trả lời:</label>
                        <textarea name="answer" class="form-control" rows="8" placeholder="Enter ..."></textarea>
                    </div>
                </div>
            </div>
            <div class="row btn-action">
                <div class="col-sm-6 d-flex justify-content-end">
                    <button type="submit" class="btn btn-success">Lưu</button>
                </div>
                <div class="col-sm-6">
                    <a href="{{ route('faqs') }}"><button type="button" class="btn btn-warning">Hủy</button></a>
                </div>
            </div>
        </form>
    </div>
@stop

@section('js')
@stop