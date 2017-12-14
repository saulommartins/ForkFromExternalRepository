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
* $Id: fn_consulta_numeracao_parcela.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-5.3.19
* Caso de uso: uc-05.03.19
*/

/*
$Log$
Revision 1.32  2007/03/12 21:25:18  dibueno
*** empty log message ***

Revision 1.31  2007/03/06 12:53:53  dibueno
Bug #8432#
Modificações referentes à consulta

Revision 1.30  2007/02/05 11:06:50  dibueno
Melhorias da consulta da arrecadacao

Revision 1.29  2006/11/21 16:09:37  dibueno
Melhoria no SQL.
Caso parcela unica e vencida, exibe "CANCELADA"

Revision 1.28  2006/11/06 16:35:25  dibueno
Bug #7351#

Revision 1.27  2006/10/25 18:17:24  dibueno
adição de Função para buscar valor correto da parcela, caso seja consolidada/reemitida

Revision 1.26  2006/10/25 11:22:10  dibueno
Exibição do valor da consolidação

Revision 1.25  2006/10/24 18:44:53  dibueno
Alterações para consulta da consolidação

Revision 1.24  2006/09/15 10:20:09  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION arrecadacao.fn_consulta_numeracao_parcela ( integer , date )  RETURNS SETOF RECORD AS '
DECLARE
    inCodParcela    ALIAS FOR $1;
    dtDataBase1      ALIAS FOR $2;
    dtDataBase      date;
    stSql           VARCHAR;
    reRecord        RECORD;
    stNumeracao     VARCHAR;
BEGIN

        SELECT numeracao, data_pagamento into stNumeracao, dtDataBase
          FROM arrecadacao.carne
         INNER JOIN(SELECT max(timestamp)as timestamp
                         , data_pagamento
                      FROM arrecadacao.carne
                     INNER JOIN arrecadacao.pagamento
                     USING(numeracao)
                     WHERE cod_parcela = inCodParcela
                     GROUP BY data_pagamento )as temp using(TIMESTAMP);
    IF dtDataBase IS NULL THEN
       dtDataBase := dtDataBase1;
    end if; 
    stSql := ''
	SELECT
		case when ap.numeracao is not null then
			ap.numeracao::varchar
		else
			case when parcela_paga_reemissao.consultacarnepagoreemissao is not null then
				parcela_paga_reemissao.consultacarnepagoreemissao::varchar
			else
				ac.numeracao::varchar
			end
		end as numeracao,
		ac.exercicio::varchar,

		case when ap.numeracao is not null then
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
						case when accon.numeracao is not null then
							ap.nom_tipo||'''' (C)''''
						else
							ap.nom_tipo
						end
					end
				end
			else
				case when accon.numeracao is not null then
					ap.nom_tipo||'''' (C)''''
				else
					ap.nom_tipo
				end
			end
		else
			case  when acd.numeracao is not null  AND acd.dt_devolucao <= ''''''||dtDataBase||'''''' then
				( select descricao from arrecadacao.motivo_devolucao amd where amd.cod_motivo = acd.cod_motivo )::varchar
			else
				case when ( parcela_paga_reemissao.consultacarnepagoreemissao  is not null ) then
					(   select atp2.nom_tipo
						from arrecadacao.tipo_pagamento as atp2
						INNER JOIN arrecadacao.pagamento as apag2 ON apag2.cod_tipo = atp2.cod_tipo
						where apag2.numeracao = parcela_paga_reemissao.consultacarnepagoreemissao
--						order by apag2.ocorrencia_pagamento DESC
						limit 1
						)::varchar
				else
					case when apr.cod_parcela is not null and accon.numeracao is null then
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
					else
						case when accon.numeracao is not null then
							''''Em Aberto - (Consolidada)<hr>Numeração:
							''''||accon.numeracao_consolidacao||
							''''<br>Valor: R$ ''''||par.valor||
							''''<br>Vencimento Consolidação: ''''|| to_char(par.vencimento,''''dd/mm/YYYY'''') ::varchar
						else
                            case when par.nr_parcela = 0 and baixa_manual_unica.valor = ''''nao'''' then
                                ''''Parcela Única Vencida''''::varchar
                            else
                                ''''Em Aberto''''::varchar
                            end
						end
					end
				end
			end
		end as situacao,

		case when ap.numeracao is not null then

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
				case when ( parcela_paga_reemissao.consultacarnepagoreemissao  is not null ) then
					(   select atp2.nom_resumido
						from arrecadacao.tipo_pagamento as atp2
						INNER JOIN arrecadacao.pagamento as apag2 ON apag2.cod_tipo = atp2.cod_tipo
						where apag2.numeracao = parcela_paga_reemissao.consultacarnepagoreemissao
						order by apag2.ocorrencia_pagamento DESC
						limit 1
						)::varchar
				else
					case when apr.cod_parcela is not null and accon.numeracao is null then
						case
							when par.vencimento < ''''''||dtDataBase||'''''' then ''''Vencida(R)''''::varchar
							else ''''Em Aberto(R)''''::varchar
						end
					else
						case when par.vencimento < ''''''||dtDataBase||'''''' then
							case when accon.numeracao is not null then
								''''Vencida (C)''''::varchar
							else
                                case when par.nr_parcela = 0 and baixa_manual_unica.valor = ''''nao'''' then
                                    ''''Cancelada''''::varchar
                                else
                                    ''''Vencida''''::varchar
                                end
							end
						else
							case when accon.numeracao is not null then
								''''Em Aberto (C)''''::varchar
							else
								''''Em Aberto''''::varchar
							end
						end
					end
				end
			end
		end as situacao_resumida,

		acm.numeracao_migracao,
		acm.prefixo,
		(case when apr.cod_parcela is not null then
			(to_char( arrecadacao.fn_atualiza_data_vencimento(apr.vencimento),''''dd/mm/YYYY''''))::varchar
		else
			(to_char( arrecadacao.fn_atualiza_data_vencimento(par.vencimento),''''dd/mm/YYYY''''))::varchar
		end) as vencimento_original,

		( case when parcela_paga_reemissao.consultacarnepagoreemissao is not null then
			( select 
                data_pagamento 
              from 
                arrecadacao.pagamento 
              where 
                numeracao = parcela_paga_reemissao.consultacarnepagoreemissao 
              order by
                ocorrencia_pagamento desc
              limit 1 
            )
		  else
            ap.data_pagamento
		  end
		) as data_pagamento,

		( case when parcela_paga_reemissao.consultacarnepagoreemissao is not null then
		      ( select ocorrencia_pagamento from arrecadacao.pagamento where numeracao = parcela_paga_reemissao.consultacarnepagoreemissao order by ocorrencia_pagamento desc limit 1)
		  else
              ap.ocorrencia_pagamento
		  end
		) as ocorrencia_pagamento

	FROM
		arrecadacao.carne ac

        LEFT JOIN  (
            select
                exercicio
                , valor
            from
                administracao.configuracao
            where parametro = ''''baixa_manual_unica''''
        ) as baixa_manual_unica
        ON baixa_manual_unica.exercicio = ac.exercicio

		INNER JOIN (
			SELECT
				par2.cod_parcela,
				par2.cod_lancamento,
				par2.valor,
				par2.nr_parcela,
				arrecadacao.fn_atualiza_data_vencimento(par2.vencimento) as vencimento
			FROM
				(
					select * from arrecadacao.parcela as par
					where cod_parcela = ''||inCodParcela||''
				) as par2
		) as par  ON par.cod_parcela = ac.cod_parcela

		LEFT JOIN                                                                                
        (                                                                                        
            select apr.cod_parcela, vencimento, valor                                            
            from arrecadacao.parcela_reemissao apr                                                
            inner join (                                                                        
                select cod_parcela, min(timestamp) as timestamp                                    
                from arrecadacao.parcela_reemissao                                                
                group by cod_parcela
				limit 1                                                             
                ) as apr2                                                                        
                ON apr2.cod_parcela = apr.cod_parcela AND                                        
                apr2.timestamp = apr.timestamp
				limit 1                                                    
            ) as apr                                                                            
        ON apr.cod_parcela = par.cod_parcela

		LEFT JOIN (
            select
                numeracao
                , cod_convenio
                , numeracao_consolidacao
			from
                arrecadacao.carne_consolidacao
			order by
                numeracao_consolidacao DESC
			limit 1
		) as accon
        ON accon.numeracao = ac.numeracao
        AND accon.cod_convenio = ac.cod_convenio

		LEFT JOIN (
            select
                ap.*
                , atp.pagamento
                , atp.nom_resumido
                , atp.nom_tipo
            from
				arrecadacao.pagamento ap
				INNER JOIN arrecadacao.tipo_pagamento as atp ON atp.cod_tipo = ap.cod_tipo
				AND ap.numeracao in (
                    select
                        numeracao
                    from
                        arrecadacao.carne as c
                        INNER JOIN arrecadacao.parcela as p
                        ON c.cod_parcela = p.cod_parcela
                    where
                        p.cod_parcela = ''||inCodParcela||''
                    order by
                        c.numeracao DESC
                    --limit 1 --comentado dia 09_06_2008
                )
                order by ap.ocorrencia_pagamento desc 
			limit 1
		) as ap
        ON ap.numeracao = ac.numeracao
        AND ap.cod_convenio = ac.cod_convenio


		LEFT JOIN arrecadacao.carne_devolucao acd
		ON acd.numeracao = ac.numeracao and acd.cod_convenio = ac.cod_convenio,

		( 	select * from arrecadacao.carne where cod_parcela = ''||inCodParcela||''
			order by timestamp limit 1 ) as cantes

		LEFT JOIN arrecadacao.carne_migracao acm
			ON acm.numeracao = cantes.numeracao and acm.cod_convenio =cantes.cod_convenio

		, ( select coalesce (arrecadacao.consultaCarnePagoReemissao(  ''||inCodParcela||'' ) , null) as consultacarnepagoreemissao ) as parcela_paga_reemissao


	WHERE par.cod_parcela = ''||inCodParcela||''

	order by
		ap.numeracao, ap.data_pagamento DESC, ap.ocorrencia_pagamento DESC, ac.timestamp DESC , acd.timestamp desc

	limit 1

        '';

    FOR reRecord IN EXECUTE stSql LOOP
        return next reRecord;
    END LOOP;

    RETURN ;
END;
' LANGUAGE 'plpgsql';
