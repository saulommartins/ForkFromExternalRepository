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
    * Arquivo de processamento para alteração/exclusão/inclusão de Solicitantes
    * Data de Criação: 11/02/2008

    * @author Analista: Gelson W
    * @author Luiz Felipe Prestes Teixeira

    * Casos de uso: uc-03.04.34

    $Id: PRManterSolicitante.php 59612 2014-09-02 12:00:51Z gelson $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GP_COM_MAPEAMENTO."TComprasSolicitante.class.php" );

$stPrograma = "ManterSolicitante";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$obTComprasSolicitante = new TComprasSolicitante();

Sessao::setTrataExcecao( true );
Sessao::getTransacao()->setMapeamento( $obTComprasSolicitante );

switch ($stAcao) {
    case 'incluir' :

            //insere na table compras.solicitante
            $obTComprasSolicitante->setDado('solicitante', $_REQUEST['inCodCGMSolicitante'] );
            $obTComprasSolicitante->setDado('ativo', true  );
            $obTComprasSolicitante->setCgmSolicitacao($_REQUEST['inCodCGMSolicitante']);

            $obTComprasSolicitante->verificaPodeInserirSolicitante( $rsSolicitante );

            if ($rsSolicitante->inNumColunas == 0) {
                $obTComprasSolicitante->inclusao();
                SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=".$stAcao,'Solicitante - '.$_REQUEST['inCodCGMSolicitante'],"incluir","aviso", Sessao::getId(), "../");
            } else {
                $stMensagem = "Solicitante já incluso";
                SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=".$stAcao,urlencode($stMensagem).'!',"n_incluir","erro","erro");
            }
    break;

    case 'alterar' :

            //insere na table compras.solicitante
            $obTComprasSolicitante->setDado('solicitante', $_REQUEST['inCodCGMSolicitante'] );
            $obTComprasSolicitante->setDado('ativo', ( $_REQUEST['boAtivo'] == 'true' ) ? true : false );
            $obTComprasSolicitante->alteracao();

            SistemaLegado::alertaAviso($pgFilt."?".Sessao::getId()."&stAcao=".$stAcao,'Solicitante - '.$_REQUEST['inCodCGMSolicitante'],"alterar","aviso", Sessao::getId(), "../");

    break;

    case 'excluir' :

            $obTComprasSolicitante->setDado('solicitante', $_REQUEST['inCodCGMSolicitante'] );
            $obTComprasSolicitante->setCgmSolicitacao($_REQUEST['inCodCGMSolicitante']);
            $obTComprasSolicitante->verificaPodeExcluirSolicitante( $rsSolicitante );

            if ($rsSolicitante->inNumColunas == 0) {

                //exclui da table compras.solicitante
                $obTComprasSolicitante->exclusao();
                SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=".$stAcao,'Solicitante - '.$_REQUEST['inCodCGMSolicitante'],"excluir","aviso", Sessao::getId(), "../");
            } else {
                $stMensagem = "Solicitante já participou de solicitações";
                SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=".$stAcao,urlencode($stMensagem).'!',"n_excluir","erro");
            }

   break;
}

Sessao::encerraExcecao();
