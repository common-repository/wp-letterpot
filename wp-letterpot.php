<?php

/*
Plugin Name: WP LetterPot
Plugin URI: https://github.com/kanakogi/WP-LetterPot
Description: LetterPotのプラグインです。
Author: Nakashima Masahiro, Hnin Ei Eaindray
Version: 1.0.1
Author URI: http://innovasia-mj.com
*/
class WP_LetterPot
{
    public function __construct()
    {
        add_action('admin_menu', array($this, 'add_menu'));
        add_action('admin_menu', array($this, 'add_submenu'));
        add_shortcode('LetterPot', array($this, 'wp_letterpot_shortcode'));
        add_action('admin_enqueue_scripts', array($this, 'add_my_stylesheet'));
        add_filter('the_content', array($this, 'my_content'), 1);

        // Cron
        add_action('cron_upadte_user_data', array($this, 'upadte_user_data'));
        register_activation_hook(__FILE__, array($this, 'activation_hook'));
        register_deactivation_hook(__FILE__, array($this, 'deactivation_hook'));
        register_uninstall_hook(__FILE__, array($this, 'deactivation_hook'));
    }

    public function add_menu()
    {
        add_menu_page(
          'WP LetterPot', //ページのタイトル
          'WP LetterPot', //管理画面のメニュー
          'manage_options', //ユーザーレベル
          'wpletterpot', //スラッグ
          array($this, 'admin_setting') //機能を提供する関数
        );
    }

    public function add_submenu()
    {
        add_submenu_page(
          'wpletterpot', //親メニューのslug
          '使い方',
          '使い方',
          'manage_options', //ユーザーレベル
          'wpletterpot-howto', //スラッグ
          array($this, 'admin_howto') //機能を提供する関数
        );
        add_submenu_page(
          'wpletterpot', //親メニューのslug
          '制作者',
          '制作者',
          'manage_options', //ユーザーレベル
          'wpletterpot-developer', //スラッグ
          array($this, 'admin_developer') //機能を提供する関数
        );
    }

    public function admin_setting()
    {
        include plugin_dir_path(__FILE__).'/templates/admin_setting.php';
    }

    public function admin_howto()
    {
        include plugin_dir_path(__FILE__).'/templates/admin_howto.php';
    }

    public function admin_developer()
    {
        include plugin_dir_path(__FILE__).'/templates/admin_developer.php';
    }

    public function wp_letterpot_shortcode($atts)
    {
        return include plugin_dir_path(__FILE__).'/templates/letterpot.php';
    }

    public function my_content($content)
    {
        // ショートコード
        if (has_shortcode($content, 'LetterPot')) {
            $plugin_url = plugin_dir_url(__FILE__);
            wp_enqueue_style('custom_wp_admin_css', $plugin_url.'assets/css/styles.min.css');
        }

        // 記事の最後に表示機能
        $options = get_option('WPLetterPot');
        if ($options['afterContent'] == 1) {
            $content .= '[LetterPot]';
            $plugin_url = plugin_dir_url(__FILE__);
            wp_enqueue_style('custom_wp_admin_css', $plugin_url.'assets/css/styles.min.css');
        }

        return $content;
    }

    public function add_my_stylesheet()
    {
        $pages = array('wpletterpot', 'wpletterpot-howto', 'wpletterpot-developer');
        if (isset($_GET['page']) && in_array($_GET['page'], $pages)) {
            $plugin_url = plugin_dir_url(__FILE__);
            wp_enqueue_script('languages/jquery.validationEngine-ja', $plugin_url.'assets/js/validation/languages/jquery.validationEngine-ja.js', array('jquery'));
            wp_enqueue_script('jquery.validationEngine', $plugin_url.'assets/js/validation/jquery.validationEngine.js', array('jquery'));
            wp_enqueue_script('formValidate', $plugin_url.'assets/js/formValidate.js', array('jquery'));
            wp_enqueue_style('custom_wp_admin_css', $plugin_url.'assets/css/styles.min.css');
            wp_enqueue_style('validation_style_css', $plugin_url.'assets/css/validationEngine.jquery.css');
        }
    }

    public function activation_hook()
    {
        if (!wp_next_scheduled('cron_upadte_user_data')) {
            wp_schedule_event(time(), 'twicedaily', 'cron_upadte_user_data');
        }
    }

    public function deactivation_hook()
    {
        wp_clear_scheduled_hook('cron_upadte_user_data');
        delete_option('WPLetterPot');
    }

    /**
     * ユーザー情報をアップデート.
     */
    public function upadte_user_data()
    {
        $options = get_option('WPLetterPot');
        if ($options['user_id']) {
            $this->save_user_data($options['user_id']);
        }
    }

    /**
     * LetterPotからユーザー情報を取得する
     * ex: print_r($this->get_user_data(17601));
     * ex: echo '<img src="'.$user_data['thumbnail_path'].'">';.
     */
    public function get_user_data($user_id)
    {
        $url = 'https://letterpot.otogimachi.jp/users/'.$user_id.'?qr=true';
        $html = wp_remote_get($url);
        if (!is_wp_error($html) && $html['response']['code'] === 200) {
            $dom = new DOMDocument();
            @$dom->loadHTML(mb_convert_encoding($html['body'], 'HTML-ENTITIES', 'UTF-8'));
            $xpath = new DOMXPath($dom);

            // スクレイピング (Scraping)
            $data = array();
            $data['title'] = $xpath->evaluate('string(//title)');
            $data['username'] = self::remove_noise($xpath->evaluate('string(//div[@id="wrap-main"]//div[@class="user-info"]//div[@class="username"])'));
            $data['thumbnail_path'] = $xpath->evaluate('string(//div[@id="wrap-main"]//div[@class="user-info"]//div[@class="thumbnail"]/img/@src)');
            $amounts = array();
            foreach ($xpath->query('//div[@id="wrap-main"]//ul[@class="amount-lists"]/li') as $key => $node) {
                $data['amounts'][] = self::remove_noise($node->nodeValue);
            }

            return $data;
        }

        return false;
    }

    /**
     * 空白と改行を除去.
     */
    private function remove_noise($str)
    {
        $str = str_replace(PHP_EOL, '', $str);

        return  str_replace(' ', '', $str);
    }

    /**
     * URLかどうか.
     */
    public function is_url($text)
    {
        if (preg_match('/^(https?|ftp)(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)$/', $text)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * ユーザーのデータを保存.
     */
    public function save_user_data($user_id)
    {
        $options = array(
            'user_id' => $user_id,
        );
        // LetterPotからデータをとってくる
        if ($options['user_data'] = self::get_user_data($user_id)) {
            update_option('WPLetterPot', $options);

            return true;
        }

        return false;
    }

    public function display_sccess($str)
    {
        echo '<div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible">';
        echo '<p><strong>'.$str.'</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">この通知を非表示にする</span></button></div>';
    }
    public function display_error($str)
    {
        echo '<div id="setting-error-settings_updated" class="error settings-error notice is-dismissible">';
        echo '<p><strong>'.$str.'</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">この通知を非表示にする</span></button></div>';
    }
}
new WP_LetterPot();
