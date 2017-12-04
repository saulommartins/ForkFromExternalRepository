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

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-07-13 12:21:34 -0300 (Sex, 13 Jul 2007) $

    * Casos de uso: uc-04.05.29
*/

isset($stNomeCgm ) ? $stNomeCgm  : $stNomeCgm = "";
isset($inNumCGM ) ? $inNumCGM  : $inNumCGM = "";
$obBscFornecedor = new BuscaInner;
$obBscFornecedor->setRotulo           ( "CGM Fornecedor"          );
$obBscFornecedor->setTitle            ( "Informe ou selecione o CGM do Fornecedor para emissão da Autorização de Empenho." );
$obBscFornecedor->setNullBarra        ( false );
$obBscFornecedor->setValue            ( $stNomeCgm );
$obBscFornecedor->setId               ( "campoInner" );
$obBscFornecedor->obCampoCod->setName ( "inNumCGM"   );
$obBscFornecedor->obCampoCod->setValue( $inNumCGM    );
$obBscFornecedor->obCampoCod->obEvento->setOnChange("montaParametrosGET('buscaCGM','inNumCGM',true);");
$obBscFornecedor->setFuncaoBusca( "abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inNumCGM','campoInner','geral','".Sessao::getId()."','800','550')" );

isset($stDescricaoAutorizacao) ? $stDescricaoAutorizacao  : $stDescricaoAutorizacao = "";
$obTxtDescricaoAutorizacao = new TextArea();
$obTxtDescricaoAutorizacao->setRotulo("Descrição da Autorização");
$obTxtDescricaoAutorizacao->setTitle("Digite a descrição da autorização de empenho.");
$obTxtDescricaoAutorizacao->setName("stDescricaoAutorizacao");
$obTxtDescricaoAutorizacao->setValue($stDescricaoAutorizacao);
$obTxtDescricaoAutorizacao->setRows(3);
$obTxtDescricaoAutorizacao->setMaxCaracteres(160);

$obSpnCmbHistoricoPadrao = new Span();
$obSpnCmbHistoricoPadrao->setId('spnCmbHistoricoPadrao');

isset($stDescricaoItemAutorizacao) ? $stDescricaoItemAutorizacao  : $stDescricaoItemAutorizacao = "";
$obTxtDescricaoItemAutorizacao = new TextArea();
$obTxtDescricaoItemAutorizacao->setRotulo("Descrição Ítem da Autorização");
$obTxtDescricaoItemAutorizacao->setTitle("Digite a descrição do ítem da autorização de empenho. Exemplo: Folha de Pagamento - 01/2007, diárias, salário família ou salário maternidade.");
$obTxtDescricaoItemAutorizacao->setName("stDescricaoItemAutorizacao");
$obTxtDescricaoItemAutorizacao->setId("stDescricaoItemAutorizacao");
$obTxtDescricaoItemAutorizacao->setValue($stDescricaoItemAutorizacao);
$obTxtDescricaoItemAutorizacao->setRows(3);
$obTxtDescricaoItemAutorizacao->setMaxCaracteres(160);
$obTxtDescricaoItemAutorizacao->setNullBarra(false);

isset($stComplementoAutorizacao) ? $stComplementoAutorizacao : $stComplementoAutorizacao = "";
$obTxtComplementoAutorizacao = new TextArea();
$obTxtComplementoAutorizacao->setRotulo("Complemento");
$obTxtComplementoAutorizacao->setTitle("Digite o texto complementar do ítem da autorização de empenho.");
$obTxtComplementoAutorizacao->setName("stComplementoAutorizacao");
$obTxtComplementoAutorizacao->setValue($stComplementoAutorizacao);
$obTxtComplementoAutorizacao->setRows(3);
$obTxtComplementoAutorizacao->setMaxCaracteres(160);

$arComponentesAutorizacao = array($obBscFornecedor,
                                  $obTxtDescricaoAutorizacao,
                                  $obTxtDescricaoItemAutorizacao,
                                  $obTxtComplementoAutorizacao);

$obSpnConfiguracoesEmpenhos =  new Span();
$obSpnConfiguracoesEmpenhos->setId("spnConfiguracoesEmpenho");

?>
