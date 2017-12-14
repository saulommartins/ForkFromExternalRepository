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
CREATE OR REPLACE FUNCTION tcepb.recuperaAcumulacaoContratoServidor(VARCHAR, INTEGER, INTEGER, INTEGER) RETURNS INTEGER AS $$
DECLARE
    stEntidade                      ALIAS FOR $1;
    inCodPeriodoMovimentacao        ALIAS FOR $2;
    inCodServidor                   ALIAS FOR $3;
    inCodContrato                   ALIAS FOR $4;
    stSql                           VARCHAR;
    reRegistro                      RECORD;
    crCursor                        REFCURSOR;
    inRetorno                       INTEGER := 0;
    stTimestampFechamentoPeriodo    VARCHAR;
BEGIN

    stTimestampFechamentoPeriodo := ultimoTimestampPeriodoMovimentacao(inCodPeriodoMovimentacao,stEntidade);

    stSql := '      SELECT * 
                      FROM (
                                SELECT servidor_contrato_servidor.cod_contrato, nextval(''sequenceRecuperaAcumulacaoContratoServidor'') as acumulacao
                                  FROM pessoal'||stEntidade||'.servidor_contrato_servidor
                            INNER JOIN pessoal'||stEntidade||'.servidor
                                    ON servidor.cod_servidor = servidor_contrato_servidor.cod_servidor
                                   AND servidor.cod_servidor = '||inCodServidor||'
                                 WHERE NOT EXISTS (  SELECT 1
                                                       FROM pessoal'||stEntidade||'.contrato_servidor_caso_causa
                                                      WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                                        AND contrato_servidor_caso_causa.cod_contrato = servidor_contrato_servidor.cod_contrato
                                                  )
                              ORDER BY servidor_contrato_servidor.cod_contrato
                           ) AS contratos_acumulacao
                  ORDER BY acumulacao DESC';

    CREATE TEMP SEQUENCE sequenceRecuperaAcumulacaoContratoServidor;
    OPEN crCursor FOR EXECUTE stSql;
    FETCH crCursor INTO reRegistro;

    IF reRegistro.acumulacao > 1 THEN
        WHILE FOUND LOOP

            IF reRegistro.cod_contrato = inCodContrato THEN
                inRetorno := reRegistro.acumulacao - 1;
                EXIT;
            END IF;

            FETCH crCursor INTO reRegistro;
        END LOOP;
    END IF;
    CLOSE crCursor;
    DROP SEQUENCE sequenceRecuperaAcumulacaoContratoServidor;

    RETURN inRetorno;
END;
$$ LANGUAGE 'plpgsql';

