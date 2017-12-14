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

CREATE OR REPLACE FUNCTION dirfPlanoSaude(VARCHAR,VARCHAR,VARCHAR,VARCHAR,INTEGER,VARCHAR) RETURNS SETOF colunasDirfPlanoSaude AS $$
DECLARE
    stEntidade                  ALIAS FOR $1;
    stTipoFiltro                ALIAS FOR $2;
    stValoresFiltro             ALIAS FOR $3;
    stExercicio                 ALIAS FOR $4;
    inCodEvento                 ALIAS FOR $5;
    stExercicioAnterior         ALIAS FOR $6;
    stSelect                    VARCHAR;
    rwPlanoSaude                colunasDirfPlanoSaude%ROWTYPE;
    vlPlanoSaudeServidor        NUMERIC := 0;
    vlPlanoSaude0               NUMERIC := 0;
    vlPlanoSaude1               NUMERIC := 0;
    vlPlanoSaude2               NUMERIC := 0;
    vlPlanoSaude3               NUMERIC := 0;
    reResultados                RECORD;
    reServidores                RECORD;
    rePeriodos                  RECORD;
BEGIN
    -- select recuperar todos os servidores
    stSelect := '
        SELECT
             cadastro.cod_contrato
            ,cadastro.registro
            ,cadastro.nom_cgm
            ,cadastro.numcgm
            ,cadastro.cpf
        FROM (
            SELECT
                 cod_contrato
                ,registro
                ,nom_cgm
                ,numcgm
                ,cpf
            FROM
                recuperarContratoServidor(''cgm'','|| quote_literal(stEntidade) ||',0,'|| quote_literal(stTipoFiltro) ||','|| quote_literal(stValoresFiltro) ||','|| quote_literal(stExercicio) ||')

            UNION

            SELECT
                 cod_contrato
                ,registro
                ,nom_cgm
                ,numcgm
                ,cpf
            FROM
                recuperarContratoPensionista(''cgm'','|| quote_literal(stEntidade) ||',0,'|| quote_literal(stTipoFiltro) ||','|| quote_literal(stValoresFiltro) ||','|| quote_literal(stExercicio) ||')
            ) as CADASTRO

        WHERE NOT EXISTS ( SELECT 1
                             FROM pessoal'|| stEntidade ||'.contrato_servidor_caso_causa
                            WHERE contrato_servidor_caso_causa.cod_contrato = cadastro.cod_contrato
                              AND to_char(dt_rescisao,''yyyy'') < ''|| inExercicio ||'')
                              AND NOT EXISTS ( SELECT 1
                                                 FROM pessoal'|| stEntidade ||'.aposentadoria
                                                WHERE aposentadoria.cod_contrato = cadastro.cod_contrato
                                                  AND NOT EXISTS ( SELECT 1
                                                                     FROM pessoal'|| stEntidade ||'.aposentadoria_excluida
                                                                    WHERE aposentadoria_excluida.cod_contrato = aposentadoria.cod_contrato
                                                                      AND aposentadoria_excluida.timestamp = aposentadoria.timestamp
                                                                 )
                                                  AND NOT EXISTS ( SELECT 1
                                                                     FROM pessoal'|| stEntidade ||'.aposentadoria_encerramento
                                                                    WHERE aposentadoria_encerramento.cod_contrato = aposentadoria.cod_contrato
                                                                      AND aposentadoria_encerramento.timestamp = aposentadoria.timestamp
                                                                 )
                                             )
                             AND NOT EXISTS ( SELECT 1
                                                FROM pessoal'|| stEntidade ||'.contrato_pensionista
                                               WHERE contrato_pensionista.cod_contrato = cadastro.cod_contrato
                                                 AND contrato_pensionista.dt_encerramento IS NOT NULL
                                            )

        ORDER BY cadastro.cod_contrato';

    -- para cada servidor calcula o evento em questão em todas as folhas (0 a 4)
    FOR reServidores IN EXECUTE stSelect
    LOOP
        -- recupera todos os períodos de movimentação no exercício
        stSelect := 'SELECT *
                       FROM folhapagamento'|| stEntidade ||'.periodo_movimentacao
                      WHERE to_char(dt_final,''yyyy'') = '|| quote_literal(stExercicio) ||' ';

                   IF stExercicioAnterior IS NOT NULL THEN
                       stSelect := stSelect || '   AND to_char(dt_final,''mm'') <> 12
                                     UNION
                                    SELECT *
                                      FROM folhapagamento'|| stEntidade ||'.periodo_movimentacao
                                     WHERE to_char(dt_final,''yyyy'') = '|| quote_literal(stExercicioAnterior) ||'
                                       AND to_char(dt_final,''mm'') = 12';
                   END IF;

                   stSelect := stSelect || 'ORDER BY dt_final';

        FOR rePeriodos IN EXECUTE stSelect
        LOOP
            vlPlanoSaude0 := vlPlanoSaude0 + (SELECT * from recupera_evento_calculado_dirf(0, rePeriodos.cod_periodo_movimentacao, reServidores.cod_contrato, inCodEvento, stEntidade));
            vlPlanoSaude1 := vlPlanoSaude1 + (SELECT * from recupera_evento_calculado_dirf(1, rePeriodos.cod_periodo_movimentacao, reServidores.cod_contrato, inCodEvento, stEntidade));
            vlPlanoSaude2 := vlPlanoSaude2 + (SELECT * from recupera_evento_calculado_dirf(2, rePeriodos.cod_periodo_movimentacao, reServidores.cod_contrato, inCodEvento, stEntidade));
            vlPlanoSaude3 := vlPlanoSaude3 + (SELECT * from recupera_evento_calculado_dirf(3, rePeriodos.cod_periodo_movimentacao, reServidores.cod_contrato, inCodEvento, stEntidade));
        END LOOP;

        vlPlanoSaudeServidor := vlPlanoSaude0 + vlPlanoSaude1 + vlPlanoSaude2 +  vlPlanoSaude3;


        rwPlanoSaude.registro     := reServidores.registro;
        rwPlanoSaude.cod_contrato := reServidores.cod_contrato;
        rwPlanoSaude.nom_cgm      := reServidores.nom_cgm;
        rwPlanoSaude.numcgm       := reServidores.numcgm;
        rwPlanoSaude.cpf          := reServidores.cpf;
        rwPlanoSaude.valor        := vlPlanoSaudeServidor;

        vlPlanoSaude0 := 0;
        vlPlanoSaude1 := 0;
        vlPlanoSaude2 := 0;
        vlPlanoSaude3 := 0;
        vlPlanoSaudeServidor := 0;

        RETURN NEXT rwPlanoSaude;

    END LOOP;
END;
$$ LANGUAGE 'plpgsql';

