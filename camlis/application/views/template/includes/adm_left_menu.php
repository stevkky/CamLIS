<div class="col-sm-3 adm-left-menu">
	<div class="list-group">
		<?php
			$method	= $this->router->method == 'index' ? 'laboratory' : $this->router->method;

			foreach($left_menu as $row) {
				$link		= ($row->link == '#' || empty($row->link)) ? 'javascript:void(0)' : base_url().$row->link;
				$selected	= "";

				if (preg_match('/.*'.strtolower($method).'.*/i', $row->label)) {
					$selected = "selected";
				}
				
				echo "<a href='$link' class='list-group-item $selected'>"._t('global.'.$row->label)."</a>";
			}
		?>
	</div>
</div>