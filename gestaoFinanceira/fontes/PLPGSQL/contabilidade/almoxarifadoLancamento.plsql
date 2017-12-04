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
CREATE OR REPLACE FUNCTION contabilidade.almoxarifadoLancamento(INTEGER, VARCHAR, NUMERIC, VARCHAR, INTEGER, VARCHAR, INTEGER, VARCHAR) RETURNS INTEGER AS $$
DECLARE
    inCodContaDespesa     ALIAS FOR $1;
    stExercicio           ALIAS FOR $2;
    nuValor               ALIAS FOR $3;
    stComplemento         ALIAS FOR $4;
    inCodLote             ALIAS FOR $5;
    stTipoLote            ALIAS FOR $6;
    inCodEntidade         ALIAS FOR $7;
    boEstorno             ALIAS FOR $8;

    inSequencia INTEGER := 0;
    reContasAlmoxarifado RECORD;
    stSqlContasAlmoxarifado VARCHAR := '';
    inCodHistorico INTEGER := 0;
    
BEGIN
raise notice '%', boEstorno;

IF SUBSTRING(stComplemento,1,1) = 'D'
    THEN inCodHistorico := 961;
    ELSE inCodHistorico := 960;
    /*ELSE IF (TRIM(BOTH FROM (SUBSTRING(stComplemento,1,6)))) = 'Saída'
            THEN inCodHistorico := 960;
        END IF;*/
END IF;


    stSqlContasAlmoxarifado := '
                    SELECT plano_analitica_credito.cod_plano AS conta_credito
                         , plano_analitica_debito.cod_plano AS conta_debito
                         , configuracao_lancamento_debito.cod_conta_despesa
                         , REPLACE(plano_conta_debito.cod_estrutural, ''.'', '''') as estrutural_debito
                         , REPLACE(plano_conta_credito.cod_estrutural, ''.'', '''') as estrutural_credito
                      FROM contabilidade.configuracao_lancamento_credito
                INNER JOIN contabilidade.configuracao_lancamento_debito
                        ON configuracao_lancamento_credito.exercicio = configuracao_lancamento_debito.exercicio
                       AND configuracao_lancamento_credito.cod_conta_despesa = configuracao_lancamento_debito.cod_conta_despesa
                       AND configuracao_lancamento_credito.tipo = configuracao_lancamento_debito.tipo
                       AND configuracao_lancamento_credito.estorno = configuracao_lancamento_debito.estorno
                INNER JOIN contabilidade.plano_conta plano_conta_credito
                        ON plano_conta_credito.cod_conta = configuracao_lancamento_credito.cod_conta
                       AND plano_conta_credito.exercicio = configuracao_lancamento_credito.exercicio
                INNER JOIN contabilidade.plano_analitica plano_analitica_credito
                        ON plano_conta_credito.cod_conta = plano_analitica_credito.cod_conta
                       AND plano_conta_credito.exercicio = plano_analitica_credito.exercicio
                INNER JOIN contabilidade.plano_conta plano_conta_debito
                        ON plano_conta_debito.cod_conta = configuracao_lancamento_debito.cod_conta
                       AND plano_conta_debito.exercicio = configuracao_lancamento_debito.exercicio
                INNER JOIN contabilidade.plano_analitica plano_analitica_debito
                        ON plano_conta_debito.cod_conta = plano_analitica_debito.cod_conta
                       AND plano_conta_debito.exercicio = plano_analitica_debito.exercicio
                INNER JOIN orcamento.conta_despesa
                        ON configuracao_lancamento_credito.cod_conta_despesa = conta_despesa.cod_conta
                       AND configuracao_lancamento_credito.exercicio = conta_despesa.exercicio
                INNER JOIN contabilidade.configuracao_lancamento_conta_despesa_item
                        ON conta_despesa.cod_conta = configuracao_lancamento_conta_despesa_item.cod_conta_despesa
                       AND conta_despesa.exercicio = configuracao_lancamento_conta_despesa_item.exercicio
                     WHERE configuracao_lancamento_credito.estorno = '||quote_literal(boEstorno)||'
                       AND configuracao_lancamento_credito.exercicio = '||quote_literal(stExercicio)||'
                       AND configuracao_lancamento_credito.tipo = '||quote_literal( 'almoxarifado' )||'
                       AND configuracao_lancamento_conta_despesa_item.cod_conta_despesa = '||inCodContaDespesa||'
                  GROUP BY plano_analitica_credito.cod_plano
                         , plano_analitica_debito.cod_plano
                         , configuracao_lancamento_debito.cod_conta_despesa
                         , plano_conta_debito.cod_estrutural
                         , plano_conta_credito.cod_estrutural
    ';

    FOR reContasAlmoxarifado IN EXECUTE stSqlContasAlmoxarifado
    LOOP
        inSequencia := fazerLancamento( reContasAlmoxarifado.estrutural_debito , reContasAlmoxarifado.estrutural_credito , inCodHistorico , stExercicio , nuValor , stComplemento , inCodLote , stTipoLote , inCodEntidade, reContasAlmoxarifado.conta_debito, reContasAlmoxarifado.conta_credito );
    END LOOP;

    RETURN inSequencia;
END;
$$ language 'plpgsql';
