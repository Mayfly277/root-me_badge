<?php

class User
{
    public $nick = 'nickname';
    public $place = '203487/203487';
    public $rank = 'newbie';
    public $status = 'Visitor';
    public $challenges = '0/393';
    public $profile_picture = 'auton0.png';
    public $points = '0';

    /**
     * User constructor.
     * @param string $nick
     * @param string $place
     * @param string $rank
     * @param string $status
     * @param string $challenges
     * @param string $profile_picture
     * @param string $points
     */
    public function __construct(string $nick, string $place, string $rank, string $status, string $challenges, string $profile_picture, string $points)
    {
        $this->nick = $nick;
        $this->place = $place;
        $this->rank = $rank;
        $this->status = $status;
        $this->challenges = $challenges;
        $this->profile_picture = $profile_picture;
        $this->points = $points;
    }
}

class Theme
{
    public $bg_color;
    public $nick_color;
    public $place_color;
    public $rank_color;
    public $challenge_color;
    public $points_color;
    public $status_color;
    public $rm_logo;
    public $font;

    public const orange = [222, 119, 15];
    public const blue = [43, 166, 203];
    public const black = [0, 0, 0];
    public const white = [255, 255, 255];
    public const red = [222, 43, 15];
    public const green = [0, 204, 0];
    public const grey = [118, 118, 118];

    /**
     * Default theme values
     */
    public function __construct()
    {
        $this->whiteTheme();
        $this->font = 'assets/BebasNeue-Regular.ttf';
    }

    public function setTheme($theme){
        switch ($theme){
            case 'white':
                $this->whiteTheme();
                break;
            case 'black':
                $this->blackTheme();
                break;
            default:
                $this->whiteTheme();
        }
    }
    public function whiteTheme(): void
    {
        $this->bg_color = self::white;
        $this->nick_color = self::black;
        $this->place_color = self::black;
        $this->rank_color = self::black;
        $this->challenge_color = self::black;
        $this->points_color = self::blue;
        $this->status_color = ['Visitor' => self::green,
            'Redactor' => self::orange,
            'Premium' => self::red,
            'Admin' => self::grey];
        $this->rm_logo = 'assets/skull-black.png';
    }

    public function blackTheme(): void
    {
        $this->bg_color = self::black;
        $this->nick_color = self::white;
        $this->place_color = self::white;
        $this->rank_color = self::white;
        $this->challenge_color = self::white;
        $this->points_color = self::blue;
        $this->status_color = ['Visitor' => self::green,
            'Redactor' => self::orange,
            'Premium' => self::red,
            'Admin' => self::grey];
        $this->rm_logo = 'assets/skull-white.png';
    }
}

class Badge
{
    public const WIDTH = 500;
    public const HEIGHT = 140;
    public const PADDING = 10;
    public const LINE_SPACE = 10;

