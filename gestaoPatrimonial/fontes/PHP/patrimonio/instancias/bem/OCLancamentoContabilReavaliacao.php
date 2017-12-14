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
    * Data de Criação: 18/08/2016

    * @author Analista: Ane Caroline Fiegenbaum Pereira
    * @author Desenvolvedor: Michel Teixeira

    * @package URBEM
    * @subpackage

    $Id: OCLancamentoContabilReavaliacao.php 66372 2016-08-19 19:06:35Z michel $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GP_PAT_MAPEAMENTO.'TPatrimonioReavaliacao.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "LancamentoContabilReavaliacao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stCtrl = $request->get('stCtrl');

switch ($stCtrl) {
    case 'montaBem':
        $inExercicio = $request->get('inExercicio');
        $inCompetencia = $request->get('inCompetencia');

        $stJs  = "jq('#inCodBem').empty().append(new Option('Selecione','')); \n";

        if(!empty($inExercicio) && !empty($inCompetencia)){
            $stFiltro  = " WHERE DATE_PART('YEAR', reavaliacao.dt_reavaliacao)::INTEGER = ".$inExercicio." ";
            $stFiltro .= "   AND DATE_PART('MONTH', reavaliacao.dt_reavaliacao)::INTEGER = ".$inCompetencia." ";
            if($request->get('stAcao') == 'incluir')
                $stFiltro .= "   AND ( lancamento_reavaliacao.cod_bem IS NULL OR lancamento_reavaliacao.estorno = TRUE ) ";
            else
                $stFiltro .= "   AND ( lancamento_reavaliacao.cod_bem IS NOT NULL AND lancamento_reavaliacao.estorno = FALSE ) ";

            $obTPatrimonioReavaliacao = new TPatrimonioReavaliacao();
            $obTPatrimonioReavaliacao->recuperaRelacionamento( $rsBemReavaliacao, $stFiltro );

            $arBem = array();
            while (!$rsBemReavaliacao->eof()) {
                $arBem[$rsBemReavaliacao->getCampo('cod_bem')]['descricao'] = $rsBemReavaliacao->getCampo('descricao');
                $rsBemReavaliacao->proximo();
            }

            foreach($arBem AS $inCodBem => $bem ){
                $stOption  = "'";
                $stOption .= $inCodBem;
                $stOption .= " - ";
                $stOption .= $bem['descricao'];
                $stOption .= "', '";
                $stOption .= $inCodBem;
                $stOption .= "', ''";

                $stJs .= "jq('#inCodBem').append(new Option(".$stOption.")); \n";
            }
        }

        echo $stJs;
    break;
}

echo $stJs;
