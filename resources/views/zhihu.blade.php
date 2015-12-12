<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>Document</title>
    <link rel="stylesheet" href="//cdn.bootcss.com/bootstrap/3.3.5/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <div class="panel panel-default">
        <div class="panel-body">
            <table id="table" class="table table-hover table-responsive table-striped">
                <thead>
                    <tr>
                        <th class="col-md-6">标题</th>
                        <th class="col-md-3">链接</th>
                        <th class="col-md-1">关注数</th>
                        <th class="col-md-1">回答数</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($zhi_hus as $zhihu)
                        <tr>
                            <td>{{ $zhihu->title }}</td>
                            <td>{{ $zhihu->url }}</td>
                            <td>{{ $zhihu->answer_num }}</td>
                            <td>{{ $zhihu->concerned_num }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>