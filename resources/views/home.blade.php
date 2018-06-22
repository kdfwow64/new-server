@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Search Box</div>

                <div class="panel-body home-body" style="text-align: center;">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    <span class="search_title">Google Search</span>
                    <span class="running" style="display: none;">It's running...</span>
                    
                    <div class="row" id="google_search_div">
                        <div class="col-md-4">
                            <label>Start Page : </label>
                            <input type="number" class="form-control input-box"  id="start_page" name="start_page" name="start_page" min="0" style="width: 40px;border: solid 1px;" value="0">
                            <label>End Page : </label>
                            <input type="number" class="form-control input-box" name="end_page"  id="end_page" name="end_page" min="0" style="width: 40px;border: solid 1px;" value="0">
                        </div>

                        <div class="col-md-4">
                            <input type="input" class="form-control input-box" name="keyword" id="keyword" style="border: solid 1px;" placeholder="Please input keyword..." name="keyword">
                            <button id="google_search" class="btn btn-warning">Search</button>
                        </div>

                        @if($permission->value == 1)
                        <div class="col-md-4">
                            <button id="send_email_btn" class="btn btn-success">Send Mail</button>
                        </div>
                        @endif
                    </div>
                    <br>
                    <span class="search_title">Search in the Database</span>
                    <div class="row" id="other_search_div">
                        <div class="col-md-6">
                            <input type="input" class="form-control input-box" name="domain_keyword" id="domain_keyword" name="domain_keyword" placeholder="Domain keyword ..." style="border: solid 1px;">
                            <button id="domain_search" class="btn btn-warning">Search</button>
                            <br>
                            <table id="domain_search_table" class="table table-striped" style="display: none;">
                                <thead>
                                    <tr>
                                        <td>No</td>
                                        <td>Domain Name</td>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <input type="input" class="form-control input-box" name="email_keyword" id="email_keyword" name="email_keyword" placeholder="Search Email ..." style="border: solid 1px;">
                            <button id="email_search" class="btn btn-warning">Search</button>
                            <br>
                            <table id="email_search_table" class="table table-striped" style="display: none;font-size: 13px;">
                                <thead>
                                    <tr>
                                        <td>No</td>
                                        <td>Email</td>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>




                </div>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
    
    $(document).ready(function(){
        $("#home_navbar").css('color','#cc02e2');
        $("#home_navbar").css('font-weight','bold');
        var noteOption = {
            clickToHide : true,
            autoHide : false,
            globalPosition : 'top center',
            style : 'bootstrap',
            className : 'error',
            showAnimation : 'slideDown',
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
        $('input').change(function(){
            $('.notifyjs-corner').empty();
            if($('#start_page').val()>$('#end_page').val()) {
                $.notify("Input correct pages!",{style:'happyblue',className:'superblue'});
                if($(this).attr('id') == 'start_page')
                    $(this).val($('#end_page').val());
                else
                    $(this).val($('#start_page').val());
            }
        });
        $('#google_search').click(function() {
            start_page = $('#start_page').val();
            end_page = $('#end_page').val();
            keyword = $('#keyword').val();
            $('.running').css('display','unset');
            $('.search_title').addClass('display_none');
            $('#google_search_div').addClass('display_none');
            $.ajax({
                url: "{{url('home/scrape')}}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    start_page: start_page,
                    end_page: end_page,
                    keyword: keyword
                },
                type: 'post',
                success: function(result) {
                    $('.running').css('display','none');
                    $('.search_title').removeClass('display_none');
                    $('#google_search_div').removeClass('display_none');
                    console.log(result);
                },
                error: function(error) {
                    $('.running').css('display','none');
                    $('.search_title').removeClass('display_none');
                    $('#google_search_div').removeClass('display_none');
                    alert("Error");
                }
            });
        });
        $('#domain_search').click(function(){
            var domain = $('#domain_keyword').val();
            $('#domain_search_table').css('display','unset');
            $('#domain_search_table tbody').html("");
            $.ajax({
                url: "{{url('home/getDomains')}}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    domain: domain
                },
                type: 'post',
                success: function(result) {
                    for(i = 0; i < result.length ; i++) {
                        $('#domain_search_table tbody').append("<tr><td>"+(i+1)+"</td><td>"+result[i]['domain_name']+"</td></tr>");
                    }
                },
                error: function(error) {
                    alert("Error");
                }
            });
        });
        
        $('#email_search').click(function(){
            var email = $('#email_keyword').val();
            $('#email_search_table').css('display','unset');
            $('#email_search_table tbody').html("");
            $.ajax({
                url: "{{url('home/getEmail')}}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    email: email
                },
                type: 'post',
                success: function(result) {
                    for(i = 0; i < result.length ; i++) {
                        $('#email_search_table tbody').append("<tr><td>"+(i+1)+"</td><td>"+result[i]['email']+"</td></tr>");
                    }
                },
                error: function(error) {
                    alert("Error");
                }
            });
        });

        $('#send_email_btn').click(function() {
            $.ajax({
                url: "{{url('mail/sendAll')}}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'post',
                success: function(result) {
                    alert("Done");
                },
                error: function(error) {
                    alert("Error");
                }
            });
        });
    });
</script>
@endsection

