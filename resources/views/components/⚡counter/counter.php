<?php

use Livewire\Component;

new class extends Component {
    public $count = 0;
    
    public function increment() {
        $this->count++;
    }
};