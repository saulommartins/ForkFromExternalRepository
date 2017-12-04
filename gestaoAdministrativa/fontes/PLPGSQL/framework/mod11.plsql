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
* $Revision: 3077 $
* $Name$
* $Author: pablo $
* $Date: 2005-11-29 14:53:37 -0200 (Ter, 29 Nov 2005) $
*
* Casos de uso: uc-01.01.00
*/
create or replace function publico.fn_mod11 (text) returns integer as '
declare
   iBase    integer;
   sValor      text;
   iSoma    integer;
   iPeso    integer;
   iDigito     integer;
   iTamanho integer;
   iValor      integer;
begin
   iBase    := 9;
   iSoma    := 0;
   iPeso    := 2;
   sValor   := trim(both '' '' from $1);
   iTamanho := length(sValor);
   while iTamanho > 0 loop
      iValor := to_number(substr(sValor, iTamanho, 1), ''9'')::integer;
      iSoma := iSoma + (iValor * iPeso);
      if iPeso < iBase then
         iPeso := iPeso + 1;
      else
         iPeso := 2;
      end if;
      iTamanho := iTamanho - 1;
   end loop;
   iDigito := 11 - (iSoma%11);
   if iDigito > 9 then
      iDigito := 0;
   end if;
   return iDigito;
end;
' language 'plpgsql';


