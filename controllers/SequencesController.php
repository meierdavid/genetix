<?php

namespace app\controllers;

use Yii;
use app\models\Sequences;
use app\models\SequencesSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\components\Logic;
use app\components\Veritas;
use app\components\VeritasLogic;
use app\components\MinimalDisjunctiveForm;
use app\components\Word;
use app\components\VeritasWord;
use app\components\Semantic;
use app\components\Bitset;
/**
 * SequencesController implements the CRUD actions for Sequences model.
 */
class SequencesController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Sequences models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SequencesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Sequences model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Sequences model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Sequences();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id_sequence]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Sequences model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id_sequence]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Sequences model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionResult(){
         $searchModel = new SequencesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        if(isset($_GET['fonction'])){
             try 
	    {
                 
		if (empty($_GET['fonction']))
		    throw new \exception('La fonction ne peut être vide');
		
		$fonction = str_replace('-', '+', urldecode($_GET['fonction']));
		
		$logic = new Logic($fonction);
		$veritas = new VeritasLogic($logic);
		            
		setcookie ("fonction", $fonction, time() + 365*24*3600);
		
		    return $this->render('result', [
                            'searchModel' => $searchModel,
                            'dataProvider' => $dataProvider,
                            'logic' => $logic,
                            'veritas' => $veritas,
                ]);
            }
	   catch (\Exception $e)
	    {
                    return $this->render('erreur', [
                            'searchModel' => $searchModel,
                            'dataProvider' => $dataProvider,
                            'erreur' => $e->getMessage(),
                            
                ]);
            }
        }
       
        
    }
      public function actionInfo()
    {
        $searchModel = new SequencesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('phpinfo', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    
    public function actionSearch_seq_treatment(){
        //faire requete
        if (isset($_GET['fonctionSearch'])) 
	{
	    try 
	    {
		if (empty($_GET['fonctionSearch']))
		    throw new \exception(t('La séquence ne peut être vide'));
		
		$fonction = str_replace('-', '+', urldecode($_GET['fonctionSearch']));
		
		$logic = new Logic($fonction);
		$veritas = new VeritasLogic($logic);
		
		setcookie ("fonction", $fonction, time() + 365*24*3600);
		
		//$wordsManager = new WordsManager($bdd);
		//$logicManager = new LogicManager($bdd);
 		
		/*$pagination = new Pagination(30, $wordsManager->getNombre($logicManager->getLogic(
		    ['ndf',bindec($veritas->getMinimalOutput ()),DB::SQL_AND,'nb_inputs',$veritas->getMinimalNbInputs()])->getId_fn()), 
		    'listSeq.php?output='.$veritas->getMinimalOutput ()."&amp;nbInputs=".$veritas->getMinimalNbInputs());
		if (isset($_GET['page'])) $pagination->setPageActuelle($_GET['page']);
		$pagination->setPremier(false);*/
		
                //Requête SQL pour récuperer la liste des séquences
                // Select all from Sequences Where  'ndf' = bindec($veritas->getMinimalOutput() and 
                //'nb_inputs' = $veritas->getMinimalNbInputs() 
                // orderby weak_constraint DESC, length ASC
		$liste =  $wordsManager->getListe($pagination, 
		    ['ndf',bindec($veritas->getMinimalOutput ()),DB::SQL_AND,'nb_inputs',$veritas->getMinimalNbInputs()], 
		    array(['champ' => 'weak_constraint', 'sens' => DB::ORDRE_DESC], ['champ' => 'length', 'sens' => DB::ORDRE_ASC]));
		    
		/*$tpl->assign(array(
			'listeSequences' => $liste,
			'pages' => $pagination->getPages()));
		
		$tpl->display('listSeq.html'); */
                
                 return $this->render('result', [
                            'searchModel' => $searchModel,
                            'dataProvider' => $dataProvider,
                            'logic' => $logic,
                            'veritas' => $veritas,
                ]);
	    }
	    catch (\Exception $e)
	    {
                return $this->render('erreur', [
                            'searchModel' => $searchModel,
                            'dataProvider' => $dataProvider,
                            'erreur' => $e->getMessage(),
                            
                ]);
	    }
	}
        
       
        
        
    }
    // -----------------------------------SearchSeq
    // recupère la variable proposition. 
    // proposition : fonction qui implémente les sequences qu'on cherche
    // Logic / Logic Manager / VeritasLogic / WordsManager dans Includes
    public function actionSearch_seq() {
        $model = new Sequences();
        
            if (isset($_POST['Sequences']['proposition'])) {
                
                // requete SQL A VOIR AVEC GUIGUI
                
                return $this->render('listeResult', [
                        'model' => $model,
                 ]);
           
        } else {
            return $this->render('searchSeq', [
                        'model' => $model,
            ]);
        }
    }
    
    public function actionInter_seq_res() {
        if (isset($_GET['sequence'])) 
	{
	    try 
	    {
		if (empty($_GET['sequence']))
		    throw new \exception(t('La séquence ne peut être vide'));
		
		$word = new Word(urldecode($_GET['sequence']));
		$word->exceptionsIfInvalid();
		
		setcookie ("sequence", urldecode($_GET['sequence']), time() + 365*24*3600);
		$veritas = new VeritasWord($word);
			
		return $this->render('detailView', [
                            'word' => $word,
                            'veritas' => $veritas,
                ]);
	    }
	    catch (\Exception $e)
	    {
		
                return $this->render('erreur', [
                            
                            'erreur' => $e->getMessage(),
                            
                ]);
	    }
	}
	else {
            if (isset($_COOKIE['sequence'])) {
                
                $sequence = $_COOKIE['sequence'];
                return $this->render('detailView', [
                            
                            'sequence' => $sequence,
                ]);
            } else
                return $this->render('detailView', [
                        
                            'sequence' => '',
                ]);
        }
    }

    //Interpreter des Sequences
    public function actionInter_seq() {
        $model = new Sequences();
        
            if (isset($_POST['Sequences']['proposition'])) {
                
                // requete SQL A VOIR AVEC GUIGUI
                
                return $this->render('listeResult', [
                        'model' => $model,
                 ]);
            
                

           
        } else {
            return $this->render('interSeq', [
                        'model' => $model,
            ]);
        }
    }
    
    
    
    /**
     * Finds the Sequences model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Sequences the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Sequences::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}




/*requête word manager : "SELECT s.id_s, sequence, weak_constraint, strong_constraint, length, word, names
			    FROM sequences s
			    JOIN implements i ON i.id_s=s.id_s
			    JOIN logical_functions lf ON lf.id_lf=i.id_lf 
			    JOIN sequences_features sf ON sf.id_sf=s.id_sf  
			    JOIN dyck_words dw ON dw.id_dw=s.id_dw
			    JOIN namings n ON n.id_n=i.id_n
			    "*/