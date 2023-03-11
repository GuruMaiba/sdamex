<?php

namespace app\controllers;

use Yii;
use app\models\{User,Payment};
use app\models\course\{Course, Student};
use app\models\webinar\{Webinar, Member};
use app\models\promoter\Code;
use app\controllers\AppController;
use yii\web\Response;
use yii\web\HttpException;
use yii\helpers\Url;
use yii\httpclient\Client;
use app\components\{PayType, CodeType};

class PayController extends AppController
{
    public $layout = false;
    public $lessonCost = 700;
    public $endCourseSale = 10;
    public $testId = 6;

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => \yii\filters\VerbFilter::className(),
                'actions' => [
                    'index'             => ['GET'],
                    // 'logout'            => ['GET'],
                    // 'signin'            => ['POST'],
                    // 'signup'            => ['POST'],
                    // 'resetpass'         => ['POST'],
                    // 'newpass'           => ['GET'],
                    // 'set-newpass'       => ['POST'],
                    // 'change-option'     => ['POST'],
                    // 'sub-change-ava'    => ['POST'],
                    // 'oauth-vk'          => ['GET'],
                    // 'oauth-fb'          => ['GET'],
                    // 'oauth-gl'          => ['GET'],
                ],
            ],
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                // 'only' => ['sub-change-ava', 'change-option'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@']
                        // 'actions' => ['sub-change-ava', 'change-option'],
                        // 'matchCallback' => function ($rule, $action) {
                        //     return (Yii::$app->request->post('secretKey') === Yii::$app->params['secretKey']);
                        // },
                    ],
                ],
            ],
            // [
            //     'class' => 'yii\filters\PageCache',
            //     'only' => ['index'],
            //     'duration' => 3600, //3600
            //     // 'variations' => [
            //     //     \Yii::$app->language,
            //     // ],
            //     // 'dependency' => [
            //     //     'class' => 'yii\caching\DbDependency',
            //     //     'sql' => 'SELECT COUNT(*) FROM post',
            //     // ],
            // ],
        ];
    }

    public function actionIndex()
    {
        $this->view->title = 'SDAMEX PAY | УРОКИ';
        // $this->view->registerMetaTag([
        //     'name' => 'description',
        //     'content' => 'Стань онлайн учителем ОГЭ, ЕГЭ и получай процент с продаж. На всех этапах подготовки курса, мы поможем и окажем содействие, вливайся в солнечную команду SDAMEX!',
        // ]);
        // $this->view->registerMetaTag([
        //     'name' => 'keywords',
        //     'content' => 'огэ, егэ, учитель, онлайн курс, ведущий курса, стать учителем, партнёрские условия, '.Yii::$app->params['commonKeyWords'],
        // ]);

        return $this->render('index', [
            'type' => 'lesson',
            'cost' => $this->lessonCost
        ]);
    }

    public function actionLesson()
    {
        $this->view->title = 'SDAMEX PAY | УРОКИ';
        // $this->view->registerMetaTag([
        //     'name' => 'description',
        //     'content' => 'Стань онлайн учителем ОГЭ, ЕГЭ и получай процент с продаж. На всех этапах подготовки курса, мы поможем и окажем содействие, вливайся в солнечную команду SDAMEX!',
        // ]);
        // $this->view->registerMetaTag([
        //     'name' => 'keywords',
        //     'content' => 'огэ, егэ, учитель, онлайн курс, ведущий курса, стать учителем, партнёрские условия, '.Yii::$app->params['commonKeyWords'],
        // ]);
        return $this->render('index', [
            'type' => 'lesson',
            'cost' => $this->lessonCost
        ]);
    }

    public function actionCourse($id = 0)
    {
        if ($id <= 0)
            throw new HttpException(404);

        $course = Course::find()->select(['id','subject_id','title','cost','free'])->where(['id'=>$id, 'publish'=>1])->asArray()->limit(1)->one();
        if (!$course || $corse['free'] || !$course['cost'])
            throw new HttpException(404);

        $course['costSale'] = $course['cost'];
        $lowCost = (int)($course['cost'] * 0.9);
        while ($lowCost % 10 != 0)
            --$lowCost;

        $course['partSale'] = $course['cost'] + ($lowCost*2);
        $course['partPrice'] = $course['cost'] * 3;
        $course['fullSale'] = $this->getFullprice($course['cost'], $lowCost);
        $course['fullPrice'] = $this->getFullprice($course['cost'], $course['cost']);

        $isSale = false;
        $user = Yii::$app->user->identity;
        $stats = json_decode($user->statistics, true);
        if ($course['cost'] >= 1500 && !empty($user->invite_code) && ($user->invite_code != 'SPENT' || !empty($stats[1]['saleCode']))) {
            $code = $user->invite_code;
            // Делаем скидку ещё 3 дня
            if (!empty($stats[1]['saleCode'])) {
                if ($stats[1]['saleEnd'] > time() && !in_array($course['id'], $stats[1]['saleCourses'])) {
                    $code = $stats[1]['saleCode'];
                } else {
                    unset($stats[1]['saleCode']);
                    unset($stats[1]['saleEnd']);
                    unset($stats[1]['saleCourses']);
                    $user->statistics = json_encode($stats);
                    $user->update();
                }
            }

            $code = Code::find()->where(['code' => $code])->asArray()->limit(1)->one();
            if ($code && $code['end_at'] > time()) {
                $props = CodeType::getPropsArr();
                $props = $props[$code['type']];

                if ($props['sale_cost'] > 0) {
                    $isSale = true;

                    // 
                    $course['costSale'] -= $props['sale_cost'];
                    $course['costSale'] = ($course['costSale'] < 0) ? 0 : $course['costSale'];
                    //
                    $course['partSale'] -= $props['sale_cost'];
                    $course['partSale'] = ($course['partSale'] < 0) ? 0 : $course['partSale'];
                    //
                    $course['fullSale'] -= $props['sale_cost'];
                    $course['fullSale'] = ($course['fullSale'] < 0) ? 0 : $course['fullSale'];
                }

                if ($props['sale_percent'] > 0) {
                    $isSale = true;

                    //
                    $course['costSale'] -= (int)($course['costSale'] / 100 * $props['sale_percent']);
                    $course['partSale'] -= (int)($course['partSale'] / 100 * $props['sale_percent']);
                    $course['fullSale'] -= (int)($course['fullSale'] / 100 * $props['sale_percent']);
                }
            }
        } else {    
            $isStudent = Student::find()->where(['learner_id'=>$user->id, 'course_id'=>$course['id']])->asArray()->limit(1)->one();
            if ($isStudent && ($isStudent['end_at']+(3*24*3600)) > time()) {
                $isSale = true;
                $course['costSale'] -= (int)($course['costSale'] / 100 * $this->endCourseSale);
                $course['partSale'] -= (int)($course['partSale'] / 100 * $this->endCourseSale);
                $course['fullSale'] -= (int)($course['fullSale'] / 100 * $this->endCourseSale);
            }
        }

        $this->view->title = "SDAMEX PAY | КУРС: $course[title]";
        // $this->view->registerMetaTag([
        //     'name' => 'description',
        //     'content' => 'Стань онлайн учителем ОГЭ, ЕГЭ и получай процент с продаж. На всех этапах подготовки курса, мы поможем и окажем содействие, вливайся в солнечную команду SDAMEX!',
        // ]);
        // $this->view->registerMetaTag([
        //     'name' => 'keywords',
        //     'content' => 'огэ, егэ, учитель, онлайн курс, ведущий курса, стать учителем, партнёрские условия, '.Yii::$app->params['commonKeyWords'],
        // ]);

        // return $this->debug(Yii::$app->params['listSubs'][$course['subject_id']]);
        return $this->render('index', [
            'type' => 'course',
            'model' => $course,
            'subject' => Yii::$app->params['listSubs'][$course['subject_id']],
            'isSale' => $isSale,
            'code' => $code['code']
        ]);
    }

    public function actionWebinar($id = 0)
    {
        if ($id <= 0)
            throw new HttpException(404);

        $webinar = Webinar::find()->select(['id','subject_id','title','cost'])->where(['id'=>$id, 'publish'=>1])->asArray()->limit(1)->one();
        if (!$webinar || !$webinar['cost'])
            throw new HttpException(404);

        $this->view->title = "SDAMEX PAY | ВЕБИНАР: $webinar[title]";
        // $this->view->registerMetaTag([
        //     'name' => 'description',
        //     'content' => 'Стань онлайн учителем ОГЭ, ЕГЭ и получай процент с продаж. На всех этапах подготовки курса, мы поможем и окажем содействие, вливайся в солнечную команду SDAMEX!',
        // ]);
        // $this->view->registerMetaTag([
        //     'name' => 'keywords',
        //     'content' => 'огэ, егэ, учитель, онлайн курс, ведущий курса, стать учителем, партнёрские условия, '.Yii::$app->params['commonKeyWords'],
        // ]);

        // return $this->debug(Yii::$app->params['listSubs'][$course['subject_id']]);
        return $this->render('index', [
            'type' => 'webinar',
            'model' => $webinar,
            'subject' => Yii::$app->params['listSubs'][$webinar['subject_id']],
        ]);
    }

    public function actionPayment()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $id = (int)$_POST['id'];
        $type = $_POST['type'];
        $err = ['type'=>'error', 'message'=>'Произошла ошибка передачи данных, попробуйте повторить позже!'];

        if (empty($id) || $id < 1 || empty($type))
            return $err;

        $amount = 0;
        $order = new Payment;
        $item = [
            'name' => 'Подписка',
            'qnt' => 1,
            'price' => 0,
        ];
        $options = [ 'duration' => $_POST['duration'], ];
        $user = Yii::$app->user->identity;

        switch ($type) {
            case PayType::COURSE:
                if (empty($options['duration']))
                    return $err;

                $course = Course::find()->select(['id','subject_id','title','cost'])->where(['id'=>$id, 'publish'=>1])->asArray()->limit(1)->one();
                if (!$course)
                    return $err;

                $locPay = Payment::find()->where(['user_id'=>$user->id, 'model_id'=>$id, 'success'=>0, 'type'=>PayType::COURSE])->limit(1)->one();
                if ($locPay) {
                    $order = $locPay;
                } else {
                    $order->user_id = $user->id;
                    $order->model_id = $id;
                    $order->type = PayType::COURSE;
                    $order->success = 0;
                }

                $order->desc = "Приобритение доступа к курсу";
                $order->amount = (int)($course['cost']);
                $lowCost = (int)($course['cost'] * 0.9);
                while ($lowCost % 10 != 0)
                    --$lowCost;

                switch ($options['duration']) {
                    case 1:
                        $order->desc .= " на срок 1 месяц.";
                        $options['days'] = 30;
                        break;

                    case 2:
                        $order->amount += ($lowCost*2);
                        $options['days'] = 90;
                        $order->desc .= " на срок 3 месяца.";
                        break;

                    case 3: // Добавить количество дней (200 дней)
                        $order->amount = (int)$this->getFullprice($course['cost'], $lowCost);
                        $options['days'] = $this->getPayDays();
                        $order->desc .= " на полный учебный год.";
                        break;
                    
                    default:
                        return $err;
                }

                $stats = json_decode($user->statistics, true);
                if ($course['cost'] >= 1500 && !empty($user->invite_code) && ($user->invite_code != 'SPENT' || !empty($stats[1]['saleCode']))) {
                    $code = $user->invite_code;
                    if (!empty($stats[1]['saleCode']) && $stats[1]['saleEnd'] > time() && !in_array($course['id'], $stats[1]['saleCourses']))
                        $code = $stats[1]['saleCode'];
                    $code = Code::find()->where(['code' => $code])->asArray()->limit(1)->one();
                    if ($code && $code['end_at'] > time()) {
                        $order->code = $code['code'];
                        $props = CodeType::getPropsArr();
                        $props = $props[$code['type']];

                        if ($props['sale_cost'] > 0 || $props['sale_percent'] > 0) {
                            $order->desc .= " Со скидкой:";

                            if ($props['sale_cost'] > 0) {
                                $order->desc .= " $props[sale_cost] рублей";
                                $order->amount = $order->amount - $props['sale_cost'];
                                if ($order->amount < 0)
                                    $order->amount = 0;
                            }
    
                            if ($props['sale_percent'] > 0) {
                                if ($props['sale_cost'] > 0)
                                    $order->desc .= " и";
                                $order->desc .= " $props[sale_percent]%";
                                $order->amount -= (int)($order->amount / 100 * $props['sale_percent']);
                            }

                            $order->desc .= ".";
                        } // end if sale
                    } else if ($code['end_at'] < time())
                        $order->code = null;
                } // end if invite_code
                else {
                    $isStudent = Student::find()->where(['learner_id'=>$user->id, 'course_id'=>$course['id']])->asArray()->limit(1)->one();
                    if ($isStudent && $isStudent['end_at']+(3*24*3600) > time())
                        $order->amount -= (int)($order->amount / 100 * $this->endCourseSale);
                }

                $order->desc .= " К оплате: $order->amount рублей.";
                $order->extra_options = json_encode($options);

                $item['name'] = "Подписка на курс: $course[title]";
                $item['price'] = $order->amount;
                break;

            case PayType::WEBINAR:
                $webinar = Webinar::find()->select(['id','title','cost'])->where(['id'=>$id, 'publish'=>1])->asArray()->limit(1)->one();
                if (!$webinar || $webinar['cost'] == 0)
                    return $err;

                $locPay = Payment::find()->where(['user_id'=>$user->id, 'model_id'=>$id, 'success'=>0, 'type'=>PayType::WEBINAR])->limit(1)->one();
                if ($locPay) {
                    $order = $locPay;
                } else {
                    $order->user_id = $user->id;
                    $order->model_id = $id;
                    $order->type = PayType::WEBINAR;
                    $order->success = 0;
                }
                $order->amount = (int)($webinar['cost']);
                $order->desc = "Приобритение доступа к вебинару. К оплате: $order->amount рублей.";

                $item['name'] = "Доступ к вебинару: $webinar[title]";
                $item['price'] = $order->amount;
                break;

            case PayType::LESSON:
                if ($id > 99)
                    return $err;

                $locPay = Payment::find()->where(['user_id'=>$user->id, 'success'=>0, 'type'=>PayType::LESSON])->limit(1)->one();
                if ($locPay) {
                    $order = $locPay;
                } else {
                    $order->user_id = $user->id;
                    $order->type = PayType::LESSON;
                    $order->success = 0;
                }
                $order->extra_options = "$id";
                $order->amount = (int)($this->lessonCost * $id);
                $order->desc = "Покупка уроков, в кол. $id. К оплате: $order->amount рублей.";

                $item['qnt'] = $id;
                $item['name'] = "Занятие с преподавателем";
                $item['price'] = $this->lessonCost;
                break;
            
            default:
                return $err;
                break;
        }
        // return $order;
        $order->updated_at = time();
        $order->save();

        if ($order->amount > 0) {
            $client = new Client();
            $response = $client->createRequest()
                ->setFormat(Client::FORMAT_JSON)
                ->setMethod('post')
                ->setUrl('https://securepay.tinkoff.ru/v2/Init')
                ->setData([
                    'TerminalKey' => '1601455323664', //DEMO
                    'Amount' => $order->amount * 100,
                    'OrderId' => $order->id,
                    'Description' => $order->desc,
                    'Receipt' => [
                        'Email' => $user->email,
                        // 'Phone' => '+79031234567',
                        'EmailCompany' => 'team@sdamex.ru',
                        'Taxation' => 'usn_income',
                        'Items' => [
                            [
                                'Name' => $item['name'],
                                'Price' => $item['price'] * 100,
                                'Quantity' => number_format($item['qnt'], 2, '.', ''),
                                'Amount' => $order->amount * 100,
                                'PaymentObject' => 'service',
                                'Tax' => 'none'
                            ],
                        ],
                    ]
                ])->send();

            // ERROR ======
            // Details: "Заказ 1 был оплачен."
            // ErrorCode: "8"
            // Message: "Неверный статус транзакции."
            // Success: false
            // --------------------------------------
            // SUCCESS ====
            // Amount: 200000
            // ErrorCode: "0"
            // OrderId: "2"
            // PaymentId: "324753476"
            // PaymentURL: "https://securepay.tinkoff.ru/new/E26l6h5g"
            // Status: "NEW"
            // Success: true
            // TerminalKey: "1601455323664DEMO"
            
            if (!$response->isOk)
            return [ 'type' => 'error', 'message' => 'Что-то пошло не так!' ];

            $resp = $response->data;
            if ($resp['Success']) {
                $order->payment_id = $resp['PaymentId'];
                $order->update();
                return [ 'type' => 'success', 'link' => $resp['PaymentURL'], 'details' => $resp['Details'] ];
            } else {
                return [ 'type' => 'error', 'message' => "Что-то пошло не так. $resp[Message]", 'details' => $resp['Details'] ];
            }
        } else if ($type == PayType::COURSE) {
            $dayPrice = (int)$options['days'];

            // DEBUG: удалить дублирование
            $student = Student::find()->where(['learner_id'=>$user->id, 'course_id'=>$order->model_id])->limit(1)->one();
            if (!$student) {
                $student = new Student;
                $student->learner_id = $user->id;
                $student->course_id = $order->model_id;

                $stats = json_decode($user->statistics, true);
                if (empty($stats[$course['subject_id']]['courses'][$order->model_id])) {
                    $stats[$course['subject_id']]['courses'][$order->model_id] = [
                        'end' => 0,
                        'modules' => [],
                    ];
                    $user->statistics = json_encode($stats);
                    $user->update();
                }
            }

            if ($student->end_at > time()) {
                $student->end_at = $student->end_at+($dayPrice*24*60*60);
            } else {
                $student->start_at = time();
                $student->end_at = time()+($dayPrice*24*60*60);
            }
            
            $student->save();

            $user->invite_code = 'SPENT';
            $user->update();

            $order->success = 1;
            $order->update();

            $sub = Yii::$app->params['listSubs'][$course['subject_id']];
            $link = $sub['link']."course/$course[id]";

            return [
                'type' => 'success',
                'link' => Url::to([
                    'pay/success', 'free' => 1,
                    'message' => "Вам открыт доступ к курсу: <br><a href='$link'>$course[title]</a>."
                ]),
            ];
        }

        return $err;
    }

    public function actionSuccess()
    {
        $this->view->title = "SDAMEX PAY";

        if ($_GET['free'])
            return $this->render('index', [
                'type' => 'success',
                'message' => (!empty($_GET['message'])) ? $_GET['message'] : 'Вы использовали инвайт-код, доступ к этому курсу открыт!',
            ]);

        if (!$_GET['Success'])
            return $this->render('index', [ 'type' => 'error', 'message' => $_GET['Message'] ]);

        $order = ($_GET['OrderId'] > 0) ? Payment::find()->where(['id'=>$_GET['OrderId'], 'payment_id'=>$_GET['PaymentId']])->limit(1)->one() : null;
        if (!$order)
            return $this->render('index', [
                'type' => 'error',
                'message' => 'В системе произошла ошибка, обратитесь в службу поддержки!'
            ]);

        if ($order->success)
            return $this->render('index', [
                'type' => 'success',
                'message' => 'Оплата прошла успешно.'
            ]);

        if ($order->amount != (int)($_GET['Amount']/100))
            return $this->render('index', [
                'type' => 'error',
                'message' => 'Сумма заказа и сумма оплаты не сходятся! Обратитесь в службу поддержки, для получения оплаченной услуги.',
            ]);

        $message = "Оплата прошла успешно. ";
        $user = User::find()->select(['id','teacher_class','statistics'])->where(['id'=>$order->user_id])->limit(1)->one();
        if (!$user)
            return $this->render('index', [
                'type' => 'error',
                'message' => "Пользователь не найден, обратитесь в службу поддержки и сообщите номер заказа - $order->id",
            ]);

        switch ($order->type) {
            case PayType::COURSE:
                $course = Course::find()->select(['id', 'subject_id', 'title', 'cost'])
                    ->where(['id'=>$order->model_id])->asArray()->limit(1)->one();
                if (!$course)
                    return $this->render('index', [
                        'type' => 'error',
                        'message' => "Курс не найден, обратитесь в службу поддержки и сообщите номер заказа - $order->id",
                    ]);

                $dayPrice = json_decode($order->extra_options, true);
                $dayPrice = (int)$dayPrice['days'];

                // DEBUG: удалить дублирование
                $student = Student::find()->where(['learner_id'=>$user->id, 'course_id'=>$order->model_id])->limit(1)->one();
                if (!$student) {
                    $student = new Student;
                    $student->learner_id = $user->id;
                    $student->course_id = $order->model_id;

                    $stats = json_decode($user->statistics, true);
                    if (empty($stats[$course['subject_id']]['courses'][$order->model_id])) {
                        $stats[$course['subject_id']]['courses'][$order->model_id] = [
                            'end' => 0,
                            'modules' => [],
                        ];
                        $user->statistics = json_encode($stats);
                        $user->update();
                    }
                }

                if ($student->end_at > time()) {
                    $student->end_at = $student->end_at+($dayPrice*24*60*60);
                } else {
                    $student->start_at = time();
                    $student->end_at = time()+($dayPrice*24*60*60);
                }

                $student->save();

                if ($course['cost'] >= 1500 && !empty($order->code)) {
                    $code = Code::find()->where(['code'=>$order->code])->asArray()->limit(1)->one();
                    if ($code && $code['end_at'] > time() && $code['reward'] > 0) {
                        $promoter = User::find()->select(['id','cash'])->where(['id'=>$code['promoter_id']])
                            ->with([
                                'seller' => function ($query) { $query->select(['id', 'cash']); }
                            ])->limit(1)->one();

                        if ($promoter) {
                            $props = CodeType::getPropsArr();
                            $props = $props[$code['type']];

                            if ($props['sale_cost'] > 0) {
                                $course['cost'] -= $props['sale_cost'];
                                $course['costSale'] = ($course['cost'] < 0) ? 0 : $course['cost'];
                            }
            
                            if ($props['sale_percent'] > 0)
                                $course['cost'] -= (int)($course['cost'] / 100 * $props['sale_percent']);
                                
                            $promoter->cash += ($code['reward'] > 30) ? $code['reward'] : ($order->amount * ($code['reward']/100));
                            $promoter->update();
                            if ($promoter->seller) {
                                $promoter->seller->cash += $order->amount * (5/100);
                                $promoter->seller->update();
                            }

                            // Делаем скидку ещё 3 дня
                            $stats = json_decode($user->statistics, true);
                            if (empty($stats[1]['saleCode'])) {
                                $stats[1]['saleCode'] = $order->code;
                                $stats[1]['saleEnd'] = time()+(3*24*3600);
                                $stats[1]['saleCourses'] = [$course['id']];
                            } else 
                                $stats[1]['saleCourses'][] = $course['id'];

                            $user->statistics = json_encode($stats);
                        }

                        $user->invite_code = 'SPENT';
                        $user->update();
                    }
                }

                $sub = Yii::$app->params['listSubs'][$course['subject_id']];
                $link = $sub['link']."course/$course[id]";
                $message .= "Вам открыт доступ к курсу: <br><a href='$link'>$course[title]</a>.";
                break;

            case PayType::WEBINAR:
                $webinar = Webinar::find()->select(['id', 'subject_id', 'title'])
                    ->where(['id' => $order->model_id])->asArray()->limit(1)->one();
                if (!$webinar)
                    return $this->render('index', [
                        'type' => 'error',
                        'message' => "Вебинар не найден, обратитесь в службу поддержки и сообщите номер заказа - $order->id",
                    ]);

                $member = new Member;
                $member->webinar_id = $order->model_id;
                $member->user_id = $user->id;
                $member->save();

                $sub = Yii::$app->params['listSubs'][$webinar['subject_id']];
                $link = $sub['link']."webinar/$webinar[id]";
                $message .= "Вам открыт доступ к вебинару: <br><a href='$link'>$webinar[title]</a>.";
                break;

            case PayType::LESSON:
                $user->teacher_class += (int)$order->extra_options;
                $user->update();

                $message .= "Вам начисленны занятия в количестве: <br>$order->extra_options.";
                break;
        }

        $order->success = 1;
        $order->update();

        return $this->render('index', [
            'type' => 'success',
            'message' => $message,
        ]);
    }

    public function actionError()
    {
        $this->view->title = "SDAMEX PAY";
        // DEBUG: Сделать обработку и отправлять на 2 круг!
        return $this->render('index', [
            'type' => 'error',
            'message' => 'Оплата не была произведена! '.$_GET['Message'], //.' <br><a href="/pay">Попробовать ещё раз?</a>'
        ]);
    }

    private function getFullprice($cost, $lowCost)
    {
        $payDays = $this->getPayDays();
        $payMonths = intdiv($payDays, 30);
        if ($payMonths > 1) {
            $remainder = $payDays % 30; // остаток дней
            $fullPrice = (int)($cost + (($payMonths-1) * $lowCost) + ($remainder * ceil($lowCost/30))); // вычисляем стоимость всех дней
        } else
            $fullPrice = $cost;
        
        // $fullPrice -= (int)($fullPrice / 100 * $sale); // делаем скидку
        
        return $fullPrice;
    }

    private function getPayDays()
    {
        $now = time();
        $month = (int)date('m', $now);
        $year = (int)date('Y', $now);
        if ($month > 6)
            ++$year;
        $july = mktime(0, 0, 0, 6, 30, $year);
        $payDays = intdiv(($july - $now),(24*60*60));
        if ($payDays < 30)
            $payDays = 30;

        return $payDays;
    }
}
