<span style="font-size: 20px;">{{ $demo->title }}</span>,
<br>
{!! preg_replace( "/\r|\n/", "", $demo->text ) !!}
<br>
Thank You,
{{ $demo->sender }}
