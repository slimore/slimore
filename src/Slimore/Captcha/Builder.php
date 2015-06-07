<?php

/**
 * Slimore - The fully (H)MVC framework based on the Slim PHP framework.
 *
 * @author      Pandao <slimore@ipandao.com>
 * @copyright   2015 Pandao
 * @link        http://github.com/pandao/slimore
 * @license     MIT License https://github.com/pandao/slimore#license
 * @version     0.1.0
 * @package     Slimore\Captcha
 */

namespace Slimore\Captcha;

/**
 * Class Builder
 *
 * Support English, Chinese and mixed characters
 *
 * @author Pandao
 * @package Slimore\Captcha
 */

class Builder
{
    /**
     * @var array
     */
    private $imageTypes = ['png', 'jpeg', 'gif'];

    /**
     * @var string
     */
    public  $imageType = 'png';

    /**
     * @var int
     */
    public  $width = 120;

    /**
     * @var int
     */
    public  $height = 40;

    /**
     * @var int
     */
    public  $lines = 6;

    /**
     * @var array
     */
    public  $backgroundColor = [255, 255, 255];

    /**
     * @var int
     */
    public  $borderWidth = 1;

    /**
     * @var int
     */
    public  $length = 5;

    /**
     * @var string
     */
    public $numbers = '0123456789';

    /**
     * @var array
     */
    public  $letters = [
        '123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ',
        'ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789',
        '123456789abcdefghijkmnopqrstuvwxyz'
    ];

    /**
     * @var string
     */
    public $pureLetters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz';

    /**
     * 0 Numbers
     * 1 Pure English letters
     * 2 English letters & Numbers mixed
     * 3 chinese characters
     * 4 All mixed
     *
     * @var int
     */
    public $randomType = 1;

