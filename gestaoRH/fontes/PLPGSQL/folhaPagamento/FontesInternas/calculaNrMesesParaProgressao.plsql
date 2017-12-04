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

--
-- script de funcao PLSQL
-- 
-- URBEM Soluções de Gestão Pública Ltda
-- www.urbem.cnm.org.br
--
-- $Revision: 23095 $
-- $Name$
-- $Autor: Marcia $
-- Date: 2005/12/22 10:50:00 $
--
-- Caso de uso: uc-04.05.48
--
-- Objetivo: Recebe a data inicial para a contagem de progressao 
-- e a data final do periodo de competencia retornando o nr. de meses 
-- conforme esta hoje no sistema quando do calculo do campo de salario.
-- Nao considera o mes inicial e considera o mes final. 
-- ( 12 - mes inicial ) + (12 * anos integrais) + ( mes da data final )  
--



CREATE OR REPLACE FUNCTION calculaNrMesesParaProgressao(varchar,varchar) RETURNS integer as '

DECLARE
   stDataInicialProgressao         ALIAS FOR $1;
   stTimestampFinalCompetencia     ALIAS FOR $2;

   stDataFinalCompetencia          VARCHAR;

   inMesInicio                     INTEGER:=0;
   inAnoInicio                     INTEGER:=0;
   inMesFinal                      INTEGER:=0;
   inAnoFinal                      INTEGER:=0;

   inMesesAnoInicial               INTEGER := 0;
   inMesesAnosIntegrais            INTEGER := 0;
   inMesesAnoFinal                 INTEGER := 0;

   inNrMesesProgressao             INTEGER:=0;

BEGIN

   stDataFinalCompetencia := substr(stTimestampFinalCompetencia,1,10) ;
      
   inMesInicio := to_number(substr(stDataInicialProgressao,6,2),''99::VARCHAR'') ;
   inAnoInicio := to_number(substr(stDataInicialProgressao,1,4),''9999::VARCHAR'');
   inMesFinal  := to_number(substr(stDataFinalCompetencia,6,2),''99::VARCHAR'');
   inAnoFinal  := to_number(substr(stDataFinalCompetencia,1,4),''9999::VARCHAR'');

   IF ( inAnoInicio < inAnoFinal) THEN
      inMesesAnoInicial := (12 - inMesInicio);
      inMesesAnosIntegrais := ((inAnoFinal - inAnoInicio - 1)*12);
   END IF;
   inMesesAnoFinal := inMesFinal;

   inNrMesesProgressao := ( inMesesAnoInicial + InMesesAnosIntegrais + inMesesAnoFinal );

   RETURN inNrMesesProgressao;
END;

' LANGUAGE 'plpgsql';

