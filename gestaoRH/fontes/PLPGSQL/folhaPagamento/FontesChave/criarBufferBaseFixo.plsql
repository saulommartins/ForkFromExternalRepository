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
--    * @author Analista: 
--    * @author Desenvolvedor: 
--
--    * @package URBEM
--    * @subpackage
--
--    $Revision: 23095 $
--    $Name$
--    $Author: souzadl $
--    $Date: 2007-06-05 17:06:51 -0300 (Ter, 05 Jun 2007) $
--
--    * Casos de uso: uc-04.05.09
--    * Casos de uso: uc-04.05.10
--*/


CREATE OR REPLACE FUNCTION criarBufferBaseFixo(NUMERIC,NUMERIC) RETURNS NUMERIC AS '
DECLARE
    nuValor             ALIAS FOR $1;
    nuQuantidade        ALIAS FOR $2;
    nuValorEvento       NUMERIC;
    nuQuantidadeEvento  NUMERIC;
    stCodigoEvento      VARCHAR := '''';
    nuRetorno           NUMERIC := 0;
    inCodConfiguracao   INTEGER;
    stSql               VARCHAR;
    stBuffer            VARCHAR;

BEGIN
    inCodConfiguracao  := recuperarBufferInteiro(''inCodConfiguracao'');
    stCodigoEvento     := recuperarBufferTexto(''stCodigoEvento'');
    stBuffer := stCodigoEvento||inCodConfiguracao;

    IF countBufferNumerico(stBuffer||''ValorFixo'') = 0 THEN
       nuValorEvento := criarBufferNumerico(stBuffer||''ValorFixo'',nuValor);
    END IF;

    IF countBufferNumerico(stBuffer||''QuantidadeFixo'') = 0 THEN
       nuQuantidadeEvento := criarBufferNumerico(stBuffer||''QuantidadeFixo'',nuQuantidade);
    END IF;

    RETURN nuRetorno;
END;
' LANGUAGE 'plpgsql';
