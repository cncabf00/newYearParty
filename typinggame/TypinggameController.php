<?php

class TypinggameController extends Controller
{
				const k1=1;
			const k2=-14.285714;
			const k22=-20;
			const k3=2;
			const k4=-381.08;
			const k42=-320;
			const k5=71.096601922449;
			const k52=115.8;
	public $layout='//layouts/column2';


	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}

	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('rank'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('game','getString'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
                'actions'=>array('admin','delete'),
                'expression'=>'$user->isAdmin()',
            ),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionGame()
	{
		$this->render('game');
	}

	public function actionRank()
	{
		$model=new TypingGame('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['TypingGame']))
			$model->attributes=$_GET['TypingGame'];
		
		$this->render('rank',array(
				'model'=>$model,
		));
	}

	public function getMillisecond() {
		list($s1, $s2) = explode(' ', microtime());		
		return (float)sprintf('%.0f', (floatval($s1) + floatval($s2)) * 1000);	
	}

	public function actionGetString()
	{
		if (Yii::app ()->request->isPostRequest) {
			$seedarray =microtime(); 
			$seedstr =explode(" ",$seedarray,5); 
			$seed =$seedstr[0]*10000; 

			//第二步:使用种子初始化随机数发生器 
			srand($seed); 
			if ($_POST['round']==0)
			{
				$_SESSION['total']=0;
				$_SESSION['current']=0;
				$round=0;
				for ($i=0;$i<=5;$i++)
				{
					unset($_SESSION["round"][$i]);
				}
			}
			else if (isset($_SESSION['current']) && $_SESSION['current']>=0 && $_SESSION['current']<=5)
			{
				$round=$_SESSION['current'];
			}
			else
			{
				$_SESSION['total']=0;
				$_SESSION['current']=0;
				$round=0;
				for ($i=0;$i<=5;$i++)
				{
					unset($_SESSION["round"][$i]);
				}
			}
			$error = $_POST['e'];
			$time = $_POST['t'];
			$k = $_POST['k'];
			$p=$_POST['p'];
			$q=$_POST['q'];

			

			if (isset($_SESSION["round"][$round]))
			{
				unset($_SESSION['current']);
				for ($i=0;$i<=5;$i++)
				{
					unset($_SESSION["round"][$i]);
				}
				throw new CHttpException (403, "好孩子请不要作弊哟~");
			}
			$_SESSION["round"][$round] = $this->getMillisecond();
			$_SESSION["error"][$round] = $error;
			$_SESSION["t"][$round] = $time;
			$newtime = strval($time + $error*0.3);
			if ($round==0)
			{
				$_SESSION['time']=0.0;
				for ($i=1;$i<=5;$i++)
				{
					unset($_SESSION["round"][$i]);
				}
			}
			else
			{
				$_SESSION['time']=$_SESSION["round"][$round]-$_SESSION["round"][0];
			}
			

			if( $round < 0 || $round > 5
			 || $error < 0 
			 || $time < 0)
			{
				unset($_SESSION['current']);
				for ($i=0;$i<=5;$i++)
				{
					unset($_SESSION["round"][$i]);
				}
				throw new CHttpException (403, "好孩子请不要作弊哟~");
			}

			

			if($round > 0)
			{
				if ($p<8.8)
					$roundTime=(self::k1*$p*$p+self::k2*$p+(self::k3*$q*$q+self::k4*$q)/1000+self::k5)/5;
				else
					$roundTime=(self::k1*$p*$p+self::k22*$p+(self::k3*$q*$q+self::k42*$q)/1000+self::k52)/5;
				$_SESSION['total']+=$roundTime;

				if( $_SESSION["error"][$round] < $_SESSION["error"][$round - 1] 
				 // || $_SESSION["time"] > ($time*1.5+$round+1)*1000
				 || $_SESSION["time"] < $time*1000
				 || $_SESSION["t"][$round] - $_SESSION["t"][$round - 1] < $roundTime
				 || !isset($_SESSION["round"][$round - 1])
				 || $_SESSION["key"] != $k
				)
				{
					unset($_SESSION['current']);
					for ($i=0;$i<=5;$i++)
					{
						unset($_SESSION["round"][$i]);
					}
					throw new CHttpException (403, "好孩子请不要作弊哟~");
					// header("HTTP/1.1 403 Forbidden");
					// error_log("Error 2");
					// die("Error 2");
				}
			}

			if($round==5)
			{
				unset($_SESSION['current']);
				for ($i=0;$i<=5;$i++)
				{
					unset($_SESSION["round"][$i]);
				}
				if ($newtime<1.92345+rand(0,0.1) || $newtime<$_SESSION['total'])
				{
					throw new CHttpException (403, "好孩子请不要作弊哟~");
				}
				if ($newtime<5)
				{
					$newtime+=rand(0,0.00011);
				}
				$model = new TypingGame;
				if (Yii::app()->user->isGuest)
				{
					$model->user=1;
				}
				else
				{
					$model->user=Yii::app()->user->id;
				}
				$model->date=new CDbExpression ( 'NOW()' );
				$model->score=$newtime;
				$model->save();
				echo json_encode(array($model->player->bestScore->score, $model->ranking()));
				die();
			}

			$a = array();
			for($i=0; $i<10; $i++)
			{
				if ( rand(1,36) <= 10 )
					$n = rand(48,57);
				else
					$n = rand(65,90);
				$c = chr($n);
				$a[$i] = $c;
			}
			$a[10] = rand(1,1000);
			for ($i=0;$i<10;$i++)
			{
				$k=($k<<1)%($k+1);
			}
			$_SESSION["key"] = $a[10]*$a[10]%($k+65536);
			echo json_encode($a);
			$_SESSION['current']=$_SESSION['current']+1;
		}
		else
			throw new CHttpException ( 400, 'Invalid request. Please do not repeat this request again.' );
	}

    /**
     * Displays a particular model.
     * @param integer $id the ID of the model to be displayed
     */
    public function actionView($id)
    {
        $this->render('view',array(
            'model'=>$this->loadModel($id),
        ));
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate()
    {
        $model=new TypingGame;

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if(isset($_POST['TypingGame']))
        {
            $model->attributes=$_POST['TypingGame'];
            if($model->save())
                $this->redirect(array('view','id'=>$model->id));
        }

        $this->render('create',array(
            'model'=>$model,
        ));
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id the ID of the model to be updated
     */
    public function actionUpdate($id)
    {
        $model=$this->loadModel($id);

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if(isset($_POST['TypingGame']))
        {
            $model->attributes=$_POST['TypingGame'];
            if($model->save())
                $this->redirect(array('view','id'=>$model->id));
        }

        $this->render('update',array(
            'model'=>$model,
        ));
    }

    /**
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'admin' page.
     * @param integer $id the ID of the model to be deleted
     */
    public function actionDelete($id)
    {
        if(Yii::app()->request->isPostRequest)
        {
            // we only allow deletion via POST request
            $this->loadModel($id)->delete();

            // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
            if(!isset($_GET['ajax']))
                $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
        }
        else
            throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
    }

    /**
     * Lists all models.
     */
    public function actionIndex()
    {
        $dataProvider=new CActiveDataProvider('TypingGame');
        $this->render('index',array(
            'dataProvider'=>$dataProvider,
        ));
    }

    /**
     * Manages all models.
     */
    public function actionAdmin()
    {
        $model=new TypingGame('search');
        $model->unsetAttributes();  // clear any default values
        if(isset($_GET['TypingGame']))
            $model->attributes=$_GET['TypingGame'];

        $this->render('admin',array(
            'model'=>$model,
        ));
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    public function loadModel($id)
    {
        $model=TypingGame::model()->findByPk($id);
        if($model===null)
            throw new CHttpException(404,'The requested page does not exist.');
        return $model;
    }

    /**
     * Performs the AJAX validation.
     * @param CModel the model to be validated
     */
    protected function performAjaxValidation($model)
    {
        if(isset($_POST['ajax']) && $_POST['ajax']==='typing-game-form')
        {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

	
	// Uncomment the following methods and override them if needed
	/*
	public function filters()
	{
		// return the filter configuration for this controller, e.g.:
		return array(
			'inlineFilterName',
			array(
				'class'=>'path.to.FilterClass',
				'propertyName'=>'propertyValue',
			),
		);
	}

	public function actions()
	{
		// return external action classes, e.g.:
		return array(
			'action1'=>'path.to.ActionClass',
			'action2'=>array(
				'class'=>'path.to.AnotherActionClass',
				'propertyName'=>'propertyValue',
			),
		);
	}
	*/
}