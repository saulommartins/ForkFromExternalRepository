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
 * Arquivo de instância para manutenção de orgao
 * Data de Criação: 25/07/2005

 * @author Analista: Cassiano
 * @author Desenvolvedor: Cassiano

 $Id: OCManterOrgao.php 66268 2016-08-04 14:37:06Z lisiane $

 Casos de uso: uc-01.05.02

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GA_ORGAN_NEGOCIO."ROrganogramaOrgao.class.php";
include_once CAM_GA_CGM_NEGOCIO."RCGM.class.php";

$stCtrl = $_REQUEST['stCtrl'];

function MontaNivel($stSelecionado = "")
{
    $obRegra = new ROrganogramaOrgao;

    $stCombo  = "inCodNivel";
    $stFiltro = "inCodOrganograma";
    $stJs .= "limpaSelect(f.$stCombo,0); \n";

    # Limpa o campo Código do Nível.
    $stJs .= "jQuery('#inCodNivelTxt').val(''); \n";

    # Posiciona o select na primeira opção.
    $stJs .= "f.$stCombo.options[0] = new Option('Selecione','', 'selected'); \n";

    if ($_REQUEST[$stFiltro] != "") {

        $inCodOrganograma    = $_REQUEST[$stFiltro];
        $inCodOrganogramaAux = $_REQUEST['inCodOrganogramaAux'];

        if ($stSelecionado && ($inCodOrganogramaAux == $inCodOrganograma))
            $stJs .= "jQuery('#".$stCombo."Txt').val('".$stSelecionado."');        \n";
        else {
            $stJs .= "jQuery('#".$stCombo."Txt').val(''); 						   \n";
            $stJs .= "jQuery('#inCodOrganogramaAux').val('".$inCodOrganograma."'); \n";
        }

        $obRegra->obROrganograma->setCodOrganograma( $inCodOrganograma );
        $obRegra->obROrganograma->listarNiveis( $rsCombo );
        $inCount = 0;

        while (!$rsCombo->eof()) {
            $inCount++;
            $inId   = $rsCombo->getCampo("cod_nivel");
            $stDesc = $rsCombo->getCampo("descricao");

            $stSelected = (($stSelecionado == $inId) && ($inCodOrganogramaAux == $inCodOrganograma)) ? "selected" : "";

            $stJs .= "f.$stCombo.options[$inCount] = new Option('".$stDesc."', '".$inId."', '".$stSelected."'); \n";
            $rsCombo->proximo();
        }
    }

    return $stJs;
}

function MontaOrgaoSuperior($stSelecionado = "")
{
    $obRegra = new ROrganogramaOrgao;
    $obRegra->obROrganograma->setCodOrganograma($_REQUEST['inCodOrganograma']);
    $obErro = $obRegra->obROrganograma->consultar();

    $stCombo  = "inCodOrgaoSuperior";

    $stFiltro = "inCodNivelTxt";
    $stJs .= "limpaSelect(f.$stCombo,0); \n";

    # Limpa o campo inCodOrgaoSuperiorTxt
    $stJs .= "jQuery('#inCodOrgaoSuperiorTxt').val('');                       \n";
    $stJs .= "f.$stCombo.options[0] = new Option('Selecione',''); \n";

    if ($_REQUEST[$stFiltro] != "") {
        $inCodNivel    = $_REQUEST[$stFiltro];
        $inCodNivelAux = $_REQUEST['inCodNivelAux'];

        if ($inCodNivel == 1) {
            $stJs .= "jQuery('#".$stCombo."').attr('disabled', true);     \n";
            $stJs .= "jQuery('#".$stCombo."Txt').attr('disabled', true); 	\n";
            $stJs .= "jQuery('#stOrgaoSuperior').html(''); 			\n";
        } else {
            if ($stSelecionado && ($inCodNivelAux == $inCodNivel)) {
                $stJs .= "f.".$stCombo."Txt.value='$stSelecionado';\n";
            } else {
                $stJs .= "f.".$stCombo."Txt.value='';\n";
            }
        $stJs .= "jQuery('#".$stCombo."').attr('disabled', false);     \n";
            $stJs .= "jQuery('#".$stCombo."Txt').attr('disabled', false);  \n";
            $stJs .= "jQuery('#stOrgaoSuperior').html(''); 		\n";

            $stValida = "stCampo=document.frm.".$stCombo.";if (stCampo.value==\"\") {erro = true; mensagem += \"@Campo Órgão Superior inválido!()\";}";
            $stJs .= "jQuery('#stOrgaoSuperior').html('".$stValida."'); \n";
            $obRegra->obROrganograma->setCodOrganograma( $_REQUEST['inCodOrganograma'] );
            $obRegra->obRNivel->setCodNivel( $_REQUEST[$stFiltro] );
            $obRegra->listarOrgaosSuperiores( $rsCombo );

            if ($rsCombo->getNumLinhas()>0) {
                $inCount = 0;
                while (!$rsCombo->eof()) {
                    $inCount++;
                    $inId   = $rsCombo->getCampo("cod_orgao");
                    $stDesc = $rsCombo->getCampo("descricao");

                    if ($stSelecionado == $inId && ($inCodNivelAux == $inCodNivel)) {
                        $stSelected = 'selected';
            $stCodNivelSelecionado = $inCodNivel;
                    }
                    $stJs .= "f.$stCombo.options[$inCount] = new Option('".addslashes($stDesc)."','".$inId."'); \n";
                    $rsCombo->proximo();
                }
                $stJs .= "f.$stCombo.value = '$stCodNivelSelecionado';";
            } else {
                $stJs .= "jQuery('#stOrgaoSuperior').val(''); \n";
                $stJs .= "jQuery('#inCodNivel').val('');      \n";
                $stJs .= "jQuery('#inCodNivelTxt').val('');   \n";
                $stJs .= " erro = true; 					  \n";
                $stJs .= " mensagem = ''; 					  \n";
                $stJs .= "alertaAviso('Para cadastrar o nivel ".$inCodNivel." é necessário antes cadastrar seu nivel superior!', 'form', 'erro', '".Sessao::getId()."');";
            }
        }
    }

    return $stJs;
}

