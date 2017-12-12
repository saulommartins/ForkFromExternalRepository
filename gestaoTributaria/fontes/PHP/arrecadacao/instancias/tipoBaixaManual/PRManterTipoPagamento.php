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
    * Pagina de processamento para Edificação
    * Data de Criação   : 03/09/2004
    * @author Analista: Ricardo Lopes
    * @author Desenvolvedor: Fábio Bertoldi Rodrigues
    * @package URBEM
    * @subpackage Regra
    * @ignore

    * $Id: PRManterTipoPagamento.php 63839 2015-10-22 18:08:07Z franver $

    * Casos de uso: uc-05.03.09
*/

/*
$Log$
Revision 1.4  2006/09/15 11:19:33  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GT_CIM_NEGOCIO."RCIMEdificacao.class.php"      );
include_once( CAM_GT_CIM_NEGOCIO."RCIMUnidadeAutonoma.class.php" );

$stAcao = $request->get('stAcao');

//MANTEM O FILTRO E A PAGINACAO
$link = Sessao::read( "link" );
$stLink = "&pg=".$link["pg"]."&pos=".$link["pos"]."&stAcao=".$stAcao;

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
    $aux = explode("?",$location);
    $location = $aux[0]."?".Sessao::getId()."&".$aux[1];
    //Imprime um código javascript que redireciona o frame 'telaPrincipal'
    print '<script type="text/javascript">
                sistemaLegado::alertaAviso      ( "'.$objeto.'","'.$tipo.'","'.$chamada.'","'.Sessao::getId().'","'.$caminho.'");
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

switch ($stAcao) {
    case "incluir":
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
            $avisoInclusao = "Codigo Condominio: ".$_REQUEST["stImovelCond"];
            $obRCIMEdificacao->setCodigoTipo                         ( $_REQUEST["inCodigoTipo"]            );
            $obRCIMEdificacao->setAreaConstruida                     ( $_REQUEST["flAreaConstruida"]        );
            $obRCIMEdificacao->obRCIMCondominio->setCodigoCondominio ( $_REQUEST["stImovelCond"]            );
            $arProcesso = preg_split( "/[^a-zA-Z0-9]/", $_REQUEST["inCodigoProcesso"] );
            $obRCIMEdificacao->obRProcesso->setCodigoProcesso        ( $arProcesso[0] );
            $obRCIMEdificacao->obRProcesso->setExercicio             ( $arProcesso[1] );
            if ($_REQUEST["hdnCodigoConstrucao"] == "") {
                $obErro = $obRCIMEdificacao->incluirEdificacao();
            } else {
                $obRCIMEdificacao->setCodigoConstrucao( $_REQUEST["hdnCodigoConstrucao"] );
            }
            $inCodConstrucao = $obRCIMEdificacao->getCodigoConstrucao();
        } elseif ($_REQUEST["hdnVinculoEdificacao"] == "Imóvel") {
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
                $obRCIMUnidadeAutonoma->obRCIMEdificacao->setAreaConstruida                 ( $_REQUEST["flAreaConstruida"]     );
                $arProcesso = preg_split( "/[^a-zA-Z0-9]/", $_REQUEST["inCodigoProcesso"] );
                $obRCIMUnidadeAutonoma->obRCIMEdificacao->obRProcesso->setCodigoProcesso    ( $arProcesso[0]                    );
                $obRCIMUnidadeAutonoma->obRCIMEdificacao->obRProcesso->setExercicio         ( $arProcesso[1]                    );
                $obRCIMUnidadeAutonoma->roRCIMImovel->setNumeroInscricao                    ( $_REQUEST["stImovelCond"]         );
                $obRCIMUnidadeAutonoma->setNumero                                           ( $_REQUEST["stNumero"]             );
                $obRCIMUnidadeAutonoma->setComplemento                                      ( $_REQUEST["stComplemento"]        );
                $obRCIMUnidadeAutonoma->setAreaUnidade                                      ( $_REQUEST["flAreaUnidade"]        );
                $obErro = $obRCIMUnidadeAutonoma->incluirUnidadeAutonoma();
                $inCodConstrucao = $obRCIMUnidadeAutonoma->obRCIMEdificacao->getCodigoConstrucao();
                echo "Construção>".$inCodConstrucao;
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
                $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMEdificacao->setAreaConstruida              ( $_REQUEST["flAreaConstruida"]             );
                $arProcesso = preg_split( "/[^a-zA-Z0-9]/", $_REQUEST["inCodigoProcesso"] );
                $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMEdificacao->obRProcesso->setCodigoProcesso ( $arProcesso[0]                            );
                $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMEdificacao->obRProcesso->setExercicio      ( $arProcesso[1]                            );
                $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->setAreaUnidade                                   ( $_REQUEST["flAreaUnidade"]                );
                $obRCIMUnidadeAutonoma->roRCIMImovel->setNumeroInscricao                                            ( $_REQUEST["stImovelCond"]                 );
                $obRCIMUnidadeAutonoma->obRCIMEdificacao->setCodigoConstrucao                                       ( $_REQUEST["hdnCodigoConstrucaoAutonoma"]  );

                $obRCIMUnidadeAutonoma->obRCIMEdificacao->setCodigoTipo                                             ( $_REQUEST["hdnCodigoTipoAutonoma"]        );
                $obErro = $obRCIMUnidadeAutonoma->salvarUnidadesDependentes();
                $inCodConstrucao = $obRCIMUnidadeAutonoma->obRCIMEdificacao->getCodigoConstrucao();
            }
        }
        if ( !$obErro->ocorreu() ) {
            if ($_REQUEST["stProximaPagina"] == "edificacao") {
                sistemaLegado::alertaAviso($pgFormVinculo."?stAcao=incluir&boVinculoEdificacao=".$_REQUEST["hdnVinculoEdificacao"]."&boAdicionarEdificacao=".$_REQUEST["hdnAdicionarEdificacao"],$avisoInclusao,"incluir","aviso", Sessao::getId(), "../");
            } elseif ($_REQUEST["stProximaPagina"] == "unidade") {
                sistemaLegado::alertaAviso($pgFormVinculo."?boMesma=true&stAcao=incluir&boVinculoEdificacao=".$_REQUEST["hdnVinculoEdificacao"]."&boAdicionarEdificacao=".$_REQUEST["hdnAdicionarEdificacao"]."&inCodigoConstrucao=".$inCodConstrucao."&flAreaConstruida=".$_REQUEST["flAreaConstruida"],$avisoInclusao,"incluir","aviso", Sessao::getId(), "../");
            } else {
                session_regenerate_id();
                Sessao::setId("PHPSESSID=".session_id() );
                $sessao->geraURLRandomica();
                Sessao::write('acao', "757" );
                Sessao::write('modulo', "12" );
                if ($_REQUEST["hdnVinculoEdificacao"] == "Imóvel") {
                    $boVinculoConstrucao = "imovel";
                    $stUrl = "&inNumeroInscricao=".$_REQUEST["stImovelCond"];
                } else {
                    $boVinculoConstrucao = "condominio";
                    $stUrl = "&inCodigoCondominio=".$_REQUEST["stImovelCond"];
                }
                sistemaLegado::alertaAvisoRedirect($pgFormConstrucao."?stAcao=incluir&boVinculoConstrucao=".$boVinculoConstrucao.$stUrl,$avisoInclusao,"incluir","aviso",Sessao::getId(),"../","184" );
            }
        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    break;
    case "alterar":
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
            $obRCIMEdificacao->obRCIMCondominio->setCodigoCondominio ( $_REQUEST["hdnImovelCond"]           );
            $arProcesso = preg_split( "/[^a-zA-Z0-9]/", $_REQUEST["inCodigoProcesso"] );
            $obRCIMEdificacao->obRProcesso->setCodigoProcesso        ( $arProcesso[0] );
            $obRCIMEdificacao->obRProcesso->setExercicio             ( $arProcesso[1] );
            $obErro = $obRCIMEdificacao->alterarEdificacao();
        } elseif ($_REQUEST["hdnVinculoEdificacao"] == "Imóvel") {
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
                $obRCIMUnidadeAutonoma->obRCIMEdificacao->setCodigoConstrucao       ( $_REQUEST["hdnCodigoConstrucao"] );
                $obRCIMUnidadeAutonoma->obRCIMEdificacao->setCodigoTipo             ( $_REQUEST["hdnCodigoTipo"]       );
                $obRCIMUnidadeAutonoma->obRCIMEdificacao->setAreaConstruida         ( $_REQUEST["flAreaConstruida"]    );
                $obRCIMUnidadeAutonoma->obRCIMEdificacao->setTimestampConstrucao    ( $_REQUEST["hdnTimestamp"]        );
                $arProcesso =  preg_split( "/[^a-zA-Z0-9]/", $_REQUEST["inCodigoProcesso"] );
                $obRCIMUnidadeAutonoma->obRCIMEdificacao->obRProcesso->setCodigoProcesso ( $arProcesso[0] );
                $obRCIMUnidadeAutonoma->obRCIMEdificacao->obRProcesso->setExercicio      ( $arProcesso[1] );
                $obRCIMUnidadeAutonoma->roRCIMImovel->setNumeroInscricao                 ( $_REQUEST["stImovelCond"] );
                $obRCIMUnidadeAutonoma->setNumero                                        ( $_REQUEST["stNumero"]     );
                $obRCIMUnidadeAutonoma->setComplemento                                   ( $_REQUEST["stComplemento"]);
                $obRCIMUnidadeAutonoma->setAreaUnidade                                   ( $_REQUEST["flAreaUnidade"]);
                $obErro = $obRCIMUnidadeAutonoma->alterarUnidadeAutonoma();
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
                $obRCIMUnidadeAutonoma->obRCIMEdificacao->setCodigoConstrucao  ( $_REQUEST["hdnCodigoConstrucaoAutonoma"] );
                $obRCIMUnidadeAutonoma->obRCIMEdificacao->setCodigoTipo        ( $_REQUEST["hdnCodigoTipoAutonoma"] );

                $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMEdificacao->setCodigoConstrucao    ( $_REQUEST["hdnCodigoConstrucao"]  );
                $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMEdificacao->setCodigoTipo          ( $_REQUEST["hdnCodigoTipo"]        );
                $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMEdificacao->setAreaConstruida      ( $_REQUEST["flAreaConstruida"]     );
                $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMEdificacao->setTimestampConstrucao ( $_REQUEST["hdnTimestamp"]         );
                $arProcesso =  preg_split( "/[^a-zA-Z0-9]/", $_REQUEST["inCodigoProcesso"] );
                $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMEdificacao->obRProcesso->setCodigoProcesso ( $arProcesso[0] );
                $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMEdificacao->obRProcesso->setExercicio      ( $arProcesso[1] );
                $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->setAreaUnidade        ( $_REQUEST["flAreaUnidade"] );
                $obRCIMUnidadeAutonoma->roRCIMImovel->setNumeroInscricao                 ( $_REQUEST["stImovelCond"] );

                $obErro = $obRCIMUnidadeAutonoma->editarUnidadesDependentes();
            }
        }
        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgList."?stAcao=alterar","Codigo da Edificação: ".$_REQUEST["hdnCodigoConstrucao"],"alterar","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }
    break;
 /*    $obErro = new Erro;
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
            $arProcesso = preg_split( "/[^a-zA-Z0-9]/", $_REQUEST["inCodigoProcesso"] );
            $obRCIMEdificacao->obRProcesso->setCodigoProcesso        ( $arProcesso[0] );
            $obRCIMEdificacao->obRProcesso->setExercicio             ( $arProcesso[1] );
            if ($_REQUEST["hdnCodigoConstrucao"] == "") {
                $obErro = $obRCIMEdificacao->incluirEdificacao();
            } else {
                $obRCIMEdificacao->setCodigoConstrucao( $_REQUEST["hdnCodigoConstrucao"] );
            }
        } elseif ($_REQUEST["hdnVinculoEdstTipoVinculoificacao"] == "Imóvel") {
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
                $obRCIMUnidadeAutonoma->obRCIMEdificacao->setAreaConstruida                 ( $_REQUEST["flAreaConstruida"]     );
                $arProcesso = preg_split( "/[^a-zA-Z0-9]/", $_REQUEST["inCodigoProcesso"] );
                $obRCIMUnidadeAutonoma->obRCIMEdificacao->obRProcesso->setCodigoProcesso    ( $arProcesso[0]                    );
                $obRCIMUnidadeAutonoma->obRCIMEdificacao->obRProcesso->setExercicio         ( $arProcesso[1]                    );
                $obRCIMUnidadeAutonoma->roRCIMImovel->setNumeroInscricao                    ( $_REQUEST["stImovelCond"]         );
                $obRCIMUnidadeAutonoma->setNumero                                           ( $_REQUEST["stNumero"]             );
                $obRCIMUnidadeAutonoma->setComplemento                                      ( $_REQUEST["stComplemento"]        );
                $obRCIMUnidadeAutonoma->setAreaUnidade                                      ( $_REQUEST["flAreaUnidade"]        );
                $obErro = $obRCIMUnidadeAutonoma->incluirUnidadeAutonoma();
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
                $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMEdificacao->setAreaConstruida              ( $_REQUEST["flAreaConstruida"]             );
                $arProcesso = preg_split( "/[^a-zA-Z0-9]/", $_REQUEST["inCodigoProcesso"] );
                $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMEdificacao->obRProcesso->setCodigoProcesso ( $arProcesso[0]                            );
                $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMEdificacao->obRProcesso->setExercicio      ( $arProcesso[1]                            );
                $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->setAreaUnidade                                   ( $_REQUEST["flAreaUnidade"]                );
                $obRCIMUnidadeAutonoma->roRCIMImovel->setNumeroInscricao                                            ( $_REQUEST["stImovelCond"]                 );
                $obRCIMUnidadeAutonoma->obRCIMEdificacao->setCodigoConstrucao                                       ( $_REQUEST["hdnCodigoConstrucaoAutonoma"]  );
                $obRCIMUnidadeAutonoma->obRCIMEdificacao->setCodigoTipo                                             ( $_REQUEST["hdnCodigoTipoAutonoma"]        );
                $obErro = $obRCIMUnidadeAutonoma->salvarUnidadesDependentes();
            }
        }
        if ( !$obErro->ocorreu() ) {
            if ($_REQUEST["stProximaPagina"] == "edificacao") {
                sistemaLegado::alertaAviso($pgFormVinculo."?stAcao=incluir&boVinculoEdificacao=".$_REQUEST["hdnVinculoEdificacao"]."&boAdicionarEdificacao=".$_REQUEST["hdnAdicionarEdificacao"],$avisoInclusao,"incluir","aviso", Sessao::getId(), "../");
            } elseif ($_REQUEST["stProximaPagina"] == "unidade") {
                sistemaLegado::alertaAviso($pgFormVinculo."?stAcao=incluir&boVinculoEdificacao=".$_REQUEST["hdnVinculoEdificacao"]."&boAdicionarEdificacao=".$_REQUEST["hdnAdicionarEdificacao"]."&inCodigoConstrucao=".$_REQUEST["hdnCodigoConstrucao"]."&flAreaConstruida=".$_REQUEST["flAreaConstruida"],$avisoInclusao,"incluir","aviso", Sessao::getId(), "../");
            } else {
                session_regenerate_id();
                Sessao::getId() = "PHPSESSID=".session_id();
                $sessao->geraURLRandomica();
                Sessao::read('acao')   = "757";
                Sessao::read('modulo') = "12";
                if ($_REQUEST["hdnVinculoEdificacao"] == "Imóvel") {
                    $boVinculoConstrucao = "imovel";
                    $stUrl = "&inNumeroInscricao=".$_REQUEST["stImovelCond"];
                } else {
                    $boVinculoConstrucao = "condominio";
                    $stUrl = "&inCodigoCondominio=".$_REQUEST["stImovelCond"];
                }
                sistemaLegado::alertaAvisoRedirect($pgFormConstrucao."?stAcao=incluir&boVinculoConstrucao=".$boVinculoConstrucao.$stUrl,$avisoInclusao,"incluir","aviso",Sessao::getId(),"../","184" );
            }
        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    break;*/

    case "excluir":
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
            $obRCIMEdificacao->setCodigoTipo                         ( $_REQUEST["inCodigoTipo"]            );
            $obRCIMEdificacao->setAreaConstruida                     ( $_REQUEST["flAreaConstruida"]        );
            $obRCIMEdificacao->obRCIMCondominio->setCodigoCondominio ( $_REQUEST["stImovelCond"]            );
            $obRCIMEdificacao->obRProcesso->setCodigoProcesso        ( $_REQUEST["inCodigoProcesso"]        );
            $obRCIMEdificacao->obRProcesso->sestTipoVinculotExercicio             ( $_REQUEST["hdnAnoExercicioProcesso"] );
            $obErro = $obRCIMEdificacao->excluirEdificacao();
        } elseif ($_REQUEST["hdnVinculoEdificacao"] == "Autônoma" OR $_REQUEST["hdnVinculoEdificacao"] == "Dependente") {
            $avisoInclusao = "Inscrição Imobiliária: ".$_REQUEST["stImovelCond"];
            if ($stTipoUnidade == "Autônoma") {
                //ATRIBUTOS
                foreach ($arChave as $key=>$value) {
                    $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
                    $inCodAtributo = $arChaves[0];
                    if ( is_array($value) ) {
                        $value = implode(",",$value);
                    }
                    $obRCIMUnidadeAutonoma->obRCIMEdificacao->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $value );
                }
                $obRCIMUnidadeAutonoma->obRCIMEdificacao->setCodigoConstrucao            ( $_REQUEST["inCodigoConstrucao"]);
                $arProcesso =  preg_split( "/[^a-zA-Z0-9]/", $_REQUEST["inCodigoProcesso"] );
                $obRCIMUnidadeAutonoma->obRCIMEdificacao->obRProcesso->setCodigoProcesso ( $arProcesso[0] );
                $obRCIMUnidadeAutonoma->obRCIMEdificacao->obRProcesso->setExercicio      ( $arProcesso[1] );
                $obRCIMUnidadeAutonoma->obRCIMEdificacao->setAreaConstruida              ( $_REQUEST["flAreaConstruida"] );
                $obRCIMUnidadeAutonoma->obRCIMEdificacao->obRProcesso->setCodigoProcesso ( $_REQUEST["inCodigoProcesso"] );
                $obRCIMUnidadeAutonoma->obRCIMEdificacao->obRProcesso->setExercicio      ( $_REQUEST["hdnAnoExercicioProcesso"] );
                $obRCIMUnidadeAutonoma->roRCIMImovel->setNumeroInscricao                 ( $_REQUEST["stImovelCond"]  );
                $obRCIMUnidadeAutonoma->setNumero                                        ( $_REQUEST["stNumero"]      );
                $obRCIMUnidadeAutonoma->setComplemento                                   ( $_REQUEST["stComplemento"] );
                $obRCIMUnidadeAutonoma->setAreaUnidade                                   ( $_REQUEST["flAreaUnidade"] );
                $obErro = $obRCIMUnidadeAutonoma->excluirUnidadeAutonoma();
            } elseif ($stTipoUnidade == "Dependente") {
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
                $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMEdificacao->setCodigoTipo ( $_REQUEST["inCodigoTipo"]                             );
                $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMEdificacao->setAreaConstruida ( $_REQUEST["flAreaConstruida"]                     );
                $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMEdificacao->obRProcesso->setCodigoProcesso ( $_REQUEST["inCodigoProcesso"]        );
                $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMEdificacao->obRProcesso->setExercicio      ( $_REQUEST["hdnAnoExercicioProcesso"] );
                $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->setAreaUnidade        ( $_REQUEST["flAreaUnidade"] );
                $obRCIMUnidadeAutonoma->roRCIMImovel->setNumeroInscricao                 ( $_REQUEST["stImovelCond"]  );
                $obErro = $obRCIMUnidadeAutonoma->removerUnidadesDependentes();
            }
        }
        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgList."?stAcao=excluir","Codigo da Edificação: ".$_REQUEST["inCodigoConstrucao"],"excluir","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::alertaAviso($pgList,urlencode($obErro->getDescricao()),"n_excluir","erro",Sessao::getId(), "../");
