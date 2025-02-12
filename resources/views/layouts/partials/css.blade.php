<link rel="stylesheet" href="{{ asset('css/vendor.css?v='.$asset_v) }}">

@if( in_array(session()->get('user.language', config('app.locale')), config('constants.langs_rtl')) )
	<link rel="stylesheet" href="{{ asset('css/rtl.css?v='.$asset_v) }}">
@endif

@yield('css')

<!-- app css -->
<link rel="stylesheet" href="{{ asset('css/app.css?v='.$asset_v) }}">

@if(isset($pos_layout) && $pos_layout)
	<style type="text/css">
		.content{
			padding-bottom: 0px !important;
		}
	</style>
@endif
<style type="text/css">
	/*
	* Pattern lock css
	* Pattern direction
	* http://ignitersworld.com/lab/patternLock.html
	*/
	.patt-wrap {
	  z-index: 10;
	}
	.patt-circ.hovered {
	  background-color: #cde2f2;
	  border: none;
	}
	.patt-circ.hovered .patt-dots {
	  display: none;
	}
	.patt-circ.dir {
	  background-image: url("{{asset('/img/pattern-directionicon-arrow.png')}}");
	  background-position: center;
	  background-repeat: no-repeat;
	}
	.patt-circ.e {
	  -webkit-transform: rotate(0);
	  transform: rotate(0);
	}
	.patt-circ.s-e {
	  -webkit-transform: rotate(45deg);
	  transform: rotate(45deg);
	}
	.patt-circ.s {
	  -webkit-transform: rotate(90deg);
	  transform: rotate(90deg);
	}
	.patt-circ.s-w {
	  -webkit-transform: rotate(135deg);
	  transform: rotate(135deg);
	}
	.patt-circ.w {
	  -webkit-transform: rotate(180deg);
	  transform: rotate(180deg);
	}
	.patt-circ.n-w {
	  -webkit-transform: rotate(225deg);
	   transform: rotate(225deg);
	}
	.patt-circ.n {
	  -webkit-transform: rotate(270deg);
	  transform: rotate(270deg);
	}
	.patt-circ.n-e {
	  -webkit-transform: rotate(315deg);
	  transform: rotate(315deg);
	}

	.content-wrapper {
	border-radius: 20px;
	margin: 10px 10px 10px 250px;
	}

	.gradient-text {
		font-size: 24px;
		font-weight: bold;
		background: linear-gradient(90deg, #042761 0%, rgba(4,153,209,1) 100%);
		-webkit-background-clip: text;
		-webkit-text-fill-color: transparent;
		display: inline-block;
	}

	.btn-block{
		background: linear-gradient(90deg, #042761 0%, rgba(4,153,209,1) 100%);
		border-radius: 5px;
	}
	.table thead th {
		background-color: #042761;
		color: white;  
		text-align: center;     
		padding: 10px; 
		border-color: #dddddd; 
	}

	/* Style the search box */
	.dataTables_filter input {
		width: 250px; 
		padding: 8px 12px;
		border: 2px solid #0d52c2;
		border-radius: 5px;
		outline: none;
		font-size: 14px;
	}

	.dataTables_filter input:focus {
		border-color: #042761; 
		box-shadow: 0 0 5px #031e4b;
	}

	.box-primary {
		border-radius: 30px;

	}
	.box-primary .box-title {
		font-weight: bold;
	}
	.box-filter {
		border-radius: 30px;

	}

	.box-widget {
		border-radius: 30px;
	}

	h4.gradient-model {
			font-weight: bold;
			background: linear-gradient(90deg, #042761 0%, rgba(4,153,209,1) 100%);
			-webkit-background-clip: text;
			-webkit-text-fill-color: transparent;
			display: inline-block;
	}
	.btn-default{
		color: #fff;	background: linear-gradient(90deg, #610404 0%, rgb(163, 21, 33) 100%);
	}

	.modal-content {
		border-radius: 15px;
	}
	.btn-primary{
		background: linear-gradient(90deg, #042761 0%, rgba(4,153,209,1) 100%);
	}
	.btn-cancel{
		color: #fff;	background: linear-gradient(90deg, #610404 0%, rgb(163, 21, 33) 100%);
	}
	.btn-info{
		background: linear-gradient(90deg, #3b80ee 0%, rgb(52, 162, 202) 100%);
	}
	.btn-danger{
		color: #fff;	background: linear-gradient(90deg, #d12222ea 0%, rgb(141, 42, 50) 100%);
	}
</style>
@if(!empty($__system_settings['additional_css']))
    {!! $__system_settings['additional_css'] !!}
@endif