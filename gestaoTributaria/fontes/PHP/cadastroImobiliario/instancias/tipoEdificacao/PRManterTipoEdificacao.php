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
    * Página de processamento para o cadastro de tipo de edificação
    * Data de Criação   : 25/08/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Cassiano de Vasconcelos Ferreira

    * @ignore

    * $Id: PRManterTipoEdificacao.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.10
*/

/*
$Log$
Revision 1.5  2007/02/27 19:04:19  cassiano
Bug #8437#

Revision 1.4  2006/09/18 10:31:41  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMTipoEdificacao.class.php"       );

$stAcao = $request->get('stAcao');

$stLink = Sessao::read('stLink');

//Define o nome dos arquivos PHP
$stPrograma = "ManterTipoEdificacao";
$pgFilt = "FL".$stPrograma.".php?stAcao=$stAcao";
$pgList = "LS".$stPrograma.".php?stAcao=$stAcao".$stLink;
$pgForm = "FM".$stPrograma.".php?stAcao=$stAcao";
$pgProc = "PR".$stPrograma.".php?stAcao=$stAcao";
$pgOcul = "OC".$stPrograma.".php?stAcao=$stAcao";
$pgJS   = "JS".$stPrograma.".js";

$obRCIMTipoEdificacao = new RCIMTipoEdificacao;
$inCodAtributosSelecionados = $_REQUEST["inCodAtributoSelecionados"];

switch ($stAcao) {
    case "incluir":
        $obRCIMTipoEdificacao->setNomeTipo      ( $_REQUEST["stNomeTipo"]      );
        for ($inCount=0; $inCount < count($inCodAtributosSelecionados); $inCount++) {
            $inCodAtributo = $inCodAtributosSelecionados[ $inCount ];
            $obRCIMTipoEdificacao->obRCadastroDinamico->addAtributosDinamicos($inCodAtributo);
        }

        if ($_REQUEST["boAliquotaAtivo"]) {
            $obRCIMTipoEdificacao->setAliquotaVigencia( $_REQUEST["dtVigenciaAliquota"] );
            $obRCIMTipoEdificacao->setAliquotaCodNorma( $_REQUEST["inCodigoFundamentacaoAliquota"] );
            $obRCIMTipoEdificacao->setAliquotaTerritorial( $_REQUEST["flAliquotaTerritorial"] );
            $obRCIMTipoEdificacao->setAliquotaPredial( $_REQUEST["flAliquotaPredial"] );
        }

        if ($_REQUEST["boM2Ativo"]) {
            $obRCIMTipoEdificacao->setMDVigencia( $_REQUEST["dtVigenciaMD"] );
            $obRCIMTipoEdificacao->setMDCodNorma( $_REQUEST["inCodigoFundamentacao"] );
            $obRCIMTipoEdificacao->setMDTerritorial( $_REQUEST["flValorTerritorial"] );
            $obRCIMTipoEdificacao->setMDPredial( $_REQUEST["flValorPredial"] );
        }

        $obErro = $obRCIMTipoEdificacao->incluirTipoEdificacao();
        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgForm,"Nome tipo: ".$_REQUEST['stNomeTipo'],"incluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
        break;

    case "alterar":
        $obRCIMTipoEdificacao->setCodigoTipo     ( $_REQUEST["inCodigoTipo"]    );
        $obRCIMTipoEdificacao->setNomeTipo       ( $_REQUEST["stNomeTipo"]      );
        for ($inCount=0; $inCount<count($inCodAtributosSelecionados); $inCount++) {
            $inCodAtributo = $inCodAtributosSelecionados[ $inCount ];
            $obRCIMTipoEdificacao->obRCadastroDinamico->addAtributosDinamicos($inCodAtributo);
        }

        if ($_REQUEST["boAliquotaAtivo"]) {
            $obRCIMTipoEdificacao->setAliquotaVigencia( $_REQUEST["dtVigenciaAliquota"] );
            $obRCIMTipoEdificacao->setAliquotaCodNorma( $_REQUEST["inCodigoFundamentacaoAliquota"] );
            $obRCIMTipoEdificacao->setAliquotaTerritorial( $_REQUEST["flAliquotaTerritorial"] );
            $obRCIMTipoEdificacao->setAliquotaPredial( $_REQUEST["flAliquotaPredial"] );
        }

        if ($_REQUEST["boM2Ativo"]) {
            $obRCIMTipoEdificacao->setMDVigencia( $_REQUEST["dtVigenciaMD"] );
            $obRCIMTipoEdificacao->setMDCodNorma( $_REQUEST["inCodigoFundamentacao"] );
            $obRCIMTipoEdificacao->setMDTerritorial( $_REQUEST["flValorTerritorial"] );
            $obRCIMTipoEdificacao->setMDPredial( $_REQUEST["flValorPredial"] );
        }

        $obErro = $obRCIMTipoEdificacao->alterarTipoEdificacao();
        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList,"Nome tipo: ".$_REQUEST['stNomeTipo'],"alterar","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }
        break;

    case "excluir":
        $obRCIMTipoEdificacao->setCodigoTipo    ( $_REQUEST["inCodigoTipo"]    );
        $obRCIMTipoEdificacao->setNomeTipo      ( $_REQUEST['stNomeTipo'] );
        $obErro = $obRCIMTipoEdificacao->excluirTipoEdificacao();
        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList,"Nome tipo: ".$_REQUEST['stNomeTipo'],"excluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::alertaAviso($pgList,urlencode($obErro->getDescricao()),"n_excluir","erro",Sessao::getId(), "../");
        }
        break;
}
?>
