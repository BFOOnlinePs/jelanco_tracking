@extends('layouts.app')
@section('title')
    قائمة المهام
@endsection
@section('header')
    قائمة المهام
@endsection
@section('header_title_link')
    الرئيسية
@endsection
@section('header_title')
    قائمة المهام
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            @include('message_alert.success_message')
            @include('message_alert.fail_message')
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <a href="{{ route('tasks.add') }}" class="btn btn-dark">اضافة مهمة</a>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label for="">اسم المهمة</label>
                                <input type="text" id="task_name" onkeyup="list_tasks_ajax()" placeholder="اسم المهمة" class="form-control">
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label for="">حالة المهمة</label>
                                <select onchange="list_tasks_ajax()" class="form-control" name="" id="task_status">
                                    <option value="">جميع الحالات</option>
                                    <option value="active">مفعلة</option>
                                    <option value="notActive">غير مفعلة</option>
                                </select>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label for="">الموظفين</label>
                                <select onchange="list_tasks_ajax()" class="form-control select2bs4" name="" id="assigned_to">
                                    <option value="">جميع الموظفين</option>
                                    @foreach($users as $key)
                                        <option value="{{ $key->id }}">{{ $key->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label for="">وقت البداية</label>
                                <input onchange="list_tasks_ajax()" id="task_start_time" type="datetime-local" class="form-control">
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label for="">وقت النهاية</label>
                                <input onchange="list_tasks_ajax()" id="task_end_time" type="datetime-local" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive row" id="list_tasks_table"></div>
                </div>
            </div>
        </div>
    </div>
    <div id="imageModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.8); z-index:1000;">
        <img id="enlargedImage" style="max-width:70%; max-height:70%; margin:auto; position:absolute; top:0; left:0; bottom:0; right:0;">
    </div>
@endsection
@section('script')
    <script>
        $(document).ready(function () {
            list_tasks_ajax(1);
            $(document).on('click', '.pagination a', function(event){
                event.preventDefault();
                var page = $(this).attr('href').split('page=')[1];
                list_tasks_ajax(page);
            });
        });
        function list_tasks_ajax(page) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: '{{ route('tasks.list_tasks_ajax') }}',
                type: 'post',
                dataType: 'json',
                data:{
                    'task_name' : $('#task_name').val(),
                    'task_status' : $('#task_status').val(),
                    'task_start_time' : $('#task_start_time').val(),
                    'task_end_time' : $('#task_end_time').val(),
                    'assigned_to' : $('#assigned_to').val(),
                    'page' : page,
                },
                success: function(response) {
                    $('#list_tasks_table').html(response.view);
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        }

        function add_comment_ajax(tsc_task_id,tsc_task_submission_id,tsc_parent_id,tsc_commented_by) {
            $('#no_data_'+tsc_task_submission_id).empty();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: '{{ route('tasks.task_submission.comments.add_comment_ajax') }}',
                type: 'post',
                dataType: 'json',
                data:{
                    'tsc_task_id' : tsc_task_id,
                    'tsc_task_submission_id' : tsc_task_submission_id,
                    'tsc_parent_id' : tsc_parent_id,
                    'tsc_commented_by' : tsc_commented_by,
                    'tsc_content' : $('#comment_content_'+tsc_task_submission_id).val(),
                },
                success: function(response) {
                    const createdAt = new Date(response.data.created_at);

                    const year = createdAt.getFullYear();
                    const month = String(createdAt.getMonth() + 1).padStart(2, '0'); // Ensure two digits
                    const day = String(createdAt.getDate()).padStart(2, '0');

                    const hours = String(createdAt.getHours()).padStart(2, '0');
                    const minutes = String(createdAt.getMinutes()).padStart(2, '0');
                    const seconds = String(createdAt.getSeconds()).padStart(2, '0');

                    const formattedDate = `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
                    $('#list_comments_content_'+tsc_task_submission_id).append(`
                        <div class="row">
                                                                <div class="col-md-3 justify-content-center align-content-center float-right"
                                                                     dir="rtl">
                                                                    <div class="row">
                                                                        <div class="col-md-3 justify-content-center align-content-center">
                                                                            <img style="width: 40px" class="img-circle img-bordered-sm"
                                                                                 src="{{ asset('assets/dist/img/user7-128x128.jpg') }}"
                                                                                 alt="User Image">
                                                                        </div>
                                                                        <div class="col-md-9 justify-content-center align-content-center">
                                                                            <div class="row">
                                                                                <div class="col-md-12">
                                                          <span class="username">
                                                    <a href="#">${response.client.name}</a>
                                                    </span>
                                                                                </div>
                                                                            </div>
                                                                            <div class="row">
                                                                                <div class="col-md-12">
                                                                                    <span class="description"><span>${formattedDate}</span></span>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-9">
                                                                    <div class="dialogbox">
                                                                        <div class="body">
                                                                            <span class="tip tip-right"></span>
                                                                            <div class="row">
                                                                                <div class="col-md-12">
                                                                                    <div class="message">
                                                                                        <span>${response.data.tsc_content}</span>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                    `);
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        }

        // $('.comment_content').keypress(function (e) {
        //     if(e.key === 'Enter'){
        //         alert('asd');
        //     }
        // })

        // $(document).on('keypress','.comment_content',function (e) {
        //     if(e.key === 'Enter'){
        //         e.preventDefault();
        //         const tsc_task_submission_id = $(this).attr('id').split('_').pop(); // Extract the submission ID from the input ID
        //         const tsc_task_id = $(this).closest('.input-group').find('button').attr('onclick').match(/\d+/g)[0];
        //         const tsc_commented_by = $(this).closest('.input-group').find('button').attr('onclick').match(/\d+/g).pop();
        //         $('#no_data_'+tsc_task_submission_id).empty();
        //
        //         $(this).val('');
        //     }
        // })

        $(document).on('click', '.image_icon, .video_icon, .file_icon', function(e) {
            e.preventDefault(); // Prevent default action

            // Get the closest form element
            var form = $(this).closest('form');

            // Extract the ts_id from the input field's id attribute
            var ts_id = form.find('input.comment_content').attr('id').split('_')[2];

            // Determine the type of input to create based on the clicked icon
            var inputType;
            var acceptType;
            var previewContainerClass;

            if ($(this).hasClass('image_icon')) {
                inputType = 'image';
                acceptType = 'image/*';
                previewContainerClass = '.image_previews_';
            } else if ($(this).hasClass('video_icon')) {
                inputType = 'video';
                acceptType = 'video/*';
                previewContainerClass = '.video_previews_';
            } else if ($(this).hasClass('file_icon')) {
                inputType = 'file';
                acceptType = '*/*';
                previewContainerClass = '.file_previews_';
            }

            // Create a hidden file input element for file selection
            var input = $('<input name="' + inputType + '[]" type="file" multiple accept="' + acceptType + '" style="display:none;" />');

            // Append the input to the form
            form.append(input);

            input.on('change', function() {
                var files = this.files; // Get the selected files

                // Iterate over each file and display previews
                for (var i = 0; i < files.length; i++) {
                    var file = files[i];
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        var fileContainer = $('<div>').addClass('file-container').css({
                            display: 'inline-block',
                            position: 'relative',
                            margin: '5px'
                        });

                        var preview;
                        if (inputType === 'image') {
                            preview = $('<img>').attr('src', e.target.result).addClass('img-thumbnail').css('max-width', '100px');
                        } else if (inputType === 'video') {
                            preview = $('<video controls>').attr('src', e.target.result).css('max-width', '100px');
                        } else {
                            preview = $('<span>').text(file.name).css({
                                display: 'block',
                                'max-width': '100px',
                                overflow: 'hidden',
                                'text-overflow': 'ellipsis'
                            });
                        }

                        var deleteBtn = $('<span>').html('&times;').addClass('delete-btn').css({
                            position: 'absolute',
                            top: '0px',
                            right: '0px',
                            background: '#ff0000',
                            color: '#fff',
                            padding: '2px 5px',
                            cursor: 'pointer'
                        });

                        fileContainer.append(preview).append(deleteBtn);

                        form.find(previewContainerClass + ts_id).append(fileContainer);

                        // Delete button click event
                        deleteBtn.on('click', function() {
                            fileContainer.remove();
                            // Remove the corresponding file input
                            input.remove();
                        });

                        // Preview click event to show enlarged view (only for images and videos)
                        if (inputType === 'image' || inputType === 'video') {
                            preview.on('click', function() {
                                $('#enlargedImage').attr('src', e.target.result);
                                $('#imageModal').fadeIn();
                            });
                        }
                    };
                    reader.readAsDataURL(file);
                }
            });

            input.trigger('click');
        });


        $('#imageModal').on('click', function() {
            $(this).fadeOut();
        });

        $(document).on('click','.imageModal',function(){            
            var imgSrc = $(this).attr('src');            
            $('#enlargedImage').attr('src', imgSrc);
            $('#imageModal').fadeIn();
        });

        $(document).on('submit', '.comment-form', function(e) {
            
                e.preventDefault();
            var formData = new FormData(this);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: $(this).attr('action'),
                type: $(this).attr('method'),
                dataType: 'json',
                processData: false,
                contentType: false,
                data: formData,
                success: function(response) {
                    const createdAt = new Date(response.data.created_at);

                    // To get Time like laravel timestamp (date and time)
                    const year = createdAt.getFullYear();
                    const month = String(createdAt.getMonth() + 1).padStart(2, '0');
                    const day = String(createdAt.getDate()).padStart(2, '0');

                    const hours = String(createdAt.getHours()).padStart(2, '0');
                    const minutes = String(createdAt.getMinutes()).padStart(2, '0');
                    const seconds = String(createdAt.getSeconds()).padStart(2, '0');

                    const formattedDate = `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;

                    let imagePreviews = '';
                    if (response.attachments && response.attachments.length > 0) {
                        imagePreviews = response.attachments.map(function(attachment) {
                            return `<img style="width: 100px; margin-right: 5px;" class="img-fluid img-thumbnail imageModal" src="{{ asset('storage/comments_attachments/') }}/${attachment.a_attachment}" alt="Attachment">`;
                        }).join('');
                    }

$('#list_comments_content_' + formData.get('tsc_task_submission_id')).append(`
    <div class="row">
        <div class="col-md-3 justify-content-center align-content-center float-right" dir="rtl">
            <div class="row">
                <div class="col-md-3 justify-content-center align-content-center">
                    <img style="width: 40px" class="img-circle img-bordered-sm"
                         src="{{ asset('assets/dist/img/user7-128x128.jpg') }}"
                         alt="User Image">
                </div>
                <div class="col-md-9 justify-content-center align-content-center">
                    <div class="row">
                        <div class="col-md-12">
                            <span class="username">
                                <a href="#">${response.client.name}</a>
                            </span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <span class="description"><span>${formattedDate}</span></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="dialogbox">
                <div class="body">
                    <span class="tip tip-right"></span>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="message">
                                <p>${response.data.tsc_content}</p>
                                <!-- Include image previews here -->
                                ${imagePreviews}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
`);

$('#comment_content_' + formData.get('tsc_task_submission_id')).val('');

                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
            
            
        });
    </script>
@endsection

