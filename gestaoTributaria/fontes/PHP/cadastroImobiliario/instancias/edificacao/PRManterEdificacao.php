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
    * Página de processamento para o cadastro de edificação
    * Data de Criação   : 03/09/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
    * @author Desenvolvedor: Fábio Bertoldi Rodrigues

    * @ignore

    * $Id: PRManterEdificacao.php 62838 2015-06-26 13:02:49Z diogo.zarpelon $

    * Casos de uso: uc-05.01.11
*/

/*
$Log$
Revision 1.14  2006/09/18 10:30:30  fabio
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
$stPrograma    = "ManterEdificacao";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php?".Sessao::getId().$stLink;
$pgForm        = "FM".$stPrograma.".php";
$pgFormVinculo = "FM".$stPrograma."Vinculo.php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";
$pgFormConstrucao = "../construcao/FMManterConstrucaoVinculo.php";

include_once( $pgJs );

function alertaAvisoRedirect($location="", $objeto="", $tipo="n_incluir", $chamada="erro", $sessao, $caminho="", $func="")
{
    ;
    print '<script type="text/javascript">
                SistemaLegado::alertaAviso      ("'.$objeto.'","'.$tipo.'","'.$chamada.'","'.Sessao::getId().'","'.$caminho.'");
           </script>';
    session_regenerate_id();
    Sessao::setId("PHPSESSID=".session_id());
    $sessao->geraURLRandomica();
    Sessao::write('acao'  , "757");
    Sessao::write('modulo',  "12");
    $aux = explode("?",$location);
    $location = $aux[0]."?".Sessao::getId()."&".$aux[1];
    //Imprime um código javascript que redireciona o frame 'telaPrincipal'
    print '<script type="text/javascript">
                mudaMenu         ( "'.$func.'"     );
                mudaTelaPrincipal( "'.$location.'" );
           </script>';
}

$obRCIMEdificacao        = new RCIMEdificacao;
$obRCIMUnidadeAutonoma   = new RCIMUnidadeAutonoma( new RCIMImovel( new RCIMLote ) );
$obRCIMUnidadeDependente = new RCIMUnidadeDependente( $obRCIMUnidadeAutonoma );

$obAtributos = new MontaAtributos;
$obAtributos->setName      ( "AtributoEdificacao_" );
$obAtributos->recuperaVetor( $arChave    );
$hdnTipoUnidade =  $_REQUEST["hdnTipoUnidade"];
global $inCodConstrucao;

switch ($_REQUEST['stAcao']) {
    case "incluir":
     $obErro = new Erro;
     $obRCIMEdificacao->obRCIMImovel->setNumeroInscricao($_REQUEST['stImovelCond']);
     $obErro = $obRCIMEdificacao->obRCIMImovel->listarImoveisConsulta($rsImoveis);

     $arDataCadastro = explode( "-" , $rsImoveis->getCampo('dt_cadastro') );
     $dtCadastroBd = $arDataCadastro[2]."/".$arDataCadastro[1]."/".$arDataCadastro[0];

     if ( sistemaLegado::comparaDatas( $dtCadastroBd , $_REQUEST['stDtConstrucao'] ) ) {
        $obErro->setDescricao('Data de construção ('.$_REQUEST['stDtConstrucao'].') é menor que a data de cadastro do imóvel ('.$dtCadastroBd.')');
     }

     if ( !$obErro->ocorreu() ) {
         if ($_REQUEST["hdnVinculoEdificacao"] == "Condomínio") {
                //ATRIBUTOS
                foreach ($arChave as $key=>$value) {
                    $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
                    $inCodAtributo = $arChaves[0];
                    if ( is_array($value) ) {
                        $value = implode(",",$value);
                    }
                    $obRCIMEdificacao->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $value );
               }
                $avisoInclusao = "Codigo Condominio: ".$_REQUEST["stImovelCond"];
                $obRCIMEdificacao->setCodigoTipo                         ( $_REQUEST["inCodigoTipo"]            );
                $obRCIMEdificacao->setAreaConstruida                     ( $_REQUEST["flAreaConstruida"]        );
                $obRCIMEdificacao->obRCIMCondominio->setCodigoCondominio ( $_REQUEST["stImovelCond"]            );
                $obRCIMEdificacao->setDataConstrucao                     ( $_REQUEST["stDtConstrucao"]          );
                $arProcesso = preg_split( "/[^a-zA-Z0-9]/", $_REQUEST["inProcesso"] );
                $obRCIMEdificacao->obRProcesso->setCodigoProcesso        ( $arProcesso[0] );
                $obRCIMEdificacao->obRProcesso->setExercicio             ( $arProcesso[1] );
                if ($_REQUEST["hdnCodigoConstrucao"] == "") {
                    $obErro = $obRCIMEdificacao->incluirEdificacao();
                } else {
                    $obRCIMEdificacao->setCodigoConstrucao( $_REQUEST["hdnCodigoConstrucao"] );
                }
                $inCodConstrucao = $obRCIMEdificacao->getCodigoConstrucao();
            } elseif ($_REQUEST["hdnVinculoEdificacao"] == "Imóvel") {
                $obRCIMEdificacao->setDataConstrucao    ( $_REQUEST['stDtConstrucao']);
                $avisoInclusao = "Inscrição Imobiliária: ".$_REQUEST["stImovelCond"];
                if ($hdnTipoUnidade == "Autônoma") {
                   //ATRIBUTOS
                    foreach ($arChave as $key=>$value) {
                        $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
                        $inCodAtributo = $arChaves[0];
                        if ( is_array($value) ) {
                            $value = implode(",",$value);
                        }
                        $obRCIMUnidadeAutonoma->obRCIMEdificacao->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $value );
                    }
                    $obRCIMUnidadeAutonoma->obRCIMEdificacao->setCodigoConstrucao               ( $_REQUEST["hdnCodigoConstrucao"]  );
                    $obRCIMUnidadeAutonoma->obRCIMEdificacao->setCodigoTipo                     ( $_REQUEST["inCodigoTipo"]         );
                    $obRCIMUnidadeAutonoma->obRCIMEdificacao->setAreaConstruida                 ( $_REQUEST["hdnAreaTotal"]         );
                    $obRCIMUnidadeAutonoma->obRCIMEdificacao->setDataConstrucao                 ( $_REQUEST["stDtConstrucao"]       );
                    $arProcesso = preg_split( "/[^a-zA-Z0-9]/", $_REQUEST["inProcesso"] );
                    $obRCIMUnidadeAutonoma->obRCIMEdificacao->obRProcesso->setCodigoProcesso    ( $arProcesso[0]                    );
                    $obRCIMUnidadeAutonoma->obRCIMEdificacao->obRProcesso->setExercicio         ( $arProcesso[1]                    );
                    $obRCIMUnidadeAutonoma->roRCIMImovel->setNumeroInscricao                    ( $_REQUEST["stImovelCond"]         );
                    $obRCIMUnidadeAutonoma->setAreaUnidade                                      ( $_REQUEST["flAreaUnidade"]        );
                    $obRCIMUnidadeAutonoma->roRCIMImovel->recuperaDataLoteImovel();
                    $dtLote         = explode( "/" , $obRCIMUnidadeAutonoma->roRCIMImovel->roRCIMLote->getDataInscricao() );
                    $arDtConstrucao = explode( "/" , $_REQUEST["stDtConstrucao"]                                          );
                    $dtLote        = $dtLote[2].$dtLote[1].$dtLote[0];
                    $dtConstrucao  = $arDtConstrucao[2].$arDtConstrucao[1].$arDtConstrucao[0];
                    if ($dtConstrucao < $dtLote) {
                        $obErro->setDescricao( "A Data de Construção deve ser superior a Data de Inscrição do Lote: ".$obRCIMUnidadeAutonoma->roRCIMImovel->roRCIMLote->getDataInscricao() );
                    }
                    if ( !$obErro->ocorreu() ) {
                        $obErro = $obRCIMUnidadeAutonoma->incluirUnidadeAutonoma();
                        $inCodConstrucao = $obRCIMUnidadeAutonoma->obRCIMEdificacao->getCodigoConstrucao();
                    }
                } elseif ($hdnTipoUnidade == "Dependente") {
                    $obRCIMUnidadeAutonoma->addUnidadeDependente();
                    //ATRIBUTOS
                    foreach ($arChave as $key=>$value) {
                        $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
                        $inCodAtributo = $arChaves[0];
                        if ( is_array($value) ) {
                            $value = implode(",",$value);
                        }
                        $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMEdificacao->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $value );
                    }
                    $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMEdificacao->setCodigoTipo                  ( $_REQUEST["inCodigoTipo"]                 );
                    $arProcesso = preg_split( "/[^a-zA-Z0-9]/", $_REQUEST["inProcesso"] );
                    $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMEdificacao->obRProcesso->setCodigoProcesso ( $arProcesso[0]                            );
                    $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMEdificacao->obRProcesso->setExercicio      ( $arProcesso[1]                            );
                    $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMEdificacao->setDataConstrucao              ( $_REQUEST["stDtConstrucao"]               );
                    $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->setAreaUnidade                                   ( $_REQUEST["flAreaUnidade"]                );
                    $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMEdificacao->setAreaConstruida              ( $_REQUEST["hdnAreaTotal"]                 );
                    $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMEdificacao->setTipoUnidade                 ( 'Dependente'                              );
                    $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMEdificacao->setUnidadeAutonoma             ( $_REQUEST["hdnCodigoConstrucaoAutonoma"]  );
                    $obRCIMUnidadeAutonoma->roRCIMImovel->setNumeroInscricao                                            ( $_REQUEST["stImovelCond"]                 );
                    $obRCIMUnidadeAutonoma->obRCIMEdificacao->setCodigoConstrucao                                       ( $_REQUEST["hdnCodigoConstrucaoAutonoma"]  );
                    $obRCIMUnidadeAutonoma->obRCIMEdificacao->setCodigoTipo                                             ( $_REQUEST["hdnCodigoTipoAutonoma"]        );
                    $obRCIMUnidadeAutonoma->roRCIMImovel->recuperaDataLoteImovel();
                    $dtLote         = explode( "/" , $obRCIMUnidadeAutonoma->roRCIMImovel->roRCIMLote->getDataInscricao() );
                    $arDtConstrucao = explode( "/" , $_REQUEST["stDtConstrucao"]                                          );
                    $dtLote        = $dtLote[2].$dtLote[1].$dtLote[0];
                    $dtConstrucao  = $arDtConstrucao[2].$arDtConstrucao[1].$arDtConstrucao[0];
                    if ($dtConstrucao < $dtLote) {
                        $obErro->setDescricao( "A Data de Construção deve ser superior a Data de Inscrição do Lote: ".$obRCIMUnidadeAutonoma->roRCIMImovel->roRCIMLote->getDataInscricao() );
                    }
                    if ( !$obErro->ocorreu() ) {
                        $obErro = $obRCIMUnidadeAutonoma->salvarUnidadesDependentes();
                        $inCodConstrucao = $obRCIMUnidadeAutonoma->obRCIMEdificacao->getCodigoConstrucao();
                    }
                }
            }
        }
        if ( !$obErro->ocorreu() ) {
            if ($_REQUEST["stProximaPagina"] == "edificacao") {
                SistemaLegado::alertaAviso($pgFormVinculo."?stAcao=incluir&boVinculoEdificacao=".$_REQUEST["hdnVinculoEdificacao"]."&boAdicionarEdificacao=".$_REQUEST["hdnAdicionarEdificacao"],$avisoInclusao,"incluir","aviso", Sessao::getId(), "../");
            } elseif ($_REQUEST["stProximaPagina"] == "unidade") {
                SistemaLegado::alertaAviso($pgFormVinculo."?boMesma=true&stAcao=incluir&boVinculoEdificacao=".$_REQUEST["hdnVinculoEdificacao"]."&boAdicionarEdificacao=".$_REQUEST["hdnAdicionarEdificacao"]."&inCodigoConstrucao=".$inCodConstrucao."&flAreaConstruida=".$_REQUEST["flAreaConstruida"]."&flAreaTotal=".$_REQUEST["hdnAreaTotal"],$avisoInclusao,"incluir","aviso", Sessao::getId(), "../");
            } else {
                if ($_REQUEST["hdnVinculoEdificacao"] == "Imóvel") {
                    $boVinculoConstrucao = "imovel";
                    $stUrl = "&inNumeroInscricao=".$_REQUEST["stImovelCond"];
                } else {
                    $boVinculoConstrucao = "condominio";
                    $stUrl = "&inCodigoCondominio=".$_REQUEST["stImovelCond"];
                }
                SistemaLegado::alertaAviso($pgFormVinculo."?stAcao=incluir&boVinculoEdificacao=".$_REQUEST["hdnVinculoEdificacao"]."&boAdicionarEdificacao=".$_REQUEST["hdnAdicionarEdificacao"],$avisoInclusao,"incluir","aviso", Sessao::getId(), "../");
                alertaAvisoRedirect($pgFormConstrucao."?stAcao=incluir&boVinculoConstrucao=".$boVinculoConstrucao.$stUrl,$avisoInclusao,"incluir","aviso",Sessao::getId(),"../","184" );
            }
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    break;
    case "alterar":

     $obRCIMEdificacao->obRCIMImovel->setNumeroInscricao($_REQUEST['stImovelCond']);
     $obErro = $obRCIMEdificacao->obRCIMImovel->listarImoveisConsulta($rsImoveis);

     $arDataCadastro = explode( "-" , $rsImoveis->getCampo('dt_cadastro') );
     $dtCadastroBd = $arDataCadastro[2]."/".$arDataCadastro[1]."/".$arDataCadastro[0];

     if ( sistemaLegado::comparaDatas( $dtCadastroBd , $_REQUEST['stDtConstrucao'] ) ) {
        $obErro->setDescricao('Data de construção ('.$_REQUEST['stDtConstrucao'].') é menor que a data de cadastro do imóvel ('.$dtCadastroBd.')');
     }

     if ( !$obErro->ocorreu() ) {
        if ($_REQUEST["hdnVinculoEdificacao"] == "Condomínio") {
          //ATRIBUTOS
            foreach ($arChave as $key=>$value) {
                $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
                $inCodAtributo = $arChaves[0];
                if ( is_array($value) ) {
                    $value = implode(",",$value);
                }
                $obRCIMEdificacao->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $value );
            }
            $obRCIMEdificacao->setCodigoConstrucao                   ( $_REQUEST["hdnCodigoConstrucao"]     );
            $obRCIMEdificacao->setCodigoTipo                         ( $_REQUEST["hdnCodigoTipo"]           );
            $obRCIMEdificacao->setAreaConstruida                     ( $_REQUEST["flAreaConstruida"]        );
            $obRCIMEdificacao->setTimestampConstrucao                ( $_REQUEST["hdnTimestamp"]            );
            $obRCIMEdificacao->setDataConstrucao                     ( $_REQUEST["stDtConstrucao"]          );
            $obRCIMEdificacao->obRCIMCondominio->setCodigoCondominio ( $_REQUEST["hdnImovelCond"]           );
            $arProcesso = preg_split( "/[^a-zA-Z0-9]/", $_REQUEST["inCodigoProcesso"] );
            $obRCIMEdificacao->obRProcesso->setCodigoProcesso        ( $arProcesso[0] );
            $obRCIMEdificacao->obRProcesso->setExercicio             ( $arProcesso[1] );
            $obErro = $obRCIMEdificacao->alterarEdificacao();
        } elseif ($_REQUEST["hdnVinculoEdificacao"] == "Imóvel") {
            $avisoInclusao = "Inscrição Imobiliária: ".$_REQUEST["stImovelCond"];
            if ($_REQUEST['hdnTipoUnidade'] == "Autônoma") {
                //ATRIBUTOS
                foreach ($arChave as $key=>$value) {
                    $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
                    $inCodAtributo = $arChaves[0];
                    if ( is_array($value) ) {
                        $value = implode(",",$value);
                    }
                    $obRCIMUnidadeAutonoma->obRCIMEdificacao->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $value );
                }
                $obRCIMUnidadeAutonoma->obRCIMEdificacao->setCodigoConstrucao       ( $_REQUEST["hdnCodigoConstrucao"]          );
                $obRCIMUnidadeAutonoma->obRCIMEdificacao->setCodigoTipo             ( $_REQUEST["hdnCodigoTipo"]                );
                $obRCIMUnidadeAutonoma->obRCIMEdificacao->setAreaConstruida         ( $_REQUEST["hdnAreaTotal"]                 );
                $obRCIMUnidadeAutonoma->obRCIMEdificacao->setDataConstrucao         ( $_REQUEST["stDtConstrucao"]               );
                $obRCIMUnidadeAutonoma->obRCIMEdificacao->setTimestampConstrucao    ( $_REQUEST["hdnTimestamp"]                 );
                $obRCIMUnidadeAutonoma->setTimestampUnidadeAutonoma                 ( $_REQUEST["hdnTimestampUnidadeAutonoma"]  );
                $arProcesso =  preg_split( "/[^a-zA-Z0-9]/", $_REQUEST["inCodigoProcesso"] );
                $obRCIMUnidadeAutonoma->obRCIMEdificacao->obRProcesso->setCodigoProcesso ( $arProcesso[0] );
                $obRCIMUnidadeAutonoma->obRCIMEdificacao->obRProcesso->setExercicio      ( $arProcesso[1] );
                $obRCIMUnidadeAutonoma->roRCIMImovel->setNumeroInscricao                 ( $_REQUEST["stImovelCond"] );
                $obRCIMUnidadeAutonoma->setAreaUnidade                                   ( $_REQUEST["flAreaUnidade"]);
                $obErro = $obRCIMUnidadeAutonoma->alterarUnidadeAutonoma();
            } elseif ($_REQUEST['hdnTipoUnidade'] == "Dependente") {
                $obRCIMUnidadeAutonoma->addUnidadeDependente();
                //ATRIBUTOS
                foreach ($arChave as $key=>$value) {
                $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
                    $inCodAtributo = $arChaves[0];
                    if ( is_array($value) ) {
                        $value = implode(",",$value);
                    }
                    $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMEdificacao->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $value );
                }
                $obRCIMUnidadeAutonoma->obRCIMEdificacao->setCodigoConstrucao  ( $_REQUEST["hdnCodigoConstrucaoAutonoma"] );
                $obRCIMUnidadeAutonoma->obRCIMEdificacao->setCodigoTipo        ( $_REQUEST["hdnCodigoTipoAutonoma"] );

                $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMEdificacao->setCodigoConstrucao    ( $_REQUEST["hdnCodigoConstrucao"]  );
                $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMEdificacao->setCodigoTipo          ( $_REQUEST["hdnCodigoTipo"]        );
                $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMEdificacao->setTimestampConstrucao ( $_REQUEST["hdnTimestamp"]         );
                $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMEdificacao->setDataConstrucao      ( $_REQUEST["stDtConstrucao"]       );

                $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMEdificacao->setAreaConstruida              ( $_REQUEST["hdnAreaTotal"]                 );
                $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMEdificacao->setTipoUnidade                 ( 'Dependente'                              );
                $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMEdificacao->setUnidadeAutonoma             ( $_REQUEST["hdnCodigoConstrucaoAutonoma"]  );

                $arProcesso =  preg_split( "/[^a-zA-Z0-9]/", $_REQUEST["inCodigoProcesso"] );
                $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMEdificacao->obRProcesso->setCodigoProcesso ( $arProcesso[0] );
                $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMEdificacao->obRProcesso->setExercicio      ( $arProcesso[1] );
                $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->setAreaUnidade( $_REQUEST["flAreaUnidade"] );
                $obRCIMUnidadeAutonoma->roRCIMImovel->setNumeroInscricao         ( $_REQUEST["stImovelCond"] );
                $obErro = $obRCIMUnidadeAutonoma->editarUnidadesDependentes();
            }
        }
    }
    if ( !$obErro->ocorreu() ) {
        SistemaLegado::alertaAviso($pgList."?stAcao=alterar&hdnVinculoEdificacao=".$_REQUEST["hdnVinculoEdificacao"],"Codigo da Edificação: ".$_REQUEST["hdnCodigoConstrucao"],"alterar","aviso", Sessao::getId(), "../");
    } else {
        SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
    }
    break;
    case "excluir":
     $obErro = new Erro;
      if ($_REQUEST["hdnVinculoEdificacao"] == "Condomínio") {
            //ATRIBUTOS
            foreach ($arChave as $key=>$value) {
                $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
                $inCodAtributo = $arChaves[0];
                if ( is_array($value) ) {
                    $value = implode(",",$value);
                }
                $obRCIMEdificacao->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $value );
            }
            $obRCIMEdificacao->setCodigoConstrucao                   ( $_REQUEST["inCodigoConstrucao"]      );
            $obRCIMEdificacao->setDataConstrucao                     ( $_REQUEST["stDtConstrucao"]          );
            $obRCIMEdificacao->setCodigoTipo                         ( $_REQUEST["inCodigoTipo"]            );
            $obRCIMEdificacao->setAreaConstruida                     ( $_REQUEST["flAreaConstruida"]        );
            $obRCIMEdificacao->obRCIMCondominio->setCodigoCondominio ( $_REQUEST["stImovelCond"]            );
            $obRCIMEdificacao->obRProcesso->setCodigoProcesso        ( $_REQUEST["inCodigoProcesso"]        );
            $obRCIMEdificacao->obRProcesso->sestTipoVinculotExercicio             ( $_REQUEST["hdnAnoExercicioProcesso"] );
            $obErro = $obRCIMEdificacao->excluirEdificacao();
        } elseif ($_REQUEST["hdnVinculoEdificacao"] == "Autônoma" OR $_REQUEST["hdnVinculoEdificacao"] == "Dependente") {
            $avisoInclusao = "Inscrição Imobiliária: ".$_REQUEST["stImovelCond"];
            if ($_REQUEST['stTipoUnidade'] == "Autônoma") {

                //ATRIBUTOS
                foreach ($arChave as $key=>$value) {
                    $arChaves = preg_split( "[^a-zA-Z0-9]", $key );
                    $inCodAtributo = $arChaves[0];
                    if ( is_array($value) ) {
                        $value = implode(",",$value);
                    }
                    $obRCIMUnidadeAutonoma->obRCIMEdificacao->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $value );
                }
                $obRCIMUnidadeAutonoma->obRCIMEdificacao->setCodigoConstrucao            ( $_REQUEST["inCodigoConstrucao"]);
                $obRCIMUnidadeAutonoma->obRCIMEdificacao->setDataConstrucao              ( $_REQUEST["stDtConstrucao"]);
                $arProcesso =  preg_split( "/[^a-zA-Z0-9]/", $_REQUEST["inCodigoProcesso"] );
                $obRCIMUnidadeAutonoma->obRCIMEdificacao->obRProcesso->setCodigoProcesso ( $arProcesso[0] );
                $obRCIMUnidadeAutonoma->obRCIMEdificacao->obRProcesso->setExercicio      ( $arProcesso[1] );
                $obRCIMUnidadeAutonoma->obRCIMEdificacao->setAreaConstruida              ( $_REQUEST["hdnAreaTotal"] );
                $obRCIMUnidadeAutonoma->obRCIMEdificacao->obRProcesso->setCodigoProcesso ( $_REQUEST["inCodigoProcesso"] );
                $obRCIMUnidadeAutonoma->obRCIMEdificacao->obRProcesso->setExercicio      ( $_REQUEST["hdnAnoExercicioProcesso"] );
                $obRCIMUnidadeAutonoma->roRCIMImovel->setNumeroInscricao                 ( $_REQUEST["stImovelCond"]  );
                $obRCIMUnidadeAutonoma->setAreaUnidade                                   ( $_REQUEST["flAreaUnidade"] );
                $obErro = $obRCIMUnidadeAutonoma->excluirUnidadeAutonoma();

            } elseif ($_REQUEST['stTipoUnidade'] == "Dependente") {
                $obRCIMUnidadeAutonoma->addUnidadeDependente();
                //ATRIBUTOS
                foreach ($arChave as $key=>$value) {
                    $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
                    $inCodAtributo = $arChaves[0];
                    if ( is_array($value) ) {
                        $value = implode(",",$value);
                    }
                    $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMEdificacao->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $value );
                }
                $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMEdificacao->setCodigoConstrucao ( $_REQUEST["inCodigoConstrucao"]                 );
                $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMEdificacao->setDataConstrucao   ( $_REQUEST["stDtConstrucao"]                     );
                $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMEdificacao->setCodigoTipo ( $_REQUEST["inCodigoTipo"]                             );
                $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMEdificacao->obRProcesso->setCodigoProcesso ( $_REQUEST["inCodigoProcesso"]        );
                $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMEdificacao->obRProcesso->setExercicio      ( $_REQUEST["hdnAnoExercicioProcesso"] );
                $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->setAreaUnidade        ( $_REQUEST["flAreaUnidade"] );
                $obRCIMUnidadeAutonoma->roRCIMImovel->setNumeroInscricao                 ( $_REQUEST["stImovelCond"]  );
                $obErro = $obRCIMUnidadeAutonoma->removerUnidadesDependentes();
            }
        }
        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList."?stAcao=excluir","Codigo da Edificação: ".$_REQUEST["inCodigoConstrucao"],"excluir","aviso", Sessao::getId(), "../");
        } else {
            if ($obErro->ocorreu()) {
                $obErro->setDescricao("Edificação possui vínculos.");
            }
            SistemaLegado::alertaAviso($pgList,urlencode($obErro->getDescricao()),"n_excluir","erro",Sessao::getId(), "../");
//            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_excluir","erro");
        }
        break;

    case "reativar":
        $obErro = new Erro();
        if ($_REQUEST["hdnTipoUnidade"] == "Autônoma") {
            $obRCIMUnidadeAutonoma->roRCIMImovel->setNumeroInscricao ( $_REQUEST["stImovelCond"] );
            $obRCIMUnidadeAutonoma->obRCIMEdificacao->setCodigoConstrucao ( $_REQUEST["hdnCodigoConstrucao"] );
            $obRCIMUnidadeAutonoma->obRCIMEdificacao->setCodigoTipo  ( $_REQUEST["hdnCodigoTipo"] );
            $obRCIMUnidadeAutonoma->setJustificativa ( $_REQUEST["stJustificativa"] );
            $obRCIMUnidadeAutonoma->setJustificativaReativar ( $_REQUEST["stJustReat"] );
            $obRCIMUnidadeAutonoma->setTimestampBaixaUnidade ( $_REQUEST["stTimestamp"] );

            $obErro = $obRCIMUnidadeAutonoma->reativarUnidadeAutonoma();
        } else {
            $obRCIMUnidadeDependente->roRCIMUnidadeAutonoma->roRCIMImovel->setNumeroInscricao( $_REQUEST["stImovelCond"] );
            $obRCIMUnidadeDependente->roRCIMUnidadeAutonoma->obRCIMEdificacao->setCodigoConstrucao( $_REQUEST["hdnCodigoConstrucaoAutonoma"] );
            $obRCIMUnidadeDependente->roRCIMUnidadeAutonoma->obRCIMEdificacao->setCodigoTipo( $_REQUEST["hdnCodigoTipoAutonoma"] );
            $obRCIMUnidadeDependente->obRCIMEdificacao->setCodigoConstrucao( $_REQUEST["hdnCodigoConstrucao"] );
            $obRCIMUnidadeDependente->setJustificativa( $_REQUEST["stJustificativa"] );
            $obRCIMUnidadeDependente->setJustificativaReativar ( $_REQUEST["stJustReat"] );
            $obRCIMUnidadeDependente->setTipoConstrucao ( "Edificacao" ) ;
            $obRCIMUnidadeDependente->setTimestampBaixaUnidade ( $_REQUEST["stTimestamp"] );

            $obErro = $obRCIMUnidadeDependente->reativarUnidadeDependente();
        }

        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList."?stAcao=reativar","Reativar edificação concluído com sucesso. (Codigo da Edificação: ".$_REQUEST["hdnCodigoConstrucao"].")","reativar","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso("Falha ao reativar edificação. (".urlencode($obErro->getDescricao()).")","n_reativar","erro");
        }
        break;

    case "baixar":
        $obErro = new Erro();
        if ($_REQUEST["hdnTipoUnidade"] == "Autônoma") {
            if ($_REQUEST["hdnListaDependentes"] and  $_REQUEST["boUnidadeSelecionada"] == "") {
                $obErro->setDescricao("Unidade dependente não selecionada.");
            } else {
                $obRCIMUnidadeAutonoma->roRCIMImovel->setNumeroInscricao        ( $_REQUEST["stImovelCond"]                 );
                $obRCIMUnidadeAutonoma->obRCIMEdificacao->setCodigoConstrucao   ( $_REQUEST["hdnCodigoConstrucao"]          );
                $obRCIMUnidadeAutonoma->obRCIMEdificacao->setCodigoTipo         ( $_REQUEST["hdnCodigoTipo"]                );
                $obRCIMUnidadeAutonoma->setJustificativa                        ( $_REQUEST["stJustificativa"]              );
                $arNovaUnidade = explode("||",$_REQUEST["boUnidadeSelecionada"]);
                $flNovaArea = $arNovaUnidade[1];
//                $obRCIMUnidadeAutonoma->obRCIMEdificacao->setAreaConstruida     ( $_REQUEST["hdnAreaConstruida"]            );
                $obRCIMUnidadeAutonoma->obRCIMEdificacao->setAreaConstruida     ( $flNovaArea                               );
                $obRCIMUnidadeAutonoma->obRCIMEdificacao->setDataConstrucao     ( $_REQUEST["stDtConstrucao"]               );
                foreach ($_REQUEST as $key=>$value) {
                    if (strstr($key,'boUnidadeSelecionada') and $$key != "") {
                        $arNovaUnidade = explode("||",$value);
                        $obRCIMUnidadeAutonoma->setUnidadeSelecionada           ( $arNovaUnidade[0] );
                        $obRCIMUnidadeAutonoma->setCodigoTipoNovo ( $arNovaUnidade[2] );
                    }
                }
                $obErro = $obRCIMUnidadeAutonoma->baixarUnidadeAutonomaEdificacao();
            }
        } else {
            $obRCIMUnidadeDependente->roRCIMUnidadeAutonoma->roRCIMImovel->setNumeroInscricao( $_REQUEST["stImovelCond"] );
            $obRCIMUnidadeDependente->roRCIMUnidadeAutonoma->obRCIMEdificacao->setCodigoConstrucao( $_REQUEST["hdnCodigoConstrucaoAutonoma"] );
            $obRCIMUnidadeDependente->roRCIMUnidadeAutonoma->obRCIMEdificacao->setCodigoTipo( $_REQUEST["hdnCodigoTipoAutonoma"] );
//            $obRCIMUnidadeDependente->roRCIMUnidadeAutonoma->obRCIMEdificacao->setCodigoTipo( $_REQUEST["hdnCodigoTipo"] );
            $obRCIMUnidadeDependente->obRCIMEdificacao->setCodigoConstrucao( $_REQUEST["hdnCodigoConstrucao"] );
            $obRCIMUnidadeDependente->setJustificativa( $_REQUEST["stJustificativa"] );
            $obRCIMUnidadeDependente->setTipoConstrucao ( "Edificacao" ) ;
            $obErro = $obRCIMUnidadeDependente->baixarUnidadeDependente();
        }
        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList."?stAcao=baixar","Codigo da Edificação: ".$_REQUEST["hdnCodigoConstrucao"],"baixar","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_baixar","erro");
        }
        break;

    case "historico":
         $obRCIMEdificacao->setCodigoConstrucao ( $_REQUEST["hdnCodigoConstrucao"] );
         $obRCIMEdificacao->setCodigoTipo       ( $_REQUEST["hdnCodigoTipo"]       );

         $arProcesso = preg_split( "/[^a-zA-Z0-9]/", $_REQUEST["inProcesso"]    );
         $obRCIMEdificacao->obRProcesso->setCodigoProcesso( $arProcesso[0] );
         $obRCIMEdificacao->obRProcesso->setExercicio     ( $arProcesso[1] );

        //ATRIBUTOS
        foreach ($arChave as $key=>$value) {
            $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
            $inCodAtributo = $arChaves[0];
            if ( is_array($value) ) {
                $value = implode(",",$value);
            }
            $obRCIMEdificacao->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $value );
        }
        $obErro = $obRCIMEdificacao->alterarCaracteristicas();
        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList,"Codigo da Edificação: ".$_REQUEST["hdnCodigoConstrucao"],"alterar","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }
    break;
    case "reforma":
        $obErro = new Erro;
        if ($_REQUEST["hdnVinculoEdificacao"] == "Condomínio") {
            //ATRIBUTOS
            foreach ($arChave as $key=>$value) {
                $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
                $inCodAtributo = $arChaves[0];
                if ( is_array($value) ) {
                    $value = implode(",",$value);
                }
                $obRCIMEdificacao->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $value );
           }
            // define aviso padrao para inclusao
            $avisoInclusao = "Codigo Condominio: ".$_REQUEST["stImovelCond"];
            // define area construida para objeto Regra de Edificação
            $obRCIMEdificacao->setCodigoConstrucao                   ( $_REQUEST["inCodigoConstrucao"]  );
            $obRCIMEdificacao->setAreaConstruida                     ( $_REQUEST["flAreaUnidade"]    );
            // separa processo/exericio em dois(array)
            $arProcesso = preg_split( "/[^a-zA-Z0-9]/", $_REQUEST["inProcesso"] );
            $obRCIMEdificacao->obRProcesso->setCodigoProcesso        ( $arProcesso[0] );
            $obRCIMEdificacao->obRProcesso->setExercicio             ( $arProcesso[1] );
            // se campo hidden Codigo Construção nao estiver preenchido, inclui reforma!
            $obErro = $obRCIMEdificacao->incluirReforma();

        } elseif ($_REQUEST["hdnVinculoEdificacao"] == "Imóvel") {
            $avisoInclusao = "Incluida Reforma: ".$_REQUEST["stImovelCond"];
            if ($_REQUEST['stTipoUnidade'] == "Autônoma") {
                //ATRIBUTOS
                foreach ($arChave as $key=>$value) {
                    $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
                    $inCodAtributo = $arChaves[0];
                    if ( is_array($value) ) {
                        $value = implode(",",$value);
                    }
                    $obRCIMUnidadeAutonoma->obRCIMEdificacao->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $value );
                }
                $obRCIMUnidadeAutonoma->obRCIMEdificacao->setCodigoConstrucao               ( $_REQUEST["inCodigoConstrucao"]       );
                $obRCIMUnidadeAutonoma->obRCIMEdificacao->setCodigoTipo                     ( $_REQUEST["hdnCodigoTipo"]            );
                $obRCIMUnidadeAutonoma->obRCIMEdificacao->setAreaConstruida                 ( $_REQUEST["hdnAreaTotal"]     );
                $arProcesso = preg_split( "/[^a-zA-Z0-9]/", $_REQUEST["inProcesso"] );
                $obRCIMUnidadeAutonoma->obRProcesso->setCodigoProcesso                      ( $arProcesso[0]                        );
                $obRCIMUnidadeAutonoma->obRProcesso->setExercicio                           ( $arProcesso[1]                        );
                $obRCIMUnidadeAutonoma->roRCIMImovel->setNumeroInscricao                    ( $_REQUEST["stImovelCond"]             );
                $obRCIMUnidadeAutonoma->setAreaUnidade                                      ( $_REQUEST["flAreaUnidade"]            );
                $obErro = $obRCIMUnidadeAutonoma->incluirReforma();
            } elseif ($_REQUEST['stTipoUnidade'] == "Dependente") {
                $obRCIMUnidadeDependente->setNumeroInscricao            ( $_REQUEST["stImovelCond"          ]   );
                $obRCIMUnidadeDependente->setCodigoTipo                 ( $_REQUEST["hdnCodigoTipo"         ]   );
                $obRCIMUnidadeDependente->setCodigoConstrucao           ( $_REQUEST["hdnCodigoConstrucaoAut"]   );
                $obRCIMUnidadeDependente->setCodigoConstrucaoDependente ( $_REQUEST["inCodigoConstrucao"    ]   );
                $obRCIMUnidadeDependente->setAreaUnidade                ( $_REQUEST["flAreaUnidade"         ]   );
                $arProcesso = preg_split( "/[^a-zA-Z0-9]/", $_REQUEST["inProcesso"] );
                $obRCIMUnidadeDependente->obRProcesso->setCodigoProcesso    ( $arProcesso[0]                    );
                $obRCIMUnidadeDependente->obRProcesso->setExercicio         ( $arProcesso[1]                    );
                $obErro = $obRCIMUnidadeDependente->incluirReforma();
            }
        }
        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList,"Codigo da Edificação: ".$_REQUEST["inCodigoConstrucao"],"alterar","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }
    break;
}
?>
