<?php
class CronController extends BaseController{

  public function actionIndex(){

    $this->sendNotification();
    $this->deleteOldAccounts();
  }


  /**
   * Удаляем аккаунты по истечении 5 дней активации
   */
  public function deleteOldAccounts(){
    $users = new Users();
    $deleteDays = $this->app->config['inactive_delete_days'];
    $items = $users->where('
      activated_time > 0 and
      ((activated_time + activated_add_time + (86400*'.$deleteDays.') < '.time().') OR
      (type = "archive" AND activated_time + activated_add_time < '.time().'))
    ')->findAll();

    foreach($items as $item){
      $item->delete();
      echo $item->login.' - удален<br /><br />';
    }
  }

  /**
   * Отправляем уведомление об окончании активации
   */
  public function sendNotification(){
    $users = new Users();

    $days = $this->app->config['notification_days'];

    if( empty($days) ) return;

    $sql_add = [];
    foreach($days as $day){
      $sql_add[] = '
      (
        activated_time + activated_add_time - (86400*'.($day-1).') >= '.time().' and
        activated_time + activated_add_time - (86400*'.($day).') < '.time().'
      )
      ';
    }

    $items = $users->where('
      tariff_time >= (86400*10) and
      email != "" and
      activated_time + activated_add_time > '.time().' and
      email_send_time + 86400 < '.time().' and
      (
        '.implode(' or ', $sql_add).'
      )
    ')->limit(0, $this->app->config['notification_limit'])->findAll();

    $languagesData = $this->app->language->data();

    foreach($items as $item){
      $item->email_send_time = time();
      $item->update();

      $language = $item->getLanguage();

      $timeLeft = $item->getActivatedTimeTitle($language);
      $message = $languagesData[$language]['notification_message'];

      $message = str_replace('_LOGIN_', $item->login, $message);
      $message = str_replace('_DATETIME_', $timeLeft, $message);

      echo $languagesData[$language]['notification_subject'].'<br />';
      echo $message.'<br /><br />';

      mail($item->email, $languagesData[$language]['notification_subject'], $message);
    }
  }
}
