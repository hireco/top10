<div class="header-body">
    <a title="今日十大top10.pub" class="header-title" href="{{ url() }}">TOP10</a>
    <div class="header-menu">
         <a id="menu-clicker"><span class="glyphicon glyphicon-menu-hamburger"></span></a>
    </div>
</div>
<div class="header-foot">
    <div class="current-menu">
        <a href="{{ url() }}">今日十大</a>
        <span class="glyphicon glyphicon-menu-right"></span>
        <span>{{ $menu }}</span>
    </div>
    <div class="today collapse"></div>
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

