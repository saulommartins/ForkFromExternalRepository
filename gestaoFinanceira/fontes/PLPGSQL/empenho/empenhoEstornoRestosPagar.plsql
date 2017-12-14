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
CREATE OR REPLACE FUNCTION public.empenhoestornorestosapagar(character varying, numeric, character varying, integer, character varying, integer, integer, character varying) returns integer as $$
DECLARE
    EXERCICIO ALIAS     FOR $1;
    VALOR ALIAS         FOR $2;
    COMPLEMENTO ALIAS   FOR $3;
    CODLOTE ALIAS       FOR $4;
    TIPOLOTE ALIAS      FOR $5;
    CODENTIDADE ALIAS   FOR $6;
    CODPREEMPENHO ALIAS FOR $7;
    EXERCRP ALIAS       FOR $8;
    SEQUENCIA       INTEGER;
    SEQUENCIAAUX    INTEGER;
BEGIN
    SEQUENCIA := FAZERLANCAMENTO('295100000000000', '195920000000000', 918, EXERCICIO, VALOR, COMPLEMENTO, CODLOTE, TIPOLOTE, CODENTIDADE);
    SEQUENCIA := FAZERLANCAMENTO('292410501000000', '292410509000000', 918, EXERCICIO, VALOR, COMPLEMENTO, CODLOTE, TIPOLOTE, CODENTIDADE);
    IF (EXERCICIO < 2009) THEN
        SEQUENCIA := FAZERLANCAMENTO('193290200000000', '293200000000000', 918, EXERCICIO, VALOR, COMPLEMENTO, CODLOTE, TIPOLOTE, CODENTIDADE);
    END IF;
    -- IF (EXERCICIO > 2013) THEN
    --    SEQUENCIA := FAZERLANCAMENTO('213110200000000', '464010000000000', 918, EXERCICIO, VALOR, COMPLEMENTO, CODLOTE, TIPOLOTE, CODENTIDADE);
    --    SEQUENCIA := FAZERLANCAMENTO('632100000000000', '632990000000000', 918, EXERCICIO, VALOR, COMPLEMENTO, CODLOTE, TIPOLOTE, CODENTIDADE);
    --    SEQUENCIA := FAZERLANCAMENTO('821130000000000', '821110000000000', 918, EXERCICIO, VALOR, COMPLEMENTO, CODLOTE, TIPOLOTE, CODENTIDADE);
    --END IF;
    SEQUENCIAAUX := EMPENHOESTORNORESTOSAPAGAREXERCICIO(EXERCICIO, VALOR, COMPLEMENTO, CODLOTE, TIPOLOTE, CODENTIDADE, CODPREEMPENHO, EXERCRP);

    RETURN SEQUENCIA;

END;

$$ language 'plpgsql';
