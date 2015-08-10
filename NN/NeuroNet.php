<?php
require_once("Neuron.php");
class NeuroNet
{
    private $size;
    private $neurons_count;
    private $layers_count;
    private $number;
    private $neurons;

    function NeuroNet($i_size, $i_neurons_count, $i_number)
    {
        $this->number = $i_number;
        $this->neurons_count = $i_neurons_count;
        $this->size = $this->neurons_count[0] = $i_size;
        $this->layers_count = count($this->neurons_count) - 1;
        $this->create_neurons();
    }

    function create_neurons()
    {
        for ($layer = 1; $layer <= $this->layers_count; $layer++)
            for ($i = 0; $i < $this->neurons_count[$layer]; $i++)
                $this->neurons[$layer][$i] = new Neuron($this->neurons_count[$layer - 1],$layer,$this->number,$i);
    }
    
    public function ask_network($vector,$a,$b)
    {
        $vector=$this->normalize($vector,$a,$b);
        for ($layer = 1; $layer <= $this->layers_count; $layer++){
            $out=array();
            for ($i = 0; $i < $this->neurons_count[$layer];$i++ )
                $out[$i] = $this->neurons[$layer][$i]->ask($vector);
            $vector=$out;
        } ;
        return $out;
    }

    function normalize($vector,$a,$b){
        $min=min($vector);
        $max=max($vector);
        for ($i = 0; $i < count($vector); $i++)
            $vector[$i] = ($max - $min)==0?0:((($vector[$i] - $min) * ($b - $a)) / ($max - $min) + $a);
        return $vector;
    }
}
