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
 * Página de Oculto para Relatório de modelos
 * Data de Criação   : 25/01/2006

 * @author Tonismar Régis Bernardo

 * @ignore

 * Casos de uso : uc-06.02.11
                  uc-06.02.12
                  uc-06.02.13
                  uc-06.02.15
                  uc-06.02.17
                  uc-06.02.18

 $Id: OCModelosAMF.php 59612 2014-09-02 12:00:51Z gelson $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';
include_once CAM_GF_LDO_MAPEAMENTO.'TLDO.class.php';

$stCtrl = $request->get("stCtrl");

$stPrograma = 'ModelosAMF';
$pgForm = 'FM'.$stPrograma.'.php';
$pgOcul = 'OC'.$stPrograma.'.php';

$arValores = Sessao::read('arValores');

switch ($stCtrl) {
    case 'Valida':
        if (count($arValores) == 0) {
            $stMensagem = 'Nenhum recurso informado';
        }
        if (!$stMensagem) {
            $stJs .= "document.frm.action='OCGeraAMFDemonstrativo5.php?Sessao::getId()'; \n";
            $stJs .= "document.frm.submit();                       \n";
        } else {
            $stJs .= "alertaAviso('".$stMensagem."!','frm','alerta','".Sessao::getId()."'); \n";
        }
    break;

    case 'incluirLista':
        if ($_REQUEST['inCodRecurso'] == '') {
            $stMensagem = 'Preencha o campo Recurso';
        } else {
            if (count( $arValores ) > 0) {
                foreach ($arValores AS $arTemp) {
                    if ($arTemp['inCodRecurso'] == $_REQUEST['inCodRecurso']) {
                        $stMensagem = 'Este item já está na lista';
                        break;
                    }
                }
            }
        }

        if (!$stMensagem) {
            $inCount = count($arValores);

            $arValores[$inCount]['id'                ] = $inCount + 1;
            $arValores[$inCount]['inCodRecurso'      ] = str_replace(',','.',str_replace('.','',$_REQUEST["inCodRecurso"]));
            $arValores[$inCount]['stDescricaoRecurso'] = str_replace(',','.',str_replace('.','',$_REQUEST["stDescricaoRecurso"]));

            Sessao::write('arValores', $arValores);

            $stJs .= "$('inCodRecurso').value = '';";
            $stJs .= "$('stDescricaoRecurso').innerHTML = '&nbsp;';";
            $stJs .= montaLista($arValores);
        } else {
            $stJs .= "alertaAviso('".$stMensagem."!','frm','erro','".Sessao::getId()."'); \n";
        }
    break;

    case 'excluirLista':
        foreach ($arValores AS $arTemp) {
            if ($arTemp['inCodRecurso'] != $_REQUEST['inCodRecurso']) {
                $arAux[] = $arTemp;
            }
        }

        $arValores = $arAux;
        Sessao::write('arValores', $arValores);

        $stJs .= montaLista($arValores);
    break;

    case 'montaLista':
        $stJs .= montaLista($arValores, ($_REQUEST['boReadOnly'] == 'true') ? true : false);
    break;

    case 'preencheLDO':
        $stJs  = "jq('#stExercicio').removeOption(/./);";
        $stJs .= 'var arOptions = {';
        if ($_REQUEST['inCodPPA'] != '') {
            $obTLDO = new TLDO;
            $obTLDO->setDado('cod_ppa',$_REQUEST['inCodPPA']);
            $obTLDO->recuperaExerciciosLDO($rsLDO,' ORDER BY ano_ldo ');
            while (!$rsLDO->eof()) {
                $stJs .= "'" . $rsLDO->getCampo('ano_ldo') . "' : '" . $rsLDO->getCampo('ano_ldo') . "',";

                $rsLDO->proximo();
            }
        }
        $stJs .= '};';
        $stJs .= "jq('#stExercicio').addOption(arOptions,false);";
    break;
}

function montaLista($arItens, $boReadOnly = false)
{
    if (!is_array($arItens)) {
        $arItens = array();
    }

    $rsItens = new RecordSet();
    $rsItens->preenche($arItens);

    $obTable = new Table();
    $obTable->setRecordset( $rsItens );
    $obTable->setSummary('Lista de Recursos');

    $obTable->Head->addCabecalho('Código', 10);
    $obTable->Head->addCabecalho('Descrição',60);

    $obTable->Body->addCampo('inCodRecurso', 'C');
    $obTable->Body->addCampo('stDescricaoRecurso', 'E');

    if (!$boReadOnly) {
        $obTable->Body->addAcao('excluir', "JavaScript:ajaxJavaScript('".CAM_GPC_STN_INSTANCIAS."relatorios/OCModelosAMF.php?".Sessao::getId()."&inCodRecurso=%s', 'excluirLista');", array('inCodRecurso'));
    }

    $obTable->montaHTML(true);
    if ($rsItens->getNumLinhas() > 0) {
        return "$('spnLista').innerHTML = '".$obTable->getHtml()."';";
    } else {
        return "$('spnLista').innerHTML = '&nbsp;';";
    }
}

echo $stJs;
