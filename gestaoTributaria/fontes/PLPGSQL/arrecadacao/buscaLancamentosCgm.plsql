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
* Valor de um lancamento!
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id: buscaLancamentosCgm.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.03.00
*/

/*
$Log$
Revision 1.3  2007/02/21 19:48:57  dibueno
Bug #8416#

Revision 1.2  2006/09/15 10:20:09  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION arrecadacao.buscaLancamentosCgm(int,int,int) returns varchar as '
declare
    inNumCgm        ALIAS FOR $1;
    inNumCgm2       ALIAS FOR $2;
    inExercicio     ALIAS FOR $3;
    stRetorno       varchar := '''';
    reRecord        RECORD;
    inNumCgm22      integer;
    stSql           varchar := '''';
begin

if inNumCgm2 = 0 then
    inNumCgm22 := inNumCgm;
else
    inNumCgm22 := inNumCgm2;
end if;


stSql := ''
    select
        alc.cod_lancamento
    from
        arrecadacao.calculo_cgm cgm
        inner join arrecadacao.calculo ac
        on ac.cod_calculo = cgm.cod_calculo
        inner join arrecadacao.lancamento_calculo alc
        on alc.cod_calculo = ac.cod_calculo
    where
        ( cgm.numcgm between ''||inNumCgm||'' and ''||inNumCgm22||'' )
        and ac.exercicio = ''|| inExercicio
;


FOR reRecord IN EXECUTE stSql LOOP
    stRetorno := stRetorno||reRecord.cod_lancamento||'','';
END LOOP;

   return substring(stRetorno from 1 for char_length(stRetorno)-1);
end;
'language 'plpgsql';
