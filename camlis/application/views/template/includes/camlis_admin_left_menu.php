<div class="col-sm-3 adm-left-menu">
	<div class="list-group">
		<?php
			$method	= $this->router->method == 'index' ? 'laboratory' : $this->router->method;
		
			foreach($left_menu as $row) {
				$selected = '';
				if (preg_match('/.*'.strtolower($method).'.*/i', strtolower($row->label))) {
					$selected = "selected";
				}
				
				echo "<a href='".site_url($row->link)."' class='list-group-item ".$selected."'>"._t('global.'.$row->label)."</a>";  
			}
		?>
	</div>
</div>