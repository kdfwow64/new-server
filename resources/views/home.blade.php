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

                    <span class="search_title">Enter 1 Keyword</span>
                    <span class="running" style="display: none;">It's running...</span>
                    
                    <div class="row" id="google_search_div">
                        @if($permission->value == 1 && Auth::user()->name == 'Admin')
                        <div class="row">
                            <div class="col-md-3">
                                <input type="input" class="form-control input-box" name="keyword" id="keyword" style="border: solid 1px;" placeholder="Please input keyword...">
                            </div>
                            <div class="col-md-3">
                                <input type="input" class="form-control input-box" name="keyword_city" id="keyword_city" style="border: solid 1px;" placeholder="Please input City...">
                            </div>
                            <div class="col-md-3">
                                <input type="input" class="form-control input-box" name="keyword_state" id="keyword_state" style="border: solid 1px;" placeholder="Please input State...">
                            </div>
                            <div class="col-md-3">
                                <button id="send_email_btn" class="btn btn-success">Send Mail</button>
                            </div>
                        </div>
                        <div class="row">
                            <button id="google_search" class="btn btn-warning">Search</button>
                        </div>
                        @else
                            @if($flag == "disabled")
                            <p style="font-size: 20px;color: #ff00f7;">Please wait for one day</p>
                            <div class="clock" style="margin: 2em;zoom: 0.55;"></div>
                            @else
                            <div class="row">
                                
                                <div class="col-md-4">
                                    <input type="input" class="form-control input-box" name="keyword" id="keyword" style="border: solid 1px;" placeholder="Please input keyword...">
                                </div>
                                <div class="col-md-4">
                                    <input type="input" class="form-control input-box" name="keyword_city" id="keyword_city" style="border: solid 1px;" placeholder="Please input City...">
                                </div>
                                <div class="col-md-4">
                                    <input type="input" class="form-control input-box" name="keyword_state" id="keyword_state" style="border: solid 1px;" placeholder="Please input State...">
                                </div>
                            </div>
                            <div class="row">
                                <button id="google_search" class="btn btn-warning">Add keyword</button>
                                <p id="wait_text" style="display: none;font-size: 20px;color: #ff00f7;">Please wait for a one day</p>
                            </div>
                            @endif
                        @endif

                    </div>
                    <br>
                    @if(Auth::user()->name == 'Admin')
                    <span class="search_title">Search in the Database</span>
                    <div class="row" id="other_search_div">
                        <div class="col-md-6">
                            <input type="input" class="form-control input-box" name="domain_keyword" id="domain_keyword" placeholder="Domain keyword ..." style="border: solid 1px;">
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
                            <input type="input" class="form-control input-box" name="email_keyword" id="email_keyword" placeholder="Search Email ..." style="border: solid 1px;">
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
                    @endif




                </div>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
    var clock;
    $(document).ready(function(){
        
        var noteOption = {
            clickToHide : true,
            autoHide : true,
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
              "background-color": "#af14e2",
              "padding": "10px",
              "margin-top" : "45px",
              "border-radius" : "5px",
              "font-weight" : "bold"
            },
            superblue: {
              "color": "white",
            }
          }
        });
        var date = new Date(Date.UTC(2018,7,8,10,0,0));
        var now = new Date();
        var diff = date.getTime()/1000 - now.getTime()/1000;
        /*
        clock = $('.clock').FlipClock(diff,{
            clockFace: 'HourCounter',
            countdown: true
        });*/
        $("#home_navbar").css('color','#cc02e2');
        $("#home_navbar").css('font-weight','bold');
        
        $('#google_search').click(function() {
            let keyword = $('#keyword').val();
            let keyword_city = $('#keyword_city').val();
            let keyword_state = $('#keyword_state').val();
            
            $('.notifyjs-corner').empty();
            if(keyword == '' || keyword_city == '' || keyword == ' ' || keyword_city == ' ')
                $.notify("Keyword or City can not be null!",{style:'happyblue',className:'superblue'});
            else {
                $.ajax({
                    url: "{{url('home/addkeyword')}}",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        keyword: keyword,
                        keyword_city: keyword_city,
                        keyword_state: keyword_state
                    },
                    type: 'post',
                    success: function(result) {
                        console.log(result);
                        if(result == 0)
                            $.notify("Sorry but failed!",{style:'happyblue',className:'superblue'});
                        else {
                            $.notify("A keyword added successfully!",{style:'happyblue',className:'superblue'});
                            $('#google_search').css('display','none');
                            $('#wait_text').css('display','unset');
                        }
                    },
                    error: function(error) {
                        alert("Error");
                    }
                });
            }
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

