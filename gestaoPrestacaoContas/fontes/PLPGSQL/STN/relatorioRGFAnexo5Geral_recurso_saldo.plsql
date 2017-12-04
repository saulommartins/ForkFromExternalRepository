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
/*
* Script de função PLPGSQL
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
*
*/



CREATE OR REPLACE FUNCTION stn.pl_recurso_saldo ( varchar, varchar , varchar, varchar, varchar, varchar) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio         ALIAS FOR $1;
    data_ini            ALIAS FOR $2;
    data_fim            ALIAS FOR $3;
    stCondicao          ALIAS FOR $4;
    inCod_Entidade      ALIAS FOR $5;
    stRPPS              ALIAS FOR $6;
    reRegistro          RECORD;
    stRecurso           VARCHAR;
    stCondEntidades     VARCHAR := '';
    inCodEntidadeRPPS   INTEGER;
    stSql               VARCHAR := '';
    stSqlInsert         VARCHAR := '';
    
BEGIN

inCodEntidadeRPPS := selectintointeger('SELECT valor FROM administracao.configuracao where parametro = ''cod_entidade_rpps'' AND cod_modulo = 8 AND exercicio = ''' || stExercicio || ''' ');

IF  ( stRPPS = 'false' ) THEN

    stCondEntidades := ' and valor_lancamento.cod_entidade in ( ' || inCod_Entidade || ' )  and valor_lancamento.cod_entidade not in ( ' || inCodEntidadeRPPS || ' ) ';

ELSE

    stCondEntidades := ' and valor_lancamento.cod_entidade in ( ' || inCod_Entidade || ' )  and valor_lancamento.cod_entidade in ( ' || inCodEntidadeRPPS || ' ) ';

END IF; 

stSqlInsert := '           
        CREATE TEMPORARY TABLE tmp_recurso_lancamento_positivo AS
                SELECT DISTINCT
                     recurso_direto.cod_recurso
                    ,recurso_direto.exercicio
                    ,recurso_direto.nom_recurso
                    , COALESCE(sum ( valor_lancamento.vl_lancamento ),0.00) as valor_positivo
                    , '''' as a_pagar
                    ,recurso_direto.tipo as tipo_recurso
                    
               from contabilidade.plano_conta
                    join contabilidade.plano_analitica
                     on ( plano_conta.exercicio = plano_analitica.exercicio 
                    and   plano_conta.cod_conta = plano_analitica.cod_conta ) 
                    join contabilidade.conta_credito
                     on ( plano_analitica.exercicio = conta_credito.exercicio
                    and   plano_analitica.cod_plano = conta_credito.cod_plano )
                   join contabilidade.valor_lancamento 
                     on ( conta_credito.exercicio    = valor_lancamento.exercicio 
                    and   conta_credito.cod_entidade = valor_lancamento.cod_entidade 
                    and   conta_credito.tipo         = valor_lancamento.tipo         
                    and   conta_credito.cod_lote     = valor_lancamento.cod_lote     
                    and   conta_credito.sequencia    = valor_lancamento.sequencia    
                    and   conta_credito.tipo_valor   = valor_lancamento.tipo_valor )
                   join contabilidade.lote 
                     on ( valor_lancamento.exercicio    = lote.exercicio     
                    and   valor_lancamento.cod_entidade = lote.cod_entidade  
                    and   valor_lancamento.tipo         = lote.tipo          
                    and   valor_lancamento.cod_lote     = lote.cod_lote )
                   join contabilidade.lancamento
                     on ( lancamento.cod_lote       = valor_lancamento.cod_lote
                    and   lancamento.tipo           = valor_lancamento.tipo
                    and   lancamento.exercicio      = valor_lancamento.exercicio
                    and   lancamento.sequencia      = valor_lancamento.sequencia
                    and   lancamento.cod_entidade   = valor_lancamento.cod_entidade 
                    and   lancamento.cod_historico  not between 800 and 899 )
                    join contabilidade.plano_recurso
                     on ( plano_recurso.exercicio = plano_analitica.exercicio
                    and   plano_recurso.cod_plano = plano_analitica.cod_plano )             
                    left join orcamento.recurso_direto
                     on ( recurso_direto.exercicio = plano_recurso.exercicio
                    and   recurso_direto.cod_recurso = plano_recurso.cod_recurso )
                   
                    WHERE   plano_conta.exercicio = '''|| stExercicio || '''
                        '|| stCondicao ||'
                        and lote.dt_lote between to_date( '''|| data_ini ||''' , ''dd/mm/yyyy'' ) 
                                       and   to_date( '''|| data_fim ||''' , ''dd/mm/yyyy'' )
                        '|| stCondEntidades ||'
                        AND valor_lancamento.tipo <> ''I''
                        GROUP by recurso_direto.cod_recurso , recurso_direto.exercicio, nom_recurso , recurso_direto.tipo
                        ORDER BY nom_recurso ASC   
                    ';

EXECUTE stSqlInsert;

stSqlInsert :='';

stSqlInsert := '
        CREATE TEMPORARY TABLE tmp_recurso_lancamento_negativo AS
            SELECT DISTINCT
                recurso_direto.cod_recurso
                ,recurso_direto.exercicio
                ,recurso_direto.nom_recurso
                , COALESCE(sum ( valor_lancamento.vl_lancamento ),0.00) as valor_negativo
                , '''' as a_pagar
                ,recurso_direto.tipo as tipo_recurso
                
          
            from contabilidade.plano_conta plano_conta
                    join contabilidade.plano_analitica
                      on ( plano_conta.exercicio = plano_analitica.exercicio 
                     and   plano_conta.cod_conta = plano_analitica.cod_conta ) 
                    
                     join contabilidade.conta_debito
                      on ( plano_analitica.exercicio = conta_debito.exercicio
                     and   plano_analitica.cod_plano = conta_debito.cod_plano )
                    join contabilidade.valor_lancamento 
                      on ( conta_debito.exercicio    = valor_lancamento.exercicio 
                     and   conta_debito.cod_entidade = valor_lancamento.cod_entidade 
                     and   conta_debito.tipo         = valor_lancamento.tipo         
                     and   conta_debito.cod_lote     = valor_lancamento.cod_lote     
                     and   conta_debito.sequencia    = valor_lancamento.sequencia    
                     and   conta_debito.tipo_valor   = valor_lancamento.tipo_valor )
                    join contabilidade.lote 
                      on ( valor_lancamento.exercicio    = lote.exercicio     
                     and   valor_lancamento.cod_entidade = lote.cod_entidade  
                     and   valor_lancamento.tipo         = lote.tipo          
                     and   valor_lancamento.cod_lote     = lote.cod_lote )
                   join contabilidade.lancamento
                     on ( lancamento.cod_lote       = valor_lancamento.cod_lote
                    and   lancamento.tipo           = valor_lancamento.tipo
                    and   lancamento.sequencia      = valor_lancamento.sequencia
                    and   lancamento.exercicio      = valor_lancamento.exercicio
                    and   lancamento.cod_entidade   = valor_lancamento.cod_entidade 
                    and   lancamento.cod_historico  not between 800 and 899 )
                   join contabilidade.plano_recurso
                      on ( plano_recurso.exercicio = plano_analitica.exercicio
                     and   plano_recurso.cod_plano = plano_analitica.cod_plano )             
                   left join orcamento.recurso_direto
                      on ( recurso_direto.exercicio = plano_recurso.exercicio
                     and   recurso_direto.cod_recurso = plano_recurso.cod_recurso )
                
                WHERE  plano_conta.exercicio = '''|| stExercicio ||'''
                        '|| stCondicao ||'
                        and lote.dt_lote between to_date( '''|| data_ini||''' , ''dd/mm/yyyy'' ) 
                                       and   to_date( '''|| data_fim ||''' , ''dd/mm/yyyy'' )
                        '|| stCondEntidades ||'
                        AND valor_lancamento.tipo <> ''I''
                        GROUP by recurso_direto.cod_recurso , recurso_direto.exercicio, nom_recurso , recurso_direto.tipo
                        ORDER BY nom_recurso ASC
                ';

EXECUTE stSqlInsert;




stSql := '';
stSql := '
            SELECT
                            cod_recurso
                            ,exercicio
                            ,nom_recurso
                            ,tipo_recurso
                            ,SUM(valor_positivo) as valor_positivo
                            ,SUM(valor_negativo) as valor_negativo
                FROM(    
                    SELECT
                             tmp_recurso_lancamento_positivo.cod_recurso
                            ,tmp_recurso_lancamento_positivo.exercicio
                            ,tmp_recurso_lancamento_positivo.nom_recurso
                            ,tmp_recurso_lancamento_positivo.tipo_recurso
                            ,COALESCE( tmp_recurso_lancamento_positivo.valor_positivo,0.00) as valor_positivo
                            ,0 as valor_negativo
                    FROM      tmp_recurso_lancamento_positivo

                        UNION ALL

                    SELECT
                             tmp_recurso_lancamento_negativo.cod_recurso
                            ,tmp_recurso_lancamento_negativo.exercicio
                            ,tmp_recurso_lancamento_negativo.nom_recurso
                            ,tmp_recurso_lancamento_negativo.tipo_recurso
                            ,0 as valor_positivo
                            ,COALESCE( tmp_recurso_lancamento_negativo.valor_negativo,0.00) as valor_negativo
                    FROM      tmp_recurso_lancamento_negativo
                ) as saldos
                
                GROUP BY    cod_recurso
                            ,exercicio
                            ,nom_recurso
                            ,tipo_recurso
		
	  ';


FOR reRegistro IN EXECUTE stSql
LOOP
    RETURN NEXT reRegistro;
END LOOP;

DROP TABLE tmp_recurso_lancamento_positivo;
DROP TABLE tmp_recurso_lancamento_negativo;


END;
$$ LANGUAGE 'plpgsql';

