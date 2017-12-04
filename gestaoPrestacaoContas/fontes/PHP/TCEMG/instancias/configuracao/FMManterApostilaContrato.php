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
    * Formulário de Cadastro de Apostila de Contrato TCEMG
    * Data de Criação   : 06/05/2014
    
    * @author Analista      Silvia Martins Silva
    * @author Desenvolvedor Michel Teixeira
    
    * @package URBEM
    * @subpackage
    
    * @ignore
    
    $Id: FMManterApostilaContrato.php 62838 2015-06-26 13:02:49Z diogo.zarpelon $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GA_CGM_COMPONENTES.'IPopUpCGMVinculado.class.php';
include_once CAM_GPC_TCEMG_MAPEAMENTO.'TTCEMGContrato.class.php';

$stAcao = $_REQUEST['stAcao'];

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
$obHdnExercicioContrato->setValue( $_REQUEST['stExercicioContrato'] );

$obHdnNumContrato= new Hidden;
$obHdnNumContrato->setName( "inNumContrato" );
$obHdnNumContrato->setValue( $_REQUEST['inNumContrato'] );

$obHdnCodEntidadeContrato= new Hidden;
$obHdnCodEntidadeContrato->setName( "inCodEntidadeContrato" );
$obHdnCodEntidadeContrato->setValue( $_REQUEST['inCodEntidade'] );

$obHdnNumOrgao= new Hidden;
$obHdnNumOrgao->setName( "inNumOrgao" );

$obHdnNumUnidade= new Hidden;
$obHdnNumUnidade->setName( "inNumUnidade" );

$obHdnCodContrato= new Hidden;
$obHdnCodContrato->setName( "inCodContrato" );

//Consulta de Existencia do Contrato
$obTTCEMGContrato = new TTCEMGContrato;
$stFiltro  = "   WHERE contrato.exercicio = '".$_REQUEST['stExercicioContrato']."'";
$stFiltro .= "   AND contrato.nro_contrato = ".$_REQUEST['inNumContrato'];
$stFiltro .= "   AND contrato.cod_entidade = ".$_REQUEST['inCodEntidade'];

if($_REQUEST['stExercicioContrato']!=''&&$_REQUEST['inNumContrato']!=''&&$_REQUEST['inCodEntidade']!='')
    $obTTCEMGContrato->recuperaContrato($rsContratos, $stFiltro, $stOrder);

