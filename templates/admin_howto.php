<h1>WP LetterPotの使い方</h1>

<?php if(!get_option('WPLetterPot')): ?>
	<?php WP_LetterPot::display_error('設定画面からLetterPot IDを設定してください。'); ?>
<?php else: ?>
<p>記事の文章中に下記の[LetterPot]のショートコードを入力してください。</p>

<label class="label-block"><b>ショートコード</b></label>
<input type="text" value="[LetterPot]" class="regular-text" readonly>
<!-- <p style="font-size:12px;">※ [LetterPot id='12345'] のようにユーザーを指定することもできます。</p> -->

<h3 style="margin-top: 50px;">あなたのLetterPot</h3>
<p>ショートコードに表示されるLetterPotの情報は一定時間ごとに更新されます。</p>
<div style="max-width: 700px;">
	<?php echo do_shortcode('[LetterPot]'); ?>
</div>
<?php endif; ?>
