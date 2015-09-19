<?php  error_reporting(0);  ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<meta http-equiv="content-type" content="text/html; charset=<?php echo config_item('charset');?>" />
<base href="<?php echo $this->config->item('base_url') ?>/"/>

<link rel="stylesheet" href="www/css/main.css" type="text/css" media="screen" />
<link rel='stylesheet' href='www/lib/jquery/ui/themes/base/jquery.ui.all.css'   type='text/css' media="screen"/>

<script type="text/javascript" src="www/lib/jquery/jquery-1.5.2.min.js"></script>
<script type="text/javascript" src="www/lib/dataTable/js/jquery.dataTables.js"  ></script>
<script src="www/js/work.js"  type="text/javascript"></script>
<script type='text/javascript' src='www/lib/jquery/jquery-ui-1.8.16.custom.js'></script>
<script type='text/javascript' src='www/lib/extenal.js'></script>
<script type='text/javascript' src='www/lib/myDynamicUI/dynamicUI.js'></script>
<script type='text/javascript' src='www/lib/jquery/jquery.json-2.3.min.js'></script>
<script type='text/javascript' src='www/lib/json2.js'></script>
<script type="text/javascript" src="www/lib/jquery.ztree.core-3.0.min.js"></script>
<link rel="stylesheet" href="www/css/zTree.css" type="text/css">
<link rel="stylesheet" href="www/css/ztree/zTreeStyle/zTreeStyle.css" type="text/css">
<style type="text/css" title="currentStyle">
			@import "www/lib/dataTable/css/demo_page.css";
			@import "www/lib/dataTable/css/demo_table.css";
.dataTables_filter{display:none}
.dataTables_length{display:none}
</style>
<script>
function onCallClick(name,url){	
	//window.parent.iAddTab('外呼',url);
	location.href=url;	
}
function onProcessOrder(id){
	//alert(id);
	var url='<?php echo site_url('order/process')?>/'+id;
	window.parent.iAddTab("未命名",url);
}