    public function generate(Theme $theme, User $user, $file_result): void
    {
        $image = imagecreatetruecolor(self::WIDTH, self::HEIGHT);

        // allocate color
        $bg_color = imagecolorallocate($image, $theme->bg_color[0], $theme->bg_color[1], $theme->bg_color[2]);
        $img_border_color = imagecolorallocate($image, $theme->status_color[$user->status][0], $theme->status_color[$user->status][1], $theme->status_color[$user->status][2]);
        $nick_color = imagecolorallocate($image, $theme->nick_color[0], $theme->nick_color[1], $theme->nick_color[2]);
        $place_color = imagecolorallocate($image, $theme->place_color[0], $theme->place_color[1], $theme->place_color[2]);
        $rank_color = imagecolorallocate($image, $theme->rank_color[0], $theme->rank_color[1], $theme->rank_color[2]);
        $challenge_color = imagecolorallocate($image, $theme->challenge_color[0], $theme->challenge_color[1], $theme->challenge_color[2]);
        $points_color = imagecolorallocate($image, $theme->points_color[0], $theme->points_color[1], $theme->points_color[2]);
        $status_color = imagecolorallocate($image, $theme->status_color[$user->status][0], $theme->status_color[$user->status][1], $theme->status_color[$user->status][2]);

        // background color
        imagefill($image, 0, 0, $bg_color);

        // draw profile picture
        $width = $height = (int)self::HEIGHT - self::PADDING * 2;
        $image = $this->draw_profile_picture($image, $user, $height, $width, self::PADDING, $img_border_color);

        // draw user infos
        $x = self::PADDING * 2 + $width;
        $y = self::PADDING;
        $line_space = self::LINE_SPACE;
        $nick_size = 20;
        $font_default_size = 13;
        imagettftext($image, $nick_size, 0.0, $x, $y += $nick_size, $nick_color, $theme->font, $user->nick);
        imagettftext($image, $font_default_size, 0.0, $x, $y += self::LINE_SPACE + $font_default_size, $status_color, $theme->font, $user->status);
        imagettftext($image, $font_default_size, 0.0, $x, $y += self::LINE_SPACE + $font_default_size, $rank_color, $theme->font, "Rank : " . $user->rank);
        imagettftext($image, $font_default_size, 0.0, $x, $y + self::LINE_SPACE + $font_default_size, $challenge_color, $theme->font, str_repeat("  ",strlen("Score : " . $user->points. ' Points')) . "       Challenges : " . $user->challenges . "");
        imagettftext($image, $font_default_size, 0.0, $x, $y += self::LINE_SPACE + $font_default_size, $points_color, $theme->font, "Score : " . $user->points . ' Points');
        imagettftext($image, $font_default_size, 0.0, $x, $y += self::LINE_SPACE + $font_default_size, $place_color, $theme->font, "Place : " . $user->place);
//        imagestring($image, $font, $x, $y = $y + $line_space, $user->challenges, $challenge_color);

        // draw RM logo
        $rm_logo = imagecreatefrompng($theme->rm_logo);
        [$rm_w, $rm_h] = getimagesize($theme->rm_logo);
        imagecopy($image, $rm_logo, (int)self::WIDTH - self::PADDING - $rm_w, self::PADDING, 0, 0, $rm_w, $rm_h);
        imagepng($image, $file_result);
    }

    private function draw_profile_picture($image, User $user, int $height, int $width, int $padding, int $border_color)
    {
        [$pp_width, $pp_height] = getimagesize($user->profile_picture);
        if ($pp_width !== $pp_height) {
            if ($pp_width > $pp_height) {
                $height *= ($pp_height / $pp_width);
            } else {
                $width *= ($pp_width / $pp_height);
            }
        }
        $path_parts = pathinfo($user->profile_picture);
        if ($path_parts['extension'] === 'png') {
            $profile_picture_img = imagecreatefrompng($user->profile_picture);
        } elseif ($path_parts['extension'] === 'jpg' || $path_parts['extension'] === 'jpeg') {
            $profile_picture_img = imagecreatefromjpeg($user->profile_picture);
        } elseif ($path_parts['extension'] === 'gif') {
            $profile_picture_img = imagecreatefromgif($user->profile_picture);
        } else {
            Throw new \RuntimeException('extension not supported');
        }
        $x = $padding;
        $y = (self::HEIGHT - $height )/ 2;
        imagecopyresized($image, $profile_picture_img, $x, $y, 0, 0, $width, $height, $pp_width, $pp_height);

        // image border
        imagerectangle($image, $x - 1, $y - 1, $x + $width, $y + $height, $border_color);
        return $image;
    }

}

// launch
$theme = new Theme();
$badge = new Badge();

$themes = ['white', 'black'];
foreach ($themes as $theme_value){
    $theme->setTheme($theme_value);
    $user = new User('nickname','203487/203487','newbie','Visitor','0/393','profile_picture/auton0.png','0');
    $badge->generate($theme, $user, 'result/nickname'.$theme_value.'.png');
    $user = new User('eilco','336/203582','lamer','Visitor','204/393','profile_picture/auton29742.jpg','6240');
    $badge->generate($theme, $user, 'result/eilco_'.$theme_value.'.png');
    $user = new User('mayfly','65/203581','programmer','Premium','300/393','profile_picture/auton69804.png','10030');
    $badge->generate($theme, $user, 'result/mayfly_'.$theme_value.'.png');
    $user = new User('das','29/203581','hacker','Admin','331/393','profile_picture/auton58274.jpg','11895');
    $badge->generate($theme, $user, 'result/das_'.$theme_value.'.png');
    $user = new User('Jrmbt','6/203581','elite','Redactor','383/393','profile_picture/auton79281.jpg','16305');
    $badge->generate($theme, $user, 'result/jrmbt_'.$theme_value.'.png');
}
