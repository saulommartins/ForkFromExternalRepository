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
* Arquivo de instância para manutenção de atributos
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 4642 $
$Name$
$Author: cassiano $
$Date: 2006-01-04 10:33:50 -0200 (Qua, 04 Jan 2006) $

Casos de uso: uc-01.03.96
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GA_ADM_NEGOCIO."RAtributoDinamico.class.php");
include_once(CAM_GA_ADM_NEGOCIO."RAdministracaoConfiguracao.class.php");

$stLink = "&pg=".Sessao::read('link_pg')."&pos=".Sessao::read('link_pos')."&stAcao=".$stAcao;

//Define o nome dos arquivos PHP
$stPrograma = "ManterAtributo";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?".Sessao::getId().$stLink;
$pgForm = "FM".$stPrograma.".php?".Sessao::getId();
$pgProc = "PR".$stPrograma.".php?".Sessao::getId();
$pgOcul = "OC".$stPrograma.".php";

$obRAtributoDinamico = new RAtributoDinamico;
$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

switch ($stAcao) {
    case "incluir":
        $arSessaoValores = Sessao::read('Valores');

        $obRAtributoDinamico->setNome                ( $_POST["stNomAtributo"] );
        $obRAtributoDinamico->setMascara             ( $_POST["stMascara"] );
        $obRAtributoDinamico->setCodTipo             ( $_POST["inCodTipoAtributo"] );
        $obRAtributoDinamico->setObrigatorio         ( $_POST["boNaoNulo"] );
        $obRAtributoDinamico->setAtivo               ( 'true' );
        $obRAtributoDinamico->obRModulo->setCodModulo( $_POST["inCodModulo"] );
        $obRAtributoDinamico->setCodCadastro         ( $_POST["inCodCadastro"] );
        $obRAtributoDinamico->setAjuda               ( $_POST["stMensagemAuxiliar"] );
        $obRAtributoDinamico->setIndexavel           ( $_POST["boIndexavel"] );

        if ( ($obRAtributoDinamico->getCodTipo()==3) OR ($obRAtributoDinamico->getCodTipo()==4) ) {
            foreach ($arSessaoValores as $campo => $valor) {
                $obRAtributoDinamico->addValor (  $arSessaoValores[$campo]['cod_valor']
                                                 ,$arSessaoValores[$campo]['ativo']
                                                 ,$arSessaoValores[$campo]['valor'] );
            }
        } else {
            $obRAtributoDinamico->addValor ( 1, 'Sim', $_POST["stValorPadrao"] );
        }
        $obErro = $obRAtributoDinamico->salvar();
        if (!$obErro->ocorreu()) {
            $stLink .= "&inCodGestao=".$_REQUEST["inCodGestao"];
            $stLink .= "&inCodModulo=".$_REQUEST["inCodModulo"];
            $stLink .= "&inCodCadastro=".$_REQUEST["inCodCadastro"];
            $stLink .= "&stAcao=".$stAcao;
            SistemaLegado::alertaAviso($pgForm.$stLink,$_REQUEST["stNomAtributo"],"incluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    break;

    case "alterar":
        $arSessaoValores = Sessao::read('Valores');

        $obRAtributoDinamico->setCodAtributo         ( $_POST["inCodAtributo"] );
        $obRAtributoDinamico->setNome                ( $_POST["stNomAtributo"] );
        $obRAtributoDinamico->setMascara             ( $_POST["stMascara"] );
        $obRAtributoDinamico->setCodTipo             ( $_POST["inCodTipoAtributo"] );
        $obRAtributoDinamico->setObrigatorio         ( $_POST["boNaoNulo"] );
        $obRAtributoDinamico->setAtivo               ( $_POST["boAtributoAtivo"] );
        $obRAtributoDinamico->obRModulo->setCodModulo( $_POST["inCodModulo"] );
        $obRAtributoDinamico->setCodCadastro         ( $_POST["inCodCadastro"] );
        $obRAtributoDinamico->setAjuda               ( $_POST["stMensagemAuxiliar"] );
        if ( ($obRAtributoDinamico->getCodTipo()==3) OR ($obRAtributoDinamico->getCodTipo()==4) ) {
            foreach ($arSessaoValores as $campo => $valor) {
                $obRAtributoDinamico->addValor (  $arSessaoValores[$campo]['cod_valor']
                                                 ,$arSessaoValores[$campo]['ativo']
                                                 ,$arSessaoValores[$campo]['valor'] );
            }
        } else {
            $obRAtributoDinamico->addValor ( 1, 'Sim', $_POST["stValorPadrao"] );
        }
        $obErro = $obRAtributoDinamico->salvar();
        if (!$obErro->ocorreu()) {
            SistemaLegado::alertaAviso($pgList,$_REQUEST['stNomAtributo'],"alterar","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }
    break;

    case "excluir";
    $obRAtributoDinamico->setCodTipo                ( $_GET["inCodTipo"] );
        $obRAtributoDinamico->setCodAtributo            ( $_GET["inCodAtributo"] );
        $obRAtributoDinamico->obRModulo->setCodModulo   ( $_GET["inCodModulo"] );
        $obRAtributoDinamico->setCodCadastro            (  $_GET["inCodCadastro"] );
        $obErro = $obRAtributoDinamico->excluir($boTransacao);
        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList."&stAcao=".$_REQUEST['stAcao'],$_REQUEST['stNomAtributo'],"excluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::alertaAviso($pgList."&stAcao=".$_REQUEST['stAcao'],urlencode( $obErro->getDescricao() ),"n_excluir","erro", Sessao::getId(), "../");
        }
    break;
    case "alterarConfiguracao":
        $obRAdministracaoConfiguracao = new RAdministracaoConfiguracao;
        $obRAdministracaoConfiguracao->setNumeroInscricao     ( $_POST["boNumInscImob"] );
        $obRAdministracaoConfiguracao->setNumeroLote          ( $_POST["boNumLote"] );
        $obRAdministracaoConfiguracao->setMascaraLote         ( $_POST["stMascaraLote"] );
        $obRAdministracaoConfiguracao->setMascaraInscricao    ( $_POST["stMascaraInscImob"] );
        $obRAdministracaoConfiguracao->obRModulo->setCodModulo( $_POST["inCodModulo"] );
        $obErro = $obRAdministracaoConfiguracao->salvaConfiguracao();
        if (!$obErro->ocorreu()) {
            SistemaLegado::alertaAviso("FMManterConfiguracao.php?".Sessao::getId()."&acao=".$_POST['acao'],"Configuracao CIM ","alterar","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }
    break;
}
?>
