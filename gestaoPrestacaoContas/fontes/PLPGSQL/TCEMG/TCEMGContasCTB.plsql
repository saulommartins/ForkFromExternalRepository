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
 */

/*

*/

CREATE OR REPLACE FUNCTION tcemg.contasCTB (varchar, varchar) RETURNS SETOF RECORD AS $$

DECLARE
    stExercicio         ALIAS FOR $1;
    stCodEntidades      ALIAS FOR $2;
    stSql               VARCHAR   := '';
    reRegistro          RECORD;

BEGIN
    -- ALTERACOES NO NUM_CONTA_CORRENTE, CONTA_CORRENTE E digito_verificador_conta_bancaria, conta, desc_conta_bancaria PARA ATENDER TICKET 22877 #34
    stSql := '
              CREATE TEMPORARY TABLE tmp_contas AS
              SELECT conta_bancaria.cod_conta
                   , ''''::VARCHAR AS cod_tipo_aplicacao
                   , CASE WHEN cod_ctb_anterior IS NULL
                          THEN plano_banco.cod_plano 
                          ELSE cod_ctb_anterior
                      END AS cod_ctb_anterior 
                   , conta_bancaria.exercicio
                   , CASE WHEN plano_banco.cod_plano = 3720 OR plano_banco.cod_plano = 3495
                          THEN banco.num_banco || agencia.num_agencia || ''1324106-0''
                          ELSE (banco.num_banco || agencia.num_agencia || plano_banco.conta_corrente)
                      END AS conta
                   , CASE WHEN LTRIM(replace(num_agencia,''-'',''''),''9'') = '''' AND num_banco = ''999''
                          THEN ''999999999999''
                          WHEN plano_banco.cod_plano = 3720 OR plano_banco.cod_plano = 3495
                          THEN ''1324106''
                          ELSE LTRIM( REPLACE(split_part(plano_banco.conta_corrente,''-'',1),''.'',''''),''0'') 
                      END as num_conta_corrente
                   , CASE WHEN plano_banco.cod_plano = 3720 OR plano_banco.cod_plano = 3495
                          THEN ''1324106-0''
                          ELSE REPLACE(plano_banco.conta_corrente,''.'','''')
                      END AS conta_corrente
                   , plano_banco.cod_entidade AS cod_orgao
                   , num_banco::VARCHAR as banco
                   , split_part(num_agencia,''-'',1) AS agencia
                   , SPLIT_PART(num_agencia,''-'',2) AS digito_verificador_agencia
                   , CASE WHEN plano_banco.cod_plano = 3720 OR plano_banco.cod_plano = 3495
                          THEN ''0''
                          ELSE SPLIT_PART(plano_banco.conta_corrente,''-'',2)
                      END AS digito_verificador_conta_bancaria
                   , CASE WHEN plano_banco.cod_plano = 3720 OR plano_banco.cod_plano = 3495 
                          THEN (''Banco:''||banco.num_banco || '' Agencia:'' ||agencia.num_agencia || '' Conta Corrente:'' || ''1324106-0'' )::VARCHAR
                          ELSE (''Banco:''||banco.num_banco || '' Agencia:'' ||agencia.num_agencia || '' Conta Corrente:'' || REPLACE(plano_banco.conta_corrente,''.'','''') )::VARCHAR
                      END AS desc_conta_bancaria
                   , plano_conta.cod_estrutural
                FROM tcemg.conta_bancaria 
          INNER JOIN contabilidade.plano_conta
                  ON plano_conta.cod_conta = conta_bancaria.cod_conta
                 AND plano_conta.exercicio = conta_bancaria.exercicio
          INNER JOIN contabilidade.plano_analitica
                  ON plano_conta.cod_conta = plano_analitica.cod_conta
                 AND plano_conta.exercicio = plano_analitica.exercicio
          INNER JOIN contabilidade.plano_banco
                  ON plano_analitica.cod_plano = plano_banco.cod_plano
                 AND plano_analitica.exercicio = plano_banco.exercicio
          INNER JOIN monetario.agencia
                  ON agencia.cod_banco = plano_banco.cod_banco
                 AND agencia.cod_agencia = plano_banco.cod_agencia 
          INNER JOIN monetario.banco
                  ON banco.cod_banco = plano_banco.cod_banco
               WHERE conta_bancaria.exercicio = ' || quote_literal(stExercicio) || '
                 AND conta_bancaria.cod_entidade = (' || stCodEntidades || ')
                ';
    EXECUTE stSql;
    
    ---TIPO DE CONTA: CONTA-CORRENTE
     stSql := ' CREATE TEMPORARY TABLE tmp_corrente AS 
                    SELECT DISTINCT cod_conta
                         , ''''::VARCHAR AS cod_tipo_aplicacao
                         , cod_ctb_anterior
                         , 1 AS tipo_conta
                         , exercicio
                         , conta
                         , num_conta_corrente
                         , conta_corrente
                         , cod_orgao
                         , banco
                         , agencia
                         , digito_verificador_agencia
                         , digito_verificador_conta_bancaria
                         , desc_conta_bancaria
                      FROM tmp_contas
                     WHERE cod_estrutural LIKE ''1.1.1.1.1.19%''
        ';
        EXECUTE stSql;
        
        
    ---TIPO DE CONTA: CONTAS UNICAS, QUE SAO CORRENTE E APLICACAO EM UMA UNICA CONTA
     stSql := 'CREATE TEMPORARY TABLE tmp_conta_unica AS
                    SELECT DISTINCT cod_conta_aplicacao AS cod_conta
                         , ''''::VARCHAR AS cod_tipo_aplicacao
                         , cod_ctb_anterior_aplicacao AS cod_ctb_anterior
                         , 1 AS tipo_conta
                         , exercicio_aplicacao AS exercicio
                         , conta
                         , num_conta_corrente
                         , conta_corrente
                         , cod_orgao
                         , banco
                         , agencia
                         , digito_verificador_agencia
                         , digito_verificador_conta_bancaria
                         , desc_conta_bancaria
                      FROM tmp_corrente as corrente
                INNER JOIN ( SELECT tmp_contas.cod_conta AS cod_conta_aplicacao
                                  , cod_ctb_anterior AS cod_ctb_anterior_aplicacao 
                                  , tmp_contas.exercicio AS exercicio_aplicacao
                               FROM tmp_contas
                              WHERE tmp_contas.cod_estrutural LIKE ''1.1.1.1.1.50%'' OR tmp_contas.cod_estrutural LIKE ''1.1.4%''
                        ) as aplicacao
                        ON corrente.exercicio = aplicacao.exercicio_aplicacao
                       AND corrente.cod_ctb_anterior = aplicacao.cod_ctb_anterior_aplicacao 
                  ORDER BY cod_ctb_anterior
            ';

        EXECUTE stSql;

 ---TIPO DE CONTA: APLICACAO
    stSql := 'CREATE TEMPORARY TABLE tmp_aplicacao AS
                    SELECT DISTINCT cod_conta
                         , ''''::VARCHAR AS cod_tipo_aplicacao
                         , cod_ctb_anterior
                         , 2 AS tipo_conta
                         , exercicio
                         , conta
                         , num_conta_corrente
                         , conta_corrente
                         , cod_orgao
                         , banco
                         , agencia
                         , digito_verificador_agencia
                         , digito_verificador_conta_bancaria
                         , desc_conta_bancaria
                      FROM tmp_contas                                
                     WHERE ( cod_estrutural LIKE ''1.1.1.1.1.50%'' OR cod_estrutural LIKE ''1.1.4%'' )
                   AND NOT EXISTS ( SELECT 1 
                                      FROM tmp_conta_unica
                                      WHERE tmp_conta_unica.cod_conta = tmp_contas.cod_conta
                                  )
                 UNION ALL
                    SELECT *
                      FROM tmp_conta_unica
                ';
    EXECUTE stSql;

stSql := 'SELECT *
            FROM tmp_corrente
       UNION ALL
          SELECT *
            FROM tmp_aplicacao
        ';
    
    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN NEXT reRegistro;
    END LOOP;

    DROP TABLE tmp_contas;
    DROP TABLE tmp_corrente;
    DROP TABLE tmp_aplicacao;
    DROP TABLE tmp_conta_unica;

    RETURN;
END;
$$ LANGUAGE 'plpgsql';