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
    * Página de processamento para o cadastro de localização
    * Data de Criação   : 09/09/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

    * @ignore

    * $Id: PRManterLocalizacao.php 63826 2015-10-21 16:39:23Z arthur $

    * Casos de uso: uc-05.01.03
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLocalizacao.class.php"       );

$stAcao = $request->get('stAcao');
$stLink = Sessao::read('stLink');

//Define o nome dos arquivos PHP
$stPrograma = "ManterLocalizacao";
$pgFilt      = "FL".$stPrograma.".php?stAcao=".$stAcao;
$pgList      = "LS".$stPrograma.".php?stAcao=".$stAcao.$stLink;
$pgForm      = "FM".$stPrograma.".php?stAcao=".$stAcao;
$pgFormNivel = "FM".$stPrograma."Nivel.php?stAcao=".$stAcao;
$pgProc      = "PR".$stPrograma.".php?stAcao=".$stAcao;
$pgOcul      = "OC".$stPrograma.".php?stAcao=".$stAcao;
$pgJS        = "JS".$stPrograma.".js";

$obRCIMLocalizacao = new RCIMLocalizacao();
$obAtributos = new MontaAtributos();
$obAtributos->setName('Atributo_');
$obAtributos->recuperaVetor( $arChave );

switch ($stAcao) {
    case "incluir":
        
        if ( $request->get('boCodLocalAutomatico') == 'true' ) {
            $obRCIMLocalizacao->setLocalizacaoAutomatica ( $request->get('boCodLocalAutomatico') );
            $obRCIMLocalizacao->setCodigoVigencia ( $request->get('inCodigoVigencia') );
            $obRCIMLocalizacao->setCodigoNivel    ( $request->get('inCodigoNivel') );
            
            $stValorReduzido = $request->get('stChaveLocalizacao');
            if (isset($stValorReduzido)){
                $obRCIMLocalizacao->setValorReduzido  ( $request->get('stChaveLocalizacao') );    
            }
            $obRCIMLocalizacao->ultimorValorComposto($rsCodLocalizacao);            
            $request->set("inValorLocalizacao", $rsCodLocalizacao->getCampo('codigo_localizacao'));
        }

        $arValidaLocalizacao['cod_nivel']        = $request->get('inCodigoNivel');
        $arValidaLocalizacao['cod_vigencia']     = $request->get('inCodigoVigencia');
        $arValidaLocalizacao['valor']            = preg_replace( "/0/", "", trim( $request->get("inValorLocalizacao") ) );
        $arValidaLocalizacao['codigo_composto']  = $request->get('stChaveLocalizacao').".";
        
        $obRCIMLocalizacao->setCodigoVigencia  ( $request->get("inCodigoVigencia")   );
        $obRCIMLocalizacao->setCodigoNivel     ( $request->get("inCodigoNivel")      );
        $obRCIMLocalizacao->setNomeLocalizacao ( $request->get("stNomeLocalizacao")  );
        $obRCIMLocalizacao->setValor           ( preg_replace( "/0/", "", trim( $request->get("inValorLocalizacao") ) ) );
        $obRCIMLocalizacao->setValorComposto   ( $request->get("stChaveLocalizacao") );
        
        //MONTAR UM LOOP PARA PEGAR O VALOR DOS COMBOS
        include_once ( CAM_GT_CIM_NEGOCIO."RCIMLocalizacao.class.php"       );
        
        $obRCIMLocTmp = new RCIMLocalizacao;
        
        $stMascara = $request->get("stChaveLocalizacao");
        
        if ($stMascara != "") {
            $stComposto = "";
            $arMascara = explode(".",$stMascara);
            $i = 0;
            while ( $i < count ( $arMascara ) ) {
                $stComposto .= $arMascara[$i].".";
                $stCompostoI = substr($stComposto,0,strlen($stComposto)-1);
                $obRCIMLocTmp->setValorReduzido($stCompostoI);
                $obRCIMLocTmp->setCodigoNivel($i);
                $obRCIMLocTmp->listarNomLocalizacao($rsTmp);
                $stChave = array( ($i+1),$rsTmp->getCampo("cod_localizacao"),$arMascara[$i],$stCompostoI );
                $obRCIMLocalizacao->addCodigoLocalizacao($stChave);
                $i++;
            }
        }
        
        if ($_REQUEST["boAliquotaAtivo"]) {
            $obRCIMLocalizacao->setAliquotaVigencia( $request->get("dtVigenciaAliquota") );
            $obRCIMLocalizacao->setAliquotaCodNorma( $request->get("inCodigoFundamentacaoAliquota") );
            $obRCIMLocalizacao->setAliquotaTerritorial( $request->get("flAliquotaTerritorial") );
            $obRCIMLocalizacao->setAliquotaPredial( $request->get("flAliquotaPredial") );
        }

        if ($_REQUEST["boM2Ativo"]) {
            $obRCIMLocalizacao->setMDVigencia( $request->get("dtVigenciaMD") );
            $obRCIMLocalizacao->setMDCodNorma( $request->get("inCodigoFundamentacao") );
            $obRCIMLocalizacao->setMDTerritorial( $request->get("flValorTerritorial") );
            $obRCIMLocalizacao->setMDPredial( $request->get("flValorPredial") );
        }

        $stLink  = "&inCodigoVigencia=".$request->get("inCodigoVigencia");
        $stLink .= "&cmbNivel=".$request->get("inCodigoNivel");
        $stLink .= "&stValorComposto=".$request->get("stChaveLocalizacao");
        Sessao::write('stLink', $stLink);

        //ATRIBUTOS
        foreach ($arChave as $key=>$value) {
            $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
            $inCodAtributo = $arChaves[0];
            if ( is_array($value) ) {
                $value = implode(",",$value);
            }
            $obRCIMLocalizacao->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $value );
        }

        $obErro = $obRCIMLocalizacao->incluirLocalizacao();

        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgFormNivel.$stLink,"Nome Localização: ".$_REQUEST['stNomeLocalizacao'],"incluir","aviso", Sessao::getId(), "../");
        } else {

            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    break;
    case "alterar":
        $obErro = new Erro;

        if ( !$obErro->ocorreu() ) {

            $obRCIMLocalizacao->setCodigoVigencia    ( $_REQUEST["inCodigoVigencia"]    );
            $obRCIMLocalizacao->setCodigoNivel       ( $_REQUEST["inCodigoNivel"]       );
            $obRCIMLocalizacao->setCodigoLocalizacao ( $_REQUEST["inCodigoLocalizacao"] );
            $obRCIMLocalizacao->setNomeLocalizacao   ( $_REQUEST["stNomeLocalizacao"]   );
            $obRCIMLocalizacao->setValor             ( preg_replace( "/^0*/", "",trim( $_REQUEST["inValorLocalizacao"] ) ) );
            $obRCIMLocalizacao->setValorReduzido     ( $_REQUEST["inValorLocalizacao"].'.'.$_REQUEST['stValorReduzido']     );

            $arChaveLocalizacao = array( "","","", $_REQUEST["stValorReduzido"] );
            //[0] = cod_nivel | [1] = cod_localizacao | [2] = valor | [3] = valor_reduzido
            $obRCIMLocalizacao->addCodigoLocalizacao( $arChaveLocalizacao );

            //ATRIBUTOS
            foreach ($arChave as $key=>$value) {
                $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
                $inCodAtributo = $arChaves[0];
                if ( is_array($value) ) {
                    $value = implode(",",$value);
                }
                $obRCIMLocalizacao->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $value );
            }
            $obErro = $obRCIMLocalizacao->alterarLocalizacao();
        }
        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList,"Nome Localização: ".$_REQUEST['stNomeLocalizacao'],"alterar","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }
    break;
    case "excluir":
        $obRCIMLocalizacao->setCodigoNivel       ( $_REQUEST["inCodigoNivel"]       );
        $obRCIMLocalizacao->setCodigoVigencia    ( $_REQUEST["inCodigoVigencia"]    );
        $obRCIMLocalizacao->setCodigoLocalizacao ( $_REQUEST["inCodigoLocalizacao"] );
        $obRCIMLocalizacao->setValorReduzido     ( $_REQUEST["stValorReduzido"]     );

        $obErro = $obRCIMLocalizacao->excluirLocalizacao();

        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList,"Nome Localização: ".$_REQUEST['stNomeLocalizacao'],"excluir","aviso", Sessao::getId(), "../");
            sistemaLegado::executaFrameOculto ( "buscaDado('SetarMascaraLocalizacao');" );
        } else {
            SistemaLegado::alertaAviso($pgList,urlencode($obErro->getDescricao()),"n_excluir","erro",Sessao::getId(), "../");
        }
    break;

    case "reativar":
        $obRCIMLocalizacao->setCodigoLocalizacao        ( $_REQUEST["inCodigoLocalizacao"] );
        $obRCIMLocalizacao->setValorReduzido            ( $_REQUEST["stValorReduzido"]     );
        $obRCIMLocalizacao->setJustificativa            ( $_REQUEST["stJustificativa"]     );
        $obRCIMLocalizacao->setJustificativaReativar    ( $_REQUEST["stJustReat"]          );
        $obRCIMLocalizacao->setDataBaixa                ( $_REQUEST["stTimeStamp"]         );

        $obErro = $obRCIMLocalizacao->reativarLocalizacao();

        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList,"Reativar localização concluído com sucesso! (Nome Localização: ".$_REQUEST['stNomeLocalizacao'].")","reativar","aviso", Sessao::getId(), "../");

        } else {
            SistemaLegado::alertaAviso($pgList,"Erro ao reativar localização (".urlencode($obErro->getDescricao()).")","n_reativar","erro",Sessao::getId(), "../");
        }
        break;

    case "baixar":
        $obRCIMLocalizacao->setCodigoVigencia    ( $_REQUEST["inCodigoVigencia"]    );
        $obRCIMLocalizacao->setCodigoNivel       ( $_REQUEST["inCodigoNivel"]       );
        $obRCIMLocalizacao->setCodigoLocalizacao ( $_REQUEST["inCodigoLocalizacao"] );
        $obRCIMLocalizacao->setValorReduzido     ( $_REQUEST["stValorReduzido"]     );
        $obRCIMLocalizacao->setJustificativa     ( $_REQUEST["stJustificativa"]     );
        $obErro = $obRCIMLocalizacao->baixarLocalizacao();
        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList,"Nome Localização: ".$_REQUEST['stNomeLocalizacao'],"baixar","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::alertaAviso($pgList,urlencode($obErro->getDescricao()),"n_baixar","erro",Sessao::getId(), "../");
        }
    break;
    case "historico":
        $obRCIMLocalizacao->setCodigoVigencia    ( $_REQUEST["inCodigoVigencia"]    );
        $obRCIMLocalizacao->setCodigoNivel       ( $_REQUEST["inCodigoNivel"]       );
        $obRCIMLocalizacao->setCodigoLocalizacao ( $_REQUEST["inCodigoLocalizacao"] );
        //ATRIBUTOS
        foreach ($arChave as $key=>$value) {
            $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
            $inCodAtributo = $arChaves[0];
            if ( is_array($value) ) {
                $value = implode(",",$value);
            }
            $obRCIMLocalizacao->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $value );
        }
        $obErro = $obRCIMLocalizacao->alterarCaracteristicas();
        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList,"Nome Localização: ".$_REQUEST['stNomeLocalizacao'],"alterar","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }
    break;
}

?>