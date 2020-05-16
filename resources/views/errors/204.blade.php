<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>No Content</title>
    <style>
        html, body {
            height: 100%;
            font-family: "Microsoft YaHei", Arial, Helvetica, sans-serif, "宋体";
            background-color:#000;
        }

        body {
            margin: 0;
            padding: 0;
            width: 100%;
            color: #B0BEC5;
            display: table;
            font-weight: 100;
            font-family: 'Lato';
        }

        .container {
            text-align: center;
            display: table-cell;
            vertical-align: middle;
        }

        .content {
            text-align: center;
            display: inline-block;
        }

        .title {
            font-size: 18px;
            margin-bottom: 40px;
        }

        .content img {
            width:100%;
            margin-bottom:10px;
            border-radius: 3px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="content">
        <img src="{{ url() }}/images/404_page.jpg">
        <div class="title">没找到您要的页面喔!</div>
    </div>
</div>
</body>
</html>