<?php
class Codes extends BaseModel{
	use ActivatedTimeTrait;

	public $table = 'codes';
	public $primaryKey = 'id';
	public $relations = array(
		'user' => array(self::BELONGS_TO, 'Users', 'user_id'),
	);

	public function attributeLabels(){
		return [
			'id' => App::t('#'),
			'created_time' => App::t('Дата создания'),
			'activated_time' => App::t('Активирован с'),
			'activated_add_time' => App::t('Длительность'),
			'code' => App::t('Код продления'),
			'user_id' => App::t('Использовал'),
			'service' => App::t('Услуга'),
		];
	}

	public function rules(){
		return [
			[['code', 'service'], 'required'],
			[['code'], 'unique'],
		];
	}

	public function getStatus(){
		if( $this->activated_time > 0 ){

			$date = date('d.m.Y', $this->activated_time).' '.
				App::t('в').' '.date('H:i', $this->activated_time);

			return
			'<font color="red">'.App::t('Активировал').' '.$this->user->login.', '.$date.'</font>';
		}
		return '';
	}
}
