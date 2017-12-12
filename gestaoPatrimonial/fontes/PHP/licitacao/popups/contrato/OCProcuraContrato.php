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
* Data de Criação: 07/03/2006

* @author Analista: Diego Barbosa Victoria
* @author Desenvolvedor: Leandro André Zis

* $Id: OCProcuraContrato.php 64256 2015-12-22 16:06:28Z michel $

* Casos de uso :uc-03.04.07, uc-03.04.05
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GP_LIC_MAPEAMENTO.'TLicitacaoContrato.class.php';

$stCampoCod  = $request->get('stNomCampoCod');
$stCampoDesc = $request->get('stIdCampoDesc');
$inCodigo    = $request->get( $stCampoCod );
$stExercicioContrato = $request->get('stExercicioContrato', '');

$boFornecedor = $request->get('boFornecedor');
$inCodFornecedor = $request->get('inCodFornecedor', '');
$stCodFornecedor = ($inCodFornecedor!='') ? '= '.$inCodFornecedor : 'IS NULL';

$inCodEntidade = $request->get('inCodEntidade', '');
$stCodEntidade = ($inCodEntidade!='') ? '= '.$inCodEntidade : 'IS NULL';

switch ($request->get('stCtrl')) {
    case 'buscaPopup':
    default:
        if ($inCodigo != "" && $stExercicioContrato != "") {
            $obTLicitacaoContrato = new TLicitacaoContrato;
            $rsContrato = new RecordSet;
            $stFiltro  = " AND contrato.num_contrato = ".$inCodigo;
            $stFiltro .= " AND contrato.exercicio = '".$stExercicioContrato."'";

            if($boFornecedor){
                $stFiltro .= " AND contrato.cgm_contratado ".$stCodFornecedor;
                $stFiltro .= " AND contrato.cod_entidade ".$stCodEntidade;
            }

            $obTLicitacaoContrato->recuperaContrato($rsContrato, $stFiltro);

            if($rsContrato->getNumLinhas()==1){
                $stObjeto = $rsContrato->getCampo('descricao');
                $stObjeto = str_replace("\r\n", '', $stObjeto);
                $stJs .= "d.getElementById('".$stCampoDesc."').innerHTML = \"".$stObjeto."\";";
                $stJs .= "d.getElementById('".$stCampoCod."').value = '".$inCodigo."';";
            }else{
                $stJs .= "d.getElementById('".$stCampoCod."').value = '';";
                $stJs .= "d.getElementById('".$stCampoDesc."').innerHTML = '&nbsp;';";
                $stJs .= "alertaAviso('@Contrato(".$inCodigo.'/'.$stExercicioContrato.") não encontrado.', 'form','erro','".Sessao::getId()."');";
            }
        } else {
            $stJs .= "d.getElementById('".$stCampoCod."').value = '';";
            $stJs .= "d.getElementById('".$stCampoDesc."').innerHTML = '&nbsp;';";
        }

        sistemaLegado::executaFrameOculto( $stJs );
    break;
}

?>