//Montando Valores do Contrato
if($rsContratos->inNumLinhas==1){
    //Preenche os Hiddens do contrato
    $obHdnNumOrgao->setValue( $rsContratos->arElementos[0]['num_orgao'] );
    $obHdnNumUnidade->setValue( $rsContratos->arElementos[0]['num_unidade'] );
    $obHdnCodContrato->setValue( $rsContratos->arElementos[0]['cod_contrato'] );
    
    //Valores de Contrato para os Labels
    $inNumContrato = $rsContratos->arElementos[0]['nro_contrato']."/".$rsContratos->arElementos[0]['exercicio'];
    $inCodEntidade = $rsContratos->arElementos[0]['nom_entidade'];
    $dtAssinatura = explode("-", $rsContratos->arElementos[0]['data_assinatura']);
    $dtAssinatura = $dtAssinatura[2]."/".$dtAssinatura[1]."/".$dtAssinatura[0];
    $stModalidadeLicit = $rsContratos->arElementos[0]['st_modalidade'];
    $stNatureza = $rsContratos->arElementos[0]['st_natureza'];
    $stObjeto = $rsContratos->arElementos[0]['objeto_contrato'];
    $stInstrumento = $rsContratos->arElementos[0]['st_instrumento'];
    $stPeriodoContrato = explode("-", $rsContratos->arElementos[0]['data_inicio'].'-'.$rsContratos->arElementos[0]['data_final']);
    $stPeriodoContrato = $stPeriodoContrato[2]."/".$stPeriodoContrato[1]."/".$stPeriodoContrato[0]." até ".$stPeriodoContrato[5]."/".$stPeriodoContrato[4]."/".$stPeriodoContrato[3];
    $vlContrato = number_format($rsContratos->arElementos[0]['vl_contrato'],2,',','.');
    
    //Monta Empenho de Contrato
    $arEmpenhos = array();
    
    include_once ( CAM_GPC_TCEMG_MAPEAMENTO.'TTCEMGContratoEmpenho.class.php' );
    $obTTCEMGContratoEmpenho = new TTCEMGContratoEmpenho;
    $stFiltro  = "   WHERE cod_contrato = '".$rsContratos->arElementos[0]['cod_contrato']."'";
    $stFiltro .= "   AND exercicio = '".$rsContratos->arElementos[0]['exercicio']."'";
    $stFiltro .= "   AND cod_entidade = ".$rsContratos->arElementos[0]['cod_entidade'];

    $obTTCEMGContratoEmpenho->recuperaTodos($rsContratoEmpenho, $stFiltro, $stOrder);
    
    include_once( CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenho.class.php" );
    while(!$rsContratoEmpenho->eof()){
        $obTEmpenhoEmpenho = new TEmpenhoEmpenho;
        $stFiltro  = "   AND e.exercicio    = '".$rsContratoEmpenho->getCampo('exercicio_empenho')."'";
        $stFiltro .= "   AND e.cod_entidade =  ".$rsContratoEmpenho->getCampo('cod_entidade');
        $stFiltro .= "   AND e.cod_empenho  =  ".$rsContratoEmpenho->getCampo('cod_empenho');
        $obTEmpenhoEmpenho->recuperaEmpenhoPreEmpenhoCgm($rsRecordSet, $stFiltro);
                
        if( $rsRecordSet->getNumLinhas() > 0 ){  
            $arRegistro['cod_entidade'] = $rsRecordSet->getCampo('cod_entidade'	);
            $arRegistro['cod_empenho' ] = $rsRecordSet->getCampo('cod_empenho'	);
            $arRegistro['data_empenho'] = $rsRecordSet->getCampo('dt_empenho'	);
            $arRegistro['nom_cgm'     ] = $rsRecordSet->getCampo('credor'       );
            $arRegistro['exercicio'   ] = $rsRecordSet->getCampo('exercicio'	);
            $arEmpenhos[] = $arRegistro ;
        }
        
        $rsContratoEmpenho->proximo();
    }
    Sessao::write('arEmpenhos', $arEmpenhos);
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
//Nro Sequencial da Apostila
$obTxtCodApostila = new TextBox;
$obTxtCodApostila->setTitle    ( "Informe o Número Sequencial da Apostila." );
$obTxtCodApostila->setId       ( "inCodApostila"        );
$obTxtCodApostila->setName     ( "inCodApostila"        );
$obTxtCodApostila->setRotulo   ( "Número da Apostila"   );
$obTxtCodApostila->setInteiro  ( true   );
$obTxtCodApostila->setNull     ( false  );
$obTxtCodApostila->setMaxLength( 3      );
$obTxtCodApostila->setSize     ( 3      );

//Tipo de Apostila
$tipoApostila = array (1=>"Reajuste de preço previsto no contrato",
                2=>"Atualizações, compensações ou penalizações financeiras decorrentes das condições de pagamento previstas no contrato",
                3=>"Empenho de dotações orçamentárias suplementares até o limite do seu valor corrigido");

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
$obCmbTipoApostila->setValue        ( $inCodTipoApostila    );
$obCmbTipoApostila->setStyle        ( "width: 500px"	    );
$obCmbTipoApostila->setCampoID      ( "cod_tipo"            );
$obCmbTipoApostila->setCampoDesc    ( "desc_tipo"           );
$obCmbTipoApostila->addOption  	    ( "", "Selecione"	    );
$obCmbTipoApostila->setNull         ( false                 );
$obCmbTipoApostila->preencheCombo   ( $rsTipoApostila       );

//Data Apostila
$obDtApostila = new Data;
$obDtApostila->setId    ( "dtApostila"                  );
$obDtApostila->setName  ( "dtApostila"                  );
$obDtApostila->setRotulo( "Data da Apostila"            );
$obDtApostila->setTitle ( 'Informe a Data da Apostila.' );
$obDtApostila->setNull  ( false );
$obDtApostila->setValue ( ''    );

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
$obCmbTipoAlteracaoApostila->setValue       ( $inCodTipoAlteracaoApostila       );
$obCmbTipoAlteracaoApostila->setStyle       ( "width: 500px"	                );
$obCmbTipoAlteracaoApostila->setCampoID     ( "cod_tipo"                        );
$obCmbTipoAlteracaoApostila->setCampoDesc   ( "desc_tipo"                       );
$obCmbTipoAlteracaoApostila->addOption      ( "", "Selecione"	                );
$obCmbTipoAlteracaoApostila->setNull        ( false                             );
$obCmbTipoAlteracaoApostila->preencheCombo  ( $rsTipoAlteracaoApostila          );
$obCmbTipoAlteracaoApostila->obEvento->setOnChange("montaParametrosGET('liberaValorApostila');");

//Descrição Apostila
$obTxtDscApostila = new TextArea;
$obTxtDscApostila->setName          ( "stDscApostila"           );
$obTxtDscApostila->setId            ( "stDscApostila"           );
$obTxtDscApostila->setRotulo        ( "Descrição da Apostila"   );
$obTxtDscApostila->setNull          ( false );
$obTxtDscApostila->setRows          ( 3     );
$obTxtDscApostila->setCols          ( 100   );
$obTxtDscApostila->setMaxCaracteres ( 250   );

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
$obTxtVlApostila->setValue      ( ''    );
/* Fim Apostilamento */

//define o formulário
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnCodApostila         );
$obFormulario->addHidden( $obHdnExercicioContrato   );
$obFormulario->addHidden( $obHdnCodContrato         );
$obFormulario->addHidden( $obHdnNumContrato         );
$obFormulario->addHidden( $obHdnCodEntidadeContrato );
$obFormulario->addHidden( $obHdnNumOrgao            );
$obFormulario->addHidden( $obHdnNumUnidade          );
$obFormulario->addTitulo    ( "Dados do Contrato" );
$obFormulario->addComponente( $obLblNumContrato                             );
$obFormulario->addComponente( $obLblCodEntidade                             );
$obFormulario->addComponente( $obLblDtAssinatura                            );
$obFormulario->addComponente( $obLblModalidadeLicit                         );
$obFormulario->addComponente( $obLblStNatureza                              );
$obFormulario->addComponente( $obLblStObjeto                                );
$obFormulario->addComponente( $obLblStInstrumento                           );
$obFormulario->addComponente( $obLblStPeriodo                               );
$obFormulario->addComponente( $obLblVlContrato                              );
$obFormulario->addTitulo    ( "Dados do Apostilamento"                      );
$obFormulario->addComponente( $obTxtCodApostila                             );
$obFormulario->addComponente( $obCmbTipoApostila                            );
$obFormulario->addComponente( $obDtApostila                                 );
$obFormulario->addComponente( $obCmbTipoAlteracaoApostila                   );
$obFormulario->addComponente( $obTxtDscApostila                             );
$obFormulario->addComponente( $obTxtVlApostila                              );

if ($stAcao == "incluir") {
    $obFormulario->Ok();
} else {
    $stFiltro  = "&pg=".Sessao::read('pg');
    $stFiltro .= "&pos=".Sessao::read('pos');
    $stLocation = $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao.$stFiltro;
    $obFormulario->Cancelar($stLocation);
}

$obFormulario->show();

if ($stAcao == 'alterar') {
    echo "<script type=\"text/javascript\">             \r\n";
    echo "    ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&inNumContrato=".$_REQUEST['inNumContrato']. "&inCodEntidade=".$_REQUEST['inCodEntidade']. "&stExercicioContrato=".$_REQUEST['stExercicioContrato']. "&inCodApostila=".$_REQUEST['inCodApostila'] . "&stExercicioApostila=".$_REQUEST['stExercicioApostila']. "', 'carregaLista');  \r\n";
    echo "</script>                                                             \r\n";
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
