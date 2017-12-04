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
* $Id: listaEmissaoGraficaEconomico.plsql 60672 2014-11-07 13:42:56Z evandro $
*
* Caso de uso: uc-05.03.11
* Caso de uso: uc-05.03.19
*
*/

CREATE OR REPLACE FUNCTION arrecadacao.fn_lista_emissao_grafica_economico
  (VARCHAR, INT, INT, INT, INT, INT, INT, VARCHAR, INT, VARCHAR, VARCHAR, INT, INT, VARCHAR, VARCHAR, VARCHAR, VARCHAR, VARCHAR)

RETURNS SETOF RECORD AS $$
DECLARE

    stTipoInscricao         ALIAS FOR $1;

    inExercicio     	    ALIAS FOR $2;
    inCodGrupo      	    ALIAS FOR $3;

    inCodCredito    	    ALIAS FOR $4;
    inCodEspecie    	    ALIAS FOR $5;
    inCodGenero     	    ALIAS FOR $6;
    inCodNatureza   	    ALIAS FOR $7;

    inCodIEInicial  	    ALIAS FOR $8;
    inCodIEFinal    	    ALIAS FOR $9;

    stLocalizacaoInicial    ALIAS FOR $10;
    stLocalizacaoFinal	    ALIAS FOR $11;

    inCodEnderecoInicial    ALIAS FOR $12;
    inCodEnderecoFinal      ALIAS FOR $13;

    stOrdemEmissao          ALIAS FOR $14;
    
    stOrdemLote		        ALIAS FOR $15;
    stOrdemImovel	        ALIAS FOR $16;
    stOrdemEdificacao  	    ALIAS FOR $17;
    stPadraoCodBarra        ALIAS FOR $18;

    inRetorno               INTEGER;
    reRegistro              RECORD;
    stSql                   VARCHAR;
    stFiltro                VARCHAR := '';
    stFiltroTipoInscricao   VARCHAR := '';
    stJoins                 VARCHAR := '';
    stFrom                  VARCHAR := '';
    stOrdem                 VARCHAR := '';

    stFromOrigem            VARCHAR := '';
    stFiltroCredito         VARCHAR := '';
    stColunasOrigem         VARCHAR := '';
    stGroupByOrigem         VARCHAR := '';

    inNumConvenio           INTEGER;
    inCodFebraban           INTEGER;

BEGIN

    SELECT valor INTO inCodFebraban
      FROM administracao.configuracao 
     WHERE parametro = 'FEBRABAN'
       AND cod_modulo = 2
  ORDER BY exercicio DESC
     LIMIT 1;

    IF (inCodCredito > 0) THEN
            SELECT DISTINCT convenio.num_convenio
              INTO inNumConvenio
              FROM monetario.convenio
        INNER JOIN monetario.credito
                ON credito.cod_convenio = convenio.cod_convenio
             WHERE credito.cod_credito  = inCodCredito
               AND credito.cod_natureza = inCodNatureza
               AND credito.cod_genero   = inCodGenero
               AND credito.cod_especie  = inCodEspecie;
    ELSIF ( inCodGrupo > 0 ) THEN
            SELECT DISTINCT convenio.num_convenio
              INTO inNumConvenio
              FROM monetario.convenio
        INNER JOIN monetario.credito
                ON credito.cod_convenio = convenio.cod_convenio
        INNER JOIN arrecadacao.credito_grupo
                ON credito_grupo.cod_credito   = credito.cod_credito
               AND credito_grupo.cod_natureza  = credito.cod_natureza
               AND credito_grupo.cod_especie   = credito.cod_especie
               AND credito_grupo.cod_genero    = credito.cod_genero
             WHERE credito_grupo.cod_grupo     = inCodGrupo 
               AND credito_grupo.ano_exercicio = '' || inExercicio ||'' ;
    END IF;

-- Antes de executar a consulta, é verificado todos os filtros, aonde a tabela de maior proximidade 
-- com o filtro mais exclusivo torna-se a tabela-mãe.

-- INICIO FILTROS

    -- Filtro para crédito
    IF (inCodCredito > 0) THEN
    
        stColunasOrigem := '
            , 0 as cod_grupo
            , split_part ( monetario.fn_busca_mascara_credito(
                ac.cod_credito, ac.cod_genero, ac.cod_especie, ac.cod_natureza), ''§'', 6
            )::varchar as descricao
            , ac.exercicio::int as exercicio
        ';
    
        stFiltroCredito := '
            WHERE NOT EXISTS (SELECT cod_calculo FROM arrecadacao.calculo_grupo_credito WHERE cod_calculo = ac.cod_calculo)
              AND ac.cod_credito  = '||inCodCredito::varchar ||'
              AND ac.cod_especie  = '||inCodEspecie::varchar ||'
              AND ac.cod_genero   = '||inCodGenero::varchar  ||'
              AND ac.cod_natureza = '||inCodNatureza::varchar||'
        ';

        stGroupByOrigem := '
            , ac.cod_credito
            , ac.cod_genero
            , ac.cod_especie
            , ac.cod_natureza
        ';
    
    END IF;

    -- Filtro para Grupo de Creditos
    IF (inCodGrupo > 0) THEN
    
        stColunasOrigem := '
            , acgc.cod_grupo
            , agc.descricao
            , ac.exercicio::int as exercicio
        ';
    
        stFiltro := stFiltro||' AND aic2.cod_grupo = '||inCodGrupo;
    
        stFromOrigem := stFrom || '

            INNER JOIN arrecadacao.calculo_grupo_credito as acgc
                    ON acgc.cod_calculo = cec.cod_calculo
    
            INNER JOIN arrecadacao.grupo_credito as agc
                    ON agc.cod_grupo     = acgc.cod_grupo
                   AND agc.ano_exercicio = acgc.ano_exercicio

        ';
    
        stGroupByOrigem := '
            , agc.descricao
            , acgc.cod_grupo
        ';

    END IF;

    IF (inExercicio > 0) THEN
        stFiltro := stFiltro||' AND aic2.exercicio = '|| quote_literal(inExercicio) ||' ';
    END IF;


    -- Inscricao Economica
    stFiltro := stFiltro||' and cec.inscricao_economica in ( '||inCodIEInicial||' ) ';
    --if (inCodIEInicial > 0) then
    --  	if ( inCodIEFinal > 0) then
    --        stFiltro := stFiltro||'' and cec.inscricao_economica between ''||inCodIEInicial||'' and ''||inCodIEFinal||'' '';
    --  	else
    --    	stFiltro := stFiltro||'' and aic2.inscricao = ''||inCodIEInicial;
    --  	end if;
    --end if;

-- FIM DOS FILTROS

-- ORDER BY

    IF (stOrdemEmissao != '') THEN
        stOrdem := stOrdemEmissao;
    END IF;

    IF ( stOrdem != '' ) THEN
        stOrdem := ' ORDER BY '|| stOrdem || ' ';
    END IF;


-- CONSULTA PRINCIPAL

stSql := '

SELECT tudo.inscricao
    , tudo.exercicio
    , tudo.cod_lancamento
    , tudo.lanc_venc
    , tudo.lanc_valor
    , tudo.cod_grupo
    , tudo.descricao
    , tudo.numcgm
    , tudo.nom_cgm
 
    , split_part(infos,''§'',1)::int as inscricao_municipal_economica
    , split_part(infos,''§'',3)::varchar as codigo_composto
    , split_part(infos,''§'',4)::varchar as nom_localizacao
    , split_part(infos,''§'',2)::int as cod_lote
    , nom_fantasia
    , cnpj

    , split_part(tudo.endereco,''§'',1)::varchar as nom_tipo
    , split_part(tudo.endereco,''§'',2)::int     as cod_logradouro
    , split_part(tudo.endereco,''§'',3)::varchar as nom_logradouro
    , split_part(tudo.endereco,''§'',4)::varchar as numero
    , split_part(tudo.endereco,''§'',5)::varchar as complemento
    , split_part(tudo.endereco,''§'',6)::varchar as nom_bairro
    , split_part(tudo.endereco,''§'',7)::varchar as cep
    , split_part(tudo.endereco,''§'',8)::varchar as cod_municipio
    , split_part(tudo.endereco,''§'',9)::varchar as nom_municipio
    , split_part(tudo.endereco,''§'',10)::varchar as cod_uf
    , split_part(tudo.endereco,''§'',11)::varchar as sigla_u
    , split_part(tudo.endereco,''§'',1)::varchar as c_nom_tipo_logradouro
    , split_part(tudo.endereco,''§'',2)::int as c_cod_logradouro
    , split_part(tudo.endereco,''§'',3)::varchar as c_nom_logradouro
    , split_part(tudo.endereco,''§'',4)::varchar as c_numero
    , split_part(tudo.endereco,''§'',5)::varchar as c_complemento
    , split_part(tudo.endereco,''§'',6)::varchar as c_nom_bairro
    , split_part(tudo.endereco,''§'',7)::varchar as c_cep
    , split_part(tudo.endereco,''§'',8)::varchar as c_cod_municipio
    , split_part(tudo.endereco,''§'',9)::varchar as c_nom_municipio
    , split_part(tudo.endereco,''§'',10)::varchar as c_cod_uf
    , split_part(tudo.endereco,''§'',11)::varchar as c_sigla_uf
    
    , ( case when tudo.endereco_c is null then
            null
        else split_part(tudo.endereco_c,''§'',12)
        end
    )::varchar as c_caixa_postal

    , split_part(lista_parcelas_unicas,''§'',1)::varchar as qtde_parcelas_unicas

