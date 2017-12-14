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
 * Página de Formulario para Manter Justificativas
 * Data de Criação: 29/09/2008

 * @author Analista      Dagiane Vieira
 * @author Desenvolvedor Alex Cardoso

 * @package URBEM
 * @subpackage

 * @ignore

 $Id:$
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );
include_once ( CAM_GRH_PON_MAPEAMENTO."TPontoJustificativa.class.php"                                   );
include_once ( CAM_GRH_PON_MAPEAMENTO."TPontoJustificativaHoras.class.php"                              );

//Define o nome dos arquivos PHP
$stPrograma = "ManterJustificativa";
$pgFilt = "FL".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgDeta = "DT".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

//**************************************************************************************************************************//
//Define COMPONENTES DO FORMULARIO
//**************************************************************************************************************************//

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao             = $_REQUEST['stAcao'];
$inCodJustificativa = $_REQUEST['inCodJustificativa'];

$rsJustificativa = new RecordSet();

if ($stAcao == 'alterar') {
    $obTPontoJustificativa = new TPontoJustificativa();
    $obTPontoJustificativa->setDado('cod_justificativa',$inCodJustificativa);
    $obTPontoJustificativa->recuperaPorChave($rsJustificativa);

    if ($rsJustificativa->getCampo('anular_faltas') == 't') {
        $boAnularFaltas = true;
    }

    $jsOnload  = "executaFuncaoAjax('preencherHoras', '&inCodJustificativa=$inCodJustificativa&boAnularFaltas=$boAnularFaltas');";
}

$obHdnAcao =  new Hidden;
$obHdnAcao->setName( "stAcao");
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( $stCtrl );

//Instancia o form
$obForm = new Form;
$obForm->setAction      ( $pgProc  );
$obForm->setTarget      ( "oculto" );

$obHdnCodJustificativa = new Hidden();
$obHdnCodJustificativa->setName("inCodJustificativa");
$obHdnCodJustificativa->setId("inCodJustificativa");
$obHdnCodJustificativa->setValue($inCodJustificativa);

$obTxtDescricao = new TextBox;
$obTxtDescricao->setRotulo   ( "Descrição"   );
$obTxtDescricao->setTitle    ( "Informe a descrição da justificativa (Faltas Justificadas, Atestados, Licença Médica, Férias)" );
$obTxtDescricao->setName     ( "stDescricao" );
$obTxtDescricao->setId       ( "stDescricao" );
$obTxtDescricao->setMaxLength( 80            );
$obTxtDescricao->setSize     ( 80            );
$obTxtDescricao->setNull     ( false         );
if ($rsJustificativa->getCampo('descricao')) {
    $obTxtDescricao->setValue( stripslashes($rsJustificativa->getCampo('descricao')) );
}

$obChkAnularFaltas = new CheckBox();
$obChkAnularFaltas->setRotulo("Anular todas as horas faltas do período");
$obChkAnularFaltas->setTitle("Marque para anular todas as horas faltas do servidor, no período que será lançado.");
$obChkAnularFaltas->setName('boAnularFaltas');
$obChkAnularFaltas->setId('boAnularFaltas');
$obChkAnularFaltas->setValue(true);
$obChkAnularFaltas->obEvento->setOnClick(" montaParametrosGET('preencherHoras'); ");
$obChkAnularFaltas->setChecked(true);
if ($rsJustificativa->getCampo('anular_faltas') == 'f') {
    $obChkAnularFaltas->setChecked(false);
}
if ($stAcao == 'alterar') {
    $obChkAnularFaltas->setDisabled(true);
}

$obChkLancarDias = new CheckBox();
$obChkLancarDias->setRotulo("Lançar apenas em dias de Trabalho");
$obChkLancarDias->setTitle("Informe se a justificativa deve ser lançada apenas em dias de trabalho ou em todos os demais.");
$obChkLancarDias->setName('boLancarDias');
$obChkLancarDias->setId('boLancarDias');
$obChkLancarDias->setValue(true);
if ($rsJustificativa->getCampo('lancar_dias_trabalho') == 't') {
    $obChkLancarDias->setChecked(true);
}

$obSpanHoras = new Span();
$obSpanHoras->setId('spnHoras');

$obFormulario = new Formulario;
$obFormulario->addForm              ( $obForm                       );
$obFormulario->addTitulo			( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
$obFormulario->addHidden            ( $obHdnCtrl                    );
$obFormulario->addHidden            ( $obHdnAcao                    );
$obFormulario->addHidden            ( $obHdnCodJustificativa        );
$obFormulario->addComponente        ( $obTxtDescricao               );
$obFormulario->addComponente        ( $obChkAnularFaltas            );
$obFormulario->addSpan              ( $obSpanHoras                  );
$obFormulario->addComponente        ( $obChkLancarDias              );
$obFormulario->ok();
$obFormulario->show();

include($pgJS);

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
