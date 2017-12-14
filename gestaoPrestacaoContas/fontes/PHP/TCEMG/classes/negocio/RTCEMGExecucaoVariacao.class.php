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
    * Regra de negócio do arquivo execucaoVariacao.txt
    * Data de Criação   : 20/01/2009

    * @author Analista      Tonismar Bernardo
    * @author Desenvolvedor Alexandre Melo

    * @package URBEM
    * @subpackage

    $Id:$
    */

include_once( CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGExecucaoVariacao.class.php" );

class RTCEMGExecucaoVariacao
{
    public $obTransacao,
        $obTTCEMGExecucaoVariacao;

    public function __construct()
    {
        $this->obTransacao              = new Transacao             ();
        $this->obTTCEMGExecucaoVariacao = new TTCEMGExecucaoVariacao();
    }

    public function incluirExecucaoVariacao($arItens, $boFlagTransacao = true, $boTransacao = '')
    {
        $this->obTTCEMGExecucaoVariacao->setDado( 'exercicio'         , Sessao::getExercicio()        );
        $this->obTTCEMGExecucaoVariacao->setDado( 'cons_adm_dir'      , $arItens['stAdmDireta']       );
        $this->obTTCEMGExecucaoVariacao->setDado( 'cons_aut'          , $arItens['stConsAut']         );
        $this->obTTCEMGExecucaoVariacao->setDado( 'cons_fund'         , $arItens['stFund']            );
        $this->obTTCEMGExecucaoVariacao->setDado( 'cons_empe_est_dep' , $arItens['stEmpEstDep']       );
        $this->obTTCEMGExecucaoVariacao->setDado( 'cons_dem_ent'      , $arItens['stDemaisEntidades'] );
        $obErro = $this->obTTCEMGExecucaoVariacao->inclusao($boTransacao);

        if ($obErro->ocorreu()) {
            $obErro->setDescricao('Considerações já esxistentes para o exercício '.Sessao::getExercicio().'.' );
        }

        $this->obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $this->obTTCEMGExecucaoVariacao);

        return $obErro;
    }

    public function alterarExecucaoVariacao($arItens, $boFlagTransacao = true, $boTransacao = '')
    {
        $this->obTTCEMGExecucaoVariacao->setDado( 'exercicio'         , Sessao::getExercicio()        );
        $this->obTTCEMGExecucaoVariacao->setDado( 'cons_adm_dir'      , $arItens['stAdmDireta']       );
        $this->obTTCEMGExecucaoVariacao->setDado( 'cons_aut'          , $arItens['stConsAut']         );
        $this->obTTCEMGExecucaoVariacao->setDado( 'cons_fund'         , $arItens['stFund']            );
        $this->obTTCEMGExecucaoVariacao->setDado( 'cons_empe_est_dep' , $arItens['stEmpEstDep']       );
        $this->obTTCEMGExecucaoVariacao->setDado( 'cons_dem_ent'      , $arItens['stDemaisEntidades'] );
        $obErro = $this->obTTCEMGExecucaoVariacao->alteracao($boTransacao);

        $this->obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $this->obTTCEMGExecucaoVariacao);

        return $obErro;
    }

    public function consultaExecucaoVariacao()
    {
        $this->obTTCEMGExecucaoVariacao->setDado( 'exercicio', Sessao::getExercicio() );
        $this->obTTCEMGExecucaoVariacao->recuperaPorChave($rsRecordSet);

        return $rsRecordSet;
    }

}
?>
