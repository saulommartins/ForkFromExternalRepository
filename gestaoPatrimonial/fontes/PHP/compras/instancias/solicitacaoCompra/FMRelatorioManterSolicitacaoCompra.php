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
  * Página de processamento para Requisição
  * Data de criação : 08/02/2007

  * @author Analista: Gelson
  * @author Programador: Henrique Boaventura

  * @ignore

  $Id: FMRelatorioManterSolicitacaoCompra.php 65105 2016-04-25 19:30:38Z jean $

  Caso de uso: uc-03.04.01

  **/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

if (isset($_REQUEST['cod_solicitacao']) && isset($_REQUEST['cod_entidade'])) {
    $_REQUEST['inSolicitacao']  = $_REQUEST['cod_solicitacao'];
    $_REQUEST['inEntidade']     = $_REQUEST['cod_entidade'];
}

$obBirtPreview = new PreviewBirt(3, 35, 2);

# Relatório desenvolvido na versão 2.1.3, manter essa versão, não migrar.
$obBirtPreview->setTitulo    ('Relatório de Solicitação de Compra' );

# Parâmetros
$obBirtPreview->addParametro ("cod_solicitacao"         , $_REQUEST['inSolicitacao'] );
$obBirtPreview->addParametro ("cod_entidade"            , $_REQUEST['inEntidade'] );
$obBirtPreview->addParametro ("entidade"                , $_REQUEST['inEntidade'] );
$obBirtPreview->addParametro ("data_solicitacao"        , $_REQUEST['dtSolicitacao'] );
$obBirtPreview->addParametro ("hora_solicitacao"        , $_REQUEST['stHoraSolicitacao']);
$obBirtPreview->addParametro ("exercicio"               , Sessao::getExercicio() );
$obBirtPreview->addParametro ("exercicio_solicitacao"   , $_REQUEST['exercicio'] );
$obBirtPreview->addParametro ("cod_acao"                , 1580 );
$obBirtPreview->addParametro ("incluir_assinaturas"     , $boIncluirAssinaturas);
$obBirtPreview->addParametro ("boRegistroPreco"         , ($_REQUEST['boRegistroPreco']=='t') ? 'Sim' : 'Não' );
$obBirtPreview->addAssinaturas( Sessao::read('assinaturas'));

$obBirtPreview->preview();

?>