-- INICIO ALTERACOES
    , ( case when ( split_part(lista_parcelas_unicas,''§'',1)::integer > 0 ) THEN
            split_part(lista_parcelas_unicas,''§'',2)
        ELSE '''' END
    )::varchar as cod_parcela_unica_1
    , ( case when ( split_part(lista_parcelas_unicas,''§'',1)::integer > 0 ) THEN
            split_part(lista_parcelas_unicas,''§'',3)
        ELSE '''' END
    )::varchar as valor_unica_1
    , ( case when ( split_part(lista_parcelas_unicas,''§'',1)::integer > 0 ) THEN
            split_part(lista_parcelas_unicas,''§'',4)
        ELSE '''' END
    )::varchar as vencimento_unica_1
    , ( case when ( split_part(lista_parcelas_unicas,''§'',1)::integer > 0 ) THEN
            split_part(lista_parcelas_unicas,''§'',5)
        ELSE '''' END
    )::varchar as desconto_unica_1
    , ( case when ( split_part(lista_parcelas_unicas,''§'',1)::integer > 0 ) THEN
            split_part(lista_parcelas_unicas,''§'',6)
        ELSE '''' END
    )::varchar as numeracao_unica_1
    , ( case when ( split_part(lista_parcelas_unicas,''§'',1)::integer > 0 ) THEN
            split_part(lista_parcelas_unicas,''§'',6)
        ELSE '''' END
    )::varchar as nosso_numero_unica_1
    , RTRIM( case when ( split_part(lista_parcelas_unicas,''§'',1)::integer > 0 ) THEN
            split_part ( 
';

IF ( stPadraoCodBarra = 'febrabanCompBBAnexo5' ) THEN
    stSql := stSql ||'
        arrecadacao.geraCodigoBarraFebrabanCompensacaoBBAnexo5 (
            split_part(lista_parcelas_unicas,''§'',8)::date,
            split_part(lista_parcelas_unicas,''§'',5)::varchar,
            split_part(lista_parcelas_unicas,''§'',6)::varchar,
            9,
            '||inNumConvenio||'
        )::varchar
    ';
ELSE
    stSql := stSql ||'
        arrecadacao.geraCodigoBarraFebraban (
            split_part(lista_parcelas_unicas,''§'',8)::varchar,
            split_part(lista_parcelas_unicas,''§'',5)::varchar,
            split_part(lista_parcelas_unicas,''§'',6)::varchar,
            6,
            '||inCodFebraban||'
        )::varchar
    ';
END IF;

stSql := stSql ||'

                , ''§''
                , 1
            )
        ELSE '''' END
    )::varchar as codigo_barra_unica_1
    , LTRIM( case when ( split_part(lista_parcelas_unicas,''§'',1)::integer > 0 ) THEN
            split_part ( 
';

IF ( stPadraoCodBarra = 'febrabanCompBBAnexo5' ) THEN
    stSql := stSql ||'
        arrecadacao.geraCodigoBarraFebrabanCompensacaoBBAnexo5 (
            split_part(lista_parcelas_unicas,''§'',8)::date,
            split_part(lista_parcelas_unicas,''§'',5)::varchar,
            split_part(lista_parcelas_unicas,''§'',6)::varchar,
            9,
            '||inNumConvenio||'
        )::varchar
    ';
ELSE
    stSql := stSql ||'
        arrecadacao.geraCodigoBarraFebraban (
            split_part(lista_parcelas_unicas,''§'',8)::varchar,
            split_part(lista_parcelas_unicas,''§'',5)::varchar,
            split_part(lista_parcelas_unicas,''§'',6)::varchar,
            6,
            '||inCodFebraban||'
        )::varchar
    ';
END IF;

stSql := stSql ||'

                , ''§''
                , 2
            )
        ELSE '''' END
    )::varchar as linha_digitavel_unica_1

    , ( case when ( split_part(lista_parcelas_unicas,''§'',1)::integer > 1 ) THEN
            split_part(lista_parcelas_unicas,''§'',9)
        ELSE '''' END
    )::varchar as cod_parcela_unica_2
    , ( case when ( split_part(lista_parcelas_unicas,''§'',1)::integer > 1 ) THEN
            split_part(lista_parcelas_unicas,''§'',10)
        ELSE '''' END
    )::varchar as valor_unica_2
    , ( case when ( split_part(lista_parcelas_unicas,''§'',1)::integer > 1 ) THEN
            split_part(lista_parcelas_unicas,''§'',11)
        ELSE '''' END
    )::varchar as vencimento_unica_2
    , ( case when ( split_part(lista_parcelas_unicas,''§'',1)::integer > 1 ) THEN
            split_part(lista_parcelas_unicas,''§'',12)
        ELSE '''' END
    )::varchar as desconto_unica_2
    , ( case when ( split_part(lista_parcelas_unicas,''§'',1)::integer > 1 ) THEN
            split_part(lista_parcelas_unicas,''§'',13)
        ELSE '''' END
    )::varchar as numeracao_unica_2
    , ( case when ( split_part(lista_parcelas_unicas,''§'',1)::integer > 1 ) THEN
            split_part(lista_parcelas_unicas,''§'',13)
        ELSE '''' END
    )::varchar as nosso_numero_unica_2
    , RTRIM( case when ( split_part(lista_parcelas_unicas,''§'',1)::integer > 1 ) THEN
            split_part (  
';

IF ( stPadraoCodBarra = 'febrabanCompBBAnexo5' ) THEN
    stSql := stSql ||'
        arrecadacao.geraCodigoBarraFebrabanCompensacaoBBAnexo5 (
            split_part(lista_parcelas_unicas,''§'',15)::date,
            split_part(lista_parcelas_unicas,''§'',12)::varchar,
            split_part(lista_parcelas_unicas,''§'',13)::varchar,
            9,
            '||inNumConvenio||'
        )::varchar
    ';
ELSE
    stSql := stSql ||'
        arrecadacao.geraCodigoBarraFebraban (
            split_part(lista_parcelas_unicas,''§'',15)::varchar,
            split_part(lista_parcelas_unicas,''§'',12)::varchar,
            split_part(lista_parcelas_unicas,''§'',13)::varchar,
            6,
            '||inCodFebraban||'
        )::varchar
    ';
END IF;

stSql := stSql ||'

                , ''§''
                , 1
            )
        ELSE '''' END
    )::varchar as codigo_barra_unica_2
    , ( case when ( split_part(lista_parcelas_unicas,''§'',1)::integer > 1 ) THEN
            split_part (
';

IF ( stPadraoCodBarra = 'febrabanCompBBAnexo5' ) THEN
    stSql := stSql ||'
        arrecadacao.geraCodigoBarraFebrabanCompensacaoBBAnexo5 (
            split_part(lista_parcelas_unicas,''§'',15)::date,
            split_part(lista_parcelas_unicas,''§'',12)::varchar,
            split_part(lista_parcelas_unicas,''§'',13)::varchar,
            9,
            '||inNumConvenio||'
        )::varchar
    ';
ELSE
    stSql := stSql ||'
        arrecadacao.geraCodigoBarraFebraban (
            split_part(lista_parcelas_unicas,''§'',15)::varchar,
            split_part(lista_parcelas_unicas,''§'',12)::varchar,
            split_part(lista_parcelas_unicas,''§'',13)::varchar,
            6,
            '||inCodFebraban||'
        )::varchar
    ';
END IF;

stSql := stSql ||'
                , ''§''
                , 2
            )
        ELSE '''' END
    )::varchar as linha_digitavel_unica_2

    , ( case when ( split_part(lista_parcelas_unicas,''§'',1)::integer > 2 ) THEN
            split_part(lista_parcelas_unicas,''§'',16)
        ELSE '''' END
    )::varchar as cod_parcela_unica_3
    , ( case when ( split_part(lista_parcelas_unicas,''§'',1)::integer > 2 ) THEN
            split_part(lista_parcelas_unicas,''§'',17)
        ELSE '''' END
    )::varchar as valor_unica_3
    , ( case when ( split_part(lista_parcelas_unicas,''§'',1)::integer > 2 ) THEN
            split_part(lista_parcelas_unicas,''§'',18)
        ELSE '''' END
    )::varchar as vencimento_unica_3
    , ( case when ( split_part(lista_parcelas_unicas,''§'',1)::integer > 2 ) THEN
            split_part(lista_parcelas_unicas,''§'',19)
        ELSE '''' END
    )::varchar as desconto_unica_3
    , ( case when ( split_part(lista_parcelas_unicas,''§'',1)::integer > 2 ) THEN
            split_part(lista_parcelas_unicas,''§'',20)
        ELSE '''' END
    )::varchar as numeracao_unica_3
    , ( case when ( split_part(lista_parcelas_unicas,''§'',1)::integer > 2 ) THEN
            split_part(lista_parcelas_unicas,''§'',20)
        ELSE '''' END
    )::varchar as nosso_numero_unica_3
    , RTRIM( case when ( split_part(lista_parcelas_unicas,''§'',1)::integer > 2 ) THEN
            split_part (
';

IF ( stPadraoCodBarra = 'febrabanCompBBAnexo5' ) THEN
    stSql := stSql ||'
        arrecadacao.geraCodigoBarraFebrabanCompensacaoBBAnexo5 (
            split_part(lista_parcelas_unicas,''§'',22)::date,
            split_part(lista_parcelas_unicas,''§'',19)::varchar,
            split_part(lista_parcelas_unicas,''§'',20)::varchar,
            9,
            '||inNumConvenio||'
        )::varchar
    ';
ELSE
    stSql := stSql ||'
        arrecadacao.geraCodigoBarraFebraban (
            split_part(lista_parcelas_unicas,''§'',22)::varchar,
            split_part(lista_parcelas_unicas,''§'',19)::varchar,
            split_part(lista_parcelas_unicas,''§'',20)::varchar,
            6,
            '||inCodFebraban||'
        )::varchar
    ';
END IF;

stSql := stSql ||'
                , ''§''
                , 1
            )
        ELSE '''' END
    )::varchar as codigo_barra_unica_3
    , ( case when ( split_part(lista_parcelas_unicas,''§'',1)::integer > 2 ) THEN
            split_part (
';
IF ( stPadraoCodBarra = 'febrabanCompBBAnexo5' ) THEN
    stSql := stSql ||'
        arrecadacao.geraCodigoBarraFebrabanCompensacaoBBAnexo5 (
            split_part(lista_parcelas_unicas,''§'',22)::date,
            split_part(lista_parcelas_unicas,''§'',19)::varchar,
            split_part(lista_parcelas_unicas,''§'',20)::varchar,
            9,
            '||inNumConvenio||'
        )::varchar
    ';
ELSE
    stSql := stSql ||'
        arrecadacao.geraCodigoBarraFebraban (
            split_part(lista_parcelas_unicas,''§'',22)::varchar,
            split_part(lista_parcelas_unicas,''§'',19)::varchar,
            split_part(lista_parcelas_unicas,''§'',20)::varchar,
            6,
            '||inCodFebraban||'
        )::varchar
    ';
END IF;

stSql := stSql ||'
                , ''§''
                , 2
            )
        ELSE '''' END
    )::varchar as linha_digitavel_unica_3

    , ( case when ( split_part(lista_parcelas_unicas,''§'',1)::integer > 3 ) THEN
            split_part(lista_parcelas_unicas,''§'',23)
        ELSE '''' END
    )::varchar as cod_parcela_unica_4
    , ( case when ( split_part(lista_parcelas_unicas,''§'',1)::integer > 3 ) THEN
            split_part(lista_parcelas_unicas,''§'',24)
        ELSE '''' END
    )::varchar as valor_unica_4
    , ( case when ( split_part(lista_parcelas_unicas,''§'',1)::integer > 3 ) THEN
            split_part(lista_parcelas_unicas,''§'',25)
        ELSE '''' END
    )::varchar as vencimento_unica_4
    , ( case when ( split_part(lista_parcelas_unicas,''§'',1)::integer > 3 ) THEN
            split_part(lista_parcelas_unicas,''§'',26)
        ELSE '''' END
    )::varchar as desconto_unica_4
    , ( case when ( split_part(lista_parcelas_unicas,''§'',1)::integer > 3 ) THEN
            split_part(lista_parcelas_unicas,''§'',27)
        ELSE '''' END
    )::varchar as numeracao_unica_4
    , ( case when ( split_part(lista_parcelas_unicas,''§'',1)::integer > 3 ) THEN
            split_part(lista_parcelas_unicas,''§'',27)
        ELSE '''' END
    )::varchar as nosso_numero_unica_4
    , RTRIM( case when ( split_part(lista_parcelas_unicas,''§'',1)::integer > 3 ) THEN
            split_part (
';
IF ( stPadraoCodBarra = 'febrabanCompBBAnexo5' ) THEN
    stSql := stSql ||'
        arrecadacao.geraCodigoBarraFebrabanCompensacaoBBAnexo5 (
            split_part(lista_parcelas_unicas,''§'',29)::date,
            split_part(lista_parcelas_unicas,''§'',26)::varchar,
            split_part(lista_parcelas_unicas,''§'',27)::varchar,
            9,
            '||inNumConvenio||'
        )::varchar
    ';
ELSE
    stSql := stSql ||'
        arrecadacao.geraCodigoBarraFebraban (
            split_part(lista_parcelas_unicas,''§'',29)::varchar,
            split_part(lista_parcelas_unicas,''§'',26)::varchar,
            split_part(lista_parcelas_unicas,''§'',27)::varchar,
            6,
            '||inCodFebraban||'
        )::varchar
    ';
END IF;

stSql := stSql ||'
                , ''§''
                , 1
            )
        ELSE '''' END
    )::varchar as codigo_barra_unica_4
    , ( case when ( split_part(lista_parcelas_unicas,''§'',1)::integer > 3 ) THEN
            split_part (
';
IF ( stPadraoCodBarra = 'febrabanCompBBAnexo5' ) THEN
    stSql := stSql ||'
        arrecadacao.geraCodigoBarraFebrabanCompensacaoBBAnexo5 (
            split_part(lista_parcelas_unicas,''§'',29)::date,
            split_part(lista_parcelas_unicas,''§'',26)::varchar,
            split_part(lista_parcelas_unicas,''§'',27)::varchar,
            9,
            '||inNumConvenio||'
        )::varchar
    ';
ELSE
    stSql := stSql ||'
        arrecadacao.geraCodigoBarraFebraban (
            split_part(lista_parcelas_unicas,''§'',29)::varchar,
            split_part(lista_parcelas_unicas,''§'',26)::varchar,
            split_part(lista_parcelas_unicas,''§'',27)::varchar,
            6,
            '||inCodFebraban||'
        )::varchar
    ';
END IF;

stSql := stSql ||'
                , ''§''
                , 2
            )
        ELSE '''' END
    )::varchar as linha_digitavel_unica_4

   , ( case when ( split_part(lista_parcelas_unicas,''§'',1)::integer > 3 ) THEN
            split_part(lista_parcelas_unicas,''§'',30)
        ELSE '''' END
    )::varchar as cod_parcela_unica_5
    , ( case when ( split_part(lista_parcelas_unicas,''§'',1)::integer > 3 ) THEN
            split_part(lista_parcelas_unicas,''§'',31)
        ELSE '''' END
    )::varchar as valor_unica_5
    , ( case when ( split_part(lista_parcelas_unicas,''§'',1)::integer > 3 ) THEN
            split_part(lista_parcelas_unicas,''§'',32)
        ELSE '''' END
    )::varchar as vencimento_unica_5
    , ( case when ( split_part(lista_parcelas_unicas,''§'',1)::integer > 3 ) THEN
            split_part(lista_parcelas_unicas,''§'',33)
        ELSE '''' END
    )::varchar as desconto_unica_5
    , ( case when ( split_part(lista_parcelas_unicas,''§'',1)::integer > 3 ) THEN
            split_part(lista_parcelas_unicas,''§'',34)
        ELSE '''' END
    )::varchar as numeracao_unica_5
    , ( case when ( split_part(lista_parcelas_unicas,''§'',1)::integer > 3 ) THEN
            split_part(lista_parcelas_unicas,''§'',34)
        ELSE '''' END
    )::varchar as nosso_numero_unica_5
    , RTRIM( case when ( split_part(lista_parcelas_unicas,''§'',1)::integer > 3 ) THEN
            split_part (
';
IF ( stPadraoCodBarra = 'febrabanCompBBAnexo5' ) THEN
    stSql := stSql ||'
        arrecadacao.geraCodigoBarraFebrabanCompensacaoBBAnexo5 (
            split_part(lista_parcelas_unicas,''§'',36)::date,
            split_part(lista_parcelas_unicas,''§'',33)::varchar,
            split_part(lista_parcelas_unicas,''§'',34)::varchar,
            9,
            '||inNumConvenio||'
        )::varchar
    ';
ELSE
    stSql := stSql ||'
        arrecadacao.geraCodigoBarraFebraban (
            split_part(lista_parcelas_unicas,''§'',36)::varchar,
            split_part(lista_parcelas_unicas,''§'',33)::varchar,
            split_part(lista_parcelas_unicas,''§'',34)::varchar,
            6,
            '||inCodFebraban||'
        )::varchar
    ';
END IF;

stSql := stSql ||'
                , ''§''
                , 1
            )
        ELSE '''' END
    )::varchar as codigo_barra_unica_5
    , ( case when ( split_part(lista_parcelas_unicas,''§'',1)::integer > 3 ) THEN
            split_part (
';
IF ( stPadraoCodBarra = 'febrabanCompBBAnexo5' ) THEN
    stSql := stSql ||'
        arrecadacao.geraCodigoBarraFebrabanCompensacaoBBAnexo5 (
            split_part(lista_parcelas_unicas,''§'',36)::date,
            split_part(lista_parcelas_unicas,''§'',33)::varchar,
            split_part(lista_parcelas_unicas,''§'',34)::varchar,
            9,
            '||inNumConvenio||'
        )::varchar
    ';
ELSE
    stSql := stSql ||'
        arrecadacao.geraCodigoBarraFebraban (
            split_part(lista_parcelas_unicas,''§'',36)::varchar,
            split_part(lista_parcelas_unicas,''§'',33)::varchar,
            split_part(lista_parcelas_unicas,''§'',34)::varchar,
            6,
            '||inCodFebraban||'
        )::varchar
    ';
END IF;

stSql := stSql ||'
                , ''§''
                , 2
            )
        ELSE '''' END
    )::varchar as linha_digitavel_unica_5

    -- PARCELAS NORMAIS
    , split_part(lista_parcelas_normais,''§'',1)::varchar as qtde_parcelas_normais

    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 0 ) THEN
            split_part(lista_parcelas_normais,''§'',2)
        ELSE '''' END
    )::varchar as cod_parcela_normal_1
    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 0 ) THEN
            split_part(lista_parcelas_normais,''§'',3)
        ELSE '''' END
    )::varchar as valor_normal_1
    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 0 ) THEN
            split_part(lista_parcelas_normais,''§'',4)
        ELSE '''' END
    )::varchar as vencimento_normal_1
    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 0 ) THEN
            split_part(lista_parcelas_normais,''§'',5)
        ELSE '''' END
    )::varchar as numeracao_normal_1
    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 0 ) THEN
            split_part(lista_parcelas_normais,''§'',5)
        ELSE '''' END
    )::varchar as nosso_numero_normal_1
    , RTRIM( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 0 ) THEN
            split_part (
';
IF ( stPadraoCodBarra = 'febrabanCompBBAnexo5' ) THEN
    stSql := stSql ||'
        arrecadacao.geraCodigoBarraFebrabanCompensacaoBBAnexo5 (
            split_part(lista_parcelas_normais,''§'',7)::date,
            split_part(lista_parcelas_normais,''§'',3)::varchar,
            split_part(lista_parcelas_normais,''§'',5)::varchar,
            9,
            '||inNumConvenio||'
        )::varchar
    ';
ELSE
    stSql := stSql ||'
        arrecadacao.geraCodigoBarraFebraban (
            split_part(lista_parcelas_normais,''§'',7)::varchar,
            split_part(lista_parcelas_normais,''§'',3)::varchar,
            split_part(lista_parcelas_normais,''§'',5)::varchar,
            7,
            '||inCodFebraban||'
        )::varchar
    ';
END IF;

stSql := stSql ||'
                , ''§''
                , 1
            )
        ELSE '''' END
    )::varchar as codigo_barra_normal_1
    , LTRIM( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 0 ) THEN
            split_part (
';
IF ( stPadraoCodBarra = 'febrabanCompBBAnexo5' ) THEN
    stSql := stSql ||'
        arrecadacao.geraCodigoBarraFebrabanCompensacaoBBAnexo5 (
            split_part(lista_parcelas_normais,''§'',7)::date,
            split_part(lista_parcelas_normais,''§'',3)::varchar,
            split_part(lista_parcelas_normais,''§'',5)::varchar,
            9,
            '||inNumConvenio||'
        )::varchar
    ';
ELSE
    stSql := stSql ||'
        arrecadacao.geraCodigoBarraFebraban (
            split_part(lista_parcelas_normais,''§'',7)::varchar,
            split_part(lista_parcelas_normais,''§'',3)::varchar,
            split_part(lista_parcelas_normais,''§'',5)::varchar,
            7,
            '||inCodFebraban||'
        )::varchar
    ';
END IF;

stSql := stSql ||'
                , ''§''
                , 2
            )
        ELSE '''' END
    )::varchar as linha_digitavel_normal_1

    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 1 ) THEN
            split_part(lista_parcelas_normais,''§'',8)
        ELSE '''' END
    )::varchar as cod_parcela_normal_2
    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 1 ) THEN
            split_part(lista_parcelas_normais,''§'',9)
        ELSE '''' END
    )::varchar as valor_normal_2
    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 1 ) THEN
            split_part(lista_parcelas_normais,''§'',10)
        ELSE '''' END
    )::varchar as vencimento_normal_2
    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 1 ) THEN
            split_part(lista_parcelas_normais,''§'',11)
        ELSE '''' END
    )::varchar as numeracao_normal_2
    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 1 ) THEN
            split_part(lista_parcelas_normais,''§'',11)
        ELSE '''' END
    )::varchar as nosso_numero_normal_2
    , RTRIM( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 1 ) THEN
            split_part (
';
IF ( stPadraoCodBarra = 'febrabanCompBBAnexo5' ) THEN
    stSql := stSql ||'
        arrecadacao.geraCodigoBarraFebrabanCompensacaoBBAnexo5 (
            split_part(lista_parcelas_normais,''§'',13)::date,
            split_part(lista_parcelas_normais,''§'',9)::varchar,
            split_part(lista_parcelas_normais,''§'',11)::varchar,
            9,
            '||inNumConvenio||'
        )::varchar
    ';
ELSE
    stSql := stSql ||'
        arrecadacao.geraCodigoBarraFebraban (
            split_part(lista_parcelas_normais,''§'',13)::varchar,
            split_part(lista_parcelas_normais,''§'',9)::varchar,
            split_part(lista_parcelas_normais,''§'',11)::varchar,
            7,
            '||inCodFebraban||'
        )::varchar
    ';
END IF;

stSql := stSql ||'

                , ''§''
                , 1
            )
        ELSE '''' END
    )::varchar as codigo_barra_normal_2

    , LTRIM( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 1 ) THEN
            split_part ( 
';

IF ( stPadraoCodBarra = 'febrabanCompBBAnexo5' ) THEN
    stSql := stSql ||'
        arrecadacao.geraCodigoBarraFebrabanCompensacaoBBAnexo5 (
            split_part(lista_parcelas_normais,''§'',13)::date,
            split_part(lista_parcelas_normais,''§'',9)::varchar,
            split_part(lista_parcelas_normais,''§'',11)::varchar,
            9,
            '||inNumConvenio||'
        )::varchar
    ';
ELSE
    stSql := stSql ||'
        arrecadacao.geraCodigoBarraFebraban (
            split_part(lista_parcelas_normais,''§'',13)::varchar,
            split_part(lista_parcelas_normais,''§'',9)::varchar,
            split_part(lista_parcelas_normais,''§'',11)::varchar,
            7,
            '||inCodFebraban||'
        )::varchar
    ';
END IF;

stSql := stSql ||'
                , ''§''
                , 2
            )
        ELSE '''' END
    )::varchar as linha_digitavel_normal_2
    
