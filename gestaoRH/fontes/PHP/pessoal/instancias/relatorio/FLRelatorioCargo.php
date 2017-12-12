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
    * Página de Formulario de Seleção de Impressora para Relatorio
    * Data de Criação   : 07/06/2005

    * @author Rafael Almeida

    * @ignore

    $Revision: 30566 $
    $Name$
    $Author: andre $
    $Date: 2007-06-04 10:30:34 -0300 (Seg, 04 Jun 2007) $

    * Casos de uso :uc-04.04.11

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                     );

//Define o nome dos arquivos PHP
$stPrograma = "RelatorioCargo";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";
$pgProc = "PR".$stPrograma.".php";
$jsOnload = "";

include_once($pgJS);

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

//DEFINICAO DO FORMULARIO
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );
//$obForm->setTarget( "telaPrincipal" );

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName ( "stCaminho"                                  );
$obHdnCaminho->setValue( CAM_GRH_PES_INSTANCIAS."relatorio/".$pgProc  );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $stCtrl  );

$obHdnValidaServidores =  new hiddenEval();
$obHdnValidaServidores->setName  ( "hdnValidaServidores" );
$obHdnValidaServidores->setId    ( "hdnValidaServidores" );

$obChkApresentarTodos = new CheckBox();
$obChkApresentarTodos->setRotulo("Apresentar");
$obChkApresentarTodos->setLabel('Todos');
$obChkApresentarTodos->setId("stApresentaTodos");
$obChkApresentarTodos->obEvento->setOnClick(' if (jQuery(this).attr(\'checked\') == true) { jQuery(\'input:checkbox\').attr(\'checked\', true);  } else { jQuery(\'input:checkbox\').attr(\'checked\', false); } executaFuncaoAjax(\'habilitaSpanApresentaServidores\'); ');

$obChkApresentarPadroes = new CheckBox();
$obChkApresentarPadroes->setRotulo("Apresentar");
$obChkApresentarPadroes->setTitle("Marque as opções de informações do relatório");
$obChkApresentarPadroes->setLabel("Padrões");
$obChkApresentarPadroes->setName("stApresentaPadroes");
$obChkApresentarPadroes->setId("stApresentaPadroes");
$obChkApresentarPadroes->setValue("true");
$obChkApresentarPadroes->obEvento->setOnClick(' marcarDesmarcarTodos(this); ');

$obChkApresentarPadroesValor = new CheckBox();
$obChkApresentarPadroesValor->setRotulo("Apresentar");
$obChkApresentarPadroesValor->setLabel("Padrões com Valor");
$obChkApresentarPadroesValor->setName("stApresentaPadroesValor");
$obChkApresentarPadroesValor->setId("stApresentaPadroesValor");
$obChkApresentarPadroesValor->setValue("true");
$obChkApresentarPadroesValor->obEvento->setOnClick('if (jQuery(this).attr(\'checked\') == true){if(jQuery(\'#stApresentaPadroes\').attr(\'checked\') == false) {jQuery(\'#stApresentaPadroes\').attr(\'checked\', true); } } marcarDesmarcarTodos(this);');

$obChkApresentarProgressoes = new CheckBox();
$obChkApresentarProgressoes->setRotulo("Apresentar");
$obChkApresentarProgressoes->setLabel("Progressões");
$obChkApresentarProgressoes->setName("stApresentaProgressoes");
$obChkApresentarProgressoes->setId("stApresentaProgessoes");
$obChkApresentarProgressoes->setValue("true");
$obChkApresentarProgressoes->obEvento->setOnClick(' marcarDesmarcarTodos(this); ');

$obChkApresentarSaldoVagas = new CheckBox();
$obChkApresentarSaldoVagas->setRotulo("Apresentar");
$obChkApresentarSaldoVagas->setLabel("Saldo de Vagas");
$obChkApresentarSaldoVagas->setName("stApresentaSaldoVagas");
$obChkApresentarSaldoVagas->setId("stApresentaSaldoVagas");
$obChkApresentarSaldoVagas->setValue("true");
$obChkApresentarSaldoVagas->obEvento->setOnClick(' marcarDesmarcarTodos(this); ');

