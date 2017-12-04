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
    * PÃ¡gina do Frame oculto para Cadastro de Responsavel Tecnico
    * Data de CriaÃ§Ã£o   : 15/04/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Lucas Teixeira Stephanou

    * @ignore

    * $Id: OCManterResponsavel.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.02.04
*/

/*
$Log$
Revision 1.12  2007/05/09 19:27:11  cercato
Bug #8768#

Revision 1.11  2006/09/15 14:33:35  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GA_CSE_NEGOCIO."RProfissao.class.php" );
include_once ( CAM_GA_CSE_NEGOCIO."RConselho.class.php" );
include_once ( CAM_GA_ADM_NEGOCIO."RAdministracaoUF.class.php" );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLoteRural.class.php" );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLoteUrbano.class.php" );
include_once ( CAM_GA_CGM_NEGOCIO."RCGMPessoaFisica.class.php" );
include_once ( CAM_GA_CGM_NEGOCIO."RCGMPessoaJuridica.class.php" );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMResponsavelTecnico.class.php" );

/*function BuscaCGM() {
    global $_REQUEST;
    $obRCGM = new RCGM;

    $stText = "inNumCGM";
    $stSpan = "inNomCGM";
    if ($_REQUEST[ $stText ] != "") {
        $obRCGM->setNumCGM( $_REQUEST[ $stText ] );
        $obRCGM->consultar( $rsCGM );
        $stNull = "&nbsp;";

        if ( $rsCGM->getNumLinhas() <= 0) {
            $stJs .= 'f.'.$stText.'.value = "";';
            $stJs .= 'f.'.$stText.'.focus();';
            $stJs .= 'd.getElementById("'.$stSpan.'").innerHTML = "'.$stNull.'";';
            $stJs .= "sistemaLegado::alertaAviso('@Valor inválido. (".$_REQUEST[ $stText ].")','form','erro','".Sessao::getId()."');";
        } else {
           $stJs .= 'd.getElementById("'.$stSpan.'").innerHTML = "'.($rsCGM->getCampo('nom_cgm')?$rsCGM->getCampo('nom_cgm'):$stNull).'";';
        }
    } else {
        $stJs .= 'd.getElementById("'.$stSpan.'").innerHTML = "&nbsp;";';
    }

    return $stJs;
}
*/

function montaListaResponsaveis($rsListaResponsaveis)
{
    if ( $rsListaResponsaveis->getNumLinhas() > 0 ) {
        $obLista = new Lista;
        $obLista->setRecordSet ( $rsListaResponsaveis );
        $obLista->setTitulo ( "Lista de Resposáveis Técnicos" );
        $obLista->setMostraPaginacao ( false );

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "&nbsp;" );
        $obLista->ultimoCabecalho->setWidth    ( 5 );
        $obLista->commitCabecalho ();

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "Código" );
        $obLista->ultimoCabecalho->setWidth ( 10 );
        $obLista->commitCabecalho ();

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "Nome" );
        $obLista->ultimoCabecalho->setWidth ( 45 );
        $obLista->commitCabecalho ();

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "Profissão" );
        $obLista->ultimoCabecalho->setWidth ( 10 );
        $obLista->commitCabecalho ();

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "Registro" );
        $obLista->ultimoCabecalho->setWidth ( 30 );
        $obLista->commitCabecalho ();

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "&nbsp;" );
        $obLista->ultimoCabecalho->setWidth ( 5 );
        $obLista->commitCabecalho ();

        $obLista->addDado ();
        $obLista->ultimoDado->setCampo ( "num_cgm" );
        $obLista->commitDado ();

        $obLista->addDado ();
        $obLista->ultimoDado->setCampo ( "nom_cgm" );
        $obLista->commitDado ();

        $obLista->addDado ();
        $obLista->ultimoDado->setCampo ( "nom_profissao" );
        $obLista->commitDado ();

        $obLista->addDado ();
        $obLista->ultimoDado->setCampo ( "[nom_registro] - [num_registro]" );
        $obLista->commitDado ();

        $obLista->addAcao ();
        $obLista->ultimaAcao->setAcao ( "EXCLUIR" );
        $obLista->ultimaAcao->setFuncao ( true );
        $obLista->ultimaAcao->setLink ( "JavaScript:excluirResponsavel();" );
        $obLista->ultimaAcao->addCampo ( "inIndice1","num_cgm" );
        $obLista->ultimaAcao->addCampo ( "inIndice2","num_profissao" );

        $obLista->commitAcao ();

        $obLista->montaHTML ();
        $stHTML =  $obLista->getHtml ();
        $stHTML = str_replace ( "\n","",$stHTML );
        $stHTML = str_replace ( "  ","",$stHTML );
        $stHTML = str_replace ( "'","\\'",$stHTML );
    } else {
        $stHTML = "&nbsp";
    }

    $js .= "d.getElementById('spnListaResponsavel').innerHTML = '".$stHTML."';\n";
    $js .= "d.getElementById('inNomResponsavel').innerHTML = '&nbsp;';\n";
    $js .= "f.inNumResponsavelCGM.value ='';\n";
    $js .= "f.inNumResponsavelCGM.focus();\n";

    sistemaLegado::executaFrameOculto($js);
}

