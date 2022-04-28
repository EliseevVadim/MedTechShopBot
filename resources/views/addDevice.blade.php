@extends('layouts.app')

@section('title')
    Добавление товара
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header text-center">Добавить товар</div>
                    <div class="card-body bg-dark">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <form enctype="multipart/form-data" action="/addDevice" method="post">
                            @csrf
                            <div class="form-group">
                                <label for="group-field" class="col-form-label text-light">Введите название товара<sup>*</sup>:</label>
                                <input class="form-control" id="group-field" type="text" name="name" required placeholder="Товар...">
                            </div>
                            <div class="form-group">
                                <label for="discipline-field" class="col-form-label text-light">Введите описание товара<sup>*</sup>:</label>
                                <textarea class="form-control" id="discipline-field" name="description" required placeholder="Введите описание..."></textarea>
                            </div>
                            <div class="form-group">
                                <label for="group-field" class="col-form-label text-light">Выберите картинку товара<sup>*</sup>:</label>
                                <div class="custom-file">
                                    <input type="file"
                                           class="custom-file-input"
                                           id="file"
                                           name="image"
                                    >
                                    <label class="custom-file-label" for="file"></label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="group-field" class="col-form-label text-light">Введите цену товара<sup>*</sup>:</label>
                                <input class="form-control" id="group-field" type="number" name="price" required placeholder="Цена...">
                            </div>
                            <div class="form-group">
                                <label for="group-field" class="col-form-label text-light">Введите скидку (в процентах):</label>
                                <input class="form-control" id="group-field" type="number" name="discount" placeholder="Скидка...">
                            </div>
                            <div class="form-group">
                                <label for="group-field" class="col-form-label text-light">Выберите тип товара<sup>*</sup>:</label>
                                <select class="form-control" id="group-field" name="type_id">
                                    @foreach($types as $type)
                                        <option value="{{$type->id}}">{{$type->type_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <span class="text-light">Поля, помеченные звездочкой <sup>*</sup>, обязательны для заполнения!</span>
                            <div class="row d-flex justify-content-end mt-2">
                                <button type="submit" class="btn btn-primary w-25 mx-1">Сохранить</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(".custom-file-input").on("change", function() {
            let fileName = $(this).val().split("\\").pop();
            $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
        });
    </script>
@endsection
