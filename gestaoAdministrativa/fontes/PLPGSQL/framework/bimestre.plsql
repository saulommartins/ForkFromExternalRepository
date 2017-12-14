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
/*
* Script de função PLPGSQL
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Revision: 26695 $
* $Name$
* $Author: domluc $
* $Date: 2007-11-09 12:07:22 -0200 (Sex, 09 Nov 2007) $
*
* Casos de uso: uc-01.01.00
*/

/*
$Log$
Revision 1.2  2007/01/22 12:22:17  gris
-- Inclusão tag framework.

Revision 1.1  2006/08/09 18:12:26  rodrigo
*** empty log message ***

*/
CREATE OR REPLACE FUNCTION deleta_bimestre() RETURNS VOID AS $$
DECLARE
    stsql           varchar;
    varAchouArr     VARCHAR;
BEGIN

    SELECT proname
      INTO varAchouArr
      from pg_proc
     where proname ilike 'bimestre';

    IF FOUND THEN

        begin
          DROP FUNCTION publico.bimestre(integer,integer);
        exception
          when others then
        end;
        
        begin
          DROP FUNCTION publico.bimestre(varchar,integer);
        exception
          when others then
        end;

    END IF;

END
$$
language 'plpgsql';

SELECT        deleta_bimestre();
DROP FUNCTION deleta_bimestre();



CREATE OR REPLACE FUNCTION publico.bimestre(integer,integer) RETURNS VARCHAR[] AS $$
DECLARE
    inExercicioPar          ALIAS FOR $1;
    inBimestrePar           ALIAS FOR $2;
    inExercicio             INTEGER;
    inBimestre              INTEGER;
    stDataInicial           VARCHAR   := '''';
    stDataFinal             VARCHAR   := '''';
    arDatas                 VARCHAR[];
    reRegistro              RECORD;
BEGIN
    IF length(inExercicioPar::VARCHAR)=4 THEN
        inExercicio := inExercicioPar;
        inBimestre  := inBimestrePar;
    ELSE
        inExercicio := inBimestrePar;
        inBimestre  := inExercicioPar;
    END IF;

    IF inBimestre = 1 THEN
        arDatas[0] := '01/01/'||inExercicio;
        arDatas[1] := to_char((to_date('01/03/'||inExercicio,'dd/mm/yyyy')-1),'dd/mm/yyyy');
    ELSIF inBimestre = 2 THEN
        arDatas[0] := '01/03/'||inExercicio;
        arDatas[1] := '30/04/'||inExercicio;
    ELSIF inBimestre = 3 THEN
        arDatas[0] := '01/05/'||inExercicio;
        arDatas[1] := '30/06/'||inExercicio;
    ELSIF inBimestre = 4 THEN
        arDatas[0] := '01/07/'||inExercicio;
        arDatas[1] := '31/08/'||inExercicio;
    ELSIF inBimestre = 5 THEN
        arDatas[0] := '01/09/'||inExercicio;
        arDatas[1] := '31/10/'||inExercicio;
    ELSIF inBimestre = 6 THEN
        arDatas[0] := '01/11/'||inExercicio;
        arDatas[1] := '31/12/'||inExercicio;
    END IF;

    RETURN arDatas;
END;

$$ language plpgsql;


CREATE OR REPLACE FUNCTION publico.bimestre(varchar,integer) RETURNS VARCHAR[] AS $$
DECLARE
    inExercicioPar          ALIAS FOR $1;
    inBimestrePar           ALIAS FOR $2;
BEGIN
    RETURN publico.bimestre( CAST(inExercicioPar as INTEGER), inBimestrePar );
END;

$$ language plpgsql;
