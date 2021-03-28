<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->

        <!-- Styles -->
    </head>
    <body>
    	<p>You are Invited to Join the upcoming Leauge</p>
        <p><a href="{{$data['link']}}" target="_blank">Click Here</a></p>
    </body>
</html>
