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
* $Revision: 59612 $
* $Name$
* $Author: gelson $
* $Date: 2014-09-02 09:00:51 -0300 (Tue, 02 Sep 2014) $
*
* Casos de uso: uc-02.08.08
* Casos de uso: uc-02.08.08
*/

/*
$Log$
Revision 1.1  2007/09/26 14:43:57  gris
-- O módulo TCE-SC  funcionalidades deverá ir para a gestãp estação de contas.

Revision 1.14  2006/07/05 20:37:45  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION tcerj.fn_exportacao_empenho(varchar,varchar,varchar,varchar) RETURNS SETOF
RECORD AS '
DECLARE
    stExercicio         ALIAS FOR $1        ;
    stCodEntidades      ALIAS FOR $2        ;
    stDtInicial       ALIAS FOR $3        ;
    stDtFinal         ALIAS FOR $4        ;
    stSql               VARCHAR   := ''''   ;
    reRegistro          RECORD              ;
BEGIN

stSql := ''
    SELECT
        ped_d_cd.num_unidade                        as unidade,
        e.exercicio                                 as exercicio_empenho,
        e.cod_entidade                              as entidade,
        e.cod_empenho                               as empenho,
        ped_d_cd.exercicio                          as exercicio,
        orcamento.fn_consulta_tipo_pao(cast('' || stExercicio || '' as varchar),ped_d_cd.num_pao),
        ped_d_cd.num_pao                            as num_pao,
        ped_d_cd.cod_recurso                        as recurso,
	    cast(substr(replace(ped_d_cd.cod_estrutural,''''.'''',''''''''),1,8) as integer) as cod_estrutural,
        sum(ipe.vl_total)                           as valor,
        pe.descricao                                as descricao,
        pe.cod_tipo                                 as tipo,
        to_char(e.dt_empenho,''''dd/mm/yyyy'''')    as stData,
        cgm.nom_cgm                                 as nom_cgm,
        cgm_fisica.cpf                              as cpf,
        cgm_juridica.cnpj                           as cnpj,
        ped_d_cd.num_orgao                          as orgao,
        ped_d_cd.cod_funcao                         as funcao,
        ped_d_cd.cod_subfuncao                      as subfuncao,
        ped_d_cd.cod_programa                       as programa,
        CASE WHEN sujeito.valor is NOT NULL THEN
            ''''N''''
            ELSE
            ''''S'''' END                           as sujeito,
        CASE WHEN sujeito.valor is NOT NULL THEN
            ''''''''
            ELSE
            licitacao.valor END                     as num_proc_licit

    FROM
        empenho.empenho             as e
        LEFT JOIN
        (
        select
            epe.cod_pre_empenho, epe.exercicio, aev.valor
        from
            empenho.pre_empenho as epe,
            empenho.atributo_empenho_valor as aev,
            (
                select
                    max(aev2.timestamp) as timestamp,
                    aev2.cod_pre_empenho, aev2.exercicio, aev2.cod_cadastro, aev2.cod_modulo, aev2.cod_atributo
                from
                    empenho.atributo_empenho_valor as aev2,
                    administracao.atributo_dinamico as ad2
                where
                    aev2.cod_cadastro    = 1 and
                    aev2.cod_modulo      = 10 and
                    aev2.cod_modulo      = ad2.cod_modulo and
                    aev2.cod_cadastro    = ad2.cod_cadastro and
                    aev2.cod_atributo    = ad2.cod_atributo and
                    ad2.nom_atributo     ilike ''''%Modalidade%''''
                 group by
                    aev2.cod_pre_empenho, aev2.exercicio, aev2.cod_cadastro, aev2.cod_modulo, aev2.cod_atributo
                ) as aev2,
            administracao.atributo_dinamico as ad,
            administracao.atributo_valor_padrao as avp
        where
            -- ligação entre pre_empenho e atributo_empenho_valor
            epe.cod_pre_empenho = aev.cod_pre_empenho and
            epe.exercicio       = aev.exercicio and
            aev.cod_cadastro    = 1 and
            aev.cod_modulo      = 10 and

            -- ligação entre atributo_empenho_valor e atributo_dinamico
            aev.cod_modulo      = ad.cod_modulo and
            aev.cod_cadastro    = ad.cod_cadastro and
            aev.cod_atributo    = ad.cod_atributo and
            ad.nom_atributo     ilike ''''%Modalidade%'''' and

            -- ligação auxiliar pra pegar o atributo de timestamp maior vindo do select do aev2
            aev.cod_pre_empenho     = aev2.cod_pre_empenho and
            aev.exercicio           = aev2.exercicio and
            aev.cod_cadastro        = aev2.cod_cadastro and
            aev.cod_modulo          = aev2.cod_modulo and
            aev.cod_atributo        = aev2.cod_atributo and
            aev.timestamp           = aev2.timestamp and

            -- ligação entre atributo_dinamico e atributo_valor_padrao
            ad.cod_modulo       = avp.cod_modulo and
            ad.cod_cadastro     = avp.cod_cadastro and
            ad.cod_atributo     = avp.cod_atributo and
            avp.cod_valor       = aev.valor and
            (avp.valor_padrao    ilike ''''%Dispensa%'''' OR avp.valor_padrao ilike ''''%Inexigível%'''')

        ) as sujeito on (
            sujeito.cod_pre_empenho = e.cod_pre_empenho and
            sujeito.exercicio       = e.exercicio
          ) 
        LEFT JOIN (
            select
                epe.cod_pre_empenho, epe.exercicio, aev.valor
            from
                empenho.pre_empenho as epe,
                empenho.atributo_empenho_valor as aev,
                administracao.atributo_dinamico as ad
            where
                -- ligação entre pre_empenho e atributo_empenho_valor
                epe.cod_pre_empenho = aev.cod_pre_empenho and
                epe.exercicio       = aev.exercicio and
                aev.cod_cadastro    = 1 and
                aev.cod_modulo      = 10 and

                -- ligação entre atributo_empenho_valor e atributo_dinamico
                aev.cod_modulo      = ad.cod_modulo and
                aev.cod_cadastro    = ad.cod_cadastro and
                aev.cod_atributo    = ad.cod_atributo and
                ad.nom_atributo     ilike ''''%Nro do Processo Licitatório%'''' 
    
            ) as licitacao on (
                licitacao.cod_pre_empenho = e.cod_pre_empenho and
                licitacao.exercicio       = e.exercicio
              ),
        
        empenho.item_pre_empenho    as ipe,
        sw_cgm                      as cgm
            LEFT OUTER JOIN (
                SELECT
                    cgm_fisica.cpf,
                    cgm_fisica.numcgm
                FROM
                    sw_cgm_pessoa_fisica as cgm_fisica
            ) as cgm_fisica ON
            cgm.numcgm = cgm_fisica.numcgm
            LEFT OUTER JOIN (
                SELECT
                    cgm_juridica.cnpj,
                    cgm_juridica.numcgm
                FROM
                    sw_cgm_pessoa_juridica as cgm_juridica
            ) as cgm_juridica ON
            cgm.numcgm = cgm_juridica.numcgm,
        empenho.pre_empenho as pe
            LEFT OUTER JOIN (
                SELECT
                    d.num_unidade,
                    d.exercicio,
                    d.num_pao,
                    d.cod_recurso,
                    d.num_orgao,
                    d.cod_funcao,
                    d.cod_subfuncao,
                    d.cod_programa,
                    ped.cod_pre_empenho,
                    ped.cod_despesa,
		            cd.cod_estrutural
                FROM
                    empenho.pre_empenho_despesa as ped,
                    orcamento.despesa as d,
                    orcamento.conta_despesa as cd
                WHERE
                    ped.cod_despesa     = d.cod_despesa and
                    ped.exercicio       = d.exercicio and
                    d.cod_conta       = cd.cod_conta and
                    d.exercicio       = cd.exercicio
            ) as ped_d_cd ON
            pe.exercicio = ped_d_cd.exercicio AND
            pe.cod_pre_empenho = ped_d_cd.cod_pre_empenho
     WHERE
            e.exercicio         = '''''' || stExercicio || ''''''
        AND e.cod_entidade      IN ('' || stCodEntidades || '')
        AND e.dt_empenho BETWEEN to_date('''''' || stDtInicial || '''''',''''dd/mm/yyyy'''') AND  to_date('''''' || stDtFinal || '''''',''''dd/mm/yyyy'''')

        AND e.exercicio         = pe.exercicio
        AND e.cod_pre_empenho   = pe.cod_pre_empenho

        AND pe.cgm_beneficiario = cgm.numcgm

        AND pe.exercicio        = ipe.exercicio
        AND pe.cod_pre_empenho  = ipe.cod_pre_empenho

    GROUP BY
        ped_d_cd.num_unidade,
        e.exercicio,
        e.cod_entidade,
        e.cod_empenho,
        ped_d_cd.exercicio,
        ped_d_cd.num_pao,
        ped_d_cd.cod_recurso,
	    ped_d_cd.cod_estrutural,
        pe.descricao,
        pe.cod_tipo,
        to_char(e.dt_empenho,''''dd/mm/yyyy''''),
        cgm.nom_cgm,
        cgm_fisica.cpf,
        cgm_juridica.cnpj,
        ped_d_cd.num_orgao,
        ped_d_cd.cod_funcao,
        ped_d_cd.cod_subfuncao,
        ped_d_cd.cod_programa,
        sujeito.valor,
        num_proc_licit

    ORDER BY
        ped_d_cd.num_unidade,
        e.exercicio,
        e.cod_entidade,
        e.cod_empenho,
        ped_d_cd.exercicio,
        ped_d_cd.num_pao,
        ped_d_cd.cod_recurso,
        ped_d_cd.cod_estrutural,
        pe.descricao,
        pe.cod_tipo,
        to_char(e.dt_empenho,''''dd/mm/yyyy''''),
        cgm.nom_cgm,
        cgm_fisica.cpf,
        cgm_juridica.cnpj,
        ped_d_cd.num_orgao,
        ped_d_cd.cod_funcao,
        ped_d_cd.cod_subfuncao,
        ped_d_cd.cod_programa,
        sujeito.valor,
        num_proc_licit
'';

    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN next reRegistro;
    END LOOP;

    RETURN;
END;
'language 'plpgsql';

