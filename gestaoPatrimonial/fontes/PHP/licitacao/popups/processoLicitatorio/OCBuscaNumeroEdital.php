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
* Arquivo instância para popup de Objeto
* Data de Criação: 16/04/2009

* @author Analista: Gelson W
* @author Desenvolvedor: Luiz Felipe Prestes Teixeira

* Casos de uso :
* $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GP_LIC_MAPEAMENTO.'TLicitacaoEdital.class.php' );

$stField  = $_REQUEST['stField'];
$stFieldId = $_REQUEST['stFieldId'];
$stEditalExercicio = $_REQUEST[$stField];
$stCtrl = $_REQUEST['stCtrl'];

list($inNumEdital,$inExercicioEdital) = explode('/',$stEditalExercicio  );

if ($inExercicioEdital=="") {
   $inExercicioEdital=Sessao::getExercicio();
}

switch ($stCtrl) {

    case 'validaEdital':
    default:
        if ($inNumEdital != "") {

            $obTLicitacaoEdital = new TLicitacaoEdital;
            $rsEdital = new RecordSet;

            $obTLicitacaoEdital->setDado('num_edital', (int) $inNumEdital);
            $obTLicitacaoEdital->setDado('exercicio_edital', $inExercicioEdital);

            $stFiltro = Sessao::read('filtroAdicionalSqlEditais');

            $obTLicitacaoEdital->recuperaListaEdital( $rsEdital, $stFiltro);

            $inEdital = $rsEdital->getCampo('num_edital');

            if ($inEdital == '') {
                $stJs .= "alertaAviso('Edital Inválido (". $inNumEdital."/".$inExercicioEdital.").', 'form','erro','".Sessao::getId()."');";
                $stJs .= "d.getElementById('".$stFieldId."').value = ' ';";
            }
        } else {
            $stJs .= "d.getElementById('".$stFieldId."').value = ' ';";
        }
        echo $stJs;
    break;
}
