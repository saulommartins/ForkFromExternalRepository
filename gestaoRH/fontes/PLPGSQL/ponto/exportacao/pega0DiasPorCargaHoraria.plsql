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
/* pega0DiasPorCargaHoraria
 * 
 * Data de Criação   : 23/10/2008


 * @author Analista      Dagiane Vieira
 * @author Desenvolvedor Diego Lemos de Souza
 
 * @package URBEM
 * @subpackage 

 $Id:$
 */
CREATE OR REPLACE FUNCTION pega0DiasPorCargaHoraria(TIME,TIME) RETURNS INTEGER as $$
DECLARE
    stHorasProcessar        ALIAS FOR $1;
    stCargaHoraria          ALIAS FOR $2;
    nuCargaHoraria          NUMERIC;
    tmHoras                 TIME;
    arHoras                 VARCHAR[];
    inDias                  INTEGER;
DECLARE
BEGIN
    arHoras := string_to_array(stCargaHoraria::varchar,':');
    nuCargaHoraria := arHoras[1]::integer+round((arHoras[2]::integer/60.0),2);
    IF nuCargaHoraria = 0 THEN
        inDias := 0;
    ELSE
        tmHoras := stHorasProcessar/nuCargaHoraria;
        arHoras := string_to_array(tmHoras::varchar,':');
        inDias  := arHoras[1]::INTEGER;
    END IF;
    RETURN inDias;
END
$$ LANGUAGE 'plpgsql';
