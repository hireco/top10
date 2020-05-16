<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <link rel="shortcut icon" href="{{ url() }}/images/favicon.png">

    <title>@yield('page_title') - 今日十大</title>

    <link href="{{ url() }}/bower/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="//cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="//cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    <link href="{{ url() }}/css/myUser.css" rel="stylesheet">

    <script>
        var siteJson={
            base_url   : '{{ url() }}',
            get_url    : function(str) {
                return this.base_url+ '/' + str;
            }
        };
    </script>

    <script src="{{ url() }}/bower/jquery/dist/jquery.min.js"></script>
    <script src="{{ url() }}/bower/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="{{ url() }}/bower/moment/min/moment.min.js"></script>
    <script src="{{ url() }}/bower/moment/locale/zh-cn.js"></script>
    <script src="{{ url() }}/bower/jquery.cookie/jquery.cookie.js"></script>
    <script src="{{ url() }}/bower/jquery_lazyload/jquery.lazyload.min.js"></script>
    <script src="{{ url() }}/bower/jquery-form/jquery.form.js"></script>
    <script src="{{ url() }}/js/myUser.js"></script>

    @section('script')
    @show

</head>

<body>

<div class="header">
    @include('home.header',['menu' => $menu])
</div>

<div class="body">
    <div class="container myContainer">
        @section('main')
        @show
    </div>
    <div class="bodyMask collapse"></div>
</div>

@section('fixedSection')
    <div class="ajaxLoader collapse"></div>
@show

<div class="footer">
    @include('logged')
</div>

</body>

</html>