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
    * Oculto do Componente FiltroMultiploRegSubCarEsp
    * Data de Criação: 22/02/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Bruce Cruz de Sena

    * @ignore

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

    * Casos de uso: uc-00.00.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

function preencherAgencia()
{
    $stJs  = "jQuery('#inCodAgenciaDisponiveis').removeOption(/./);\n";
    $stJs .= "jQuery('#inCodAgenciaSelecionados').removeOption(/./);\n";
    if ($_REQUEST['inCodBancoSelecionados']) {
        $stFiltro = ' where cod_banco in (' . implode(",",$_REQUEST['inCodBancoSelecionados']) . ')';
        include_once(CAM_GT_MON_MAPEAMENTO . "TMONAgencia.class.php");
        $obTMonAgencia = new TMONAgencia;
        $obTMonAgencia->recuperaTodos( $rsAgencia, $stFiltro );
        $inIndex = 0;
        while ( !$rsAgencia->eof() ) {
            $stJs .= "jQuery('#inCodAgenciaDisponiveis').addOption('".$rsAgencia->getCampo('cod_agencia')."','".$rsAgencia->getCampo('num_agencia')." - ". $rsAgencia->getCampo( 'nom_agencia')."');";
            $inIndex++;
            $rsAgencia->proximo();
        }
    }

    return $stJs;
}

$stJs = preencherAgencia();
if ($stJs) {
    echo $stJs;
}
?>
