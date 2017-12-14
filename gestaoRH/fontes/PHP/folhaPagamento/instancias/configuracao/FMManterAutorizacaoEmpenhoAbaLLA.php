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
    * Data de Criação: 10/07/2007

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30711 $
    $Name$
    $Author: souzadl $
    $Date: 2007-07-24 16:43:58 -0300 (Ter, 24 Jul 2007) $

    * Casos de uso: uc-04.05.29
*/

$obSpnComboOpcoes= new Span();
$obSpnComboOpcoes->setId("spnComboOpcoes");

$obSpnOpcoesConfiguracao = new Span();
$obSpnOpcoesConfiguracao->setId("spnOpcoesConfiguracao");

$obHdnOpcoesConfiguracao = new hidden();
$obHdnOpcoesConfiguracao->setId("hdnOpcoesConfiguracao");

$stJs  = "var url = '".CAM_GRH_FOL_INSTANCIAS."configuracao/OCManterAutorizacaoEmpenho.php?".Sessao::getId()."';\n";

$onBtnIncluir = new Button();
$onBtnIncluir->setName("obBtnIncluir");
$onBtnIncluir->setId  ("obBtnIncluir");
$onBtnIncluir->setValue("Incluir");
$onBtnIncluir->setTipo("button");
$onBtnIncluir->setDisabled(false);
$onBtnIncluir->obEvento->setOnClick("
                                     eval(document.frm.hdnOpcoesConfiguracao.value);
                                     $stJs
                                     jQuery('#stCtrl').val('incluirLLA');
                                     jQuery.post(url, jQuery('#frm').serialize(),function (data) {executaJavaScript(data);},'html');
                                    ");

$onBtnAlterar = new Button();
$onBtnAlterar->setName("obBtnAlterar");
$onBtnAlterar->setId  ("obBtnAlterar");
$onBtnAlterar->setValue("Alterar");
$onBtnAlterar->setTipo("button");
$onBtnAlterar->setDisabled(true);
$onBtnAlterar->obEvento->setOnClick("
                                     eval(document.frm.hdnOpcoesConfiguracao.value);
                                     $stJs
                                     jQuery('#stCtrl').val('alterarLLA');
                                     jQuery.post(url, jQuery('#frm').serialize(),function (data) {executaJavaScript(data);},'html');
                                    ");

$onBtnLimpar = new Button();
$onBtnLimpar->setName("obBtnLimpar");
$onBtnLimpar->setId  ("obBtnLimpar");
$onBtnLimpar->setValue("Limpar");
$onBtnLimpar->setTipo("button");
$onBtnLimpar->setDisabled(false);
$onBtnLimpar->obEvento->setOnClick("
                                     $stJs
                                     jQuery('#stCtrl').val('limparLLA');
                                     jQuery.post(url, jQuery('#frm').serialize(),function (data) {executaJavaScript(data);},'html');
                                    ");

$arBotoesLLA = array($onBtnIncluir,$onBtnAlterar,$onBtnLimpar);

$obSpnConfiguracoesLLA =  new Span();
$obSpnConfiguracoesLLA->setId("spnConfiguracoesLLA");

?>
