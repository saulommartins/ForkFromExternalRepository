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
* Oculto de Processamento e para PopUp de Empenho
* Data de Criação: 18/10/2006

* @author Analista: Lucas Teixeira Stephanou
* @author Desenvolvedor: Lucas Teixeira Stephanou

* Casos de uso : uc-02.03.03
*/

/*
$Log$
Revision 1.3  2006/11/28 17:56:51  fernando
Adicionado o cod_empenho e o cod_entidade na sessao->filtro para poderem ser usados nos forms que utilizem este componente.

Revision 1.2  2006/11/21 16:07:03  larocca
Inclusão Ordem de Compra

Revision 1.1  2006/10/18 13:45:48  domluc
PopUp de Empenho, e oculto compartilhado com
componente IPopUpEmpenho

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

$stCampoCod  = $_GET['stNomCampoCod'];
$stCampoDesc = $_GET['stIdCampoDesc'];
$inCodigo    = $_REQUEST[ $stCampoCod ];

list($inCodEmpenho,$stExercicio) = explode('/',$_REQUEST['inCodEmpenho']);
if ($stExercicio == '') {
    $stExercicio = Sessao::getExercicio();
}

($_POST["stCtrl"])?$stCtrl = $_POST["stCtrl"] : $stCtrl = $_GET["stCtrl"];
switch ($stCtrl) {
case 'buscaPopup':
default:
    if ($inCodigo != "") {
        switch ($_REQUEST['stTipoBusca']) {
        case 'obra_tcmgo':
            include_once( CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenho.class.php" );
            //Consulta

            $obTEmpenhoEmpenho = new TEmpenhoEmpenho;
            $stFiltro .= " AND e.exercicio    = '".$stExercicio."'  \n";

            if ($_REQUEST['inCodEntidadeEmpenho'] != "") {
                $stFiltro .= " AND e.cod_entidade = ".$_REQUEST['inCodEntidadeEmpenho']. "\n";
            }
            if ($_REQUEST['inCodEmpenho']) {
                $stFiltro .= " AND e.cod_empenho = ".$inCodEmpenho." \n";
            }

            $stFiltro .= " AND  pe.cod_estrutural LIKE '4.4.9.0.51.%' ";

            $obTEmpenhoEmpenho->recuperaEmpenhoPreEmpenhoObras($rsEmpenho, $stFiltro);

            break;
        
        default:
            require_once( CAM_GF_EMP_NEGOCIO."REmpenhoEmpenho.class.php" );
            $obRegra = new REmpenhoEmpenho;

            $stCodEntidade = $_REQUEST[ 'inCodEntidadeEmpenho' ] ;
            $stExercicio = ( $stExercicio) ? $stExercicio : Sessao::getExercicio();

            $obRegra->obROrcamentoEntidade->setCodigoEntidade( $stCodEntidade   );
            $obRegra->setExercicio          ( $stExercicio                      );
            $obRegra->setCodEmpenhoInicial  ( $inCodEmpenho );
            $obRegra->setCodEmpenhoFinal    ( $inCodEmpenho );

            if ( $stExercicio == Sessao::getExercicio() ) {
                $obRegra->listarConsultaEmpenho( $rsEmpenho );
            } else {
                $obRegra->listarRestosConsultaEmpenho( $rsEmpenho );
            }

            break;
        }

        if ( $rsEmpenho->getNumLinhas() < 1 ) {
            $stJs .= "alertaAviso('@Empenho (". $inCodigo .") não encontrado.', 'form','erro','".Sessao::getId()."');";
            $stJs .= "jq_('#".$stCampoCod."').val('');";
            $stJs .= "jq_('#".$stCampoDesc."').html('&nbsp;');";
            $stJs .= "jq_('#inCodPreEmpenho').val('');";

        } else {
            $stCod = $rsEmpenho->getCampo( 'cod_empenho' ) . '/' . $rsEmpenho->getCampo( 'exercicio' );
            $stDesc = $rsEmpenho->getCampo( 'dt_empenho' ) . ' - ' . $rsEmpenho->getCampo( 'nom_fornecedor' );
            $stPre = $rsEmpenho->getCampo( 'cod_pre_empenho' );

            Sessao::write('cod_empenho', $stCod);
            Sessao::write('cod_entidade', $stCodEntidade);

            $stJs .= "d.getElementById('".$stCampoCod."').value = '".$stCod."';";
            $stJs .= "d.getElementById('".$stCampoDesc."').value = '".$stDesc."';";
            $stJs .= "d.getElementById('inCodPreEmpenho').value = '".$stPre."';";
            $stJs .= "retornaValorBscInner( '".$stCampoCod."', '".$stCampoDesc."', '".$_GET['stNomForm']."', '".$stDesc."');";
        }

    } else {
        $stJs .= "d.getElementById('".$stCampoDesc."').innerHTML = '&nbsp;';";
    }

    sistemaLegado::executaFrameOculto( $stJs );
    break;

}

?>
