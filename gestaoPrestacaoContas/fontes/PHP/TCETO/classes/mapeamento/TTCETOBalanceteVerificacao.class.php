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
    * Extensão da Classe de Mapeamento TTCETOBalanceteVerificacao
    *
    * Data de Criação: 12/11/2014
    *
    * @author: Franver Sarmento de Moraes
    *
    * $Id: TTCETOBalanceteVerificacao.class.php 60835 2014-11-18 13:35:16Z evandro $
    *
    * @ignore
    *
*/

class TTCETOBalanceteVerificacao extends Persistente {
    /**
        * Método Construtor
        * @access Public
    */
    public function TTCETOBalanceteVerificacao()
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
        $stSql = "  SELECT
                              retorno.cod_und_gestora                          
                            , retorno.cod_orgao
                            , retorno.cod_und_orcamentaria
                            , retorno.cod_conta_balancete
                            , CASE WHEN SIGN(vl_saldo_anterior) = 1 THEN
                                        ABS(vl_saldo_anterior)
                                   ELSE
                                        '0.00'
                             END AS saldo_anterior_conta_devedora 
                            , CASE WHEN SIGN(vl_saldo_anterior) = -1 THEN
                                        ABS(vl_saldo_anterior)
                                   ELSE
                                        '0.00'
                             END AS saldo_anterior_conta_credora
                            ,ABS(vl_saldo_debitos_bimestre) as mov_conta_devedora
                            ,ABS(vl_saldo_creditos_bimestre) as mov_conta_credora
                            , CASE WHEN SIGN(vl_saldo_atual) = 1 THEN
                                        ABS(vl_saldo_atual)
                                   ELSE
                                        '0.00'
                             END AS saldo_atual_conta_devedora 
                            , CASE WHEN SIGN(vl_saldo_atual) = -1 THEN
                                        ABS(vl_saldo_atual)
                                   ELSE
                                        '0.00'
                             END AS saldo_atual_conta_credora                            
                            , retorno.descricao
                            , retorno.escrituracao
                            , retorno.indicador_superavit
                            , retorno.nivel
                            , retorno.natureza_informacao
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
                                    , LPAD(tceto.recupera_codigo_orgao('".$this->getDado('exercicio')."',retorno.cod_entidade::INTEGER, 'orgao')::varchar,2,'0') AS cod_orgao
                                    , LPAD(tceto.recupera_codigo_orgao('".$this->getDado('exercicio')."',retorno.cod_entidade::INTEGER, 'unidade')::VARCHAR,4,'0') AS cod_und_orcamentaria
                                    , REPLACE(cod_estrutural,'.','') AS cod_conta_balancete
                                    , nivel
                                    , nom_conta AS descricao
                                    , CASE cod_sistema 
                                            WHEN 1 THEN 'P'
                                            WHEN 2 THEN 'O'
                                            WHEN 3 THEN 'C'
                                    END as natureza_informacao
                                    , TRIM(indicador_superavit) as indicador_superavit
                                    , CASE WHEN escrituracao = 'analitica'
                                           THEN 'S'
                                           ELSE 'N'
                                    END AS escrituracao
                                    , vl_saldo_anterior
                                    , vl_saldo_debitos_bimestre
                                    , vl_saldo_creditos_bimestre
                                    , vl_saldo_atual                                  
                                    , 1 AS tipo_balancete
                                  
                            FROM tceto.balancete_verificacao('".$this->getDado('exercicio')."',
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