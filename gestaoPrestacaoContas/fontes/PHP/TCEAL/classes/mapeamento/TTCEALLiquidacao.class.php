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

    * Extensão da Classe de Mapeamento TTCEALCredor
    *
    * Data de Criação: 27/05/2014
    *
    * @author: Michel Teixeira
    *
    $Id: TTCEALLiquidacao.class.php 65567 2016-05-31 21:12:25Z arthur $
    *
    * @ignore
    *
*/
class TTCEALLiquidacao extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCEALLiquidacao()
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
        $stSql = " SELECT CASE WHEN codigo_ua = ''
                               THEN  '0000' 
                               ELSE
                                   dados.codigo_ua
                            END AS Codigo_UA
                          , Cod_Und_Gestora
                          , num_empenho
                          , num_liquidacao
                          , dt_liquidacao
                          , valor
                          , sinal
                          , TRIM(historico, E' \\n\\r\\t') as historico
                          , ordem
                          , codigo_operacao
                          , referencia
                          , cod_credor
                          , num_processo
                          , '".$this->getDado('exercicio')."' AS exercicio
                          , '".$this->getDado('bimestre')."' AS bimestre
                          
                    FROM (   SELECT DISTINCT
                                    (SELECT PJ.cnpj
                                       FROM orcamento.entidade
                                       JOIN sw_cgm
                                         ON sw_cgm.numcgm=entidade.numcgm
                                       JOIN sw_cgm_pessoa_juridica AS PJ
                                         ON sw_cgm.numcgm=PJ.numcgm
                                      WHERE entidade.exercicio='".$this->getDado('exercicio')."'
                                        AND entidade.cod_entidade=".$this->getDado('und_gestora')."
                                    ) AS Cod_Und_Gestora
                                  , (SELECT valor
                                       FROM administracao.configuracao_entidade
                                      WHERE exercicio = '".$this->getDado('exercicio')."'
                                        AND cod_entidade = ".$this->getDado('und_gestora')."
                                        AND cod_modulo = 62
                                        AND parametro = 'tceal_configuracao_unidade_autonoma'
                                    )::VARCHAR AS Codigo_UA
                                  , EXTRACT(YEAR FROM DATE (tabela.dt_empenho))::varchar || LPAD(EXTRACT(MONTH FROM DATE (tabela.dt_empenho))::varchar,2,'0') || LPAD(tabela.cod_empenho::varchar,7,'0') AS num_empenho
                                  , EXTRACT(YEAR FROM DATE (tabela.dt_empenho))::varchar || LPAD(EXTRACT(MONTH FROM DATE (tabela.dt_empenho))::varchar,2,'0') || LPAD(tabela.cod_nota::varchar,7,'0') AS num_liquidacao
                                  , to_char(tabela.data_pagamento,'dd/mm/yyyy') AS dt_liquidacao   
                                  , tabela.valor_liquidacao AS valor     
                                  , tabela.sinal_valor AS sinal                                              
                                  , tabela.observacao AS historico                                               
                                  , tabela.ordem                                                    
                                  , '000'::VARCHAR AS codigo_operacao
                                  , TO_CHAR(tabela.dt_empenho,'mm/yyyy') AS referencia
                                  , credor.codigo AS cod_credor
                                  , processo_administrativo_ano.valor || LPAD(processo_administrativo.valor ,11, '0') AS num_processo
                                
                            FROM tceal.fn_exportacao_liquidacao('".$this->getDado('exercicio')."',    
                                                                '".$this->getDado('dtInicial')."',    
                                                                '".$this->getDado('dtFinal')."',    
                                                                '".$this->getDado('cod_entidade')."',    
                                                                '')    
                                 AS tabela   ( exercicio char(4)
                                              , cod_empenho integer
                                              , cod_entidade integer
                                              , dt_empenho date
                                              , cgm integer
                                              , cod_pre_empenho integer
                                              , cod_nota integer                   
                                              , data_pagamento date
                                              , data_liquidacao date
                                              , valor_liquidacao numeric
                                              , sinal_valor text                  
                                              , observacao varchar
                                              , ordem integer                  
                                              , oid integer
                                              , cod_ordem integer
                                              )
                                                            
                            JOIN (SELECT
                                         pf.cpf AS codigo,
                                         pf.numcgm
                                    FROM sw_cgm_pessoa_fisica AS pf
                                    
                                    UNION
                                    
                                  SELECT
                                         pj.cnpj AS codigo,
                                         pj.numcgm
                                    FROM sw_cgm_pessoa_juridica AS pj
                                ) AS credor
                              ON credor.numcgm = tabela.cgm
                              
                      INNER JOIN empenho.pre_empenho_despesa
                              ON pre_empenho_despesa.cod_pre_empenho = tabela.cod_pre_empenho
                             AND pre_empenho_despesa.exercicio       = tabela.exercicio
                             
                      INNER JOIN orcamento.despesa
                              ON despesa.cod_despesa = pre_empenho_despesa.cod_despesa
                             AND despesa.exercicio   = pre_empenho_despesa.exercicio
                             
                       -- número processo administrativo 
                       INNER JOIN empenho.atributo_empenho_valor AS processo_administrativo
                               ON processo_administrativo.exercicio       = pre_empenho_despesa.exercicio
                              AND processo_administrativo.cod_pre_empenho = pre_empenho_despesa.cod_pre_empenho
                              AND processo_administrativo.cod_atributo    = 120
                      
                      --exercicio processo administrativo 
                       INNER JOIN empenho.atributo_empenho_valor AS processo_administrativo_ano
                               ON processo_administrativo_ano.exercicio       = pre_empenho_despesa.exercicio
                              AND processo_administrativo_ano.cod_pre_empenho = pre_empenho_despesa.cod_pre_empenho
                              AND processo_administrativo_ano.cod_atributo    = 121
                              
                            ORDER BY num_empenho
                        ) as dados
                ";
                
        return $stSql;
    }

    public function recuperaDepositoPagamento(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaDepositoPagamento().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaDepositoPagamento()
    {       
        $stSql = "
            SELECT * FROM(
                SELECT 
                  codigo_ua
                , cod_und_gestora
                , num_empenho
                , num_liquidacao
                , num_pagamento
                , dt_liquidacao
                , vl_pago::varchar AS valor
                , sinal
                , tipo
                , codigo_operacao
	        , RPAD(replace(cod_estrutural::varchar,'.',''),17,'0') AS cod_estrutural		
                , RPAD(replace(cod_conta_contabil::varchar,'.',''),17,'0') AS cod_conta_contabil
                FROM (
                    SELECT
                        CASE WHEN dados.codigo_ua = ''
                             THEN '0000'
                             ELSE codigo_ua
                        END AS codigo_ua
                        , dados.cod_und_gestora
                        , dados.num_empenho
                        , dados.num_liquidacao
                        , dados.num_pagamento
                        , dados.dt_liquidacao
                        , dados.valor
                        , dados.sinal
                        , dados.tipo
                        , (SUM(dados.vl_pago)) as vl_pago
                        , SUM(dados.vl_pago) as vl_pago2
                        , SUM(dados.vl_anulado) as vl_anulado
                        , dados.codigo_operacao
                        , dados.cod_estrutural
                        , dados.cod_conta_contabil
                    FROM (
                        SELECT
                        
                            (SELECT PJ.cnpj
                               FROM orcamento.entidade
                               JOIN sw_cgm
                                 ON sw_cgm.numcgm=entidade.numcgm
                               JOIN sw_cgm_pessoa_juridica AS PJ
                                 ON sw_cgm.numcgm=PJ.numcgm
                              WHERE entidade.exercicio = '".$this->getDado('exercicio')."'
                                AND entidade.cod_entidade = tabela.cod_entidade
                            ) AS cod_und_gestora
                            
                            ,(SELECT valor
                                FROM administracao.configuracao_entidade
                               WHERE configuracao_entidade.exercicio = '".$this->getDado('exercicio')."'
                                 AND configuracao_entidade.cod_entidade = tabela.cod_entidade
                                 AND configuracao_entidade.cod_modulo = 62
                                 AND configuracao_entidade.parametro = 'tceal_configuracao_unidade_autonoma'
                            ) AS codigo_ua
                            
                            , EXTRACT(YEAR FROM DATE (tabela.dt_empenho))::varchar || LPAD(EXTRACT(MONTH FROM DATE (tabela.dt_empenho))::varchar,2,'0') || LPAD(tabela.cod_empenho::varchar,7,'0') AS num_empenho
                            , EXTRACT(YEAR FROM DATE (tabela.data_liquidacao))::varchar || LPAD(EXTRACT(MONTH FROM DATE (tabela.data_liquidacao))::varchar,2,'0') || LPAD(tabela.cod_nota::varchar,7,'0') AS num_liquidacao
                            , LPAD(pl.cod_ordem::varchar,13,'0') AS num_pagamento
                            , to_char(tabela.data_pagamento,'dd/mm/yyyy') AS dt_liquidacao   
                            , replace(tabela.valor_liquidacao::varchar,'.',',') AS valor     
                            , tabela.sinal_valor AS sinal                                              
                            , '1'::TEXT AS tipo
                            , ''::text AS codigo_operacao
                            , nota_liquidacao_paga.vl_pago
                            , CASE WHEN nota_liquidacao_paga_anulada.vl_anulado IS NOT NULL THEN
                                      nota_liquidacao_paga_anulada.vl_anulado
                              ELSE
                                      0.00
                              END AS vl_anulado
                            , conta_despesa.cod_estrutural
                            , '6.2.2.1.3.04.00.00.00.00'::text AS cod_conta_contabil
                            
                        FROM                                                                 
                            tceal.fn_exportacao_liquidacao('".$this->getDado('exercicio')."',    
                                                            '".$this->getDado('dtInicial')."',    
                                                            '".$this->getDado('dtFinal')."',    
                                                            '".$this->getDado('cod_entidade')."',    
                                                            '')    
                            AS tabela   (exercicio char(4),                        
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
                          
                        JOIN empenho.pre_empenho_despesa
                          ON pre_empenho_despesa.cod_pre_empenho = tabela.cod_pre_empenho
                         AND pre_empenho_despesa.exercicio = tabela.exercicio
                         
                        JOIN orcamento.despesa
                          ON despesa.cod_despesa = pre_empenho_despesa.cod_despesa
                         AND despesa.exercicio = pre_empenho_despesa.exercicio
    
                        LEFT JOIN orcamento.conta_despesa
                          ON conta_despesa.exercicio = despesa.exercicio
                         AND conta_despesa.cod_conta = despesa.cod_conta
                         
                        JOIN empenho.pagamento_liquidacao as pl
                          ON pl.exercicio_liquidacao=EXTRACT(YEAR FROM DATE (tabela.data_liquidacao))::varchar
                         AND pl.cod_nota=tabela.cod_nota
                         AND pl.cod_entidade=tabela.cod_entidade
                         AND pl.cod_ordem=tabela.cod_ordem
                         
                        JOIN empenho.nota_liquidacao_paga
                          ON nota_liquidacao_paga.exercicio=pl.exercicio_liquidacao
                         AND nota_liquidacao_paga.cod_entidade=pl.cod_entidade
                         AND nota_liquidacao_paga.cod_nota=pl.cod_nota
                         AND nota_liquidacao_paga.timestamp::date=tabela.data_pagamento

                    LEFT JOIN empenho.nota_liquidacao_paga_anulada
                          ON nota_liquidacao_paga_anulada.exercicio=nota_liquidacao_paga.exercicio
                         AND nota_liquidacao_paga_anulada.cod_nota=nota_liquidacao_paga.cod_nota
                         AND nota_liquidacao_paga_anulada.cod_entidade=nota_liquidacao_paga.cod_entidade
                         AND nota_liquidacao_paga_anulada.timestamp=nota_liquidacao_paga.timestamp

                        JOIN empenho.nota_liquidacao_conta_pagadora
                          ON nota_liquidacao_conta_pagadora.cod_entidade=nota_liquidacao_paga.cod_entidade
                         AND nota_liquidacao_conta_pagadora.cod_nota=nota_liquidacao_paga.cod_nota
                         AND nota_liquidacao_conta_pagadora.exercicio_liquidacao=nota_liquidacao_paga.exercicio
                         AND nota_liquidacao_conta_pagadora.timestamp=nota_liquidacao_paga.timestamp

                        JOIN contabilidade.plano_analitica
                          ON plano_analitica.cod_plano=nota_liquidacao_conta_pagadora.cod_plano
                         AND plano_analitica.exercicio=nota_liquidacao_conta_pagadora.exercicio

                        JOIN contabilidade.plano_conta
                          ON plano_conta.exercicio=plano_analitica.exercicio
                         AND plano_conta.cod_conta=plano_analitica.cod_conta
                          
                        ORDER BY num_empenho
                    ) as dados
                    GROUP BY
                      dados.codigo_ua
                    , dados.cod_und_gestora
                    , dados.num_empenho
                    , dados.num_liquidacao
                    , dados.num_pagamento
                    , dados.dt_liquidacao
                    , dados.valor
                    , dados.sinal
                    , dados.tipo
                    , dados.codigo_operacao
                    , dados.cod_estrutural
                    , dados.cod_conta_contabil

                    ORDER BY dados.num_empenho
                ) as result
                WHERE vl_pago>0.00 AND sinal='+'
                
                UNION                

                SELECT 
                  codigo_ua
                , cod_und_gestora
                , num_empenho
                , num_liquidacao
                , num_pagamento
                , dt_liquidacao
                , sum(vl_pago)::varchar AS valor
                , sinal
                , tipo
                , codigo_operacao
                , replace(cod_estrutural::varchar,'.','') AS cod_estrutural
                , replace(cod_conta_contabil::varchar,'.','') AS cod_conta_contabil
    
                FROM (
                    SELECT
                        CASE WHEN codigo_ua IS NOT NULL AND codigo_ua!=''
                             THEN codigo_ua
                             ELSE
                                  '0000'
                        END AS codigo_ua
                        , dados.cod_und_gestora
                        , dados.num_empenho
                        , dados.num_liquidacao
                        , dados.num_pagamento
                        , dados.dt_liquidacao
                        , dados.valor
                        , dados.sinal
                        , dados.tipo
                        , ((dados.vl_pago)-(dados.vl_anulado)) as vl_pago
                        , dados.vl_pago as vl_pago2
                        , dados.vl_anulado
                        , dados.codigo_operacao
                        , dados.cod_estrutural
                        , dados.cod_conta_contabil
                        , cod_nota        
                    FROM (
                        SELECT
                            (SELECT PJ.cnpj
                               FROM orcamento.entidade
                               JOIN sw_cgm
                                 ON sw_cgm.numcgm=entidade.numcgm
                               JOIN sw_cgm_pessoa_juridica AS PJ
                                 ON sw_cgm.numcgm=PJ.numcgm
                              WHERE entidade.exercicio = '".$this->getDado('exercicio')."'
                                AND entidade.cod_entidade = tabela.cod_entidade
                            ) AS cod_und_gestora
                            
                            ,(SELECT valor
                                FROM administracao.configuracao_entidade
                               WHERE configuracao_entidade.exercicio = '".$this->getDado('exercicio')."'
                                 AND configuracao_entidade.cod_entidade = tabela.cod_entidade
                                 AND configuracao_entidade.cod_modulo = 62
                                 AND configuracao_entidade.parametro = 'tceal_configuracao_unidade_autonoma'
                            ) AS codigo_ua
                            
                            , EXTRACT(YEAR FROM DATE (tabela.dt_empenho))::varchar || LPAD(EXTRACT(MONTH FROM DATE (tabela.dt_empenho))::varchar,2,'0') || LPAD(tabela.cod_empenho::varchar,7,'0') AS num_empenho
                            , EXTRACT(YEAR FROM DATE (tabela.data_liquidacao))::varchar || LPAD(EXTRACT(MONTH FROM DATE (tabela.data_liquidacao))::varchar,2,'0') || LPAD(tabela.cod_nota::varchar,7,'0') AS num_liquidacao
                            , LPAD(pl.cod_ordem::varchar,13,'0') AS num_pagamento
                            , to_char(tabela.data_pagamento,'dd/mm/yyyy') AS dt_liquidacao   
                            , replace(tabela.valor_liquidacao::varchar,'.',',') AS valor     
                            , '-'::text AS sinal                                              
                            , '2'::text as tipo
                            , ''::text AS codigo_operacao
                            , ordem_pagamento_retencao.vl_retencao AS vl_pago
                            , 0.00 AS vl_anulado
                            , pc.cod_estrutural
                            , '6.2.2.1.3.04.00.00.00.00'::text AS cod_conta_contabil
                            , nota_liquidacao_paga.cod_nota
                            
                        FROM                                                                 
                            tceal.fn_exportacao_liquidacao('".$this->getDado('exercicio')."',    
                                                            '".$this->getDado('dtInicial')."',    
                                                            '".$this->getDado('dtFinal')."',    
                                                            '".$this->getDado('cod_entidade')."',    
                                                            '')
                            AS tabela   (exercicio char(4),                        
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
                          
                        JOIN empenho.pre_empenho_despesa
                          ON pre_empenho_despesa.cod_pre_empenho = tabela.cod_pre_empenho
                         AND pre_empenho_despesa.exercicio = tabela.exercicio
                         
                        JOIN orcamento.despesa
                          ON despesa.cod_despesa = pre_empenho_despesa.cod_despesa
                         AND despesa.exercicio = pre_empenho_despesa.exercicio
                         
                        JOIN empenho.pagamento_liquidacao as pl
                          ON pl.exercicio_liquidacao='".$this->getDado('exercicio')."'
                         AND pl.cod_nota=tabela.cod_nota
                         AND pl.cod_entidade=tabela.cod_entidade
                         AND pl.cod_ordem=tabela.cod_ordem
                         
                        JOIN empenho.nota_liquidacao_paga
                          ON nota_liquidacao_paga.exercicio=pl.exercicio_liquidacao
                         AND nota_liquidacao_paga.cod_entidade=pl.cod_entidade
                         AND nota_liquidacao_paga.cod_nota=pl.cod_nota

                        JOIN empenho.ordem_pagamento_retencao
                          ON ordem_pagamento_retencao.cod_ordem=pl.cod_ordem
                         AND ordem_pagamento_retencao.cod_entidade=pl.cod_entidade
                         AND ordem_pagamento_retencao.exercicio=pl.exercicio

                        JOIN contabilidade.plano_analitica
                          ON plano_analitica.cod_plano=ordem_pagamento_retencao.cod_plano
                         AND plano_analitica.exercicio=ordem_pagamento_retencao.exercicio

                        JOIN contabilidade.plano_conta
                          ON plano_conta.exercicio=plano_analitica.exercicio
                         AND plano_conta.cod_conta=plano_analitica.cod_conta
                         
                        JOIN empenho.nota_liquidacao_conta_pagadora
                          ON nota_liquidacao_conta_pagadora.cod_entidade=nota_liquidacao_paga.cod_entidade
                         AND nota_liquidacao_conta_pagadora.cod_nota=nota_liquidacao_paga.cod_nota
                         AND nota_liquidacao_conta_pagadora.exercicio_liquidacao=nota_liquidacao_paga.exercicio
                         AND nota_liquidacao_conta_pagadora.timestamp=nota_liquidacao_paga.timestamp

                        JOIN contabilidade.plano_analitica AS pa
                          ON pa.cod_plano=nota_liquidacao_conta_pagadora.cod_plano
                         AND pa.exercicio=nota_liquidacao_conta_pagadora.exercicio

                        JOIN contabilidade.plano_conta AS pc
                          ON pc.exercicio=pa.exercicio
                         AND pc.cod_conta=pa.cod_conta
                         AND pc.cod_estrutural = '1.1.1.1.1.01.01.00.00.00'
                          
                        ORDER BY num_empenho
                    ) as dados
                    GROUP BY
                      dados.codigo_ua
                    , dados.cod_und_gestora
                    , dados.num_empenho
                    , dados.num_liquidacao
                    , dados.num_pagamento
                    , dados.dt_liquidacao
                    , dados.valor
                    , dados.sinal
                    , dados.tipo
                    , dados.codigo_operacao
                    , dados.cod_estrutural
                    , dados.cod_conta_contabil
                    , dados.vl_pago
                    , dados.vl_anulado
                    , cod_nota

                    ORDER BY dados.num_empenho
                ) as result
                
                GROUP BY
                  codigo_ua
                , cod_und_gestora
                , num_empenho
                , num_liquidacao
                , num_pagamento
                , dt_liquidacao
                , sinal
                , tipo
                , codigo_operacao
                , cod_estrutural
                , cod_conta_contabil
                
                UNION
                
                SELECT 
                  codigo_ua
                , cod_und_gestora
                , num_empenho
                , num_liquidacao
                , num_pagamento
                , dt_liquidacao
                , vl_pago::varchar AS valor
                , sinal
                , tipo
                , codigo_operacao
                , replace(cod_estrutural::varchar,'.','') AS cod_estrutural
                , replace(cod_conta_contabil::varchar,'.','') AS cod_conta_contabil
    
                FROM (
                    SELECT
                        CASE WHEN codigo_ua IS NOT NULL AND codigo_ua!=''
                             THEN codigo_ua
                             ELSE
                                  '0000'
                        END AS codigo_ua
                        , dados.cod_und_Gestora
                        , dados.num_empenho
                        , dados.num_liquidacao
                        , dados.num_pagamento
                        , dados.dt_liquidacao
                        , dados.valor
                        , dados.sinal
                        , dados.tipo
                        , (SUM(dados.vl_anulado)) as vl_pago
                        , SUM(dados.vl_pago) as vl_pago2
                        , SUM(dados.vl_anulado) as vl_anulado
                        , dados.codigo_operacao
                        , dados.cod_estrutural
                        , dados.cod_conta_contabil
                    FROM (
                        SELECT
                            (SELECT PJ.cnpj
                               FROM orcamento.entidade
                               JOIN sw_cgm
                                 ON sw_cgm.numcgm=entidade.numcgm
                               JOIN sw_cgm_pessoa_juridica AS PJ
                                 ON sw_cgm.numcgm=PJ.numcgm
                              WHERE entidade.exercicio='".$this->getDado('exercicio')."'
                                AND entidade.cod_entidade = tabela.cod_entidade
                            ) AS cod_und_gestora
                            ,(SELECT valor
                                FROM administracao.configuracao_entidade
                               WHERE configuracao_entidade.exercicio = '".$this->getDado('exercicio')."'
                                 AND configuracao_entidade.cod_entidade = tabela.cod_entidade
                                 AND configuracao_entidade.cod_modulo = 62
                                 AND configuracao_entidade.parametro = 'tceal_configuracao_unidade_autonoma'
                            ) AS codigo_ua
                            
                            , EXTRACT(YEAR FROM DATE (tabela.dt_empenho))::varchar || LPAD(EXTRACT(MONTH FROM DATE (tabela.dt_empenho))::varchar,2,'0') || LPAD(tabela.cod_empenho::varchar,7,'0') AS num_empenho
                            , EXTRACT(YEAR FROM DATE (tabela.data_liquidacao))::varchar || LPAD(EXTRACT(MONTH FROM DATE (tabela.data_liquidacao))::varchar,2,'0') || LPAD(tabela.cod_nota::varchar,7,'0') AS num_liquidacao
                            , LPAD(pl.cod_ordem::varchar,13,'0') AS num_pagamento
                            , to_char(tabela.data_pagamento,'dd/mm/yyyy') AS dt_liquidacao   
                            , replace(tabela.valor_liquidacao::varchar,'.',',') AS valor     
                            , tabela.sinal_valor::text AS sinal                                              
                            , '3'::text as tipo
                            , ''::text AS codigo_operacao
                            , nota_liquidacao_paga.vl_pago
                            , CASE WHEN nota_liquidacao_paga_anulada.vl_anulado IS NOT NULL THEN
                                      nota_liquidacao_paga_anulada.vl_anulado
                              ELSE
                                      0.00
                              END AS vl_anulado
                            , conta_despesa.cod_estrutural
                            , '6.2.2.1.3.04.00.00.00.00'::text AS cod_conta_contabil
                            
                        FROM                                                                 
                            tceal.fn_exportacao_liquidacao('".$this->getDado('exercicio')."',    
                                                            '".$this->getDado('dtInicial')."',    
                                                            '".$this->getDado('dtFinal')."',    
                                                            '".$this->getDado('cod_entidade')."',    
                                                            '')    
                            AS tabela   (exercicio char(4),                        
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
                          
                        JOIN empenho.pre_empenho_despesa
                          ON pre_empenho_despesa.cod_pre_empenho = tabela.cod_pre_empenho
                         AND pre_empenho_despesa.exercicio = tabela.exercicio
                         
                        JOIN orcamento.despesa
                          ON despesa.cod_despesa = pre_empenho_despesa.cod_despesa
                         AND despesa.exercicio = pre_empenho_despesa.exercicio
    
                        LEFT JOIN orcamento.conta_despesa
                          ON conta_despesa.exercicio = despesa.exercicio
                         AND conta_despesa.cod_conta = despesa.cod_conta
                         
                        JOIN empenho.pagamento_liquidacao as pl
                          ON pl.exercicio_liquidacao=EXTRACT(YEAR FROM DATE (tabela.data_liquidacao))::varchar
                         AND pl.cod_nota=tabela.cod_nota
                         AND pl.cod_entidade=tabela.cod_entidade
                         AND pl.cod_ordem=tabela.cod_ordem
                         
                        JOIN empenho.nota_liquidacao_paga
                          ON nota_liquidacao_paga.exercicio=pl.exercicio_liquidacao
                         AND nota_liquidacao_paga.cod_entidade=pl.cod_entidade
                         AND nota_liquidacao_paga.cod_nota=pl.cod_nota

                    LEFT JOIN empenho.nota_liquidacao_paga_anulada
                          ON nota_liquidacao_paga_anulada.exercicio=nota_liquidacao_paga.exercicio
                         AND nota_liquidacao_paga_anulada.cod_nota=nota_liquidacao_paga.cod_nota
                         AND nota_liquidacao_paga_anulada.cod_entidade=nota_liquidacao_paga.cod_entidade
                         AND nota_liquidacao_paga_anulada.timestamp=nota_liquidacao_paga.timestamp

                        JOIN empenho.nota_liquidacao_conta_pagadora
                          ON nota_liquidacao_conta_pagadora.cod_entidade=nota_liquidacao_paga.cod_entidade
                         AND nota_liquidacao_conta_pagadora.cod_nota=nota_liquidacao_paga.cod_nota
                         AND nota_liquidacao_conta_pagadora.exercicio_liquidacao=nota_liquidacao_paga.exercicio
                         AND nota_liquidacao_conta_pagadora.timestamp=nota_liquidacao_paga.timestamp

                        JOIN contabilidade.plano_analitica
                          ON plano_analitica.cod_plano=nota_liquidacao_conta_pagadora.cod_plano
                         AND plano_analitica.exercicio=nota_liquidacao_conta_pagadora.exercicio

                        JOIN contabilidade.plano_conta
                          ON plano_conta.exercicio=plano_analitica.exercicio
                         AND plano_conta.cod_conta=plano_analitica.cod_conta
                          
                        ORDER BY num_empenho
                    ) as dados
                    GROUP BY
                      dados.codigo_ua
                    , dados.cod_und_gestora
                    , dados.num_empenho
                    , dados.num_liquidacao
                    , dados.num_pagamento
                    , dados.dt_liquidacao
                    , dados.valor
                    , dados.sinal
                    , dados.tipo
                    , dados.codigo_operacao
                    , dados.cod_estrutural
                    , dados.cod_conta_contabil

                    ORDER BY dados.num_empenho
                ) as result
                WHERE vl_pago>0.00 AND sinal='-'
            ) AS final
            ORDER BY num_empenho, tipo
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
        $stSql = " SELECT
                          Cod_Und_Gestora
                        , Codigo_UA                  
                        , num_empenho
                        , num_liquidacao
                        , num_pagamento
                        , data_pagamento
                        , valor
                        , sinal
                        , TRIM(historico, E' \\n\\r\\t') as historico
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
                                  , EXTRACT(YEAR FROM DATE (tabela.dt_empenho))::varchar || LPAD(EXTRACT(MONTH FROM DATE (tabela.dt_empenho))::varchar,2,'0') || LPAD(tabela.cod_empenho::varchar,7,'0') AS num_empenho
                                  , EXTRACT(YEAR FROM DATE (tabela.data_liquidacao))::varchar || LPAD(EXTRACT(MONTH FROM DATE (tabela.data_liquidacao))::varchar,2,'0') || LPAD(tabela.cod_nota::varchar,7,'0') AS num_liquidacao
                                  , LPAD(tabela.cod_ordem::varchar,13,'0') AS num_pagamento
                                  , to_char(tabela.data_pagamento,'dd/mm/yyyy') AS data_pagamento   
                                  , tabela.valor_liquidacao::varchar AS valor     
                                  , tabela.sinal_valor AS sinal
                                  , tabela.observacao AS historico                                              
                                  , '000'::varchar AS codigo_operacao
                                  , processo_administrativo_ano.valor || LPAD(processo_administrativo.valor ,11, '0') AS numero_processo
                                
                            FROM                                                                 
                                tceal.fn_exportacao_liquidacao('".$this->getDado('exercicio')."',    
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
                                                            
                              -- número processo administrativo 
                                INNER JOIN empenho.atributo_empenho_valor AS processo_administrativo
                                    ON processo_administrativo.exercicio       = tabela.exercicio
                                   AND processo_administrativo.cod_pre_empenho = tabela.cod_pre_empenho
                                   AND processo_administrativo.cod_atributo    = 120
                             
                              --exercicio processo administrativo 
                                INNER JOIN empenho.atributo_empenho_valor AS processo_administrativo_ano
                                    ON processo_administrativo_ano.exercicio       = tabela.exercicio
                                   AND processo_administrativo_ano.cod_pre_empenho = tabela.cod_pre_empenho
                                   AND processo_administrativo_ano.cod_atributo    = 121
              
                        ) as dados
             GROUP BY Cod_Und_Gestora
                    , Codigo_UA                  
                    , num_empenho
                    , num_liquidacao
                    , num_pagamento
                    , data_pagamento
                    , valor
                    , sinal
                    , historico
                    , codigo_operacao
                    , numero_processo
             ORDER BY num_empenho, num_liquidacao, num_pagamento, data_pagamento, sinal DESC
                ";
                
        return $stSql;
    }

}
?>
