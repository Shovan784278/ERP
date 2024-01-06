{{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => 'student.record.update', 'method' => 'POST']) }}
<input type="hidden" name="student_id" value="{{ $student_detail->id }}">
<input type="hidden" name="record_id" value="{{ $record->id }}">
<div class="row">
    @php  $setting = app('school_info');   @endphp
    <div class="col-lg-12">
        <div class="input-effect" id="academic-div">
            <select class="niceSelect1 w-100 bb form-control{{ $errors->has('session') ? ' is-invalid' : '' }}"
                name="session" id="edit_academic_year">
                <option data-display="@lang('common.academic_year') *" value="">
                    @lang('common.academic_year') *</option>
                @foreach ($sessions as $session)
                    <option value="{{ $session->id }}" {{ $record->session_id == $session->id ? 'selected' : '' }}>
                        {{ $session->year }}[{{ $session->title }}]</option>
                @endforeach
            </select>
            <span class="focus-border"></span>
            @if ($errors->has('session'))
                <span class="invalid-feedback invalid-select" role="alert">
                    <strong>{{ $errors->first('session') }}</strong>
                </span>
            @endif
        </div>
    </div>
    <div class="col-lg-12 mt-25">
        <div class="input-effect" id="edit_class-div">
            <select class="niceSelect1 w-100 bb form-control{{ $errors->has('class') ? ' is-invalid' : '' }}"
                name="class" id="edit_classSelectStudent">
                <option data-display="@lang('common.class') *" value="">@lang('common.class') *</option>
                @foreach ($record->classes as $class)
                    <option value="{{ $class->id }}" {{ $record->class_id == $class->id ? 'selected' : '' }}>
                        {{ $class->class_name }}
                    </option>
                @endforeach

            </select>
            <div class="pull-right loader loader_style" id="edit_select_class_loader">
                <img class="loader_img_style" src="{{ asset('public/backEnd/img/demo_wait.gif') }}" alt="loader">
            </div>
            <span class="focus-border"></span>
            @if ($errors->has('class'))
                <span class="invalid-feedback invalid-select" role="alert">
                    <strong>{{ $errors->first('class') }}</strong>
                </span>
            @endif
        </div>
    </div>
    <div class="col-lg-12 mt-25">
        <div class="input-effect" id="edit_sectionStudentDiv">
            <select class="niceSelect1 w-100 bb form-control{{ $errors->has('section') ? ' is-invalid' : '' }}"
                name="section" id="edit_sectionSelectStudent">
                <option data-display="@lang('common.section') *" value="">@lang('common.section') *
                </option>
                @if ($record->session_id && $record->class_id)
                    @foreach ($record->class->classSection as $section)
                        <option value="{{ $section->sectionName->id }}"
                            {{ $record->section_id == $section->sectionName->id ? 'selected' : '' }}>
                            {{ $section->sectionName->section_name }}</option>
                    @endforeach
                @endif
            </select>
            <div class="pull-right loader loader_style" id="edit_select_section_loader">
                <img class="loader_img_style" src="{{ asset('public/backEnd/img/demo_wait.gif') }}" alt="loader">
            </div>
            <span class="focus-border"></span>
            @if ($errors->has('section'))
                <span class="invalid-feedback invalid-select" role="alert">
                    <strong>{{ $errors->first('section') }}</strong>
                </span>
            @endif
        </div>
    </div>
    @if($setting->multiple_roll ==1)
    <div class="col-lg-12 mt-25">
        <div class="input-effect">
            <input oninput="numberCheck(this)" class="primary-input form-control has-content" type="text"
                id="roll_number" name="roll_number" value="{{ $record->roll_no }}">
            <label>
                {{ moduleStatusCheck('Lead') == true ? __('lead::lead.id_number') : __('student.roll') }}
                @if (is_required('roll_number') == true) <span> *</span> @endif</label>
            <span class="focus-border"></span>
            <span class="text-danger" id="roll-error" role="alert">
                <strong></strong>
            </span>
            @if ($errors->has('roll_number'))
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('roll_number') }}</strong>
                </span>
            @endif
        </div>
    </div>
    @endif
   
    <div class="col-lg-12 mt-25">
            <input type="checkbox" id="iss_default" value="1" class="common-checkbox" {{ $record->is_default == 1 ? 'checked':'' }} name="is_default">
            <label for="iss_default">@lang('student.is_default')</label>
    </div>
   
    <div class="col-lg-12 text-center mt-25">
        <div class="mt-40 d-flex justify-content-between">
            <button type="button" class="primary-btn tr-bg" data-dismiss="modal">@lang('admin.cancel')</button>
            <button class="primary-btn fix-gr-bg submit" id="save_button_query"
                type="submit">@lang('admin.save')</button>
        </div>
    </div>
