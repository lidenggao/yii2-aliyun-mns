# yii2-aliyun-mns
YII2插件-阿里云消息队列SDK
===
配置
---
```php
'mns'=>[
    'class'=>'colee\aliyun\Mns',
    'accessId' => '',
    'accessKey' => '',
    'endpoint' => 'http://.mns.cn-beijing.aliyuncs.com/',
],
```
使用示例：
---
```php
// 发送消息到队列
\Yii::$app->mns->sendMessage("QueueName", "content demo");
// 接收队列消息
$messageObject = \Yii::$app->mns->receiveMessage("QueueName");
$data = $messageObject->getMessageBody();
// 删除队列消息
\Yii::$app->mns->->deleteMessage('QueueName', $messageObject);
//publish 消息到主题
\Yii::$app->mns->publishMessage('TopicName', $data);
//订阅主题，在Yii2的 controller 中接收推送过来的数据
public function actionSubscribe()
{
	$message = \Yii::$app->request->getRawBody();
	$data = json_decode($message, true); //如果消息是JSON，PHP中需要转换成数组
｝
```