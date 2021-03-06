<!DOCTYPE html>
<html>
<head>
<?php require("showheader.php"); ?>
<title>执信·青志 - 地点一览</title>
<?php require("showcss.php"); ?>
<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<?php
	header("content-type:text/html;charset=utf-8");
	if($_POST){
		if($_POST['times']===NULL||!$_POST['loc_id']===NULL){
			echo("post的信息不完整，请重试。");die();
		}
		$times=$_POST['times'];$loc_id=$_POST['loc_id'];
		require("to_json.php");
		$maxTimes=sizeof($a[$_POST['loc_id']]->times)-1;
		$maxLoc=sizeof($a)-1;
		if($loc_id>$maxLoc||$loc_id<0||!is_numeric($loc_id)){
			echo("location id不合法。".$maxLoc);die($json);
		}
		if(!is_numeric($times)||$times>$maxTimes||$times<0){
			echo("选择的时段不合法。");die();
		}
		if($a[$loc_id]->disabled==1||$alldisabled==1){
			die("报名已关闭");
		}
		session_start();
		$_SESSION['loc_id']=$loc_id;$_SESSION['times']=$times;
		header("Location: /signup.php");die();
	}
?>

<body style="font-family:Microsoft Yahei">
<?php include("shownav.php"); ?>

<div class="container-fluid">
  <div class="row">
    <div class="col-md-6 col-md-offset-3">
      <h1 class="text-center">地点一览</h1>
    </div>
  </div>
  <hr>
</div>
<div class="container">
  <div class="row text-center">
    <div class="col-md-6 col-md-offset-3">欢迎同学们来青志网参加报名！<br>世界这么大，志愿服务地点这么多，先看看自己心仪的地方吧~ <br>
		P.S. <span style="color:red">可以同时报名</span>多个服务点哦 ~</div>
  </div>
  <hr>
	<div class="row" id="puthere">
		<center id='loading'><img src="/img/loading.gif"><br><br>正在加载志愿地点信息，稍安勿躁哦~</center><br>
		<?php
			require("to_json.php");
			$ret = '';
			for ( $i = 0; $i < sizeof( $a ); $i++ ) {
				$ret .= "<div class='text-justify col-sm-4'>";
				if ( $alldisabled == 1 || $a[$i]->disabled == 1 ) $ret .= "<div class='panel panel-disabled'><div class='panel-heading'><h3 style='color:black'";
				else $ret .="<div class='panel panel-{$a[$i]->color}'><div class='panel-heading'><h3";
				$ret .= " class='panel-title text-center'><b>{$a[$i]->name}</b></h3></div><div class='panel-body text-center row'>";
				$ret .= "<img class='tu2 col-md-10 col-md-offset-1 col-sm-12 col-xs-12' src='{$a[$i]->image}'></div><div class='panel-footer text-center'>";
				if ( $alldisabled == 1 ) $ret .= "报名期限已过，请耐心等待下一轮哟~";
				elseif ( $a[$i]->disabled == 1 ) $ret .= $a[$i]->whydisabled;
				else $ret .= $a[$i]->minintro . "<br><button data-id='{$i}' onclick='showloc(this.dataset.id)' class='btn btn-sm btn-{$a[$i]->color}'>&gt;点我报名&lt;</button>";
				$ret .= "</div></div></div>";
			}
			echo($ret);
		?>
  </div>
</div>

<?php
include("showbanner.php");
require("showjs.php");
showjs( ["js/jquery-1.11.2.min.js", "js/bootstrap.min.js", "js/material.min.js", "js/ripples.min.js"],
				["defer", "defer", "defer", "defer"], ['updateInfo();', null, null, null] );
?>
<script>
	loc = {};
	function updateInfo() {
		$.ajax({url:"location.json?"+new Date().getTime(),dataType:"json",type:"GET",success:function(got){
			loc=got.loc;
			$("#loading").slideUp();
		},error:function(){
			alert("志愿服务地点信息加载失败！\n请刷新页面重试。");
		}});
	}

	window.onload = function() {
		$.material.init();
	}
