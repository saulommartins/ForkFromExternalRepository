<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/*
    * Formulário de Cadastro de Apostila de Contrato
    * Data de Criação   : 25/02/2016
    
    * @author Analista:      Gelson W. Gonçalves  <gelson.goncalves@cnm.org.br>
    * @author Desenvolvedor: Carlos Adriano       <carlos.silva@cnm.org.br>
    
    * @package URBEM
    * @subpackage
    
    * @ignore
    
    $Id: FMManterApostilaContrato.php 64923 2016-04-13 17:45:44Z jean $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GA_CGM_COMPONENTES.'IPopUpCGMVinculado.class.php';
include_once TLIC."TLicitacaoContratoApostila.class.php";

$stAcao = $request->get('stAcao');

$stPrograma = "ManterApostilaContrato";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$obForm = new Form;
$obForm->setAction(  $pgProc );
$obForm->setTarget( "oculto" );

//Hidden's
$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnCodApostila= new Hidden;
$obHdnCodApostila->setName( "inCodApostilaAtual" );
$obHdnCodApostila->setValue( '' );

$obHdnExercicioContrato= new Hidden;
$obHdnExercicioContrato->setName( "stExercicioContrato" );
$obHdnExercicioContrato->setValue( $request->get('stExercicioContrato') );

$obHdnNumContrato= new Hidden;
$obHdnNumContrato->setName( "inNumContrato" );
$obHdnNumContrato->setValue( $request->get('inNumContrato') );

$obHdnCodEntidadeContrato= new Hidden;
$obHdnCodEntidadeContrato->setName( "inCodEntidadeContrato" );
$obHdnCodEntidadeContrato->setValue( $request->get('inCodEntidade') );

$obHdnNumOrgao= new Hidden;
$obHdnNumOrgao->setName( "inNumOrgao" );

$obHdnNumUnidade= new Hidden;
$obHdnNumUnidade->setName( "inNumUnidade" );

//Consulta de Existencia do Contrato
$obTLicitacaoContratoApostila = new TLicitacaoContratoApostila;
$stFiltro  = " AND contrato.exercicio = '".$request->get('stExercicioContrato')."'";
$stFiltro .= " AND contrato.num_contrato = ".$request->get('inNumContrato');
$stFiltro .= " AND contrato.cod_entidade = ".$request->get('inCodEntidade');

if($request->get('stExercicioContrato') != '' && $request->get('inNumContrato') != '' && $request->get('inCodEntidade') != '') {
    $obTLicitacaoContratoApostila->recuperaDadosContrato($rsContratos, $stFiltro, $stOrder);
}
    
//Montando Valores do Contrato
if($rsContratos->getNumLinhas() == 1){
    //Preenche os Hiddens do contrato
    $obHdnNumOrgao->setValue( $rsContratos->arElementos[0]['num_orgao'] );
    $obHdnNumUnidade->setValue( $rsContratos->arElementos[0]['num_unidade'] );
    
    //Valores de Contrato para os Labels
    $inNumContrato = $rsContratos->arElementos[0]['num_contrato']."/".$rsContratos->arElementos[0]['exercicio'];
    $inCodEntidade = $rsContratos->arElementos[0]['cod_entidade']." - ".$rsContratos->arElementos[0]['nom_cgm'];
    $dtAssinatura = $rsContratos->arElementos[0]['dt_assinatura'];
    $stModalidadeLicit = $rsContratos->arElementos[0]['modalidade'];
    $stNatureza = $rsContratos->arElementos[0]['st_natureza'];
    $stObjeto = $rsContratos->arElementos[0]['objeto'];
    $stInstrumento = $rsContratos->arElementos[0]['instrumento'];
    $stPeriodoContrato = $rsContratos->arElementos[0]['inicio_execucao']." até ".$rsContratos->arElementos[0]['fim_execucao'];
    $vlContrato = number_format($rsContratos->arElementos[0]['valor_contratado'],2,',','.');
    
    //Monta Empenho de Contrato
    $arEmpenhos = array();
}

/* Informações do Contrato */
$obLblNumContrato = new Label;
$obLblNumContrato->setRotulo    ( "Número do Contrato"      );
$obLblNumContrato->setId        ( "inNumContrato"           );
$obLblNumContrato->setValue     ( $inNumContrato            );

$obLblCodEntidade = new Label;
$obLblCodEntidade->setRotulo    ( "Entidade"                );
$obLblCodEntidade->setId        ( "stEntidade"              );
$obLblCodEntidade->setValue     ( $inCodEntidade            );

$obLblDtAssinatura = new Label;
$obLblDtAssinatura->setRotulo   ( "Data de Assinatura"      );
$obLblDtAssinatura->setId       ( "dtAssinatura"            );
$obLblDtAssinatura->setValue    ( $dtAssinatura             );

$obLblModalidadeLicit = new Label;
$obLblModalidadeLicit->setRotulo( "Modalidade de Licitação" );
$obLblModalidadeLicit->setId    ( "stModalidadeLicit"       );
$obLblModalidadeLicit->setValue ( $stModalidadeLicit        );

