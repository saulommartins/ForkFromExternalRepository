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
* recuperarSituacaoDoContratoLiteral
* Data de Criacão   : 26/01/2009


* @author Analista      Dagiane Vieira
* @author Desenvolvedor Diego Lemos de Souza

* @package URBEM
* @subpackage

$Id:$
*/

-- A - Ativo
-- P - Aposentado
-- R - Rescindido
-- E - Pensionista

CREATE OR REPLACE FUNCTION recuperarSituacaoDoContratoLiteral(INTEGER,INTEGER,VARCHAR) RETURNS VARCHAR as $$
DECLARE
    inCodContrato               ALIAS FOR $1;
    inCodPeriodoMovimentacao    ALIAS FOR $2;
    stEntidade                  ALIAS FOR $3;
    stSituacao                  VARCHAR:='';
    stSql                       VARCHAR:='';
    stAfastamento               VARCHAR:='';
    rePeriodoMovimentacao       RECORD;
    crCursor                    REFCURSOR;
BEGIN
    stSituacao := recuperarSituacaoDoContrato(inCodContrato,inCodPeriodoMovimentacao,stEntidade);

    IF inCodPeriodoMovimentacao != 0 THEN
        stSql := 'SELECT *
                       , to_char(dt_final,''yyyy-mm'') as competencia
                    FROM folhapagamento'||stEntidade||'.periodo_movimentacao
                   WHERE cod_periodo_movimentacao = '||inCodPeriodoMovimentacao;
    ELSE
        stSql := '    SELECT *
                           , to_char(dt_final,''yyyy-mm'') as competencia
                        FROM folhapagamento'||stEntidade||'.periodo_movimentacao
                    ORDER BY cod_periodo_movimentacao
                  DESC LIMIT 1';
    END IF;
    OPEN crCursor FOR EXECUTE stSql;
        FETCH crCursor INTO rePeriodoMovimentacao;
    CLOSE crCursor;

    IF stSituacao = 'A' THEN
        stSituacao := 'Ativo';

        stSql := '    SELECT assentamento_motivo.descricao
                        FROM pessoal'||stEntidade||'.assentamento_gerado_contrato_servidor
                  INNER JOIN pessoal'||stEntidade||'.assentamento_gerado
                          ON assentamento_gerado.cod_assentamento_gerado = assentamento_gerado_contrato_servidor.cod_assentamento_gerado
                         AND ('||quote_literal(rePeriodoMovimentacao.competencia)||' BETWEEN to_char(assentamento_gerado.periodo_inicial,''yyyy-mm'') AND
                                                                     to_char(assentamento_gerado.periodo_final,''yyyy-mm''))
                  INNER JOIN pessoal'||stEntidade||'.assentamento_assentamento
                          ON assentamento_assentamento.cod_assentamento = assentamento_gerado.cod_assentamento
                  INNER JOIN pessoal'||stEntidade||'.assentamento_motivo
                          ON assentamento_motivo.cod_motivo = assentamento_assentamento.cod_motivo
                         AND assentamento_motivo.cod_motivo IN (3,5,6,7,9)
                       WHERE NOT EXISTS (SELECT 1
                                           FROM pessoal'||stEntidade||'.assentamento_gerado_excluido
                                          WHERE assentamento_gerado.cod_assentamento_gerado = assentamento_gerado_excluido.cod_assentamento_gerado
                                            AND assentamento_gerado.timestamp = assentamento_gerado_excluido.timestamp)
                         AND assentamento_gerado.timestamp = ( SELECT timestamp
                                                                 FROM pessoal'||stEntidade||'.assentamento_gerado as assentamento_gerado_interna
                                                                WHERE assentamento_gerado_interna.cod_assentamento_gerado = assentamento_gerado.cod_assentamento_gerado
                                                             ORDER BY timestamp DESC
                                                                LIMIT 1 )
                         AND assentamento_gerado_contrato_servidor.cod_contrato = '||inCodContrato;
        stAfastamento := selectIntoVarchar(stSql);
        IF stAfastamento IS NOT NULL THEN
            stSituacao := stSituacao ||'/Afastado (Motivo '||stAfastamento||')';
        END IF;
    END IF;

    IF stSituacao = 'P' THEN
        stSituacao := 'Aposentado';

        stSql := ' SELECT count(*) as total
                  FROM ( SELECT aposentadoria_encerramento.cod_contrato
                              , max(aposentadoria_encerramento.timestamp)
                           FROM pessoal'||stEntidade||'.aposentadoria_encerramento
                          WHERE aposentadoria_encerramento.cod_contrato = '||inCodContrato||'
                            AND dt_encerramento <= '||quote_literal(rePeriodoMovimentacao.dt_final)||'
                       GROUP BY aposentadoria_encerramento.cod_contrato
                         HAVING NOT EXISTS ( SELECT 1
                                               FROM pessoal'||stEntidade||'.aposentadoria_excluida
                                              WHERE aposentadoria_encerramento.cod_contrato = aposentadoria_excluida.cod_contrato
                                                AND max(aposentadoria_encerramento.timestamp) = aposentadoria_excluida.timestamp_aposentadoria )
                       ) as temp';
        
        IF selectintointeger(stSql) > 0 THEN
            stSituacao := stSituacao || ' Encerrado';
        END IF;
    END IF;

    IF stSituacao = 'R' THEN
        stSituacao := 'Rescindido';
    END IF;

    IF stSituacao = 'E' THEN
        stSituacao := 'Pensionista';

        stSql := 'SELECT coalesce(count(*),0) as total
                    FROM pessoal'||stEntidade||'.contrato_pensionista
                   WHERE cod_contrato = '||inCodContrato||'
                     AND COALESCE(dt_encerramento, ''9999-12-31'') < '||quote_literal(rePeriodoMovimentacao.dt_final)||'';

        IF selectintointeger(stSql) > 0 THEN
            stSituacao := stSituacao || ' Encerrado';
        END IF;
    END IF;

    RETURN stSituacao;
END;
$$ LANGUAGE 'plpgsql';
