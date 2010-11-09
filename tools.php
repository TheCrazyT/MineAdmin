<?php
$pageid = "tools";
require_once('header_inc.php');
require_once('includes/header.php');
$pageid = "tools";
function stop_server() {
	shell_exec("screen -S Minecraft -p 0 -X stuff `printf 'stop.\r'`; sleep 5");
}
if($_GET['action'] == "backup") {
	$minecraft->backup($_POST['backup_name'],$_POST['backup_comment']);
} elseif($_GET['action'] == "dl") {
    $result=$db->fetch_sql("SELECT filename FROM `backups` WHERE id = ".$_GET['id']);
	$path = $PATH['backups'].$result['filename'];
	if (file_exists($file)) {
	    header('Content-Description: File Transfer');
	    header('Content-Type: application/octet-stream');
	    header('Content-Disposition: attachment; filename='.basename($path));
	    header('Content-Transfer-Encoding: binary');
	    header('Expires: 0');
	    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	    header('Pragma: public');
	    header('Content-Length: ' . filesize($path));
	    ob_clean();
	    flush();
	    readfile($path);
	    exit;
	}
} elseif($_GET['action'] == "delete") {
	backup_delete($_GET['id']);
} elseif($_GET['action'] == "restore") {
	$result=$db->fetch_sql("SELECT filename FROM `backups` WHERE id = ".$_GET['id']);
	stop_server();
	$restore = shell_exec('tar xvfz -C '.$PATH['minecraft'].' '.$PATH['backup'].$result['filename']);
	shell_exec('screen -dmS Minecraft java -Xmx'.$GENERAL["memory"].' -Xms'.$GENERAL["memory"].' -jar /opt/Minecraft_Mod.jar');
	echo "<div class='success' style='display:block;'>Started server!</div>";
}

?>
	<div id="page_wrap">
		<div id="online_wrap">
			<h1>Tools</h1>
			<span id="online"></span>
			
		</div>
		<div class="info">Info message</div>
		        <div class="success">Successful operation message</div>
		        <div class="warning">Warning message</div>
		        <div class="error">Error message</div>
		<div id="actions">
			<p><a href="#"><img src="images/icons/asterisk_yellow.png">Start</a>&nbsp;<a href="#"><img src="images/icons/stop.png">Stop</a>&nbsp;<a href="#"><img src="images/icons/arrow_refresh.png">Restart</a></p>&nbsp;
		</div>
		<div id="backup_wrap">
		<h1>Backups</h1>
		<a href="#newbackup" id="addnew">new backup</a>
		<div id="newbackup" style="display:none;margin: 15px 10px;">
			<form action="tools.php?action=backup" method="post">
				<label>Backup Name
				<input class="input_text" type="text" name="backup_name" style="width:200px;margin-left:10px" />
				</label>
				<label>Comment
				<input class="input_text" type="text" name="backup_comment" style="width:200px;margin-left:10px" />				
				</label>
				<span style="float:right;"><input class="button" type="submit" value="Save" /><input class="button" id="canceladd" type="button" value="Cancel" /></span>
			</form>
		</div>
		<table>
			<tr>
				<th>Name</th>
				<th>Date</th>
				<th>Time</th>
				<th>Size</th>
				<th>Comment</th>
				<th>Actions</th>
			</tr>
			<?php
            foreach($minecraft->backup_list() as $backup){
            ?>
			<tr>
				<td><?php echo $backup['name']; ?></td>
				<td><?php echo $backup['date']; ?></td>
				<td><?php echo $backup['time']; ?></td>
				<td><?php echo $backup['size']; ?></td>
				<td><?php echo $backup['comment']; ?></td>
				<td><a href="tools.php?action=dl&id=<?php echo $backup['id']; ?>"><img src="images/icons/database_save.png" alt="Download"></a>&nbsp;<a href="tools.php?action=delete&id=<?php echo $backup['id']; ?><img src="images/icons/database_delete.png" alt="Delete"></a>&nbsp;<a href="tools.php?action=restore&id=<?php echo $backup['id']; ?><img src="images/icons/database_go.png" alt="Restore"></a>&nbsp;</td>
			</tr>
			<?php
		}
			?>
		</table>
		</div>
		<div id="map_wrap">
			<h1>Mapping</h1>
			<p>Your last map generation was: NEVER</p>
			<p>You can access your map at xxxxxxx.minecraftservers.com/map/</p>
			<p><a href="#">Start a new map job</a></p>
		</div>
	</div>
	<script type="text/javascript">
		$(function(){
			$('#addnew').click(function(){
				$('#newbackup').slideDown();
			});
			$('#canceladd').click(function(){
				$('#newbackup').slideUp();
			});
		});
	</script>
<?php require_once('includes/footer.php'); ?>