function MontaNorma($stSelecionado = "")
{
    $obRegra = new ROrganogramaOrgao;
    $stCombo  = "inCodNorma";
    $stFiltro = "inCodTipoNorma";
    $stJs .= "limpaSelect(f.$stCombo,0); \n";
    $stJs .= "f.$stCombo.options[0] = new Option('Selecione','', 'selected');\n";

    # Limpa o campo Código da Norma.
    $stJs .= "jQuery('#inCodNormaTxt').val(''); \n";

    if ($_REQUEST[$stFiltro] != "") {
        $inCodTipoNormaAux = $_REQUEST['inCodTipoNormaAux'];
        $inCodTipoNorma    = $_REQUEST[ $stFiltro ];

        if ($stSelecionado != "" && ($inCodTipoNormaAux == $inCodTipoNorma)) {
            $stJs .= "jQuery('#inCodNormaTxt').val('".$stSelecionado."'); \n";
        } else {
            $stJs .= "jQuery('#inCodNormaTxt').val(''); \n";
            $stJs .= "jQuery('#inCodTipoNormaAux').val('".$inCodTipoNorma."'); \n";
        }

        $obRegra->obRNorma->obRTipoNorma->setCodTipoNorma( $inCodTipoNorma );
        $obRegra->obRNorma->listar( $rsCombo );

        $inCount = 0;
        while (!$rsCombo->eof()) {
            $inCount++;
            $inId   = $rsCombo->getCampo("cod_norma");
            $stDesc = addslashes($rsCombo->getCampo("nom_norma"));

            if ($stSelecionado == $inId && ($inCodTipoNormaAux == $inCodTipoNorma)) {
                $stSelected = 'selected';
                $stJs .= "jQuery('#inCodNormaTxt').val('".$inId."'); \n";
            } else {
                $stSelected = '';
            }
            $stJs .= "f.$stCombo.options[$inCount] = new Option('".$stDesc."','".$inId."','".$stSelected."'); \n";
            $rsCombo->proximo();
        }
    }
    return $stJs;
}

