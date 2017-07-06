<?php
class ViewUserList extends View
{
	public function getView()
	{
		ob_start();
		?>
		<style type="text/css">
		.data_row2{color: #235A81;}
		.a-status-0{color: red;}
		.a-status-1{color: green;}
		.data_row1{background: #CCC;}
		#wrapper {
		height: <?php echo (500+(count($this->data)-10)*20);?>px;
		}
		</style>

		<table align="center" color="green">
			<caption><b> Список пользователей </b></caption>
			<tbody>
			<tr class="data_row_header">
				<th class="dataheader" align="center">
					<span class="nowrap">
						логин
					</span>
				</th>
				<th class="dataheader" align="center">
					<span class="nowrap">
						статус
					</span>
				</th>
				<th class="dataheader" align="center">
					<span class="nowrap">
						профиль
					</span>
				</th>
				
				<th class="dataheader" align="center">
					<span class="nowrap">
						активировать
					</span>
				</th>
				
				<th class="dataheader" align="center">
					<span class="nowrap">
						выслать ключ 
					</span>
				</th>
			</tr>	
				
			
<?php
/* вывод данных в таблицу */
		
			$i=0; // для чередования цветов строк
			foreach($this->data as $row)
			{
				?>
				<tr class="data_row<?php echo $i%2+1; $i++;?>"><?php /* начало строки */
				
					/* поля данных */
					?>
					<td class="datacell" align="left">
						<span class="nowrap">
							<a href="<?php echo APP_DIR_PATH."/user/edit/".$row['id']; ?>">
							<?php echo $row['username'];?>
							</a>
						</span>
					</td>
					<td class="datacell" align="left">
						<span class="nowrap">
							<a class="a-status-<?php echo $row['status'] ?>" href="<?php echo APP_DIR_PATH."/user/edit/".$row['id']; ?>">
							<?php echo $row['status']?"активирован":"не активирован";?>
							</a>
						</span>
					</td>
					<td class="datacell" align="left">
						<span class="nowrap">
							<a href="<?php echo APP_DIR_PATH."/user/edit/".$row['id']; ?>">
							<?php echo MyApp::getProfileName($row['userprofile']);?>
							</a>
						</span>
					</td>
					
					<td class="datacell" align="left">
						<span class="nowrap">
							<a class="a-status-<?php echo $row['status'] ?>" href="<?php echo APP_DIR_PATH."/user/activate/".$row['id']; ?>">
							<?php echo $row['status']?" ":"активировать";?>
							</a>
						</span>
					</td>
					
					<td class="datacell" align="left">
						<span class="nowrap">
							<a class="a-status-<?php echo $row['status'] ?>" href="<?php echo APP_DIR_PATH."/user/send_activate/".$row['id']; ?>">
							<?php echo $row['status']?" ":"выслать ключ";?>
							</a>
						</span>
					</td>
										
				</tr><?php /* конец строки */
			}
				
		/* конец таблицы */?>
		</tbody>
		</table>
		
			
		<?php
		$cont=ob_get_contents();
		ob_end_clean();
		return $cont;
	}
}