$obChkApresentarReajustes = new CheckBox();
$obChkApresentarReajustes->setRotulo("Apresentar");
$obChkApresentarReajustes->setLabel("Reajustes Salariais");
$obChkApresentarReajustes->setName("stApresentaReajustes");
$obChkApresentarReajustes->setId("stApresentaReajustes");
$obChkApresentarReajustes->setValue("true");
$obChkApresentarReajustes->obEvento->setOnClick(' marcarDesmarcarTodos(this); ');

$obChkApresentarServidores = new CheckBox();
$obChkApresentarServidores->setRotulo("Apresentar");
$obChkApresentarServidores->setLabel("Servidores");
$obChkApresentarServidores->setName("stApresentaServidores");
$obChkApresentarServidores->setId("stApresentaServidores");
$obChkApresentarServidores->setValue("true");
$obChkApresentarServidores->obEvento->setOnClick(' marcarDesmarcarTodos(this); executaFuncaoAjax(\'habilitaSpanApresentaServidores\');');

$obSpanServidores = new Span();
$obSpanServidores->setId('spnServidores');
$obSpanServidores->setValue("");

$obCodOrdenacao = new TextBox;

$obTxtCodOrdenacao= new TextBox;
$obTxtCodOrdenacao->setRotulo        ( "Ordenação dos cargos"     );
$obTxtCodOrdenacao->setTitle         ( "Selecione o tipo de ordenação dos cargos no relatório: código de cargo, descrição de cargo ou código de CBO."  );
$obTxtCodOrdenacao->setName          ( "inOrdencao" );
$obTxtCodOrdenacao->setId            ( "inOrdencao" );
$obTxtCodOrdenacao->setValue         ( $inOrdenacao );
$obTxtCodOrdenacao->setSize          ( 6            );
$obTxtCodOrdenacao->setMaxLength     ( 3            );
$obTxtCodOrdenacao->setNull          ( true         );
$obTxtCodOrdenacao->setInteiro       ( true         );

$obCmbOrdenacao = new Select;
$obCmbOrdenacao->setRotulo           ( "Ordenação"      );
$obCmbOrdenacao->setTitle            ( "Selecione o tipo de ordenação dos cargos no relatório: código de cargo, descrição de cargo ou código de CBO."  );
$obCmbOrdenacao->setName             ( "stOrdenacao"    );
$obCmbOrdenacao->setValue            ( $stOrdenacao     );
$obCmbOrdenacao->setStyle            ( "width: 200px"   );
$obCmbOrdenacao->addOption           ( "", "Selecione"  );
$obCmbOrdenacao->addOption           ( "1", "Código"    );
$obCmbOrdenacao->addOption           ( "2", "Descrição" );
$obCmbOrdenacao->addOption           ( "3", "CBO"       );
$obCmbOrdenacao->setNull             ( true             );

$obFormulario = new Formulario;
$obFormulario->addForm      ( $obForm );
$obFormulario->addTitulo    ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
$obFormulario->addHidden    ( $obHdnCaminho );
$obFormulario->addHidden    ( $obHdnValidaServidores, true );
$obFormulario->addTitulo    ( "Parâmetros para Emissão" );

$obFormulario->addComponente($obChkApresentarPadroes);
$obFormulario->addComponente($obChkApresentarPadroesValor);
$obFormulario->addComponente($obChkApresentarProgressoes);
$obFormulario->addComponente($obChkApresentarSaldoVagas);
$obFormulario->addComponente($obChkApresentarReajustes);
$obFormulario->addComponente($obChkApresentarServidores);
$obFormulario->addComponente($obChkApresentarTodos);

$obFormulario->addSpan($obSpanServidores);
$obFormulario->addComponenteComposto( $obTxtCodOrdenacao, $obCmbOrdenacao );

$obFormulario->OK(true);
$obFormulario->setFormFocus( $obTxtCodOrdenacao->getId() );
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
