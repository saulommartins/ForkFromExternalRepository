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
   * Data de Criação: 10/04/2007


   * @author Analista: Dagiane
   * @author Desenvolvedor: Diego Lemos de Souza

   * @package URBEM
   * @subpackage

   $Revision: 1.1 $
   $Name:  $
   $Author: souzadl $
   $Date: 2007/05/30 21:01:56 $

   * Casos de uso: uc-04.05.18
*/

CREATE OR REPLACE FUNCTION deletarEventosDeSistemaRescisaoZerado(INTEGER,INTEGER) RETURNS BOOLEAN as $$
DECLARE
    inCodContrato               ALIAS FOR $1;
    inCodPeriodoMovimentacao    ALIAS FOR $2;
    stSql                       VARCHAR;
    boRetorno                   BOOLEAN := TRUE;
    reRegistro                  RECORD; 
    stEntidade                  VARCHAR;
BEGIN
    stEntidade := recuperarBufferTexto('stEntidade');

    stSql := 'SELECT registro_evento_rescisao.cod_registro
                 FROM folhapagamento'||stEntidade||'.evento_rescisao_calculado
                    , folhapagamento'||stEntidade||'.registro_evento_rescisao
                    , folhapagamento'||stEntidade||'.evento
                WHERE evento_rescisao_calculado.cod_evento         = registro_evento_rescisao.cod_evento
                  AND evento_rescisao_calculado.cod_registro       = registro_evento_rescisao.cod_registro
                  AND evento_rescisao_calculado.timestamp_registro = registro_evento_rescisao.timestamp
                  AND evento_rescisao_calculado.desdobramento      = registro_evento_rescisao.desdobramento
                  AND registro_evento_rescisao.cod_evento = evento.cod_evento
                  AND registro_evento_rescisao.cod_contrato = '||inCodContrato||'
                  AND registro_evento_rescisao.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                  AND evento.evento_sistema = ''t''
                  AND evento_rescisao_calculado.valor = 0.00';

    FOR reRegistro IN EXECUTE stSql
    LOOP
        IF reRegistro.cod_registro IS NOT NULL THEN 
            stSql := 'DELETE FROM folhapagamento'||stEntidade||'.evento_rescisao_calculado_dependente    WHERE cod_registro = '||reRegistro.cod_registro;
            EXECUTE stSql;
            stSql := 'DELETE FROM folhapagamento'||stEntidade||'.evento_rescisao_calculado               WHERE cod_registro = '||reRegistro.cod_registro;
            EXECUTE stSql;
            stSql := 'DELETE FROM folhapagamento'||stEntidade||'.log_erro_calculo_rescisao               WHERE cod_registro = '||reRegistro.cod_registro;
            EXECUTE stSql;
            stSql := 'DELETE FROM folhapagamento'||stEntidade||'.registro_evento_rescisao_parcela        WHERE cod_registro = '||reRegistro.cod_registro;
            EXECUTE stSql;
            stSql := 'DELETE FROM folhapagamento'||stEntidade||'.ultimo_registro_evento_rescisao         WHERE cod_registro = '||reRegistro.cod_registro;
            EXECUTE stSql;
        END IF;
    END LOOP;
    RETURN boRetorno; 
END;
$$ LANGUAGE 'plpgsql';
