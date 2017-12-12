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
 * Arquivo instância para popup de Objeto
 * Data de Criação: 07/03/2006

 * @author Analista: Diego Barbosa Victoria
 * @author Desenvolvedor: Leandro André Zis

 * Casos de uso :uc-03.04.07, uc-03.04.05

 $Id: OCProcuraObjeto.php 59612 2014-09-02 12:00:51Z gelson $

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GP_COM_MAPEAMENTO."TComprasObjeto.class.php";

function buscaPopup()
{
    $stJs = "";
    $stCampoCod  = $_GET['stNomCampoCod'];
    $stCampoDesc = $_GET['stIdCampoDesc'];
    $inCodigo    = $_REQUEST[ $stCampoCod ];

    if ($inCodigo != "") {
        $obTComprasObjeto = new TComprasObjeto();
        $obTComprasObjeto->setDado('cod_objeto', $inCodigo );
        $rsObjeto = new RecordSet;
        $obTComprasObjeto->recuperaPorChave($rsObjeto);

        $stObjeto = stripslashes($rsObjeto->getCampo('descricao'));

        if ($stObjeto) {
            $stObjeto = nl2br(addslashes(str_replace("\r\n", "\n", preg_replace("/(\r\n|\n|\r)/", "", $stObjeto))));
        }

        $stJs .= "d.getElementById('".$stCampoDesc."').value = '".$stObjeto."';";
        $stJs .= "retornaValorBscInner( '".$stCampoCod."', '".$stCampoDesc."', '".$_GET['stNomForm']."', '".$stObjeto."');";

        if (!$stObjeto) {
            $stJs .= "alertaAviso('@Código do Objeto(". $inCodigo .") não encontrado.', 'form','erro','".Sessao::getId()."');";
        }
    } else {
        $stJs .= "d.getElementById('".$stCampoDesc."').innerHTML = '&nbsp;';";
    }

    sistemaLegado::executaFrameOculto( $stJs );
}

switch ($_GET['stCtrl']) {
    case 'buscaPopup':
    default:
        buscaPopup();
    break;
}

?>
