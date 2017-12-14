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
    * Página de Frame Oculto para Estornar Baixa Manual de Arrecadacao
    * Data de Criação   : 23/05/2006

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Fernando Piccini Cercato

    * @ignore

    * $Id: OCEstornarBaixaManual.php 65763 2016-06-16 17:31:43Z evandro $

    * Casos de uso: uc-05.03.10
*/

/*
$Log$
Revision 1.2  2006/09/15 10:55:12  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GA_CGM_NEGOCIO."RCGM.class.php" );
include_once ( CAM_GT_MON_NEGOCIO."RMONAgencia.class.php" );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMInscricaoEconomica.class.php" );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMImovel.class.php" );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLote.class.php" );

switch ($_REQUEST["stCtrl"]) {
    case "buscaContribuinte":
        $obRCGM = new RCGM;
        if ($_REQUEST[ 'inNumCGM' ] != "") {
            $obRCGM->setNumCGM( $_REQUEST['inNumCGM'] );
            $obRCGM->consultar( $rsCGM );
            if ( $rsCGM->getNumLinhas() <= 0) {
                $stJs = 'f.inNumCGM.value = "";';
                $stJs .= 'f.inNumCGM.focus();';
                $stJs .= 'd.getElementById("inNomCGM").innerHTML = "&nbsp;";';
                $stJs .= "alertaAviso('@Valor inválido. (".$_REQUEST['inNumCGM'].")','form','erro','".Sessao::getId()."');";
            } else {
                $stJs = 'd.getElementById("inNomCGM").innerHTML = "'.($rsCGM->getCampo('nom_cgm') ? $rsCGM->getCampo('nom_cgm') : "&nbsp;").'";';
            }
        } else {
            $stJs = 'd.getElementById("inNomCGM").innerHTML = "&nbsp;";';
        }

        SistemaLegado::executaFrameOculto($stJs);
        break;

    case "buscaIM":
        if ($_REQUEST["inInscricaoImobiliaria"]) {
            $obRCIMLote   = new RCIMLote;
            $obRCIMImovel = new RCIMImovel( $obRCIMLote );

            $obRCIMImovel->setNumeroInscricao( $_REQUEST["inInscricaoImobiliaria"] );
            //$obRCIMImovel->listarImoveis($rsLista );
            $obErro = $obRCIMImovel->consultarImovel();

            if ( $obErro->ocorreu() ) {
                $stJs = 'f.inInscricaoImobiliaria.value = "";';
                $stJs .= 'f.inInscricaoImobiliaria.focus();';
                $stJs .= 'd.getElementById("stEnderecoImovel").innerHTML = "&nbsp;";';
                $stJs .= "alertaAviso('@Valor inválido. (".$_REQUEST['inInscricaoImobiliaria'].")','form','erro','".Sessao::getId()."');";
            } else {
                $stLogradouro = $obRCIMImovel->getLogradouro();
                $stNumero = $obRCIMImovel->getNumeroImovel();
                $stComplemento = $obRCIMImovel->getComplementoImovel();
                $arLogradouro = explode("-", $stLogradouro);
                $stEnderecoImovel = trim($arLogradouro[1]);
                if ($stNumero) {
                    $stEnderecoImovel = $stEnderecoImovel.', '.$stNumero;
                    if ($stComplemento) {
                        $stEnderecoImovel = $stEnderecoImovel.' - '.$stComplemento;
                    }
                }

                $stJs = 'd.getElementById("stEnderecoImovel").innerHTML = "'.$stEnderecoImovel.'";';
            }
        } else {
            $stJs = 'd.getElementById("stEnderecoImovel").innerHTML = "&nbsp;";';
        }

        SistemaLegado::executaFrameOculto($stJs);
        break;

    case "buscaIE":
        if ($_REQUEST["inInscricaoEconomica"]) {
            $obRCEMInscricaoEconomica = new RCEMInscricaoEconomica;
            $obRCEMInscricaoEconomica->setInscricaoEconomica($_REQUEST["inInscricaoEconomica"]);
            $obRCEMInscricaoEconomica->consultarNomeInscricaoEconomica($rsInscricao);
            if ( !$rsInscricao->eof()) {
                $js = "f.inInscricaoEconomica.value = '".$_REQUEST["inInscricaoEconomica"]."';\n";
                $js .= "d.getElementById('stInscricaoEconomica').innerHTML= '".preg_replace("'","\'",$rsInscricao->getCampo("nom_cgm"))."' ;\n";
            } else {
                $js = "f.inInscricaoEconomica.value = '';\n";
                $js .= "f.inInscricaoEconomica.focus();\n";
                $js .= "d.getElementById('stInscricaoEconomica').innerHTML= '&nbsp;';\n";

                $stMsg = "Inscrição Econômica ".$_REQUEST["inInscricaoEconomica"]."  não encontrada!";
                $js .= "alertaAviso('@".$stMsg."','form','erro','".Sessao::getId()."');";
            }
        } else {
            $js = "d.getElementById('stInscricaoEconomica').innerHTML= '&nbsp;';\n";
        }

        SistemaLegado::executaFrameOculto($js);
        break;

    case "preencheAgencia":
        $obRMONAgencia = new RMONAgencia;
        $js .= "f.inNumAgencia.value=''; \n";
        $js .= "limpaSelect(f.cmbAgencia,1); \n";
        $js .= "f.cmbAgencia[0] = new Option('Selecione','', 'selected');\n";
        if ($_REQUEST['inNumbanco']) {
            $obRMONAgencia->obRMONBanco->setNumBanco( $_REQUEST["inNumbanco"] );
            $obRMONAgencia->listarAgencia( $rsAgencia );

            $inContador = 1;
            while ( !$rsAgencia->eof() ) {
                $inNumAgencia = $rsAgencia->getCampo( "num_agencia" );
                $stNomAgencia = $rsAgencia->getCampo( "nom_agencia" );
                $js .= "f.cmbAgencia.options[$inContador] = new Option('".$stNomAgencia."','".$inNumAgencia."'); \n";
                $inContador++;
                $rsAgencia->proximo();
            }
        }
        if ($_REQUEST["stLimpar"] == "limpar") {
            $js .= "f.inNumAgencia.value='".$_REQUEST["inNumAgencia"]."'; \n";
            $js .= "f.cmbAgencia.options[".$_REQUEST["inNumAgencia"]."].selected = true; \n";
        }
        sistemaLegado::executaFrameOculto($js);
        break;
}
?>
