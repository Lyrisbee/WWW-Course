<?php
	session_start();
	ob_start();
?>
<!DOCTYPE html>
<html>

<?php
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	include("db/db_config.php");
	if(isset($_POST['logoutbtn'])){
		unset($_COOKIE['name']);
		unset($_COOKIE['pwd']);
		setcookie('name', null, -1, '/');
		setcookie('pwd', null, -1, '/');
		unset($_SESSION['name']);
		header('Location: login.php');
		exit;
	}
	if(!isset($_SESSION['name'])){
			header('Location: login.php');
			exit; //prevents further page loading
	}else{
		$username = $_SESSION['name'];
	}
	if(isset($_POST['likeButton'])){
		$id = $_POST['postid'];
		$likesql = "SELECT postlike FROM postlike2 where postid = ? AND user = ?";
		$usernum = $db -> prepare($likesql);
		$usernum -> execute(Array($id,$username));
		$likeornot = $usernum -> fetch(PDO::FETCH_ASSOC);
		if(!$likeornot){
			$likeinsert = "INSERT INTO postlike2 (user, postid, postlike) VALUES (?,?,?)";
			$likeupd = $db -> prepare($likeinsert);
			$likeupd -> execute(Array($username,$id,1));
			header("Location: index.php#".$id);

		}else{
			if($likeornot['postlike']==0){
				$likeornot['postlike']=1;
			}else{
				$likeornot['postlike']=0;
			}
			$likeupdate = "UPDATE postlike2 SET postlike= ? WHERE postid = ? AND user = ?";
			$likeupd = $db -> prepare($likeupdate);
			$likeupd -> execute(Array($likeornot['postlike'],$id,$username));
			header("Location: index.php#".$id);

		}

	}elseif(isset($_POST['commentbtn'])){
		if(isset($_SESSION['uploadsuccess'])){
			echo "<script>alert('請先取消上傳圖片');
			window.location.href='index.php#post';
			</script>";
		}else{
			$id = $_POST['postid'];
			$text = $_POST['commentval'];
			$insert_comment="INSERT INTO comment2 (value,postid,owner,time) VALUES(?,?,?,?)";
			$sth = $db->prepare($insert_comment);
			$sth->execute(Array($text,$id,$username, date('Y-m-d H:i:s')));
			header("Location: index.php#".$id);
		}
		
	}elseif(isset($_POST['cancelbtn'])){
		$delete_image="DELETE FROM image3 where id = ?";
		$sth = $db->prepare($delete_image);
		$sth->execute(Array($_POST['imgid']));
		unset($_SESSION['uploadsuccess']);
		header("Location: index.php");
	}elseif(isset($_POST['postbtn'])){
		$id = $_POST['imgid'];
		$text = $_POST['textval'];
		$insert_post="INSERT INTO post2 (owner,value,imageid,time) VALUES(?,?,?,?)";
		$sth = $db->prepare($insert_post);
		$sth->execute(Array($username,$text,$id, date('Y-m-d H:i:s')));
		unset($_SESSION['uploadsuccess']);
		header("Location: index.php#homepage");
	}elseif(isset($_POST['uploadbtn'])){
		$imagename=$_FILES["files"]["name"]; 
		//Get the content of the image and then add slashes to it 
		$filesize = $_FILES["files"]["size"];
		if(!empty($_FILES['files']['tmp_name'])){
			$imagetmp=file_get_contents($_FILES['files']['tmp_name']);
		}
		$allow = array('jpg','png');
		$imagename = strtolower($imagename);
		$ext = pathinfo($imagename, PATHINFO_EXTENSION);
		if(in_array($ext,$allow)&& $filesize > 0 && $filesize < 85*1024 ){
			//Insert the image name and image content in image_table
			$insert_image="INSERT INTO image3 (image,imagetitle,owner) VALUES(?,?,?)";
			$sth = $db->prepare($insert_image);
			$sth->execute(array($imagetmp,$imagename,$username));
			$_SESSION['uploadsuccess'] = 1;
			header("Location: index.php#post");

		}else{
			unset($_SESSION['uploadsuccess']);
			echo "<script>alert('check your upload type/size( .jpeg.png / < 85KB )');</script>";
		}
	}
	unset($_POST);
	$_POST = array();
?>
 <!-- Bootstrap -->
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link href="css/index.css" rel="stylesheet">
<head>
	<meta = http-equiv = "Content-type" content="text/html; charset=utf-8">
</head>
<body>
<div class="infobar">
	<h4> Welcome! <strong name="username"><?php echo $_SESSION['name']?></strong></h4>
	<form action="index.php" method="post">
		<button type="submit" name="logoutbtn"><strong>Logout</strong></button>
	</form>
</div>

