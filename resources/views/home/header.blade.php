<div class="header-body">
    <div class="header-title">
		<a class="hidden-xs" title="首页" href="{{ url() }}">TOP10</a>
		<a  href="{{ url('home') }}"><span class="glyphicon glyphicon-user"></span> <span>用户中心</span></a>
	</div>
    <div class="header-menu">
		 <a id="menu-clicker"><span class="glyphicon glyphicon-menu-hamburger"></span></a>
         <a title="退出" href="{{ url('auth/logout') }}"><span class="glyphicon glyphicon-log-out"></span></a>
    </div>
</div>
<div class="header-foot">
    <div class="current-menu">
        <a href="{{ url() }}">首页</a>
		<span class="glyphicon glyphicon-menu-right"></span>
		<a href="{{ url('home') }}">用户中心</a>
        <span class="glyphicon glyphicon-menu-right"></span>
        <span>{{ $menu }}</span>
    </div>
    <div class="today collapse hidden-xs"></div>
</div>

<div class="collapse menuNav">
	<div class="container-fluid">
		<div class="row my_box_nav">
			<?php $forums = App\Models\Forum::orderBy('id', 'asc')->get();?>
			@foreach($forums as $index => $forum)
				<div class="col-lg-1 col-md-2 col-sm-3 col-xs-4">
					<div class="box_linker" style="background-color:{{ $forum->color }}">
						<a href="{{ url('forum/'.$forum->brief) }}">
							<h5><?php echo $forum->title;?></h5>
						</a>
					</div>
				</div>
			@endforeach
        </div>
	</div>
</div>

