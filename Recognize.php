<?php
error_reporting(E_ALL ^ E_NOTICE);
require_once("NN/NeuroNet.php");

class Recognize
{
        private $length_recognize_nn;
        private $letters_recognize_nn;
        private $length;
        private $alphabet=array("а"=>0,"б"=>1,"в"=>2,"г"=>3,"д"=>4,"е"=>5,"ж"=>6,"з"=>7,"и"=>8,"й"=>9,"к"=>10,"л"=>11,"м"=>12,"н"=>13,"о"=>14,"п"=>15,
        "р"=>16,"с"=>17,"т"=>18,"у"=>19,"ф"=>20,"х"=>21,"ц"=>22,"ч"=>23,"ш"=>24,"щ"=>25,"ы"=>26,"ь"=>27,"э"=>28,"ю"=>29,"я"=>30);

        function __construct($filename, $length=0,$nocrop=0)
        {
                $this->length_recognize_nn=new NeuroNet(2000,array(0,36,4),1);
                $this->letters_recognize_nn=new NeuroNet(600,array(0,900,31),2);
        
                $this->img = imagecreatefromgif($filename) OR DIE("Image does not exist");
                $this->bg_color = imagecolorallocate($this->img, 255, 255, 255);
                $size = getimagesize($filename);
                $this->width = $size[0];
                $this->height = $size[1];
                
                if($nocrop==0){
                        $this->rcrop();
                        $this->img_invert();
                }
                $this->length=$length==0?($this->get_length()+4):$length;

        }
        
        public function get_length()
        {
                $res=$this->length_recognize_nn->ask_network($this->get_vector($this->img_resize($this->img,100,20)),10,0);
                $res= array_keys($res, max($res));
                return $res[0];
        }
        
        function img_invert(){
        for($x = 0; $x < $this->width; $x++)
                for($y = 0; $y < $this->height; $y++){
                        $rgb = imagecolorat($this->img, $x, $y);
                        $r = 0xFF-(($rgb>>16)&0xFF);
                        $g = 0xFF-(($rgb>>8)&0xFF);
                        $b = 0xFF-($rgb&0xFF);
                        $color = imagecolorallocate($this->img, $r, $g, $b);
                        imagesetpixel($this->img, $x, $y, $color);
                        }
        }
        
        function img_resize($inimage,$neww,$newh) {
                $outimage=imagecreatetruecolor($neww,$newh);
                imagecopyresampled($outimage,$inimage,0,0,0,0,$neww,$newh,imagesx($inimage),imagesy($inimage));
                return $outimage;
        }
        function get_vector($img){
                $vector = array();
                $width=imagesx($img);
                $height=imagesy($img);
                for ($i = 0; $i < $width; $i++)
                        for ($j = 0; $j < $height; $j++)
                                $vector[$i*$height+$j] = ((imagecolorat($img, $i, $j) >> 16) & 0xFF);
                return $vector;
        }
        function colordist($color1, $color2)
        {
                return sqrt(pow((($color1 >> 16) & 0xFF) - (($color2 >> 16) & 0xFF), 2) + 
                                        pow((($color1 >> 8) & 0xFF) - (($color2 >> 8) & 0xFF), 2) + 
                                        pow(($color1 & 0xFF) - ($color2 & 0xFF), 2));
        }        
        
        function get_brightness($color)
        {
                return ((($color >> 16) & 0xFF) + (($color >> 8) & 0xFF) + ($color & 0xFF)) / 765;
        }        

        
        function rcrop()
        {
                imagefilledrectangle($this->img, $this->width - 36, 0, $this->width , 15, $this->bg_color);
                $tb = 0;
                $right_border = $this->width - 1;
                while ($tb < $this->height - 1)
                {
                        for ($i = 0; $i < $this->width; $i++)
                                if ($this->colordist(imagecolorat($this->img, $i, $tb), $this->bg_color) > 30)
                                        break 2;
                        $tb++;
                }
                while ($right_border > 0)
                {
                        for ($j = 0; $j < $this->height; $j++)
                                if ($this->colordist(imagecolorat($this->img, $right_border, $j), $this->bg_color) > 30)
                                        break 2;
                        $right_border--;
                }

                $this->width = $right_border;
                $this->height -= $tb;
                $c_img = imagecreatetruecolor($this->width, $this->height);
                imagecopy($c_img, $this->img, 0, 0, 1, $tb, $this->width, $this->height-5);
                $this->img = $c_img;
                $this->bg_color = imagecolorat($this->img, 0, 0);
        }

        function get_digits()
        {
                $this->d = array();
                $digit_width = $this->dq>0?ceil(($this->width) / $this->dq):0;
                for ($i = 0; $i < $this->dq; $i++)
                {
                        $offset = floor($i * ($this->width / $this->dq));
                        $bb = $this->height - 1;
                        $tb=0;
                        $this->d[$i]['image'] = imagecreatetruecolor($digit_width, ($bb - $tb + 1));
                        $white = imagecolorallocate($this->d[$i]['image'], 0, 0, 0);
                        $this->d[$i]['width'] = $digit_width;
                        $this->d[$i]['height'] = $bb - $tb + 1;
                        imagecopy($this->d[$i]['image'], $this->img, 0, 0, $offset, $tb, $this->d[$i]['width'], $this->d[$i]['height']);
                        imagefill($this->d[$i]['image'], 0, 0, $white);
                        imagefill($this->d[$i]['image'], $this->d[$i]['width']-1, $this->d[$i]['height']-1, $white);
                }
        }

        public function recognize(){
                $alphabet=(array_flip($this->alphabet));
                $this->dq=$this->length;
                $this->get_digits();
                $result=array();
                for($i=0;$i<$this->length;$i++){
                       $res=$this->letters_recognize_nn->ask_network($this->get_vector($this->img_resize($this->d[$i]['image'],20,30)),-1,1);
                       $res[9]=0;
                       $res[25]=0;
                       $res1=array_keys($res, max($res));
                       $res[$res1[0]]=0;
                       $res2=array_keys($res, max($res));
                       $res[$res2[0]]=0;
                       $res3=array_keys($res, max($res));
                       $result[$i]=array($alphabet[$res1[0]],$alphabet[$res2[0]],$alphabet[$res3[0]]);
                }
                return $result;
        }
}
