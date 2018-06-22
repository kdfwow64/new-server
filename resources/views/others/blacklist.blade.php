@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 col-md-offset-0">
            <div class="panel panel-default">
                <div class="panel-heading">Black List</div>

                <div class="panel-body black-list" style="text-align: center;">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif
                    <div class="row">
                        <div class="col-md-4">
                            <form action="{{url("blacklist/insertD")}}"  method="post">
                                <div class="row" id="blacklist_insert_div1">
                                    <input type="input" class="form-control input-box" id="blacklist_domain" style="border: solid 1px;" placeholder="Please input domain..." name="blacklist_domain">
                                    <button type="submit" id="blacklist_insert_btn1" class="btn btn-warning" style="margin-left: 10px;">Insert</button>
                                </div>
                                {{csrf_field()}}
                            </form>

                            <div class="row" id="blacklist_show_div1">
                                <table id="blacklist_table1" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <td style="width: 10%;">No</td>
                                            <td>Domain Name</td>
                                            <td style="width: 10%;"><i class="fa fa-trash" style="font-size: 20px;"></i></td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($blacklist as $key => $row)
                                            @if($row->domainORemail == 1)
                                            <tr>
                                                <td>{{++$domain_count}}</td>
                                                <td>{{$row->domain}}</td>
                                                <td style="width: 10%;"><a href="{{ url('blacklist/delete/'.$row->id) }}"><i class="fa fa-trash del" style="font-size: 20px;"></i></a></td>
                                            </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>


                        <div class="col-md-4">
                            <form action="{{url("blacklist/insertE")}}"  method="post">
                                <div class="row" id="blacklist_insert_div2">
                                    <input type="input" class="form-control input-box" id="blacklist_email" style="border: solid 1px;" placeholder="Please input email..." name="blacklist_email">
                                    <button type="submit" id="blacklist_insert_btn2" class="btn btn-warning" style="margin-left: 10px;">Insert</button>
                                </div>
                                {{csrf_field()}}
                            </form>

                            <div class="row" id="blacklist_show_div2">
                                <table id="blacklist_table2" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <td style="width: 10%;">No</td>
                                            <td>Email</td>
                                            <td style="width: 10%;"><i class="fa fa-trash" style="font-size: 20px;"></i></td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($blacklist as $key => $row)
                                            @if($row->domainORemail == 2)
                                            <tr>
                                                <td>{{++$email_count}}</td>
                                                <td>{{$row->domain}}</td>
                                                <td style="width: 10%;"><a href="{{ url('blacklist/delete/'.$row->id) }}"><i class="fa fa-trash del" style="font-size: 20px;"></i></a></td>
                                            </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <form action="{{url("blacklist/insertN")}}"  method="post">
                                <div class="row" id="blacklist_insert_div3">
                                    <input type="input" class="form-control input-box" id="blacklist_name" style="border: solid 1px;" placeholder="Please input name..." name="blacklist_name">
                                    <button type="submit" id="blacklist_insert_btn3" class="btn btn-warning" style="margin-left: 10px;">Insert</button>
                                </div>
                                {{csrf_field()}}
                            </form>

                            <div class="row" id="blacklist_show_div3">
                                <table id="blacklist_table3" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <td style="width: 10%;">No</td>
                                            <td>Name</td>
                                            <td style="width: 10%;"><i class="fa fa-trash" style="font-size: 20px;"></i></td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($blacklist as $key => $row)
                                            @if($row->domainORemail == 3)
                                            <tr>
                                                <td>{{++$name_count}}</td>
                                                <td>{{$row->domain}}</td>
                                                <td><a href="{{ url('blacklist/delete/'.$row->id) }}"><i class="fa fa-trash del" style="font-size: 20px;"></i></a></td>
                                            </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $("#blacklist_navbar").css('color','#cc02e2');
        $("#blacklist_navbar").css('font-weight','bold');

        $('#blacklist_domain').keyup(function() {
            var str = $('#blacklist_domain').val();
            $('#blacklist_table1 tbody').html("");
            $.ajax({
                url: "{{url('blacklist/getDomains')}}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    str: str
                },
                type: 'post',
                success: function(result) {
                    for(i = 0; i < result.length ; i++) {
                        $('#blacklist_table1 tbody').append("<tr><td>"+(i+1)+"</td><td>"+result[i]['domain']+"</td><td><a href="+"{{url('blacklist/delete')}}"+"/"+result[i]['id']+"><i class='fa fa-trash del' style='font-size: 20px'></i></a></td></tr>");
                    }
                    console.log(result);
                },
                error: function(error) {
                    alert("Error");
                }
            });
            
        });

        $('#blacklist_email').keyup(function() {
            var str = $('#blacklist_email').val();
            $('#blacklist_table2 tbody').html("");
            $.ajax({
                url: "{{url('blacklist/getEmails')}}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    str: str
                },
                type: 'post',
                success: function(result) {
                    for(i = 0; i < result.length ; i++) {
                        $('#blacklist_table2 tbody').append("<tr><td>"+(i+1)+"</td><td>"+result[i]['domain']+"</td><td><a href="+"{{url('blacklist/delete')}}"+"/"+result[i]['id']+"><i class='fa fa-trash del' style='font-size: 20px'></i></a></td></tr>");
                    }
                    console.log(result);
                },
                error: function(error) {
                    alert("Error");
                }
            });
            
        });

        $('#blacklist_name').keyup(function() {
            var str = $('#blacklist_name').val();
            $('#blacklist_table3 tbody').html("");
            $.ajax({
                url: "{{url('blacklist/getNames')}}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    str: str
                },
                type: 'post',
                success: function(result) {
                    for(i = 0; i < result.length ; i++) {
                        $('#blacklist_table3 tbody').append("<tr><td>"+(i+1)+"</td><td>"+result[i]['domain']+"</td><td><a href="+"{{url('blacklist/delete')}}"+"/"+result[i]['id']+"><i class='fa fa-trash del' style='font-size: 20px'></i></a></td></tr>");
                    }
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