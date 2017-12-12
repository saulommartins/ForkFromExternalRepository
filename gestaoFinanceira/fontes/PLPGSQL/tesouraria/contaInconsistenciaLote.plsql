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
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Revision: 22935 $
* $Name$
* $Author: domluc $
* $Date: 2007-05-29 11:19:13 -0300 (Ter, 29 Mai 2007) $
*
* Caso de uso: uc-02.04.33
* Caso de uso: uc-05.03.00
*/

/*
$Log$
Revision 1.1  2007/05/29 14:14:13  domluc
*** empty log message ***

Revision 1.2  2007/04/13 15:16:48  fabio
adicionado uc 05.03.00

Revision 1.1  2007/03/15 16:15:42  domluc
Caso de Uso 02.04.33

*/

CREATE OR REPLACE FUNCTION arrecadacao.contaInconsistenciaLote(integer,integer) returns integer as $$
declare
    inCodLote       ALIAS FOR $1;
    inExercicio     ALIAS FOR $2;
    inSoma          integer;
begin

    select count(1)
      into inSoma
      from arrecadacao.lote_inconsistencia
     where arrecadacao.lote_inconsistencia.cod_lote  = inCodLote 
       and arrecadacao.lote_inconsistencia.exercicio  = inExercicio::VARCHAR ;

   return inSoma;
end;
$$ language 'plpgsql';