$(document).ready(function() {
	//给时间控件付初值
	var ctime=new Date();
	$("#s_hour").get(0).selectedIndex="00";//index为索引值
	$("#s_min").get(0).selectedIndex="00"	
	$("#start_ymd").attr('value', ctime.format('yyyy-MM-dd'));	
	$("#e_hour").get(0).selectedIndex="23";//index为索引值
	$("#e_min").get(0).selectedIndex="59"
	$("#end_ymd").attr('value', ctime.format('yyyy-MM-dd'));
	
	//招生状态赋值
	function getDateString(ymd, hour, minut){
	 	return ymd+" "+hour+":"+minut+":00";
	}
	
	setDatePickerLanguageCn();
	$("#start_ymd").datepicker(); 
	$("#end_ymd").datepicker();   
	
	
	//填充工单类型
	/*var req={type:1,text:"工单类型"};
	$.post("<?php echo site_url("dictionary/ajaxGetKeyValue")?>",req,function(ret){
		$('#order_group').append("<option value='全部'>全部</option>");
		$.each(ret,function(index,row){
			if(row.name_text != '未填写')
				$('#order_group').append("<option value='"+row.name_text+"'>"+row.name_text+"</option>");
				else
				$('#order_group').append("<option selected='selected' value='"+row.name_text+"'>"+row.name_text+"</option>");
		});
	});
	
	var orderStatusValues=[{'name_text':'全部'},{'name_text':'新建'},{'name_text':'已处理'},{'name_text':'已回访'},{'name_text':'已关闭'}];
	$.each(orderStatusValues,function(index,row){
			if(row.name_text != '未填写')
				$('#order_status').append("<option value='"+row.name_text+"'>"+row.name_text+"</option>");
				else
				$('#order_status').append("<option selected='selected' value='"+row.name_text+"'>"+row.name_text+"</option>");
	});*/
	

	function getSearchString(){	
		var searchStr=[];
		//var opt=<?php echo json_encode($searchPanelTableData);?>;		
		var timeSearch=[];
		timeSearch.push("and");
		timeSearch.push("datetime");
		timeSearch.push("lastTime");
		timeSearch.push(getDateString($('#start_ymd').attr('value'), $('#s_hour').val(),$('#s_min').val()));
		timeSearch.push(getDateString($('#end_ymd').attr('value'), $('#e_hour').val(),$('#e_min').val()));
		searchStr.push(timeSearch);
		var row=[{'dbtype':'varchar','id':'order_id','type':2},{'dbtype':'varchar','id':'client_psss','type':2}];
		

		alert("111111111111111111111111");
			$.each(row,function(rowIndex,node){	
				
			 	var onSearchItem=[];
				/*onSearchItem.push("and");
				onSearchItem.push(node.dbtype);
				onSearchItem.push(node.id);
				onSearchItem.push($("#"+node.id).val());
				if(node.type ===2 && $("#"+node.id).val() != '全部' && $("#"+node.id).val() != '未填写' ){
					searchStr.push(onSearchItem);
				}	
				
				if(node.type ===1 && $("#"+node.id).value != ''){
					searchStr.push(onSearchItem);
				}	*/		
			});	
	
		//var ownerS=["and","varchar","owner",""];
		//ownerS[3]=$("#agentId").attr("value");
		
		//searchStr.push(ownerS);
		
		ret={agentId:'',searchText:[]};
		ret.agentId=$('#agentId').attr('value');
		
		ret.searchText=searchStr;

		//alert(searchStr);
	
		return (ret);
	}
	
	
	createTables=function (filterString){
		$('#dataList').dataTable( {
			"bProcessing": true,
			"bServerSide": true,
			"bStateSave" : false,
			"fnServerParams": function (aoData) {

				var externData={"name": "filterString", "value": ""};
				externData.value=filterString;
				aoData.push(externData);
			},
			"fnCreatedRow": function( nRow, aData, iDataIndex ) {
				//alert(aData[0]);
			 	
				
			 	$('td:eq(0)', nRow).html("<a href='#'>"+aData[0]+"</a>");
				$('td:eq(1)', nRow).html("<a href='#'>"+aData[1]+"</a>");
				$('td:eq(2)', nRow).html("<a href='#'>"+aData[2]+"</a>");
				$('td:eq(3)', nRow).html("<a href='#'>"+aData[3]+"</a>");
				$('td:eq(4)', nRow).html("<a href='#'>"+aData[4]+"</a>");
				$('td:eq(5)', nRow).html("<a href='#'>"+aData[5]+"</a>");
				$('td:eq(6)', nRow).html("<a href='#'>"+aData[6]+"</a>");
			},
			"sAjaxSource": "<?php echo site_url('order/ajaxOrderFormFind')?>",
			"oLanguage": {
				"sUrl": "<?php echo $this->config->item('base_url')?>/www/lib/dataTable/de_DE.txt"
			}
    	}); 
	}	
	
	createTables($.toJSON(getSearchString()));
	$("#btnSearch").click(function(){
		filterString=$.toJSON(getSearchString());
		alert("22222222222");
		var oTable = $('#dataList').dataTable();
		
		oTable.fnDestroy();	
		createTables(filterString);	
	});
	
	function refreashTable(){
		var oTable = $('#dataList').dataTable();
		oTable.fnDestroy();	
		createTables(getSearchString());
	}
		
	//导出文件
	/*$("#btnExport").click(function(){	
		 var req={"filterString":""}
		 var reqParam={"searchType":1,"agentId":"","searchText":[]};
		 reqParam.agentId=$('#agentId').attr('value');
		 reqParam.searchText=getSearchString();
		 req.filterString=JSON.stringify(reqParam);
		 $("#csvUrl").html("");
		 $.post("<?php echo site_url('export/ajaxClientExport')?>",req,function(res){	
			$("#csvUrl").attr("href", res.path);
			$("#csvUrl").html(res.fileName);					  							
		});  	
	});	*/
	
	 $('#example tbody tr').live('dblclick', function(){
		window.parent.iAddTab("详细资料","<?php echo site_url('communicate/connected') ?>/manulClick/"+$('#agentId').attr('value')+"/"+this.id);		
	  });
});
</script>    
</head>
<body>
<!--<input id='agentId' type="hidden" value="<?php  echo $agentId;?>">-->
<div class="page_main page_tops">
	<div class="page_nav">
         <div class="nav_ico"><img src="www/images/page_nav_ico.jpg" /></div>
         <div class="nav_">当前位置：&gt; 工单查询</div>
         <div class="nav_other"></div>
	</div>
    <div class="func-panel">
			 <div class="left">
                从
                <input type="text" name="start_ymd"   id="start_ymd" value="" style="width:80px"/>
				<?php echo form_dropdown('s_hour',$beginTime['hourOptions'],$beginTime['hourDef'],'id="s_hour"')?><?php echo form_dropdown('s_min',$beginTime['minOptions'],$beginTime['minDef'],'id="s_min"')?>
                到
                <input type="text" name="end_ymd"   id="end_ymd" value="" style="width:80px"/>
  <?php echo form_dropdown('e_hour',$endTime['hourOptions'],$endTime['hourDef'],'id="e_hour"')?><?php echo form_dropdown('e_min',$endTime['minOptions'],$endTime['minDef'],'id="e_min"')?>
  				订单状态<select id="order_status"></select>
                订单类型<select id="order_group"></select>
                <input type="button" id="btnSearch" value="搜索" class="btnSearch"/>
                <input type="button" id="btnExport" value="导出" class="btnSearch"/>
                <a id="csvUrl" href='export_datas/clients_09Apr12.csv'></a>
			 </div>
			 <div align='right' class="right">
			
			 </div>		
			 <div style="clear:both;"></div>  
	</div>	
  
     <div id="example" style='display:block'>
          <table width="100%" cellpadding="0" cellspacing="0" border="0"  id="dataList" >
          	<thead>       
            		<tr>      
						<th align="left">订单单号</th>
                        <th align="left">状态</th>
                        <th align="left">产品</th>
                        <th align="left">客户姓名</th>
                        <th align="left">来电号码</th>
                        <th align="left">地址</th>
            			<th align="left">快递</th>
                        <th align="left">登记时间</th>
                        <th align="left">话务员</th>
                        <th align="left">来电时间</th>
                      
                    </tr>                    
            </thead>
            <tbody></tbody>	
          </table>
      </div>
</div>
</body>
</html>