</div>
{{ Form::close() }}

<script>
    if ($(".niceSelect1").length) {
        $(".niceSelect1").niceSelect();
    }
    // $(document).ready(function() {
        $(document).on("change",'#edit_academic_year',function() {
            // alert($(this).val());
                var url = $("#url").val();
                var i = 0;
                var formData = {
                    id: $(this).val(),
                };
                // get section for student
                $.ajax({
                    type: "GET",
                    data: formData,
                    dataType: "json",
                    url: url + "/" + "academic-year-get-class",

                    beforeSend: function() {
                        $('#edit_select_class_loader').addClass('pre_loader');
                        $('#edit_select_class_loader').removeClass('loader');
                    },

                    success: function(data) {
                        $("#edit_classSelectStudent").empty().append(
                            $("<option>", {
                                value:  '',
                                text: window.jsLang('select_class') + ' *',
                            })
                        );

                        if (data[0].length) {
                            $.each(data[0], function(i, className) {
                                $("#edit_classSelectStudent").append(
                                    $("<option>", {
                                        value: className.id,
                                        text: className.class_name,
                                    })
                                );
                            });
                        } 
                        $('#edit_classSelectStudent').niceSelect('update');
                        $('#edit_classSelectStudent').trigger('change');
                    },
                    error: function(data) {
                        console.log('Error:', data);
                    },
                    complete: function() {
                        i--;
                        if (i <= 0) {
                            $('#edit_select_class_loader').removeClass('pre_loader');
                            $('#edit_select_class_loader').addClass('loader');
                        }
                    }
                });
        });
        $(document).on("change","#edit_classSelectStudent", function() {
            var url = $("#url").val();
            var i = 0;

            var formData = {
                id: $(this).val(),
            };
            // get section for student
            $.ajax({
                type: "GET",
                data: formData,
                dataType: "json",
                url: url + "/" + "ajaxSectionStudent",

                beforeSend: function() {
                    $('#edit_select_section_loader').addClass('pre_loader');
                    $('#edit_select_section_loader').removeClass('loader');
                },
                success: function(data) {

                    $("#edit_sectionSelectStudent").empty().append(
                        $("<option>", {
                            value:  '',
                            text: window.jsLang('select_section') + ' *',
                        })
                    );
                    $.each(data, function(i, item) {
                       
                        if (item.length) {
                            $.each(item, function(i, section) {
                                $("#edit_sectionSelectStudent").append(
                                    $("<option>", {
                                        value: section.id,
                                        text: section.section_name,
                                    })
                                );
                                
                            });
                        } 
                    });
                    $("#edit_sectionSelectStudent").trigger('change').niceSelect('update')
                    

                },
                error: function(data) {
                    console.log("Error:", data);
                },
                complete: function() {
                    i--;
                    if (i <= 0) {
                        $('#edit_select_section_loader').removeClass('pre_loader');
                        $('#edit_select_section_loader').addClass('loader');
                    }
                }
            });
        });
    
</script>