</script>
<div class="modal fade" id="myModal">
  <form action="/location.php" method="post" id="frm">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
        <h3 id='loc_name' class="modal-title">提示</h3>
      </div>
      <div class="modal-body">
        <p id='msg'></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">&lt; 了解</button>
        <button type="button" class="btn btn-success" onclick="verify()">报名 &gt;</button>
        <script>
			function isChecked(){aa=$("[name='times']");for(ii in aa){if(aa[ii].checked){return true;}}return false;}
			function getSel(){aa=$("[name='times']");for(ii in aa){if(aa[ii].checked){return aa[ii].value;}}}
			function verify(){
				if(current<1||current>=loc.length){
					alert("location id不合法，请检查。");return 0;
				}
				if(!isChecked()||getSel()<0||getSel()>times_max){
					alert("请选择正确的时段。");return 0;
				}
				t=document.createElement("input");
				t.type="text";t.name="loc_id";t.hidden=true;t.value=current;t.id="temp";
				$("#frm").append(t);
				$("#frm").submit();
				$("#temp").remove();
			}
		</script>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
  </form>
</div><!-- /.modal -->
<script>
	function alt(msg,title){
		if(title){$("#loc_name")[0].innerHTML=title;}
		$("#msg")[0].innerHTML=msg;
		$("#myModal").modal('show');
	}
	function tr(sth){
		return "<tr>"+sth+"</tr>";
	}
	function th(sth){
		switch(sth){
			case "area":sth="地区";break;
			case "addr":sth="地址";break;
			case "traffic":sth="交通";break;
			case "works":sth="工作";break;
			case "times":sth="时段";break;
			case "comm":sth="备注";break;
			case "addrE":sth="地图";break;
			case "hours":sth="工时";break;
			default: return;
		}
		return "<th>"+sth+"</th>";
	}
	function td(sth){
		return "<td>"+sth+"</td>";
	}
	var tb='';
	function gen(r){
		tb=document.createElement("table");
		tb.className="table table-striped table-hover table-bordered";
		tb.style.borderRadius="5px";tb.style.borderCollapse="separate";
		tb.innerHTML="";
		var tmd="";var tmp="";
		for(i in loc[r]){
			//XXX: Using isArray?
			if(i=="works"||i=="times"||i=="comm"){
				//using tmd instead of innerHTML or browser will add <!--/tr--> automaticly
				tmd+="<tr>"+th(i);
				if(i=="times"){
					tmp='<div class="radio">';
					for(j=0;j<loc[r][i].length;j++){
						tmp+='<label style="color:black"><input type="radio" name="times" value="'+j+'">'+loc[r][i][j]+'</label><br>';
					}
					tmp+="</div>";
					times_max=loc[r][i].length;
				}else{
					for(j in loc[r][i]){
						tmp+=loc[r][i][j]+"<br>";
					}
				}
				tmd+=td(tmp)+"</tr>";
				tmp="";
				tb.innerHTML=tmd;
			}else if(i=="addrE"){
				tmd+=tr(th(i)+td("<a href='"+loc[r][i]+"' target='view_window'>点此查看</a>"));
			}else{
				if(!(h=th(i))){continue;}
				tmd+=tr(h+td(loc[r][i]));
			}
		}
	}
	var current=0;var times_max=0;
	function showloc(id){
		current=id;
		//-1 for array
		gen(id);
		alt('',loc[id].name);
		e="onerror=\"this.src=\'/img/noimg.jpg\'\"";
		$('#msg')[0].innerHTML="<img src='"+loc[id].image+"' style='width:100%' class='tu text-center' "+e+">";
		$('#msg').append(tb);
		$.material.init();
	}
</script>
</body>
</html>
