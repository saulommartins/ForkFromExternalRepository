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
    * Página de processamento para o cadastro de construção
    * Data de Criação   : 12/01/2005

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
    * @author Desenvolvedor: Fábio Bertoldi Rodrigues

    * @ignore

    * $Id: PRManterConstrucao.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.12
*/

/*
$Log$
Revision 1.8  2006/09/18 10:30:16  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMEdificacao.class.php"      );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMUnidadeAutonoma.class.php" );

$stAcao = $request->get('stAcao');

//MANTEM O FILTRO E A PAGINACAO
$stLink = Sessao::read('stLink');

//Define o nome dos arquivos PHP
$stPrograma    = "ManterConstrucao";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php?".$stLink;
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";

$obAtributos = new MontaAtributos;
$obAtributos->setName      ( "Atributo_" );
$obAtributos->recuperaVetor( $arChave    );

switch ($_REQUEST["stAcao"]) {
    case "incluir":
    if ($_REQUEST["stTipo"] == "imovel") {
       $obRCIMUnidadeAutonoma = new RCIMUnidadeAutonoma( new RCIMImovel( new RCIMLote ) );
       $obRCIMUnidadeAutonoma->roRCIMImovel->setNumeroInscricao( $_REQUEST["inNumeroInscricao"] );
       $obErro = $obRCIMUnidadeAutonoma->roRCIMImovel->listarImoveis( $rsImovel );

       if ( sistemaLegado::comparaDatas($rsImovel->getCampo("dt_cadastro"),$_REQUEST['stDtConstrucao']) ) {
            $obErro->setDescricao('Data de construção ( '.$_REQUEST['stDtConstrucao']. ') inferior a data de cadastro do imóvel ('.$rsImovel->getCampo('dt_cadastro').')');
       }

       if ( !$obErro->ocorreu() ) {
            $obErro = $obRCIMUnidadeAutonoma->verificaUnidadeAutonoma( $rsRecordSet );
            if ( !$obErro->ocorreu() and !$rsRecordSet->eof() ) {
                $obErro = $obRCIMUnidadeAutonoma->consultarUnidadeAutonoma();
                if ( !$obErro->ocorreu() ) {
                    $obRCIMUnidadeAutonoma->addUnidadeDependente();
                    //ATRIBUTOS
                    foreach ($arChave as $key=>$value) {
                        $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
                        $inCodAtributo = $arChaves[0];
                        if ( is_array($value) ) {
                            $value = implode(",",$value);
                        }
                        $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMConstrucaoOutros->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $value );
                    }
                    //$obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMConstrucaoOutros->setAreaConstruida( $_REQUEST["flAreaConstrucao"] );
                    //$obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMConstrucaoOutros->setDataConstrucao( $_REQUEST["stDtConstrucao"] );
                    if ($_REQUEST["inProcesso"]) {
                        $arProcesso = preg_split( "/[^a-zA-Z0-9]/", $_REQUEST["inProcesso"] );
                        $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMConstrucaoOutros->obRProcesso->setCodigoProcesso ( $arProcesso[0] );
                        $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMConstrucaoOutros->obRProcesso->setExercicio      ( $arProcesso[1] );
                    }
                    $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMConstrucaoOutros->setDescricao( $_REQUEST["stDescricaoConstrucao"] );
                    $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->setAreaUnidade( $_REQUEST["flAreaConstrucao"] );
                    $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMConstrucaoOutros->setDataConstrucao( $_REQUEST["stDtConstrucao"] );
                    $obErro = $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->incluirUnidadeDependente();
                }
            } else {
                $obErro->setDescricao( "Deve haver no mínimo uma edificação como unidade autônoma no imóvel informado!" );
            }
       }
       if ( !$obErro->ocorreu() ) {
           SistemaLegado::alertaAviso($pgForm,"Inscrição Imobiliária: ".$_REQUEST["inNumeroInscricao"],"incluir","aviso", Sessao::getId(), "../");
       } else {
           SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
       }
    } else { // se for inclusao condominio
        $obConstrucao =  new RCIMConstrucaoOutros;
        $obConstrucao->setAreaConstruida($_REQUEST["flAreaConstrucao"]);
        $obConstrucao->obRCIMCondominio->setCodigoCondominio($_REQUEST["inCodigoCondominio"]);
        $obConstrucao->setDescricao($_REQUEST["stDescricaoConstrucao"]);
        $obConstrucao->setDataConstrucao($_REQUEST["stDtConstrucao"]);
        if ($_REQUEST["inProcesso"]) {
            $arProcesso = preg_split( "/[^a-zA-Z0-9]/", $_REQUEST["inProcesso"] );
            $obConstrucao->obRProcesso->setCodigoProcesso ( $arProcesso[0] );
            $obConstrucao->obRProcesso->setExercicio      ( $arProcesso[1] );
        }
        foreach ($arChave as $key=>$value) {
            $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
            $inCodAtributo = $arChaves[0];
            if ( is_array($value) ) {
                $value = implode(",",$value);
            }
            $obConstrucao->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $value );
        }

        $obErro = $obConstrucao->incluirConstrucao();

        if ( !$obErro->ocorreu()) {
            SistemaLegado::alertaAviso($pgForm,"Condomínio: ".$_REQUEST["inCodigoCondominio"],"incluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    }
    break;
    case "alterar":
    if ($_REQUEST["stTipoVinculo"] == "dependente") { // se for inclusao de imovel
       $obRCIMUnidadeAutonoma = new RCIMUnidadeAutonoma( new RCIMImovel( new RCIMLote ) );
       $obRCIMUnidadeAutonoma->roRCIMImovel->setNumeroInscricao( $_REQUEST["inNumeroInscricao"] );
       $obErro = $obRCIMUnidadeAutonoma->roRCIMImovel->listarImoveis( $rsImovel );

       if ( sistemaLegado::comparaDatas($rsImovel->getCampo("dt_cadastro"),$_REQUEST['stDtConstrucao']) ) {
            $obErro->setDescricao('Data de construção ( '.$_REQUEST['stDtConstrucao']. ') inferior a data de cadastro do imóvel ('.$rsImovel->getCampo('dt_cadastro').')');
       }
       if ( !$obErro->ocorreu() ) {
           $obErro = $obRCIMUnidadeAutonoma->verificaUnidadeAutonoma( $rsRecordSet );
           if ( !$obErro->ocorreu() and !$rsRecordSet->eof() ) {
               $obErro = $obRCIMUnidadeAutonoma->consultarUnidadeAutonoma();
               if ( !$obErro->ocorreu() ) {
                   $obRCIMUnidadeAutonoma->addUnidadeDependente();
                   $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMConstrucaoOutros->setCodigoConstrucao( $_REQUEST["inCodigoConstrucao"] );
                   $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMConstrucaoOutros->setDataConstrucao  ( $_REQUEST["stDataContrucao"] );
                   //ATRIBUTOS
                   foreach ($arChave as $key=>$value) {
                       $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
                       $inCodAtributo = $arChaves[0];
                       if ( is_array($value) ) {
                           $value = implode(",",$value);
                       }
                       $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMConstrucaoOutros->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $value );
                   }
                   //$obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMConstrucaoOutros->setAreaConstruida( $_REQUEST["flAreaConstrucao"] );
                   //$obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMConstrucaoOutros->setDataConstrucao ($_REQUEST["stDtConstrucao"]);
                   if ($_REQUEST["inProcesso"]) {
                       $arProcesso = preg_split( "/[^a-zA-Z0-9]/", $_REQUEST["inProcesso"] );
                       $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMConstrucaoOutros->obRProcesso->setCodigoProcesso ( $arProcesso[0] );
                       $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMConstrucaoOutros->obRProcesso->setExercicio      ( $arProcesso[1] );
                   }
                   $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMConstrucaoOutros->setDescricao              ( $_REQUEST["stDescricaoConstrucao"] );
                   $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMConstrucaoOutros->setTimestampConstrucao    ( $_REQUEST["hdnTimestamp"]) ;
                   $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMConstrucaoOutros->setDataConstrucao         ( $_REQUEST["stDtConstrucao"]);
                   $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->setAreaUnidade( $_REQUEST["flAreaConstrucao"] );

                   $obErro = $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->alterarUnidadeDependente();
               }
           } else {
               $obErro->setDescricao( "Deve haver no mínimo uma edificação como unidade autônoma no imóvel informado!" );
           }
       }
       if ( !$obErro->ocorreu() ) {
           SistemaLegado::alertaAviso($pgList."?stAcao=alterar","Inscrição Imobiliária: ".$_REQUEST["inNumeroInscricao"],"alterar","aviso", Sessao::getId(), "../");
       } else {
           SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
       }
    } else { // caso seja do tipo condominio
        $obConstrucao =  new RCIMConstrucaoOutros;
        $obConstrucao->setCodigoConstrucao      ( $_REQUEST["inCodigoConstrucao"]   );
        $obConstrucao->setDataConstrucao        ( $_REQUEST["stDtConstrucao"]   );
        $obConstrucao->setAreaConstruida        ( $_REQUEST["flAreaConstrucao"]     );
        $obConstrucao->setTimestampConstrucao   ( $_REQUEST["hdnTimestamp"]         );
        $obConstrucao->obRCIMCondominio->setCodigoCondominio($_REQUEST["inCodigoCondominio"]);
        $obConstrucao->setDescricao($_REQUEST["stDescricaoConstrucao"]);
        if ($_REQUEST["inProcesso"]) {
            $arProcesso = preg_split( "/[^a-zA-Z0-9]/", $_REQUEST["inProcesso"] );
            $obConstrucao->setTimestampConstrucao         ($_REQUEST["hdnTimestamp"]);
            $obConstrucao->obRProcesso->setCodigoProcesso ( $arProcesso[0] );
            $obConstrucao->obRProcesso->setExercicio      ( $arProcesso[1] );
        }

        foreach ($arChave as $key=>$value) {
            $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
            $inCodAtributo = $arChaves[0];
            if ( is_array($value) ) {
                $value = implode(",",$value);
            }
            $obConstrucao->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $value );
        }

        $obErro = $obConstrucao->alterarConstrucao();

        if ( !$obErro->ocorreu()) {

            SistemaLegado::alertaAviso($pgList.$stLink."&stAcao=alterar&stTipoVinculo=condominio","Código construção: ".$_REQUEST["inCodigoConstrucao"],"incluir","aviso", Sessao::getId(), "../");
//           SistemaLegado::alertaAviso($pgList."?stAcao=alterar","Condomínio: ".$_REQUEST["inCodigoCondominio"],"alterar","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }

    }
    break;
    case "excluir":
    if ($_REQUEST["stTipoVinculo"] == "dependente") { // se for exclusao de imovel
        $obRCIMUnidadeAutonoma = new RCIMUnidadeAutonoma( new RCIMImovel( new RCIMLote ) );
        $obRCIMUnidadeAutonoma->roRCIMImovel->setNumeroInscricao( $_REQUEST["inNumeroInscricao"] );
        $obErro = $obRCIMUnidadeAutonoma->verificaUnidadeAutonoma( $rsRecordSet );
        if ( !$obErro->ocorreu() and !$rsRecordSet->eof() ) {
            $obErro = $obRCIMUnidadeAutonoma->consultarUnidadeAutonoma();
            if ( !$obErro->ocorreu() ) {
                $obRCIMUnidadeAutonoma->addUnidadeDependente();
                $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMConstrucaoOutros->setCodigoConstrucao( $_REQUEST["inCodigoConstrucao"] );
                $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMConstrucaoOutros->setDataConstrucao( $_REQUEST["stDtConstrucao"]);
                $obErro = $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMConstrucaoOutros->consultarConstrucao();
                if ( !$obErro->ocorreu() ) {
                    $obErro = $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->excluirUnidadeDependente();
                }
            }
        }
        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList.$stLink,"Código da construção:".$_REQUEST["inCodigoConstrucao"],"excluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::alertaAviso($pgList.$stLink,urlencode($obErro->getDescricao()),"n_excluir","erro",Sessao::getId(), "../");
        }
    } else { // se for exclusao condominio
        $obConstrucao =  new RCIMConstrucaoOutros;
        //$obConstrucao->setCodigoCondominio();
        $obConstrucao->obRCIMCondominio->setCodigoCondominio($_REQUEST["inCodigoCondominio"]);
        $obConstrucao->setCodigoConstrucao($_REQUEST["inCodigoConstrucao"]);
        $obConstrucao->setDataConstrucao($_REQUEST["stDtConstrucao"]);
        $obErro = $obConstrucao->excluirConstrucao();

        if ( !$obErro->ocorreu()) {
            SistemaLegado::alertaAviso($pgList.$stLink."&stAcao=exlcuir&stTipoVinculo=condominio","Código construção: ".$_REQUEST["inCodigoConstrucao"],"incluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    }
        break;

    case "reativar":
        $obRCIMConstrucaoOutros = new RCIMConstrucaoOutros;
        $obRCIMConstrucaoOutros->setCodigoConstrucao ( $_REQUEST["inCodigoConstrucao"] );
        $obRCIMConstrucaoOutros->setJustificativa ( $_REQUEST["stJustificativa"] );
        $obRCIMConstrucaoOutros->setJustificativaReativar ( $_REQUEST["stJustReat"] );
        $obRCIMConstrucaoOutros->setDataConstrucao ( $_REQUEST["stTimestamp"] );
        if ($_REQUEST["inProcesso"]) {
            $arProcesso = preg_split( "/[^a-z$obFormulario->addComponente      ( $obLblCodigoConstrucao    );A-Z0-9]/", $_REQUEST["inProcesso"] );
            $obRCIMConstrucaoOutros->obRProcesso->setCodigoProcesso ( $arProcesso[0] );
            $obRCIMConstrucaoOutros->obRProcesso->setExercicio      ( $arProcesso[1] );
        }

        $obErro = $obRCIMConstrucaoOutros->reativarConstrucao();
        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList,"Reativar construção concluído com sucesso! (Código da construção: ".$_REQUEST["inCodigoConstrucao"].")","reativar","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso("Falha ao reativar construção. (".urlencode($obErro->getDescricao()).")","n_reativar","erro");
        }
        break;

    case "baixar":
        $obRCIMConstrucaoOutros = new RCIMConstrucaoOutros;
        $obRCIMConstrucaoOutros->setCodigoConstrucao( $_REQUEST["inCodigoConstrucao"] );
        $obRCIMConstrucaoOutros->setJustificativa    ( $_REQUEST["stJustificativa"] );

        if ($_REQUEST["inProcesso"]) {
            $arProcesso = preg_split( "/[^a-z$obFormulario->addComponente      ( $obLblCodigoConstrucao    );A-Z0-9]/", $_REQUEST["inProcesso"] );
            $obRCIMConstrucaoOutros->obRProcesso->setCodigoProcesso ( $arProcesso[0] );
            $obRCIMConstrucaoOutros->obRProcesso->setExercicio      ( $arProcesso[1] );
        }

        $obErro = $obRCIMConstrucaoOutros->baixarConstrucao();
        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList,"Código da construção:".$_REQUEST["inCodigoConstrucao"],"baixar","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_baixar","erro");
        }
        break;

    case "historico":
        $obRCIMConstrucaoOutros = new RCIMConstrucaoOutros;
        $obRCIMConstrucaoOutros->setCodigoConstrucao ( $_REQUEST["inCodigoConstrucao"] );

        $arProcesso = preg_split( "/[^a-zA-Z0-9]/", $_REQUEST["inProcesso"]    );
        $obRCIMConstrucaoOutros->obRProcesso->setCodigoProcesso( $arProcesso[0] );
        $obRCIMConstrucaoOutros->obRProcesso->setExercicio     ( $arProcesso[1] );

        //ATRIBUTOS
        foreach ($arChave as $key=>$value) {
            $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
            $inCodAtributo = $arChaves[0];
            if ( is_array($value) ) {
                $value = implode(",",$value);
            }
            $obRCIMConstrucaoOutros->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $value );
        }
        $obErro = $obRCIMConstrucaoOutros->alterarCaracteristicas();
        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList,"Código da construção:".$_REQUEST["inCodigoConstrucao"],"alterar","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }
    break;
    case "reforma":
    if ($_REQUEST["stTipoVinculo"] == "dependente") { // se for inclusao de imovel
       $obRCIMUnidadeAutonoma = new RCIMUnidadeAutonoma( new RCIMImovel( new RCIMLote ) );
       $obRCIMUnidadeAutonoma->roRCIMImovel->setNumeroInscricao( $_REQUEST["inNumeroInscricao"] );
       $obErro = $obRCIMUnidadeAutonoma->verificaUnidadeAutonoma( $rsRecordSet );
       if ( !$obErro->ocorreu() and !$rsRecordSet->eof() ) {
           $obErro = $obRCIMUnidadeAutonoma->consultarUnidadeAutonoma();
           if ( !$obErro->ocorreu() ) {
               $obRCIMUnidadeAutonoma->addUnidadeDependente();
               $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMConstrucaoOutros->setCodigoConstrucao( $_REQUEST["inCodigoConstrucao"] );
               $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMConstrucaoOutros->setDataConstrucao  ( $_REQUEST["stDataContrucao"] );
               $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->setCodigoConstrucaoDependente($_REQUEST["inCodigoConstrucao"] );
               //ATRIBUTOS
               foreach ($arChave as $key=>$value) {
                   $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
                   $inCodAtributo = $arChaves[0];
                   if ( is_array($value) ) {
                       $value = implode(",",$value);
                   }
                   $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMConstrucaoOutros->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $value );
               }
               //$obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMConstrucaoOutros->setAreaConstruida(
               //$_REQUEST["flAreaConstrucao"] );
               //$obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMConstrucaoOutros->setDataConstrucao
               //($_REQUEST["stDtConstrucao"]);
               if ($_REQUEST["inProcesso"]) {
                   $arProcesso = preg_split( "/[^a-zA-Z0-9]/", $_REQUEST["inProcesso"] );
                   $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMConstrucaoOutros->obRProcesso->setCodigoProcesso ( $arProcesso[0] );
                   $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMConstrucaoOutros->obRProcesso->setExercicio ( $arProcesso[1] );
               }
               $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMConstrucaoOutros->setDescricao ( $_REQUEST["stDescricaoConstrucao"] );
               $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMConstrucaoOutros->setTimestampConstrucao ( $_REQUEST["hdnTimestamp"]) ;
               $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMConstrucaoOutros->setDataConstrucao ( $_REQUEST["stDtConstrucao"]);
               $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->setAreaUnidade( $_REQUEST["flAreaConstrucao"] );

               $obErro = $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->incluirReformaConstrucao();
           }
       } else {
           $obErro->setDescricao( "Deve haver no mínimo uma edificação como unidade autônoma no imóvel informado!" );
       }
       if ( !$obErro->ocorreu() ) {
           SistemaLegado::alertaAviso($pgList.$stLink."?stAcao=alterar","Inscrição Imobiliária: ".$_REQUEST["inNumeroInscricao"],"alterar","aviso", Sessao::getId(), "../");
       } else {
           SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
       }
    } else {
        $obConstrucao =  new RCIMConstrucaoOutros;
        $obConstrucao->setCodigoConstrucao( $_REQUEST["inCodigoConstrucao"] );
        $obConstrucao->setAreaConstruida($_REQUEST["flAreaConstrucao"]);
        $obConstrucao->setDescricao($_REQUEST["stDescricaoConstrucao"]);
        if ($_REQUEST["inProcesso"]) {
            $arProcesso = preg_split( "/[^a-zA-Z0-9]/", $_REQUEST["inProcesso"] );
            $obConstrucao->obRProcesso->setCodigoProcesso ( $arProcesso[0] );
            $obConstrucao->obRProcesso->setExercicio      ( $arProcesso[1] );
        }
        $obErro = $obConstrucao->incluirReforma();

        if ( !$obErro->ocorreu()) {
            SistemaLegado::alertaAviso($pgList.$stLink,"Codigo Construção: ".$_REQUEST["inCodigoConstrucao"],"incluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    }
    break;
}
?>
