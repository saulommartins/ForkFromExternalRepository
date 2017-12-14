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
/**
    * Arquivo de mapeamento para a função que busca os dados dos serviços de terceiros
    * Data de Criação   : 16/01/2008


    * @author Analista      Tonismar Regis Bernardo
    * @author Desenvolvedor Eduardo Paculski Schitz
    
    * @package URBEM
    * @subpackage 

    $Id:$
*/

CREATE OR REPLACE FUNCTION tcemg.fn_serv_terceiros(VARCHAR, VARCHAR, INTEGER) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio         ALIAS FOR $1;
    stCodEntidade       ALIAS FOR $2;
    inMes               ALIAS FOR $3;
    stSql               VARCHAR := '';
    reRegistro          RECORD;

BEGIN

    CREATE TEMPORARY TABLE tmp_arquivo (
          mes         INTEGER
        , liquidado   NUMERIC(14,2)
    );

    stSql := '
    INSERT INTO tmp_arquivo(mes, liquidado) VALUES( ' || inMes || ', 
    (SELECT COALESCE(liquidado_total, 0.00) AS liquidado
      FROM (
         SELECT CAST(SUM(valor) AS NUMERIC) AS liquidado_total
           FROM ( SELECT SUM(nota_liquidacao_item.vl_total) - SUM(COALESCE(nota_liquidacao_item_anulado.vl_anulado, 0.00)) AS valor
                       , EXTRACT(month from nota_liquidacao.dt_liquidacao) AS mes
                    FROM empenho.empenho
                    JOIN empenho.nota_liquidacao
                      ON empenho.exercicio    = nota_liquidacao.exercicio_empenho
                     AND empenho.cod_entidade = nota_liquidacao.cod_entidade
                     AND empenho.cod_empenho  = nota_liquidacao.cod_empenho
                    JOIN empenho.nota_liquidacao_item
                      ON nota_liquidacao_item.exercicio    = nota_liquidacao.exercicio
                     AND nota_liquidacao_item.cod_nota     = nota_liquidacao.cod_nota
                     AND nota_liquidacao_item.cod_entidade = nota_liquidacao.cod_entidade
               LEFT JOIN empenho.nota_liquidacao_item_anulado
                      ON nota_liquidacao_item_anulado.exercicio       = nota_liquidacao_item.exercicio
                     AND nota_liquidacao_item_anulado.cod_nota        = nota_liquidacao_item.cod_nota
                     AND nota_liquidacao_item_anulado.num_item        = nota_liquidacao_item.num_item
                     AND nota_liquidacao_item_anulado.exercicio_item  = nota_liquidacao_item.exercicio_item
                     AND nota_liquidacao_item_anulado.cod_pre_empenho = nota_liquidacao_item.cod_pre_empenho
                     AND nota_liquidacao_item_anulado.cod_entidade    = nota_liquidacao_item.cod_entidade
                     AND EXTRACT(month from nota_liquidacao_item_anulado."timestamp") =  ' || inMes || '
                    JOIN empenho.pre_empenho
                      ON pre_empenho.exercicio       = empenho.exercicio
                     AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                    JOIN empenho.pre_empenho_despesa
                      ON pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
                     AND pre_empenho_despesa.exercicio = pre_empenho.exercicio
                    JOIN orcamento.conta_despesa
                      ON conta_despesa.cod_conta = pre_empenho_despesa.cod_conta
                     AND conta_despesa.exercicio = pre_empenho_despesa.exercicio
                   WHERE empenho.exercicio    = ''' || stExercicio || '''
                     AND empenho.cod_entidade IN ( ' || stCodEntidade || ' )
                     AND EXTRACT(month from nota_liquidacao.dt_liquidacao) = ' || inMes || '
                     AND ( cod_estrutural LIKE ''3.3.9.0.35%''
                        OR cod_estrutural LIKE ''3.3.9.0.37%''
                        OR cod_estrutural LIKE ''3.3.9.0.39%'')
                GROUP BY nota_liquidacao.dt_liquidacao
           ) AS total_liquidacao
       GROUP BY mes
          ) AS retorno))';

    EXECUTE stSql;

    stSql := ' SELECT mes, COALESCE(liquidado, 0.00) FROM tmp_arquivo; ';

    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN NEXT reRegistro;
    END LOOP;

    DROP TABLE tmp_arquivo;

    RETURN;

END;
$$ LANGUAGE 'plpgsql';                                                                  
