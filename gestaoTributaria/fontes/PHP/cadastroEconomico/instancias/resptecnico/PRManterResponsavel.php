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
    * Pagina de processamento para Responsavel Tecnico
    * Data de Criação   : 15/04/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Lucas Teixeira Stephanou

    * @ignore

    * $Id: PRManterResponsavel.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.02.04
*/

/*
$Log$
Revision 1.12  2007/05/09 18:41:35  cercato
Bug #8767#

Revision 1.11  2007/03/08 20:49:58  rodrigo
Bug #8426#

Revision 1.10  2007/02/22 14:43:21  rodrigo
Bug #8426#

Revision 1.9  2006/09/15 14:33:35  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMResponsavelTecnico.class.php" );
include_once ( CAM_GA_CSE_NEGOCIO."RConselho.class.php" );
include_once ( CAM_GA_CSE_NEGOCIO."RProfissao.class.php" );
include_once ( CAM_GA_ADM_NEGOCIO."RAdministracaoUF.class.php" );

$stAcao = $request->get('stAcao');
$link = Sessao::read("link" );
//MANTEM O FILTRO E A PAGINACAO
$stLink = "?".Sessao::getId()."&pg=".$link["pg"]."&pos=".$link["pos"]."&stAcao=".$stAcao;

//Define o nome dos arquivos PHP
$stPrograma    = "ManterResponsavel";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php".$stLink;
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";

switch ($stAcao) {
    case "incluir":
        if ($_REQUEST["boTipoResponsavel"] == "profissional") {
            $obRCEMRT = new RCEMResponsavelTecnico;
            $obRCEMRT->setNumCgm            ( $_REQUEST[ "inNumCGM"         ]   ) ;
            $obRCEMRT->setCodigoProfissao   ( $_REQUEST[ "inCodigoProfissao"]   ) ;
            $obRCEMRT->setNumRegistro       ( trim($_REQUEST[ "stRegistro"  ]  )) ;
            $obRCEMRT->setCodigoUF          ( $_REQUEST[ "inCodigoUf"       ]   ) ;

            // VERIFICA SE JA EXISTE RESPONSAVEL
            $obErro = $obRCEMRT->verificaResponsavelTecnico( $rsResponsavel );

            if ( (!($obErro->ocorreu())) && ( $rsResponsavel->getNumLinhas() > 0 ) ) {
//              $obErro->setDescricao("Erro. Número de registro ( ".$obRCEMRT->getNumRegistro().") já existente para a profissão e UF selecionadas.");
                $obErro->setDescricao("Responsável técnico já cadastrado no sistema!");
            }

            if ( !$obErro->ocorreu() ) {
                $obErro = $obRCEMRT->incluirResponsavelTecnico();
            }

            if ( !$obErro->ocorreu()) {
                sistemaLegado::alertaAviso($pgForm,"Registro: ".$_REQUEST["stRegistro"],"incluir","aviso", Sessao::getId(), "../");
            } else {
                sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
            }
        } else { //Empresa Responsável
            $obRResponsavel = new RCEMResponsavelTecnico;
            $obRResponsavel->setNumCgm( $_REQUEST[ "inNumCGM" ] );
            $inProfissoesSelecionadas = $_REQUEST["inCodProfissoesSelecionadas"];

            $arResponsaveisSessao = Sessao::read( 'responsaveis' );
            $nregistros = count ( $arResponsaveisSessao );
            if ( ($nregistros < 1) && ($_REQUEST[ "inNumResponsavelCGM" ] == "") ) {
                $js = "alertaAviso('@Campo Responsável vazio.','form','erro','".Sessao::getId()."');";
                sistemaLegado::executaFrameOculto( $js );
            } else {
                if ($_REQUEST[ "inNumResponsavelCGM" ] != "") {
                    $newResponsavel = $_REQUEST['inNumResponsavelCGM'];
                    $tempCGM = $obRResponsavel->getNumCgm();
                    $obRResponsavel->setNumCgm( $newResponsavel );
                    for ( $inCount=0; $inCount < count($inProfissoesSelecionadas); $inCount++) {
                        $inCodProfissao = $inProfissoesSelecionadas[ $inCount ];
                        $obRResponsavel->obRProfissao->setCodigoProfissao( $inCodProfissao );
                        $obRResponsavel->listarResponsavelTecnico( $rsListaResponsavel );
                        if ( !$rsListaResponsavel->eof() ) {
                            $arResponsaveisSessao = Sessao::read( 'responsaveis' );
                            $nregistros = count ( $arResponsaveisSessao );
                            $cont = 0;
                            $insere = true;
                            while ($cont < $nregistros) {
                                if ( ($arResponsaveisSessao[$cont]['num_cgm'] == $newResponsavel) && ($arResponsaveisSessao[$cont]['num_profissao'] == $inCodProfissao) ) {
                                    //codigo ja estava na lista!
                                    $js .= 'f.inNumResponsavelCGM.value = "";';
                                    $js .= 'f.inNumResponsavelCGM.focus();';
                                    $js .= 'd.getElementById("inNomResponsavel").innerHTML = "&nbsp;";';
                                    $js .= "alertaAviso('@Código do Responsável já está na lista. (".$newResponsavel.")','form','erro','".Sessao::getId()."');";

                                    sistemaLegado::executaFrameOculto( $js );
                                    $insere = false;
                                    break;
                                } else {
                                    $cont++;
                                }
                            }

                            if ($insere) {
                                $arResponsaveisSessao[$nregistros]['num_profissao'] = $inCodProfissao;
                                $arResponsaveisSessao[$nregistros]['num_cgm'] = $newResponsavel;
                                $arResponsaveisSessao[$nregistros]['nom_cgm'] = $rsListaResponsavel->getCampo("nom_cgm");
                                $arResponsaveisSessao[$nregistros]['nom_profissao'] = $rsListaResponsavel->getCampo("nom_profissao");
                                $arResponsaveisSessao[$nregistros]['nom_registro'] = $rsListaResponsavel->getCampo("nom_registro");
                                $arResponsaveisSessao[$nregistros]['num_registro'] = $rsListaResponsavel->getCampo("num_registro");
                                $arResponsaveisSessao[$nregistros]['nom_registro'] = $rsListaResponsavel->getCampo("nom_registro");
                                $arResponsaveisSessao[$nregistros]['sequencia'] = $rsListaResponsavel->getCampo("sequencia");

                                Sessao::write( "responsaveis", $arResponsaveisSessao );
                            }
                        }
                    }

                    $obRResponsavel->setNumCgm($tempCGM);
                }

                $arProfissoes = array();
                for ( $inCount=0; $inCount < count($inProfissoesSelecionadas); $inCount++) {
                     $arProfissoes[]['cod_profissao'] = $inProfissoesSelecionadas[ $inCount ];
                }

                $obRResponsavel->setProfissoes( $arProfissoes );

                $arResponsaveisSessao = Sessao::read( 'responsaveis' );
                $nregistros = count ( $arResponsaveisSessao );
                $arResponsaveis = array();
                $cont = 0;
                while ($cont < $nregistros) {
                    $arResponsaveis[] = $arResponsaveisSessao[$cont];
                    $cont++;
                }

                if (count($arResponsaveis) < 1) {
                    $obErro = new Erro;

                    $obErro->setDescricao ('Responsável não corresponde à nenhuma das profissões selecionadas para a empresa! ('.$newResponsavel.')');
                } else {
                    $obRResponsavel->setResponsaveis( $arResponsaveis );

                    $obErro = $obRResponsavel->incluirResponsavelEmpresa();
                }

                //mensagem confirmando ação incluir
                if ( !$obErro->ocorreu()) {
                    sistemaLegado::alertaAviso($pgForm, "Empresa Responsável (".$_REQUEST[ "inNumCGM" ].")","incluir","aviso",Sessao::getId(), "../");
                } else {
                    sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
                }
            }
        }
        break;

    case "alterar":
        $obRCEMRT = new RCEMResponsavelTecnico;
        $obRCEMRT->setNumRegistro       ( trim($_REQUEST["stRegistro"]) ) ;
        $obRCEMRT->setCodigoProfissao   ( $_REQUEST["inCodigoProfissao"] ) ;
        $obRCEMRT->setCodigoUF          ( $_REQUEST["inCodigoUf"]   ) ;

        $obErro = $obRCEMRT->verificaResponsavelTecnico( $rsResponsavel );

        $obRCEMRT->setNumCgm            ( $_REQUEST["inNumCGM"]  ) ;
        $obRCEMRT->setSequencia         ( $_REQUEST["inSequencia"] );

        if ( !$obErro->ocorreu() && ( $rsResponsavel->getNumLinhas() > 0 ) && ($rsResponsavel->getCampo("numcgm") != $_REQUEST["inNumCGM"]) ) {
            $obErro->setDescricao("Erro. Número de registro ( ".$obRCEMRT->getNumRegistro().") já existente para a profissão e UF selecionadas.");
        }

        if ( !$obErro->ocorreu() ) {
            $obErro = $obRCEMRT->alterarResponsavelTecnico();
        }

        $obRUF = new RUF;
        $obRUF->setCodigoUF( $_REQUEST["inCodigoUf"] );
        $obRUF->listarUF($rsUF);

        if ( !$obErro->ocorreu()) {
            //$stMsg = $_REQUEST["stNomRegistro"]." ".$_REQUEST["stRegistro"].$_REQUEST["inCodigoUf"];
            $stMsg = $_REQUEST["stNomRegistro"]."-".$rsUF->getCampo("sigla_uf")."-".$_REQUEST["stRegistro"];
            sistemaLegado::alertaAviso($pgList,$stMsg,"alterar","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }

    break;

    case "excluir":
        $obRCEMRT = new RCEMResponsavelTecnico;
        $obRCEMRT->setNumCgm            ( $_REQUEST["inNumCGM"] ) ;
        $obRCEMRT->setCodigoProfissao   ( $_REQUEST["inCodigoProfissao"] ) ;
        $obRCEMRT->setSequencia         ( $_REQUEST["inSequencia"] );
        $obErro = $obRCEMRT->excluirResponsavelTecnico();

        if ( !$obErro->ocorreu()) {
            sistemaLegado::alertaAviso($pgList,"Registro: ".$_REQUEST["stRegistro"],"excluir","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::alertaAviso($pgList,urlencode($obErro->getDescricao()),"n_excluir","erro",Sessao::getId(), "../");
        }
    break;

}

?>
