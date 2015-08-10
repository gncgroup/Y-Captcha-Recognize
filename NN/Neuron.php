<?php
class Neuron
{
    private $size;
    private $weight;
    
    function Neuron($i_size,$layer,$number,$pos)
    {
        $this->size = $i_size+1;
        $this->weight = array();
        $this->read_weight($layer,$number,$pos);
    }

    public function read_weight($layer,$number,$pos)
    {
            $this->weight = explode("\n",str_replace(",",".",file_get_contents("NN/weights/" . $layer . "_" . $number . "_" . $pos . ".txt")));
    }

    public function ask($vector)
    {
        $sum = 0.0;
        for ($i = 0; $i < $this->size - 1; $i++)
            $sum += $vector[$i] * $this->weight[$i];
            
        $sum += $this->weight[$this->size - 1];  //BIAS neuron
        return (1 / (1 + exp(-$sum)));
    }
}
