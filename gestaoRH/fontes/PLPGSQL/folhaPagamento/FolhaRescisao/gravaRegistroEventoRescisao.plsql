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
--    * Data de Criação: 01/11/2006 
--
--
--    * @author Analista: Vandré Miguel Ramos
--
--    * @package URBEM
--    * @subpackage
--
--    $Revision: 23402 $
--    $Name$
--    $Author: souzadl $
--    $Date: 2007-06-20 16:57:16 -0300 (Qua, 20 Jun 2007) $
--
--    * Casos de uso: uc-04.05.18
--*/


CREATE OR REPLACE FUNCTION gravaRegistroEventoRescisao(integer,integer,integer,numeric,numeric,varchar) RETURNS BOOLEAN as $$

DECLARE

   inCodContrato                ALIAS FOR $1;
   inCodPeriodoMovimentacao     ALIAS FOR $2;
   inCodEvento                  ALIAS FOR $3;
   nuValorPar                   ALIAS FOR $4;
   nuQuantidadePar              ALIAS FOR $5;
   stDesdobramento              ALIAS FOR $6;

   nuValor                      NUMERIC:=0.00;
   nuQuantidade                 NUMERIC:=0.00;
   inContador                   INTEGER := 0;
   inCodRegistro                INTEGER := 0;
   stTimestamp                  TIMESTAMP := now();

   boRetorno                    BOOLEAN := TRUE;
   stSql                        VARCHAR := '';
   reRegistro                   RECORD;
   stEntidade VARCHAR := recuperarBufferTexto('stEntidade');
   
BEGIN

    inContador := selectIntoInteger('SELECT COUNT(ultimo_registro_evento_rescisao.*) AS contador
                 FROM folhapagamento'||stEntidade||'.ultimo_registro_evento_rescisao
                    , folhapagamento'||stEntidade||'.registro_evento_rescisao
                WHERE ultimo_registro_evento_rescisao.cod_registro      = registro_evento_rescisao.cod_registro
                  AND ultimo_registro_evento_rescisao.cod_evento        = registro_evento_rescisao.cod_evento
                  AND ultimo_registro_evento_rescisao.timestamp         = registro_evento_rescisao.timestamp
                  AND ultimo_registro_evento_rescisao.desdobramento     = registro_evento_rescisao.desdobramento
                  AND registro_evento_rescisao.cod_contrato             = '||inCodContrato||'
                  AND registro_evento_rescisao.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                  AND registro_evento_rescisao.cod_evento               = '||inCodEvento||'
                  AND registro_evento_rescisao.desdobramento            = '|| quote_literal(stDesdobramento) ||'');

    --IF inContador = 0 THEN
        stSql := 'SELECT cod_registro
                     FROM folhapagamento'||stEntidade||'.registro_evento_rescisao
                    WHERE cod_contrato             = '||inCodContrato||'
                      AND cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                      AND cod_evento               = '||inCodEvento||'
                      AND desdobramento            = '|| quote_literal(stDesdobramento) ||'';
                      
        FOR reRegistro IN EXECUTE stSql
        LOOP
            stSql := 'DELETE FROM folhapagamento'||stEntidade||'.registro_evento_rescisao_parcela WHERE cod_registro = '||reRegistro.cod_registro;
            EXECUTE stSql; 
            stSql := 'DELETE FROM folhapagamento'||stEntidade||'.evento_rescisao_calculado        WHERE cod_registro = '||reRegistro.cod_registro;
            EXECUTE stSql;
            stSql := 'DELETE FROM folhapagamento'||stEntidade||'.log_erro_calculo_rescisao        WHERE cod_registro = '||reRegistro.cod_registro;
            EXECUTE stSql;
            stSql := 'DELETE FROM folhapagamento'||stEntidade||'.ultimo_registro_evento_rescisao  WHERE cod_registro = '||reRegistro.cod_registro;
            EXECUTE stSql;
            stSql := 'DELETE FROM folhapagamento'||stEntidade||'.registro_evento_rescisao         WHERE cod_registro = '||reRegistro.cod_registro;
            EXECUTE stSql;
        END LOOP;
        
        inCodRegistro := selectIntoInteger(' SELECT COALESCE(max(cod_registro)+1,1) as cod_registro FROM folhapagamento'||stEntidade||'.registro_evento_rescisao');
        inContador := selectIntoInteger('SELECT count(*)
                                           FROM folhapagamento'||stEntidade||'.contrato_servidor_periodo
                                          WHERE cod_contrato = '||inCodContrato||'
                                            AND cod_periodo_movimentacao = '||inCodPeriodoMovimentacao );
        IF inContador = 0 THEN
            stSql := 'INSERT INTO folhapagamento'||stEntidade||'.contrato_servidor_periodo (cod_contrato,cod_periodo_movimentacao) VALUES ('||inCodContrato||','||inCodPeriodoMovimentacao||')';
            EXECUTE stSql;
        END IF;

    IF nuValorPar IS NOT NULL THEN
        nuValor := nuValorPar;
    END IF;
    
    IF nuQuantidadePar IS NOT NULL THEN
        nuQuantidade := nuQuantidadePar;
    END IF;
    
    stSql := ' INSERT into folhapagamento'||stEntidade||'.registro_evento_rescisao
                 ( cod_registro
                  ,timestamp
                  ,cod_evento
                  ,desdobramento
                  ,cod_contrato
                  ,cod_periodo_movimentacao
                  ,valor
                  ,quantidade
                  ,automatico
                 )
               VALUES 
                 (  '||inCodRegistro||'
                   , TO_TIMESTAMP('|| quote_literal(stTimestamp) ||', ''yyyy-mm-dd hh24:mi:ss.us'')
                   , '||inCodEvento||'
                   , '|| quote_literal(stDesdobramento) ||'
                   , '||inCodContrato||'
                   , '||inCodPeriodoMovimentacao||'
                   , '||nuValor||'
                   , '||nuQuantidade||'
                   , TRUE
                 )';
                 
       EXECUTE stSql;

      stSql := ' INSERT into folhapagamento'||stEntidade||'.ultimo_registro_evento_rescisao
                 ( cod_registro
                  ,timestamp
                  ,cod_evento
                  ,desdobramento
                 )
               VALUES
                 (  '||inCodRegistro||'
                   , TO_TIMESTAMP('|| quote_literal(stTimestamp) ||', ''yyyy-mm-dd hh24:mi:ss.us'')
                   , '||inCodEvento||'
                   , '|| quote_literal(stDesdobramento) ||'
                 )';

       EXECUTE stSql;



    --END IF;


   RETURN TRUE;

END;
$$ LANGUAGE 'plpgsql';


