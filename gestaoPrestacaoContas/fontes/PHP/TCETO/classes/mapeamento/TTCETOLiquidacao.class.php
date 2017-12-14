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
    * com  este  programa; se não, escreva para  a  Free  Software FoundatiON  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************

    * Extensão da Classe de Mapeamento TTCETOLLiquidacao
    *
    * Data de Criação: 11/11/2014
    *
    * @author: Evandro Melos
    *
    $Id: TTCETOLiquidacao.class.php 60939 2014-11-25 17:56:46Z evandro $
    *
    * @ignore
    *
*/
class TTCETOLiquidacao extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCETOLiquidacao()
    {
        parent::Persistente();
        $this->setDado('exercicio',Sessao::getExercicio());
    }
    
    /**
    * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaCredor.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
    */
    public function recuperaLiquidacao(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaLiquidacao().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaLiquidacao()
    {
        
        $stSql ="   SELECT DISTINCT * 
                    FROM(
                        SELECT  ".$this->getDado('bimestre')." AS bimestre
                                , '".$this->getDado('exercicio')."' AS exercicio
                                , ( SELECT PJ.cnpj
                                      FROM orcamento.entidade
                                      JOIN sw_cgm
                                        ON sw_cgm.numcgm = entidade.numcgm
                                      JOIN sw_cgm_pessoa_juridica AS PJ
                                        ON sw_cgm.numcgm = PJ.numcgm
                                     WHERE entidade.exercicio    = '".$this->getDado('exercicio')."'
                                       AND entidade.cod_entidade = ".$this->getDado('cod_entidade')."
                                  ) AS cod_und_gestora
                                , empenho.cod_empenho
                                , empenho.dt_empenho
                                , EXTRACT(YEAR FROM DATE (empenho.dt_empenho))::varchar || LPAD(empenho.cod_empenho::varchar,9,'0') AS num_empenho
                                , EXTRACT(YEAR FROM DATE (empenho.dt_empenho))::varchar || LPAD(nota_liquidacao_item.cod_nota::varchar,9,'0') AS num_liquidacao
                                , nota_liquidacao_item.cod_nota
                                , nota_liquidacao_item.num_item
                                , nota_liquidacao_item.cod_entidade
                                , nota_liquidacao.dt_liquidacao
                                , nota_liquidacao.observacao AS historico
                                , TO_CHAR(empenho.dt_empenho,'mm') AS referencia_mes
                                , TO_CHAR(empenho.dt_empenho,'yyyy') AS referencia_ano
                                , credor.codigo AS cod_credor
                                , CASE WHEN nota_liquidacao_item_anulado.cod_nota IS NULL
                                       THEN '+'
                                       ELSE'-'
                                  END AS sinal
                                , ' '::varchar AS codigo_operacao
                                , ' '::varchar AS num_processo
                                , SUM(nota_liquidacao_item.vl_total) AS valor
                       
                        FROM empenho.nota_liquidacao 
                   
                        JOIN empenho.empenho
                           ON empenho.exercicio    =  nota_liquidacao.exercicio_empenho   
                          AND empenho.cod_entidade =  nota_liquidacao.cod_entidade
                          AND empenho.cod_empenho  =  nota_liquidacao.cod_empenho
                   
                        JOIN empenho.pre_empenho
                           ON pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                          AND pre_empenho.exercicio       = empenho.exercicio
                          
                        JOIN empenho.nota_liquidacao_item
                           ON nota_liquidacao_item.exercicio    =  nota_liquidacao.exercicio
                          AND nota_liquidacao_item.cod_nota     =  nota_liquidacao.cod_nota
                          AND nota_liquidacao_item.cod_entidade =  nota_liquidacao.cod_entidade
                   
                        LEFT JOIN empenho.nota_liquidacao_item_anulado 
                           ON nota_liquidacao_item.exercicio       = nota_liquidacao_item_anulado.exercicio
                          AND nota_liquidacao_item.cod_nota        = nota_liquidacao_item_anulado.cod_nota
                          AND nota_liquidacao_item.cod_entidade    = nota_liquidacao_item_anulado.cod_entidade
                          AND nota_liquidacao_item.num_item        = nota_liquidacao_item_anulado.num_item
                          AND nota_liquidacao_item.cod_pre_empenho = nota_liquidacao_item_anulado.cod_pre_empenho
                          AND nota_liquidacao_item.exercicio_item  = nota_liquidacao_item_anulado.exercicio_item
                   
                        JOIN (SELECT pf.cpf as codigo,
                                      pf.numcgm
                                 FROM sw_cgm_pessoa_fisica as pf
                           
                                UNION
                           
                                SELECT pj.cnpj as codigo,
                                       pj.numcgm
                                  FROM sw_cgm_pessoa_juridica as pj
                          ) AS credor
                           ON credor.numcgm = pre_empenho.cgm_beneficiario
                   
                        WHERE nota_liquidacao.exercicio = '".$this->getDado('exercicio')."'
                          AND nota_liquidacao.cod_entidade IN (".$this->getDado('cod_entidade').")
                          AND nota_liquidacao.dt_liquidacao::DATE BETWEEN TO_DATE('".$this->getDado('dtInicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFinal')."','dd/mm/yyyy')
                   
                        GROUP BY empenho.cod_empenho
                            , empenho.dt_empenho
                            , credor.codigo
                            , nota_liquidacao_item.cod_nota
                            , nota_liquidacao_item.num_item
                            , nota_liquidacao_item.cod_entidade
                            , nota_liquidacao.dt_liquidacao
                            , nota_liquidacao.observacao
                            , nota_liquidacao_item_anulado.cod_nota
                   
                    UNION

                        SELECT  ".$this->getDado('bimestre')." AS bimestre
                                , '".$this->getDado('exercicio')."' AS exercicio
                                , ( SELECT PJ.cnpj
                                      FROM orcamento.entidade
                                      JOIN sw_cgm
                                        ON sw_cgm.numcgm = entidade.numcgm
                                      JOIN sw_cgm_pessoa_juridica AS PJ
                                        ON sw_cgm.numcgm = PJ.numcgm
                                     WHERE entidade.exercicio    = '".$this->getDado('exercicio')."'
                                       AND entidade.cod_entidade = ".$this->getDado('cod_entidade')."
                                  ) AS cod_und_gestora
                                , empenho.cod_empenho
                                , empenho.dt_empenho
                                , EXTRACT(YEAR FROM DATE (empenho.dt_empenho))::varchar || LPAD(empenho.cod_empenho::varchar,9,'0') AS num_empenho
                                , EXTRACT(YEAR FROM DATE (empenho.dt_empenho))::varchar || LPAD(nota_liquidacao_item_anulado.cod_nota::varchar,9,'0') AS num_liquidacao
                                , nota_liquidacao_item_anulado.cod_nota
                                , nota_liquidacao_item_anulado.num_item
                                , nota_liquidacao_item_anulado.cod_entidade
                                , nota_liquidacao_item_anulado.timestamp::DATE
                                , nota_liquidacao.observacao AS historico
                                , TO_CHAR(empenho.dt_empenho,'mm') AS referencia_mes
                                , TO_CHAR(empenho.dt_empenho,'yyyy') AS referencia_ano
                                , credor.codigo AS cod_credor
                                , CASE WHEN nota_liquidacao_item_anulado.cod_nota IS NULL
                                       THEN '+'
                                       ELSE'-'
                                  END AS sinal
                                , ' '::varchar AS codigo_operacao
                                , ' '::varchar AS num_processo
                                , SUM(nota_liquidacao_item_anulado.vl_anulado) AS valor

                        FROM empenho.nota_liquidacao 
                   
                        JOIN empenho.empenho
                           ON empenho.exercicio    =  nota_liquidacao.exercicio_empenho   
                          AND empenho.cod_entidade =  nota_liquidacao.cod_entidade
                          AND empenho.cod_empenho  =  nota_liquidacao.cod_empenho
                   
                        JOIN empenho.pre_empenho
                           ON pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                          AND pre_empenho.exercicio       = empenho.exercicio
                          
                        JOIN empenho.nota_liquidacao_item
                           ON nota_liquidacao_item.exercicio    =  nota_liquidacao.exercicio
                          AND nota_liquidacao_item.cod_nota     =  nota_liquidacao.cod_nota
                          AND nota_liquidacao_item.cod_entidade =  nota_liquidacao.cod_entidade
                   
                        JOIN empenho.nota_liquidacao_item_anulado 
                           ON nota_liquidacao_item.exercicio       = nota_liquidacao_item_anulado.exercicio
                          AND nota_liquidacao_item.cod_nota        = nota_liquidacao_item_anulado.cod_nota
                          AND nota_liquidacao_item.cod_entidade    = nota_liquidacao_item_anulado.cod_entidade
                          AND nota_liquidacao_item.num_item        = nota_liquidacao_item_anulado.num_item
                          AND nota_liquidacao_item.cod_pre_empenho = nota_liquidacao_item_anulado.cod_pre_empenho
                          AND nota_liquidacao_item.exercicio_item  = nota_liquidacao_item_anulado.exercicio_item
                   
                        JOIN (SELECT pf.cpf as codigo,
                                      pf.numcgm
                                 FROM sw_cgm_pessoa_fisica as pf
                           
                                UNION
                           
                                SELECT pj.cnpj as codigo,
                                       pj.numcgm
                                  FROM sw_cgm_pessoa_juridica as pj
                          ) AS credor
                           ON credor.numcgm = pre_empenho.cgm_beneficiario
                   
                        WHERE nota_liquidacao_item_anulado.exercicio = '".$this->getDado('exercicio')."'
                          AND nota_liquidacao_item_anulado.cod_entidade IN (".$this->getDado('cod_entidade').")
                          AND nota_liquidacao_item_anulado.timestamp::DATE BETWEEN TO_DATE('".$this->getDado('dtInicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFinal')."','dd/mm/yyyy')
                   
                        GROUP BY empenho.cod_empenho
                            , empenho.dt_empenho
                            , credor.codigo
                            , nota_liquidacao_item_anulado.cod_nota
                            , nota_liquidacao_item_anulado.num_item
                            , nota_liquidacao_item_anulado.cod_entidade
                            , nota_liquidacao_item_anulado.timestamp
                            , nota_liquidacao.observacao
                            , nota_liquidacao_item_anulado.cod_nota

                    ) as tabela
                    ORDER BY cod_nota DESC

                     ";
                
        return $stSql;
    }

    public function recuperaPagamento(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaPagamento().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaPagamento()
    {       
        $stSql = " 
                  SELECT
                          Cod_Und_Gestora
                        , num_empenho
                        , num_liquidacao
                        , num_pagamento
                        , data_pagamento
                        , valor
                        , sinal
                        , TRIM(historico, E' \\n\\r\\t') AS historico
                        , codigo_operacao
                        , numero_processo                          
                    FROM (
                            SELECT
                                    (SELECT PJ.cnpj
                                       FROM orcamento.entidade
                                       JOIN sw_cgm
                                         ON sw_cgm.numcgm=entidade.numcgm
                                       JOIN sw_cgm_pessoa_juridica AS PJ
                                         ON sw_cgm.numcgm=PJ.numcgm
                                      WHERE entidade.exercicio='".$this->getDado('exercicio')."'
                                        AND entidade.cod_entidade=".$this->getDado('und_gestora')."
                                    ) AS Cod_Und_Gestora
                                  , (SELECT CASE WHEN valor != '' THEN valor ELSE '0000' END AS valor
                                       FROM administracao.configuracao_entidade
                                      WHERE exercicio = '".$this->getDado('exercicio')."'
                                        AND cod_entidade = ".$this->getDado('und_gestora')."
                                        AND cod_modulo = 62
                                        AND parametro = 'tceal_configuracao_unidade_autonoma'
                                    ) AS Codigo_UA
                                  , TO_CHAR(tabela.dt_empenho, 'YYYY') || LPAD(tabela.cod_empenho::varchar,9,'0') AS num_empenho
                                  , TO_CHAR(tabela.data_liquidacao, 'YYYY') || LPAD(tabela.cod_nota::varchar,9,'0') AS num_liquidacao
                                  , TO_CHAR(tabela.data_pagamento, 'YYYY') || LPAD(tabela.cod_ordem::varchar,9,'0') AS num_pagamento
                                  , tabela.data_pagamento   
                                  , tabela.valor_liquidacao::varchar AS valor     
                                  , tabela.sinal_valor AS sinal
                                  , tabela.observacao AS historico                                              
                                  , ''::VARCHAR AS codigo_operacao
                                  , ''::VARCHAR AS numero_processo
                            FROM                                                                 
                                tceto.fn_exportacao_liquidacao('".$this->getDado('exercicio')."',    
                                                                '".$this->getDado('dtInicial')."',    
                                                                '".$this->getDado('dtFinal')."',    
                                                                '".$this->getDado('cod_entidade')."',    
                                                                '')    
                                    AS tabela              (exercicio char(4),                        
                                                            cod_empenho integer,                      
                                                            cod_entidade integer,
                                                            dt_empenho date,
                                                            cgm integer,
                                                            cod_pre_empenho integer,
                                                            cod_nota integer,                         
                                                            data_pagamento date,
                                                            data_liquidacao date,  
                                                            valor_liquidacao numeric,                 
                                                            sinal_valor text,                         
                                                            observacao varchar,                       
                                                            ordem integer,                      
                                                            oid integer,
                                                            cod_ordem integer)
                                                            
                                      JOIN empenho.pagamento_liquidacao as pl     
                                        ON pl.cod_nota     = tabela.cod_nota
                                       AND pl.cod_entidade = tabela.cod_entidade
                                       AND pl.cod_ordem    = tabela.cod_ordem
                                       AND pl.exercicio    = tabela.exercicio
                         
                                     JOIN empenho.nota_liquidacao_paga
                                       ON nota_liquidacao_paga.exercicio    = pl.exercicio_liquidacao
                                      AND nota_liquidacao_paga.cod_entidade = pl.cod_entidade
                                      AND nota_liquidacao_paga.cod_nota     = pl.cod_nota

                                LEFT JOIN empenho.nota_liquidacao_paga_anulada
                                       ON nota_liquidacao_paga_anulada.exercicio    = nota_liquidacao_paga.exercicio
                                      AND nota_liquidacao_paga_anulada.cod_nota     = nota_liquidacao_paga.cod_nota
                                      AND nota_liquidacao_paga_anulada.cod_entidade = nota_liquidacao_paga.cod_entidade
                                      AND nota_liquidacao_paga_anulada.timestamp    = nota_liquidacao_paga.timestamp                      
                        ) as dados

             GROUP BY Cod_Und_Gestora
                    , num_empenho
                    , num_liquidacao
                    , num_pagamento
                    , data_pagamento
                    , valor
                    , sinal
                    , historico
                    , codigo_operacao
                    , numero_processo

             ORDER BY num_empenho, num_liquidacao, num_pagamento, data_pagamento, sinal DESC ";
                
        return $stSql;
    }

}

?>
