<?php
class Users extends BaseModel{
	use ActivatedTimeTrait;

	public $table = 'users';
	public $primaryKey = 'id';
	public $relations = array(
		'codes' => array(self::HAS_MANY, 'Codes', 'user_id'),
	);

	public function beforeSave($insert){
		$this->setType();
		$this->saveServices();

		//Меняем тариф при создании аккаунта и при изменении тарифа неактивированного аккаунта
		if( $insert || !$this->activated_time ){
			$this->tariff_time = $this->activated_add_time;
		}
	}

	public function beforeDelete(){
		foreach($this->codes as $code){
			$code->delete();
		}
	}

	public function attributeLabels(){
		return [
			'id' => App::t('ID'),
			'login' => App::t('Логин'),
			'password' => App::t('Пароль'),
			'email' => App::t('E-mail'),
			'need_email' => App::t('Запросить e-mail?'),
			'language' => App::t('Язык'),
			'activated_time' => App::t('Активирован с'),
			'activated_add_time' => App::t('Активирован до'),
			'tariff_time' => App::t('Тариф'),
			'archives_time_info' => App::t('Время жизни архивов'),
			'users_limit' => App::t('Лимит человек'),
			'users_online' => App::t('IP-адреса онлайн'),
			'email_send_time' => App::t('Последняя отправка письма'),
			'ip_old' => App::t('Старый IP'),
			'ip_new' => App::t('Новый IP'),
			'last_enter_time' => App::t('Последний вход'),
			'last_update_time' => App::t('Последняя активность'),
			'services' => App::t('Услуга'),
			'type' => App::t('Тип услуги'),
			'note' => App::t('Примечание'),
			'banned' => App::t('Заблокирован'),
		];
	}

	public function rules(){
		return [
			[['login', 'password', 'need_email', 'users_limit', 'services'], 'required'],
			[['login'], 'unique', 'message' => App::t('Такой логин уже есть в базе')],
		];
	}

	public function tariffDate(){
		return $this->getTimeTitle($this->tariff_time);
	}

	public function activatedDate(){
		if( $this->activated_time == 0 ) return App::t('Нет');
		return date('d.m.Y H:i', $this->activated_time);
	}

	public function activatedTimeLeft(){
		$time = ($this->activated_time + $this->activated_add_time) - time();
		return $this->getTimeTitle($time);
	}

	public function activatedEndDateForAdmin(){
		if( $this->activated_time > 0 && $this->activated_time + $this->activated_add_time < time() ){

			return '<span style="color: red">-'.$this->deleteTimeLeft().'</span>';
		}else{
			return $this->activatedEndDate();
		}
	}

	public function activatedEndDate(){
		if( $this->activated_time == 0 ){
			return $this->getActivatedTimeTitle();
		}
		return date('d.m.Y H:i', intval($this->activated_time + $this->activated_add_time));
	}

	public function enterDate(){
		if( $this->last_enter_time == 0 ) return App::t('Нет');
		return date('d.m.Y', intval($this->last_enter_time));
	}

	public function onLine(){
		if( $this->last_update_time + 600 > time() ) return 1;
		return 0;
	}

	public function usersOnlineLimited(){
		if( $this->blank('users_online') ) return false;

		$usersOnline = unserialize($this->users_online);
		$ip = $_SERVER['REMOTE_ADDR'];

		unset($usersOnline[$ip]);

		foreach($usersOnline as $userIp=>$userTime){
			if( $userTime + 600 > time() ) continue;
			unset($usersOnline[$userIp]);
		}

		if( count($usersOnline) >= $this->users_limit ) return true;
		return false;
	}

	public function usersOnlineSet(){
		$usersOnline = [];
		if( !$this->blank('users_online') ){
			$usersOnline = unserialize($this->users_online);
		}
		$ip = $_SERVER['REMOTE_ADDR'];

		$usersOnline[$ip] = time();
		$this->users_online = serialize($usersOnline);
	}

	public function usersOnlineDel(){
		$usersOnline = [];
		if( !$this->blank('users_online') ){
			$usersOnline = unserialize($this->users_online);
		}
		$ip = $_SERVER['REMOTE_ADDR'];

		unset($usersOnline[$ip]);
		$this->users_online = serialize($usersOnline);
	}

	public function getLanguage(){
		return $this->getData('language', App::get()->config['default_language']);
	}

