# yii2-aliyun-mns
YII2插件-阿里云消息队列SDK

### 配置
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
// 发送消息
\Yii::$app->mns->sendMessage("QueueName", "content demo");
// 接收消息
$messageObject = \Yii::$app->mns->receiveMessage("QueueName");
// 删除消息
\Yii::$app->mns->->deleteMessage('QueueName', $messageObject);
```