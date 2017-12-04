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
    * Extensão da Classe de mapeamento
    * Data de Criação : 08/06/2016

    * @author Analista: Valtair Santos
    * @author Desenvolvedor: Michel Teixeira

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TTBAConcilia.class.php 65710 2016-06-09 20:52:09Z michel $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTBAConcilia extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function __construct()
    {
        parent::Persistente();

        $this->setDado('stExercicio', Sessao::getExercicio());
    }

    public function recuperaDadosTribunal(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(empty($stOrdem))
            $stOrdem = " ORDER BY conciliacao.competencia, conciliacao.cod_conciliacao ";

        $stSql = $this->montaRecuperaDadosTribunal().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaDadosTribunal()
    {
        $stSql = " SELECT 1 AS tipo_registro
                        , conciliacao.exercicio
                        , ".$this->getDado('inCodGestora')." AS unidade_gestora
                        , REPLACE(conciliacao.cod_estrutural,'.','') AS cod_estrutural
                        , conciliacao.competencia
                        , 0 AS reservado
                        , conciliacao.cod_tipo_conciliacao
                        , sem_acentos(conciliacao.descricao) AS descricao
                        , TO_CHAR(conciliacao.timestamp,'dd/mm/yyyy') AS data_conciliacao
                        , conciliacao.vl_lancamento
                        , conciliacao.cod_conciliacao
                        , conciliacao.cod_tipo_pagamento
                        , conciliacao.num_documento
                     FROM tcmba.fn_conciliacao_movimentacao_corrente('".$this->getDado('stExercicio')."'
                                                                    ,'".$this->getDado('stEntidades')."'
                                                                    ,'".$this->getDado('stDtInicio')."'
                                                                    ,'".$this->getDado('stDtFim')."'
                                                                    ) AS conciliacao
                                                                    ( exercicio                VARCHAR,
                                                                      competencia              TEXT,
                                                                      cod_estrutural           VARCHAR,
                                                                      cod_tipo_conciliacao     INTEGER,
                                                                      descricao                TEXT,
                                                                      dt_extrato               DATE,
                                                                      timestamp                TIMESTAMP,
                                                                      vl_lancamento            NUMERIC,
                                                                      cod_tipo_pagamento       INTEGER,
                                                                      num_documento            VARCHAR,
                                                                      cod_plano                INTEGER,
                                                                      cod_conciliacao          INTEGER
                                                                    )
            ";

        return $stSql;
    }

}

?>