	/**
	 * Принимает массив сервисов и сохраняет их в виде строки черехз запятую
	 */
	public function saveServices(){
		if( $this->getData('services', '') == '' ){
			$this->services = '';
		}else
		if( is_array($this->services) ){
			$this->services = array_filter($this->services, function($item){
				if( !empty($item) ) return true;
			});
			$this->services = implode(',', $this->services);
		}
	}

	/**
	 * Возвращает массив сервисов
	 */
	public function loadServices(){
		if( !is_array($this->services) ){
			$this->services = explode(',', $this->services);
		}
	}

	/**
	 * Определяет статус активации
	 */
	public function activationTimeout(){
		if($this->activated_time + $this->activated_add_time < time() ) return true;
		return false;
	}

	/**
	 * Время до удаления аккаунта
	 */
	public function deleteDate(){
		$add_days = App::get()->config['inactive_delete_days']*86400;
		echo date('d.m.Y H:i', $this->activated_time + $this->activated_add_time + $add_days);
	}

	/**
	 * Отсчет до удаления аккаунта
	 */
	public function deleteTimeLeft(){
		$time = ($this->activated_time + $this->activated_add_time) - time();
		$time += App::get()->config['inactive_delete_days'] * 86400;
		return $this->getTimeTitle($time);
	}

	/**
	 * Тип аккаунта (txt, page или archive)
	 */
	public function setType(){
		$this->loadServices();
		$services = new Services();
		$services = $services
			->in('sid', $this->services)
			->eq('parent', '0')
			->findAll();

		foreach( $services as $service ){
			if( $service->type == 'txt' ){
				return $this->type = 'txt';
			}else
			if( $service->type == 'txtgroup' ){
				return $this->type = 'txtgroup';
			}else
			if( $service->type == 'page' ){
				return $this->type = 'page';
			}
		}

		$this->type = 'archive';
	}

	/**
	 * Статус аккаунта - активен, заблокирован или приостановлен
	 */
	public function accountStatus(){
		if( $this->banned ) return 3;
		if( $this->activationTimeout() ) return 2;
		return 1;
	}

	/**
	 * Десериализуем инфо о жизни архивов
	 */
	public function archivesTimeUnserialize(){
		$archivesTime = [];
		if( !$this->blank('archives_time_info') ){
			$archivesTime = unserialize($this->archives_time_info);
		}

		return $archivesTime;
	}

	/**
	 * Устанавливаем время жизни архивов
	 */
	public function archivesTimeSet(){
		$this->loadServices();

		$services = new Services();
		$services->in('sid', $this->services);
		$models = $services->findAll();

		$archivesTime = $this->archivesTimeUnserialize();

		foreach( $models as $model ){
			if( !in_array($model->type, ['zip', 'rar']) ) continue; //Записываем только про архивы
			if( isset($archivesTime[$model->id]) ) continue; //Пропускаем записанные архивы

			$archivesTime[$model->id] = time();
		}

		$this->archives_time_info = serialize($archivesTime);
		$this->update();
	}

	/**
	 * Возвращает id устаревших архивов
	 */
	public function archivesTimeOld(){
		$archivesTime = $this->archivesTimeUnserialize();
		$lifeTime = App::get()->config['txt_archives_life_min'] * 60;

		$oldIds = [];
		foreach($archivesTime as $id=>$time){
			if( $time + $lifeTime > time() ) continue; //Пропускаем свежие архивы
			$oldIds[] = $id;
		}

		return $oldIds;
	}

	/**
	 * Возвращает массив окончания времен, где ключ - id сервиса
	 */
	public function archivesTimeOutTitles(){
		$archivesTime = $this->archivesTimeUnserialize();
		$lifeTime = App::get()->config['txt_archives_life_min'] * 60;

		$timesInfo = [];
		foreach($archivesTime as $id=>$time){
			if( $time + $lifeTime < time() ) continue; //Пропускаем старые архивы
			$timesInfo[$id] = $this->getTimeTitle(($time + $lifeTime) - time());
		}

		return $timesInfo;
	}

	/**
	 * Выбираем основной сервис (если архив - архив, если текс+архив - текст)
	 */
	function getMainService(){
		$this->loadServices();

		$services = new Services();
		$services->in('sid', $this->services);
		$services->eq('parent', '0');
		if( $this->type != 'archive' ){
			$services->in('type', ['txt', 'txtgroup', 'page']);
		}
		return $services->find();
	}
}
