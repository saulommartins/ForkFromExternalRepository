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
--    * Data de Criação: 19/07/2006
--
--
--    * @package URBEM
--    * @subpackage
--
--    $Revision: 23133 $
--    $Name$
--    $Author: souzadl $
--    $Date: 2007-06-07 12:40:10 -0300 (Qui, 07 Jun 2007) $
--
--    * Casos de uso: uc-04.05.48
--    * Casos de uso: uc-04.05.19
--*/



CREATE OR REPLACE FUNCTION pegaValorCalculadoFerias(VARCHAR,VARCHAR,INTEGER) RETURNS NUMERIC AS $$
DECLARE
    stCodigoEvento      ALIAS FOR $1;
    stDesdobramento     ALIAS FOR $2;
    inCodConfiguracao   ALIAS FOR $3;
    stSql               VARCHAR := '';
    crCursor            REFCURSOR;
    nuValorEvento       NUMERIC := 0.00;    
    inCountTabela       INTEGER := 0;
    inCountControle     INTEGER;
BEGIN

--     SELECT INTO inCountControle (SELECT COUNT(*) FROM tmp#inControle);
-- 
--     IF inCountControle > 1 THEN
--        nuValorEvento      := pegaValorCalculadoFixo(stCodigoEvento,inCodConfiguracao);
--     ELSE
--        stSql := 'SELECT count(*)  
--                     FROM pg_tables
--                    WHERE tablename = LOWER(''tmp#'|| stCodigoEvento|| stDesdobramento|| inCodConfiguracao ||'valor'')     ' ;
--        OPEN crCursor FOR EXECUTE stSql;
--            FETCH crCursor INTO inCountTabela;
--        CLOSE crCursor;
--        IF inCountTabela > 0 THEN     
    nuValorEvento      := recuperarBufferNumerico(stCodigoEvento||stDesdobramento||inCodConfiguracao||'Valor');
--        END IF;
--     END IF;
    IF nuValorEvento is not null THEN
        RETURN nuValorEvento;
    ELSE
        return 0.00;
    END IF;
END;
$$ LANGUAGE 'plpgsql';
