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
    * Página de Processamento de Ajustes de modelos
    * Data de Criação   : 18/05/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Anderson R. M. Buzo

    $Revision: 30668 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso uc-02.05.01

    * @ignore
*/

/*
$Log$
Revision 1.4  2006/07/05 20:45:22  cleisson
Adicionada tag Log aos arquivos

*/
include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include( CAM_GF_LRF_NEGOCIO."RLRFTCERSModelo.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "AjustesModelos";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$obRLRFTCERSModelo = new RLRFTCERSModelo();

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

switch ($stAcao) {
    case "ajustar":
        $obRLRFTCERSModelo->setExercicio( Sessao::getExercicio() );
        $obRLRFTCERSModelo->setCodModelo( $_POST['inCodModelo'] );
        foreach ($_POST as $stVar => $nuValorAjuste) {
            $arVar = explode( '_', $stVar );
            if ($arVar[0] == 'nuValor') {
                $inCodQuadro   = $arVar[1];
                $inCodConta    = $arVar[2];
                $nuValorAjuste = str_replace( '.', '' , $nuValorAjuste );
                $nuValorAjuste = str_replace( ',', '.', $nuValorAjuste );
                if ( sizeof( $obRLRFTCERSModelo->getRLRFTCERSQuadro() ) > 0 ) {
                    if( $obRLRFTCERSModelo->roUltimoQuadro->getCodQuadro() != $inCodQuadro )
                        $obRLRFTCERSModelo->addQuadro();
                        $obRLRFTCERSModelo->roUltimoQuadro->setCodQuadro( $inCodQuadro );
                } else {
                    $obRLRFTCERSModelo->addQuadro();
                    $obRLRFTCERSModelo->roUltimoQuadro->setCodQuadro( $inCodQuadro );
                }
                $obRLRFTCERSModelo->roUltimoQuadro->addContaPlano();
                $obRLRFTCERSModelo->roUltimoQuadro->roUltimaContaPlano->setMes( $_POST['inMes'] );
                $obRLRFTCERSModelo->roUltimoQuadro->roUltimaContaPlano->setCodConta( $inCodConta );
                $obRLRFTCERSModelo->roUltimoQuadro->roUltimaContaPlano->setValor( $nuValorAjuste );
                $obRLRFTCERSModelo->roUltimoQuadro->roUltimaContaPlano->obROrcamentoEntidade->setCodigoEntidade( $_POST['inCodEntidade'] );
            }
        }

        $obErro = $obRLRFTCERSModelo->salvarValorContas();
        if( !$obErro->ocorreu() )
            SistemaLegado::alertaAviso($pgFilt.'?'.Sessao::getId().'&stAcao='.$stAcao, $_POST['inCodModelo'], "incluir", "aviso", Sessao::getId(), "../");
        else
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
    break;
}
?>
