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
* $Id: fn_consulta_numeracao_parcela_tmp.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-5.3.19
* Caso de uso: uc-05.03.19
*/

/*
$Log$
Revision 1.8  2006/09/15 10:20:09  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION arrecadacao.fn_consulta_numeracao_parcela_tmp( integer , date )  RETURNS SETOF RECORD AS '
DECLARE
    inCodParcela    ALIAS FOR $1;
    dtDataBase      ALIAS FOR $2;
    stSql           VARCHAR;
    reRecord        RECORD;
BEGIN
    stSql := '' SELECT   
                    case when ap.numeracao is not null then
                        ap.numeracao::varchar
                    else
                        ac.numeracao::varchar
                    end as numeracao,
                    ac.exercicio::integer,
                    case
                            --when ap.numeracao is not null AND ap.data_pagamento  <= ''''''||dtDataBase||'''''' then
                            when ap.numeracao is not null then
                                case when ap.pagamento = true then
                                    case when arrecadacao.verificaPagUnica(''||inCodParcela||'',''''''||dtDataBase||'''''' ) and ( select nr_parcela from arrecadacao.parcela where cod_parcela = ''||inCodParcela||'') != 0 then
                                        ''''Pagamento Duplicado <hr> Pagamento de Cota Única ja efetuado''''::varchar
                                    else  
                                        case when ( select count(ac.numeracao) from arrecadacao.carne as ac 
                                                        INNER JOIN arrecadacao.pagamento as ap ON ap.numeracao = ac.numeracao
                                                        where ac.cod_parcela = ''||inCodParcela||'' 
                                                      ) > 1 then
                                        ''''Pagamento Duplicado''''
                                        else
                                            ap.nom_tipo
                                        end
                                    end
                                else
                                    ap.nom_tipo
                                end
                    else
                        case  when acd.numeracao is not null  AND acd.dt_devolucao <= ''''''||dtDataBase||'''''' then 
                                    ( select descricao from arrecadacao.motivo_devolucao amd where amd.cod_motivo = acd.cod_motivo )::varchar
                            when apr.cod_parcela is not null then
                                (
                                    select t.motivo || ''''<hr>Vencimento da Reemissão: '''' || t.dtVencR ||''''<br>Valor: ''''||t.valor  from
                                    (
                                    select
                                        (to_char(ap.vencimento,''''dd/mm/YYYY'''')) as dtVencR,
                                        apr.valor as ValorAntigo,
                                        ( select descricao from arrecadacao.motivo_devolucao amd 
                                        where amd.cod_motivo = 10 ) as motivo,
                                        ap.valor as valor
                                    from 
                                        arrecadacao.parcela as ap 
                                    where 
                                        ap.cod_parcela = ''||inCodParcela||''
                                    ) as t
                                )
                            else ''''Em Aberto''''::varchar
                            end 
                     end as situacao,  
                     case
                            --when ap.numeracao is not null AND ap.data_pagamento  <= ''''''||dtDataBase||'''''' then 
                            when ap.numeracao is not null then 
                                case when ap.pagamento = true then
                                    case when arrecadacao.verificaPagUnica(''||inCodParcela||'',''''''||dtDataBase||'''''' ) and ( select nr_parcela from arrecadacao.parcela where cod_parcela = ''||inCodParcela||'') != 0 then
                                          ''''Pago(!)''''::varchar
                                    else  
                                        case when ( select count(ac.numeracao) from arrecadacao.carne as ac 
                                                           INNER JOIN arrecadacao.pagamento as ap ON ap.numeracao = ac.numeracao
                                                            where ac.cod_parcela = ''||inCodParcela||'' 
                                                      ) > 1 then
                                               ( select nom_tipo||''''(!)'''' from arrecadacao.tipo_pagamento where cod_tipo = ap.cod_tipo )
                                        else
                                            ap.nom_tipo
                                        end
                                    end
                                else 
                                    ap.nom_resumido
                                end
                      else
                            case when acd.numeracao is not null  AND acd.dt_devolucao <= ''''''||dtDataBase||'''''' then 
                                    ( select descricao_resumida from arrecadacao.motivo_devolucao amd where amd.cod_motivo = acd.cod_motivo limit 1)::varchar
                            else 
                                case when apr.cod_parcela is not null then
                                        case
                                            when par.vencimento < ''''''||dtDataBase||'''''' then ''''Vencida(R)''''::varchar
                                            else ''''Em Aberto(R)''''::varchar
                                        end
                                else
                                        case
                                            when par.vencimento < ''''''||dtDataBase||'''''' then ''''Vencida''''::varchar
                                            else ''''Em Aberto''''::varchar
                                        end
                                end
                            end
                    end as situacao_resumida,
                      
                    acm.numeracao_migracao,
                    acm.prefixo,
                    case 
                        when apr.cod_parcela is not null then (to_char(apr.vencimento,''''dd/mm/YYYY''''))::varchar 
                        else (to_char(par.vencimento,''''dd/mm/YYYY''''))::varchar
                    end as vencimento_original,
                    ap.data_pagamento,
                    ap.ocorrencia_pagamento
                FROM
                    arrecadacao.carne ac
                    INNER JOIN arrecadacao.parcela as par 
                    ON par.cod_parcela = ac.cod_parcela
                    
                    LEFT JOIN 
                        ( select ap.*, atp.pagamento, atp.nom_resumido, atp.nom_tipo from 
                            arrecadacao.pagamento ap
                            INNER JOIN arrecadacao.tipo_pagamento as atp ON atp.cod_tipo = ap.cod_tipo
                            AND ap.numeracao in ( select numeracao from arrecadacao.carne as c, arrecadacao.parcela as p where c.cod_parcela = p.cod_parcela and p.cod_parcela = ''||inCodParcela||'' order by c.numeracao DESC limit 1 )
                        ) as ap ON ap.numeracao = ac.numeracao and ap.cod_convenio = ac.cod_convenio and ap.ocorrencia_pagamento = 1
                    
                    
                    LEFT JOIN arrecadacao.parcela_reemissao apr 
                    ON apr.cod_parcela = ac.cod_parcela
                    
                    LEFT JOIN arrecadacao.carne_devolucao acd 
                    ON acd.numeracao = ac.numeracao and acd.cod_convenio = ac.cod_convenio,
                    
                (select * from arrecadacao.carne where cod_parcela = ''||inCodParcela||'' order by timestamp limit 1) as cantes
                LEFT JOIN arrecadacao.carne_migracao acm 
                    ON acm.numeracao = cantes.numeracao and acm.cod_convenio =cantes.cod_convenio
                                    
                WHERE ac.cod_parcela= ''||inCodParcela||''
                
                order by 
                    ap.numeracao, ap.data_pagamento DESC, ac.timestamp desc
                    
                limit 1
                
        '';
    FOR reRecord IN EXECUTE stSql LOOP
        return next reRecord;
    END LOOP;

    RETURN ;
END;
' LANGUAGE 'plpgsql';
