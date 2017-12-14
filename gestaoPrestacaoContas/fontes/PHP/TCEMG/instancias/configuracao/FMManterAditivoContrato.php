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
    * Formulário de Cadastro de Aditivo de Contrato TCEMG
    * Data de Criação   : 30/04/2014
    
    * @author Analista      Silvia Martins Silva
    * @author Desenvolvedor Michel Teixeira
    
    * @package URBEM
    * @subpackage
    
    * @ignore
    
    $Id: FMManterAditivoContrato.php 62838 2015-06-26 13:02:49Z diogo.zarpelon $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GA_CGM_COMPONENTES.'IPopUpCGMVinculado.class.php';
include_once CAM_GPC_TCEMG_MAPEAMENTO.'TTCEMGContrato.class.php';

$stAcao = $_REQUEST['stAcao'];

$stPrograma = "ManterAditivoContrato";
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

$obHdnCodAditivo= new Hidden;
$obHdnCodAditivo->setName( "inCodAditivo" );
$obHdnCodAditivo->setValue( '' );

$obHdnExercicioAditivo= new Hidden;
$obHdnExercicioAditivo->setName( "stExercicioAditivoAtual" );
$obHdnExercicioAditivo->setValue( '' );

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
$stFiltro .= "   AND contrato.cod_objeto >= 1 ";
$stFiltro .= "   AND contrato.cod_objeto <= 3 ";
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

/* Aditivo */
//Exercicio
$obTxtExercicioAditivo = new TextBox;
$obTxtExercicioAditivo->setName     ( "stExercicioAditivo"              );
$obTxtExercicioAditivo->setValue    ( $stExercicioContrato              );
$obTxtExercicioAditivo->setRotulo   ( "Exercício do Aditivo"            );
$obTxtExercicioAditivo->setTitle    ( "Informe o exercício do aditivo." );
$obTxtExercicioAditivo->setInteiro  ( false                             );
$obTxtExercicioAditivo->setNull     ( true                              );
$obTxtExercicioAditivo->setMaxLength( 4                                 );
$obTxtExercicioAditivo->setSize     ( 5                                 );
$obTxtExercicioAditivo->setValue    ( Sessao::getExercicio()            );
$obTxtExercicioAditivo->setNull     (false);

//Número do Aditivo
$obTxtCodAditivo = new TextBox;
$obTxtCodAditivo->setId       ( "inNumAditivo"      );
$obTxtCodAditivo->setName     ( "inNumAditivo"      );
$obTxtCodAditivo->setRotulo   ( "Número do Aditivo" );
$obTxtCodAditivo->setTitle    ( "Número do Aditivo.");
$obTxtCodAditivo->setInteiro  ( true    );
$obTxtCodAditivo->setNull     ( true    );
$obTxtCodAditivo->setMaxLength( 2       );
$obTxtCodAditivo->setSize     ( 3       );
$obTxtCodAditivo->setNull     ( false   );

//Data da Assinatura
$obDtAssinaturaAditivo = new Data;
$obDtAssinaturaAditivo->setId       ( "dtAssinaturaAditivo"                     );
$obDtAssinaturaAditivo->setName     ( "dtAssinaturaAditivo"                     );
$obDtAssinaturaAditivo->setRotulo   ( "Data da Assinatura"                      );
$obDtAssinaturaAditivo->setTitle    ( 'Informe a Data da Assinatura do Aditivo.');
$obDtAssinaturaAditivo->setNull     ( false                                     );

//Tipo de Termo de Aditivo
include_once( CAM_GPC_TCEMG_MAPEAMENTO.'TTCEMGContratoAditivoTipo.class.php' );
$obTTCEMGContratoAditivoTipo = new TTCEMGContratoAditivoTipo;
$obTTCEMGContratoAditivoTipo->recuperaTodos($rsTermoAditivo, "", "descricao");

