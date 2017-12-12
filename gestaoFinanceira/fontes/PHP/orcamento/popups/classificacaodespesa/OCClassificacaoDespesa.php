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
    * Página de Listagem de Itens
    * Data de Criação   : 16/07/2004

    * @author Desenvolvedor: Marcelo Boezzio Paulino

    * @ignore

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.01.06
*/

/*
$Log$
Revision 1.8  2006/07/05 20:43:43  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoClassificacaoDespesa.class.php" );

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];

switch ($stCtrl) {
    case "mascaraClassificacao":
        $arMascClassificacao = Mascara::validaMascaraDinamica( $_POST['stMascClassificacao'] , $_POST['inCodClassificacao'] );
        $js .= "f.inCodClassificacao.value = '".$arMascClassificacao[1]."'; \n";
        SistemaLegado::executaIFrameOculto( $js );
    break;
    case 'buscaPopup':
        if ($_POST[ $_GET['stNomCampoCod'] ]) {

            $arMascClassificacao = Mascara::validaMascaraDinamica( $_POST['stMascClassificacao'] , $_POST[$_GET['stNomCampoCod']] );
            $_POST[$_GET['stNomCampoCod']] = $arMascClassificacao[1];
            $obROrcamentoClassificacaoDespesa = new ROrcamentoClassificacaoDespesa;
            $obROrcamentoClassificacaoDespesa->setExercicio( Sessao::getExercicio() );
            $obROrcamentoClassificacaoDespesa->setMascClassificacao( $_POST[$_GET['stNomCampoCod']] );
            $obROrcamentoClassificacaoDespesa->setListarAnaliticas('true');
            $obROrcamentoClassificacaoDespesa->consultar( $rsLista );
            if ( $rsLista->getNumLinhas() > -1 ) {
                $stDescricao = $rsLista->getCampo("descricao");
            } else {
                SistemaLegado::exibeAviso("Despesa não possui conta analítica na contabilidade!(".$_POST[ $_GET['stNomCampoCod'] ].")","","");
            }
            SistemaLegado::executaFrameOculto("retornaValorBscInner( '".$_GET['stNomCampoCod']."', '".$_GET['stIdCampoDesc']."', '".$_GET['stNomForm']."', '".$stDescricao."')");
        } else {
            SistemaLegado::executaFrameOculto("retornaValorBscInner( '".$_GET['stNomCampoCod']."', '".$_GET['stIdCampoDesc']."', '".$_GET['stNomForm']."', '".$stDescricao."')");
        }
    break;
}
?>
