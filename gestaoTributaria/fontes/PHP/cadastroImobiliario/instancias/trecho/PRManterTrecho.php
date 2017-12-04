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
    * Página de processamento para o cadastro de trecho
    * Data de Criação   : 03/09/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Gustavo Passos Tourinho

    * @ignore

    * $Id: PRManterTrecho.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.06
*/

/*
$Log$
Revision 1.9  2006/09/18 10:31:51  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMTrecho.class.php"            );

$stAcao = $request->get('stAcao');

//MANTEM O FILTRO E A PAGINACAO
//$stLink = Sessao::read('stLink');
//$link = Sessao::read('link');

//Define o nome dos arquivos PHP
$stPrograma = "ManterTrecho" ;
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php?stAcao=".$_REQUEST['stAcao'].$stLink;
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js" ;

$obRCIMTrecho = new RCIMTrecho;

$obAtributos = new MontaAtributos;
$obAtributos->setName( "Atributo_" );
$obAtributos->recuperaVetor( $arChave );

switch ($stAcao) {
    case "incluir":
        $obRCIMTrecho->setCodigoLogradouro ( $_REQUEST["inNumLogradouro"] );
        $obRCIMTrecho->setSequencia        ( $_REQUEST["inCodSequencia"]  );
        $obRCIMTrecho->setExtensao         ( $_REQUEST["inExtensao"]      );
        $obRCIMTrecho->setExtensao         ( $_REQUEST["flExtensao"]      );
        //ATRIBUTOS
        foreach ($arChave as $key=>$value) {
            $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
            $inCodAtributo = $arChaves[0];
            if ( is_array($value) ) {
                $value = implode(",",$value);
            }
            $obRCIMTrecho->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $value );
        }

        if ($_REQUEST["boAliquotaAtivo"]) {
            $obRCIMTrecho->setAliquotaVigencia( $_REQUEST["dtVigenciaAliquota"] );
            $obRCIMTrecho->setAliquotaCodNorma( $_REQUEST["inCodigoFundamentacaoAliquota"] );
            $obRCIMTrecho->setAliquotaTerritorial( $_REQUEST["flAliquotaTerritorial"] );
            $obRCIMTrecho->setAliquotaPredial( $_REQUEST["flAliquotaPredial"] );
        }

        if ($_REQUEST["boM2Ativo"]) {
            $obRCIMTrecho->setMDVigencia( $_REQUEST["dtVigenciaMD"] );
            $obRCIMTrecho->setMDCodNorma( $_REQUEST["inCodigoFundamentacao"] );
            $obRCIMTrecho->setMDTerritorial( $_REQUEST["flValorTerritorial"] );
            $obRCIMTrecho->setMDPredial( $_REQUEST["flValorPredial"] );
        }

        $obErro = $obRCIMTrecho->incluirTrecho();

        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgForm,"Nome Logradouro: ".$_REQUEST["stNomeLogradouro"]." - Trecho: ".$obRCIMTrecho->getCodigoTrecho(),"incluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
        break;

    case "alterar":
        if ($_REQUEST["flExtensao"] < 0) {
            SistemaLegado::exibeAviso("O valor da extensão deve ser positivo","n_incluir","erro");
            exit();
        }

        $obRCIMTrecho->setCodigoTrecho     ( $_REQUEST["inCodTrecho"]     );
        $obRCIMTrecho->setCodigoLogradouro ( $_REQUEST["inCodLogradouro"] );
        $obRCIMTrecho->setSequencia        ( $_REQUEST["inSequencia"]     );
        $obRCIMTrecho->setExtensao         ( $_REQUEST["flExtensao"]      );

        if ($_REQUEST["boAliquotaAtivo"]) {
            $obRCIMTrecho->setAliquotaVigencia( $_REQUEST["dtVigenciaAliquota"] );
            $obRCIMTrecho->setAliquotaCodNorma( $_REQUEST["inCodigoFundamentacaoAliquota"] );
            $obRCIMTrecho->setAliquotaTerritorial( $_REQUEST["flAliquotaTerritorial"] );
            $obRCIMTrecho->setAliquotaPredial( $_REQUEST["flAliquotaPredial"] );
        }

        if ($_REQUEST["boM2Ativo"]) {
            $obRCIMTrecho->setMDVigencia( $_REQUEST["dtVigenciaMD"] );
            $obRCIMTrecho->setMDCodNorma( $_REQUEST["inCodigoFundamentacao"] );
            $obRCIMTrecho->setMDTerritorial( $_REQUEST["flValorTerritorial"] );
            $obRCIMTrecho->setMDPredial( $_REQUEST["flValorPredial"] );
        }

        //ATRIBUTOS
        foreach ($arChave as $key=>$value) {
            $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
            $inCodAtributo = $arChaves[0];
            if ( is_array($value) ) {
                $value = implode(",",$value);
            }
            $obRCIMTrecho->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $value );
        }
        $obErro = $obRCIMTrecho->alterarTrecho();

        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList,"Nome Logradouro: ".$_REQUEST["stNomeLogradouro"]." - Trecho: ".$_REQUEST["inCodTrecho"],"alterar","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }
        break;

    case "excluir";
        $obRCIMTrecho->setCodigoTrecho  ( $_REQUEST[ "inCodTrecho"     ] );
        $obRCIMTrecho->setCodigoLogradouro ( $_REQUEST[ "inCodLogradouro" ] );
        $obErro = $obRCIMTrecho->excluirTrecho();

        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList,"Nome Logradouro: ".$_REQUEST["stNomeLogradouro"]." - Trecho: ".$_REQUEST["inCodTrecho"],"excluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::alertaAviso($pgList,"Nome Logradouro: ".$_REQUEST["stNomeLogradouro"]." - ".urlencode( $obErro->getDescricao() ),"n_excluir","erro", Sessao::getId(), "../");
//            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_excluir","erro");
        }
        break;

    case "reativar":
        $obRCIMTrecho->setDataBaixa ( $_REQUEST ["stTimeStamp"] );
        $obRCIMTrecho->setCodigoTrecho  ( $_REQUEST["inCodTrecho"]     );
        $obRCIMTrecho->setCodigoLogradouro ( $_REQUEST["inCodLogradouro"] );
        $obRCIMTrecho->setJustificativa ( $_REQUEST["stJustificativa"] );
        $obRCIMTrecho->setJustificativaReativar ( $_REQUEST["stJustReat"] );
        $obErro = $obRCIMTrecho->reativarTrecho();
        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList,"Reativar trecho concluído com sucesso! (Nome Logradouro: ".$_REQUEST["stNomeLogradouro"]." - Trecho: ".$_REQUEST["inCodTrecho"].")","reativar","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso("Erro ao reativar trecho (".urlencode($obErro->getDescricao()).")","n_reativar","erro");
        }
        break;

    case "baixar";
        $obRCIMTrecho->setCodigoTrecho  ( $_REQUEST["inCodTrecho"]     );
        $obRCIMTrecho->setCodigoLogradouro ( $_REQUEST["inCodLogradouro"] );
        $obRCIMTrecho->setJustificativa ( $_REQUEST["stJustificativa"] );
        $obErro = $obRCIMTrecho->baixarTrecho();

        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList,"Nome Logradouro: ".$_REQUEST["stNomeLogradouro"]." - Trecho: ".$_REQUEST["inCodTrecho"],"baixar","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_baixar","erro");
        }
        break;

    case "historico";

        $obRCIMTrecho->setCodigoTrecho  ( $_REQUEST["inCodTrecho"]     );
        $obRCIMTrecho->setCodigoLogradouro ( $_REQUEST["inCodLogradouro"] );
        //ATRIBUTOS
        foreach ($arChave as $key=>$value) {
            $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
            $inCodAtributo = $arChaves[0];
            if ( is_array($value) ) {
                $value = implode(",",$value);
            }
            $obRCIMTrecho->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $value );
        }
        $obErro = $obRCIMTrecho->alterarCaracteristicas();

        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList,"Nome Logradouro: ".$_REQUEST["stNomeLogradouro"]." - Trecho: ".$_REQUEST["inCodTrecho"],"alterar","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }
        break;
}
?>