    /**
     * @var string
     */
    public $chineseCharacters = '民谣歌手周云蓬今年四月发行新专辑四月旧州其中专辑主打曲目镜中提前首发这首改编自张枣著名诗歌的单曲一经发布就引发了热烈的反响得到了圈内圈外的一致好评更引发了新一轮文艺界的诗歌复兴镜中周云蓬词周云蓬改编自张枣同名诗歌曲周云蓬我在镜中等你归来坐在镜中望窗外想起一生后悔的事情梅花就会落下来我在镜中等你归来坐在镜中看云天想起一生后悔的事情梅花就会落满南山镜子照镜子很多的镜子所有镜中都要有你一个小影子一个老影子抱着睡进黑暗里九月周云蓬目击众神死亡的草原上野花一片远在远方的风比远方更远我的琴声呜咽我的泪水全无我把远方的远归还草原一个叫木头一个叫马尾一个叫木头一个叫马尾目击众神死亡的草原上野花一片远在远方的风比远方更远我的琴声呜咽我的泪水全无我把远方的远归还草原一个叫木头一个叫马尾一个叫木头一个叫马尾一个叫木头一个叫马尾一个叫木头一个叫马尾亡我祁连山使我牛羊不蕃息失我胭脂山令我妇女无颜色远方只有在死亡中凝聚野花一片明月如镜高悬在草原映照千年的岁月我的琴声呜咽我的泪水全无只身打马过草原一个叫木头一个叫马尾一个叫木头一个叫马尾一个叫木头一个叫马尾一个叫木头一个叫马尾一个叫木头一个叫马尾一个叫木头一个叫马尾一个叫木头一个叫马尾今生今世遥不可及李健多想拥你在我的怀里却无法超越那距离美好回忆渐渐地远去盼望今生出现奇迹无尽的想念荒了容颜无助的爱恋从未改变喔喔喔啦愿有情人终成眷属李健八月照相馆风吹过照相馆的橱窗窗外溜走的时光当我路过这个地方仿佛就像回到昨天一样你幸福地靠我的肩说就这样过生命里的每一天嗯那一个夏天在心底深藏偶尔荡漾嗯渐渐泛黄的相片带我回到那某一年的某一天光闪过你笑着的脸庞永远装进了相框这首歌让我回望那年八月我们傻傻的模样当捧着那些旧照片燃起戒掉的烟悄悄的湿了眼嗯那么一瞬间留下来的笑已成永远当爱消失在时间里面相片永远把你留在我的身边啊似水流年偶尔在镜子里面旧时光和我相遇那片远远的天空炉火映红的暖冬大雁飞过秋天的海面看着奔跑的童年赤着脚的快乐只不过是仓惶的一转眼她手中的口琴唱的歌唤醒贪玩的耳朵爱是手中捧的红苹果那年夏天她微笑着不说让我这一夜长醉流年似水的滋味笑中青涩的眼泪那时光渐渐沉睡记忆中曾飘动的烛光今夜又照亮远方这不经意之间的回望让目光走过那扇窗生命的河从身旁流过将回忆慢慢淹没那年春天燃起的篝火多年以后泛着泪光闪烁我愿这一夜长醉流年似水的滋味旅行词曲许巍阵阵晚风吹动着松涛吹响这风铃声如天籁站在这城市的寂静处让一切喧嚣走远只有青山藏在白云间蝴蝶穿行自由在清涧看那晚霞盛开在天边有一群向西归鸟谁画出这天地又画下我和你让我们的世界绚丽多彩谁让我们哭泣又给我们惊喜让我们就这样相爱相遇总是要说再见相聚又分离总是走在漫长的路上啦谁画出这天地又画下我和你让我们的世界绚丽多彩谁让我们哭泣又给我们惊喜让我们就这样相爱相遇总是要说再见相聚又分离总是走在漫长的路上总是走在漫长的路上陀螺李健词曲万晓利在田野上转在清风里转在飘着香的鲜花上转在沉默里转在孤独里转在结着冰的湖面上转在欢笑里转在泪水里转在燃烧着的生命里转在洁白里转在血红里转在你已衰老的容颜里转如果我可以停下来我想把眼睛睁开看着你怎么离开可是我不能停下来也无法为你喝彩请你把双手松开在酒杯里转在噩梦里转在深不见底的黑暗里转在欲望里转在挣扎里转在任由天命的麻木里转在阳光灿烂的一天你用手捂着你的脸对我说你很疲倦你扔下手中的道具开始咒骂这场游戏说你一直想放弃但不能停止转转转转转高高地举起你的鞭转转转转转转转转轻轻地闭上我的眼啦啦啦春水初生春林初盛春风十里不如你生命如河流李健生命像条河我顺流而下湍急或平缓随着它我一路奔忙穿越了冬夏难过或欢乐却从容不下虚假忽明忽暗的风景起起落落的心情这一切像浪花蜕成涟漪无论现实多坚硬柔软如初的心灵忘不了为爱感伤的夜里但终究未曾对真爱怀疑我也会想起那动听的话如今不计较是真假我几经辗转才遇见了他他让我看到生命绚烂的云霞生命像条河我顺流而下湍急或平缓随着它学问越渊博的人越谦卑才疏学浅的人往往声色俱厉歌手李健意林假如爱有天意李健坦言有时候爱情和理想一样都很美好但都难以实现尘缘人间有我残梦未醒漫漫长路起伏不能由我任多少深情独向寂寞人随风过自在花开花又落我始终爱你李健因为你我尝过甜蜜忘记你却谈何容易这么多年沧海桑田我情深却无怨再见你你依然美丽望着你我默默无语又想问候又怕心痛让岁月为我说你知不知道我始终爱你我只是没有告诉你让相思烈火熊熊烧着我也不愿让你受委屈你知不知道我始终爱你却只能远远看着你春去秋来谁会明了我认真的感情在梦里我也曾哭泣在风里我不断寻觅情愿寂寞不愿割舍这一生为你过你知不知道我始终爱你我只是没有告诉你让相思烈火熊熊烧着我也不愿让你受委屈你知不知道我始终爱你却只能远远看着你春去秋来盼你明了我认真的感情你知不知道我始终爱你我只是没有告诉你春去秋来你会明了我认真的感情春去秋来你会明了我始终爱你为你而来李健词曲李健编曲刘卓不停地追赶理想忽隐又忽现为生活来不及疲倦阳光下世界多鲜艳怎么能视而不见我是为你而来不在乎穿越绵绵山脉你给我的最爱永远在盛开是我一生的精彩啦不停地驱赶来去无常的孤单也茫然还好有勇敢看星空梦幻般流转怎么能袖手旁观我是为你而来不在乎穿越绵绵山脉你给我的最爱永远在盛开是我一生的精彩中学时代水木年华词卢庚戌曲李健穿过运动场让雨淋湿我羞涩的你何时变孤寂躲在墙角里偷偷的哭泣我忧郁的你有谁会懂你爱是什么我不知道我不懂永远我不懂自己爱是什么我还不知道谁能懂永远谁能懂自己穿过运动场让雨淋湿我羞涩的你何时变孤寂躲在墙角里偷偷的哭泣我忧郁的你不许谁懂你爱是什么我不知道我不懂永远我不懂自己爱是什么我还不知道谁能懂永远谁能懂自己把百合日记藏在书包我纯真的你我生命中的唯一绽放李健词左右曲李健紫色的火穿越夜的云朵流星一样飞过雨的线索繁花碎落打开平静湖泊鱼鹰一样急迫远去的我另一边世界的光亮在这片水面下摇晃每一颗水珠已绽放在生命最美的地方在生命最美的地方每一颗水珠已绽放在这片水面下摇晃另一边世界的光亮远去的我鱼鹰一样急迫打开平静湖泊繁花碎落雨的线索流星一样飞过穿越夜的云朵紫色的火花碎落平静湖泊夜的云朵远去的我向往李健词曲李健我知道并不是所有鸟儿都飞翔当夏天过去后还有鲜花未曾开放我害怕看到你独自一人绝望更害怕看不到你不能和你一起迷惘多想你在我身旁看命运变幻无常体会这默默忍耐的力量当春风掠过山岗依然能感觉寒冷却无法阻挡对温暖的向往啊我知道并不是耕耘就有收获当泪水流干后生命还是那么脆弱多残忍你和我就像流星划落多绚烂飞驰而过点亮黑夜最美烟火多想你在我身旁看命运变幻无常体会这默默忍耐的力量当春风掠过山岗依然能感觉寒冷却无法阻挡对温暖的向往向往向往多想你在我身旁看命运变幻无常体会这默默忍耐的力量当春风掠过山岗依然能感觉寒冷却无法阻挡对温暖的向往多想你在我身旁看命运变幻无常体会这默默忍耐的力量当春风掠过山岗依然能感觉寒冷又怎能停止对温暖的向往向往';

