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
* Casos de uso: uc-02.00.00
* Casos de uso: uc-02.00.00
*/

/*
$Log$
Revision 1.1  2007/09/26 14:43:57  gris
-- O módulo TCE-SC  funcionalidades deverá ir para a gestãp estação de contas.

Revision 1.7  2007/09/25 14:10:43  cako
Ticket#10193#

Revision 1.6  2007/07/30 18:28:33  tonismar
alteração de uc-00.00.00 para uc-02.00.00

Revision 1.5  2006/07/05 20:37:45  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION tcers.fn_exportacao_Empenho_Itens_Anulados(varchar,varchar,varchar,varchar) RETURNS SETOF record AS $$
DECLARE
    stExercicio     ALIAS FOR $1    ;
    stDataInicial   ALIAS FOR $2    ;
    stDataFinal     ALIAS FOR $3    ;
    stCodEntidade   ALIAS FOR $4    ;
    stSql           VARCHAR := '' ;
    raRegistro      RECORD          ;
    arDados         VARCHAR[] := array[0];
BEGIN
stSql = '
    -- Select para soma do itens  --> data vem de empenho.empenho.dt_empenho
SELECT
        0 as num_orgao      ,       -- ok
        0 as num_unidade    ,       -- ok
        0 as cod_funcao     ,       -- ok
        0 as cod_subfuncao  ,       -- ok
        0 as cod_programa   ,       -- ok
        0 as num_pao        ,       -- ok
        0 as cod_recurso,           -- ok
        cast('''' as varchar)  as cod_estrutural,
        ee.cod_empenho,
        to_date(to_char(eea.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'') as dt_empenho,
        eeai.vl_anulado as vl_empenhado,
        cast(''-'' as varchar) as sinal,
        pe.cgm_beneficiario,
        CAST(CASE WHEN trim(pe.descricao) = '''' THEN
                publico.concatenar_hifen(ie.quantidade::varchar) || '' '' || publico.concatenar_hifen(ie.nom_unidade) || '' '' || publico.concatenar_hifen(ie.nom_item) || '' '' || publico.concatenar_hifen(ie.complemento)
            ELSE pe.descricao
        END as varchar ) as historico,
        ee.cod_pre_empenho,
        ee.exercicio,
        ee.cod_entidade,
        cast(''2'' as integer) as ordem,
        eeai.oid
    FROM
            empenho.pre_empenho          as pe   ,
            empenho.empenho_anulado      as eea  ,
            empenho.empenho_anulado_item as eeai ,
            empenho.empenho              as ee   ,
            empenho.item_pre_empenho    as ie
    WHERE
        to_date(to_char(eea.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'') BETWEEN to_date('''||stDataInicial||''',''dd/mm/yyyy'') AND to_date('''||stDataFinal||''',''dd/mm/yyyy'')
        AND ee.cod_entidade     IN   ('||stCodEntidade||')
        -- ligar pre empenho
        AND pe.exercicio        = ee.exercicio
        AND pe.cod_pre_empenho  = ee.cod_pre_empenho
        -- ligar empenho_anulado
        AND eea.exercicio       = ee.exercicio
        AND eea.cod_entidade    = ee.cod_entidade
        AND eea.cod_empenho     = ee.cod_empenho
        -- ligar empenh_anulado_item
        AND eeai.exercicio      = eea.exercicio
        AND eeai.cod_entidade   = eea.cod_entidade
        AND eeai.cod_empenho    = eea.cod_empenho
        AND eeai.timestamp      = eea.timestamp
        -- ligar pre_empenho item_pre_empenho
        AND pe.exercicio        = ie.exercicio
        AND pe.cod_pre_empenho  = ie.cod_pre_empenho

        GROUP BY
            num_orgao      ,
            num_unidade    ,
            cod_funcao     ,
            cod_subfuncao  ,
            cod_programa   ,
            num_pao        ,
            cod_recurso    ,
            cod_estrutural   ,
            ee.cod_empenho,
            ee.dt_empenho,
            vl_empenhado,
            sinal,
            pe.cgm_beneficiario,
            ee.cod_pre_empenho,
            ee.exercicio,
            ee.cod_entidade,
            ordem,
            pe.descricao,
            to_date(to_char(eea.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy''),
            eeai.oid
';

    FOR raRegistro IN EXECUTE stSql
    LOOP
        arDados := tcers.fn_exportacao_dados_empenho(raRegistro.cod_empenho,raRegistro.exercicio,raRegistro.cod_entidade);
        raRegistro.num_orgao        := to_number(arDados[1], '9999999999');
        raRegistro.num_unidade      := to_number(arDados[2], '9999999999');
        raRegistro.cod_funcao       := to_number(arDados[3], '9999999999');
        raRegistro.cod_subfuncao    := to_number(arDados[4], '9999999999');
        raRegistro.cod_programa     := to_number(arDados[5], '9999999999');
        raRegistro.num_pao          := to_number(arDados[6], '9999999999');
        raRegistro.cod_estrutural   := arDados[7];
        raRegistro.cod_recurso      := to_number(arDados[8], '9999999999');
        RETURN NEXT raRegistro;
    END LOOP;

    RETURN;
END;
$$ LANGUAGE 'plpgsql';
