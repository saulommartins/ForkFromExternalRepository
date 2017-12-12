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
    * Data de Criação: 07/11/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * $Id: TFrotaAutorizacao.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-03.02.10
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TFrotaAutorizacao extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TFrotaAutorizacao()
    {
        parent::Persistente();
        $this->setTabela('frota.autorizacao');
        $this->setCampoCod('cod_autorizacao');
        $this->setComplementoChave('exercicio');
        $this->AddCampo('cod_autorizacao'       ,'integer'  ,true ,''    ,true ,false);
        $this->AddCampo('exercicio'             ,'varchar'  ,true ,''    ,true ,false);
        $this->AddCampo('cod_item'              ,'integer'  ,true ,''    ,false,true);
        $this->AddCampo('cgm_resp_autorizacao'  ,'integer'  ,true ,''    ,false,true);
        $this->AddCampo('cgm_fornecedor'        ,'integer'  ,true ,''    ,false,true);
        $this->AddCampo('cod_veiculo'           ,'integer'  ,true ,''    ,false,true);
        $this->AddCampo('quantidade'            ,'numeric'  ,false,'14.2',false,false);
        $this->AddCampo('valor'                 ,'numeric'  ,true ,'14.2',false,false);
        $this->AddCampo('observacao'            ,'text'     ,false,''    ,false,false);
        $this->AddCampo('cgm_motorista'         ,'integer'  ,true ,''    ,true ,false);
        $this->AddCampo('timestamp'             ,'timestamp',true ,''    ,false,false);
    }

    public function recuperaRelacionamento(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
         return $this->executaRecupera("montaRecuperaRelacionamento",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }
    public function montaRecuperaRelacionamento()
    {
        $stSql = "
            SELECT autorizacao.cod_autorizacao
                 , autorizacao.exercicio
                 , TO_CHAR(timestamp,'dd/mm/yyyy') AS dt_autorizacao
                 , marca.cod_marca
                 , marca.nom_marca
                 , modelo.cod_modelo
                 , modelo.nom_modelo
                 , tipo_veiculo.cod_tipo
                 , tipo_veiculo.nom_tipo
                 , combustivel.cod_combustivel
                 , combustivel.nom_combustivel
              FROM frota.autorizacao
        INNER JOIN frota.combustivel_item
                ON combustivel_item.cod_item = autorizacao.cod_item
        INNER JOIN frota.combustivel
                ON combustivel.cod_combustivel = combustivel_item.cod_combustivel
        INNER JOIN frota.veiculo
                ON veiculo.cod_veiculo = autorizacao.cod_veiculo
        INNER JOIN frota.marca
                ON marca.cod_marca = veiculo.cod_marca
        INNER JOIN frota.modelo
                ON modelo.cod_marca = veiculo.cod_marca
               AND modelo.cod_modelo = veiculo.cod_modelo
        INNER JOIN frota.tipo_veiculo
                ON tipo_veiculo.cod_tipo = veiculo.cod_tipo_veiculo
             WHERE ";
        if ( $this->getDado( 'cod_autorizacao' ) != '' ) {
            $stSql .= " autorizacao.cod_autorizacao = '".$this->getDado('cod_autorizacao')."' AND  ";
        }
        if ( $this->getDado( 'cod_autorizacao' ) != '' ) {
            $stSql .= " autorizacao.exercicio = '".$this->getDado('exercicio')."' AND  ";
        }

        return substr($stSql,0,-6);
    }

    public function recuperaAutorizacao(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
         return $this->executaRecupera("montaRecuperaAutorizacao",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }
    public function montaRecuperaAutorizacao()
    {
        $stSql = "
            SELECT autorizacao.cod_autorizacao
                 , autorizacao.cod_item
                 , autorizacao.cgm_motorista
                 , sw_cgm.nom_cgm
                 , catalogo_item.descricao
                 , autorizacao.cgm_resp_autorizacao
                 , autorizador.nom_cgm AS nom_resp_autorizacao
                 , autorizacao.cgm_fornecedor
                 , fornecedor.nom_cgm AS nom_fornecedor
                 , autorizacao.cod_veiculo
                 , autorizacao.exercicio
                 , veiculo.placa
                 , SUBSTR(veiculo.placa,1,3) || '-' || SUBSTR(veiculo.placa,4,4) AS placa_masc
                 , veiculo.prefixo
                 , modelo.nom_modelo
                 , autorizacao.quantidade
                 , autorizacao.valor
                 , autorizacao.observacao
                 , TO_CHAR(timestamp,'dd/mm/yyyy') AS dt_autorizacao
                 , CASE WHEN autorizacao.quantidade = 0.0000
                        THEN true
                        ELSE false
                   END AS completar
              FROM frota.autorizacao
        INNER JOIN frota.veiculo
                ON veiculo.cod_veiculo = autorizacao.cod_veiculo
        INNER JOIN sw_cgm
                ON sw_cgm.numcgm = autorizacao.cgm_motorista
        INNER JOIN frota.modelo
                ON modelo.cod_marca = veiculo.cod_marca
               AND modelo.cod_modelo = veiculo.cod_modelo
        INNER JOIN sw_cgm AS autorizador
                ON autorizador.numcgm = autorizacao.cgm_resp_autorizacao
        INNER JOIN sw_cgm AS fornecedor
                ON fornecedor.numcgm = autorizacao.cgm_fornecedor
        INNER JOIN almoxarifado.catalogo_item
                ON catalogo_item.cod_item = autorizacao.cod_item
             WHERE ";
        if ( $this->getDado( 'cod_autorizacao' ) != '' ) {
            $stSql .= " autorizacao.cod_autorizacao = ".$this->getDado('cod_autorizacao')." AND   ";
        }
        if ( $this->getDado( 'exercicio' ) != '' ) {
            $stSql .= " autorizacao.exercicio = '".$this->getDado('exercicio')."' AND   ";
        }

        return substr($stSql,0,-6);

    }
}
