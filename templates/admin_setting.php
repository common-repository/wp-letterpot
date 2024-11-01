<?php
// 保存処理
if (isset($_POST['user_id']) && isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'save_setting')) {
    $user_id = esc_html($_POST['user_id']);


    if (is_numeric($user_id)) {
        // save
        if(WP_LetterPot::save_user_data($user_id)){
          WP_LetterPot::display_sccess('設定を保存しました');
        }else{
          WP_LetterPot::display_error('入力されたデータが正しくありません');
        }
    } elseif (WP_LetterPot::is_url($user_id)) {
        preg_match('/[0-9]*\z/', $user_id, $matches);
        if(WP_LetterPot::save_user_data($matches[0])){
          WP_LetterPot::display_sccess('設定を保存しました');
        }else{
          WP_LetterPot::display_error('入力されたデータが正しくありません');
        }
    } else {
        WP_LetterPot::display_error('UserIDまたはマイページのURLを入力してください');
    }

    if( isset($_POST['afterContent']) && $_POST['afterContent'] == 0 || $_POST['afterContent'] == 1 ){
      $options = get_option('WPLetterPot');
      $options['afterContent'] = esc_html($_POST['afterContent']);
      update_option('WPLetterPot', $options);
    }
}
$options = get_option('WPLetterPot');
?>

<h1>WP LetterPotの設定</h1>

<?php if($options['user_id']): ?>
<div class="card">
	<h2 class="title">LetterPot ID: <?php echo $options['user_id'] ?></h2>
</div>
<?php endif; ?>

<form method="post" action="" id="form_id">
	<h2>LetterPot IDの設定</h2>
	<?php wp_nonce_field('save_setting'); ?>
	<label for="wplp-user_id">あなたのLetterPot ID、またはマイページのURLを入力してください <a href="<?php echo plugins_url('wp-letterpot') . '/assets/images/image1.png' ?>" target="_blank">[詳細]</a></label>
	<div>
    <input type="text" name="user_id" id="wplp-user_id" class="validate[required] regular-text" value="<?php echo $options['user_id'] ?>">
  </div>

  <h3 style="margin: 30px 0 5px;">表示設定</h3>
	<label for="wplp-user_id">記事の最後にLetterPotのウィジェットを表示する</label>
  <div style="color:#999; font-size:12px;">※この機能を使わなくてもショートコードで表示できます</div>
	<div>
    <input type="checkbox" name="afterContent" value="1" <?php echo $options['afterContent'] == 1 ? 'checked' : null; ?>>
  </div>
	<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="保存"></p>
</form>
