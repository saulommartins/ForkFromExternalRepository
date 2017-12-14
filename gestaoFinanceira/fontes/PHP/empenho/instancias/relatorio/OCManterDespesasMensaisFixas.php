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
    * Página de Oculto de Relatório Despesas Mensais Fixas
    * Data de Criação : 04/09/2006

    * @author Analista: Cleisson Barboza
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @ignore

    $Revision: 30805 $
    $Name$
    $Autor: $
    $Date: 2006-10-31 14:37:06 -0300 (Ter, 31 Out 2006) $

    * Casos de uso: uc-02.03.33
*/

/**

$Log$
Revision 1.4  2006/10/31 17:37:06  larocca
Bug #7207#

Revision 1.3  2006/10/16 16:32:21  larocca
Bug #7207#

Revision 1.2  2006/09/08 10:23:00  tonismar
relatório de despesas fixas

Revision 1.1  2006/09/05 11:50:26  tonismar
desenvolvendo relatório de despesas fixas

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GF_EMP_NEGOCIO."REmpenhoRelatorioDespesasMensaisFixas.class.php" );

$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];

$arFiltro = Sessao::read('filtroRelatorio');

switch ($_REQUEST['stCtrl']) {
    case "buscarLocal":
        include_once(TORG."TOrganogramaLocal.class.php");

        $rsLocal = new RecordSet();
        $obTOrganogramaLocal = new TOrganogramaLocal();
        $obTOrganogramaLocal->setDado( "cod_local", $_REQUEST['inCodLocal'] );
        $obTOrganogramaLocal->recuperaPorChave( $rsLocal );

        if ( $rsLocal->getNumLinhas() >= 1 ) {
            $stJs  = "f.inCodLocal.value = ".$rsLocal->getCampo('cod_local')."\n";
            $stJs .= "d.getElementById('stLocal').innerHTML = '".$rsLocal->getCampo('descricao')."' \n";
        } else {
            $stJs  = "f.inCodLocal.value = '' \n";
            $stJs .= "d.getElementById('stLocal').innerHTML = '&nbsp;'  \n";
        }
    break;
    default:

        $obREmpenhoRelatorioDespesasMensaisFixas = new REmpenhoRelatorioDespesasMensaisFixas();
        $obREmpenhoRelatorioDespesasMensaisFixas->setCodEntidade( $arFiltro['inCodEntidade'] );
        $obREmpenhoRelatorioDespesasMensaisFixas->setExercicio( $arFiltro['stExercicio'] );
        $obREmpenhoRelatorioDespesasMensaisFixas->setPeriodicidadeInicial( $arFiltro['stDataInicial'] );
        $obREmpenhoRelatorioDespesasMensaisFixas->setPeriodicidadeFinal( $arFiltro['stDataFinal'] );
        $obREmpenhoRelatorioDespesasMensaisFixas->setCodTipo( $arFiltro['inCodTipo'] );
        $obREmpenhoRelatorioDespesasMensaisFixas->setContrato( $arFiltro['inContrato'] );
        $obREmpenhoRelatorioDespesasMensaisFixas->setCodLocal( $arFiltro['inCodLocal'] );
        $obREmpenhoRelatorioDespesasMensaisFixas->setCodDotacao( $arFiltro['inCodDotacao'] );
        $obREmpenhoRelatorioDespesasMensaisFixas->setCodCredor( $arFiltro['inCodCredor'] );

        $obREmpenhoRelatorioDespesasMensaisFixas->geraRelatorioDespesasMensaisFixas( $arDespesa, $arDetalhe );

        Sessao::write('rsRecordSet0', $arDespesa);
        Sessao::write('rsRecordSet1', $arDetalhe);

        $obREmpenhoRelatorioDespesasMensaisFixas->obRRelatorio->executaFrameOculto('OCGeraRelatorioEmpenhoDespesasMensaisFixas.php');

    break;
}

echo $stJs;