//            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_excluir","erro");
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
                $obRCIMUnidadeAutonoma->setNumero                               ( $_REQUEST["stNumero"]                     );
                $obRCIMUnidadeAutonoma->setComplemento                          ( $_REQUEST["stComplemento"]                );
                $obRCIMUnidadeAutonoma->setJustificativa                        ( $_REQUEST["stJustificativa"]              );
                $obRCIMUnidadeAutonoma->obRCIMEdificacao->setAreaConstruida     ( $_REQUEST["hdnAreaConstruida"]            );
                foreach ($_REQUEST as $key=>$value) {
                    if (strstr($key,'boUnidadeSelecionada') and $$key != "") {
                        $obRCIMUnidadeAutonoma->setUnidadeSelecionada           ( $value                                    );
                    }
                }
                $obErro = $obRCIMUnidadeAutonoma->baixarUnidadeAutonomaEdificacao();
            }
        } else {
            $obRCIMUnidadeDependente->roRCIMUnidadeAutonoma->roRCIMImovel->setNumeroInscricao( $_REQUEST["stImovelCond"] );
            $obRCIMUnidadeDependente->roRCIMUnidadeAutonoma->obRCIMEdificacao->setCodigoConstrucao( $_REQUEST["hdnCodigoConstrucaoAutonoma"] );
            $obRCIMUnidadeDependente->roRCIMUnidadeAutonoma->obRCIMEdificacao->setCodigoTipo( $_REQUEST["inCodigoTipo"] );
            $obRCIMUnidadeDependente->obRCIMEdificacao->setCodigoConstrucao( $_REQUEST["hdnCodigoConstrucao"] );
            $obRCIMUnidadeDependente->setJustificatica( $_REQUEST["stJustificativa"] );
            $obErro = $obRCIMUnidadeDependente->baixaUnidadeDependente();
        }
        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgList."?stAcao=baixar","Codigo da Edificação: ".$_REQUEST["hdnCodigoConstrucao"],"baixar","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_baixar","erro");
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
            sistemaLegado::alertaAviso($pgList,"Codigo da Edificação: ".$_REQUEST["hdnCodigoConstrucao"],"alterar","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
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
            echo "Valor vindo:".$_REQUEST["flAreaEdificacao"];
            $obRCIMEdificacao->setCodigoConstrucao                   ( $_REQUEST["inCodigoConstrucao"]  );
            $obRCIMEdificacao->setAreaConstruida                     ( $_REQUEST["flAreaEdificacao"]    );
            // separa processo/exericio em dois(array)
            $arProcesso = preg_split( "/[^a-zA-Z0-9]/", $_REQUEST["inCodigoProcesso"] );
            // Seta codigo do processo e exercicio para objeto de Regra de Edificação
            $obRCIMEdificacao->obRProcesso->setCodigoProcesso        ( $arProcesso[0] );
            $obRCIMEdificacao->obRProcesso->setExercicio             ( $arProcesso[1] );
            // se campo hidden Codigo Construção nao estiver preenchido, inclui reforma!
            $obErro = $obRCIMEdificacao->incluirReforma();

        } elseif ($_REQUEST["hdnVinculoEdificacao"] == "Imóvel") {
            $avisoInclusao = "Incluida Reforma: ".$_REQUEST["stImovelCond"];
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
                $obRCIMUnidadeAutonoma->obRCIMEdificacao->setCodigoConstrucao               ( $_REQUEST["inCodigoConstrucao"]       );
                $obRCIMUnidadeAutonoma->obRCIMEdificacao->setCodigoTipo                     ( $_REQUEST["hdnCodigoTipo"]            );
                $obRCIMUnidadeAutonoma->obRCIMEdificacao->setAreaConstruida                 ( $_REQUEST["flAreaEdificacao"]         );
                $arProcesso = preg_split( "/[^a-zA-Z0-9]/", $_REQUEST["inProcesso"] );
                $obRCIMUnidadeAutonoma->obRProcesso->setCodigoProcesso                      ( $arProcesso[0]                        );
                $obRCIMUnidadeAutonoma->obRProcesso->setExercicio                           ( $arProcesso[1]                        );
                $obRCIMUnidadeAutonoma->roRCIMImovel->setNumeroInscricao                    ( $_REQUEST["stImovelCond"]             );
                $obRCIMUnidadeAutonoma->setNumero                                           ( $_REQUEST["stNumero"]                 );
                $obRCIMUnidadeAutonoma->setComplemento                                      ( $_REQUEST["stComplemento"]            );
                $obRCIMUnidadeAutonoma->setAreaUnidade                                      ( $_REQUEST["flAreaUnidade"]            );
                $obErro = $obRCIMUnidadeAutonoma->incluirReforma();
            } elseif ($hdnTipoUnidade == "Dependente") {
                    echo "Entramos em Unidade Dependente";
                    $obRCIMUnidadeDependente->setNumeroInscricao            ( $_REQUEST["stImovelCond"          ]   );
                    $obRCIMUnidadeDependente->setCodigoTipo                 ( $_REQUEST["hdnCodigoTipo"         ]   );
                    $obRCIMUnidadeDependente->setCodigoConstrucao           ( $_REQUEST["hdnCodigoConstrucaoAut"]   );
                    $obRCIMUnidadeDependente->setCodigoConstrucaoDependente ( $_REQUEST["inCodigoConstrucao"    ]   );
                    $obRCIMUnidadeDependente->setAreaUnidade                ( $_REQUEST["flAreaUnidade"         ]   );
                    $arProcesso = preg_split( "/[^a-zA-Z0-9]/", $_REQUEST["inProcesso"] );
                    $obRCIMUnidadeDependente->obRProcesso->setCodigoProcesso    ( $arProcesso[0]                    );
                    $obRCIMUnidadeDependente->obRProcesso->setExercicio         ( $arProcesso[1]                    );
                    $obRCIMUnidadeDependente->incluirReforma();

            }
        }
