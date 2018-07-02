@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row ">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Mail Template</div>

                <div class="panel-body mail-template" style="text-align: center;">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif
                    <input type="hidden" id="template_id" value="{{$template_id}}">
                    <div class="row" id="email_template_div">
                        <h3 style="color: #1000ff;">Subject:</h3>
                        <input type="input" id="email_title" value="{{$template_name}}">
                        <br>
                        <h3 style="color: #1000ff;">HTML Version of Email:</h3> 
                        <div id="email_area"></div>
                        <br>
                        <h3 style="color: #1000ff;">TEXT Version of Email:</h3>
                        <textarea cols="100" rows="10" id="email_area2" style="max-width: 100%;" required> {{$template_text2}}</textarea>
                        <br>
                        <button class="btn btn-primary email-btn">Save Template</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $("#mail_navbar").css('color','#cc02e2');
        $("#mail_navbar").css('font-weight','bold');
        var editor1 = CKEDITOR.appendTo('email_area');
        txt = "{{ preg_replace( "/\r|\n/", "", $template_text ) }}";

        txt = $('<textarea />').html(txt).text();
        CKEDITOR.instances.editor1.setData(txt);


        $('#cke_editor1').click(function() {
            
                 alert('Click Event');
        });


        var noteOption = {
            clickToHide : true,
            autoHide : true,
            globalPosition : 'top center',
            style : 'bootstrap',
            className : 'error',
            showAnimation : 'slideDown',
            showDuration : 400,
            hideAnimation: 'slideUp',
            hideDuration: 200,
            gap : 20,
        }
        $.notify.defaults(noteOption);
        $.notify.addStyle('happyblue', {
          html: "<div><span data-notify-text/></div>",
          classes: {
            base: {
              "white-space": "nowrap",
              "background-color": "#333399",
              "padding": "10px",
              "margin-top" : "45px",
              "border-radius" : "5px"
            },
            superblue: {
              "color": "white",
            }
          }
        });
        $('.email-btn').click( function() {
            var data = CKEDITOR.instances.editor1.getData();
            
            $('.notifyjs-corner').empty();
            var text = $('#email_area').val();
            var text2 = $('#email_area2').val();
            var title = $('#email_title').val();
            var id = $('#template_id').val();
            $.ajax({
                url: "{{url('mail/save')}}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    template_name: title,
                    template_text: data,
                    template_text2: text2,
                    template_id: id
                },
                type: 'post',
                success: function(result) {
                    if(result == '1')
                        $.notify("Successfully saved!",{style:'happyblue',className:'superblue'});
                    else
                        $.notify("Error occured!",{style:'happyblue',className:'superblue'});
                    console.log(result);
                },
                error: function(error) {
                    alert("Error");
                }
            });
        });
    });
</script>
@endsection
