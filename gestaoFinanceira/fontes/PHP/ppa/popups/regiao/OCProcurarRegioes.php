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
* Arquivo instância para popup de Regiao
* Data de Criação: 21/06/2007

* @author Analista: Anelise
* @author Desenvolvedor: Leandro André Zis
* @author Desenvolvedor: Marcio Medeiros

* Casos de uso :uc-02.09.011
*/

/*
$Log$
Revision 1.1  2007/06/21 19:38:28  leandro.zis
popup produto do ppa

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once(CAM_GF_PPA_MAPEAMENTO."TPPARegiao.class.php");

$stCampoCod  	= $_GET['stNomCampoCod'];
$stCampoDesc 	= $_GET['stIdCampoDesc'];
$inCodigo    	= $_REQUEST[ 'inCodigo' ];

switch ($_REQUEST["stCtrl"]) {
    case "buscaRegiao":
        $obTPPARegiao = new TPPARegiao();
        $rsRegiao = new RecordSet();
        $obTPPARegiao->setDado('cod_regiao', $_REQUEST['inCodigo']);

        $obTPPARegiao->recuperaPorChave($rsRegiao);

        if ($rsRegiao->inNumLinhas > 0) {
            $stJs = "document.getElementById('".$stCampoDesc."').innerHTML = '".addslashes($rsRegiao->getCampo('nome'))."';";
        } else {
            $stJs = "document.getElementById('".$stCampoDesc."').innerHTML = '&nbsp;';";
            $stJs.= "document.getElementById('".$stCampoCod."').value = '';";
            $stJs.= "alertaAviso('@Código da Região (". $inCodigo .") não encontrado.', 'form','erro','".Sessao::getId()."');";
        }
        echo $stJs;
    break;
}

?>
