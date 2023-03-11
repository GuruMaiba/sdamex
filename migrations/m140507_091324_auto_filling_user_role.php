<?php

use yii\db\Migration;
use app\models\User;
use app\components\UserStatus;

/**
 * Class m140507_091324_auto_filling_user_role
 */
class m140507_091324_auto_filling_user_role extends Migration
{
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        // Создаём разрешение на редактирование своего профиля
        $updateProfile = $auth->createPermission('updateProfile');
        $updateProfile->description = 'Update profile';
        $auth->add($updateProfile);

        $uRule = new \app\rbac\UserRule;
        $auth->add($uRule);

        // add the "updateOwnProfile" permission and associate the rule with it.
        $updateOwnProfile = $auth->createPermission('updateOwnProfile');
        $updateOwnProfile->description = 'Update own profile';
        $updateOwnProfile->ruleName = $uRule->name;
        $auth->add($updateOwnProfile);

        // "updateOwnProfile" will be used from "updateprofile"
        $auth->addChild($updateOwnProfile, $updateProfile);

        $user = $auth->createRole('user');
            $auth->add($user);
            $auth->addChild($user, $updateOwnProfile);

        $promoter = $auth->createRole('promoter');
            $auth->add($promoter);
            $auth->addChild($promoter, $user);

        $seller = $auth->createRole('seller');
            $auth->add($seller);
            $auth->addChild($seller, $promoter);

        $checkTeacher = $auth->createRole('checkTeacher');
            $auth->add($checkTeacher);
            $auth->addChild($checkTeacher, $promoter);

        $speaker = $auth->createRole('speaker');
            $auth->add($speaker);
            $auth->addChild($speaker, $promoter);

        $teacher = $auth->createRole('teacher');
            $auth->add($teacher);
            $auth->addChild($teacher, $checkTeacher);
            $auth->addChild($teacher, $promoter);

        $mainTeacher = $auth->createRole('mainTeacher');
            $auth->add($mainTeacher);
            $auth->addChild($mainTeacher, $speaker);
            $auth->addChild($mainTeacher, $teacher);

        $manager = $auth->createRole('manager');
            $auth->add($manager);
            $auth->addChild($manager, $promoter);

        $topManager = $auth->createRole('topManager');
            $auth->add($topManager);
            $auth->addChild($topManager, $manager);

        $moderator = $auth->createRole('moderator');
            $auth->add($moderator);
            $auth->addChild($moderator, $promoter);

        $financier = $auth->createRole('financier');
            $auth->add($financier);
            $auth->addChild($financier, $promoter);

        $assistant = $auth->createRole('assistant');
            $auth->add($assistant);
            $auth->addChild($assistant, $promoter);

        $admin = $auth->createRole('admin');
            $auth->add($admin);
            $auth->addChild($admin, $updateProfile);
            $auth->addChild($admin, $seller);
            $auth->addChild($admin, $mainTeacher);
            $auth->addChild($admin, $topManager);
            $auth->addChild($admin, $moderator);
            $auth->addChild($admin, $financier);
            $auth->addChild($admin, $assistant);

        $megaAdmin = $auth->createRole('MegaAdmin');
            $auth->add($megaAdmin);
            $auth->addChild($megaAdmin, $admin);

        $support = new User;
            $support->username = 'Support';
            $support->name = 'Служба Поддержки';
            $support->email = 'support@sdamex.ru';
            $support->status = UserStatus::ACTIVE;
            $support->statistics = json_encode([ Yii::$app->params['subInx'] => [] ]);
            $support->setPassword('SupportSdamex2020@');
            $support->generateAuthKey();
            $support->save();

        $auth->assign($moderator, $support->id);

        $polya = new User;
            $polya->username = 'Sunny';
            $polya->email = 'pmilomaeva@mail.ru';
            $polya->status = UserStatus::ACTIVE;
            $polya->statistics = json_encode([ Yii::$app->params['subInx'] => [] ]);
            $polya->setPassword('IloveUniverse2505gtsu');
            $polya->generateAuthKey();
            $polya->save();

        $auth->assign($admin, $polya->id);

        $maiba = new User;
            $maiba->username = 'Maiba';
            $maiba->email = 'gurumaiba@gmail.com';
            $maiba->status = UserStatus::ACTIVE;
            $maiba->statistics = json_encode([ Yii::$app->params['subInx'] => [] ]);
            $maiba->setPassword('MegaGuru1305@');
            $maiba->generateAuthKey();
            $maiba->save();

        $auth->assign($megaAdmin, $maiba->id);
    }

    public function safeDown()
    {
        $auth = Yii::$app->authManager;
        $auth->removeAll();
    }
}
