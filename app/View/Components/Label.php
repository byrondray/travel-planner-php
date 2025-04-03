namespace App\View\Components;

use Illuminate\View\Component;

class Label extends Component
{
public $value;

public function __construct($value = null)
{
$this->value = $value;
}

public function render()
{
return view('components.label');
}
}