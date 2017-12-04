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
    * PÃ¡gina do Frame oculto para Definir Responsavel Tecnico
    * Data de CriaÃ§Ã£o   : 15/04/2005

    * @author Tonismar Régis Bernardo

    * @ignore

    * $Id: OCDefinirResponsaveis.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.02.10

*/

/*
$Log$
Revision 1.11  2007/03/27 19:28:51  rodrigo
Bug #8768#

Revision 1.10  2006/09/15 14:33:07  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GA_CGM_NEGOCIO."RCGM.class.php"        );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMResponsavelTecnico.class.php");
include_once ( CAM_GT_CEM_NEGOCIO."RCEMInscricaoEconomica.class.php");

$obRCEMResponsavelTecnico = new RCEMResponsavelTecnico;
$obRCEMInscricaoEconomica = new RCEMInscricaoEconomica;

function montaListaResponsavel(&$arListaResponsavel)
{
    $rsListaResponsavel = new Recordset;
    $rsListaResponsavel->preenche( is_array($arListaResponsavel) ? $arListaResponsavel : array() );

    if ( $rsListaResponsavel->getNumLinhas() > 0 ) {
        $obLista = new Lista;
        $obLista->setRecordSet                 ( $rsListaResponsavel    );
        $obLista->setTitulo                    ( "Lista de Responsáveis Técnicos" );
        $obLista->setMostraPaginacao           ( false                  );
        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "&nbsp;"               );
        $obLista->ultimoCabecalho->setWidth    ( 5                      );
        $obLista->commitCabecalho              (                        );
        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "Código"               );
        $obLista->ultimoCabecalho->setWidth    ( 10                     );
        $obLista->commitCabecalho              (                        );
        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "Nome"                 );
        $obLista->ultimoCabecalho->setWidth    ( 45                     );
        $obLista->commitCabecalho              (                        );
        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "Registro"             );
        $obLista->ultimoCabecalho->setWidth    ( 15                     );
        $obLista->commitCabecalho              (                        );
        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "Profissão"            );
        $obLista->ultimoCabecalho->setWidth    ( 15                     );
        $obLista->commitCabecalho              (                        );
        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "&nbsp;"               );
        $obLista->ultimoCabecalho->setWidth    ( 5                      );
        $obLista->commitCabecalho              (                        );

        $obLista->addDado                      (                        );
        $obLista->ultimoDado->setAlinhamento   ( "DIREITA"              );
        $obLista->ultimoDado->setCampo         ( "inNumCGM"             );
        $obLista->commitDado                   (                        );
        $obLista->addDado                      (                        );
        $obLista->ultimoDado->setCampo         ( "inNomCGM"             );
        $obLista->commitDado                   (                        );
        $obLista->addDado                      (                        );
        $obLista->ultimoDado->setCampo         ( "inRegistro"           );
        $obLista->commitDado                   (                        );
        $obLista->addDado                      (                        );
        $obLista->ultimoDado->setCampo         ( "stProfissao"          );
        $obLista->commitDado                   (                        );
        $obLista->addAcao                      (                        );
        $obLista->ultimaAcao->setAcao          ( "EXCLUIR"              );
        $obLista->ultimaAcao->setFuncao        ( true                   );
        $obLista->ultimaAcao->setLink( "JavaScript:excluirValor('excluirResponsavel');" );
        $obLista->ultimaAcao->addCampo         ( "1","inLinha"   );
        $obLista->commitAcao                   (                        );

        $obLista->montaHtml();
        $stHTML =  $obLista->getHtml();
        $stHTML = str_replace                  ( "\n","",$stHTML        );
        $stHTML = str_replace                  ( "  ","",$stHTML        );
        $stHTML = str_replace                  ( "'","\\'",$stHTML      );
    } else {
        $stHTML = "&nbsp;";
    }
    $stJs  = "d.getElementById('stProfissao').innerHTML = '&nbsp;';\n";
    $stJs .= "d.getElementById('lsListaResponsavel').innerHTML = '".$stHTML."';\n";
    $stJs .= "d.getElementById('inNomCGM').innerHTML = '&nbsp;';\n";
    $stJs .= "f.inNumCGM.value = ''";

    return $stJs;
}

switch ($_REQUEST["stCtrl"]) {
    case "buscaProfissao":
        if (!$_REQUEST['inNumCGM']) {
            $stJs .= 'f.inNumCGM.value = "";';
            $stJs .= 'd.getElementById("inNomCGM").innerHTML = "&nbsp;";';
            $stJs .= 'd.getElementById("stProfissao").innerHTML = "&nbsp;";';
        } else {
            include_once ( CAM_GT_CEM_NEGOCIO."RCEMAtividadeProfissao.class.php");
            $obRCEMAtividadeProfissao = new RCEMAtividadeProfissao;

            $stNull = '&nbsp;';
            $obRCEMResponsavelTecnico->setNumCGM( $_REQUEST['inNumCGM'] );
            $obRCEMResponsavelTecnico->listarTecnico( $rsResponsavel );

            $codigo_profissao = $rsResponsavel->getCampo('cod_profissao');
            $obRCEMAtividadeProfissao->setCodigoProfissao   (   $codigo_profissao   );
            $obRCEMAtividadeProfissao->setCodigosAtividades (   $arAtividades       );

            if ( $rsResponsavel->getNumLinhas() > 0 ) {
                $obRCEMAtividadeProfissao->listarAtividadesProfissoes ( $rsLista );
                if ( $rsLista->getNumLinhas() > 0 ) {
                    $stJs .= 'd.getElementById("inNomCGM").innerHTML = "'.$rsResponsavel->getCampo('nom_cgm').'\n";';
                    $stJs .= 'd.getElementById("stProfissao").innerHTML =  "'.$rsResponsavel->getCampo('nom_profissao').'";';
                } else {
                    $stJs .= 'f.inNumCGM.value = "";';
                    $stJs .= 'd.getElementById("inNomCGM").innerHTML = "&nbsp;";';
                    $stJs .= 'd.getElementById("stProfissao").innerHTML = "&nbsp;";';
                    $stJs .= "alertaAviso('@Valor inválido. (Responsável não pertence à lista de atividades da inscrição)','form', 'erro','".Sessao::getId()."');";
                }
            } else {
                $stJs .= 'f.inNumCGM.value = "";';
                $stJs .= 'd.getElementById("inNomCGM").innerHTML = "&nbsp;";';
                $stJs .= 'd.getElementById("stProfissao").innerHTML = "&nbsp;";';
                $stJs .= "alertaAviso('@Valor inválido. (".$_REQUEST['inNumCGM'].")','form', 'erro','".Sessao::getId()."');";

            }
        }
        sistemaLegado::executaFrameOculto($stJs);
        break;

    case "limparResponsavel":

        $stJs = '';
        $stJs.= "d.getElementById('inNomCGM').innerHTML = '&nbsp;';";
        $stJs.= "d.getElementById('stProfissao').innerHTML = '&nbsp;';";
        $stJs.= "f.inNumCGM.value = '';";
        sistemaLegado::executaFrameOculto( $stJs );

    break;
    case "montaResponsavel":
        $rsResponsavel = new RecordSet;
        $obRCEMResponsavelTecnico->setNumCGM( $_REQUEST['inNumCGM'] );
        $obRCEMResponsavelTecnico->listarTecnico( $rsResponsavel );
        $stMsg = "";

        $boErro = false;
        $arResponsaveisSessao = Sessao::read( "responsaveis" );
        foreach ($arResponsaveisSessao as  $inChave => $arResponsaveis) {
            if ($arResponsaveis["inNumCGM"] == $_REQUEST["inNumCGM"]) {
                $boErro = true;
                $stMsg  = "Responsável já informado!";
                break;
            }
        }

        if ($boErro) {
            $stJs = "f.inNumCGM.value = '';\n";
            $stJs .= "d.getElementById('inNomCGM').innerHTML = '&nbsp;';\n";
            $stJs .= "d.getElementById('stProfissao').innerHTML = '&nbsp;';\n";
            $stJs .= "alertaAviso('".$stMsg."(".$_REQUEST["inNumCGM"].")','form','erro','".Sessao::getId()."', '../');";

        } else {
            $stJs  = "f.inNumCGM.value = '';\n";
            $stJs .= "d.getElementById('inNomCGM').innerHTML = '&nbsp;';\n";
            $stJs .= "d.getElementById('stProfissao').innerHTML = '&nbsp;';\n";
            if (!($rsResponsavel->getCampo('num_registro')=="")) {
                $registro = $rsResponsavel->getCampo('nom_registro')." ".$rsResponsavel->getCampo('num_registro')." ".$rsResponsavel->getCampo('sigla_uf');
            } else {
                $registro = "";
            }
            $arResponsaveis = array( "sequencia"   => $rsResponsavel->getCampo('sequencia'),
                                     "inNumCGM"    => $_REQUEST["inNumCGM"],
                                     "inNomCGM"    => $rsResponsavel->getCampo('nom_cgm'),
                                     "inRegistro"  => $registro,
                                     "stProfissao" => $rsResponsavel->getCampo('nom_profissao'),
                                     "inCodigoProfissao" => $rsResponsavel->getCampo('cod_profissao'));
            $arResponsaveis["inLinha"] = count( $arResponsaveisSessao );
            $arResponsaveisSessao[] = $arResponsaveis;
            Sessao::write( "responsaveis", $arResponsaveisSessao );
            $stJs .= montaListaResponsavel( $arResponsaveisSessao );
        }
        sistemaLegado::executaFrameOculto($stJs);

    break;
    case "excluirResponsavel":
        $inLinha = $_REQUEST["inLinha"] ? $_REQUEST["inLinha"] : 0;
        $arNovaListaResponsaveis = array();
        $inContLinha = 0;
        $arResponsaveisSessao = Sessao::read( "responsaveis" );
        foreach ($arResponsaveisSessao as $inChave => $arResponsaveis) {
            if ($inChave != $inLinha) {
                $arResponsaveis["inLinha"] = $inContLinha++;
                $arNovaListaResponsaveis[] = $arResponsaveis;
            } else {
                if ($inContLinha!=0) {

                }
                $arResponsaveisSessao[]= $arResponsaveis;
            }
        }

        print_r($arResponsaveisSessao);
        Sessao::write( "responsaveis", $arNovaListaResponsaveis );

        $rsListaResponsavel = new RecordSet;
        $stJs = montaListaResponsavel( $arNovaListaResponsaveis );
        sistemaLegado::executaFrameOculto($stJs);
    break;
    case "carregaResponsaveis":
        $responsaveis = Sessao::read( "responsaveis" );
        $stJs = montaListaResponsavel( $responsaveis );
        sistemaLegado::executaFrameOculto($stJs);
    break;
}

?>
