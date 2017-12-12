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
    * Página de processamento para o cadastro de natureza de transferência
    * Data de Criação   : 17/11/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Vitor Davi Valentini

    * @ignore

    * $Id: PRManterNaturezaTransferencia.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.16
*/

/*
$Log$
Revision 1.5  2006/09/18 10:31:03  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMNaturezaTransferencia.class.php"       );

$stAcao = $request->get('stAcao');

//Define o nome dos arquivos PHP
$stPrograma = "ManterNaturezaTransferencia";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgProc = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgOcul = "OC".$stPrograma.".php";

$obRCIMNaturezaTransferencia = new RCIMNaturezaTransferencia;

if ($_REQUEST["boAutomaticaNatureza"] == "Sim") {
    $boAutomaticaNatureza = "t";
} else {
    $boAutomaticaNatureza = "f";
}

switch ($stAcao) {
    case "incluir":
    $obRCIMNaturezaTransferencia->setDescricaoNatureza   ( $_REQUEST["stDescricaoNatureza"] );
    $obRCIMNaturezaTransferencia->setAutomaticaNatureza  ( $boAutomaticaNatureza            );
    $obRCIMNaturezaTransferencia->setDocumentosInterface ( Sessao::read('Documentos')       );
    $obErro = $obRCIMNaturezaTransferencia->incluirNaturezaTransferencia();
        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgForm, $obRCIMNaturezaTransferencia->getCodigoNatureza()." - ".$_REQUEST['stDescricaoNatureza'],"incluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
        break;

    case "alterar":
        $obRCIMNaturezaTransferencia->setCodigoNatureza      ( $_REQUEST["inCodigoNatureza"]    );
        $obRCIMNaturezaTransferencia->setDescricaoNatureza   ( $_REQUEST["stDescricaoNatureza"] );
        $obRCIMNaturezaTransferencia->setAutomaticaNatureza  ( $boAutomaticaNatureza            );
        $obRCIMNaturezaTransferencia->setDocumentosInterface ( Sessao::read('Documentos')       );
        $obErro = $obRCIMNaturezaTransferencia->alterarNaturezaTransferencia();
        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList, $obRCIMNaturezaTransferencia->getCodigoNatureza()." - ".$_REQUEST['stDescricaoNatureza'],"alterar","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }
        break;

    case "excluir":
        $obRCIMNaturezaTransferencia->setCodigoNatureza ( $_REQUEST["inCodigoNatureza"] );
        $obErro = $obRCIMNaturezaTransferencia->excluirNaturezaTransferencia();
        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList, $obRCIMNaturezaTransferencia->getCodigoNatureza()." - ".$_REQUEST['stDescricaoNatureza'],"excluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::alertaAviso($pgList,urlencode($obErro->getDescricao()),"n_excluir","erro",Sessao::getId(), "../");
        }
    break;
}
?>
