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
* Oculto de Processamento e para PopUp de Penalidade
* Data de Criação: 17/10/2006

* @author Analista: Lucas Teixeira Stephanou
* @author Desenvolvedor: Lucas Teixeira Stephanou

* Casos de uso :uc-03.05.28
*/

/*
$Log$
Revision 1.1  2006/10/17 12:00:15  domluc
PopUp de Penalidade usada no Componente de Penalidade.

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once(CAM_GP_LIC_MAPEAMENTO."TLicitacaoPenalidade.class.php");

$stCampoCod  = $_GET['stNomCampoCod'];
$stCampoDesc = $_GET['stIdCampoDesc'];
$inCodigo    = $_REQUEST[ $stCampoCod ];

switch ($_GET['stCtrl']) {

    case 'buscaPopup':
    default:
        if ($inCodigo != "") {
            $obTLicitacaoPenalidade = new TLicitacaoPenalidade();
            $obTLicitacaoPenalidade->setDado('cod_penalidade', $inCodigo );
            $rsObjeto = new RecordSet;
            $obTLicitacaoPenalidade->recuperaPorChave($rsObjeto);
            $stObjeto = $rsObjeto->getCampo('descricao');
            $stJs .= "d.getElementById('".$stCampoDesc."').value = '".$stObjeto."';";
            $stJs .= "retornaValorBscInner( '".$stCampoCod."', '".$stCampoDesc."', '".$_GET['stNomForm']."', '".$stObjeto."');";
            if (!$stObjeto) {
                $stJs .= "alertaAviso('@Código da Penalidade(". $inCodigo .") não encontrada.', 'form','erro','".Sessao::getId()."');";
            }
        } else {
            $stJs .= "d.getElementById('".$stCampoDesc."').innerHTML = '&nbsp;';";
        }
        sistemaLegado::executaFrameOculto( $stJs );
    break;

}

?>
