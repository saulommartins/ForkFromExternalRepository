<?php

ini_set("display_errors", 1);
error_reporting(E_ALL);

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once ( CAM_GF_ORC_COMPONENTES."ISelectMultiploEntidadeUsuario.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ExportarArquivosDCASP" ;
$pgFilt     = "FL".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";

$stCtrl = $request->get('stCtrl');
$stAcao = $request->get('stAcao');

//Instancia o formulário
$obForm = new Form;
$obForm->setAction( "PRExportador.php" );
$obForm->setTarget( "telaPrincipal" );

//Define o objeto que ira armazenar o nome da pagina oculta
$obHdnPaginaExportacao = new Hidden;
$obHdnPaginaExportacao->setName ('hdnPaginaExportacao');
$obHdnPaginaExportacao->setValue("../exportacao/".$pgOcul);

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( $stCtrl );

$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

/* ComboBox dos entidades */
$obISelectMultiploEntidadeUsuario = new ISelectMultiploEntidadeUsuario();

/* ComboBox dos meses */
$obMes = new Mes();
$obMes->setNull(false);

/* Radio para selecionar tipo de exportacao*/
/* Tipo Arquivo Individual */
$obRdbTipoExportArqIndividual = new Radio;
$obRdbTipoExportArqIndividual->setName   ( "stTipoExport"         );
$obRdbTipoExportArqIndividual->setLabel  ( "Arquivos Individuais" );
$obRdbTipoExportArqIndividual->setValue  ( "individuais"          );
$obRdbTipoExportArqIndividual->setRotulo ( "*Tipo de Exportação"  );
$obRdbTipoExportArqIndividual->setTitle  ( "Tipo de Exportação"   );
$obRdbTipoExportArqIndividual->setChecked(true                    );
/* Tipo Arquivo Compactado */
$obRdbTipoExportArqCompactado = new Radio;
$obRdbTipoExportArqCompactado->setName  ( "stTipoExport" );
$obRdbTipoExportArqCompactado->setLabel ( "Compactados"  );
$obRdbTipoExportArqCompactado->setValue ( "compactados"  );


$arNomeArquivos = array(
    'IDE.csv',
    'BO.csv',
    'BF.csv',
    'BP.csv',
    'DVP.csv',
    'DFC.csv',
    'RPSD.csv',
);


// Preenche array
for ($inCounter=0;$inCounter < count($arNomeArquivos);$inCounter++) {
    $arElementosArq[$inCounter]['Arquivo']   = $arNomeArquivos[$inCounter];
    $arElementosArq[$inCounter]['Nome'   ]   = $arNomeArquivos[$inCounter];
}

$rsArqSelecionados = new RecordSet;
$rsArqDisponiveis  = new RecordSet;
$rsArqDisponiveis->preenche($arElementosArq);

$obCmbArquivos = new SelectMultiplo();
$obCmbArquivos->setName  ( 'arArquivosSelecionados' );
$obCmbArquivos->setRotulo( "Arquivos" );
$obCmbArquivos->setNull  ( false );
$obCmbArquivos->setTitle ( 'Arquivos Disponiveis' );

// lista de ARQUIVOS disponiveis
$obCmbArquivos->SetNomeLista1( 'arCodArqDisponiveis' );
$obCmbArquivos->setCampoId1  ( 'Arquivo' );
$obCmbArquivos->setCampoDesc1( 'Nome' );
$obCmbArquivos->SetRecord1   ( $rsArqDisponiveis   );

// lista de ARQUIVOS selecionados
$obCmbArquivos->SetNomeLista2( 'arArquivosSelecionados' );
$obCmbArquivos->setCampoId2  ( 'Arquivo' );
$obCmbArquivos->setCampoDesc2( 'Nome' );
$obCmbArquivos->SetRecord2   ( $rsArqSelecionados );

$obLbExercicio = new Label();
$obLbExercicio->setName  ( 'stExercicio' );
$obLbExercicio->setRotulo( 'Exercício' );
$obLbExercicio->setTitle ( 'Exercício' );
$obLbExercicio->setValue ( '/ '.Sessao::getExercicio());

$obFormulario = new Formulario;
$obFormulario->addForm($obForm);
$obFormulario->addTitulo("Dados para geração de arquivos");
$obFormulario->addHidden($obHdnCtrl);
$obFormulario->addHidden($obHdnAcao);
$obFormulario->addHidden($obHdnPaginaExportacao);
$obFormulario->addComponente($obISelectMultiploEntidadeUsuario);
$obFormulario->agrupaComponentes(array($obRdbTipoExportArqIndividual,$obRdbTipoExportArqCompactado));
$obFormulario->addComponente($obCmbArquivos);
// $obFormulario->addComponente($obMes);
$obFormulario->addComponenteComposto($obMes, $obLbExercicio);

$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
