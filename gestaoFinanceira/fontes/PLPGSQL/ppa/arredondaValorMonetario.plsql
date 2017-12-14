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
    * Arredonda um valor monetário
    * Data de Criação: 03/08/2009


    * @author Analista:      Tonismar Regis Bernardo <tonismar.bernardo@cnm.org.br>
    * @author Desenvolvedor: Eduardo Paculski Schitz <eduardo.schitz@cnm.org.br>

    * @package      URBEM

    * $Id: $
*/

CREATE OR REPLACE FUNCTION ppa.arredondaValorMonetario(nuValor NUMERIC) RETURNS NUMERIC AS $$
DECLARE
    inCentavos  INTEGER;
    inCount     INTEGER;
    inParametro INTEGER;
    nuRetorno   NUMERIC;
BEGIN

    inCentavos  := SPLIT_PART(nuValor, '.', 2);
    inCount     := LENGTH(inCentavos);
    inParametro := LPAD('6', inCount, 5);

    nuRetorno   := FLOOR(nuValor);

    IF (inCentavos >= inParametro) THEN
        nuRetorno := FLOOR(nuValor) + 1;
    END IF;

    RETURN nuRetorno;

END;

$$ LANGUAGE 'plpgsql';
