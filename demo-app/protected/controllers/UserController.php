<?php

class UserController extends Controller
{
	public function actionIndex()
	{
		$this->render('index');
	}

	public function actionNewPhoto()
	{
		$this->render('newPhoto');
	}

	public function actionSaveJpg()
	{
		$this->render('saveJpg');
	}

    public function actions()
    {
        return array(
            'jpegcam.'=> array(
                'class'=>'application.extensions.jpegcam.EJpegcam',
                'saveJpg'=>array(
                    'filepath'=> Yii::app()->basePath . "/../uploads/user_photo.jpg" // This could be whatever
                )
            )
        );
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
	*/
}