--    , LTRIM( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 1 ) THEN
--            split_part (
--                arrecadacao.geraCodigoBarraFebraban (
--                      split_part(lista_parcelas_normais,''§'',13)::varchar
--                    , split_part(lista_parcelas_normais,''§'',9)::varchar
--                    , split_part(lista_parcelas_normais,''§'',11)::varchar
--                    , 7
--                    , '||inCodFebraban||'
--                )::varchar
--                , ''§''
--                , 2
--            )
--        ELSE '''' END
--    )::varchar as linha_digitavel_normal_2

    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 2 ) THEN
            split_part(lista_parcelas_normais,''§'',14)
        ELSE '''' END
    )::varchar as cod_parcela_normal_3
    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 2 ) THEN
            split_part(lista_parcelas_normais,''§'',15)
        ELSE '''' END
    )::varchar as valor_normal_3
    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 2 ) THEN
            split_part(lista_parcelas_normais,''§'',16)
        ELSE '''' END
    )::varchar as vencimento_normal_3
    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 2 ) THEN
            split_part(lista_parcelas_normais,''§'',17)
        ELSE '''' END
    )::varchar as numeracao_normal_3
    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 2 ) THEN
            split_part(lista_parcelas_normais,''§'',17)
        ELSE '''' END
    )::varchar as nosso_numero_normal_3
    , RTRIM( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 2 ) THEN
            split_part (
';

IF ( stPadraoCodBarra = 'febrabanCompBBAnexo5' ) THEN
    stSql := stSql ||'
        arrecadacao.geraCodigoBarraFebrabanCompensacaoBBAnexo5 (
            split_part(lista_parcelas_normais,''§'',19)::date,
            split_part(lista_parcelas_normais,''§'',15)::varchar,
            split_part(lista_parcelas_normais,''§'',17)::varchar,
            9,
            '||inNumConvenio||'
        )::varchar
    ';
ELSE
    stSql := stSql ||'
        arrecadacao.geraCodigoBarraFebraban (
            split_part(lista_parcelas_normais,''§'',19)::varchar,
            split_part(lista_parcelas_normais,''§'',15)::varchar,
            split_part(lista_parcelas_normais,''§'',17)::varchar,
            7,
            '||inCodFebraban||'
        )::varchar
    ';
END IF;

stSql := stSql ||'
                , ''§''
                , 1
            )
        ELSE '''' END
    )::varchar as codigo_barra_normal_3
    , LTRIM( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 2 ) THEN
            split_part (
';
IF ( stPadraoCodBarra = 'febrabanCompBBAnexo5' ) THEN
    stSql := stSql ||'
        arrecadacao.geraCodigoBarraFebrabanCompensacaoBBAnexo5 (
            split_part(lista_parcelas_normais,''§'',19)::date,
            split_part(lista_parcelas_normais,''§'',15)::varchar,
            split_part(lista_parcelas_normais,''§'',17)::varchar,
            9,
            '||inNumConvenio||'
        )::varchar
    ';
ELSE
    stSql := stSql ||'
        arrecadacao.geraCodigoBarraFebraban (
            split_part(lista_parcelas_normais,''§'',19)::varchar,
            split_part(lista_parcelas_normais,''§'',15)::varchar,
            split_part(lista_parcelas_normais,''§'',17)::varchar,
            7,
            '||inCodFebraban||'
        )::varchar
    ';
END IF;

stSql := stSql ||'
                , ''§''
                , 2
            )
        ELSE '''' END
    )::varchar as linha_digitavel_normal_3

    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 3 ) THEN
            split_part(lista_parcelas_normais,''§'',20)
        ELSE '''' END
    )::varchar as cod_parcela_normal_4
    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 3 ) THEN
            split_part(lista_parcelas_normais,''§'',21)
        ELSE '''' END
    )::varchar as valor_normal_4
    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 3 ) THEN
            split_part(lista_parcelas_normais,''§'',22)
        ELSE '''' END
    )::varchar as vencimento_normal_4
    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 3 ) THEN
            split_part(lista_parcelas_normais,''§'',23)
        ELSE '''' END
    )::varchar as numeracao_normal_4
    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 3 ) THEN
            split_part(lista_parcelas_normais,''§'',23)
        ELSE '''' END
    )::varchar as nosso_numero_normal_4
    , RTRIM( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 3 ) THEN
            split_part (
';

IF ( stPadraoCodBarra = 'febrabanCompBBAnexo5' ) THEN
    stSql := stSql ||'
        arrecadacao.geraCodigoBarraFebrabanCompensacaoBBAnexo5 (
            split_part(lista_parcelas_normais,''§'',25)::date,
            split_part(lista_parcelas_normais,''§'',21)::varchar,
            split_part(lista_parcelas_normais,''§'',23)::varchar,
            9,
            '||inNumConvenio||'
        )::varchar
    ';
ELSE
    stSql := stSql ||'
        arrecadacao.geraCodigoBarraFebraban (
            split_part(lista_parcelas_normais,''§'',25)::varchar,
            split_part(lista_parcelas_normais,''§'',21)::varchar,
            split_part(lista_parcelas_normais,''§'',23)::varchar,
            7,
            '||inCodFebraban||'
        )::varchar
    ';
END IF;

stSql := stSql ||'
                , ''§''
                , 1
            )
        ELSE '''' END
    )::varchar as codigo_barra_normal_4
    , LTRIM( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 3 ) THEN
            split_part (
';
IF ( stPadraoCodBarra = 'febrabanCompBBAnexo5' ) THEN
    stSql := stSql ||'
        arrecadacao.geraCodigoBarraFebrabanCompensacaoBBAnexo5 (
            split_part(lista_parcelas_normais,''§'',25)::date,
            split_part(lista_parcelas_normais,''§'',21)::varchar,
            split_part(lista_parcelas_normais,''§'',23)::varchar,
            9,
            '||inNumConvenio||'
        )::varchar
    ';
ELSE
    stSql := stSql ||'
        arrecadacao.geraCodigoBarraFebraban (
            split_part(lista_parcelas_normais,''§'',25)::varchar,
            split_part(lista_parcelas_normais,''§'',21)::varchar,
            split_part(lista_parcelas_normais,''§'',23)::varchar,
            7,
            '||inCodFebraban||'
        )::varchar
    ';
END IF;

stSql := stSql ||'
                , ''§''
                , 2
            )
        ELSE '''' END
    )::varchar as linha_digitavel_normal_4

    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 4 ) THEN
            split_part(lista_parcelas_normais,''§'',26)
        ELSE '''' END
    )::varchar as cod_parcela_normal_5
    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 4 ) THEN
            split_part(lista_parcelas_normais,''§'',27)
        ELSE '''' END
    )::varchar as valor_normal_5
    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 4 ) THEN
            split_part(lista_parcelas_normais,''§'',28)
        ELSE '''' END
    )::varchar as vencimento_normal_5
    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 4 ) THEN
            split_part(lista_parcelas_normais,''§'',29)
        ELSE '''' END
    )::varchar as numeracao_normal_5
    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 4 ) THEN
            split_part(lista_parcelas_normais,''§'',29)
        ELSE '''' END
    )::varchar as nosso_numero_normal_5
    , RTRIM( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 4 ) THEN
            split_part (
';

IF ( stPadraoCodBarra = 'febrabanCompBBAnexo5' ) THEN
    stSql := stSql ||'
        arrecadacao.geraCodigoBarraFebrabanCompensacaoBBAnexo5 (
            split_part(lista_parcelas_normais,''§'',31)::date,
            split_part(lista_parcelas_normais,''§'',27)::varchar,
            split_part(lista_parcelas_normais,''§'',29)::varchar,
            9,
            '||inNumConvenio||'
        )::varchar
    ';
ELSE
    stSql := stSql ||'
        arrecadacao.geraCodigoBarraFebraban (
            split_part(lista_parcelas_normais,''§'',31)::varchar,
            split_part(lista_parcelas_normais,''§'',27)::varchar,
            split_part(lista_parcelas_normais,''§'',29)::varchar,
            7,
            '||inCodFebraban||'
        )::varchar
    ';
END IF;

stSql := stSql ||'
                , ''§''
                , 1
            )
        ELSE '''' END
    )::varchar as codigo_barra_normal_5
    , LTRIM( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 4 ) THEN
            split_part (
';
IF ( stPadraoCodBarra = 'febrabanCompBBAnexo5' ) THEN
    stSql := stSql ||'
        arrecadacao.geraCodigoBarraFebrabanCompensacaoBBAnexo5 (
            split_part(lista_parcelas_normais,''§'',31)::date,
            split_part(lista_parcelas_normais,''§'',27)::varchar,
            split_part(lista_parcelas_normais,''§'',29)::varchar,
            9,
            '||inNumConvenio||'
        )::varchar
    ';
ELSE
    stSql := stSql ||'
        arrecadacao.geraCodigoBarraFebraban (
            split_part(lista_parcelas_normais,''§'',31)::varchar,
            split_part(lista_parcelas_normais,''§'',27)::varchar,
            split_part(lista_parcelas_normais,''§'',29)::varchar,
            7,
            '||inCodFebraban||'
        )::varchar
    ';
END IF;

stSql := stSql ||'
                , ''§''
                , 2
            )
        ELSE '''' END
    )::varchar as linha_digitavel_normal_5

    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 5 ) THEN
            split_part(lista_parcelas_normais,''§'',32)
        ELSE '''' END
    )::varchar as cod_parcela_normal_6
    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 5 ) THEN
            split_part(lista_parcelas_normais,''§'',33)
        ELSE '''' END
    )::varchar as valor_normal_6
    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 5 ) THEN
            split_part(lista_parcelas_normais,''§'',34)
        ELSE '''' END
    )::varchar as vencimento_normal_6
    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 5 ) THEN
            split_part(lista_parcelas_normais,''§'',35)
        ELSE '''' END
    )::varchar as numeracao_normal_6
    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 5 ) THEN
            split_part(lista_parcelas_normais,''§'',35)
        ELSE '''' END
    )::varchar as nosso_numero_normal_6
    , RTRIM( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 5 ) THEN
            split_part (
';

IF ( stPadraoCodBarra = 'febrabanCompBBAnexo5' ) THEN
    stSql := stSql ||'
        arrecadacao.geraCodigoBarraFebrabanCompensacaoBBAnexo5 (
            split_part(lista_parcelas_normais,''§'',37)::date,
            split_part(lista_parcelas_normais,''§'',33)::varchar,
            split_part(lista_parcelas_normais,''§'',35)::varchar,
            9,
            '||inNumConvenio||'
        )::varchar
    ';
ELSE
    stSql := stSql ||'
        arrecadacao.geraCodigoBarraFebraban (
            split_part(lista_parcelas_normais,''§'',37)::varchar,
            split_part(lista_parcelas_normais,''§'',33)::varchar,
            split_part(lista_parcelas_normais,''§'',35)::varchar,
            7,
            '||inCodFebraban||'
        )::varchar
    ';
END IF;

stSql := stSql ||'
                , ''§''
                , 1
            )
        ELSE '''' END
    )::varchar as codigo_barra_normal_6
    , LTRIM( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 5 ) THEN
            split_part (
';

IF ( stPadraoCodBarra = 'febrabanCompBBAnexo5' ) THEN
    stSql := stSql ||'
        arrecadacao.geraCodigoBarraFebrabanCompensacaoBBAnexo5 (
            split_part(lista_parcelas_normais,''§'',37)::date,
            split_part(lista_parcelas_normais,''§'',33)::varchar,
            split_part(lista_parcelas_normais,''§'',35)::varchar,
            9,
            '||inNumConvenio||'
        )::varchar
    ';
ELSE
    stSql := stSql ||'
        arrecadacao.geraCodigoBarraFebraban (
            split_part(lista_parcelas_normais,''§'',37)::varchar,
            split_part(lista_parcelas_normais,''§'',33)::varchar,
            split_part(lista_parcelas_normais,''§'',35)::varchar,
            7,
            '||inCodFebraban||'
        )::varchar
    ';
END IF;

stSql := stSql ||'
                , ''§''
                , 2
            )
        ELSE '''' END
    )::varchar as linha_digitavel_normal_6

    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 6 ) THEN
            split_part(lista_parcelas_normais,''§'',38)
        ELSE '''' END
    )::varchar as cod_parcela_normal_7
    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 6 ) THEN
            split_part(lista_parcelas_normais,''§'',39)
        ELSE '''' END
    )::varchar as valor_normal_7
    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 6 ) THEN
            split_part(lista_parcelas_normais,''§'',40)
        ELSE '''' END
    )::varchar as vencimento_normal_7
    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 6 ) THEN
            split_part(lista_parcelas_normais,''§'',41)
        ELSE '''' END
    )::varchar as numeracao_normal_7
    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 6 ) THEN
            split_part(lista_parcelas_normais,''§'',41)
        ELSE '''' END
    )::varchar as nosso_numero_normal_7
    , RTRIM( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 6 ) THEN
            split_part (
';

IF ( stPadraoCodBarra = 'febrabanCompBBAnexo5' ) THEN
    stSql := stSql ||'
        arrecadacao.geraCodigoBarraFebrabanCompensacaoBBAnexo5 (
            split_part(lista_parcelas_normais,''§'',43)::date,
            split_part(lista_parcelas_normais,''§'',39)::varchar,
            split_part(lista_parcelas_normais,''§'',41)::varchar,
            9,
            '||inNumConvenio||'
        )::varchar
    ';
ELSE
    stSql := stSql ||'
        arrecadacao.geraCodigoBarraFebraban (
            split_part(lista_parcelas_normais,''§'',43)::varchar,
            split_part(lista_parcelas_normais,''§'',39)::varchar,
            split_part(lista_parcelas_normais,''§'',41)::varchar,
            7,
            '||inCodFebraban||'
        )::varchar
    ';
END IF;

stSql := stSql ||'
                , ''§''
                , 1
            )
        ELSE '''' END
    )::varchar as codigo_barra_normal_7
    , LTRIM( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 6 ) THEN
            split_part (
';

IF ( stPadraoCodBarra = 'febrabanCompBBAnexo5' ) THEN
    stSql := stSql ||'
        arrecadacao.geraCodigoBarraFebrabanCompensacaoBBAnexo5 (
            split_part(lista_parcelas_normais,''§'',43)::date,
            split_part(lista_parcelas_normais,''§'',39)::varchar,
            split_part(lista_parcelas_normais,''§'',41)::varchar,
            9,
            '||inNumConvenio||'
        )::varchar
    ';
ELSE
    stSql := stSql ||'
        arrecadacao.geraCodigoBarraFebraban (
            split_part(lista_parcelas_normais,''§'',43)::varchar,
            split_part(lista_parcelas_normais,''§'',39)::varchar,
            split_part(lista_parcelas_normais,''§'',41)::varchar,
            7,
            '||inCodFebraban||'
        )::varchar
    ';
END IF;

stSql := stSql ||'
                , ''§''
                , 2
            )
        ELSE '''' END
    )::varchar as linha_digitavel_normal_7

    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 7 ) THEN
            split_part(lista_parcelas_normais,''§'',44)
        ELSE '''' END
    )::varchar as cod_parcela_normal_8
    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 7 ) THEN
            split_part(lista_parcelas_normais,''§'',45)
        ELSE '''' END
    )::varchar as valor_normal_8
    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 7 ) THEN
            split_part(lista_parcelas_normais,''§'',46)
        ELSE '''' END
    )::varchar as vencimento_normal_8
    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 7 ) THEN
            split_part(lista_parcelas_normais,''§'',47)
        ELSE '''' END
    )::varchar as numeracao_normal_8
    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 7 ) THEN
            split_part(lista_parcelas_normais,''§'',47)
        ELSE '''' END
    )::varchar as nosso_numero_normal_8
    , RTRIM( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 7 ) THEN
            split_part (
';

IF ( stPadraoCodBarra = 'febrabanCompBBAnexo5' ) THEN
    stSql := stSql ||'
        arrecadacao.geraCodigoBarraFebrabanCompensacaoBBAnexo5 (
            split_part(lista_parcelas_normais,''§'',49)::date,
            split_part(lista_parcelas_normais,''§'',45)::varchar,
            split_part(lista_parcelas_normais,''§'',47)::varchar,
            9,
            '||inNumConvenio||'
        )::varchar
    ';
ELSE
    stSql := stSql ||'
        arrecadacao.geraCodigoBarraFebraban (
            split_part(lista_parcelas_normais,''§'',49)::varchar,
            split_part(lista_parcelas_normais,''§'',45)::varchar,
            split_part(lista_parcelas_normais,''§'',47)::varchar,
            7,
            '||inCodFebraban||'
        )::varchar
    ';
END IF;

stSql := stSql ||'
                , ''§''
                , 1
            )
        ELSE '''' END
    )::varchar as codigo_barra_normal_8
    , LTRIM( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 7 ) THEN
            split_part (
';

IF ( stPadraoCodBarra = 'febrabanCompBBAnexo5' ) THEN
    stSql := stSql ||'
        arrecadacao.geraCodigoBarraFebrabanCompensacaoBBAnexo5 (
            split_part(lista_parcelas_normais,''§'',49)::date,
            split_part(lista_parcelas_normais,''§'',45)::varchar,
            split_part(lista_parcelas_normais,''§'',47)::varchar,
            9,
            '||inNumConvenio||'
        )::varchar
    ';
ELSE
    stSql := stSql ||'
        arrecadacao.geraCodigoBarraFebraban (
            split_part(lista_parcelas_normais,''§'',49)::varchar,
            split_part(lista_parcelas_normais,''§'',45)::varchar,
            split_part(lista_parcelas_normais,''§'',47)::varchar,
            7,
            '||inCodFebraban||'
        )::varchar
    ';
END IF;

stSql := stSql ||'
                , ''§''
                , 2
            )
        ELSE '''' END
    )::varchar as linha_digitavel_normal_8

    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 8 ) THEN
            split_part(lista_parcelas_normais,''§'',50)
        ELSE '''' END
    )::varchar as cod_parcela_normal_9
    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 8 ) THEN
            split_part(lista_parcelas_normais,''§'',51)
        ELSE '''' END
    )::varchar as valor_normal_9
    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 8 ) THEN
            split_part(lista_parcelas_normais,''§'',52)
        ELSE '''' END
    )::varchar as vencimento_normal_9
    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 8 ) THEN
            split_part(lista_parcelas_normais,''§'',53)
        ELSE '''' END
    )::varchar as numeracao_normal_9
    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 8 ) THEN
            split_part(lista_parcelas_normais,''§'',53)
        ELSE '''' END
    )::varchar as nosso_numero_normal_9
    , RTRIM( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 8 ) THEN
            split_part (
';

IF ( stPadraoCodBarra = 'febrabanCompBBAnexo5' ) THEN
    stSql := stSql ||'
        arrecadacao.geraCodigoBarraFebrabanCompensacaoBBAnexo5 (
            split_part(lista_parcelas_normais,''§'',55)::date,
            split_part(lista_parcelas_normais,''§'',51)::varchar,
            split_part(lista_parcelas_normais,''§'',53)::varchar,
            9,
            '||inNumConvenio||'
        )::varchar
    ';
ELSE
    stSql := stSql ||'
        arrecadacao.geraCodigoBarraFebraban (
            split_part(lista_parcelas_normais,''§'',55)::varchar,
            split_part(lista_parcelas_normais,''§'',51)::varchar,
            split_part(lista_parcelas_normais,''§'',53)::varchar,
            7,
            '||inCodFebraban||'
        )::varchar
    ';
END IF;

stSql := stSql ||'
                , ''§''
                , 1
            )
        ELSE '''' END
    )::varchar as codigo_barra_normal_9
    , LTRIM( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 8 ) THEN
            split_part (
';
IF ( stPadraoCodBarra = 'febrabanCompBBAnexo5' ) THEN
    stSql := stSql ||'
        arrecadacao.geraCodigoBarraFebrabanCompensacaoBBAnexo5 (
            split_part(lista_parcelas_normais,''§'',55)::date,
            split_part(lista_parcelas_normais,''§'',51)::varchar,
            split_part(lista_parcelas_normais,''§'',53)::varchar,
            9,
            '||inNumConvenio||'
        )::varchar
    ';
ELSE
    stSql := stSql ||'
        arrecadacao.geraCodigoBarraFebraban (
            split_part(lista_parcelas_normais,''§'',55)::varchar,
            split_part(lista_parcelas_normais,''§'',51)::varchar,
            split_part(lista_parcelas_normais,''§'',53)::varchar,
            7,
            '||inCodFebraban||'
        )::varchar
    ';
END IF;

stSql := stSql ||'
                , ''§''
                , 2
            )
        ELSE '''' END
    )::varchar as linha_digitavel_normal_9

    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 9 ) THEN
            split_part(lista_parcelas_normais,''§'',56)
        ELSE '''' END
    )::varchar as cod_parcela_normal_10
    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 9 ) THEN
            split_part(lista_parcelas_normais,''§'',57)
        ELSE '''' END
    )::varchar as valor_normal_10
    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 9 ) THEN
            split_part(lista_parcelas_normais,''§'',58)
        ELSE '''' END
    )::varchar as vencimento_normal_10
    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 9 ) THEN
            split_part(lista_parcelas_normais,''§'',59)
        ELSE '''' END
    )::varchar as numeracao_normal_10
    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 9 ) THEN
            split_part(lista_parcelas_normais,''§'',59)
        ELSE '''' END
    )::varchar as nosso_numero_normal_10
    , RTRIM( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 9 ) THEN
            split_part (
';

IF ( stPadraoCodBarra = 'febrabanCompBBAnexo5' ) THEN
    stSql := stSql ||'
        arrecadacao.geraCodigoBarraFebrabanCompensacaoBBAnexo5 (
            split_part(lista_parcelas_normais,''§'',61)::date,
            split_part(lista_parcelas_normais,''§'',57)::varchar,
            split_part(lista_parcelas_normais,''§'',59)::varchar,
            9,
            '||inNumConvenio||'
        )::varchar
    ';
ELSE
    stSql := stSql ||'
        arrecadacao.geraCodigoBarraFebraban (
            split_part(lista_parcelas_normais,''§'',61)::varchar,
            split_part(lista_parcelas_normais,''§'',57)::varchar,
            split_part(lista_parcelas_normais,''§'',59)::varchar,
            7,
            '||inCodFebraban||'
        )::varchar
    ';
END IF;

stSql := stSql ||'
                , ''§''
                , 1
            )
        ELSE '''' END
    )::varchar as codigo_barra_normal_10
    , LTRIM( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 9 ) THEN
            split_part (
';
IF ( stPadraoCodBarra = 'febrabanCompBBAnexo5' ) THEN
    stSql := stSql ||'
        arrecadacao.geraCodigoBarraFebrabanCompensacaoBBAnexo5 (
            split_part(lista_parcelas_normais,''§'',61)::date,
            split_part(lista_parcelas_normais,''§'',57)::varchar,
            split_part(lista_parcelas_normais,''§'',59)::varchar,
            9,
            '||inNumConvenio||'
        )::varchar
    ';
ELSE
    stSql := stSql ||'
        arrecadacao.geraCodigoBarraFebraban (
            split_part(lista_parcelas_normais,''§'',61)::varchar,
            split_part(lista_parcelas_normais,''§'',57)::varchar,
            split_part(lista_parcelas_normais,''§'',59)::varchar,
            7,
            '||inCodFebraban||'
        )::varchar
    ';
END IF;

stSql := stSql ||'
                , ''§''
                , 2
            )
        ELSE '''' END
    )::varchar as linha_digitavel_normal_10

    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 10 ) THEN
            split_part(lista_parcelas_normais,''§'',62)
        ELSE '''' END
    )::varchar as cod_parcela_normal_11
    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 10 ) THEN
            split_part(lista_parcelas_normais,''§'',63)
        ELSE '''' END
    )::varchar as valor_normal_11
    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 10 ) THEN
            split_part(lista_parcelas_normais,''§'',64)
        ELSE '''' END
    )::varchar as vencimento_normal_11
    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 10 ) THEN
            split_part(lista_parcelas_normais,''§'',65)
        ELSE '''' END
    )::varchar as numeracao_normal_11
    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 10 ) THEN
            split_part(lista_parcelas_normais,''§'',65)
        ELSE '''' END
    )::varchar as nosso_numero_normal_11
    , RTRIM( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 10 ) THEN
            split_part (
';

IF ( stPadraoCodBarra = 'febrabanCompBBAnexo5' ) THEN
    stSql := stSql ||'
        arrecadacao.geraCodigoBarraFebrabanCompensacaoBBAnexo5 (
            split_part(lista_parcelas_normais,''§'',67)::date,
            split_part(lista_parcelas_normais,''§'',63)::varchar,
            split_part(lista_parcelas_normais,''§'',65)::varchar,
            9,
            '||inNumConvenio||'
        )::varchar
    ';
ELSE
    stSql := stSql ||'
        arrecadacao.geraCodigoBarraFebraban (
            split_part(lista_parcelas_normais,''§'',67)::varchar,
            split_part(lista_parcelas_normais,''§'',63)::varchar,
            split_part(lista_parcelas_normais,''§'',65)::varchar,
            7,
            '||inCodFebraban||'
        )::varchar
    ';
END IF;

stSql := stSql ||'
                , ''§''
                , 1
            )
        ELSE '''' END
    )::varchar as codigo_barra_normal_11
    , LTRIM( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 10 ) THEN
            split_part (
';

IF ( stPadraoCodBarra = 'febrabanCompBBAnexo5' ) THEN
    stSql := stSql ||'
        arrecadacao.geraCodigoBarraFebrabanCompensacaoBBAnexo5 (
            split_part(lista_parcelas_normais,''§'',67)::date,
            split_part(lista_parcelas_normais,''§'',63)::varchar,
            split_part(lista_parcelas_normais,''§'',65)::varchar,
            9,
            '||inNumConvenio||'
        )::varchar
    ';
ELSE
    stSql := stSql ||'
        arrecadacao.geraCodigoBarraFebraban (
            split_part(lista_parcelas_normais,''§'',67)::varchar,
            split_part(lista_parcelas_normais,''§'',63)::varchar,
            split_part(lista_parcelas_normais,''§'',65)::varchar,
            7,
            '||inCodFebraban||'
        )::varchar
    ';
END IF;

stSql := stSql ||'
                , ''§''
                , 2
            )
        ELSE '''' END
    )::varchar as linha_digitavel_normal_11

    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 11 ) THEN
            split_part(lista_parcelas_normais,''§'',68)
        ELSE '''' END
    )::varchar as cod_parcela_normal_12
    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 11 ) THEN
            split_part(lista_parcelas_normais,''§'',69)
        ELSE '''' END
    )::varchar as valor_normal_12
    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 11 ) THEN
            split_part(lista_parcelas_normais,''§'',70)
        ELSE '''' END
    )::varchar as vencimento_normal_12
    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 11 ) THEN
            split_part(lista_parcelas_normais,''§'',71)
        ELSE '''' END
    )::varchar as numeracao_normal_12
    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 11 ) THEN
            split_part(lista_parcelas_normais,''§'',71)
        ELSE '''' END
    )::varchar as nosso_numero_normal_12
    , RTRIM( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 11 ) THEN
            split_part (
';

IF ( stPadraoCodBarra = 'febrabanCompBBAnexo5' ) THEN
    stSql := stSql ||'
        arrecadacao.geraCodigoBarraFebrabanCompensacaoBBAnexo5 (
            split_part(lista_parcelas_normais,''§'',73)::date,
            split_part(lista_parcelas_normais,''§'',69)::varchar,
            split_part(lista_parcelas_normais,''§'',71)::varchar,
            9,
            '||inNumConvenio||'
        )::varchar
    ';
ELSE
    stSql := stSql ||'
        arrecadacao.geraCodigoBarraFebraban (
            split_part(lista_parcelas_normais,''§'',73)::varchar,
            split_part(lista_parcelas_normais,''§'',69)::varchar,
            split_part(lista_parcelas_normais,''§'',71)::varchar,
            7,
            '||inCodFebraban||'
        )::varchar
    ';
END IF;

stSql := stSql ||'
                , ''§''
                , 1
            )
        ELSE '''' END
    )::varchar as codigo_barra_normal_12
    , LTRIM( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 11 ) THEN
            split_part (
';

IF ( stPadraoCodBarra = 'febrabanCompBBAnexo5' ) THEN
    stSql := stSql ||'
        arrecadacao.geraCodigoBarraFebrabanCompensacaoBBAnexo5 (
            split_part(lista_parcelas_normais,''§'',73)::date,
            split_part(lista_parcelas_normais,''§'',69)::varchar,
            split_part(lista_parcelas_normais,''§'',71)::varchar,
            9,
            '||inNumConvenio||'
        )::varchar
    ';
ELSE
    stSql := stSql ||'
        arrecadacao.geraCodigoBarraFebraban (
            split_part(lista_parcelas_normais,''§'',73)::varchar,
            split_part(lista_parcelas_normais,''§'',69)::varchar,
            split_part(lista_parcelas_normais,''§'',71)::varchar,
            7,
            '||inCodFebraban||'
        )::varchar
    ';
END IF;

stSql := stSql ||'
                , ''§''
                , 2
            )
        ELSE '''' END
    )::varchar as linha_digitavel_normal_12

-- FINAL ALTERACOES

    , split_part(lista_creditos,''§'',1)::varchar as soma_creditos

    , split_part(lista_creditos,''§'',2)::varchar as cod_credito_1
    , split_part(lista_creditos,''§'',3)::varchar as descricao_1
    , split_part(lista_creditos,''§'',4)::varchar as valor_1

    , split_part(lista_creditos,''§'',5)::varchar as cod_credito_2
    , split_part(lista_creditos,''§'',6)::varchar as descricao_2
    , split_part(lista_creditos,''§'',7)::varchar as valor_2

    , split_part(lista_creditos,''§'',8)::varchar as cod_credito_3
    , split_part(lista_creditos,''§'',9)::varchar as descricao_3
    , split_part(lista_creditos,''§'',10)::varchar as valor_3

    , split_part(lista_creditos,''§'',11)::varchar as cod_credito_4
    , split_part(lista_creditos,''§'',12)::varchar as descricao_4
    , split_part(lista_creditos,''§'',13)::varchar as valor_4

    , split_part(lista_creditos,''§'',14)::varchar as cod_credito_5
    , split_part(lista_creditos,''§'',15)::varchar as descricao_5
    , split_part(lista_creditos,''§'',16)::varchar as valor_5

    , split_part(lista_creditos,''§'',17)::varchar as cod_credito_6
    , split_part(lista_creditos,''§'',18)::varchar as descricao_6
    , split_part(lista_creditos,''§'',19)::varchar as valor_6

    , split_part(lista_creditos,''§'',20)::varchar as cod_credito_7
    , split_part(lista_creditos,''§'',21)::varchar as descricao_7
    , split_part(lista_creditos,''§'',22)::varchar as valor_7

    , imobiliario.fn_busca_localizacao_primeiro_nivel( split_part(infos,''§'',3) ) as localizacao_primeiro_nivel
    , lista_atividades
    , lista_responsaveis

    , ''''::varchar as atributo_1
    , ''''::varchar as atributo_2
    , ''''::varchar as atributo_3
    , ''''::varchar as atributo_4
    , ''''::varchar as atributo_5
    , ''''::varchar as atributo_6
    , ''''::varchar as atributo_7
    , ''''::varchar as atributo_8
    , ''''::varchar as atributo_9
    , ''''::varchar as atributo_10
    , ''''::varchar as atributo_11
    , ''''::varchar as atributo_12
    , ''''::varchar as atributo_13
    , ''''::varchar as atributo_14
    , ''''::varchar as atributo_15

    -------------------------------------------------------- LOTE
    , ( SELECT atributo_lote_urbano_valor.valor
          FROM imobiliario.atributo_lote_urbano_valor
    INNER JOIN (SELECT cod_lote, cod_atributo, max(timestamp) AS timestamp FROM imobiliario.atributo_lote_urbano_valor GROUP BY cod_lote, cod_atributo) AS max_timestamp 
            ON max_timestamp.timestamp    = atributo_lote_urbano_valor.timestamp 
           AND max_timestamp.cod_atributo = atributo_lote_urbano_valor.cod_atributo
           AND max_timestamp.cod_lote     = atributo_lote_urbano_valor.cod_lote
           
  INNER JOIN imobiliario.imovel_lote 
          ON imovel_lote.cod_lote = atributo_lote_urbano_valor.cod_lote
         AND imovel_lote.inscricao_municipal = split_part(infos,''§'',1)::int
       WHERE atributo_lote_urbano_valor.cod_atributo = 7  AND atributo_lote_urbano_valor.cod_lote = imovel_lote.cod_lote
    )::varchar as lote
    
       
    -------------------------------------------------------- QUADRA
    , ( SELECT atributo_lote_urbano_valor.valor
          FROM imobiliario.atributo_lote_urbano_valor
    INNER JOIN (SELECT cod_lote, cod_atributo, max(timestamp) AS timestamp FROM imobiliario.atributo_lote_urbano_valor GROUP BY cod_lote, cod_atributo) AS max_timestamp 
            ON max_timestamp.timestamp    = atributo_lote_urbano_valor.timestamp 
           AND max_timestamp.cod_atributo = atributo_lote_urbano_valor.cod_atributo
           AND max_timestamp.cod_lote     = atributo_lote_urbano_valor.cod_lote
           
  INNER JOIN imobiliario.imovel_lote 
          ON imovel_lote.cod_lote = atributo_lote_urbano_valor.cod_lote
         AND imovel_lote.inscricao_municipal = split_part(infos,''§'',1)::int
       WHERE atributo_lote_urbano_valor.cod_atributo = 5  AND atributo_lote_urbano_valor.cod_lote = imovel_lote.cod_lote           
    )::varchar as quadra

    -- DADOS DA EMPRESA
    , tudo.inscricao_economica::varchar
    , to_char(tudo.data_abertura,''DD/MM/YYYY'')::varchar
    , tudo.numcgm_responsavel::varchar
    , tudo.nome_responsavel::varchar
    , tudo.cod_natureza::varchar
    , tudo.natureza_juridica::varchar
    , tudo.cod_categoria::varchar
    , tudo.categoria::varchar
    , tudo.cod_atividade_principal::varchar
    , tudo.descricao_atividade_principal::varchar
    , to_char(tudo.data_inicio,''DD/MM/YYYY'')::varchar

    -- RELACAO SOCIOS
    , SPLIT_PART(tudo.socios,''§'',1)::VARCHAR AS numcgm_socio_1
    , SPLIT_PART(tudo.socios,''§'',2)::VARCHAR AS nome_socio_1
    , SPLIT_PART(tudo.socios,''§'',3)::VARCHAR AS quota_socio_1

    , SPLIT_PART(tudo.socios,''§'',4)::VARCHAR AS numcgm_socio_2
    , SPLIT_PART(tudo.socios,''§'',5)::VARCHAR AS nome_socio_2
    , SPLIT_PART(tudo.socios,''§'',6)::VARCHAR AS quota_socio_2

    , SPLIT_PART(tudo.socios,''§'',7)::VARCHAR AS numcgm_socio_3
    , SPLIT_PART(tudo.socios,''§'',8)::VARCHAR AS nome_socio_3
    , SPLIT_PART(tudo.socios,''§'',9)::VARCHAR AS quota_socio_3

    , SPLIT_PART(tudo.socios,''§'',10)::VARCHAR AS numcgm_socio_4
    , SPLIT_PART(tudo.socios,''§'',11)::VARCHAR AS nome_socio_4
    , SPLIT_PART(tudo.socios,''§'',12)::VARCHAR AS quota_socio_4

    , SPLIT_PART(tudo.socios,''§'',13)::VARCHAR AS numcgm_socio_5
    , SPLIT_PART(tudo.socios,''§'',14)::VARCHAR AS nome_socio_5
    , SPLIT_PART(tudo.socios,''§'',15)::VARCHAR AS quota_socio_5
    
