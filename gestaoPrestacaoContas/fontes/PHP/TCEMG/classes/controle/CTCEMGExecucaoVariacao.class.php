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
   /*
    * Classe de controle do arquivo execucaoVariacao.txt
    * Data de Criação   : 20/01/2009

    * @author Analista      Tonismar Bernardo
    * @author Desenvolvedor Alexandre Melo

    * @package URBEM
    * @subpackage

    $Id:$
    */

class CTCEMGExecucaoVariacao
{

    public $obModel;

    public function __construct(&$obModel)
    {
        $this->obModel = $obModel;
    }

    public function incluir($arRequest)
    {
        $obErro = $this->obModel->incluirExecucaoVariacao($arRequest);

        if (!$obErro->ocorreu()) {
            SistemaLegado::alertaAviso('FMExecucaoVariacao.php'.'?'.Sessao::getId().'&stAcao=incluir', '12/'.Sessao::getExercicio(), 'incluir','aviso', Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso($obErro->getDescricao(), 'n_incluir', 'erro');
        }
        limpaCampos();
    }

    public function alterar($arRequest)
    {
        $obErro = $this->obModel->alterarExecucaoVariacao($arRequest);

        if (!$obErro->ocorreu()) {
            SistemaLegado::alertaAviso('FMExecucaoVariacao.php'.'?'.Sessao::getId().'&stAcao=alterar', '12/'.Sessao::getExercicio(), 'alterar','aviso', Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso($obErro->getDescricao(), 'n_incluir', 'erro');
        }
    }

    public function carregaFrmAlteracao()
    {
        $rsRecordSet = $this->obModel->consultaExecucaoVariacao($arRequest);

        if ($rsRecordSet->getNumLinhas() > 0) {
            $stJs  = "f.stAdmDireta.value='".$rsRecordSet->getCampo('cons_adm_dir')."';";
            $stJs .= "f.stConsAut.value='".$rsRecordSet->getCampo('cons_aut')."';";
            $stJs .= "f.stFund.value='".$rsRecordSet->getCampo('cons_fund')."';";
            $stJs .= "f.stEmpEstDep.value='".$rsRecordSet->getCampo('cons_empe_est_dep')."';";
            $stJs .= "f.stDemaisEntidades.value='".$rsRecordSet->getCampo('cons_dem_ent')."';";
       }

       echo $stJs;
    }

    public function limpaCampos()
    {
        $stJs  = "f.stAdmDireta.value='';";
        $stJs .= "f.stConsAut.value='';";
        $stJs .= "f.stFund.value='';";
        $stJs .= "f.stEmpEstDep.value='';";
        $stJs .= "f.stDemaisEntidades.value='';";

        echo $stJs;
    }

}

?>
