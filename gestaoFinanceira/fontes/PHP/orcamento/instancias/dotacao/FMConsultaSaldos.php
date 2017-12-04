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
    * Página de Consulta de Saldos de Dotação
    * Data de Criação   : 22/06/2005

    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @ignore

    $Revision: 30824 $
    $Name$
    $Autor: $
    $Date: 2008-03-24 11:42:25 -0300 (Seg, 24 Mar 2008) $

    * Casos de uso: uc-02.01.26

*/

/*
$Log$
Revision 1.6  2007/08/14 14:39:56  bruce
Bug#9908#

Revision 1.5  2006/07/05 20:42:50  cleisson
Adicionada tag Log aos arquivos

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoDespesa.class.php"                    );

//Define o nome dos arquivos PHP
$stPrograma = "ConsultaSaldos";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once($pgJS);

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

$obRegra = new ROrcamentoDespesa;

$obRegra->obROrcamentoEntidade->setExercicio( Sessao::getExercicio() );
$obRegra->obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );

$obRegra->obROrcamentoClassificacaoDespesa->setExercicio( Sessao::getExercicio() );
$stMascaraRubrica = $obRegra->obROrcamentoClassificacaoDespesa->recuperaMascara();

Sessao::remove('arItens');

$obRegra->obROrcamentoEntidade->setCodigoEntidade   ( $_REQUEST['inCodEntidade']    );
$obRegra->setExercicio                              ( $_REQUEST['stExercicio']      );
$obRegra->obTPeriodo->setDataInicial                ( $_REQUEST['stDataInicial']    );
$obRegra->obTPeriodo->setDataFinal                  ( $_REQUEST['stDataFinal']      );
$obRegra->setCodDespesa                             ( $_REQUEST['inCodDespesa']     );

$obRegra->consultarDotacao($rsDotacao);

$stFiltro = '';
$arFiltro = Sessao::read('filtro');
if ( is_array($arFiltro) ) {
    foreach ($arFiltro AS $stCampo => $stValor) {
        $stFiltro .= "&".$stCampo2."=".@urlencode( $stValor2 );
    }
    $stFiltro .= 'pg='.Sessao::read('pg').'&pos='.Sessao::read('pos').'&paginando='.Sessao::read('paginando');
}

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgList );
$obForm->setTarget( "telaPrincipal" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( $stCtrl  );

$obLblEntidade = new Label;
$obLblEntidade->setRotulo( "Entidade"       );
$obLblEntidade->setId    ( "inCodEntidade" );
$obLblEntidade->setValue ( $rsDotacao->getCampo("cod_entidade") . " - " . $rsDotacao->getCampo("entidade") );

$obLblDotacao = new Label;
$obLblDotacao->setRotulo( "Dotação Orçamentária"       );
$obLblDotacao->setId    ( "inCodDotacao" );
$obLblDotacao->setValue ( $rsDotacao->getCampo("cod_despesa") . " - " . $rsDotacao->getCampo("descricao") );

$obLblOrgao = new Label;
$obLblOrgao->setRotulo( "Órgão Orçamentário"       );
$obLblOrgao->setId    ( "inCodOrgao" );
$obLblOrgao->setValue ( $rsDotacao->getCampo("num_orgao") . " - " . $rsDotacao->getCampo("nom_orgao") );

$obLblUnidade = new Label;
$obLblUnidade->setRotulo( "Unidade Orçamentária"       );
$obLblUnidade->setId    ( "inCodUnidade" );
$obLblUnidade->setValue ( $rsDotacao->getCampo("num_unidade") . " - " . $rsDotacao->getCampo("nom_unidade") );

$obLblFuncao = new Label;
$obLblFuncao->setRotulo( "Função"       );
$obLblFuncao->setId    ( "inCodFuncao"  );
$obLblFuncao->setValue ( $rsDotacao->getCampo("cod_funcao") . " - " . $rsDotacao->getCampo("funcao") );

$obLblSubFuncao = new Label;
$obLblSubFuncao->setRotulo( "Sub-Função"    );
$obLblSubFuncao->setId    ( "inCodSubFuncao");
$obLblSubFuncao->setValue ( $rsDotacao->getCampo("cod_subfuncao") . " - " . $rsDotacao->getCampo("subfuncao") );

$obLblPrograma = new Label;
$obLblPrograma->setRotulo( "Programa"       );
$obLblPrograma->setId    ( "inCodPrograma"  );
$obLblPrograma->setValue ( $rsDotacao->getCampo("num_programa") . " - " . $rsDotacao->getCampo("programa") );

$obLblPAO = new Label;
$obLblPAO->setRotulo( "PAO"       );
$obLblPAO->setId    ( "inCodPAO"  );
$obLblPAO->setValue ( $rsDotacao->getCampo("num_acao") . " - " . $rsDotacao->getCampo("nom_pao") );

$obLblDesdobramento = new Label;
$obLblDesdobramento->setRotulo( "Desdobramento"    );
$obLblDesdobramento->setId    ( "inCodEstrurutal");
$obLblDesdobramento->setValue ( $rsDotacao->getCampo("cod_estrutural"));

$obLblRecurso = new Label;
$obLblRecurso->setRotulo( "Recurso" );
$obLblRecurso->setValue ( $rsDotacao->getCampo("cod_recurso")." - ".$rsDotacao->getCampo("nom_recurso") );

/*include_once(CAM_GF_ORC_COMPONENTES."IMontaRecursoDestinacao.class.php");
$obIMontaRecursoDestinacao = new IMontaRecursoDestinacao;
$obIMontaRecursoDestinacao->setLabel( true );
$obIMontaRecursoDestinacao->setCodRecurso( $rsDotacao->getCampo("cod_recurso"));*/

