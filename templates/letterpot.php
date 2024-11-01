<?php
// ショートコードに引数が設定されている場合
if($atts){
	$atts = shortcode_atts(array(
		'id' => null,
	), $atts);
	extract($atts);
	$user_id = $id;
	$user_data = WP_LetterPot::get_user_data($id);
	if(!$user_data){
		return false;
	}
}
// 通常の表示
else{
	$options = get_option('WPLetterPot');
	// 設定がされていない場合
	if(!$options){
		return false;
	}
	$user_id = $options['user_id'];
	$user_data = $options['user_data'];
}

$myString = $user_data['title'];
$user_title = str_replace( ' | LetterPot (α)', 'はこちら', $myString );
$mypage_url = 'https://letterpot.otogimachi.jp/users/' . $user_id;
$amounts = $user_data['amounts'];
$logo = plugins_url('wp-letterpot') . '/assets/images/letterpot.png';
$content = <<< EOM
<div class="wplp-card">
	<div class="wplp-card__text">
		<div class="wplp-card__pic">
			<a href="{$mypage_url}" target="_blank"><img src="{$user_data['thumbnail_path']}" ></a>
		</div>
		<div class="wplp-card__info">
			<div class="wplp-card__infoTitle"><a href="{$mypage_url}" target="_blank">{$user_title}</a></div>
			<ul class="wplp-card__infoList">
				<li>{$amounts[0]}</li>
				<li>{$amounts[1]}</li>
				<li>{$amounts[2]}</li>
				<li>{$amounts[3]}</li>
			</ul>
		</div>
	</div>
	<div class="wplp-card__logo">
		<a href="https://letterpot.otogimachi.jp/" target="_blank"><img src="{$logo}"></a>
	</div>
</div>
EOM;
return $content;
?>