$obCmbTermoAditivo= new Select;
$obCmbTermoAditivo->setRotulo       ( "Tipo de Termo de Aditivo"    );
$obCmbTermoAditivo->setName         ( "inCodTermoAditivo"           );
$obCmbTermoAditivo->setId           ( "inCodTermoAditivo"           );
$obCmbTermoAditivo->setValue        ( $inCodTermoAditivo            );
$obCmbTermoAditivo->setStyle        ( "width: 500px"                );
$obCmbTermoAditivo->setCampoID      ( "cod_tipo_aditivo"            );
$obCmbTermoAditivo->setCampoDesc    ( "descricao"                   );
$obCmbTermoAditivo->addOption  	    ( "", "Selecione"               );
$obCmbTermoAditivo->setNull         ( false                         );
$obCmbTermoAditivo->preencheCombo   ( $rsTermoAditivo               );
$obCmbTermoAditivo->obEvento->setOnChange("montaParametrosGET('MontaTermoAditivo');");

//Span Auxiliar para Termo de Aditivo
$SpnTermoAditivo = new Span;
$SpnTermoAditivo->SetId('spnTermoAditivo');

//Painel veiculos de publicidade aditivo
$obVeiculoPublicidadeAditivo = new IPopUpCGMVinculado( $obForm );
$obVeiculoPublicidadeAditivo->setTabelaVinculo      ( 'licitacao.veiculos_publicidade'              );
$obVeiculoPublicidadeAditivo->setCampoVinculo  	    ( 'numcgm'                                      );
$obVeiculoPublicidadeAditivo->setNomeVinculo        ( 'Veículo de Publicação'                       );
$obVeiculoPublicidadeAditivo->setRotulo      	    ( 'Veículo de Publicação'                       );
$obVeiculoPublicidadeAditivo->setTitle        	    ( 'Informe o Veículo de Publicidade do Aditivo.');
$obVeiculoPublicidadeAditivo->setName         	    ( 'stNomCgmVeiculoPublicadadeAditivo'           );
$obVeiculoPublicidadeAditivo->setId           	    ( 'stNomCgmVeiculoPublicadadeAditivo'           );
$obVeiculoPublicidadeAditivo->obCampoCod->setName   ( 'inVeiculoAditivo'                            );
$obVeiculoPublicidadeAditivo->obCampoCod->setId	    ( 'inVeiculoAditivo'                            );
$obVeiculoPublicidadeAditivo->setNull               ( false                                         );
$obVeiculoPublicidadeAditivo->obCampoCod->setNull   ( false                                         );

//Data de Publicação
$obDtPublicacaoAditivo = new Data;
$obDtPublicacaoAditivo->setId       ( "dtPublicacaoAditivo"                     ); 
$obDtPublicacaoAditivo->setName     ( "dtPublicacaoAditivo"                     ); 
$obDtPublicacaoAditivo->setRotulo   ( "Data de Publicação"                      );
$obDtPublicacaoAditivo->setTitle    ( 'Informe a data de publicação do aditivo' );
$obDtPublicacaoAditivo->setNull     ( false                                     );

//Hidden Qtd de Itens
$obHdnQtdItens = new Hidden;
$obHdnQtdItens->setName  ( "qtd_Itens" );
$obHdnQtdItens->setValue ( 0 );
/* Fim Aditivo */

//define o formulário
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnCodAditivo          );
$obFormulario->addHidden( $obHdnExercicioAditivo    );
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
$obFormulario->addTitulo    ( "Dados do Aditivo de Contrato" );
$obFormulario->addComponente( $obTxtExercicioAditivo                        );
$obFormulario->addComponente( $obTxtCodAditivo                              );
$obFormulario->addComponente( $obDtAssinaturaAditivo                        );
$obFormulario->addComponente( $obCmbTermoAditivo                            );
$obFormulario->addComponente( $obVeiculoPublicidadeAditivo                  );
$obFormulario->addComponente( $obDtPublicacaoAditivo                        );
$obFormulario->addSpan      ( $SpnTermoAditivo                              );
$obFormulario->addHidden    ( $obHdnQtdItens                                );

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
    echo "    ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&inNumContrato=".$_REQUEST['inNumContrato']. "&inCodEntidade=".$_REQUEST['inCodEntidade']. "&stExercicioContrato=".$_REQUEST['stExercicioContrato']. "&inNumeroAditivo=".$_REQUEST['inNumeroAditivo'] . "&stExercicioAditivo=".$_REQUEST['stExercicioAditivo']. "', 'carregaLista');  \r\n";
    echo "</script>                                                             \r\n";
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
