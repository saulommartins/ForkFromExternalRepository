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
    * Extensão da Classe de Mapeamento TTCEALBalanceteVerificacao
    *
    * Data de Criação: 27/05/2014
    *
    * @author: Franver Sarmento de Moraes
    *
    * $Id: TTCEALBalanceteVerificacao.class.php 65563 2016-05-31 20:36:59Z michel $
    *
    * @ignore
    *
*/

class TTCEALBalanceteVerificacao extends Persistente {
    /**
        * Método Construtor
        * @access Public
    */
    public function TTCEALBalanceteVerificacao()
    {
        parent::Persistente();
    }
    /**
     * Método para trazer todos os registros de Projeto Atividade, para o TCEAL
     * @access Public
     * @param  Object  $rsRecordSet Objeto RecordSet
     * @param  String  $stCondicao  String de condição do SQL (WHERE)
     * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
     * @param  Boolean $boTransacao
     * @return Object  Objeto Erro
    */
    public function recuperaBalanceteVerificacao(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaBalanceteVerificacao().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }
    
    public function montaRecuperaBalanceteVerificacao()
    {
        $stSql = "
                    SELECT
                            retorno.cod_und_gestora
                          , CASE WHEN retorno.codigo_ua <> '' THEN retorno.codigo_ua ELSE '0000' END AS codigo_ua
                          , retorno.cod_orgao
                          , retorno.cod_conta_balancete
                          , retorno.cod_und_orcamentaria
                          , retorno.nivel
                          , retorno.descricao
                          , retorno.cod_sistema
                          , retorno.indicador_superavit
                          , retorno.tipo_nivel_conta
                          , REPLACE(retorno.saldo_anterior,'-','') AS saldo_anterior
                          , retorno.vl_saldo_debitos AS mov_debito_no_mes
                          , retorno.vl_saldo_debitos_bimestre AS mov_debito_ate_mes
                          , REPLACE(retorno.vl_saldo_creditos,'-','') AS mov_credito_no_mes
                          , REPLACE(retorno.vl_saldo_creditos_bimestre,'-','') AS mov_credito_ate_mes
                          , REPLACE(retorno.saldo_atual,'-','') AS saldo_atual
                          , retorno.natureza_saldo
                          , retorno.tipo_balancete
                          
                      FROM (
                             SELECT (SELECT PJ.cnpj
                                       FROM orcamento.entidade
                                       JOIN sw_cgm
                                         ON sw_cgm.numcgm=entidade.numcgm
                                       JOIN sw_cgm_pessoa_juridica AS PJ
                                         ON sw_cgm.numcgm=PJ.numcgm
                                      WHERE entidade.exercicio='".$this->getDado('exercicio')."'
                                        AND entidade.cod_entidade=retorno.cod_entidade
                                    ) AS cod_und_gestora
                                  , (SELECT valor
                                       FROM administracao.configuracao_entidade
                                      WHERE configuracao_entidade.exercicio = '".$this->getDado('exercicio')."'
                                        AND configuracao_entidade.cod_entidade = retorno.cod_entidade
                                        AND configuracao_entidade.cod_modulo = 62
                                        AND configuracao_entidade.parametro = 'tceal_configuracao_unidade_autonoma'
                                    ) AS codigo_ua
                                  , LPAD(tceal.recupera_codigo_orgao('".$this->getDado('exercicio')."',retorno.cod_entidade::INTEGER, 'orgao')::varchar,2,'0') AS cod_orgao
                                  , RPAD(REPLACE(cod_estrutural,'.',''),17,'0') AS cod_conta_balancete
                                  , LPAD(tceal.recupera_codigo_orgao('".$this->getDado('exercicio')."',retorno.cod_entidade::INTEGER, 'unidade')::VARCHAR,4,'0') AS cod_und_orcamentaria
                                  , nivel
                                  , nom_conta AS descricao
                                  , cod_sistema
                                  , indicador_superavit
                                  , CASE WHEN escrituracao = 'sintetica'
                                         THEN 'S'
                                         ELSE 'A'
                                  END AS tipo_nivel_conta
                                  , vl_saldo_anterior::varchar AS saldo_anterior
                                  , vl_saldo_debitos::varchar AS vl_saldo_debitos
                                  , vl_saldo_debitos_bimestre::varchar AS vl_saldo_debitos_bimestre
                                  , vl_saldo_creditos::varchar AS vl_saldo_creditos
                                  , vl_saldo_creditos_bimestre::varchar AS vl_saldo_creditos_bimestre
                                  , vl_saldo_atual::varchar AS saldo_atual
                                  , (SELECT natureza_saldo FROM contabilidade.plano_conta WHERE plano_conta.exercicio = '".$this->getDado('exercicio')."' AND plano_conta.cod_estrutural = retorno.cod_estrutural) AS natureza_saldo
                                  , 1 AS tipo_balancete
                                  
                              FROM tceal.balancete_verificacao('".$this->getDado('exercicio')."',
                                                               '".$this->getDado('cod_entidade')."',
                                                               '".$this->getDado('dtInicial')."',
                                                               '".$this->getDado('dtFinal')."'
                                                            ) as retorno ( cod_estrutural               VARCHAR
                                                                         , nivel                        INTEGER
                                                                         , nom_conta                    VARCHAR
                                                                         , cod_sistema                  INTEGER
                                                                         , indicador_superavit          CHAR(12)
                                                                         , escrituracao                 CHAR(9)
                                                                         , vl_saldo_anterior            NUMERIC
                                                                         , vl_saldo_debitos             NUMERIC
                                                                         , vl_saldo_debitos_bimestre    NUMERIC
                                                                         , vl_saldo_creditos            NUMERIC
                                                                         , vl_saldo_creditos_bimestre   NUMERIC
                                                                         , vl_saldo_atual               NUMERIC
                                                                         , cod_entidade                 INTEGER
                                                                        )
                        ) AS retorno
                  ";
                  
        return $stSql;
    }
}
?>