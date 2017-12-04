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
--    * Data de Criação: 09/07/2007
--
--
--    * @author Desenvolvedor: Rafael Garbin
--
--    * Casos de uso: uc-04.05.68
--
--    $Id: pegaListaEventoDaBase.plsql
--*/

CREATE OR REPLACE FUNCTION  recuperaOcorrenciadoEventonaListadaBase(VARCHAR,VARCHAR) RETURNS BOOLEAN as $$ 
DECLARE
    stNomBase      ALIAS FOR $1;
    stCodigoEvento ALIAS FOR $2;
    stEvento       VARCHAR[];
    stTeste        VARCHAR;
    boRetorno      BOOLEAN := FALSE;
BEGIN

    stTeste := pegalistaeventosdabase(stNomBase);
    stEvento := string_to_array(stTeste,';');

    FOR inCount IN 1..array_upper(stEvento,1) LOOP
        IF stCodigoEvento = stEvento[inCount] THEN
            boRetorno := TRUE;
            EXIT;
        END IF;
    END LOOP;

    RETURN boRetorno;
END;
$$ LANGUAGE 'plpgsql';
