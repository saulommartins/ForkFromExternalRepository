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

    * Extensão da Classe de Mapeamento TTCETODepositoPagamento
    *
    * Data de Criação: 17/11/2014
    *
    * @author: Evandro Melos
    *
    * $Id: TTCETODepositoPagamento.class.php 60988 2014-11-27 13:30:50Z evandro $
    *
    * @ignore
    *
*/
class TTCETODepositoPagamento extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCETODepositoPagamento()
    {
        parent::Persistente();
        $this->setDado('exercicio',Sessao::getExercicio());
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
        $stSql = "  SELECT * 
                    FROM(
                    SELECT                   
                             cod_und_gestora
                            , recurso_vinculado
                            , replace(cod_conta_contabil::varchar,'.','') AS cod_conta_contabil
                            , num_empenho
                            , num_pagamento
                            , num_registro
                            , dt_liquidacao
                            , vl_pago::varchar AS valor
                            , sinal                                                                                    
                            , sinal as sinal_lancamento
                    FROM (
                            SELECT
                                    dados.cod_und_gestora
                                    , dados.num_empenho
                                    , dados.num_pagamento
                                    , dados.num_registro                                    
                                    , dados.dt_liquidacao
                                    , dados.valor
                                    , dados.sinal                                    
                                    , (SUM(dados.vl_pago)) as vl_pago
                                    , SUM(dados.vl_pago) as vl_pago2
                                    , SUM(dados.vl_anulado) as vl_anulado                                    
                                    , dados.cod_estrutural
                                    , dados.cod_conta_contabil
                                    , cod_conta                          
                                    , recurso_vinculado
                            FROM(
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
                                            , EXTRACT(YEAR FROM DATE (tabela.dt_empenho))::varchar || LPAD(tabela.cod_empenho::varchar,9,'0') AS num_empenho
                                            , EXTRACT(YEAR FROM DATE (tabela.data_pagamento))::varchar || LPAD(pl.cod_ordem::varchar,9,'0') AS num_pagamento
                                            , TO_CHAR(tabela.data_pagamento, 'yyyy') || LPAD(tabela.cod_ordem::varchar,9,'0') AS num_registro                                            
                                            , to_char(tabela.data_pagamento,'yyyy-mm-dd') AS dt_liquidacao   
                                            , replace(tabela.valor_liquidacao::varchar,'.',',') AS valor     
                                            , tabela.sinal_valor AS sinal                                                                                                                                      
                                            , nota_liquidacao_paga.vl_pago
                                            , CASE WHEN nota_liquidacao_paga_anulada.vl_anulado IS NOT NULL THEN
                                                      nota_liquidacao_paga_anulada.vl_anulado
                                              ELSE
                                                      0.00
                                              END AS vl_anulado
                                            , plano_conta.cod_estrutural
                                            , plano_conta.cod_estrutural AS cod_conta_contabil
                                            , plano_analitica.cod_plano AS cod_conta
                                            , despesa.cod_recurso as recurso_vinculado
                            
                                    FROM tceto.fn_exportacao_liquidacao('".$this->getDado('exercicio')."',    
                                                                        '".$this->getDado('dtInicial')."',    
                                                                        '".$this->getDado('dtFinal')."',    
                                                                        '".$this->getDado('cod_entidade')."',    
                                                                        '')    
                                    AS tabela   (   exercicio char(4),                        
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
                                                    cod_ordem integer
                                    )
                          
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
                                    dados.cod_und_gestora
                                    , dados.num_empenho
                                    , dados.num_registro
                                    , dados.num_pagamento
                                    , dados.dt_liquidacao
                                    , dados.valor
                                    , dados.sinal                                                                       
                                    , dados.cod_estrutural
                                    , dados.cod_conta_contabil
                                    , cod_conta
                                    , recurso_vinculado
                            ORDER BY dados.num_empenho
                        ) as result
                        WHERE vl_pago>0.00 AND sinal='+'
                
                    UNION                

                        SELECT                                 
                                cod_und_gestora
                                , recurso_vinculado
                                , replace(cod_conta_contabil::varchar,'.','') AS cod_conta_contabil
                                , num_empenho
                                , num_pagamento
                                , num_registro
                                , dt_liquidacao
                                , sum(vl_pago)::varchar AS valor
                                , sinal                                                                                                                                
                                , sinal as sinal_lancamento
                        FROM (
                                SELECT
                                        dados.cod_und_gestora
                                        , dados.num_empenho
                                        , dados.num_registro
                                        , dados.num_pagamento
                                        , dados.dt_liquidacao
                                        , dados.valor
                                        , dados.sinal                                        
                                        , ((dados.vl_pago)-(dados.vl_anulado)) as vl_pago
                                        , dados.vl_pago as vl_pago2
                                        , dados.vl_anulado                                        
                                        , dados.cod_estrutural
                                        , dados.cod_conta_contabil
                                        , cod_conta
                                        , cod_nota
                                        , recurso_vinculado        
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
                                                , EXTRACT(YEAR FROM DATE (tabela.dt_empenho))::varchar || LPAD(tabela.cod_empenho::varchar,9,'0') AS num_empenho
                                                , EXTRACT(YEAR FROM DATE (tabela.data_pagamento))::varchar || LPAD(pl.cod_ordem::varchar,9,'0') AS num_pagamento
                                                , TO_CHAR(tabela.data_pagamento, 'yyyy') || LPAD(tabela.cod_ordem::varchar,9,'0') AS num_registro                                            
                                                , to_char(tabela.data_pagamento,'yyyy-mm-dd') AS dt_liquidacao   
                                                , replace(tabela.valor_liquidacao::varchar,'.',',') AS valor     
                                                , '-'::text AS sinal                                                                                                                                              
                                                , ordem_pagamento_retencao.vl_retencao AS vl_pago
                                                , 0.00 AS vl_anulado
                                                , pc.cod_estrutural
                                                , plano_conta.cod_estrutural AS cod_conta_contabil
                                                , ordem_pagamento_retencao.cod_plano AS cod_conta
                                                , nota_liquidacao_paga.cod_nota
                                                , despesa.cod_recurso as recurso_vinculado
                                        FROM tceto.fn_exportacao_liquidacao('".$this->getDado('exercicio')."',    
                                                                            '".$this->getDado('dtInicial')."',    
                                                                            '".$this->getDado('dtFinal')."',
                                                                            '".$this->getDado('cod_entidade')."',    
                                                                            '')
                                        AS tabela   (   exercicio char(4),                        
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
                                                        cod_ordem integer
                                        )
                          
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
                                        dados.cod_und_gestora
                                        , dados.num_empenho
                                        , dados.num_registro
                                        , dados.num_pagamento
                                        , dados.dt_liquidacao
                                        , dados.valor
                                        , dados.sinal                                                                                
                                        , dados.cod_estrutural
                                        , dados.cod_conta_contabil
                                        , cod_conta
                                        , dados.vl_pago
                                        , dados.vl_anulado
                                        , cod_nota
                                        , recurso_vinculado 
                                ORDER BY dados.num_empenho
                        ) as result
                        GROUP BY
                                cod_und_gestora
                                , num_empenho
                                , num_registro
                                , num_pagamento
                                , dt_liquidacao
                                , sinal                                                                
                                , cod_estrutural
                                , cod_conta_contabil
                                , recurso_vinculado
                
                    UNION
                
                        SELECT 
                                cod_und_gestora
                                , recurso_vinculado
                                , replace(cod_conta_contabil::varchar,'.','') AS cod_conta_contabil
                                , num_empenho
                                , num_pagamento
                                , num_registro
                                , dt_liquidacao
                                , vl_pago::varchar AS valor
                                , sinal                                                                                                
                                , sinal as sinal_lancamento
                        FROM (
                                SELECT
                                        dados.cod_und_Gestora
                                        , dados.num_empenho
                                        , dados.num_registro
                                        , dados.num_pagamento
                                        , dados.dt_liquidacao
                                        , dados.valor
                                        , dados.sinal                                        
                                        , (SUM(dados.vl_anulado)) as vl_pago
                                        , SUM(dados.vl_pago) as vl_pago2
                                        , SUM(dados.vl_anulado) as vl_anulado                                                                                
                                        , dados.cod_conta_contabil
                                        , cod_conta                          
                                        , recurso_vinculado
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
                                                , EXTRACT(YEAR FROM DATE (tabela.dt_empenho))::varchar || LPAD(tabela.cod_empenho::varchar,9,'0') AS num_empenho
                                                , EXTRACT(YEAR FROM DATE (tabela.data_pagamento))::varchar || LPAD(pl.cod_ordem::varchar,9,'0') AS num_pagamento
                                                , TO_CHAR(tabela.data_pagamento, 'yyyy') || LPAD(tabela.cod_ordem::varchar,9,'0') AS num_registro
                                                , to_char(tabela.data_pagamento,'yyyy-mm-dd') AS dt_liquidacao   
                                                , replace(tabela.valor_liquidacao::varchar,'.',',') AS valor     
                                                , tabela.sinal_valor::text AS sinal                                                                                                                                              
                                                , nota_liquidacao_paga.vl_pago
                                                , CASE WHEN nota_liquidacao_paga_anulada.vl_anulado IS NOT NULL THEN
                                                          nota_liquidacao_paga_anulada.vl_anulado
                                                  ELSE
                                                          0.00
                                                  END AS vl_anulado
                                                , plano_conta.cod_estrutural
                                                , plano_conta.cod_estrutural AS cod_conta_contabil
                                                , plano_analitica.cod_plano AS cod_conta
                                                , despesa.cod_recurso as recurso_vinculado
                                        FROM tceto.fn_exportacao_liquidacao('".$this->getDado('exercicio')."',    
                                                                            '".$this->getDado('dtInicial')."',    
                                                                            '".$this->getDado('dtFinal')."',
                                                                            '".$this->getDado('cod_entidade')."',    
                                                                            '')    
                                        AS tabela   (   exercicio char(4),                        
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
                                                        cod_ordem integer
                                        )

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
                                dados.cod_und_gestora
                                , dados.num_empenho
                                , dados.num_registro
                                , dados.num_pagamento
                                , dados.dt_liquidacao
                                , dados.valor
                                , dados.sinal                                                                                               
                                , dados.cod_conta_contabil
                                , cod_conta
                                , recurso_vinculado
                        ORDER BY dados.num_empenho
                    ) as result
                    WHERE vl_pago>0.00 AND sinal='-'
                    
                    UNION
                                        
                    SELECT tabela_estornados.cod_und_gestora
                         , tabela_estornados.recurso_vinculado                 
                         , tabela_estornados.cod_conta_balancete AS cod_conta_contabil
                         , tabela_estornados.num_empenho
                         , tabela_estornados.num_pagamento
                         , tabela_estornados.num_registro
                         , tabela_estornados.data::VARCHAR AS data_liquidacao
                         , tabela_estornados.valor::VARCHAR
                         , tabela_estornados.sinal
                         , tabela_estornados.sinal AS sinal_lancamento 
                    FROM (
                            SELECT (SELECT PJ.cnpj
                                      FROM orcamento.entidade
                                      JOIN sw_cgm
                                        ON sw_cgm.numcgm = entidade.numcgm
                                      JOIN sw_cgm_pessoa_juridica AS PJ
                                        ON sw_cgm.numcgm = PJ.numcgm
                                     WHERE entidade.exercicio    = '".$this->getDado('exercicio')."'
                                       AND entidade.cod_entidade = entidade
                                    ) AS cod_und_gestora
                                    , recurso_vinculado
                                    , exercicio || LPAD(empenho::VARCHAR ,9,'0') AS num_empenho
                                    , exercicio || LPAD(ordem::VARCHAR,9,'0') AS num_pagamento
                                    , exercicio || LPAD(ordem::VARCHAR,9,'0') AS num_registro
                                    , to_date(data,'dd/mm/yyyy') as data
                                    , REPLACE(despesa, '.','') AS cod_conta_balancete
                                    , CASE WHEN sinal = '-'THEN 
                                           valor_estornado
                                       ELSE
                                           valor
                                       END as valor
                                    , sinal
                                    , tipo_pagamento
                                    , num_documento
                                    
                                 FROM tceto.empenho_pago_estornado('".$this->getDado('exercicio')."','".$this->getDado('dtInicial')."','".$this->getDado('dtFinal')."','".$this->getDado('cod_entidade')."','data')
                                   AS retorno (
                                        entidade            integer,
                                        descricao_categoria varchar,
                                        nom_tipo            varchar,
                                        empenho             integer,
                                        exercicio           char(4),
                                        cgm                 integer,
                                        razao_social        varchar,
                                        cod_nota            integer,
                                        exercicio_liquidacao char(4),
                                        dt_liquidacao       date,
                                        data                text,
                                        ordem               integer,
                                        conta               integer,
                                        nome_conta          varchar,
                                        valor               numeric,
                                        valor_estornado     numeric,
                                        valor_liquido       numeric,
                                        descricao           varchar,
                                        recurso             varchar,
                                        despesa             varchar(150),
                                        cod_banco           varchar,
                                        cod_agencia         varchar,
                                        conta_corrente      varchar(30),
                                        sinal               varchar,
                                        dt_empenho          date,
                                        num_documento       varchar,
                                        tipo_pagamento      integer,                                        
                                        recurso_vinculado   integer
                                    )
                    
                    UNION
                            
                            SELECT (SELECT PJ.cnpj
                                     FROM orcamento.entidade
                                     JOIN sw_cgm
                                       ON sw_cgm.numcgm = entidade.numcgm
                                     JOIN sw_cgm_pessoa_juridica AS PJ
                                       ON sw_cgm.numcgm = PJ.numcgm
                                    WHERE entidade.exercicio    = '".$this->getDado('exercicio')."'
                                      AND entidade.cod_entidade = entidade
                                    ) AS cod_und_gestora
                                    , recurso_vinculado
                                    , exercicio || LPAD(empenho::VARCHAR ,9,'0') AS num_empenho
                                    , exercicio || LPAD(ordem::VARCHAR,9,'0') AS num_pagamento
                                    , exercicio || LPAD(ordem::VARCHAR,9,'0') AS num_registro
                                    , to_date(data,'dd/mm/yyyy') as data
                                    , REPLACE(cod_estrutural,'.','') AS conta_contabil
                                    , valor
                                    , sinal
                                    , tipo_pagamento
                                    , num_documento
                                    
                                 FROM tceto.empenho_pago_estornado_restos( '".$this->getDado('dtInicial')."', '".$this->getDado('dtFinal')."', '".$this->getDado('cod_entidade')."', '1')
                                   AS retorno1( 
                                        entidade            integer,                             
                                        empenho             integer,                             
                                        exercicio           char(4),                             
                                        credor              varchar,                             
                                        cod_estrutural      varchar,                             
                                        cod_nota            integer,                             
                                        exercicio_liquidacao char(4),
                                        dt_liquidacao       date,
                                        data                text,                                
                                        conta               integer,                             
                                        banco               varchar,                             
                                        valor               numeric,
                                        cod_banco           varchar,
                                        cod_agencia         varchar,
                                        conta_corrente      varchar,
                                        sinal               varchar,
                                        dt_empenho          date,
                                        ordem               integer,
                                        num_documento       varchar,
                                        tipo_pagamento      integer,
                                        recurso_vinculado   integer
                                    ) 
                        
                        UNION
                        
                            SELECT (SELECT PJ.cnpj
                                     FROM orcamento.entidade
                                     JOIN sw_cgm
                                       ON sw_cgm.numcgm = entidade.numcgm
                                     JOIN sw_cgm_pessoa_juridica AS PJ
                                       ON sw_cgm.numcgm = PJ.numcgm
                                    WHERE entidade.exercicio    = '".$this->getDado('exercicio')."'
                                      AND entidade.cod_entidade = entidade
                                    ) AS cod_und_gestora
                                    , recurso_vinculado
                                    , exercicio || LPAD(empenho::VARCHAR ,9,'0') AS num_empenho
                                    , exercicio || LPAD(ordem::VARCHAR,9,'0') AS num_pagamento
                                    , exercicio || LPAD(ordem::VARCHAR,9,'0') AS num_registro
                                    , to_date(data,'dd/mm/yyyy') as data
                                    , REPLACE(cod_estrutural,'.','') AS conta_contabil
                                    , valor
                                    , sinal
                                    , tipo_pagamento
                                    , num_documento
                                    
                                FROM tceto.empenho_pago_estornado_restos( '".$this->getDado('dtInicial')."', '".$this->getDado('dtFinal')."', '".$this->getDado('cod_entidade')."', '2')
                                  AS retorno2 ( 
                                        entidade            integer,                             
                                        empenho             integer,                             
                                        exercicio           char(4),                             
                                        credor              varchar,                             
                                        cod_estrutural      varchar,                             
                                        cod_nota            integer,                             
                                        exercicio_liquidacao char(4),
                                        dt_liquidacao       date,
                                        data                text,                                
                                        conta               integer,                             
                                        banco               varchar,                             
                                        valor               numeric,
                                        cod_banco           varchar,
                                        cod_agencia         varchar,
                                        conta_corrente      varchar,
                                        sinal               varchar,
                                        dt_empenho          date,
                                        ordem               integer,
                                        num_documento       varchar,
                                        tipo_pagamento      integer,
                                        recurso_vinculado   integer
                                )
                    ) AS tabela_estornados
                    
                ) AS final
                WHERE SUBSTR(cod_conta_contabil,1,1) <> '4'
                ORDER BY num_empenho ";
                
        return $stSql;
    }
    
}
?>