FROM
(
    SELECT aic2.inscricao
         , aic2.exercicio::int
         , al.cod_lancamento
         , arrecadacao.fn_atualiza_data_vencimento ( al.vencimento ) as lanc_venc
         , al.valor as lanc_valor
         , aic2.cod_grupo
         , aic2.descricao
         , cgm.numcgm
         , cgm.nom_cgm
         , pjcgm.nom_fantasia
         , pjcgm.cnpj
         
         , arrecadacao.fn_lista_parcelas_unicas( al.cod_lancamento ) as lista_parcelas_unicas
         , arrecadacao.fn_lista_parcelas_normais( al.cod_lancamento ) as lista_parcelas_normais
         
         , arrecadacao.fn_lista_creditos_lancamento( al.cod_lancamento, aic2.cod_grupo, aic2.exercicio ) as lista_creditos
         
         , economico.fn_lista_sociedade_grafica( cec.inscricao_economica ) as lista_responsaveis
         , economico.fn_lista_atividades_grafica( cec.inscricao_economica ) as lista_atividades
         
         , ( case when edei.inscricao_economica is not null then
                case when edef.inscricao_economica is not null and edef.timestamp > edei.timestamp then
                    economico.fn_busca_domicilio_fiscal( cec.inscricao_economica )
                else
                    economico.fn_busca_domicilio_informado( cec.inscricao_economica )
                end
            else
                economico.fn_busca_domicilio_fiscal( cec.inscricao_economica )
            end
          ) as endereco

         , ( case when edei.inscricao_economica is not null then
                case when edef.inscricao_economica is not null and edef.timestamp > edei.timestamp then
                    imobiliario.fn_busca_endereco_correspondencia ( edef.inscricao_municipal  )
                else
                    null
                end
            else
                imobiliario.fn_busca_endereco_correspondencia ( edef.inscricao_municipal  )
            end
         ) as endereco_c

         , ( case when edef.inscricao_economica is not null then
                (
                SELECT
                    IML.inscricao_municipal||''§''||IML.cod_lote||''§''||ILOC.codigo_composto||''§''||ILOC.nom_localizacao
                FROM

                    imobiliario.imovel_lote as IML

                    INNER JOIN imobiliario.lote_localizacao as ILLO
                    ON ILLO.cod_lote = IML.cod_lote

                    INNER JOIN imobiliario.localizacao as ILOC
                    ON ILOC.cod_localizacao = ILLO.cod_localizacao

                WHERE IML.inscricao_municipal = edef.inscricao_municipal
                )::varchar
            end
        ) as infos

        -- DADOS DA EMPRESA
        , aic2.inscricao as inscricao_economica
        , (
                SELECT dt_abertura
                  FROM economico.cadastro_economico ce
                 WHERE ce.inscricao_economica = cec.inscricao_economica
          ) as data_abertura

        -- RESPONSAVEL
        , (
                SELECT numcgm
                  FROM economico.cadastro_econ_resp_tecnico cert
                 WHERE cert.inscricao_economica = cec.inscricao_economica
              ORDER BY cert.sequencia DESC LIMIT 1
          ) as numcgm_responsavel
        , (
                SELECT nom_cgm
                  FROM economico.cadastro_econ_resp_tecnico cert, sw_cgm
                 WHERE cert.inscricao_economica = cec.inscricao_economica AND sw_cgm.numcgm = cert.numcgm
              ORDER BY cert.sequencia DESC LIMIT 1
          ) as nome_responsavel

        -- NATUREZA
        , (
                SELECT cod_natureza
                  FROM economico.empresa_direito_natureza_juridica ednj
                 WHERE ednj.inscricao_economica = CEED.inscricao_economica
              ORDER BY ednj.timestamp DESC LIMIT 1
          ) as cod_natureza
        , (
               SELECT nom_natureza
                 FROM economico.natureza_juridica nj,
                      economico.empresa_direito_natureza_juridica ednj
                WHERE ednj.inscricao_economica = CEED.inscricao_economica
                  AND nj.cod_natureza = ednj.cod_natureza
             ORDER BY ednj.timestamp DESC LIMIT 1 
          ) as natureza_juridica

        -- CATEGORIA
        , CEED.cod_categoria
        , (
                SELECT nom_categoria
                  FROM economico.categoria
                 WHERE categoria.cod_categoria = CEED.cod_categoria
          ) as categoria
        
        -- ATIVIDADE PRINCIPAL
        , (
                SELECT atividade.cod_estrutural
                  FROM economico.atividade_cadastro_economico ace
            INNER JOIN economico.atividade
                    ON atividade.cod_atividade = ace.cod_atividade
                 WHERE ace.inscricao_economica = cec.inscricao_economica
                   AND ace.principal = TRUE
              ORDER BY ocorrencia_atividade DESC LIMIT 1
          ) as cod_atividade_principal
        , (
                SELECT a.nom_atividade
                  FROM economico.atividade_cadastro_economico ace, economico.atividade a 
                 WHERE ace.inscricao_economica = cec.inscricao_economica
                   AND ace.principal = TRUE
                   AND a.cod_atividade = ace.cod_atividade

              ORDER BY ocorrencia_atividade DESC LIMIT 1
          ) as descricao_atividade_principal
        , (
                SELECT ace.dt_inicio
                  FROM economico.atividade_cadastro_economico ace
                 WHERE ace.inscricao_economica = cec.inscricao_economica
                   AND ace.principal = TRUE
              ORDER BY ocorrencia_atividade DESC LIMIT 1
          ) as data_inicio
        
        -- SOCIOS
        , (
         SELECT array_to_string(ARRAY(SELECT numcgm || ''§'' || nom_cgm || ''§'' || quota_socio from
                (
                SELECT  DISTINCT
                            s.numcgm  AS numcgm
                         ,  nom_cgm
                         ,  (
                                SELECT  s2.timestamp
                                  FROM  economico.sociedade s2
                                 WHERE  s2.numcgm = s.numcgm
                                   AND  s2.inscricao_economica = s.inscricao_economica
                              ORDER BY  timestamp DESC LIMIT 1
                            ) as timestamp
                         ,  (
                                SELECT  quota_socio
                                  FROM  economico.sociedade s2
                                 WHERE  s2.numcgm = s.numcgm
                                   AND  s2.inscricao_economica = s.inscricao_economica
                              ORDER BY  timestamp DESC LIMIT 1
                            ) as quota_socio
                            
                      FROM  economico.sociedade s
                INNER JOIN  sw_cgm
                        ON  sw_cgm.numcgm = s.numcgm
                     WHERE  s.inscricao_economica = CEED.inscricao_economica
                  ORDER BY  timestamp LIMIT 5
                ) AS socios), ''§'')
        ) as socios
        
             FROM arrecadacao.cadastro_economico_calculo as cec

       INNER JOIN (

            SELECT cec.inscricao_economica as inscricao
                 , max(ac.cod_calculo) as cod_calculo
                 '||stColunasOrigem||'
            FROM arrecadacao.cadastro_economico_calculo as cec

                '||stFromOrigem||'

                INNER JOIN arrecadacao.calculo as ac
                        ON ac.cod_calculo = cec.cod_calculo

                '||stFiltroCredito||'
        
            GROUP BY cec.inscricao_economica, ac.exercicio '|| stGroupByOrigem ||'

                ) AS aic2
               ON aic2.inscricao = cec.inscricao_economica
              AND aic2.cod_calculo = cec.cod_calculo

       INNER JOIN arrecadacao.lancamento_calculo as alc
               ON alc.cod_calculo = cec.cod_calculo

       INNER JOIN arrecadacao.lancamento as al
               ON al.cod_lancamento = alc.cod_lancamento

       INNER JOIN  (
                        SELECT  ccgm.numcgm, ccgm.cod_calculo, cgm.nom_cgm
                          FROM  arrecadacao.calculo_cgm as ccgm
                    INNER JOIN  sw_cgm as cgm ON cgm.numcgm = ccgm.numcgm
                ) AS cgm
               ON cgm.cod_calculo = cec.cod_calculo

       INNER JOIN sw_cgm_pessoa_juridica as pjcgm
               ON pjcgm.numcgm = cgm.numcgm

      LEFT JOIN economico.domicilio_fiscal as edef
             ON edef.inscricao_economica = cec.inscricao_economica
            AND edef.timestamp = (
                                      SELECT MAX(timestamp)
                                        FROM economico.domicilio_fiscal
                                       WHERE domicilio_fiscal.inscricao_economica = cec.inscricao_economica
                                 )

      LEFT JOIN economico.domicilio_informado as edei
             ON edei.inscricao_economica = cec.inscricao_economica
            AND edei.timestamp = (
                                      SELECT MAX(timestamp)
                                        FROM economico.domicilio_informado
                                       WHERE domicilio_informado.inscricao_economica = cec.inscricao_economica
                                 )

        LEFT JOIN economico.cadastro_economico_empresa_direito CEED
               ON CEED.inscricao_economica = cec.inscricao_economica

        LEFT JOIN economico.cadastro_economico_empresa_fato CEEF
               ON CEEF.inscricao_economica = cec.inscricao_economica

        LEFT JOIN economico.cadastro_economico_autonomo CEA
               ON CEA.inscricao_economica = cec.inscricao_economica
    ';

    stSql := stSql ||'
        WHERE  al.cod_lancamento is not null
          AND  al.valor > 0.00
          AND  COALESCE (CEED.numcgm, CEEF.numcgm, CEA.numcgm) = cgm.numcgm
          AND  pjcgm.numcgm = cgm.numcgm

      '|| stFiltro
    ;

    -- ORDER BY 
    IF (stOrdem != '') THEN
            stSql := stSql || stOrdem;
    ELSE
        stSql := stSql ||' ORDER BY al.cod_lancamento  ';
    END IF;

    stSql := stSql ||'
        ) as tudo
    ';
    FOR reRegistro IN EXECUTE stSql 
    LOOP
        return next reRegistro;
    END LOOP;

    RETURN;
END;

$$ LANGUAGE 'plpgsql';
