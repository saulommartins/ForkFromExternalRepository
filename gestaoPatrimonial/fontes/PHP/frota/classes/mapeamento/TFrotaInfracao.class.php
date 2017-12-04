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

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TFrotaInfracao extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TFrotaInfracao()
    {
        parent::Persistente();
        $this->setTabela('frota.infracao');
        $this->setCampoCod('id');

        $this->AddCampo('id'            ,'integer', true, ''  , true , false);
        $this->AddCampo('auto_infracao' ,'varchar', true, '15', false, false);
        $this->AddCampo('data_infracao' ,'date'   , true, ''  , false, false);
        $this->AddCampo('cod_veiculo'   ,'integer', true, ''  , true , false);
        $this->AddCampo('cgm_motorista' ,'integer', true, ''  , true , false);
        $this->AddCampo('cod_infracao'  ,'integer', true, ''  , true , false);
    }

    public function recuperaInfracao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaInfracao().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaInfracao()
    {
        $stSql  = " SELECT infracao.id,
                           infracao.auto_infracao,
                           infracao.cod_veiculo,
                           to_char(data_infracao, 'dd/mm/yyyy'::varchar) as data_infracao,
                           infracao.cgm_motorista,
                           sw_cgm.numcgm,
                           sw_cgm.nom_cgm,
                           infracao.cod_infracao,
                           modelo.nom_modelo,
                           motivo_infracao.descricao as motivo,
                           motivo_infracao.base_legal,
                           motivo_infracao.gravidade,
                           motivo_infracao.pontos
                      FROM frota.infracao
                INNER JOIN frota.motivo_infracao
                        ON motivo_infracao.cod_infracao = infracao.cod_infracao
                INNER JOIN frota.veiculo
                        ON infracao.cod_veiculo = veiculo.cod_veiculo
                INNER JOIN frota.modelo
                        ON modelo.cod_modelo = veiculo.cod_modelo
                       AND modelo.cod_marca  = veiculo.cod_marca
                INNER JOIN sw_cgm
                        ON sw_cgm.numcgm = infracao.cgm_motorista ";

        return $stSql;
    }

    public function recuperaPontosMotorista(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaPontosMotorista().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaPontosMotorista()
    {
        $stSql  = "SELECT SUM(pontos) as pontos
                     FROM frota.infracao
               INNER JOIN frota.motivo_infracao
                       ON motivo_infracao.cod_infracao = infracao.cod_infracao
                    WHERE infracao.cgm_motorista = ".$this->getDado('cgm_motorista')."
                      AND data_infracao BETWEEN (CURRENT_DATE - INTERVAL '1 year') AND CURRENT_DATE";

        return $stSql;
    }
}