    /**
     * @var bool
     */
    public  $drawBorder = false;

    /**
     * @var bool
     */
    public  $drawLine = true;

    /**
     * @var bool
     */
    public  $drawPixel = true;

    /**
     * @var int
     */
    public  $fontSize = 16;

    /**
     * @var string
     */
    public  $fontFile = null;

    /**
     * @var resource
     */
    private $image;

    /**
     * @var string
     */
    private $code;

    /**
     * Constructor
     */
    public function __construct()
    {
    }

    /**
     * Get image type
     *
     * @return string
     */

    public function getImageType()
    {
        $this->imageType = ($this->imageType === 'jpg') ? 'jpeg' : $this->imageType;
        $this->imageType = ( in_array($this->imageType, $this->imageTypes) ) ? $this->imageType : 'png';

        return $this->imageType;
    }

    /**
     * Get code
     *
     * @param bool $uppercase true
     * @return string
     */

    public function getCode($uppercase = true)
    {
        return ($uppercase) ? strtoupper($this->code) : $this->code;
    }

    /**
     * Captcha image creator
     *
     * @param int $width null
     * @param int $height null
     * @return $this
     */

    public function create($width = null, $height = null)
    {
        $this->width  = ($width)  ? $width : $this->width;
        $this->height = ($height) ? $height : $this->height;

        $imageType    = $this->getImageType();
        $createFunc   = ($imageType === 'gif') ? 'imagecreate' : 'imagecreatetruecolor';
        $this->image  = $createFunc($this->width, $this->height);

        $bgColor = $this->backgroundColor;

        $backgroundColor = imagecolorallocate($this->image, $bgColor[0], $bgColor[1], $bgColor[2]);

        imagefilledrectangle($this->image, 0, 0, $this->width, $this->height, $backgroundColor);

        $this->drawText();

        if ($this->drawBorder)
        {
            $this->drawBorder();
        }

        if ($this->drawLine)
        {
            $this->drawLine();
        }

        if ($this->drawPixel)
        {
            $this->drawPixel();
        }

        return $this;
    }

    /**
     * Random generation numbers
     *
     * @return void
     */

    protected function randomNumbers()
    {
        $this->code = substr(str_shuffle($this->numbers), 0, $this->length);
    }

    /**
     * Random generation pure English letters
     *
     * @return void
     */

    protected function randomPureLetters()
    {
        $this->code = substr(str_shuffle($this->pureLetters), 0, $this->length);
    }

    /**
     * Random generation English & Numbers letters
     *
     * @return void
     */

    protected function randomLetters()
    {
        for ($i = 0; $i < $this->length; $i++)
        {
            $letters     = $this->letters[mt_rand(0, count($this->letters) - 1)];
            $this->code .= $letters[mt_rand(0, strlen($letters) - 1)];
        }
    }

