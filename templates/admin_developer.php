<h1>WP LetterPotの制作者</h1>
<p>もし、このプラグインが気に入ればLetterを貰えるとうれしいです。</p>
<?php
$nakashima_id = 17601;
$eaindray_id = 53240;
$nakashima = WP_LetterPot::get_user_data($nakashima_id);
$eaindray = WP_LetterPot::get_user_data($eaindray_id);
$users = array($nakashima_id => $nakashima, $eaindray_id => $eaindray);
foreach($users as $key => $user):
	$myString = $user['title'];
	$user_title = str_replace( ' | LetterPot (α)', 'はこちら', $myString );
	$mypage_url = 'https://letterpot.otogimachi.jp/users/' . $key;
 ?>
 <div style="max-width: 700px;">
 <div class="wplp-card">
 	<div class="wplp-card__text">
 		<div class="wplp-card__pic">
 			<?php
 			echo '<a href="' . $mypage_url . '" target="_blank"><img src="' . $user['thumbnail_path'] . '" ></a> ';
 			?>
 		</div>
 		<div class="wplp-card__info">
 			<div class="wplp-card__infoTitle"><a href="<?php echo $mypage_url ?>" target="_blank"><?php echo $user_title ?></a></div>
 			<ul class="wplp-card__infoList">
 				<?php
 				$amounts = $user['amounts'];
 				foreach ($amounts as $key => $amount) {
 					echo '<li>' .$amount. '</li>';
 				}
 				?>
 			</ul>
 		</div>
 	</div>
 	<div class="wplp-card__logo">
 		<a href="https://letterpot.otogimachi.jp/" target="_blank"><?php echo '<img src="' . plugins_url('wp-letterpot') . '/assets/images/letterpot.png' . '">' ?></a>
 	</div>
 </div>
<?php endforeach; ?>
</div>
