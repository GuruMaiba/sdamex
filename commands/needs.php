<?

$model = new DynamicModel(['name' => $name, 'email' => $email]);
$model->addRule(['name', 'email'], 'string', ['max' => 128])
    ->addRule('email', 'email')
    ->validate();

if ($model->hasErrors()) {
    // validation fails
} else {
    // validation succeeds
}

/////////////////////////////////////

use yii\base\Model;

class MyForm extends Model
{
    public $country;
    public $token;

    public function rules()
    {
        return [
            // an inline validator defined as the model method validateCountry()
            ['country', 'validateCountry'],

            // an inline validator defined as an anonymous function
            ['token', function ($attribute, $params, $validator) {
                if (!ctype_alnum($this->$attribute)) {
                    $this->addError($attribute, 'The token must contain letters or digits.');
                }
            }],
        ];
    }

    public function validateCountry($attribute, $params, $validator)
    {
        if (!in_array($this->$attribute, ['USA', 'Indonesia'])) {
            $this->addError($attribute, 'The country must be either "USA" or "Indonesia".');
        }
    }
}