/*
        if ( !$obErro->ocorreu() ) {
            if ($_REQUEST["stProximaPagina"] == "edificacao") {
                sistemaLegado::alertaAviso($pgFormVinculo."?stAcao=incluir&boVinculoEdificacao=".$_REQUEST["hdnVinculoEdificacao"]."&boAdicionarEdificacao=".$_REQUEST["hdnAdicionarEdificacao"],$avisoInclusao,"incluir","aviso", Sessao::getId(), "../");
            } elseif ($_REQUEST["stProximaPagina"] == "unidade") {
                sistemaLegado::alertaAviso($pgFormVinculo."?stAcao=incluir&boVinculoEdificacao=".$_REQUEST["hdnVinculoEdificacao"]."&boAdicionarEdificacao=".$_REQUEST["hdnAdicionarEdificacao"]."&inCodigoConstrucao=".$_REQUEST["hdnCodigoConstrucao"]."&flAreaConstruida=".$_REQUEST["flAreaConstruida"],$avisoInclusao,"incluir","aviso", Sessao::getId(), "../");
            } else {
                session_regenerate_id();
                Sessao::getId() = "PHPSESSID=".session_id();
                $sessao->geraURLRandomica();
                Sessao::read('acao')   = "757";
                Sessao::read('modulo') = "12";
                if ($_REQUEST["hdnVinculoEdificacao"] == "Imóvel") {
                    $boVinculoConstrucao = "imovel";
                    $stUrl = "&inNumeroInscricao=".$_REQUEST["stImovelCond"];
                } else {
                    $boVinculoConstrucao = "condominio";
                    $stUrl = "&inCodigoCondominio=".$_REQUEST["stImovelCond"];
                }
                 sistemaLegado::alertaAviso($pgFilt."?stAcao=reforma&boVinculoEdificacao=".$_REQUEST["hdnVinculoEdificacao"],$avisoInclusao,"incluir","aviso", Sessao::getId(), "../");
                //sistemaLegado::alertaAvisoRedirect($pgList."?stAcao=reforma&boVinculoConstrucao=".$boVinculoConstrucao.$stUrl,$avisoInclusao,"incluir","aviso",Sessao::getId(),"../","184" );
            }
        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }*/
    break;
}
?>
