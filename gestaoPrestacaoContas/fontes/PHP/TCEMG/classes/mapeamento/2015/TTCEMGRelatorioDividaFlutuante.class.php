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
  * Página de Mapemanto Relatorio Divida Flutuante
  * Data de Criação: 31/07/2014
  * @author Desenvolvedor: Evandro Melos  
  *$Id: TTCEMGRelatorioDividaFlutuante.class.php 62269 2015-04-15 18:28:39Z franver $
  *$Date: $
  *$Author: $
  *$Rev: $
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once (CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracaoEntidade.class.php");

class TTCEMGRelatorioDividaFlutuante extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCEMGRelatorioDividaFlutuante()
    {
        parent::Persistente();        
    }

    public function recuperaSaldoAnteriorRestosEntidades(&$rsRecordSet, $boTransacao = "")
    {
        $obErro      = new Erro;    
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaSaldoAnteriorRestosEntidades();
        $this->stDebug = $stSql;
        
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
    }
    
    public function montaRecuperaSaldoAnteriorRestosEntidades()
    {
        $stSql = " SELECT
                        SUM(valor) as saldo_anterior
                    FROM tcemg.relatorio_divida_flutuante_restos_pagar(
                        '".$this->getDado('exercicio')."'
                        ,''
                        ,'".$this->getDado('data_inicial')."' 
                        ,'".$this->getDado('data_final')."'
                        ,'".$this->getDado('cod_entidade')."'
                        ,''
                        ,''
                        ,''
                        ,''
                        ,''
                        ,''
                        ,'2'
                    ) as retorno(                      
                        entidade            integer,                                           
                        empenho             integer,                                           
                        exercicio           char(4),                                           
                        cgm                 integer,                                           
                        razao_social        varchar,                                           
                        cod_nota            integer,                                           
                        valor               numeric,                                           
                        data                text                                           
                    )
                ";
        return $stSql;
    }

    public function recuperaInscricoesEntidades(&$rsRecordSet, $boTransacao = "")
    {
        $obErro      = new Erro;    
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaInscricoesEntidades();
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
    }
    
    public function montaRecuperaInscricoesEntidades()
    {
        $stSql = " SELECT
                        SUM(valor) as inscricoes
                    FROM tcemg.relatorio_divida_flutuante_restos_pagar(
                        '".$this->getDado('exercicio')."'
                        ,''
                        ,'".$this->getDado('data_inicial')."' 
                        ,'".$this->getDado('data_final')."'
                        ,'".$this->getDado('cod_entidade')."'
                        ,''
                        ,''
                        ,''
                        ,''
                        ,''
                        ,''
                        ,'1'
                    ) as retorno(                      
                        entidade            integer,                                           
                        empenho             integer,                                           
                        exercicio           char(4),                                           
                        cgm                 integer,                                           
                        razao_social        varchar,                                           
                        cod_nota            integer,                                           
                        valor               numeric,                                           
                        data                text                                           
                    )
                ";
        return $stSql;
    }

    public function recuperaBaixaCancelamentoEntidades(&$rsRecordSet, $boTransacao = "")
    {
        $obErro      = new Erro;    
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaBaixaCancelamentoEntidades();
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
    }

    public function montaRecuperaBaixaCancelamentoEntidades()
    {
        $stSql = "  SELECT 
                             SUM(baixa)         as baixa
                            ,SUM(cancelamentos) as cancelamento  
                        FROM (
                            SELECT    
                                    SUM(valor) as baixa
                                    ,0.00 as cancelamentos
                            --BUSCA VALOR DE BAIXAS DE TODAS AS ENTIDADES
                            FROM tcemg.relatorio_divida_flutuante_pagamento_estorno(
                                '".$this->getDado('exercicio')."'                      
                                , ''                      
                                , '".$this->getDado('data_inicial')."' 
                                , '".$this->getDado('data_final')."'
                                , '".$this->getDado('cod_entidade')."'
                                , ''
                                , ''
                                , ''
                                , ''
                                , ''
                                , ''
                                , ''
                                , '1'
                                , ''
                                , ''
                            ) as retorno(      
                                entidade            integer,                             
                                empenho             integer,                             
                                exercicio           char(4),                             
                                credor              varchar,
                                cod_pre_empenho     integer,
                                cod_estrutural      varchar,                             
                                cod_nota            integer,                             
                                data                text,                                
                                conta               integer,                             
                                banco               varchar,                             
                                valor               numeric                                            
                            )
                            WHERE exercicio = '".$this->getDado('exercicio')."'

                            UNION

                            SELECT  
                                    0.00 as baixa
                                    ,SUM(valor) as cancelamentos
                            --BUSCA VALOR DE CANCELAMENTOS DE TODAS AS ENTIDADES                                        
                            FROM tcemg.relatorio_divida_flutuante_pagamento_estorno( 
                                '".$this->getDado('exercicio')."'                      
                                , ''                      
                                , '".$this->getDado('data_inicial')."' 
                                , '".$this->getDado('data_final')."'
                                , '".$this->getDado('cod_entidade')."'
                                , ''
                                , ''
                                , ''
                                , ''
                                , ''
                                , ''
                                , ''
                                , '2'
                                , ''
                                , ''
                            ) as retorno(      
                                    entidade            integer,                             
                                    empenho             integer,                             
                                    exercicio           char(4),                             
                                    credor              varchar,
                                    cod_pre_empenho     integer,
                                    cod_estrutural      varchar,                             
                                    cod_nota            integer,                             
                                    data                text,                                
                                    conta               integer,                             
                                    banco               varchar,                             
                                    valor               numeric              
                            )
                            WHERE exercicio = '".$this->getDado('exercicio')."'
                        )as resultado
                    
                ";
        return $stSql;
    }

    public function recuperaRestosPagarProcessados(&$rsRecordSet, $boTransacao = "")
    {
        $obErro      = new Erro;    
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaRestosPagarProcessados();
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
    }

    public function montaRecuperaRestosPagarProcessados()
    {
        $stSql = "  SELECT 
                            entidade
                            ,'Restos a Pagar de '|| retorno.exercicio::varchar as titulo
                            ,sw_cgm.nom_cgm as nom_entidade
                            ,SUM(empenho.vl_saldo_anterior) as saldo_anterior
                            ,SUM(baixa)                     as baixa 
                            ,SUM(cancelamentos)             as cancelamentos 
                            ,ABS(SUM(empenho.vl_saldo_anterior - (baixa-cancelamentos))) as saldo_atual 
                    FROM (
                            SELECT    
                                valor as baixa
                                ,0.00 as cancelamentos 
                                ,entidade
                                ,exercicio
                                ,empenho
                                ,cod_pre_empenho               
                            FROM tcemg.relatorio_divida_flutuante_pagamento_estorno
                            (   '".$this->getDado('exercicio')."'                      
                               , ''                      
                               , '".$this->getDado('data_inicial')."'
                               , '".$this->getDado('data_final')."'
                               , '".$this->getDado('cod_entidade')."'
                               , ''
                               , ''
                               , ''
                               , ''
                               , ''
                               , ''
                               , ''
                               , '1'
                               , ''
                               , ''
                            ) as retorno_baixas (      
                                entidade            integer,                             
                                empenho             integer,                             
                                exercicio           char(4),                             
                                credor              varchar,
                                cod_pre_empenho     integer,                             
                                cod_estrutural      varchar,                             
                                cod_nota            integer,                             
                                data                text,                                
                                conta               integer,                             
                                banco               varchar,                             
                                valor               numeric                              
                            )

                            UNION


                            SELECT  
                                0.00 as baixa
                                ,valor as cancelamentos
                                ,entidade
                                ,exercicio
                                ,empenho
                                ,cod_pre_empenho            
                            FROM tcemg.relatorio_divida_flutuante_pagamento_estorno(
                               '".$this->getDado('exercicio')."'                      
                               , ''                      
                               , '".$this->getDado('data_inicial')."'
                               , '".$this->getDado('data_final')."'
                               , '".$this->getDado('cod_entidade')."'
                               , ''
                               , ''
                               , ''
                               , ''
                               , ''
                               , ''
                               , ''
                               , '2'
                               , ''
                               , ''
                            ) as retorno_cancelamentos (      
                                entidade            integer,                             
                                empenho             integer,                             
                                exercicio           char(4),                             
                                credor              varchar,
                                cod_pre_empenho     integer,
                                cod_estrutural      varchar,                             
                                cod_nota            integer,                             
                                data                text,                                
                                conta               integer,                             
                                banco               varchar,                             
                                valor               numeric                              
                            )  
                    ) AS retorno

                    JOIN empenho.restos_pre_empenho
                         ON restos_pre_empenho.cod_pre_empenho = retorno.cod_pre_empenho
                        AND restos_pre_empenho.exercicio       = retorno.exercicio
                    JOIN empenho.pre_empenho
                         ON restos_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                        AND restos_pre_empenho.exercicio       = pre_empenho.exercicio
                    JOIN empenho.empenho
                         ON pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                        AND pre_empenho.exercicio       = empenho.exercicio
                    JOIN orcamento.entidade 
                         ON entidade.cod_entidade = retorno.entidade
                    JOIN sw_cgm
                         ON sw_cgm.numcgm = entidade.numcgm
    
                    WHERE retorno.exercicio <> '".$this->getDado('exercicio')."'

                GROUP BY 
                        retorno.entidade
                        ,retorno.exercicio
                        ,sw_cgm.nom_cgm 
                ";
        return $stSql;
    }

    public function recuperaRestosPagarNaoProcessados(&$rsRecordSet, $boTransacao = "")
    {
        $obErro      = new Erro;    
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaRestosPagarNaoProcessados();
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
    }

    public function montaRecuperaRestosPagarNaoProcessados()
    {
        $stSql = "  SELECT 
                            cod_entidade
                            ,exercicio as titulo
                            ,nom_entidade
                            ,saldo_anterior           as saldo_anterior
                            ,baixas                   as baixas 
                            ,liquidados               as liquidados
                            ,cancelados               as cancelados
                            ,saldo_exercicio_anterior as saldo_exercicio_anterior
                            ,ABS(saldo_anterior - (baixas-cancelados)) as saldo_atual
                    FROM(
                        SELECT   
                                'Restos a Pagar de ' || exercicio as exercicio
                                ,cod_entidade
                                ,nom_entidade
                                ,COALESCE(SUM(nao_processados_exercicio_anterior),0.00) as saldo_anterior
                                ,COALESCE(SUM(nao_processados_pago),0.00) as baixas
                                ,COALESCE(SUM(nao_processados_liquidados),0.00) as liquidados
                                ,COALESCE(SUM(nao_processados_cancelado),0.00) as cancelados
                                ,COALESCE(SUM(nao_processados_exercicio_anteriores),0.00) as saldo_exercicio_anterior   
                        FROM(
                            SELECT 
                                    exercicio
                                    ,nom_entidade
                                    ,cod_entidade
                                    ,CASE WHEN tipo = 'tmp_nao_processados_exercicio_anterior' THEN
                                        COALESCE(SUM(vl_total),0.00)
                                    END as nao_processados_exercicio_anterior
                                    ,CASE WHEN tipo = 'tmp_nao_processados_pago' THEN
                                        COALESCE(SUM(vl_total),0.00)
                                    END as nao_processados_pago
                                    ,CASE WHEN tipo = 'tmp_nao_processados_liquidado' THEN
                                        COALESCE(SUM(vl_total),0.00)
                                    END as nao_processados_liquidados     
                                    ,CASE WHEN tipo = 'tmp_nao_processados_cancelado' THEN
                                        COALESCE(SUM(vl_total),0.00)
                                    END as nao_processados_cancelado
                                    ,CASE WHEN tipo = 'tmp_nao_processados_exercicios_anteriores' THEN
                                        COALESCE(SUM(vl_total),0.00)
                                    END as nao_processados_exercicio_anteriores
                            FROM tcemg.relatorio_divida_flutuante_restos_nao_processados('".$this->getDado('exercicio')."', '".$this->getDado('cod_entidade')."', '".$this->getDado('data_final')."')
                            AS (
                                  cod_entidade  INTEGER
                                , nom_entidade  VARCHAR
                                , exercicio     CHAR(4)
                                , vl_total      NUMERIC
                                , tipo          VARCHAR
                            )
                            GROUP BY 
                                    cod_entidade
                                    ,exercicio
                                    ,nom_entidade
                                    ,tipo
                        )as retorno
                        GROUP BY 
                                exercicio
                                ,cod_entidade
                                ,nom_entidade
                    )as relatorio
                ";
        return $stSql;
    }

    public function recuperaDepositosDividaFlutuante(&$rsRecordSet, $boTransacao = "")
    {
        $obErro      = new Erro;    
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaDepositosDividaFlutuante();
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
    }

    public function montaRecuperaDepositosDividaFlutuante()
    {
        $stSql = " 
                    SELECT   
                             cod_entidade
                            ,nom_conta    
                            ,nom_entidade
                            ,ABS(SUM(vl_saldo_anterior)) as vl_saldo_anterior
                            ,ABS(SUM(vl_saldo_debitos))  as inscricao
                            ,ABS(SUM(vl_saldo_creditos)) as baixa
                            ,ABS(SUM(vl_saldo_atual))    as vl_saldo_atual
                    FROM tcemg.relatorio_divida_flutuante_depositos(
                                                                    '".$this->getDado('exercicio')."'
                                                                    ,'AND cod_entidade IN (".$this->getDado('cod_entidade').")'
                                                                    ,'".$this->getDado('data_inicial')."'
                                                                    ,'".$this->getDado('data_final')."'
                                                                    ,'A')
                    AS(
                         cod_estrutural     VARCHAR
                        ,nivel              INTEGER
                        ,nom_conta          VARCHAR
                        ,nom_entidade       VARCHAR
                        ,cod_entidade       INTEGER
                        ,vl_saldo_anterior  NUMERIC
                        ,vl_saldo_debitos   NUMERIC
                        ,vl_saldo_creditos  NUMERIC
                        ,vl_saldo_atual     NUMERIC                
                    )
                    GROUP BY cod_entidade, nom_entidade, nom_conta
                ";
        return $stSql;
    }

    public function recuperaTotaisOrgao(&$rsRecordSet, $boTransacao = "")
    {
        $obErro      = new Erro;    
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaTotaisOrgao();
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
    }

    public function montaRecuperaTotaisOrgao()
    {
        $stSql = "
                    SELECT 
                            cod_entidade                            
                            ,nom_entidade
                            ,SUM(saldo_anterior)        as saldo_anterior
                            ,SUM(incricao)              as inscricao
                            ,SUM(restabelecimento)      as restabelecimento
                            ,SUM(baixa)                 as baixa 
                            ,SUM(cancelamentos)         as cancelamentos 
                            ,ABS(SUM(saldo_atual))      as saldo_atual 
     
                    FROM(
                            SELECT 
                                 entidade               as cod_entidade                           
                                ,sw_cgm.nom_cgm             as nom_entidade
                                ,SUM(empenho.vl_saldo_anterior) as saldo_anterior
                                ,SUM(0.00)              as incricao
                                ,SUM(0.00)              as restabelecimento
                                ,SUM(baixa)                     as baixa 
                                ,SUM(cancelamentos)             as cancelamentos 
                                ,ABS(SUM(empenho.vl_saldo_anterior - (baixa-cancelamentos))) as saldo_atual 
                            FROM (
                                    SELECT    
                                            valor as baixa
                                            ,0.00 as cancelamentos 
                                            ,entidade
                                            ,exercicio
                                            ,empenho
                                            ,cod_pre_empenho               
                                    FROM tcemg.relatorio_divida_flutuante_pagamento_estorno
                                    (   '".$this->getDado('exercicio')."'                      
                                       , ''                      
                                       , '".$this->getDado('data_inicial')."'
                                       , '".$this->getDado('data_final')."'
                                       , '".$this->getDado('cod_entidade')."'
                                       , ''
                                       , ''
                                       , ''
                                       , ''
                                       , ''
                                       , ''
                                       , ''
                                       , '1'
                                       , ''
                                       , ''
                                    ) as retorno_baixas (      
                                        entidade            integer,                             
                                        empenho             integer,                             
                                        exercicio           char(4),                             
                                        credor              varchar,
                                        cod_pre_empenho     integer,                             
                                        cod_estrutural      varchar,                             
                                        cod_nota            integer,                             
                                        data                text,                                
                                        conta               integer,                             
                                        banco               varchar,                             
                                        valor               numeric                              
                                    )

                                    UNION


                                    SELECT  
                                            0.00 as baixa
                                            ,valor as cancelamentos
                                            ,entidade
                                            ,exercicio
                                            ,empenho
                                            ,cod_pre_empenho            
                                    FROM tcemg.relatorio_divida_flutuante_pagamento_estorno(
                                       '".$this->getDado('exercicio')."'
                                       , ''                      
                                       , '".$this->getDado('data_inicial')."'
                                       , '".$this->getDado('data_final')."'
                                       , '".$this->getDado('cod_entidade')."'
                                       , ''
                                       , ''
                                       , ''
                                       , ''
                                       , ''
                                       , ''
                                       , ''
                                       , '2'
                                       , ''
                                       , ''
                                    ) as retorno_cancelamentos (      
                                        entidade            integer,                             
                                        empenho             integer,                             
                                        exercicio           char(4),                             
                                        credor              varchar,
                                        cod_pre_empenho     integer,
                                        cod_estrutural      varchar,                             
                                        cod_nota            integer,                             
                                        data                text,                                
                                        conta               integer,                             
                                        banco               varchar,                             
                                        valor               numeric                              
                                    )  
                                ) AS retorno

                                JOIN empenho.restos_pre_empenho
                                     ON restos_pre_empenho.cod_pre_empenho = retorno.cod_pre_empenho
                                    AND restos_pre_empenho.exercicio       = retorno.exercicio
                                JOIN empenho.pre_empenho
                                     ON restos_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                                    AND restos_pre_empenho.exercicio       = pre_empenho.exercicio
                                JOIN empenho.empenho
                                     ON pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                                    AND pre_empenho.exercicio       = empenho.exercicio
                                JOIN orcamento.entidade 
                                     ON entidade.cod_entidade = retorno.entidade
                                JOIN sw_cgm
                                     ON sw_cgm.numcgm = entidade.numcgm
            
                                WHERE retorno.exercicio <> '2014'

                                GROUP BY 
                                        retorno.entidade
                                        ,retorno.exercicio
                                        ,sw_cgm.nom_cgm 
                
                UNION
                        SELECT 
                                cod_entidade                            
                                ,nom_entidade
                                ,saldo_anterior as saldo_anterior
                                ,0.00           as incricao
                                ,0.00           as restabelecimento
                                ,baixas         as baixas             
                                ,cancelados     as cancelamentos                            
                                ,ABS(saldo_anterior - (baixas-cancelados)) as saldo_atual
                        FROM(
                                SELECT   
                                        'Restos a Pagar de ' || exercicio as exercicio
                                        ,cod_entidade
                                        ,nom_entidade
                                        ,COALESCE(SUM(nao_processados_exercicio_anterior),0.00) as saldo_anterior
                                        ,COALESCE(SUM(nao_processados_pago),0.00) as baixas
                                        ,COALESCE(SUM(nao_processados_liquidados),0.00) as liquidados
                                        ,COALESCE(SUM(nao_processados_cancelado),0.00) as cancelados
                                        ,COALESCE(SUM(nao_processados_exercicio_anteriores),0.00) as saldo_exercicio_anterior   
                                FROM(
                                        SELECT 
                                                exercicio
                                                ,nom_entidade
                                                ,cod_entidade
                                                ,CASE WHEN tipo = 'tmp_nao_processados_exercicio_anterior' THEN
                                                    COALESCE(SUM(vl_total),0.00)
                                                END as nao_processados_exercicio_anterior
                                                ,CASE WHEN tipo = 'tmp_nao_processados_pago' THEN
                                                    COALESCE(SUM(vl_total),0.00)
                                                END as nao_processados_pago
                                                ,CASE WHEN tipo = 'tmp_nao_processados_liquidado' THEN
                                                    COALESCE(SUM(vl_total),0.00)
                                                END as nao_processados_liquidados     
                                                ,CASE WHEN tipo = 'tmp_nao_processados_cancelado' THEN
                                                    COALESCE(SUM(vl_total),0.00)
                                                END as nao_processados_cancelado
                                                ,CASE WHEN tipo = 'tmp_nao_processados_exercicios_anteriores' THEN
                                                    COALESCE(SUM(vl_total),0.00)
                                                END as nao_processados_exercicio_anteriores
                                        FROM tcemg.relatorio_divida_flutuante_restos_nao_processados('".$this->getDado('exercicio')."', '".$this->getDado('cod_entidade')."', '".$this->getDado('data_final')."')
                                        AS (
                                              cod_entidade  INTEGER
                                            , nom_entidade  VARCHAR
                                            , exercicio     CHAR(4)
                                            , vl_total      NUMERIC
                                            , tipo          VARCHAR
                                        )
                                        GROUP BY 
                                                cod_entidade
                                                ,exercicio
                                                ,nom_entidade
                                                ,tipo
                                    )as retorno
                                    GROUP BY 
                                            exercicio
                                            ,cod_entidade
                                            ,nom_entidade
                                )as relatorio

                UNION
                        SELECT   
                                 cod_entidade                             
                                ,nom_entidade
                                ,ABS(SUM(vl_saldo_anterior)) as saldo_anterior
                                ,ABS(SUM(vl_saldo_debitos))  as inscricao
                                ,SUM(0.00)               as restabelecimento
                                ,ABS(SUM(vl_saldo_creditos)) as baixa
                                ,SUM(0.00)                   as cancelamentos
                                ,ABS(SUM(vl_saldo_atual))    as saldo_atual
                        FROM tcemg.relatorio_divida_flutuante_depositos(
                                                                        '".$this->getDado('exercicio')."'
                                                                        ,'AND cod_entidade IN (".$this->getDado('cod_entidade').")'
                                                                        ,'".$this->getDado('data_inicial')."'
                                                                        ,'".$this->getDado('data_final')."'
                                                                        ,'A')
                        AS(
                             cod_estrutural     VARCHAR
                            ,nivel              INTEGER
                            ,nom_conta          VARCHAR
                            ,nom_entidade       VARCHAR
                            ,cod_entidade       INTEGER
                            ,vl_saldo_anterior  NUMERIC
                            ,vl_saldo_debitos   NUMERIC
                            ,vl_saldo_creditos  NUMERIC
                            ,vl_saldo_atual     NUMERIC                
                        )
                        GROUP BY cod_entidade, nom_entidade, nom_conta
            ) AS total                

        GROUP BY cod_entidade, nom_entidade

            ";
        return $stSql;
    }
    
     public function recuperaRestosPagar(&$rsRecordSet, $boTransacao = "")
    {
        $obErro      = new Erro;    
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaRestosPagar();
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
    }

    public function montaRecuperaRestosPagar()
    {
        $stSql = "
                   SELECT  'Restos a Pagar ' || rp.exercicio as titulo
                             , sw_cgm.nom_cgm as entidade
                             , rp.cod_entidade
                             , rp.exercicio
                             , SUM(0.00) as restabelicimento_p
                             , SUM(0.00) as restabelicimento_np
                             , SUM(valor_processado_exercicios_anteriores) as saldo_anterior_p
                             , SUM(valor_processado_exercicio_anterior) as inscricao_p
                             , SUM(valor_processado_cancelado) as cancelado_p
                             , SUM(valor_processado_pago) as baixa_p
                             , (SUM(valor_processado_exercicios_anteriores)+SUM(valor_processado_exercicio_anterior)- SUM(valor_processado_cancelado)-SUM(valor_processado_pago)) as saldo_atual_p
                             , SUM(valor_nao_processado_exercicios_anteriores) as saldo_anterior_np
                             , SUM(valor_nao_processado_exercicio_anterior) as inscricao_np
                             , SUM(valor_nao_processado_cancelado) as cancelado_np
                             , SUM(valor_nao_processado_pago) as baixa_np 
                             , (SUM(valor_nao_processado_exercicios_anteriores)+SUM(valor_nao_processado_exercicio_anterior)- SUM(valor_nao_processado_cancelado)-SUM(valor_nao_processado_pago)) as saldo_atual_np
                     FROM tcemg.fn_restos_pagar( '".$this->getDado('exercicio')."','".$this->getDado('cod_entidade')."',1) as rp (
                                                            cod_empenho INTEGER,
                                                            cod_entidade INTEGER,
                                                            exercicio CHARACTER(4),
                                                            valor_processado_exercicios_anteriores NUMERIC,
                                                            valor_processado_exercicio_anterior NUMERIC,
                                                            valor_processado_cancelado NUMERIC, 
                                                            valor_processado_pago NUMERIC,
                                                            valor_nao_processado_exercicios_anteriores NUMERIC,
                                                            valor_nao_processado_exercicio_anterior NUMERIC,
                                                            valor_nao_processado_cancelado NUMERIC,
                                                            valor_nao_processado_pago NUMERIC

                                                            )
           INNER JOIN orcamento.entidade 
                       ON entidade.cod_entidade = rp.cod_entidade
                     AND entidade.exercicio = rp.exercicio
           INNER JOIN sw_cgm 
                       ON sw_cgm.numcgm = entidade.numcgm 
        
            GROUP BY rp.cod_entidade
                          , rp.exercicio
                          , titulo
                          , sw_cgm.nom_cgm
            ORDER BY rp.cod_entidade
                          , rp.exercicio

            ";
        return $stSql;
    }
    public function recuperaBalanceteVerificacao(&$rsRecordSet, $boTransacao = "")
    {
        $obErro      = new Erro;    
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaBalanceteVerificacao();
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
    }

    public function montaRecuperaBalanceteVerificacao()
    {
        $stSql = "
                    SELECT CASE WHEN cc.nom_cgm IS NULL THEN 
                                        cd.nom_cgm
                                    ELSE 
                                        cc.nom_cgm 
                                    END as nome_entidade
                             , retorno.cod_estrutural                                                       
                             , retorno.nivel                                                                
                             , retorno.nom_conta                  
                             , retorno.cod_sistema                                                        
                             , retorno.indicador_superavit                                              
                             , retorno.vl_saldo_anterior                                                   
                             , (retorno.vl_saldo_debitos)  * -1 as baixa
                             , (retorno.vl_saldo_creditos) * -1 as inscricao
                             , (retorno.vl_saldo_atual)    * -1  as vl_saldo_atual
                     FROM                                                                                        
                                 tcemg.fn_balancete_verificacao( '".$this->getDado('exercicio')."','cod_entidade IN (".$this->getDado('cod_entidade').") and substr(cod_estrutural,1,5)=''2.1.8'' ','".$this->getDado('data_inicial')."' ,'".$this->getDado('data_final')."','')
                                                             as retorno( cod_estrutural varchar                                                      
                                                                                ,nivel integer                                                               
                                                                                ,nom_conta varchar                  

                                                                                ,cod_sistema integer                                                         
                                                                                ,indicador_superavit char(12)                                                
                                                                                ,vl_saldo_anterior numeric                                                   
                                                                                ,vl_saldo_debitos  numeric                                                   
                                                                                ,vl_saldo_creditos numeric                                                   
                                                                                ,vl_saldo_atual    numeric                                                   
                                                                                ) 
                LEFT JOIN (
                                SELECT plano_conta.cod_estrutural 
                                         , sw_cgm.nom_cgm
                                  FROM contabilidade.plano_conta
                          INNER JOIN contabilidade.plano_analitica
                                      ON plano_analitica.exercicio = plano_conta.exercicio
                                    AND plano_analitica.cod_conta = plano_conta.cod_conta
                          INNER JOIN contabilidade.conta_credito
                                      ON conta_credito.cod_plano = plano_analitica.cod_plano
                                    AND conta_credito.exercicio = plano_analitica.exercicio
                          INNER JOIN orcamento.entidade 
                                      ON entidade.cod_entidade = conta_credito.cod_entidade
                                    AND entidade.exercicio = conta_credito.exercicio
                          INNER JOIN sw_cgm 
                                      ON sw_cgm.numcgm = entidade.numcgm 
                          GROUP BY plano_conta.cod_estrutural 
                                         , sw_cgm.nom_cgm
                            ) as cc
                        ON cc.cod_estrutural = retorno.cod_estrutural
                        
               LEFT JOIN (
                            SELECT plano_conta.cod_estrutural 
                                      , sw_cgm.nom_cgm
                              FROM contabilidade.plano_conta
                      INNER JOIN contabilidade.plano_analitica
                                  ON plano_analitica.exercicio = plano_conta.exercicio
                                AND plano_analitica.cod_conta = plano_conta.cod_conta
                      INNER JOIN contabilidade.conta_debito
                                  ON conta_debito.cod_plano = plano_analitica.cod_plano
                                AND conta_debito.exercicio = plano_analitica.exercicio
                      INNER JOIN orcamento.entidade 
                                 ON entidade.cod_entidade = conta_debito.cod_entidade
                               AND entidade.exercicio = conta_debito.exercicio
                     INNER JOIN sw_cgm 
                                ON sw_cgm.numcgm = entidade.numcgm 
                     GROUP BY plano_conta.cod_estrutural 
                                  , sw_cgm.nom_cgm
                           ) as cd
                        ON cd.cod_estrutural = retorno.cod_estrutural
                  WHERE substr(retorno.cod_estrutural,1,9) >= '2.1.8' 
                      AND substr(retorno.cod_estrutural,1,9) <= '2.1.8.9.4' 


            ";
        return $stSql;
    }
    
    public function __destruct(){}

}   
?>
