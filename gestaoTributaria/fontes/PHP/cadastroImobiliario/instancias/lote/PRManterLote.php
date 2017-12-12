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
    * Página de processamento para o cadastro de lote
    * Data de Criação   : 06/12/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

    * @ignore

    * $Id: PRManterLote.php 62838 2015-06-26 13:02:49Z diogo.zarpelon $

    * Casos de uso: uc-05.01.08
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLoteRural.class.php"        );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLoteUrbano.class.php"       );

include_once ( CAM_GA_ADM_MAPEAMENTO."TUnidadeMedida.class.php"       );

//MANTEM O FILTRO E A PAGINACAO
$stLink = Sessao::read('stLink');
$link   = Sessao::read('link');

foreach($link as $key => $value){
    if($value!='')
        $stLink .= "&".$key."=".$value;
}

//Define o nome dos arquivos PHP
$stPrograma = "ManterLote" ;
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php?".$stLink."&funcionalidade=".$_REQUEST["funcionalidade"];
$pgForm     = "FM".$stPrograma.".php?funcionalidade=".$_REQUEST["funcionalidade"];
$pgFormAlt  = "FM".$stPrograma."Alteracao.php?funcionalidade=".$_REQUEST["funcionalidade"];
$pgFormAgl  = "FM".$stPrograma."Aglutinar.php?funcionalidade=".$_REQUEST["funcionalidade"];
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js" ;
$pgListValidar    = "LSValidarLote.php?".$stLink."&funcionalidade=".$_REQUEST["funcionalidade"];
$pgListCancDesm   = "LSCancelarDesmembramentoLote.php?$stLink&funcionalidade=".$_REQUEST["funcionalidade"];
$pgFormImovel     = "../imovel/FMManterImovelLote.php";

include_once( $pgJS );

function alertaAvisoRedirect($location="", $objeto="", $tipo="n_validar", $chamada="erro", $sessao, $caminho="", $func="", $acao="")
{
    //Imprime um código javascript que redireciona o frame 'telaPrincipal'
    print '<script type="text/javascript">
                alertaAviso ( "'.$objeto.'","'.$tipo.'","'.$chamada.'","'.Sessao::getId().'","'.$caminho.'");
           </script>';

    $aux = explode("?",$location);

    $location = $aux[0]."?".Sessao::getId()."&".$aux[1];

    Sessao::write('acaoLote', $acao);
    Sessao::write('modulo', "12" );

    print '<script type="text/javascript">
                mudaTelaPrincipal( "'.$location.'" );
                mudaMenu         ( "'.$func.'"     );
           </script>';
}

if ($_REQUEST["funcionalidade"] == 178) {
    $obRCIMLote = new RCIMLoteUrbano;
    $stTipoLote = " Urbano";
} elseif ($_REQUEST["funcionalidade"] == 193) {
    $obRCIMLote = new RCIMLoteRural;
    $stTipoLote = " Rural";
}

$obAtributos = new MontaAtributos;
$obAtributos->setName( "Atributo_" );
$obAtributos->recuperaVetor( $arChave );

