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
--/**
--    * Função PLSQL
--    * Data de Criação: 03/07/2006
--
--
--    * @author Analista: Vandré Miguel Ramos
--    * @author Desenvolvedor: Diego Lemos de Souza
--
--    * @package URBEM
--    * @subpackage
--
--    $Revision: 23095 $
--    $Name$
--    $Author: souzadl $
--    $Date: 2007-06-05 17:06:51 -0300 (Ter, 05 Jun 2007) $
--
--    * Casos de uso: uc-04.05.10
--*/

CREATE OR REPLACE FUNCTION deletarBaseDeRegistroEventoComplementar(INTEGER,INTEGER,INTEGER) RETURNS BOOLEAN as '

DECLARE
    inCodContrato               ALIAS FOR $1;
    inCodPeriodoMovimentacao    ALIAS FOR $2;
    inCodComplementar           ALIAS FOR $3;
    stSql                       VARCHAR := '''';
    stSql2                      VARCHAR := '''';
    Registro                    RECORD;
    Registro2                   RECORD;
    inCodEventoBase             INTEGER;
    inContador                  INTEGER;
    boRetorno                   BOOLEAN;
    stEntidade VARCHAR := recuperarBufferTexto(''stEntidade'');
BEGIN
    stSql := ''SELECT ultimo_registro_evento_complementar.*
                 FROM folhapagamento''||stEntidade||''.ultimo_registro_evento_complementar
                    , folhapagamento''||stEntidade||''.registro_evento_complementar
                    , folhapagamento''||stEntidade||''.evento
                WHERE ultimo_registro_evento_complementar.cod_registro = registro_evento_complementar.cod_registro
                  AND ultimo_registro_evento_complementar.cod_evento = registro_evento_complementar.cod_evento
                  AND ultimo_registro_evento_complementar.cod_configuracao = registro_evento_complementar.cod_configuracao
                  AND ultimo_registro_evento_complementar.timestamp = registro_evento_complementar.timestamp
                  AND ultimo_registro_evento_complementar.cod_evento = evento.cod_evento
                  AND evento.evento_sistema = false
                  AND evento.natureza = ''''B''''
                  AND registro_evento_complementar.cod_contrato = ''||inCodContrato||''
                  AND registro_evento_complementar.cod_complementar = ''||inCodComplementar||''
                  AND registro_evento_complementar.cod_periodo_movimentacao = ''||inCodPeriodoMovimentacao||'''';
    FOR Registro IN EXECUTE stSql
    LOOP
        stSql := ''DELETE FROM folhapagamento''||stEntidade||''.evento_complementar_calculado WHERE cod_registro = ''||Registro.cod_registro||''
                                                                   AND cod_evento   = ''||Registro.cod_evento||''
                                                                   AND cod_configuracao = ''||Registro.cod_configuracao||''
                                                                   AND timestamp_registro    = ''''''||Registro.timestamp||'''''' '';
        EXECUTE stSql;
        stSql := ''DELETE FROM folhapagamento''||stEntidade||''.log_erro_calculo_complementar WHERE cod_registro = ''||Registro.cod_registro||''
                                                                   AND cod_evento   = ''||Registro.cod_evento||''
                                                                   AND cod_configuracao = ''||Registro.cod_configuracao||''
                                                                   AND timestamp    = ''''''||Registro.timestamp||'''''' '';
        EXECUTE stSql;
        stSql := ''DELETE FROM folhapagamento''||stEntidade||''.registro_evento_complementar_parcela WHERE cod_registro = ''||Registro.cod_registro||''
                                                                          AND cod_evento   = ''||Registro.cod_evento||''
                                                                          AND cod_configuracao = ''||Registro.cod_configuracao||''
                                                                          AND timestamp    = ''''''||Registro.timestamp||'''''' '';
        EXECUTE stSql;
        stSql := ''DELETE FROM folhapagamento''||stEntidade||''.ultimo_registro_evento_complementar WHERE cod_registro = ''||Registro.cod_registro||''
                                                                         AND cod_evento   = ''||Registro.cod_evento||''
                                                                         AND cod_configuracao = ''||Registro.cod_configuracao||''
                                                                         AND timestamp    = ''''''||Registro.timestamp||'''''' '';
        EXECUTE stSql;
    END LOOP;

    stSql := ''SELECT ultimo_registro_evento_complementar.*
                 FROM folhapagamento''||stEntidade||''.ultimo_registro_evento_complementar
                    , folhapagamento''||stEntidade||''.registro_evento_complementar
                    , folhapagamento''||stEntidade||''.evento
                WHERE ultimo_registro_evento_complementar.cod_registro = registro_evento_complementar.cod_registro
                  AND ultimo_registro_evento_complementar.cod_evento = registro_evento_complementar.cod_evento
                  AND ultimo_registro_evento_complementar.cod_configuracao = registro_evento_complementar.cod_configuracao
                  AND ultimo_registro_evento_complementar.timestamp = registro_evento_complementar.timestamp
                  AND ultimo_registro_evento_complementar.cod_evento = evento.cod_evento
                  AND evento.natureza != ''''B''''
                  AND registro_evento_complementar.cod_contrato = ''||inCodContrato||''
                  AND registro_evento_complementar.cod_complementar = ''||inCodComplementar||''
                  AND registro_evento_complementar.cod_periodo_movimentacao = ''||inCodPeriodoMovimentacao||'''';

    FOR Registro IN EXECUTE stSql
    LOOP
        stSql2 := ''SELECT evento_base.cod_evento_base
                         , evento_base.cod_configuracao_base
                      FROM folhapagamento''||stEntidade||''.configuracao_evento_caso
                         , (SELECT max(timestamp) as timestamp
                                 , cod_evento
                              FROM folhapagamento''||stEntidade||''.configuracao_evento_caso
                            GROUP BY cod_evento) as max_configuracao_evento_caso
                         , folhapagamento''||stEntidade||''.evento_base
                     WHERE configuracao_evento_caso.cod_evento = ''||Registro.cod_evento||''
                       AND configuracao_evento_caso.cod_configuracao = 1
                       AND configuracao_evento_caso.cod_configuracao = evento_base.cod_configuracao
                       AND configuracao_evento_caso.cod_evento =max_configuracao_evento_caso.cod_evento
                       AND configuracao_evento_caso.timestamp = max_configuracao_evento_caso.timestamp
                       AND configuracao_evento_caso.cod_evento = evento_base.cod_evento
                       AND configuracao_evento_caso.timestamp = evento_base.timestamp'';
        FOR Registro2 IN EXECUTE stSql2
        LOOP        
            IF Registro2.cod_evento_base IS NOT NULL THEN
                inContador := selectIntoInteger(''SELECT count(ultimo_registro_evento_complementar.*) as contador
                                          FROM folhapagamento''||stEntidade||''.ultimo_registro_evento_complementar
                                             , folhapagamento''||stEntidade||''.registro_evento_complementar
                                         WHERE ultimo_registro_evento_complementar.cod_registro = registro_evento_complementar.cod_registro
                                           AND ultimo_registro_evento_complementar.cod_evento = registro_evento_complementar.cod_evento
                                           AND ultimo_registro_evento_complementar.cod_configuracao = registro_evento_complementar.cod_configuracao
                                           AND ultimo_registro_evento_complementar.timestamp = registro_evento_complementar.timestamp
                                           AND registro_evento_complementar.cod_contrato = ''||inCodContrato||''
                                           AND registro_evento_complementar.cod_periodo_movimentacao = ''||inCodPeriodoMovimentacao||''
                                           AND registro_evento_complementar.cod_complementar = ''||inCodComplementar||''
                                           AND ultimo_registro_evento_complementar.cod_evento = ''||Registro2.cod_evento_base);
                IF inContador = 0 THEN
                    boRetorno := insertRegistroEventoAutomaticoComplementar(inCodContrato,inCodPeriodoMovimentacao,Registro2.cod_evento_base,inCodComplementar,Registro2.cod_configuracao_base);        
                END IF;
            END IF;
        END LOOP;
    END LOOP;
    RETURN true;
END;
'LANGUAGE 'plpgsql';
