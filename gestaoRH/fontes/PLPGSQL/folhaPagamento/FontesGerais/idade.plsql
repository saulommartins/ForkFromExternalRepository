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
* script de funcao PLSQL
*
* URBEM Solugues de Gestco Pzblica Ltda
* www.urbem.cnm.org.br

* $Revision: 23095 $
* $Name$
* $Autor: Marcia $
* Date: 2006/05/23 10:50:00 $
*
* Caso de uso: uc-04.05.48
*
* Objetivo: retorna o nr.de anos entre 2 datas ( idade ). Somente comsidera 
* a troca de idade no mes seguinte ao da data inicial.
*/


CREATE OR REPLACE FUNCTION idade(varchar,varchar) RETURNS integer as '

DECLARE
    stDataInicial               ALIAS FOR $1;
    stDataFinal                 ALIAS FOR $2;

    inIdade                     INTEGER := 0;

    inAnoInicial                INTEGER := 0;
    inMesInicial                INTEGER := 0; 

    inAnoFinal                  INTEGER := 0;
    inMesFinal                  INTEGER := 0;

BEGIN

  inAnoInicial = to_number( substr(stDataInicial,1,4)::varchar, ''9999'' );
  inMesInicial = to_number( substr(stDataInicial,6,2)::varchar, ''99'' );

  inAnoFinal   = to_number( substr(stDataFinal,1,4)::varchar, ''9999'' );
  inMesFinal   = to_number( substr(stDataFinal,6,2)::varchar, ''99'' );

  IF inAnoInicial < inAnoFinal THEN
     inIdade := inIdade - 1;
     WHILE inAnoInicial <= inAnoFinal LOOP
        inIdade := inIdade + 1;
        inAnoInicial := inAnoInicial + 1;
     END LOOP;
     IF inMesInicial >= inMesFinal THEN
        inIdade := inIdade - 1;
     END IF;
  ELSE
     inIdade = 0;
  END IF;

  RETURN inIdade;

END;
' LANGUAGE 'plpgsql';