switch ($_REQUEST['stAcao']) {
    case "incluir":
       $stValorLote = ltrim( $_REQUEST["stNumeroLote"],"0" );

       include_once ( CAM_GT_CIM_NEGOCIO."RCIMLocalizacao.class.php"       );
       $obRCIMLocalizacao = new RCIMLocalizacao;
       $obRCIMLocalizacao->setValorComposto($_REQUEST["stChaveLocalizacao"]);
       $obRCIMLocalizacao->consultaCodigoLocalizacao($inCodigoLocalizacao);

       $obRCIMLote->obRCIMLocalizacao->setCodigoLocalizacao( $inCodigoLocalizacao );
       $obRCIMLote->setNumeroLote        ( $stValorLote                     );
       $obRCIMLote->setDataInscricao     ( $_REQUEST["dtDataInscricaoLote"] );
       $obRCIMLote->setProfundidadeMedia ( $_REQUEST["flProfundidadeMedia"] );
       $arGrandezaUnidade = explode      ( "-", $_REQUEST["stChaveUnidadeMedida"] );
       $obRCIMLote->setCodigoGrandeza    ( $arGrandezaUnidade[1]            );
       $obRCIMLote->setCodigoUnidade     ( $arGrandezaUnidade[0]            );
       $obRCIMLote->setAreaLote          ( $_REQUEST["flAreaLote"]          );
       //BAIRRO
       $obRCIMLote->obRCIMBairro->setCodigoUF        ( $_REQUEST["inCodigoUF"] );
       $obRCIMLote->obRCIMBairro->setCodigoMunicipio ( $_REQUEST["inCodigoMunicipio"] );
       $obRCIMLote->obRCIMBairro->setCodigoBairro    ( $_REQUEST["inCodigoBairroLote"] );
       $arProcesso = explode                    ( "/", $_REQUEST["inNumProcesso"] );
       $obRCIMLote->obRProcesso->setCodigoProcesso( $arProcesso[0] );
       $obRCIMLote->obRProcesso->setExercicio     ( $arProcesso[1] );
       //SETANDO AS CONFRONTACOES
       $arConfrontacoesSessao = Sessao::read('confrontacoes');

       foreach ($arConfrontacoesSessao as $inChave => $arConfrontacao) {
           switch ($arConfrontacao["stTipoConfrotacao"]) {
               case "lote":
                   $obRCIMLote->addConfrontacaoLote();
                   $obRCIMLote->roUltimaConfrontacaoLote->obRCIMLote->setCodigoLote( $arConfrontacao["inCodigoLoteConfrontacao"] );
                   $obRCIMLote->roUltimaConfrontacaoLote->setCodigoPontoCardeal( $arConfrontacao["inCodigoPontoCardeal"] );
                   $obRCIMLote->roUltimaConfrontacaoLote->setExtensao( $arConfrontacao["flExtensao"] );
               break;
               case "trecho":
                   $obErro = $obRCIMLote->addConfrontacaoTrecho( $arConfrontacao["stChaveTrecho"] );
                   $obRCIMLote->roUltimaConfrontacaoTrecho->setPrincipal( $arConfrontacao["boTestada"] == "S" ? "t" : "f" );
                   $obRCIMLote->roUltimaConfrontacaoTrecho->setCodigoPontoCardeal( $arConfrontacao["inCodigoPontoCardeal"] );
                   $obRCIMLote->roUltimaConfrontacaoTrecho->setExtensao( $arConfrontacao["flExtensao"] );
               break;
               case "outros":
                   $obRCIMLote->addConfrontacaoDiversa();
                   $obRCIMLote->roUltimaConfrontacaoDiversa->setDescricaoConfrontacao( $arConfrontacao["stDescricaoOutros"] );
                   $obRCIMLote->roUltimaConfrontacaoDiversa->setCodigoPontoCardeal( $arConfrontacao["inCodigoPontoCardeal"] );
                   $obRCIMLote->roUltimaConfrontacaoDiversa->setExtensao( $arConfrontacao["flExtensao"] );
               break;
           }
       }
       //ATRIBUTOS
        foreach ($arChave as $key=>$value) {
            $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
            $inCodAtributo = $arChaves[0];
            if ( is_array($value) ) {
                $value = implode(",",$value);
            }
            $obRCIMLote->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $value );
        }

       $obErro = $obRCIMLote->incluirLote();
       if ( !$obErro->ocorreu() ) {
           if ($_REQUEST["boSeguir"]) {
                alertaAvisoRedirect($pgFormImovel."?stAcao=incluir&stTipoLote=$stTipoLote&inCodigoLote=".$obRCIMLote->getCodigoLote()."&inNumeroLote=".$stValorLote,"Número do Lote".$stTipoLote.": ".STR_PAD($_REQUEST["stNumeroLote"],strlen($hdnMascaraLote),'0',STR_PAD_LEFT),"incluir","aviso",Sessao::getId(),"../", "179","738");
           } else {
                SistemaLegado::alertaAviso($pgForm,"Número do Lote".$stTipoLote.": ".STR_PAD($_REQUEST["stNumeroLote"],strlen($hdnMascaraLote),'0',STR_PAD_LEFT),"incluir","aviso", Sessao::getId(), "../");
           }
       } else {
           SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
       }
    break;
    case "alterar":
    case "validar":
        $obErro = new Erro;
        $obRCIMLote->setCodigoLote( $_REQUEST["inCodigoLote"] );
        $obRCIMLote->obRCIMLocalizacao->setCodigoLocalizacao( $_REQUEST["inCodigoLocalizacao"] );
        $obRCIMLote->setNumeroLote        ( $_REQUEST["stNumeroLote"]        );
        $obRCIMLote->setDataInscricao     ( $_REQUEST["dtDataInscricaoLote"] );
        $obRCIMLote->setProfundidadeMedia ( $_REQUEST["flProfundidadeMedia"] );
        $arGrandezaUnidade = explode      ( "-", $_REQUEST["stChaveUnidadeMedida"] );
        $obRCIMLote->setCodigoGrandeza    ( $arGrandezaUnidade[1]            );
        $obRCIMLote->setCodigoUnidade     ( $arGrandezaUnidade[0]            );
        $obRCIMLote->setAreaLote          ( $_REQUEST["flAreaLote"]          );

        $obRCIMLote->obRCIMBairro->setCodigoBairro ( $_REQUEST['inCodigoBairroLote'] ) ;
        $obRCIMLote->obRCIMBairro->setCodigoUF ( $_REQUEST['inCodigoUF'] ) ;
        $obRCIMLote->obRCIMBairro->setCodigoMunicipio ( $_REQUEST['inCodigoMunicipio'] ) ;

        //Exclusivo para validação  de lotes desmembrados
        if ($_REQUEST['stAcao'] == "validar") {
            $flAreaLote = str_replace( '.', '' , $_REQUEST["flAreaLote"] );
            $flAreaLote = str_replace( ',', '.', $flAreaLote             );
            $obRCIMLote->setDataParcelamento  ( $_REQUEST['hdnTimestampParcelamento'] );
            $obRCIMLote->setCodigoLoteOriginal( $_REQUEST["inCodigoLoteOriginal"]     );
            $obRCIMLote->setCodigoParcelamento( $_REQUEST["inCodigoParcelamento"] );
            $obRCIMLote->verificaAreaLoteValidado( $flAreaRestante );
            if ($_REQUEST['nuLotesValidacao'] == 1) {
                if ($flAreaLote != $flAreaRestante) {
                    $obErro->setDescricao( "A área informada deve ser igual a área disponível para validação (".$flAreaRestante.")." );
                }
            } else {
                if ($flAreaLote >= $flAreaRestante) {
                    $obErro->setDescricao( "A área informada excede o limite disponível para validação (".$flAreaRestante.")." );
                }
            }

            if ( $_REQUEST['nuLotesValidacao'] == 1 AND !$obErro->ocorreu() ) {
                $listaEdificacoesSessao = Sessao::read('lsEdificacoes');
                foreach ($listaEdificacoesSessao as $value) {
                    if ($value["sel"] == 'f') {
                        $obErro->setDescricao( "Devem ser selecionadas todas as edificações disponíveis para este lote." );
                        break;
                    }
                }
            }
        }
        //Fim validação lotes desmembrados

        if ( !$obErro->ocorreu() ) {
            $obRCIMLote->setTimestampLote              ( $_REQUEST["hdnTimestampLote"]   );
            $arProcesso = explode                      ( "/", $_REQUEST["inNumProcesso"] );
            $obRCIMLote->obRProcesso->setCodigoProcesso( $arProcesso[0] );
            $obRCIMLote->obRProcesso->setExercicio     ( $arProcesso[1] );
            //SETANDO AS CONFRONTACOES
            $arConfrontacoesSessao = Sessao::read('confrontacoes');
            foreach ($arConfrontacoesSessao as $inChave => $arConfrontacao) {
                switch ($arConfrontacao["stTipoConfrotacao"]) {
                    case "lote":
                        $obRCIMLote->addConfrontacaoLote();
                        $obRCIMLote->roUltimaConfrontacaoLote->setCodigoConfrontacao ( $arConfrontacao["inCodigoConfrontacao"] );
                        $obRCIMLote->roUltimaConfrontacaoLote->obRCIMLote->setCodigoLote( $arConfrontacao["inCodigoLoteConfrontacao"] );
                        $obRCIMLote->roUltimaConfrontacaoLote->setCodigoPontoCardeal( $arConfrontacao["inCodigoPontoCardeal"] );
                        $obRCIMLote->roUltimaConfrontacaoLote->setExtensao( $arConfrontacao["flExtensao"] );
                    break;
                    case "trecho":
                        $obErro = $obRCIMLote->addConfrontacaoTrecho( $arConfrontacao["stChaveTrecho"] );
                        $obRCIMLote->roUltimaConfrontacaoTrecho->setCodigoConfrontacao ( $arConfrontacao["inCodigoConfrontacao"] );
                        $obRCIMLote->roUltimaConfrontacaoTrecho->setPrincipal( $arConfrontacao["boTestada"] == "S" ? "t" : "f" );
                        $obRCIMLote->roUltimaConfrontacaoTrecho->setCodigoPontoCardeal( $arConfrontacao["inCodigoPontoCardeal"] );
                        $obRCIMLote->roUltimaConfrontacaoTrecho->setExtensao( $arConfrontacao["flExtensao"] );
                    break;
                    case "outros":
                        $obRCIMLote->addConfrontacaoDiversa();
                        $obRCIMLote->roUltimaConfrontacaoDiversa->setCodigoConfrontacao ( $arConfrontacao["inCodigoConfrontacao"] );
                        $obRCIMLote->roUltimaConfrontacaoDiversa->setDescricaoConfrontacao( $arConfrontacao["stDescricaoOutros"] );
                        $obRCIMLote->roUltimaConfrontacaoDiversa->setCodigoPontoCardeal( $arConfrontacao["inCodigoPontoCardeal"] );
                        $obRCIMLote->roUltimaConfrontacaoDiversa->setExtensao( $arConfrontacao["flExtensao"] );
                    break;
                }
            }
            //ATRIBUTOS
            foreach ($arChave as $key=>$value) {
                $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
                $inCodAtributo = $arChaves[0];
                if ( is_array($value) ) {
                    $value = implode(",",$value);
                }
                $obRCIMLote->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $value );
            }
                // passar para regra o array de edificaçoes
            $arEdificacoes = array();
            $listaEdificacoesSessao = Sessao::read('lsEdificacoes');
            if ($listaEdificacoesSessao) {
            foreach ($listaEdificacoesSessao as $value) {
                if ( $value["sel"] == 't')
                    $arEdificacoes[] = $value;
            }
            $obRCIMLote->setEdificacoes ( $arEdificacoes);
        }
            if ( !$obErro->ocorreu() ) {
                $obErro = $obRCIMLote->alterarLoteParcelado( $boTransacao,$_REQUEST["stOrigem"] );
            }
        }

        if ( !$obErro->ocorreu() ) {
            if ($_REQUEST["stOrigem"] == "validar") {
                    SistemaLegado::alertaAviso($pgListValidar,"Número do Lote".$stTipoLote.": ".STR_PAD($_REQUEST["stNumeroLote"],strlen($stMascaraLote),'0',STR_PAD_LEFT),"alterar","aviso", Sessao::getId(), "../");
            } else {
                    SistemaLegado::alertaAviso($pgList,"Número do Lote".$stTipoLote.": ".$_REQUEST["stNumeroLote"],"alterar","aviso", Sessao::getId(), "../");
            }
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }

    break;
    case "excluir":
       $obRCIMLote->setCodigoLote( $_REQUEST["inCodigoLote"] );
       $obRCIMLote->setNumeroLote( $_REQUEST["stValor"] );
       $obRCIMLote->obRCIMLocalizacao->setCodigoLocalizacao( $_REQUEST["inCodigoLocalizacao"] );
       $obErro = $obRCIMLote->excluirLote();
       if ( !$obErro->ocorreu() ) {
           SistemaLegado::alertaAviso($pgList,"Número do Lote".$stTipoLote.": ".$_REQUEST["stDescQuestao"],"excluir","aviso", Sessao::getId(), "../");
       } else {
           SistemaLegado::alertaAviso($pgList,urlencode($obErro->getDescricao()),"n_excluir","erro",Sessao::getId(), "../");
       }
       break;

    case "reativar":
       $obRCIMLote->setDataInscricao ( $_REQUEST["stTimestamp"]     );
       $obRCIMLote->setCodigoLote    ( $_REQUEST["inCodigoLote"]    );
       $obRCIMLote->setJustificativa ( $_REQUEST["stJustificativa"] );
       $obRCIMLote->setJustificativaReativar ( $_REQUEST["stJustReat"] );
       if ($_REQUEST["inProcesso"]) {
            $arProcesso = explode('/',$_REQUEST["inProcesso"]);
            $obRCIMLote->obRProcesso->setCodigoProcesso ( $arProcesso[0] );
            $obRCIMLote->obRProcesso->setExercicio      ( $arProcesso[1] );
       }

       $obErro = $obRCIMLote->reativarLote();
       if ( !$obErro->ocorreu() ) {
           SistemaLegado::alertaAviso($pgList,"Reativar lote concluído com sucesso! (Número do Lote".$stTipoLote.": ".$_REQUEST["stNumeroLote"].")","reativar","aviso", Sessao::getId(), "../");
       } else {
           SistemaLegado::exibeAviso("Erro ao reativar lote (".urlencode($obErro->getDescricao()).")","n_reativar","erro");
       }
       break;

    case "baixar":
       $obRCIMLote->setCodigoLote    ( $_REQUEST["inCodigoLote"]    );
       $obRCIMLote->setJustificativa ( $_REQUEST["stJustificativa"] );

       if ($_REQUEST["inProcesso"]) {
            $arProcesso = explode('/',$_REQUEST["inProcesso"]);
            $obRCIMLote->obRProcesso->setCodigoProcesso ( $arProcesso[0] );
            $obRCIMLote->obRProcesso->setExercicio      ( $arProcesso[1] );
       }

       $obErro = $obRCIMLote->baixarLote();
       if ( !$obErro->ocorreu() ) {
           SistemaLegado::alertaAviso($pgList,"Número do Lote".$stTipoLote.": ".$_REQUEST["stNumeroLote"],"baixar","aviso", Sessao::getId(), "../");
       } else {
           SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_baixar","erro");
       }
    break;
    case "historico":
        $obRCIMLote->setCodigoLote    ( $_REQUEST["inCodigoLote"]    );

        if ($request->get('inProcesso') != '') {
            $arProcesso = preg_split( "/[^a-zA-Z0-9]/", $_REQUEST["inProcesso"]    );
            $obRCIMLote->obRProcesso->setCodigoProcesso( $arProcesso[0] );
            $obRCIMLote->obRProcesso->setExercicio     ( $arProcesso[1] );
        }

        //ATRIBUTOS
        foreach ($arChave as $key=>$value) {
            $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
            $inCodAtributo = $arChaves[0];
            if ( is_array($value) ) {
                $value = implode(",",$value);
            }
            $obRCIMLote->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $value );
        }
        $obErro = $obRCIMLote->alterarCaracteristicas();

        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList,"Número do Lote".$stTipoLote.": ".$_REQUEST["stNumeroLote"],"alterar","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }
    break;
    case "desmembrar":
        $stValorLote = ltrim( $_REQUEST["stNumeroLote"],"0" );
        $flAreaLote = str_replace( '.' , ''  , $_REQUEST["flAreaLote"] );
        $flAreaLote = str_replace( ',' , '.' , $flAreaLote );
        $flAreaRealOrigem = str_replace( '.' , ''  , $_REQUEST["flAreaRealOrigem"] );
        $flAreaRealOrigem = str_replace( ',' , '.' , $flAreaRealOrigem );
        if ($flAreaLote >= $flAreaRealOrigem) {
            $stFiltro  = " WHERE COD_GRANDEZA = 2 AND ";
            $stFiltro .= " COD_UNIDADE = ".$_REQUEST["inCodigoUnidadeOrigem"];
            $obTUnidadeMedida =  new TUnidadeMedida;
            $obErro = $obTUnidadeMedida->recuperaTodos( $rsRecordSet, $stFiltro, "", $boTransacao );
            $stMensagem = "A área informada tem que ser menor que ".$_REQUEST["flAreaRealOrigem"] ." ". $rsRecordSet->getCampo("simbolo");
            SistemaLegado::exibeAviso($stMensagem,"n_alterar","erro");
        } else {
            $obRCIMLote->setCodigoLote                           ( $_REQUEST["inCodigoLote"]                 );
            $obRCIMLote->obRCIMLocalizacao->setCodigoLocalizacao ( $_REQUEST["inCodigoLocalizacao"]          );
            $obRCIMLote->setNumeroLote                           ( $stValorLote                              );
            $obRCIMLote->setDataInscricao                        ( $_REQUEST["dtDataInscricaoLote"]          );
            $obRCIMLote->setProfundidadeMedia                    ( $_REQUEST["flProfundidadeMedia"]          );
            $arGrandezaUnidade = explode                         ( "-", $_REQUEST["stChaveUnidadeMedida"]    );
            $obRCIMLote->setCodigoGrandeza                       ( $arGrandezaUnidade[1]                     );
            $obRCIMLote->setCodigoUnidade                        ( $arGrandezaUnidade[0]                     );
            $obRCIMLote->setAreaLote                             ( $_REQUEST["flAreaLote"]                   );
            //BAIRRO
            $obRCIMLote->obRCIMBairro->setCodigoUF               ( $_REQUEST["inCodigoUF"] );
            $obRCIMLote->obRCIMBairro->setCodigoMunicipio        ( $_REQUEST["inCodigoMunicipio"]            );
            $obRCIMLote->obRCIMBairro->setCodigoBairro           ( $_REQUEST["inCodigoBairroLote"]           );
            $arProcesso = explode                                ( "/", $_REQUEST["inNumProcesso"]           );
            $obRCIMLote->obRProcesso->setCodigoProcesso          ( $arProcesso[0]                            );
            $obRCIMLote->obRProcesso->setExercicio               ( $arProcesso[1]                            );
            //SETANDO AS CONFRONTACOES
            $arConfrontacoesSessao = Sessao::read('confrontacoes');
            foreach ($arConfrontacoesSessao as $inChave => $arConfrontacao) {
                switch ($arConfrontacao["stTipoConfrotacao"]) {
                    case "lote":
                        $obRCIMLote->addConfrontacaoLote();
                        $obRCIMLote->roUltimaConfrontacaoLote->setCodigoConfrontacao         ( $arConfrontacao["inCodigoConfrontacao"]           );
                        $obRCIMLote->roUltimaConfrontacaoLote->obRCIMLote->setCodigoLote     ( $arConfrontacao["inCodigoLoteConfrontacao"]       );
                        $obRCIMLote->roUltimaConfrontacaoLote->setCodigoPontoCardeal         ( $arConfrontacao["inCodigoPontoCardeal"]           );
                        $obRCIMLote->roUltimaConfrontacaoLote->setExtensao                   ( $arConfrontacao["flExtensao"]                     );
                    break;
                    case "trecho":
                        $obErro = $obRCIMLote->addConfrontacaoTrecho                         ( $arConfrontacao["stChaveTrecho"]                  );
                        $obRCIMLote->roUltimaConfrontacaoTrecho->setCodigoConfrontacao       ( $arConfrontacao["inCodigoConfrontacao"]           );
                        $obRCIMLote->roUltimaConfrontacaoTrecho->setPrincipal                ( $arConfrontacao["boTestada"] == "S" ? "t" : "f"   );
                        $obRCIMLote->roUltimaConfrontacaoTrecho->setCodigoPontoCardeal       ( $arConfrontacao["inCodigoPontoCardeal"]           );
                        $obRCIMLote->roUltimaConfrontacaoTrecho->setExtensao                 ( $arConfrontacao["flExtensao"]                     );
                    break;
                    case "outros":
                        $obRCIMLote->addConfrontacaoDiversa();
                        $obRCIMLote->roUltimaConfrontacaoDiversa->setCodigoConfrontacao      ( $arConfrontacao["inCodigoConfrontacao"]           );
                        $obRCIMLote->roUltimaConfrontacaoDiversa->setDescricaoConfrontacao   ( $arConfrontacao["stDescricaoOutros"]              );
                        $obRCIMLote->roUltimaConfrontacaoDiversa->setCodigoPontoCardeal      ( $arConfrontacao["inCodigoPontoCardeal"]           );
                        $obRCIMLote->roUltimaConfrontacaoDiversa->setExtensao                ( $arConfrontacao["flExtensao"]                     );
                    break;
                }
            }
            //ATRIBUTOS
            foreach ($arChave as $key=>$value) {
                $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
                $inCodAtributo = $arChaves[0];
                if ( is_array($value) ) {
                    $value = implode(",",$value);
                }
                $obRCIMLote->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $value );
            }
            $obRCIMLote->setDataParcelamento    ( $_REQUEST["dtDataDesmembramento"]   );
            $obRCIMLote->setProfundidadeMedia   ( $_REQUEST["flProfundidadeMedia"]  );
            $obRCIMLote->setQuantLotes          ( $_REQUEST["inQuantLote"]  );
            $obErro = $obRCIMLote->desmembramentodeLotes();
            if ( !$obErro->ocorreu() ) {
                Sessao::remove('link');
                SistemaLegado::alertaAviso($pgListValidar,"Número do Lote".$stTipoLote.": ".STR_PAD($_REQUEST["stNumeroLote"],strlen($hdnMascaraLote),'0',STR_PAD_LEFT),"alterar","aviso",Sessao::getId(),"../" );
            } else {
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
            }
        }
    break;
    case "aglutinar":
        $obRCIMLote->setCodigoLote                           ( $_REQUEST["inCodigoLote"]                 );
        $obRCIMLote->obRCIMLocalizacao->setCodigoLocalizacao ( $_REQUEST["inCodigoLocalizacao"]          );
        $obRCIMLote->setNumeroLote                           ( $_REQUEST["stNumeroLote"]                 );
        $obRCIMLote->setDataInscricao                        ( $_REQUEST["dtDataInscricaoLote"]          );
        $obRCIMLote->setProfundidadeMedia                    ( $_REQUEST["flProfundidadeMedia"]          );
        $obRCIMLote->setCodigoGrandeza                       ( $_REQUEST["inCodigoGrandeza"]             );
        $obRCIMLote->setCodigoUnidade                        ( $_REQUEST["inCodigoUnidade"]              );
        //AREA
        $flAreaLote = str_replace(',','.',substr( $_REQUEST['flAreaLote'], 0, strpos( $_REQUEST['flAreaLote'], " ") ));

        $obRCIMLote->listarLotesAglutinar($rsPodeAglutinar);
        $inTotalLotesSemImovel = 0;
        if ( $rsPodeAglutinar->eof() ) {
            $inTotalLotesSemImovel++;
            //SistemaLegado::exibeAviso("Um dos lotes informados não possui imóvel cadastrado! (".$_REQUEST["stNumeroLote"].")","n_alterar","erro");
            //exit();
        }
        $arLoteSessao = Sessao::read('lotes');
        foreach ($arLoteSessao as $inChave => $arLotes) {
            $flAreaLote = $flAreaLote + $arLotes['flAreaReal'];
        }
        $obRCIMLote->setAreaLote                             ( $flAreaLote                               );
        //BAIRRO
        $obRCIMLote->obRCIMBairro->setCodigoUF               ( $_REQUEST["inCodigoUF"] );
        $obRCIMLote->obRCIMBairro->setCodigoMunicipio        ( $_REQUEST["inCodigoMunicipio"]            );
        $obRCIMLote->obRCIMBairro->setCodigoBairro           ( $_REQUEST["inCodigoBairroLote"]           );
        $arProcesso = explode                                ( "/", $_REQUEST["inNumProcesso"]           );
        $obRCIMLote->obRProcesso->setCodigoProcesso          ( $arProcesso[0]                            );
        $obRCIMLote->obRProcesso->setExercicio               ( $arProcesso[1]                            );

        //ATRIBUTOS
        foreach ($arChave as $key=>$value) {
            $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
            $inCodAtributo = $arChaves[0];
            if ( is_array($value) ) {
                $value = implode(",",$value);
            }
            $obRCIMLote->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $value );
        }
        $obRCIMLote->setDataParcelamento    ( $_REQUEST["dtDataAglutinacaoLote"]);
        $obRCIMLote->setProfundidadeMedia   ( $_REQUEST["flProfundidadeMedia"]  );

        //SETANDO AS CONFRONTACOES
        $obRCIMLote->arRCIMConfrontacaoLote     = array();
        $obRCIMLote->arRCIMConfrontacaoDiversa  = array();
        $obRCIMLote->arRCIMConfrontacaoTrecho   = array();
        $arConfrontacoesSessao = Sessao::read('confrontacoes');
        foreach ($arConfrontacoesSessao as $inChave => $arConfrontacao) {
            switch ($arConfrontacao["stTipoConfrotacao"]) {
                case "lote":
                    $obRCIMLote->addConfrontacaoLote();
                    $obRCIMLote->roUltimaConfrontacaoLote->setCodigoConfrontacao         ( $arConfrontacao["inCodigoConfrontacao"]           );
                    $obRCIMLote->roUltimaConfrontacaoLote->obRCIMLote->setCodigoLote     ( $arConfrontacao["inCodigoLoteConfrontacao"]       );
                    $obRCIMLote->roUltimaConfrontacaoLote->setCodigoPontoCardeal         ( $arConfrontacao["inCodigoPontoCardeal"]           );
                    $obRCIMLote->roUltimaConfrontacaoLote->setExtensao                   ( $arConfrontacao["flExtensao"]                     );
                break;
                case "trecho":
                    $obErro = $obRCIMLote->addConfrontacaoTrecho                         ( $arConfrontacao["stChaveTrecho"]                  );
                    $obRCIMLote->roUltimaConfrontacaoTrecho->setCodigoConfrontacao       ( $arConfrontacao["inCodigoConfrontacao"]           );
                    $obRCIMLote->roUltimaConfrontacaoTrecho->setPrincipal                ( $arConfrontacao["boTestada"] == "S" ? "t" : "f"   );
                    $obRCIMLote->roUltimaConfrontacaoTrecho->setCodigoPontoCardeal       ( $arConfrontacao["inCodigoPontoCardeal"]           );
                    $obRCIMLote->roUltimaConfrontacaoTrecho->setExtensao                 ( $arConfrontacao["flExtensao"]                     );
                break;
                case "outros":
                    $obRCIMLote->addConfrontacaoDiversa();
                    $obRCIMLote->roUltimaConfrontacaoDiversa->setCodigoConfrontacao      ( $arConfrontacao["inCodigoConfrontacao"]           );
                    $obRCIMLote->roUltimaConfrontacaoDiversa->setDescricaoConfrontacao   ( $arConfrontacao["stDescricaoOutros"]              );
                    $obRCIMLote->roUltimaConfrontacaoDiversa->setCodigoPontoCardeal      ( $arConfrontacao["inCodigoPontoCardeal"]           );
                    $obRCIMLote->roUltimaConfrontacaoDiversa->setExtensao                ( $arConfrontacao["flExtensao"]                     );
                break;
            }
        }

        $obRCIMLote->listarLoteProprietarios( $rsRemanescer );
        $arRemanescer = array();
        while ( !$rsRemanescer->eof() ) {
            array_push($arRemanescer,$rsRemanescer->getCampo( "numcgm" ));
            $rsRemanescer->proximo();
        }
        sort($arRemanescer);
        $count = 0;
        $arLoteSessao = Sessao::read('lotes');
        $inTotaldeLotes = count($arLoteSessao) + 1;
        foreach ($arLoteSessao as $inChave => $arLote) {
            $tempCodLote = $obRCIMLote->getCodigoLote();
            $obRCIMLote->setCodigoLote( $arLote["inNumLote"] );
            $obRCIMLote->listarLotesAglutinar($rsPodeAglutinar);
            $obRCIMLote->setCodigoLote($tempCodLote);

            if ( $rsPodeAglutinar->eof() ) {
                $inTotalLotesSemImovel++;
                if ($inTotalLotesSemImovel >= $inTotaldeLotes) {
                    SistemaLegado::exibeAviso("Um dos lotes informados não possui imóvel cadastrado! (".$arLote["inValorLote"].")","n_alterar","erro");
                    exit();
                }
            }

            $obErro = $obRCIMLote->addLote( $arLote['inNumLote'] );
            $obRCIMLote->arRCIMLote[$count]->setJustificativa( "Lote aglutinado. Lote remanescente ".$_REQUEST['stNumeroLote'] );
            $obRCIMLote->arRCIMLote[$count]->listarLoteProprietarios( $rsAglutinar );

            $arAglutinar = array();
            while ( !$rsAglutinar->eof() ) {
                array_push($arAglutinar, $rsAglutinar->getCampo( "numcgm" ));
                $rsAglutinar->proximo();
            }
            sort( $arAglutinar );
            if ( $arAglutinar && $arRemanescer )
                if ($arRemanescer != $arAglutinar) {
                    $obErro->setDescricao( " Lote ".$arLote['inValorLote']." não tem os mesmos proprietários que o lote remanescente. " );
                }

            if ( $obErro->ocorreu() ) {
                break;
            }
            $stLoteAglutinados .= $arLote['inValorLote'].", ";
            $count++;
        }

        if ( !$obErro->ocorreu() ) {
            $obErro = $obRCIMLote->aglutinarLote();
        }

        if ( !$obErro->ocorreu() ) {
            foreach ($obRCIMLote->arRCIMLote as $inChave => $obRegra) {
                $obRegra->baixarLote();
            }
        }

        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList,"Aglutinados o(s) lote(s) ".$stLoteAglutinados." Lote ".$stTipoLote." remanescente: ".$_REQUEST["stNumeroLote"],"aglutinar","aviso",Sessao::getId(),"../","178" );
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }
    break;
    case 'Cancelar':
        $obRCIMLote->setCodigoParcelamento($_REQUEST['inCodigoParcelamento']);
        $obRCIMLote->setCodigoLote($_REQUEST['inCodigoLote']);
        $obRCIMLote->obRCIMLocalizacao->setCodigoLocalizacao($_REQUEST['inCodigoLocalizacao'] );
        $obErro = $obRCIMLote->cancelarDesmembramento();
        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgListCancDesm,"Cancelado o desmembramento do lote ".$_REQUEST['stDescQuestao'],$_REQUEST['stAcao'],"aviso",Sessao::getId(),"../",$_REQUEST['funcionalidade'] );
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_cancelar","erro");
        }
    break;
}

?>