function BuscaCGMOrgao()
{
    $obRGM = new RCGM;

    $stText = "inNumCGMOrgao";
    $stSpan = "inNomCGMOrgao";
    if ($_REQUEST[$stText] != "") {
        $obRGM->setNumCGM( $_REQUEST[ $stText ] );
        $obRGM->setTipoPessoa('J');
        $obRGM->listar( $rsCGM );
        $stNull = "&nbsp;";
        if ( $rsCGM->getNumLinhas() <= 0) {
            $stJs .= 'f.'.$stText.'.value = "";';
            $stJs .= 'f.'.$stText.'.focus();';
            $stJs .= 'd.getElementById("inTelefone").innerHTML = "'.$stNull.'";';
            $stJs .= 'd.getElementById("inRamal").innerHTML = "'.$stNull.'";';
            $stJs .= 'd.getElementById("stEndereco").innerHTML = "'.$stNull.'";';
            $stJs .= 'd.getElementById("inNumero").innerHTML = "'.$stNull.'";';
            $stJs .= 'd.getElementById("stEmailOrgao").innerHTML = "'.$stNull.'";';
            $stJs .= 'd.getElementById("inRamal").innerHTML = "'.$stNull.'";';
            $stJs .= 'd.getElementById("'.$stSpan.'").innerHTML = "'.$stNull.'";';
            $stJs .= "alertaAviso('@Valor inválido. (".$_REQUEST[ $stText ].")','form','erro','".Sessao::getId()."');";
        } else {
            //$stJs .= 'f.stEmailResponsavel.value = "'.$rsCGM->getCampo('email').'";';
            $stJs .= 'd.getElementById("inTelefone").innerHTML = "'.(trim($rsCGM->getCampo('fone_residencial'))?$rsCGM->getCampo('fone_residencial'):$stNull).'";';
            $stJs .= 'd.getElementById("inRamal").innerHTML = "'.(trim($rsCGM->getCampo('ramal_residencial'))?$rsCGM->getCampo('ramal_residencial'):$stNull).'";';
            $stJs .= 'd.getElementById("stEndereco").innerHTML = "'.($rsCGM->getCampo('logradouro')? ($rsCGM->getCampo('tipo_logradouro').' '.$rsCGM->getCampo('logradouro')):$stNull).'";';
            $stJs .= 'd.getElementById("inNumero").innerHTML = "'.(trim($rsCGM->getCampo('numero'))?$rsCGM->getCampo('numero'):$stNull).'";';
            $stJs .= 'd.getElementById("stEmailOrgao").innerHTML = "'.($rsCGM->getCampo('e_mail')?$rsCGM->getCampo('e_mail'):$stNull).'";';
            $stJs .= 'd.getElementById("inRamal").innerHTML = "'.($rsCGM->getCampo('ramal_comercial')?$rsCGM->getCampo('ramal_comercial'):$stNull).'";';
            $stJs .= 'd.getElementById("'.$stSpan.'").innerHTML = "'.$rsCGM->getCampo('nom_cgm').'";';
        }
    } else {
        $stNull = "&nbsp;";
        $stJs .= 'd.getElementById("inTelefone").innerHTML = "'.$stNull.'";';
        $stJs .= 'd.getElementById("inRamal").innerHTML = "'.$stNull.'";';
        $stJs .= 'd.getElementById("stEndereco").innerHTML = "'.$stNull.'";';
        $stJs .= 'd.getElementById("inNumero").innerHTML = "'.$stNull.'";';
        $stJs .= 'd.getElementById("stEmailOrgao").innerHTML = "'.$stNull.'";';
        $stJs .= 'd.getElementById("'.$stSpan.'").innerHTML = "'.$stNull.'";';
    }

    return $stJs;
}