    /**
     * Random generation chinese characters
     *
     * @return void
     */

    protected function randomChinese()
    {
        if ( function_exists('mb_substr') )
        {
            $len = mb_strlen($this->chineseCharacters, 'utf-8');

            for ($i = 0; $i < $this->length; $i++)
            {
                $this->code .= mb_substr($this->chineseCharacters, mt_rand(0, $len - 1), 1, 'utf-8');
            }
        }
    }

    /**
     * Random generation mixed characters
     *
     * @return void
     */

    protected function randomMixed()
    {
        $characters = implode('', $this->letters) . $this->chineseCharacters;

        if (function_exists('mb_substr'))
        {
            $len = mb_strlen($characters, 'utf-8');

            for ($i = 0; $i < $this->length; $i++)
            {
                $this->code .= mb_substr($characters, mt_rand(0, $len - 1), 1, 'utf-8');
            }
        }
    }

    /**
     * Draw text characters to image
     *
     * @return void
     */

    protected function drawText()
    {
        /**
         * 0 Numbers
         * 1 Pure English letters
         * 2 English letters & Numbers mixed
         * 3 chinese characters
         * 4 All mixed
         */

        switch($this->randomType)
        {
            case 0:
                $this->randomNumbers();
                break;

            case 1:
                $this->randomPureLetters();
                break;

            case 3:
                $this->randomChinese();
                break;

            case 4:
                $this->randomMixed();
                break;

            case 2:
            default:
                $this->randomLetters();
                break;
        }

        if (!$this->fontFile)
        {
            $this->fontFile = __DIR__ . '../../Fonts/verdana.ttf';
        }

        if (!file_exists($this->fontFile))
        {
            throw new \InvalidArgumentException('Font file ' . $this->fontFile . ' not found.');
        }

        $textColor = imagecolorallocate($this->image, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));

        $x = ($this->width - ($this->fontSize * $this->length)) / 2;
        $y = $this->fontSize + ($this->height - $this->fontSize) / 2;
        $fontFile = (is_array($this->fontFile)) ? $this->fontFile[array_rand($this->fontFile)] : $this->fontFile;

        imagettftext($this->image, $this->fontSize , mt_rand(-8, 8), $x, $y, $textColor, $fontFile, $this->code);
    }

    /**
     * Draw border
     *
     * @return void
     */

    protected function drawBorder()
    {
        $borderColor = imagecolorallocate($this->image, 0, 0, mt_rand(50, 255));

        imagerectangle($this->image, 0, 0, $this->width - $this->borderWidth, $this->height - $this->borderWidth, $borderColor);
    }

    /**
     * Draw lines
     *
     * @return void
     */

    protected function drawLine()
    {
        $lineColor = imagecolorallocate($this->image, mt_rand(50, 255), mt_rand(100, 255), mt_rand(0, 255));

        for($i = 0; $i < $this->lines; $i++)
        {
            imageline($this->image, 0, mt_rand() % $this->height, $this->width, mt_rand() % $this->height, $lineColor);
        }
    }

    /**
     * Draw pixel points
     *
     * @return void
     */

    protected function drawPixel()
    {
        $pixelColor = imagecolorallocate($this->image, mt_rand(0, 255), mt_rand(100, 255), mt_rand(50, 255));

        for($i = 0; $i< mt_rand(1000, 1800); $i++)
        {
            imagesetpixel($this->image, mt_rand() % $this->width, mt_rand() % $this->height, $pixelColor);
        }
    }

    /**
     * Display captcha image in page
     *
     * @return $this
     */

    public function display()
    {
        $imageType = $this->getImageType();

        header('Content-type: image/' . $imageType);

        $imageFunc = 'image' . $imageType;
        $imageFunc($this->image);

        $this->destroy($this->image);

        return $this;
    }

    /**
     * Save captcha image file
     *
     * @param string $filename
     * @return $this
     */

    public function save($filename)
    {
        $type = pathinfo($filename, PATHINFO_EXTENSION);
        $type = ($type === 'jpg') ? 'jpeg' : $type;
        $type = (in_array($type, $this->imageTypes)) ? $type : 'png';

        header('Content-type: image/' . $type);

        $imageFunc = 'image' . $type;
        $imageFunc($this->image, $filename);

        $this->destroy($this->image);

        return $this;
    }

    /**
     * Destroy image resource
     *
     * @param $image
     */
    protected function destroy($image)
    {
        if (is_resource($image))
        {
            imagedestroy($image);
        }
    }
}