$obLblStNatureza = new Label;
$obLblStNatureza->setRotulo     ( "Natureza do Objeto"      );
$obLblStNatureza->setId         ( "stNatureza"              );
$obLblStNatureza->setValue      ( $stNatureza               );

$obLblStObjeto = new Label;
$obLblStObjeto->setRotulo       ( "Objeto do Contrato"      );
$obLblStObjeto->setId           ( "stObjeto"                );
$obLblStObjeto->setValue        ( $stObjeto                 );

$obLblStInstrumento = new Label;
$obLblStInstrumento->setRotulo  ( "Tipo de Instrumento"     );
$obLblStInstrumento->setId      ( "stInstrumento"           );
$obLblStInstrumento->setValue   ( $stInstrumento            );

$obLblStPeriodo = new Label;
$obLblStPeriodo->setRotulo      ("Período do Contrato"      );
$obLblStPeriodo->setId          ("stPeriodoContrato"        );
$obLblStPeriodo->setValue       ($stPeriodoContrato         );

$obLblVlContrato = new Label;
$obLblVlContrato->setRotulo     ( "Valor do Contrato"       );
$obLblVlContrato->setId         ( "vlContrato"              );
$obLblVlContrato->setValue      ( $vlContrato               );
/* Fim das Informações do Contrato */

/* Início Apostilamento */
$obTLicitacaoContratoApostila = new TLicitacaoContratoApostila;
$obTLicitacaoContratoApostila->setDado('cod_apostila', $request->get('inCodApostila'));
$obTLicitacaoContratoApostila->setDado('num_contrato', $request->get('inNumContrato'));
$obTLicitacaoContratoApostila->setDado('cod_entidade', $request->get('inCodEntidade'));
$obTLicitacaoContratoApostila->setDado('exercicio'   , $request->get('stExercicioApostila'));
$obTLicitacaoContratoApostila->recuperaPorChave($recordSet);

if ($stAcao == "alterar") {
    $valorApostila = SistemaLegado::formataValorDecimal($recordSet->getCampo('valor_apostila'),true);
} else {
    $valorApostila = "";
}

//Nro Sequencial da Apostila
$obTxtCodApostila = new TextBox;
$obTxtCodApostila->setTitle    ( "Informe o Número Sequencial da Apostila." );
$obTxtCodApostila->setId       ( "inCodApostila"        );
$obTxtCodApostila->setName     ( "inCodApostila"        );
$obTxtCodApostila->setRotulo   ( "Número da Apostila"   );
$obTxtCodApostila->setInteiro  ( true  );
$obTxtCodApostila->setNull     ( false );
$obTxtCodApostila->setMaxLength( 3     );
$obTxtCodApostila->setSize     ( 3     );
$obTxtCodApostila->setValue    ( $recordSet->getCampo('cod_apostila') );

//Nro Sequencial da Apostila
$obHdnCodApostila = new Hidden;
$obHdnCodApostila->setId    ( "inHdnCodApostila"        );
$obHdnCodApostila->setName  ( "inHdnCodApostila"        );
$obHdnCodApostila->setValue ( $recordSet->getCampo('cod_apostila') );

//Tipo de Apostila
$tipoApostila = array (
                    1 => "Reajuste de preço previsto no contrato",
                    2 => "Atualizações, compensações ou penalizações financeiras decorrentes das condições de pagamento previstas no contrato",
                    3 => "Empenho de dotações orçamentárias suplementares até o limite do seu valor corrigido"
                );

for($i=0;$i<3;$i++){
    $arTipoApostila[$i]['desc_tipo']= $tipoApostila[($i+1)];
    $arTipoApostila[$i]['cod_tipo'] = ($i+1);
}

$rsTipoApostila = new RecordSet;
$rsTipoApostila->preenche ( $arTipoApostila );

$obCmbTipoApostila= new Select;
$obCmbTipoApostila->setRotulo       ( "Tipo de Apostila"    );
$obCmbTipoApostila->setName         ( "inCodTipoApostila"   );
$obCmbTipoApostila->setId           ( "inCodTipoApostila"   );
$obCmbTipoApostila->setStyle        ( "width: 500px"	    );
$obCmbTipoApostila->setCampoID      ( "cod_tipo"            );
$obCmbTipoApostila->setCampoDesc    ( "desc_tipo"           );
$obCmbTipoApostila->addOption  	    ( "", "Selecione"	    );
$obCmbTipoApostila->setNull         ( false                 );
$obCmbTipoApostila->preencheCombo   ( $rsTipoApostila       );
$obCmbTipoApostila->setValue        ( $recordSet->getCampo('cod_tipo') );

//Data Apostila
$obDtApostila = new Data;
$obDtApostila->setId    ( "dtApostila"                  );
$obDtApostila->setName  ( "dtApostila"                  );
$obDtApostila->setRotulo( "Data da Apostila"            );
$obDtApostila->setTitle ( 'Informe a Data da Apostila.' );
$obDtApostila->setNull  ( false );
$obDtApostila->setValue ( ''    );
$obDtApostila->setValue ( $recordSet->getCampo('data_apostila') );

