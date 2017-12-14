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
 * Titulo do arquivo : processarNomeBufferPilha
 * Data de Criação   : 01/04/2008


 * @author Analista      Dagiane Vieira
 * @author Desenvolvedor Diego Lemos de Souza
 
 * @package URBEM
 * @subpackage 

 $Id:$
 */
create or replace function processarNomeBufferPilha(varchar,varchar,varchar) returns varchar
as $$
declare
    stBufferPar alias for $1;
    stTipo      alias for $2;
    stOperacao  alias for $3;
    stSql       varchar;
    stBuffer    varchar;
    inContador  integer;
    stSeparador varchar:='#';
begin
    stBuffer := processarNomeBuffer(stBufferPar);
    --Código para montagem de um buffer especial em forma de pilha
    --Esse buffer deverá se comportar como uma pinha, para isso quando for passado o nome do buffer aqui
    --especificado deverá ser acrescido de um número que identificara sua posição na pilha    
    stSql := 'select count(1) from administracao.buffers_'||stTipo||' where buffer = '''||stBuffer||'''';
    inContador := selectIntoInteger(stSql);    
    stSql := 'select count(1) from administracao.buffers_'||stTipo||' where buffer ilike '''||stBuffer||stSeparador||'%''';
    inContador := inContador + selectIntoInteger(stSql);
    --Se o contador for maior de 0 significa que ja existe um buffer e o próximo valor deverá entrar na pinha 
    --com o nome do buffer acrescido de um número.
    if inContador >= 1 then
        if stOperacao = 'add' then
            stBuffer := stBuffer||stSeparador||inContador;
        else            
            if inContador > 1 then
                stBuffer := stBuffer||stSeparador||(inContador-1);
            end if;
        end if;            
    end if;
    return stBuffer;
end;
$$ language 'plpgsql';