switch ($_REQUEST["stCtrl"]) {
    case "PreencheResponsavelTecnico":
        $stNomCampo = $_GET["stNomCampoCod"];
        $stId = $_GET["stId"];

        if ($_GET[$stNomCampo]) {
            $obRResponsavel = new RCEMResponsavelTecnico;
            $obRResponsavel->setNumCgm( $_GET[$stNomCampo] );
            if ( Sessao::read( "arProfissoes" ) )
                $obRResponsavel->setProfissoes( Sessao::read( "arProfissoes" ) );

            $obRResponsavel->listarResponsavelTecnico( $rsListaResponsavel );
            if ( $rsListaResponsavel->eof() ) { //codigo informado invalido
                $js .= 'f.'.$stNomCampo.'.value = "";';
                $js .= 'f.'.$stNomCampo.'.focus();';
                $js .= 'd.getElementById("'.$stId.'").innerHTML = "&nbsp;";';
                $js .= "alertaAviso('@Código do Responsável inválido. (".$_GET[$stNomCampo].")','form','erro','".Sessao::getId()."');";
            } else {
                $js .= 'd.getElementById("'.$stId.'").innerHTML = "'.$rsListaResponsavel->getCampo("nom_cgm") .'";';
            }
        } else {
            $js .= 'd.getElementById("'.$stId.'").innerHTML = "&nbsp;";';
        }

        echo $js;
        break;

    case "montaAtributosProfissao":
        $obRProfissao       = new RProfissao;
        $obRConselho        = new RConselho ;
        if ($_REQUEST[ "cmbProfissao" ]) {
            $obRProfissao->setCodigoProfissao( $_REQUEST["cmbProfissao"]        );
        } elseif ($_REQUEST[ "inCodigoProfissao" ]) {
            $obRProfissao->setCodigoProfissao( $_REQUEST["inCodigoProfissao"]   );
        }

        $obErro = $obRProfissao->consultarProfissao();
        if ( !$obErro->ocorreu() ) {
            $obRConselho->setCodigoConselho( $obRProfissao->getCodigoConselho() );
            $obErro = $obRConselho->consultarConselho();
            if ( !$obErro->ocorreu() ) {
                $stJs .= 'f.inCodigoProfissao.value = "'.$obRProfissao->getCodigoProfissao().'";';
                if ($_REQUEST["stAcao"]== "incluir") {
                   $stJs .= 'd.getElementById("stNomeConselhoClasse").innerHTML = "'.$obRConselho->getNomeConselho().'";';
                }
                $stJs .= 'd.getElementById("rotRegistro").innerHTML = "'.$obRConselho->getNomeRegistro().'";';
//                $stJs .= 'd.getElementById("cmbProfissao").selectedIndex = d.getElementById("cmbProfissao").options.value=
            } else {
                $stJs .= 'f.inCodigoProfissao.value = "";';
                $stJs .= 'd.getElementById("stNomeConselhoClasse").innerHTML = "&nbsp;";';
                $stJs .= "alertaAviso('@Valor inválido. (".$_REQUEST["cmbProfisao"].")','form','erro','".Sessao::getId()."');";
            }
        }
        sistemaLegado::executaFrameOculto($stJs);
        break;

    case "buscaResponsavelCGM":
        $obRResponsavel = new RCEMResponsavelTecnico;
        $obRResponsavel->setNumCgm( $_REQUEST["inNumResponsavelCGM"] );
        $obRResponsavel->listarResponsavelTecnico( $rsListaResponsavel );
        if ( $rsListaResponsavel->eof() ) { //codigo informado invalido
            $js .= 'f.inNumResponsavelCGM.value = "";';
            $js .= 'f.inNumResponsavelCGM.focus();';
            $js .= 'd.getElementById("inNomResponsavel").innerHTML = "&nbsp;";';
            $js .= "alertaAviso('@Código do Responsável inválido. (".$_REQUEST["inNumResponsavelCGM"].")','form','erro','".Sessao::getId()."');";
        } else {
            $js .= 'd.getElementById("inNomResponsavel").innerHTML = "'.$rsListaResponsavel->getCampo("nom_cgm") .'";';
        }

        sistemaLegado::executaFrameOculto( $js );
        break;

    case "buscaCGMPF":
        if ($_REQUEST["inNumCGM"] != "") {
            $obRCGMPessoaFisica = new RCGMPessoaFisica;
            $obRCGMPessoaFisica->setNumCGM ( $_REQUEST["inNumCGM"] );
            $stWhere = " numcgm = ".$obRCGMPessoaFisica->getNumCGM();

            $obRCGMPessoaFisica->consultarCGM($rsCgm, $stWhere);
            $inNumLinhas = $rsCgm->getNumLinhas();
            $js = '';
            if ($inNumLinhas <= 0) {
                $js .= 'f.inNumCGM.value = "";';
                $js .= 'f.inNumCGM.focus();';
                $js .= 'd.getElementById("inNomCGM").innerHTML = "&nbsp;";';
                $js .= "alertaAviso('@CGM deve ser de Pessoa Fisica. (".$_REQUEST["inNumCGM"].")','form','erro','".Sessao::getId()."');";
            } else {
                $stNomCgm = $rsCgm->getCampo("nom_cgm");
                $js .= 'd.getElementById("inNomCGM").innerHTML = "'.$stNomCgm.'";';
            }
            sistemaLegado::executaFrameOculto($js);
        } else {
            $js = 'd.getElementById("inNomCGM").innerHTML = "&nbsp;";';
            sistemaLegado::executaFrameOculto($js);
        }
        break;

    case "buscaCGMPJ":
        if ($_REQUEST["inNumCGM"] != "") {
            $obRCGMPessoaJuridica = new RCGMPessoaJuridica;
            $obRCGMPessoaJuridica->setNumCGM ( $_REQUEST["inNumCGM"] );
            $stWhere = " numcgm = ".$obRCGMPessoaJuridica->getNumCGM();

            $obRCGMPessoaJuridica->consultarCGM($rsCgm, $stWhere);
            $inNumLinhas = $rsCgm->getNumLinhas();
            $js = '';
            if ($inNumLinhas <= 0) {
                $js .= 'f.inNumCGM.value = "";';
                $js .= 'f.inNumCGM.focus();';
                $js .= 'd.getElementById("inNomCGM").innerHTML = "&nbsp;";';
                $js .= "alertaAviso('@CGM deve ser de Pessoa Juridica. (".$_REQUEST["inNumCGM"].")','form','erro','".Sessao::getId()."');";
            } else {
                $stNomCgm = $rsCgm->getCampo("nom_cgm");
                $js .= 'd.getElementById("inNomCGM").innerHTML = "'.$stNomCgm.'";';
            }
            sistemaLegado::executaFrameOculto($js);
        } else {
            $js = 'd.getElementById("inNomCGM").innerHTML = "&nbsp;";';
            sistemaLegado::executaFrameOculto($js);
        }
        break;

    case "limparResponsavel":
        $js .= 'f.inNumResponsavelCGM.value = "";';
        $js .= 'f.inNumResponsavelCGM.focus();';
        $js .= 'd.getElementById("inNomResponsavel").innerHTML = "&nbsp;";';
        sistemaLegado::executaFrameOculto($js);
        break;

    case "LimparSessao":
        Sessao::write( "responsaveis", array() );
        break;

    case "preparaProfissoes":
        $inProfissoesSelecionadas = $_REQUEST["inCodProfissoesSelecionadas"];
        $stAtividades = '';
        for ( $inCount=0; $inCount < count($inProfissoesSelecionadas); $inCount++) {
            if ( $inCount != 0 )
                $stAtividades .=  ','.$inProfissoesSelecionadas[ $inCount ];
            else
                $stAtividades .=  $inProfissoesSelecionadas[ $inCount ];
        }
        break;

    case "incluirResponsavel":
        $newResponsavel = $_REQUEST['inNumResponsavelCGM'];
        $obRResponsavel = new RCEMResponsavelTecnico;
        $obRResponsavel->setNumCgm( $newResponsavel );
        $num_incluiu = 0;
        $inProfissoesSelecionadas = $_REQUEST["inCodProfissoesSelecionadas"];
        for ( $inCount=0; $inCount < count($inProfissoesSelecionadas); $inCount++) {
            $inCodProfissao = $inProfissoesSelecionadas[ $inCount ];
            $obRResponsavel->obRProfissao->setCodigoProfissao( $inCodProfissao );
            $obRResponsavel->listarResponsavelTecnico( $rsListaResponsavel );
            if ( !$rsListaResponsavel->eof() ) {
                $arResponsaveisSessao = Sessao::read( "responsaveis" );
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
                    $num_incluiu++;
                    $arResponsaveisSessao[$nregistros]['num_profissao'] = $inCodProfissao;
                    $arResponsaveisSessao[$nregistros]['num_cgm'] = $newResponsavel;
                    $arResponsaveisSessao[$nregistros]['nom_cgm'] = $rsListaResponsavel->getCampo("nom_cgm");
                    $arResponsaveisSessao[$nregistros]['nom_profissao'] = $rsListaResponsavel->getCampo("nom_profissao");
                    $arResponsaveisSessao[$nregistros]['nom_registro'] = $rsListaResponsavel->getCampo("nom_registro");
                    $arResponsaveisSessao[$nregistros]['num_registro'] = $rsListaResponsavel->getCampo("num_registro");
                    $arResponsaveisSessao[$nregistros]['nom_registro'] = $rsListaResponsavel->getCampo("nom_registro");
                    $arResponsaveisSessao[$nregistros]['sequencia'] = $rsListaResponsavel->getCampo("sequencia");

                    Sessao::write( "responsaveis", $arResponsaveisSessao );
                    $rsListaResponsaveis = new RecordSet;
                    $rsListaResponsaveis->preenche ( $arResponsaveisSessao );
                    $rsListaResponsaveis->ordena("num_cgm");

                    montaListaResponsaveis ( $rsListaResponsaveis );
                }
            }
        }

        if ( count($inProfissoesSelecionadas) < 1 ) {
            $js .= "alertaAviso('@Nenhuma profissão selecionada.','form','erro','".Sessao::getId()."');";

            sistemaLegado::executaFrameOculto( $js );
        }else
        if ($num_incluiu == 0) {
            if ($newResponsavel) {
                $js .= "alertaAviso('@Profissão do responsável não corresponde a nenhuma das profissões das atividades da inscrição econômica! (".$newResponsavel.")','form','erro','".Sessao::getId()."');";
            } else {
                $js .= "alertaAviso('@Código do Responsável inválido. (".$newResponsavel.")','form','erro','".Sessao::getId()."');";
            }

            sistemaLegado::executaFrameOculto( $js );
        }
        break;

    case "excluirResponsavel":
        $cgm_excluir = $_REQUEST['inIndice1'];
        $profissao_excluir = $_REQUEST['inIndice2'];
        $arTmpAtividade = array ();
        $inCountArray = 0;
        $arResponsaveisSessao = Sessao::read( "responsaveis" );
        $nregistros = count ( $arResponsaveisSessao );
        for ($inCount = 0; $inCount < $nregistros; $inCount++) {
            if ( ( $arResponsaveisSessao[$inCount]["num_cgm"] != $cgm_excluir ) && ( $arResponsaveisSessao[$inCount]["num_cgm"] != $profissao_excluir ) ) {
                $arTmpAtividade[$inCountArray] = $arResponsaveisSessao[$inCount];
                $inCountArray++;
            }
        }

        Sessao::write( "responsaveis", $arTmpAtividade );

        $rsListaResponsaveis = new RecordSet;
        $rsListaResponsaveis->preenche ( $arTmpAtividade );
        $rsListaResponsaveis->ordena("num_cgm");

        montaListaResponsaveis ( $rsListaResponsaveis );
        break;
}

?>