<div class ="main row">
		<ul class="nav nav-tabs" role="tablist">
		<li role="presentation" id="homepagetab" <?php if(!isset($_SESSION['uploadsuccess'])) echo"class=\"active\"";?>><a href="#homepage" aria-controls="homepage" role="tab" data-toggle="tab">Homepage</a></li>
		<li role="presentation" id="posttab" <?php if(isset($_SESSION['uploadsuccess'])) echo"class=\"active\"";?>><a href="#post" aria-controls="post" role="tab" data-toggle="tab">Post</a></li>    
		</ul>
		<div class = "tab-content">
			<div role="tabpanel" class="tab-pane fade <?php if(!isset($_SESSION['uploadsuccess'])) echo" in active"?>" id="homepage">
				<?php 
					$select_post = "SELECT * FROM post2 ORDER BY time DESC;";
					$sth = $db -> prepare($select_post);
					$sth -> execute();
								
					foreach($sth -> fetchAll() as $postrow){
						$sql = "SELECT * FROM image3 where id = ?";
						$imageget = $db -> prepare($sql);
						$imageget -> execute(Array($postrow['imageid']));
						$imagedata = $imageget -> fetch(PDO::FETCH_ASSOC);
						$image = $imagedata['image'];
					
				?>
					<div class="postdiv rounded" id="<?php echo $postrow['id'];?>">
						<div class="context">
							<div class="author-info">
								<h2><?php echo $postrow['owner'];?></h2>
								<h6 class="text-right"><small><?php echo $postrow['time'];?></small></h6>
							</div>
							<div class="text">
								<h4><?php echo htmlspecialchars($postrow['value']);?></h4>
								<?php echo '<img class="preview img-rounded" src="data:image/png;charset=utf-8;;base64,'.base64_encode( $image ).'"/>'; ?>
								<form action="index.php" method="post" class="form-inline commentform ">
									<input type="hidden" name="postid" value="<?php echo $postrow['id']; ?>">
									<?php
										$likesql = "SELECT * FROM postlike2 where postid = ? AND user = ?";
										$usernum = $db -> prepare($likesql);
										$usernum -> execute(Array($postrow['id'],$username));
										$likeornot = $usernum -> fetch(PDO::FETCH_ASSOC);
									?>
									<button type="submit" name="likeButton" class="likeButton <?php if($likeornot['postlike']==1){echo "like";}?> ">♥</button>
								</form>
								<h6>
								<?php
									$sum_like = "SELECT postid,SUM(postlike) as total FROM postlike2 where postid = ? GROUP BY postid";
									$likenum = $db -> prepare($sum_like);
									$likenum -> execute(Array($postrow['id']));
									$num = $likenum -> fetch(PDO::FETCH_ASSOC);
									echo 0+$num['total'];
								?>
								 likes</h6>
							</div>

							<table class = "table table-striped">
								<?php
									$sql = "SELECT * FROM comment2 where postid = ? ORDER BY time;";
									$commentget = $db -> prepare($sql);
									$commentget -> execute(Array($postrow['id']));
									foreach($commentget -> fetchAll() as $commentrow){	
								?>
									<tr>
										<td width="10%"><strong><?php echo $commentrow['owner']; ?></strong></td>
										<td><?php echo htmlspecialchars($commentrow['value']); ?></td>
										<td width="20%"><h6><small><?php echo $commentrow['time'];?></small></h6></td>
									</tr>									
								<?php
									}	
								?>
							</table>
							<form action="index.php" method="post" class=" commentform ">
								<input type="hidden" name="postid" value="<?php echo $postrow['id']; ?>">
								<h5><strong><?php echo $postrow['owner']." "?></strong></h5>
								<input type="text" maxlength="150" class="form-control comment" name="commentval" placeholder="comment">
								<button type="submit" name="commentbtn" class="btn btn-warning" value="comment">comment</button>
							</form>
						</div>
					</div>
				<?php
					}
				?>
					
								
			</div>
			<div role="tabpanel" class="area tab-pane fade <?php if(isset($_SESSION['uploadsuccess'])) echo"in active"?>" id="post">
					<?php if(!isset($_SESSION['uploadsuccess'])){echo"
					<form action=\"index.php\" method=\"post\" class=\"uploadform\" enctype=\"multipart/form-data\">
						<div class=\"imgblock\">
							<input type=\"file\" id=\"files\" name=\"files\"/>
							<button type=\"submit\" name=\"uploadbtn\" class=\"btn btn-info btn-lg\">upload</button>
						</div>
					</form>
					";}?>
						<?php if(isset($_SESSION['uploadsuccess'])){
								$last = "SELECT * FROM image3 WHERE owner = ? ORDER BY id DESC LIMIT 1;";
								$sth = $db->prepare($last);
								$sth->execute(Array($username));
								$row = $sth->fetch(PDO::FETCH_ASSOC);
								$sql = "SELECT * FROM image3 where id = ?";
								$sth = $db -> prepare($sql);
								$sth -> execute(Array($row['id']));
								$row = $sth -> fetch(PDO::FETCH_ASSOC);
								$image = $row['image'];
								echo '<img class="preview img-rounded" src="data:image/png;charset=utf-8;;base64,'.base64_encode( $row['image'] ).'"/>';
								}
						?> 
					<form action="index.php" method="post" class="postform" style="<?php if(!isset($_SESSION['uploadsuccess'] )){echo"	visibility: hidden;";}?>">
						<div id= "inputarea">
							<input type="hidden" name="imgid" value="<?php if(isset($_SESSION['uploadsuccess'])){ echo $row['id']; }?>">
							<textarea class="form-control" maxlength="400" name="textval" rows="5" placeholder = "輸入你想說的話"></textarea>
							<div class="area">
								<button type="submit" name="postbtn" class="btn btn-primary btn-lg">post</button>
								<button type="submit" name="cancelbtn" class="btn btn-default btn-lg">cancel</button>
							</div>
						</div>
					</form>
			</div>
		</div>
	</div>

	<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
	<!-- Include all compiled plugins (below), or include individual files as needed -->
	<script src="js/app.js"></script>

</body>
</html>