$obLblValorOrcado = new Label;
$obLblValorOrcado->setRotulo( "Valor Orçado"    );
$obLblValorOrcado->setId    ( "vlOrcado"        );
$obLblValorOrcado->setValue ( number_format($rsDotacao->getCampo("valor_orcado"),2,',','.'));

$obLblValorSuplementado = new Label;
$obLblValorSuplementado->setRotulo( "Valor Suplementado"    );
$obLblValorSuplementado->setId    ( "vlSuplementado"        );
$obLblValorSuplementado->setValue ( number_format($rsDotacao->getCampo("valor_suplementado"),2,',','.'));

$obLblValorReduzido = new Label;
$obLblValorReduzido->setRotulo( "Valor Reduzido"    );
$obLblValorReduzido->setId    ( "vlReduzido"        );
$obLblValorReduzido->setValue ( number_format($rsDotacao->getCampo("valor_reduzido"),2,',','.'));

$obLblValorReserva = new Label;
$obLblValorReserva->setRotulo( "Valor de Reserva"    );
$obLblValorReserva->setId    ( "vlReserva"        );
$obLblValorReserva->setValue ( number_format($rsDotacao->getCampo("valor_reserva"),2,',','.'));

$obLblValorEmpenhado = new Label;
$obLblValorEmpenhado->setRotulo( "Valor Empenhado"    );
$obLblValorEmpenhado->setId    ( "vlEmpenhado"        );
$obLblValorEmpenhado->setValue ( number_format($rsDotacao->getCampo("valor_empenhado"),2,',','.'));

$obLblValorAnulado = new Label;
$obLblValorAnulado->setRotulo( "Valor Anulado"    );
$obLblValorAnulado->setId    ( "vlAnulado"        );
$obLblValorAnulado->setValue ( number_format($rsDotacao->getCampo("valor_anulado"),2,',','.'));

$obLblValorLiquidado = new Label;
$obLblValorLiquidado->setRotulo( "Valor Liquidado"    );
$obLblValorLiquidado->setId    ( "vlLiquidado"        );
$obLblValorLiquidado->setValue ( number_format($rsDotacao->getCampo("valor_liquidado"),2,',','.'));

$obLblValorPago = new Label;
$obLblValorPago->setRotulo( "Valor Pago"    );
$obLblValorPago->setId    ( "vlPago"        );
$obLblValorPago->setValue ( number_format($rsDotacao->getCampo("valor_pago"),2,',','.'));

//$vlSaldoDisponivel = number_format((((($rsDotacao->getCampo("valor_orcado") + $rsDotacao->getCampo("valor_suplementado")) - $rsDotacao->getCampo("valor_reduzido")) - $rsDotacao->getCampo("valor_empenhado")) + $rsDotacao->getCampo("valor_anulado")),2,',','.');
$obLblValorSaldoDisponivel = new Label;
$obLblValorSaldoDisponivel->setRotulo( "Saldo Disponível"   );
$obLblValorSaldoDisponivel->setId    ( "vlSaldoDisponivel"  );
//$obLblValorSaldoDisponivel->setValue ( $vlSaldoDisponivel   );
$obLblValorSaldoDisponivel->setValue ( number_format($rsDotacao->getCampo("saldo_disponivel"),2,',','.'));

$stLocation = $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao.$stFiltro;
$obButtonVoltar = new Button;
$obButtonVoltar->setName  ( "Voltar" );
$obButtonVoltar->setValue ( "Voltar" );
$obButtonVoltar->obEvento->setOnClick("Cancelar('".$stLocation."');");

$obFormulario = new Formulario;
$obFormulario->addForm( $obForm                    );
$obFormulario->addTitulo( "Dados da Dotação"       );
$obFormulario->addHidden( $obHdnCtrl               );
$obFormulario->addHidden( $obHdnAcao               );
$obFormulario->setAjuda ( 'UC-02.01.26' );
$obFormulario->addComponente( $obLblEntidade        );
$obFormulario->addComponente( $obLblDotacao         );
$obFormulario->addComponente( $obLblOrgao           );
$obFormulario->addComponente( $obLblUnidade         );
$obFormulario->addComponente( $obLblFuncao          );
$obFormulario->addComponente( $obLblSubFuncao       );
$obFormulario->addComponente( $obLblPrograma        );
$obFormulario->addComponente( $obLblPAO             );
$obFormulario->addComponente( $obLblDesdobramento   );
$obFormulario->addComponente( $obLblRecurso         );

$obFormulario->addTitulo( "Saldos"                      );
$obFormulario->addComponente( $obLblValorOrcado         );
$obFormulario->addComponente( $obLblValorSuplementado   );
$obFormulario->addComponente( $obLblValorReduzido       );
$obFormulario->addComponente( $obLblValorEmpenhado      );
$obFormulario->addComponente( $obLblValorAnulado        );
$obFormulario->addComponente( $obLblValorLiquidado      );
$obFormulario->addComponente( $obLblValorPago           );
$obFormulario->addComponente( $obLblValorReserva        );
$obFormulario->addComponente( $obLblValorSaldoDisponivel);

$obFormulario->defineBarra( array( $obButtonVoltar ), "left", "" );
$obFormulario->show();

?>
