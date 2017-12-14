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
--    * Data de Criação: 00/00/0000
--
--
--    * @author Analista: Vandré Miguel Ramos
--    * @author Desenvolvedor: Diego Lemos de Souza
--
--    * @package URBEM
--    * @subpackage
--
--    $Revision: 25871 $
--    $Name$
--    $Author: souzadl $
--    $Date: 2007-10-08 09:34:30 -0300 (Seg, 08 Out 2007) $
--
--    * Casos de uso: uc-04.05.09
--    * Casos de uso: uc-04.05.10
--*/

CREATE OR REPLACE FUNCTION pegaValorCalculado(VARCHAR,INTEGER) RETURNS NUMERIC AS $$
DECLARE
    stCodigoEvento      ALIAS FOR $1;
    inCodConfiguracao   ALIAS FOR $2;
    stSql               VARCHAR := '';
    crCursor            REFCURSOR;
    nuValorEvento       NUMERIC := 0.00;    
    inCountTabela       INTEGER := 0;
    inControle     INTEGER;
BEGIN
    inControle := recuperarBufferInteiro('inControle');
    IF inControle > 1 THEN
        nuValorEvento := pegaValorCalculadoFixo(stCodigoEvento,inCodConfiguracao);
    ELSE
        nuValorEvento := recuperarBufferNumerico(stCodigoEvento||inCodConfiguracao||'Valor');
    END IF;    
    IF nuValorEvento is not null THEN
        RETURN nuValorEvento;
    ELSE
        return 0.00;
    END IF;
END;
$$ LANGUAGE 'plpgsql';
