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
* $Id: buscaSituacaoImovel.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.03.05
*/

/*
$Log$
Revision 1.6  2006/11/21 18:44:21  cercato
bug #7529#

Revision 1.5  2006/09/15 10:19:52  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION imobiliario.fn_busca_situacao_imovel(INTEGER,DATE)  RETURNS varchar AS '
DECLARE
    inIM                        ALIAS FOR $1;
    dtData                      ALIAS FOR $2;
    stSituacao                  varchar;
    stDetalhe                   varchar;
    stInicio                    varchar;
    stTermino                   varchar;
    stDataB                     varchar; 
    stJustifica                 varchar; 
    inTeste                     integer;
    inVerificador             varchar;
BEGIN

--        select inscricao_municipal
--            , to_char(timestamp , ''dd/mm/YYYY'')
--             , to_char(dt_inicio , ''dd/mm/YYYY'')
--             , to_char(dt_termino, ''dd/mm/YYYY'') 
--             , justificativa
--          into inTeste, stDataB,stInicio,stTermino, stJustifica
--          from imobiliario.baixa_imovel 
--         where inscricao_municipal = inIM 
--           and dtData
--       between dt_inicio and dt_termino;

--    if (FOUND) then
--        stSituacao:= (''Baixado*-*''||stDataB||''*-*''||stInicio||''*-*''||coalesce(stTermino,''Indeterminado'')||''*-*''||stJustifica)::varchar;
--    else
--        stSituacao:= ''Ativo''::varchar;
--    end if;   

       
select 
     
     ( case when ( tabela.inscricao_municipal is not null ) and ( dtData >= tabela.dt_inicio ) then
            case when ( tabela.dt_termino is null ) or ( ( tabela.dt_termino is not null )  and ( dtData <= tabela.dt_termino ) )  then
                ''Baixado*-*''||tabela.stDataBase||''*-*''||tabela.dataInicio||''*-*''||coalesce(tabela.dataTermino,''Indeterminado'')||''*-*''||tabela.justificativa
            else
                ''Ativo''
            end
        else
            ''Ativo''
        end)::varchar as valor
      
INTO stSituacao
FROM
(        
        select 
               inscricao_municipal
             , to_char(timestamp , ''dd/mm/YYYY'') as stDataBase
             , to_char(dt_inicio , ''dd/mm/YYYY'')     as dataInicio
             , to_char(dt_termino, ''dd/mm/YYYY'' ) as dataTermino
             , dt_inicio
             , dt_termino
             , justificativa
         from imobiliario.baixa_imovel 
         where inscricao_municipal = inIM
) as tabela;

    if ( stSituacao is null ) then
        stSituacao := ''Ativo''::varchar;
    end if;
    
    return stSituacao;

end;
' LANGUAGE 'plpgsql';
