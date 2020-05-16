    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
	<link rel="apple-touch-icon-precomposed" href="{{ url() }}/images/webIcon-128.png">
    <link rel="shortcut icon" href="{{ url() }}/images/favicon.png">

    <title>今日十大</title>

    <link href="{{ url() }}/bower/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="{{ url() }}/bower/swipebox-master/src/css/swipebox.min.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="//cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="//cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    <link href="{{ url() }}/css/myApp.css" rel="stylesheet">

    <script>
        var siteJson={
			base_url   : '{{ url() }}',
			get_url    : function(str) {
				return this.base_url+ '/' + str;
			}
		};
    </script>