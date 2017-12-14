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
 * Página Oculta do componente IPopUpConta
 * Data de Criação: 07/09/2009
 * Copyright CNM - Confederação Nacional de Municípios
 *
 * @author Janilson Mendes Pereira da Silva <janilson.mendes>
 * @package GF
 * @subpackage LDO
 * @uc uc-02.10.03
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GF_ORC_NEGOCIO . "ROrcamentoClassificacaoDespesa.class.php";

switch ($_GET['stCtrl']) {
    case 'contaDespesa':
        if ($_GET[$_GET['stNomCampoCod']]) {
            $obROrcamentoClassificacaoDespesa = new ROrcamentoClassificacaoDespesa;
            $obROrcamentoClassificacaoDespesa->setCodConta($_GET[$_GET['stNomCampoCod']]);
            $obROrcamentoClassificacaoDespesa->setExercicio($_GET['stExercicio']);
            $obROrcamentoClassificacaoDespesa->consultar($rsClassificacao);
            if ($rsClassificacao->getNumLinhas() > 0) {
                $stDescricao = $rsClassificacao->getCampo("descricao");
                $stJs.= "retornaValorBscInner( '".$_GET['stNomCampoCod']."', '".$_GET['stIdCampoDesc']."', 'frm', '".$stDescricao."');";
            } else {
                $stJs = "document.frm.".$_GET['stNomCampoCod'].".value='';";
                $stJs.= "document.getElementById('".$_GET['stIdCampoDesc']."').innerHTML = '&nbsp;';";
                $stJs.= "document.frm.".$_GET['stNomCampoCod'].".focus();";
                $stJs.= "alertaAviso('Código da Conta Inválida.','form','erro','".Sessao::getId()."');";
                $stJs.= "document.getElementById('".$_GET['stIdCampoDesc']."').innerHTML = '&nbsp;';";
            }
        } else {
            $stJs.= "document.getElementById('".$_GET['stIdCampoDesc']."').innerHTML = '&nbsp;';";
        }
    break;
}

if ($stJs) {
    echo $stJs;
}
