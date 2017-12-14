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
    * Página de Oculto do Configuração Dirf
    * Data de Criação: 22/11/2007

    * @author Diego Lemos de Souza

    * Casos de uso: uc-04.08.14

    $Id: OCNaturezaEstabelecimento.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

function preencheNaturezaEstabelecimento()
{
    include_once( CAM_GRH_IMA_MAPEAMENTO."TIMANaturezaEstabelecimento.class.php" );
    $obTIMANaturezaEstabelecimento = new TIMANaturezaEstabelecimento();
    $stJs = "d.getElementById('stNatureza').innerHTML = '&nbsp;';\n";
    if ($_GET["inNatureza"] != "") {
        $stFiltro = " AND cod_natureza = ".$_GET["inNatureza"];
        $stFiltro = " WHERE ".substr($stFiltro,4,strlen($stFiltro));
        $obTIMANaturezaEstabelecimento->recuperaTodos($rsLista,$stFiltro);
        if ($rsLista->inNumLinhas>0) {
            $stDescricao = $rsLista->getCampo("descricao");
            $stJs = "d.getElementById('stNatureza').innerHTML = '$stDescricao';\n";
        } else {
            $stJs .= "d.getElementById('inNatureza').innerHTML = '';\n";
            $stJs .= "d.getElementById('inNatureza').value = '';\n";
            $stAviso = "alertaAviso('Código da Natureza do Estabelecimento inválido.(".$_GET["inNatureza"].")', 'form','erro','".Sessao::getId()."');";
            echo  $stAviso;
        }
    }

    return $stJs;
}

switch ($_GET['stCtrl']) {
    case "preencheNaturezaEstabelecimento":
        $stJs = preencheNaturezaEstabelecimento();
        break;
}
if ($stJs) {
    echo $stJs;
}

?>
