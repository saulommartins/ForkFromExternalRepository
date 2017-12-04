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
   * Função PLSQL
   * Data de Criação: 06/09/2006


   * @author Analista: Vandré Miguel Ramos
   * @author Desenvolvedor: Vandré Miguel Ramos

   * @package URBEM
   * @subpackage

   $Revision: 23133 $
   $Name$
   $Author: souzadl $
   $Date: 2007-06-07 12:40:10 -0300 (Qui, 07 Jun 2007) $

   * Casos de uso: uc-04.05.19
*/

CREATE OR REPLACE FUNCTION deletarEventosDeSistemaFeriasZerado(INTEGER,INTEGER) RETURNS BOOLEAN as $$
DECLARE
    inCodContrato               ALIAS FOR $1;
    inCodPeriodoMovimentacao    ALIAS FOR $2;
    stSql                       VARCHAR;
    boRetorno                   BOOLEAN := TRUE;
    reRegistro                  RECORD; 
    stEntidade                  VARCHAR;
BEGIN
    stEntidade := recuperarBufferTexto('stEntidade');
    stSql := 'SELECT registro_evento_ferias.*
                 FROM folhapagamento'|| stEntidade ||'.evento_ferias_calculado
                    , folhapagamento'|| stEntidade ||'.registro_evento_ferias
                    , folhapagamento'|| stEntidade ||'.evento
                WHERE evento_ferias_calculado.cod_evento         = registro_evento_ferias.cod_evento
                  AND evento_ferias_calculado.cod_registro       = registro_evento_ferias.cod_registro
                  AND evento_ferias_calculado.timestamp_registro = registro_evento_ferias.timestamp
                  AND evento_ferias_calculado.desdobramento      = registro_evento_ferias.desdobramento
                  AND registro_evento_ferias.cod_evento = evento.cod_evento
                  AND registro_evento_ferias.cod_contrato = '|| inCodContrato ||'
                  AND registro_evento_ferias.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                  AND evento.evento_sistema = ''t''
                  AND evento_ferias_calculado.valor = 0.00';

    FOR reRegistro IN EXECUTE stSql
    LOOP
        IF reRegistro.cod_registro IS NOT NULL THEN 
            stSql := 'DELETE FROM folhapagamento'|| stEntidade ||'.evento_ferias_calculado_dependente WHERE cod_registro = '|| reRegistro.cod_registro ||'
                                                                                                      AND cod_evento = '|| reRegistro.cod_evento ||'
                                                                                                      AND desdobramento = '|| quote_literal(reRegistro.desdobramento) ||'
                                                                                                      AND timestamp_registro = '|| quote_literal(reRegistro.timestamp) ||' ';
            EXECUTE stSql;
            stSql := 'DELETE FROM folhapagamento'|| stEntidade ||'.evento_ferias_calculado        WHERE cod_registro = '|| reRegistro.cod_registro ||'
                                                                                                      AND cod_evento = '|| reRegistro.cod_evento ||'
                                                                                                      AND desdobramento = '|| quote_literal(reRegistro.desdobramento) ||'
                                                                                                      AND timestamp_registro = '|| quote_literal(reRegistro.timestamp) ||' ';
            EXECUTE stSql;
            stSql := 'DELETE FROM folhapagamento'|| stEntidade ||'.log_erro_calculo_ferias        WHERE cod_registro = '|| reRegistro.cod_registro ||'
                                                                                                      AND cod_evento = '|| reRegistro.cod_evento ||'
                                                                                                      AND desdobramento = '|| quote_literal(reRegistro.desdobramento) ||'
                                                                                                      AND timestamp = '|| quote_literal(reRegistro.timestamp) ||' ';
            EXECUTE stSql;
            stSql := 'DELETE FROM folhapagamento'|| stEntidade ||'.registro_evento_ferias_parcela WHERE cod_registro = '|| reRegistro.cod_registro ||'
                                                                                                      AND cod_evento = '|| reRegistro.cod_evento ||'
                                                                                                      AND desdobramento = '|| quote_literal(reRegistro.desdobramento) ||'
                                                                                                      AND timestamp = '|| quote_literal(reRegistro.timestamp) ||' ';
            EXECUTE stSql;
            stSql := 'DELETE FROM folhapagamento'|| stEntidade ||'.ultimo_registro_evento_ferias  WHERE cod_registro = '|| reRegistro.cod_registro ||'
                                                                                                      AND cod_evento = '|| reRegistro.cod_evento ||'
                                                                                                      AND desdobramento = '|| quote_literal(reRegistro.desdobramento) ||'
                                                                                                      AND timestamp = '|| quote_literal(reRegistro.timestamp) ||' ';
            EXECUTE stSql;
        END IF;
    END LOOP;
    RETURN boRetorno; 
END;
$$ LANGUAGE 'plpgsql';
