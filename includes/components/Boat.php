<?php
namespace IYC\components;

class Boat extends Component
{
    protected $file = 'components/boat-card.twig';

    public function init() 
    {
        $this->context['length_feet'] = (int)str_replace('ft', '', strtolower((string)$this->context['length_feet']));
    }

    public function default_values() 
    {
        return [
            'link' => '',
            'thumbnail' => '',
            'title' => '',
            'price_from' => '',
            'price_to' => '',
            'guests' => '',
            'staterooms' => '',
            'boat_type' => '',
            'length_feet' => '',
            'button_text' => 'View Yacht'
        ];
    }
}