<?php
class Buttons extends BaseModel{
	use ActivatedTimeTrait;

	public $table = 'buttons';
	public $primaryKey = 'id';

	public function attributeLabels(){
		return [
			'id' => App::t('#'),
			'created_time' => App::t('Дата создания'),
			'activated_add_time' => App::t('Тариф'),
			'link' => App::t('Ссылка'),
			'service' => App::t('Услуга'),
		];
	}

	public function rules(){
		return [
			[['link', 'service'], 'required'],
		];
	}

	public static function showButtons(){
		$buttons = new self();

		$userInfo = App::get()->user->identity;
		$userInfo->loadServices();

		$buttons = $buttons
			->in('service', $userInfo->services)
			->findAll();

		$links = [];

		foreach($buttons as $button){
			$days = ceil($button->activated_add_time / 86400);
			$text = App::t('Продлить на {days} дней');
			$text = str_replace('{days}', $days, $text);
			$links[] = '<a href="'.$button->link.'" target="_blank" class="btn btn-primary">'.$text.'</a>';
		}

		return implode(' ', $links);
	}
}
