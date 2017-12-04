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
/**
    * Formulário
    * Data de Criação: 13/09/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Vandré Miguel Ramos

    * @ignore

    $Revision: 30566 $
    $Name$
    $Author: andre $
    $Date: 2007-06-04 10:30:34 -0300 (Seg, 04 Jun 2007) $

    * Casos de uso: uc-04.05.55
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php" );

include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoConfiguracao.class.php"                             );
include_once ( CAM_GRH_FOL_MAPEAMENTO .'TFolhaPagamentoTipoEventoDecimo.class.php'                     );
include_once (CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                        );

$stPrograma = 'ManterConfiguracaoDecimo';
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

include_once ( $pgOcul );
include_once ( $pgJs );

$obRFolhaPagamentoConfiguracao = new RFolhaPagamentoConfiguracao;
$obRFolhaPagamentoConfiguracao->consultar();
$stMascaraEvento = $obRFolhaPagamentoConfiguracao->getMascaraEvento();

$rsTiposEvento = new RecordSet;
$arCompEventos = array();

$obForm = new Form;
$obForm->setAction ( $pgProc  );
$obForm->setTarget ( 'oculto' );

$obHdnAcao = new Hidden;
$obHdnAcao->setName     ( "stAcao" );
$obHdnAcao->setValue    ( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName     ( "stCtrl" );
$obHdnCtrl->setValue    ( ""       );

//BUSCANDO A LISTA DE EVENTOS DE PENSÃO
$obTFolhaPagamentoTipoDecimo = new TFolhaPagamentoTipoEventoDecimo();
$obTFolhaPagamentoTipoDecimo->recuperaTodos( $rsTiposEvento );

while ( !$rsTiposEvento->eof() ) {
    $stNome  = 'stInner_Cod_' . $rsTiposEvento->getCampo ('cod_tipo') ;
    $stInner = 'stInner_'.$rsTiposEvento->getCampo ('cod_tipo')       ;
    $ObjInner = new BuscaInner;
    $ObjInner->setRotulo                       ( $rsTiposEvento->getCampo('descricao')                       );
    $ObjInner->setTitle                        ( "Informe o Evento."                                         );
    $ObjInner->setId                           ( $stInner                                                    );
    $ObjInner->setNull                         ( false                                                       );
    $ObjInner->obCampoCod->setName             ( $stNome                                                     );

    ////TODO desscobrir como buscar o valor
    $ObjInner->obCampoCod->setValue            ( ''                                                          );
    $ObjInner->obCampoCod->setAlign            ( "LEFT"                                                      );
    $ObjInner->obCampoCod->setMascara          ( $stMascaraEvento                                            );
    $ObjInner->obCampoCod->setPreencheComZeros ( "E"                                                         );
    $ObjInner->obCampoCod->obEvento->setOnChange( "preencherEvento('".$rsTiposEvento->getCampo('cod_tipo')."','D');" );
    $ObjInner->setFuncaoBusca ( "abrePopUp('".CAM_GRH_FOL_POPUPS."IRRF/FLManterTabelaIRRF.php','frm','" .$stNome."','".$stInner."','','".Sessao::getId()."&stNatureza=D&boEventoSistema=true','800','550')" );
    $arCompEventos[] = $ObjInner;
    $rsTiposEvento->proximo();
}

$obRFolhaPagamentoConfiguracao = new RFolhaPagamentoConfiguracao;
$obRFolhaPagamentoConfiguracao->consultar();
$inMesCalculoDecimo = $obRFolhaPagamentoConfiguracao->getMesCalculoDecimo();

$obRdnTodos = new Radio;
$obRdnTodos->setRotulo  ( "Numeração" );
$obRdnTodos->setName    ( "boNumeracao"      );
$obRdnTodos->setId      ( "boNumeracao"      );
$obRdnTodos->setLabel   ( "Todos"            );
$obRdnTodos->setValue   ( "Todos"            );
$obRdnTodos->setChecked ( true               );

$obRdnPares = new Radio;
$obRdnPares->setRotulo  ( "Numeração" );
$obRdnPares->setName    ( "boNumeracao"      );
$obRdnPares->setId      ( "boNumeracao"      );
$obRdnPares->setLabel   ( "Pares"            );
$obRdnPares->setValue   ( "Pares"            );
$obRdnPares->setChecked ( false              );

$obRdAdiantamento13MesSalarioSim = new Radio();
$obRdAdiantamento13MesSalarioSim->setRotulo ('Gera Adiant. de 13º Salário no mês de aniversário');
$obRdAdiantamento13MesSalarioSim->setName   ('boRdGerarAdiantamento13');
$obRdAdiantamento13MesSalarioSim->setId     ('boRdGerarAdiantamento13');
$obRdAdiantamento13MesSalarioSim->setLabel  ('Sim');
$obRdAdiantamento13MesSalarioSim->setValue  ('true');

$obRdAdiantamento13MesSalarioNao = new Radio();
$obRdAdiantamento13MesSalarioNao->setRotulo ('Gera Adiant. de 13º Salário no mês de aniversário');
$obRdAdiantamento13MesSalarioNao->setName   ('boRdGerarAdiantamento13');
$obRdAdiantamento13MesSalarioNao->setId     ('boRdGerarAdiantamento13');
$obRdAdiantamento13MesSalarioNao->setLabel  ('Não');
$obRdAdiantamento13MesSalarioNao->setValue  ('false');

//busca configuracao ja realizada
$boAdiantamentoDecimo = SistemaLegado::pegaConfiguracao('adiantamento_13_salario'.Sessao::getEntidade(),27,Sessao::getExercicio(), $boTransacao);
if ( $boAdiantamentoDecimo == 'true' ) {
    $obRdAdiantamento13MesSalarioSim->setChecked(true);
    $obRdAdiantamento13MesSalarioNao->setChecked(false);
}else{
    $obRdAdiantamento13MesSalarioSim->setChecked(false);
    $obRdAdiantamento13MesSalarioNao->setChecked(true);
}

$arRadAdiantamento13 = array($obRdAdiantamento13MesSalarioSim, $obRdAdiantamento13MesSalarioNao);

$obRdbDecimoNovembro = new Radio;
$obRdbDecimoNovembro->setRotulo ( "Saldo de 13º Salário" );
$obRdbDecimoNovembro->setName   ( "inMesCalculoDecimo" );
$obRdbDecimoNovembro->setValue  ( "11" );
$obRdbDecimoNovembro->setTitle  ( "Informe o mês em que será realizado o pagamento do 13º Salário." );
$obRdbDecimoNovembro->setLabel  ( "Novembro" );
$obRdbDecimoNovembro->setChecked( $inMesCalculoDecimo != '12' );
$obRdbDecimoNovembro->setNull   ( false );

$obRdbDecimoDezembro = new Radio;
$obRdbDecimoDezembro->setName    ( "inMesCalculoDecimo" );
$obRdbDecimoDezembro->setValue   ( "12" );
$obRdbDecimoDezembro->setLabel   ( "Dezembro" );
$obRdbDecimoDezembro->setChecked ( $inMesCalculoDecimo == '12' );
$obRdbDecimoDezembro->setNull    ( false );

$obFormulario = new Formulario;
$obFormulario->addForm   ( $obForm    );
$obFormulario->addTitulo ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
$obFormulario->addHidden ( $obHdnCtrl );
$obFormulario->addHidden ( $obHdnAcao );
$obFormulario->addTitulo("Eventos" );
// adicionando ao fourmulário os buscaInner pra cada tipo de evento encontrado
foreach ($arCompEventos as $componente) {
    $obFormulario->addComponente( $componente );
}

$obFormulario->agrupaComponentes ( $arRadAdiantamento13 );
$obFormulario->addTitulo("Competência de Pagamento" );
$obFormulario->agrupaComponentes(array($obRdbDecimoNovembro,$obRdbDecimoDezembro));

$obFormulario->Ok();
$obFormulario->show();
preencherInnerEventos( true );

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
