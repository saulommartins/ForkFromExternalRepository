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
 * Descrição do Arquivo
 *

  $Id: $

 * @category   Urbem
 * @package    Framework
 * @author     Analista Tonismar Bernardo <tonismar.bernardo@cnm.org.br>
 * @author     Desenvolvedor Eduardo Schitz <eduardo.schitz@cnm.org.br>
 * $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php" );

$preview = new PreviewBirt(2, 10, 6);
$preview->setVersaoBirt('2.5.0');
$preview->setTitulo('Nota da Prestação de Contas');
$preview->setFormato('pdf');

$preview->addParametro('exercicio', $_REQUEST['exercicio']);
$preview->addParametro('cod_credor', $_REQUEST['inCodCredor']);
$preview->addParametro('nome_credor', $_REQUEST['stNomFornecedor']);
$preview->addParametro('cod_despesa', $_REQUEST['inCodDespesa']);
$preview->addParametro('nome_despesa', $_REQUEST['stNomeDespesa']);
$preview->addParametro('cod_empenho', $_REQUEST['inCodEmpenho']);
$preview->addParametro('cod_liquidacao', $_REQUEST['inCodLiquidacao']);
$preview->addParametro('cod_ordem', $_REQUEST['inCodOP']);
$preview->addParametro('cod_entidade', $_REQUEST['inCodEntidade']);
$preview->addParametro('vl_pago', $_REQUEST['inVlPago']);
$preview->addParametro('data_prestacao_contas', $_REQUEST['stDtPrestacaoContas']);
$preview->addParametro('data_pagamento_empenho', $_REQUEST['stDataPagamentoEmpenho']);

$obTOrcamentoEntidade = new TOrcamentoEntidade();
$obTOrcamentoEntidade->setDado( 'exercicio'   , Sessao::getExercicio() );
$obTOrcamentoEntidade->recuperaEntidades( $rsEntidade, "and e.cod_entidade = ".$_REQUEST['inCodEntidade'] );

if ( count($_REQUEST['inCodEntidade']) == 1 ) {
    $preview->addParametro( 'nom_entidade', $rsEntidade->getCampo('nom_cgm') );
}

$preview->addAssinaturas(Sessao::read('assinaturas'));
$preview->preview();
