@extends('layouts.app')

@section('title')
    Пользовательские сообщения
@endsection

@section('content')
    <div id="reply-window" class="modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Введите свой ответ</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="closeModalWindow()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <textarea class="form-control" name="answer" id="answer" cols="60" rows="10">

                    </textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="sendResponse()">Отправить</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="closeModalWindow()">Закрыть</button>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Отправитель</th>
                    <th>Сообщение</th>
                    <th>Действия</th>
                </tr>
                </thead>
                <tbody>
                @foreach($messages as $message)
                    <tr>
                        <th scope="row">{{$message->id}}</th>
                        <td>{{$message->full_name}}</td>
                        <td>{{$message->content}}</td>
                        <td>
                            <button class="btn btn-warning" onclick="openModalWindow({{$message->id}})">Ответить</button>
                            <button class="btn btn-danger" onclick="deleteMessage({{$message->id}})">Удалить</button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <script>
        let messageId = 0;
        function deleteMessage(id) {
            axios.delete('/deleteMessage/' + id)
                .then(() => {
                    location.reload();
                })
        }

        function openModalWindow(id) {
            messageId = id;
            document.getElementById('reply-window').style.display = 'block';
        }

        function closeModalWindow() {
            document.getElementById('reply-window').style.display = 'none';
        }

        function sendResponse() {
            let formData = new FormData();
            let content = document.getElementById('answer').value;
            formData.append('message_id', messageId);
            formData.append('answer', content);
            axios.post('/reply', formData)
                .then(() => {
                    location.reload();
                })
        }
    </script>
@endsection
