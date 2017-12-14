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
Arquivo de mapeamento para a função que busca os dados de variação patrimonial
    * Data de Criação   : 15/10/2013

    * @author Analista      
    * @author Desenvolvedor Carolina Schwaab Marçal
    * @package URBEM
    * @subpackage

    $Id: $
*/

CREATE OR REPLACE FUNCTION tcemg.fn_passivo_perm(VARCHAR, VARCHAR, VARCHAR, VARCHAR) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio         ALIAS FOR $1;
    stCodEntidade       ALIAS FOR $2;
    stDtInicial         ALIAS FOR $3;
    stDtFinal           ALIAS FOR $4;
    stSql               VARCHAR := '';
    reRegistro          RECORD;

BEGIN 

CREATE TEMPORARY TABLE tmp_arquivo (
          mes                   INTEGER
        , codTipo               INTEGER
        , valorEmp              NUMERIC
        , valorTransConcedidas  NUMERIC
        , valorProvisaoRPPS     NUMERIC
    );

stSql := ' CREATE TEMPORARY TABLE tmp_passivo_perm AS
                SELECT cod_estrutural
                     , COALESCE(SUM(valor_lancamento.vl_lancamento),0.00) AS vl_lancamento
                     
                  FROM contabilidade.plano_conta      
          
            INNER JOIN contabilidade.plano_analitica
                    ON plano_conta.cod_conta = plano_analitica.cod_conta
                   AND plano_conta.exercicio = plano_analitica.exercicio
                 
            INNER JOIN contabilidade.conta_debito          
                    ON plano_analitica.cod_plano = conta_debito.cod_plano
                   AND plano_analitica.exercicio = conta_debito.exercicio
                 
            INNER JOIN contabilidade.valor_lancamento     
                    ON conta_debito.cod_lote     = valor_lancamento.cod_lote
                   AND conta_debito.tipo         = valor_lancamento.tipo
                   AND conta_debito.sequencia    = valor_lancamento.sequencia
                   AND conta_debito.exercicio    = valor_lancamento.exercicio
                   AND conta_debito.tipo_valor   = valor_lancamento.tipo_valor
                   AND conta_debito.cod_entidade = valor_lancamento.cod_entidade
            
            INNER JOIN contabilidade.lancamento
                    ON valor_lancamento.cod_lote     = lancamento.cod_lote
                   AND valor_lancamento.tipo         = lancamento.tipo
                   AND valor_lancamento.sequencia    = lancamento.sequencia
                   AND valor_lancamento.exercicio    = lancamento.exercicio
                   AND valor_lancamento.tipo         = lancamento.tipo
                   AND valor_lancamento.cod_entidade = lancamento.cod_entidade
            
            INNER JOIN contabilidade.lote
                    ON lancamento.cod_lote     = lote.cod_lote
                   AND lancamento.exercicio    = lote.exercicio
                   AND lancamento.tipo         = lote.tipo
                   AND lancamento.cod_entidade = lote.cod_entidade        
                   AND lancamento.tipo <> ''I''
                   
                 WHERE plano_conta.exercicio  = '|| quote_literal(stExercicio) || '
                   AND conta_debito.cod_entidade IN ( ' || stCodEntidade || ' )
                   AND plano_conta.indicador_superavit like ''p%''
                   
              GROUP BY cod_estrutural ';
              
EXECUTE stSql;

stSql := '
    INSERT INTO tmp_arquivo
        (  mes
         , valorEmp
         , valorTransConcedidas
         , valorProvisaoRPPS
        ) VALUES (
           ''12''
         , (
                SELECT tmp_passivo_perm.vl_lancamento
                  FROM tmp_passivo_perm
                 WHERE tmp_passivo_perm.cod_estrutural like  ''2.1.2%''
            )
          , (
                SELECT tmp_passivo_perm.vl_lancamento
                  FROM tmp_passivo_perm
                 WHERE tmp_passivo_perm.cod_estrutural like  ''3.5%''
            )
          , (
                SELECT tmp_passivo_perm.vl_lancamento
                  FROM tmp_passivo_perm
                 WHERE tmp_passivo_perm.cod_estrutural like  ''2.2.7.2%''
          )
        )';
        
EXECUTE stSql;

--lançamento a débito com sinal positivo, então será com o codtipo 01 - acréscimo)
--lançamento a débito com sinal negativo, então será com o codtipo 02 - redução)
stSql := '  SELECT  mes
                    , CASE WHEN SIGN(valorEmp) > 0.00 THEN
                            valorEmp
                        ELSE
                            0.00
                    END as valorEmp
                    , CASE WHEN SIGN(valorTransConcedidas) > 0 THEN
                            valorTransConcedidas
                        ELSE
                            0.00
                    END as valorTransConcedidas
                    , CASE WHEN SIGN(valorProvisaoRPPS) > 0 THEN
                            valorProvisaoRPPS
                        ELSE
                            0.00
                    END as valorProvisaoRPPS
                    , 1 as codTipo
            FROM tmp_arquivo

        UNION

            SELECT  mes
                    , CASE WHEN SIGN(valorEmp) < 0 THEN
                            valorEmp
                        ELSE
                            0.00
                    END as valorEmp
                    , CASE WHEN SIGN(valorTransConcedidas) < 0 THEN
                            valorTransConcedidas
                        ELSE
                            0.00
                    END as valorTransConcedidas
                    , CASE WHEN SIGN(valorProvisaoRPPS) < 0 THEN
                            valorProvisaoRPPS
                        ELSE
                            0.00
                    END as valorProvisaoRPPS
                    , 2 as codTipo
            FROM tmp_arquivo
 
        ';

FOR reRegistro IN EXECUTE stSql
LOOP
    RETURN NEXT reRegistro;
END LOOP;

DROP TABLE tmp_passivo_perm;
DROP TABLE tmp_arquivo;

RETURN;

END;
$$ LANGUAGE 'plpgsql';