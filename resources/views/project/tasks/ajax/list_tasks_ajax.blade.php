<div class="col-md-12">
    @php
        $specificDate = '';
    @endphp
    <div class="timeline">
        @foreach($data as $key)
            <div class="time-label">
                @if(\Carbon\Carbon::parse($key->created_at)->toDateString() != $specificDate)
                    <span class=""
                          style="float: left">{{ \Carbon\Carbon::parse($key->created_at)->toDateString() }}</span>
                @endif
            </div>
            <div class="@if(\Carbon\Carbon::parse($key->created_at)->toDateString() != $specificDate) mt-5 @endif">
                <i class="fa fa-file bg-dark"></i>
                <div class="timeline-item bg-light">
                    <span class="time float-right">تم التوكيل بالمهمة بتاريخ <span>{{ $key->created_at }}</span> <i class="fas fa-clock"></i></span>
                    <h3 class="timeline-header">
                        <label class="">بواسطة <span class="badge bg-warning">{{ $key->addedByUser->name }}</span></label>
                        |
                        @foreach(json_decode($key->t_assigned_to) as $user)
                            <span class="badge bg-dark">{{ \App\Models\User::where('id',$user)->first()->name }}</span>
                        @endforeach
                    </h3>
                    <div class="timeline-body">
                        <span class="fa fa-edit text-gray"></span>
                        &nbsp;
                        <span class="text-bold">
                            {{ $key->t_content }}
                        </span>
                        <hr>
                        @if($key->submissions->isEmpty())
                            <div class="text-center">
                                لا توجد مهام مسلمة
                            </div>
                        @else
                            @foreach($key->submissions as $submission)
                                <div style="font-size: 14px" class="card">
                                    <div class="card-body">
                                <span class="time float-right" style="font-size: 12px">تم التسليم بتاريخ <span>{{ $submission->created_at }}</span> <i
                                        class="fas fa-clock"></i></span>
                                        <div>
                                            <img class="rounded img-bordered-sm" style="width: 40px" src="{{ asset('assets/dist/img/user7-128x128.jpg') }}" alt="">
                                            <span>تسليم بواسطة <span>{{ $submission->submitter->name }}</span></span>
                                        </div>
                                        <hr>
                                        <div class="form-group">
                                            <span>تم تسليم المهمة بنجاح تم تسليم المهمة بنجاح تم تسليم المهمة بنجاح تم تسليم المهمة بنجاح تم تسليم المهمة بنجاح تم تسليم المهمة بنجاح</span>
                                        </div>
                                            <div class="form-group">
                                                <div class="">
                                                    <form class="form-horizontal comment-form" method="post" action="{{ route('tasks.task_submission.comments.add_comment_ajax') }}" enctype="multipart/form-data">
                                                        @csrf
                                                        <input type="hidden" name="tsc_task_submission_id" value="{{ $submission->ts_id }}">
                                                        <input type="hidden" name="tsc_task_id" value="{{ $key->t_id }}">
                                                        <div class="input-group input-group-sm mb-0">
                                                            <input name="tsc_content" class="form-control form-control-sm comment_content" id="comment_content_{{ $submission->ts_id }}" placeholder="ابدأ بكتابة تعليق ...">
                                                            <div class="input-group-append">
                                                                <button type="submit" class="btn btn-dark"><span class="fa fa-arrow-left"></span></button>
                                                            </div>
                                                        </div>
                                                        <div style="font-size: 14px" class="p-2">
                                                            <span class="fa fa-image mr-1 image_icon"></span>
                                                            <span class="fa fa-camera mr-1 video_icon"></span>
                                                            <span class="fa fa-file mr-1 file_icon"></span>
                                                        </div>
                                                        <div class="image_previews_{{ $submission->ts_id }}"></div>
                                                        <div class="video_previews_{{ $submission->ts_id }}"></div>
                                                        <div class="file_previews_{{ $submission->ts_id }}"></div>
                                                    </form>
                                                </div>
                                                <div id="list_comments_content_{{ $submission->ts_id }}">
                                                @if($submission->comments->isEmpty())
                                                    <div id="no_data_{{ $submission->ts_id }}" class="text-center">
                                                        لا توجد تعليقات
                                                    </div>
                                                @else
                                                        @foreach($submission->comments as $comment)
                                                            <div class="row">
                                                                <div class="col-md-3 justify-content-center align-content-center float-right" dir="rtl">
                                                                    <div class="row">
                                                                        <div class="col-md-3 justify-content-center align-content-center">
                                                                            <img style="width: 40px" class="img-circle img-bordered-sm"
                                                                                 src="{{ asset('assets/dist/img/user7-128x128.jpg') }}" alt="User Image">
                                                                        </div>
                                                                        <div class="col-md-9 justify-content-center align-content-center">
                                                                            <div class="row">
                                                                                <div class="col-md-12">
                            <span class="username">
                                <a href="#">{{ $comment->comment_by->name }}</a>
                            </span>
                                                                                </div>
                                                                            </div>
                                                                            <div class="row">
                                                                                <div class="col-md-12">
                                                                                    <span class="description"><span>{{ $comment->created_at }}</span></span>
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
                                                                                        <p>{{ $comment->tsc_content }}</p>
                                                                                        <div id="imagePreviewsContainer">
                                                                                            @if (!$comment->attachments->isEmpty())
                                                                                                @foreach ($comment->attachments as $attachment)
                                                                                                    @php
                                                                                                        $filePath = asset('storage/comments_attachments/'.$attachment->a_attachment);
                                                                                                        $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);
                                                                                                    @endphp

                                                                                                    @if(in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif']))
                                                                                                        <img style="width: 100px" class="img-fluid img-thumbnail imageModal" src="{{ $filePath }}" alt="Image">
                                                                                                    @elseif(in_array(strtolower($fileExtension), ['mp4', 'webm', 'ogg']))
                                                                                                        <video style="width: 100px;" controls class="img-fluid video-thumbnail">
                                                                                                            <source src="{{ $filePath }}" type="video/{{ $fileExtension }}">
                                                                                                        </video>
                                                                                                    @elseif($fileExtension === 'pdf')
                                                                                                        <a href="{{ $filePath }}" target="_blank">
                                                                                                            <embed src="{{ $filePath }}" type="application/pdf" width="100px" height="100px" class="pdf-thumbnail"/>
                                                                                                        </a>
                                                                                                    @else
                                                                                                        <a href="{{ $filePath }}" target="_blank">{{ $attachment->a_attachment }}</a>
                                                                                                    @endif
                                                                                                @endforeach
                                                                                            @endif
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                @endif
                                                </div>

                                            </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
            @php
                $specificDate = \Carbon\Carbon::parse($key->created_at)->toDateString();
            @endphp
        @endforeach
    </div>
</div>

<div class="mr-5 ml-5">
    {{ $data->links() }}
</div>

<script>
    // $(document).ready(function () {
    //     $('.comment_content').keyup(function () {
    //         alert('asd');
    //     });
    // });
</script>