function BuscaCGMResponsavel()
{
    $obRegra = new ROrganogramaOrgao;

    $stText = "inNumCGMResponsavel";
    $stSpan = "inNomCGMResponsavel";
    $stNull = "&nbsp;";
    if ($_REQUEST[ $stText ] != "" AND $_GEY[ $stText ] != "0") {
        $obRegra->obRCgmPF->setNumCGM( $_REQUEST[ $stText ] );
        $obRegra->obRCgmPF->consultarCGM( $rsCGM );

        if ( $rsCGM->getNumLinhas() <= 0) {
            $stJs .= 'f.'.$stText.'.value = "";';
            $stJs .= 'f.'.$stText.'.focus();';
            $stJs .= 'd.getElementById("inTelefoneCelular").innerHTML = "'.$stNull.'";';
            $stJs .= 'd.getElementById("inTelefoneComercial").innerHTML = "'.$stNull.'";';
            $stJs .= 'd.getElementById("inRamalComercial").innerHTML = "'.$stNull.'";';
            $stJs .= 'd.getElementById("stEmailResponsavel").innerHTML = "'.$stNull.'";';
            $stJs .= 'd.getElementById("'.$stSpan.'").innerHTML = "'.$stNull.'";';
            $stJs .= "alertaAviso('@Valor inválido. (".$_REQUEST[ $stText ].")','form','erro','".Sessao::getId()."');";
        } else {
            $stJs .= 'd.getElementById("stEmailResponsavel").innerHTML = "'.($rsCGM->getCampo('e_mail')?$rsCGM->getCampo('e_mail'):$stNull).'";'."\n";
            $stJs .= 'd.getElementById("inTelefoneCelular").innerHTML = "'.(trim($rsCGM->getCampo('fone_celular'))?$rsCGM->getCampo('fone_celular'):$stNull).'";'."\n";
            $stJs .= 'd.getElementById("inRamalComercial").innerHTML = "'.(trim($rsCGM->getCampo('ramal_comercial'))?$rsCGM->getCampo('ramal_comercial'):$stNull).'";'."\n";
            $stJs .= 'd.getElementById("inTelefoneComercial").innerHTML = "'.($rsCGM->getCampo('fone_comercial')?$rsCGM->getCampo('fone_comercial'):$stNull).'";'."\n";
            $stJs .= 'd.getElementById("'.$stSpan.'").innerHTML = "'.($rsCGM->getCampo('nom_cgm')?$rsCGM->getCampo('nom_cgm'):$stNull).'";'."\n";
        }
    } else {
            $stJs .= 'f.'.$stText.'.value = "";'."\n";
            $stJs .= 'f.'.$stText.'.focus();'."\n";
            $stJs .= 'd.getElementById("inTelefoneCelular").innerHTML = "'.$stNull.'";'."\n";
            $stJs .= 'd.getElementById("inTelefoneComercial").innerHTML = "'.$stNull.'";'."\n";
            $stJs .= 'd.getElementById("inRamalComercial").innerHTML = "'.$stNull.'";'."\n";
            $stJs .= 'd.getElementById("stEmailResponsavel").innerHTML = "'.$stNull.'";'."\n";
            $stJs .= 'd.getElementById("'.$stSpan.'").innerHTML = "'.$stNull.'";'."\n";
            $stJs .= "alertaAviso('@Valor inválido. (".$_REQUEST[ $stText ].")','form','erro','".Sessao::getId()."');"."\n";
    }    return $stJs;
}

function BuscaNormaDoOrgaoSuperior()
{
    $obRegra = new ROrganogramaOrgao;

    if ($_REQUEST[inCodOrgaoSuperior] != '') {
        $obRegra->setCodOrgao($_REQUEST['inCodOrgaoSuperior']);
        $obRegra->obROrganograma->setCodOrganograma($_REQUEST['inCodOrganograma']);
        $obErro = $obRegra->consultar();
        if (!$obErro->ocorreu()) {
            $inCodNorma = $obRegra->obRNorma->getCodNorma();
            $inCodTipoNorma = $obRegra->obRNorma->obRTipoNorma->getCodTipoNorma();
            $stJs .= "f.inCodTipoNorma.value = '$inCodTipoNorma';";
            $stJs .= "f.inCodTipoNormaTxt.value = '$inCodTipoNorma';";
            $_REQUEST['inCodTipoNorma'] = $inCodTipoNorma;
            $obRegra = new ROrganogramaOrgao;
            $stJs .= MontaNorma($inCodNorma);
        }
    } else {
        $stJs .= "f.inCodTipoNorma.value = '';";
        $stJs .= "f.inCodTipoNormaTxt.value = '';";
        $stJs .= "limpaSelect(f.inCodNorma,0);";
        $stJs .= "f.inCodNormaTxt.value = '';";
    }

    return $stJs;
}

# Retirada as variaveis globais.
#
# $obRegra = new ROrganogramaOrgao;
# $rsNorma = new RecordSet;

// Acoes por pagina

switch ($stCtrl) {
    case "MontaNivel":
        $stJs = MontaNivel($_REQUEST['inCodNivelTxt']);
    break;

    case "MontaOrgaoSuperior":
        $stJs = MontaOrgaoSuperior($_REQUEST['inCodOrgaoSuperiorTxt']);
    break;

    case "buscaCGMResponsavel":
        echo BuscaCGMResponsavel();
    break;

    case "buscaCGMOrgao":
        echo BuscaCGMOrgao();
    break;

    case "MontaNorma":
        $stJs = MontaNorma($_REQUEST['inCodNormaTxt']);
    break;

    case 'preencheInner':
        $stJs .= BuscaCGMOrgao();
        $stJs .= BuscaCGMResponsavel();
        $stJs .= MontaNorma ($_REQUEST['inCodNormaTxt']);
        echo $stJs;
        unset($stJs);
    break;

    case 'recuperaNormaDoOrgaoSuperior':
        $stJs = BuscaNormaDoOrgaoSuperior();
    break;
}

if ($stJs) {
    echo $stJs;
}

?>
