<div style="margin-left:40%">
	You have only report assigned<br /> <br /> 
    <?php 
		foreach($report_name as $row){
	?>	
		<i class="fa fa-building-o"></i>&nbsp;
        <a href="<?php echo $this->app_language->site_url($row->url); ?>"><?php echo $row->report_name;?></a><br /> 
	<?php 
		}
	?>
</div>