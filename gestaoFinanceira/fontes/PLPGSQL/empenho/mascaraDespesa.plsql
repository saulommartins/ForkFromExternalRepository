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
* $Revision: 12203 $
* $Name$
* $Author: cleisson $
* $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $
*
* Casos de uso: uc-02.03.11
*/

/*
$Log$
Revision 1.6  2006/07/05 20:37:38  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION  empenho.fn_mascara_despesa( VARCHAR, VARCHAR)  RETURNS VARCHAR AS $$
DECLARE
   stExercicio      ALIAS FOR $1;
   stValor          ALIAS FOR $2;
   stValorr         varchar := '';
   stMascaraa       varchar := '';
   stValorTmp       varchar := '';
   chMascara        varchar := '';
   chValor          varchar := '';
   inCount          integer := 1;
   inCountMas       integer := 1;
   inCountVal       integer := 1;
   inCountElem      integer := 0;
   stMascRecurso    VARCHAR := '';
   stMascDespesa    VARCHAR := '';
   stExercicioo     integer := 0;


   stOut       varchar := '';
BEGIN
    stExercicioo = stExercicio;
        WHILE stMascDespesa is null or stMascDespesa='' LOOP
            SELECT INTO
                       stMascRecurso
                       valor
             FROM   tmp_mascara
            WHERE
                    parametro = 'masc_recurso'
              AND   exercicio = stExercicio;

            SELECT INTO
                       stMascDespesa
                       valor
             FROM   tmp_mascara
            WHERE
                    parametro = 'masc_despesa'
              AND   exercicio = stExercicio;

            IF stMascDespesa is null or stMascDespesa='' THEN
                IF inCount = 1 THEN
                    SELECT INTO stExercicioo max(exercicio) FROM tmp_mascara;
                ELSE
                    stExercicioo = stExercicioo - 1;
                END IF;
            ELSE
                stMascaraa = stMascDespesa || '.' || stMascRecurso || '.';
            END IF;

            inCount := inCount + 1;
        END LOOP;



    stValorr = stValor || '.';
   WHILE inCountVal <= length(stValorr) LOOP
      chValor = substr(stValorr,inCountVal,1);
        IF  chValor ~ '[0-9]' THEN
            stValorTmp = stValorTmp || chValor;
        ELSE
           WHILE inCountMas <= length(stMascaraa) LOOP
              chMascara = substr(stMascaraa,inCountMas,1);
                IF  chMascara ~ '[0-9]' THEN
                    inCountElem = inCountElem + 1;
                ELSE
                    inCountMas  = inCountMas + 1;
                    stOut = stOut || LPAD(stValorTmp,inCountElem,'0');
                    stOut = stOut ||chMascara;

                    stValorTmp = '';
                    inCountElem = 0;
                    EXIT;
                END IF;
                inCountMas  = inCountMas + 1;
            END LOOP;
        END IF;

        inCountVal = inCountVal + 1;
    END LOOP;
    stOut = substr(stOut,0,length(stOut));

   RETURN stOut;
END;

$$ language 'plpgsql';