//Tipo de Alteração da Apostila
$tipoAlteracaoApostila = array (1=>"Acréscimo de valor", 2=>"Decréscimo de valor", 3=>"Não houve alteração de valor");

for($i=0;$i<3;$i++){
    $arTipoAlteracaoApostila[$i]['desc_tipo']= $tipoAlteracaoApostila[($i+1)];
    $arTipoAlteracaoApostila[$i]['cod_tipo'] = ($i+1);
}

$rsTipoAlteracaoApostila = new RecordSet;
$rsTipoAlteracaoApostila->preenche ( $arTipoAlteracaoApostila );

$obCmbTipoAlteracaoApostila= new Select;
$obCmbTipoAlteracaoApostila->setRotulo      ( "Tipo de Alteração da Apostila"   );
$obCmbTipoAlteracaoApostila->setName        ( "inCodTipoAlteracaoApostila"      );
$obCmbTipoAlteracaoApostila->setId          ( "inCodTipoAlteracaoApostila"      );
$obCmbTipoAlteracaoApostila->setStyle       ( "width: 500px"	                );
$obCmbTipoAlteracaoApostila->setCampoID     ( "cod_tipo"                        );
$obCmbTipoAlteracaoApostila->setCampoDesc   ( "desc_tipo"                       );
$obCmbTipoAlteracaoApostila->addOption      ( "", "Selecione"	                );
$obCmbTipoAlteracaoApostila->setNull        ( false                             );
$obCmbTipoAlteracaoApostila->preencheCombo  ( $rsTipoAlteracaoApostila          );
$obCmbTipoAlteracaoApostila->obEvento->setOnChange("montaParametrosGET('liberaValorApostila');");
$obCmbTipoAlteracaoApostila->setValue       ( $recordSet->getCampo('cod_alteracao') );

//Descrição Apostila
$obTxtDscApostila = new TextArea;
$obTxtDscApostila->setName          ( "stDscApostila"           );
$obTxtDscApostila->setId            ( "stDscApostila"           );
$obTxtDscApostila->setRotulo        ( "Descrição da Apostila"   );
$obTxtDscApostila->setNull          ( false );
$obTxtDscApostila->setRows          ( 3     );
$obTxtDscApostila->setCols          ( 100   );
$obTxtDscApostila->setValue         ( $recordSet->getCampo('descricao')   );

//Valor Apostila
$obTxtVlApostila = new Moeda;
$obTxtVlApostila->setTitle      ( 'Informe o Valor da Apostila.');
$obTxtVlApostila->setName       ( "nuVlApostila"                );
$obTxtVlApostila->setId         ( "nuVlApostila"                );
$obTxtVlApostila->setRotulo     ( "Valor da Apostila"           );
$obTxtVlApostila->setAlign      ( 'RIGHT'                       );
$obTxtVlApostila->setTitle      ( ""    );
$obTxtVlApostila->setMaxLength  ( 19    );
$obTxtVlApostila->setSize       ( 21    );
$obTxtVlApostila->setNull       ( false );
$obTxtVlApostila->setValue      ( $valorApostila );
/* Fim Apostilamento */

//define o formulário
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnCodApostila         );
$obFormulario->addHidden( $obHdnExercicioContrato   );
$obFormulario->addHidden( $obHdnNumContrato         );
$obFormulario->addHidden( $obHdnCodEntidadeContrato );
$obFormulario->addHidden( $obHdnNumOrgao            );
$obFormulario->addHidden( $obHdnNumUnidade          );
$obFormulario->addTitulo    ( "Dados do Contrato" );
$obFormulario->addComponente( $obLblNumContrato           );
$obFormulario->addComponente( $obLblCodEntidade           );
$obFormulario->addComponente( $obLblDtAssinatura          );
$obFormulario->addComponente( $obLblModalidadeLicit       );
$obFormulario->addComponente( $obLblStNatureza            );
$obFormulario->addComponente( $obLblStObjeto              );
$obFormulario->addComponente( $obLblStInstrumento         );
$obFormulario->addComponente( $obLblStPeriodo             );
$obFormulario->addComponente( $obLblVlContrato            );
$obFormulario->addTitulo    ( "Dados do Apostilamento"    );
$obFormulario->addHidden    ( $obHdnCodApostila           );
$obFormulario->addComponente( $obTxtCodApostila           );
$obFormulario->addComponente( $obCmbTipoApostila          );
$obFormulario->addComponente( $obDtApostila               );
$obFormulario->addComponente( $obCmbTipoAlteracaoApostila );
$obFormulario->addComponente( $obTxtDscApostila           );
$obFormulario->addComponente( $obTxtVlApostila            );

if ($stAcao == "incluir") {
    $obFormulario->Ok();
} else {
    $stFiltro  = "&pg=".Sessao::read('pg');
    $stFiltro .= "&pos=".Sessao::read('pos');
    $stLocation = $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao.$stFiltro;
    $obFormulario->Cancelar($stLocation);
}

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';