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
* $Id: listaEmissaoGraficaImobiliaria.plsql 63867 2015-10-27 17:25:14Z evandro $
*
* Caso de uso: uc-05.03.11
* Caso de uso: uc-05.03.19
*
*/

/*  1    2     3    4   5   6   7   8   9    10     11     12  13   14      15      16      17  */
CREATE OR REPLACE FUNCTION arrecadacao.fn_lista_emissao_grafica_imobiliaria
  ( varchar,int,int,int,int ,int,int,varchar,int,varchar ,varchar,int,int,varchar,varchar ,varchar,varchar, varchar )

RETURNS SETOF RECORD AS $$
DECLARE

    stTipoInscricao         ALIAS FOR $1;

    inExercicio     		ALIAS FOR $2;
    inCodGrupo      		ALIAS FOR $3;
    inCodCredito    		ALIAS FOR $4;
    inCodEspecie    		ALIAS FOR $5;
    inCodGenero     		ALIAS FOR $6;
    inCodNatureza   		ALIAS FOR $7;

    inCodIIInicial  		ALIAS FOR $8;
    inCodIIFinal    		ALIAS FOR $9;
    stLocalizacaoInicial	ALIAS FOR $10;

    stLocalizacaoFinal		ALIAS FOR $11;
    inCodEnderecoInicial  	ALIAS FOR $12;
    inCodEnderecoFinal    	ALIAS FOR $13;
    stOrdemEmissao			ALIAS FOR $14;
    stOrdemLote				ALIAS FOR $15;

    stOrdemImovel	  		ALIAS FOR $16;
    stOrdemEdificacao  		ALIAS FOR $17;
    stPadraoCodBarra        ALIAS FOR $18;
    
   
    inRetorno       integer;
    reRegistro      RECORD;
    stSql           VARCHAR;
    stFiltro        varchar := '';
	stFiltroTipoInscricao varchar := '';
    stJoins         varchar := '';
    stFrom          varchar := '';
	stOrdem         varchar := '';
    stFromOrigem    varchar := '';
    stFiltroCredito varchar := '';
    stColunasOrigem varchar := '';
    stGroupByOrigem varchar := '';
    inNumConvenio   integer;
    inCodFebraban   integer;
BEGIN

    SELECT valor INTO inCodFebraban
      FROM administracao.configuracao 
     WHERE parametro = 'FEBRABAN'
       AND cod_modulo = 2
  ORDER BY exercicio DESC
     LIMIT 1;

IF ( inCodCredito > 0 ) THEN
        SELECT DISTINCT convenio.num_convenio
          INTO inNumConvenio
          FROM monetario.convenio
    INNER JOIN monetario.credito
            ON credito.cod_convenio = convenio.cod_convenio
         WHERE credito.cod_credito = inCodCredito
           AND credito.cod_natureza = inCodNatureza
           AND credito.cod_genero = inCodGenero
           AND credito.cod_especie = inCodEspecie;
ELSIF ( inCodGrupo > 0 ) THEN
        SELECT DISTINCT convenio.num_convenio
          INTO inNumConvenio
          FROM monetario.convenio
    INNER JOIN monetario.credito
            ON credito.cod_convenio = convenio.cod_convenio
    INNER JOIN arrecadacao.credito_grupo
            ON credito_grupo.cod_credito = credito.cod_credito
           AND credito_grupo.cod_natureza = credito.cod_natureza
           AND credito_grupo.cod_especie = credito.cod_especie
           AND credito_grupo.cod_genero = credito.cod_genero
         WHERE credito_grupo.cod_grupo = inCodGrupo
           AND credito_grupo.ano_exercicio = inExercicio::varchar;
END IF;

/**
*   FUNCIONAMENTO
*   Antes de executar a consulta, é verificado todos os filtros, aonde a tabela de maior proximidade 
*   com o filtro mais exclusivo torna-se a tabela-mãe.
*/


-- ##########################   Filtro para crédito #############
IF ( inCodCredito > 0 ) THEN

    stColunasOrigem := '
        , 0 as cod_grupo
        , split_part ( monetario.fn_busca_mascara_credito(
            ac.cod_credito, ac.cod_genero, ac.cod_especie, ac.cod_natureza), ''§'', 6
        )::varchar as descricao
        , ac.exercicio::int as exercicio
    ';

    stFiltroCredito := '
        WHERE
            NOT EXISTS (select cod_calculo from arrecadacao.calculo_grupo_credito where cod_calculo = ac.cod_calculo)
            and ac.cod_credito      = '||inCodCredito::varchar||'
            and ac.cod_especie      = '||inCodEspecie::varchar||'
            and ac.cod_genero       = '||inCodGenero::varchar||'
            and ac.cod_natureza     = '||inCodNatureza::varchar||'
    ';

    stGroupByOrigem := '
        , ac.cod_credito, ac.cod_genero, ac.cod_especie, ac.cod_natureza
    ';

END IF;


-- ##########################   Filtro para GRUPO  #############
IF ( inCodGrupo > 0 ) THEN

    stColunasOrigem := '
        , acgc.cod_grupo
        , agc.descricao
        , ac.exercicio::int as exercicio
    ';

    stFiltro := stFiltro||' and aic2.cod_grupo = '||inCodGrupo;

    stFromOrigem := stFrom || '

        INNER JOIN arrecadacao.calculo_grupo_credito as acgc
        ON acgc.cod_calculo = aic.cod_calculo

        INNER JOIN arrecadacao.grupo_credito as agc
        ON agc.cod_grupo = acgc.cod_grupo
        and agc.ano_exercicio = acgc.ano_exercicio

    ';

    stGroupByOrigem := '
        , agc.descricao, acgc.cod_grupo
    ';

END IF;

IF ( inExercicio > 0 ) THEN
    stFiltro := stFiltro||' and aic2.exercicio = '||quote_literal(inExercicio)||' ';
END IF;


-- ##########################   TIPO DE INSCRICAO  #############
IF ( stTipoInscricao != '') THEN
	IF ( stTipoInscricao = 'prediais') THEN
		stFiltro := stFiltro||' and exists ( select inscricao_municipal from imobiliario.unidade_autonoma as iau where iau.inscricao_municipal = aic2.inscricao ) ';
	ELSE
        IF ( stTipoInscricao = 'territoriais') THEN
            stFiltro := stFiltro||' and not exists ( select inscricao_municipal from imobiliario.unidade_autonoma as iau where iau.inscricao_municipal = aic2.inscricao ) ';
		END IF;
	END IF;
END IF;


-- ##########################   INSCRICAO IMOBILIARIA  #########
stFiltro := stFiltro||' and aic2.inscricao in ( '||inCodIIInicial||' ) ';

-- ##########################   COD LOTE  ######################
IF ( inCodEnderecoInicial > 0 ) THEN
	IF ( inCodEnderecoFinal > 0 ) THEN
		stFiltro := stFiltro||' and  ENDERECO.cod_lote between '||inCodEnderecoInicial||' and '||inCodEnderecoFinal||'  ';
	ELSE
		stFiltro := stFiltro||' and ENDERECO.cod_lote = '||inCodEnderecoInicial ;
	END IF;
END IF;

-- ##########################   LOCALIZACAO   ##################
IF ( stLocalizacaoInicial != '' ) THEN
	IF ( stLocalizacaoFinal != '' ) THEN
        stFiltro := stFiltro||' and ILOC.codigo_composto between '''||stLocalizacaoInicial||''' and ''' ||stLocalizacaoFinal||''' ';
	ELSE
		stFiltro := stFiltro ||' and ILOC.codigo_composto = '''|| stLocalizacaoInicial||''' ' ;
	END IF;
END IF;

-- ================================================###   FIM DOS FILTROS   ========================###




-- ##########################   ORDER BY   ##################
IF ( stOrdemEmissao != '' ) THEN
	stOrdem := stOrdemEmissao;
END IF;

IF ( stOrdem != '' ) THEN
	stOrdem := ' ORDER BY '|| stOrdem || ' ';
END IF;




/* **********************************************************************************/
/* *******************************************************    CONSULTA PRINCIPAL    */
/* **********************************************************************************/
    stSql := '

SELECT DISTINCT

    tudo.inscricao
    , tudo.exercicio
    , tudo.cod_lancamento
    , tudo.lanc_venc
    , tudo.lanc_valor

    , tudo.cod_grupo
    , tudo.descricao

    , tudo.numcgm
    , tudo.nom_cgm
    , tudo.area_lote
    , tudo.area_edificada
    , tudo.codigo_composto
    , tudo.nom_localizacao
    , tudo.cod_lote
    , tudo.cod_construcao
    , tudo.cod_tipo_construcao

    , split_part(tudo.endereco,''§'',1)::varchar as nom_tipo
    , split_part(tudo.endereco,''§'',2)::int     as cod_logradouro
    , split_part(tudo.endereco,''§'',3)::varchar as nom_logradouro
    , split_part(tudo.endereco,''§'',4)::varchar as numero
    , split_part(tudo.endereco,''§'',5)::varchar as complemento
    , COALESCE(tudo.nome_condominio,'''') as nome_condominio
    , split_part(tudo.endereco,''§'',6)::varchar as nom_bairro
    , split_part(tudo.endereco,''§'',7)::varchar as cep
    , split_part(tudo.endereco,''§'',8)::varchar as cod_municipio
    , split_part(tudo.endereco,''§'',9)::varchar as nom_municipio
    , split_part(tudo.endereco,''§'',10)::varchar as cod_uf
    , split_part(tudo.endereco,''§'',11)::varchar as sigla_uf

    , ( case when tudo.endereco_c is null then
            split_part(tudo.endereco,''§'',1)
        else  split_part(tudo.endereco_c,''§'',1)
        end
    )::varchar as c_nom_tipo_logradouro
    , ( case when tudo.endereco_c is null then
            split_part(tudo.endereco,''§'',2)::int
        else  split_part(tudo.endereco_c,''§'',2)::int
        end
    )::int as c_cod_logradouro

    , ( case when tudo.endereco_c is null then
            split_part(tudo.endereco,''§'',3)
        else  split_part(tudo.endereco_c,''§'',3)
        end
    )::varchar as c_nom_logradouro
    , ( case when tudo.endereco_c is null then
            split_part(tudo.endereco,''§'',4)
        else  split_part(tudo.endereco_c,''§'',4)
        end
    )::varchar as c_numero
    , ( case when tudo.endereco_c is null then
            split_part(tudo.endereco,''§'',5)
        else  split_part(tudo.endereco_c,''§'',5)
        end
    )::varchar as c_complemento

    , ( case when tudo.endereco_c is null then
            split_part(tudo.endereco,''§'',6)
        else split_part(tudo.endereco_c,''§'',6)
        end
    )::varchar as c_nom_bairro

    , ( case when tudo.endereco_c is null then
            split_part(tudo.endereco,''§'',7)
        else  split_part(tudo.endereco_c,''§'',7)
        end
    )::varchar as c_cep

    , ( case when tudo.endereco_c is null then
            split_part(tudo.endereco,''§'',8)
        else split_part(tudo.endereco_c,''§'',8)
        end
    )::varchar as c_cod_municipio

    , ( case when tudo.endereco_c is null then
            split_part(tudo.endereco,''§'',9)
        else split_part(tudo.endereco_c,''§'',9)
        end
    )::varchar as c_nom_municipio

    , ( case when tudo.endereco_c is null then
            split_part(tudo.endereco,''§'',10)
        else split_part(tudo.endereco_c,''§'',10)
        end
    )::varchar as c_cod_uf
    , ( case when tudo.endereco_c is null then
            split_part(tudo.endereco,''§'',11)
        else split_part(tudo.endereco_c,''§'',11)
        end
    )::varchar as c_sigla_uf
    , ( case when tudo.endereco_c is null then
            null
        else split_part(tudo.endereco_c,''§'',12)
        end
    )::varchar as c_caixa_postal




    , split_part(lista_parcelas_unicas,''§'',1)::varchar as qtde_parcelas_unicas



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
    , ( case when ( split_part(lista_parcelas_unicas,''§'',1)::integer > 0 ) THEN
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
    , ( case when ( split_part(lista_parcelas_unicas,''§'',1)::integer > 0 ) THEN
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
    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 0 ) THEN
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
    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 0 ) THEN
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
    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 1 ) THEN
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
  
--    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 1 ) THEN
--            split_part (
--                arrecadacao.geraCodigoBarraFebraban (
--                    split_part(lista_parcelas_normais  ,''§'',13)::varchar
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
    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 2 ) THEN
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
    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 2 ) THEN
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
    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 3 ) THEN
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
    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 3 ) THEN
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
    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 4 ) THEN
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
    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 4 ) THEN
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
    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 5 ) THEN
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
    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 5 ) THEN
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
    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 6 ) THEN
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
    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 6 ) THEN
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
    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 7 ) THEN
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
    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 7 ) THEN
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
    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 8 ) THEN
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
    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 8 ) THEN
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
    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 9 ) THEN
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
    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 9 ) THEN
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
    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 10 ) THEN
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
    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 10 ) THEN
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
    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 11 ) THEN
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
    , ( case when ( split_part(lista_parcelas_normais,''§'',1)::integer > 11 ) THEN
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

    , split_part(valor_venal,''§'',1)::varchar as venal_territorial
    , split_part(valor_venal,''§'',2)::varchar as venal_predial
    , split_part(valor_venal,''§'',3)::varchar as venal_total
--  , valor_venal::varchar as venal_total

    , split_part(m2_territorial,''§'',1)::varchar as valor_m2_territorial
    , coalesce (m2_predial, ''0.00'')::varchar as valor_m2_predial

    , imobiliario.fn_busca_localizacao_primeiro_nivel( tudo.codigo_composto )::varchar as localizacao_primeiro_nivel

    , split_part(lista_creditos_mata,''§'',1)::varchar as valor_imposto

    , split_part(dados_taxa_limpeza,''§'',1)::varchar as area_limpeza
    , split_part(dados_taxa_limpeza,''§'',2)::varchar as aliquota_limpeza
    , aliquota_imposto::varchar

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
    , (( coalesce( m2_predial::numeric, 0.00 ) / 2)::numeric(14,2))::varchar AS valor_m2_predial_descoberto
    , ( ( arrecadacao.fn_vc2num(recuperaCadastroImobiliarioImovelAreaTotalDescoberta( tudo.inscricao )) * ( coalesce( m2_predial::numeric, 0.00 ) / 2 ) )::numeric(14,2) )::varchar AS valor_venal_predial_descoberto
    , ( imobiliario.fn_calcula_area_imovel( tudo.inscricao ) + arrecadacao.fn_vc2num(recuperaCadastroImobiliarioImovelAreaTotalDescoberta( tudo.inscricao )) )::varchar AS area_construida_total
    , arrecadacao.fn_vc2num(recuperaCadastroImobiliarioImovelAreaTotalDescoberta( tudo.inscricao ))::varchar AS area_descoberta
    , (( imobiliario.fn_calcula_area_imovel( tudo.inscricao ) * coalesce( m2_predial::numeric, 0.00 ) )::numeric(14,2))::varchar as venal_predial_coberto
FROM
    (
    SELECT

        aic2.inscricao
        , aic2.exercicio
        , al.cod_lancamento
        , arrecadacao.fn_atualiza_data_vencimento ( al.vencimento ) as lanc_venc
        , al.valor as lanc_valor
        , aic2.cod_grupo
        , aic2.descricao
        , cgm.numcgm
        , cgm.nom_cgm
        , imobiliario.fn_calcula_area_imovel_construcao( aic.inscricao_municipal )::numeric(14,2) as area_edificada
        , ILOC.codigo_composto
        , ILOC.nom_localizacao
        , condominio.nom_condominio as nome_condominio

        , IML.cod_lote
        , ial.area_real as area_lote
        , coalesce ( iua.cod_construcao, null ) as cod_construcao
        , coalesce ( iua.cod_tipo, null ) as cod_tipo_construcao

        , imobiliario.fn_busca_endereco_imovel ( aic.inscricao_municipal ) as endereco
--        , imobiliario.fn_busca_endereco_correspondencia ( aic.inscricao_municipal ) as endereco_c
        , arrecadacao.fn_consulta_endereco_mata_saojoao ( aic.inscricao_municipal, aic2.exercicio::varchar ) as endereco_c

        , arrecadacao.fn_lista_parcelas_unicas( al.cod_lancamento ) as lista_parcelas_unicas
        , arrecadacao.fn_lista_parcelas_normais( al.cod_lancamento ) as lista_parcelas_normais

        , arrecadacao.fn_lista_creditos_lancamento( al.cod_lancamento, aic2.cod_grupo, aic2.exercicio ) as lista_creditos
        , arrecadacao.fn_lista_creditos_lancamento_mata( al.cod_lancamento, aic2.cod_grupo, aic2.exercicio ) as lista_creditos_mata

        , vvenal.venal_territorial_calculado||''§''||vvenal.venal_predial_calculado||''§''|| vvenal.venal_total_calculado as valor_venal

        , imobiliario.fn_busca_valor_m2_terreno( aic.inscricao_municipal ) as m2_territorial
        , imobiliario.fn_busca_valor_m2_predial( iua.cod_construcao, iua.cod_tipo, aic2.exercicio ) as m2_predial

        , economico.fn_busca_dados_taxa_limpeza( aic.inscricao_municipal, aic2.exercicio ) as dados_taxa_limpeza
        , economico.fn_busca_aliquota_imposto( aic.inscricao_municipal, aic2.exercicio ) as aliquota_imposto


    FROM
        arrecadacao.imovel_calculo as aic

        INNER JOIN (

            select
                aic.inscricao_municipal as inscricao
                , max(ac.cod_calculo) as cod_calculo
                '||stColunasOrigem||'
            from
                arrecadacao.imovel_calculo as aic

                '||stFromOrigem||'

                INNER JOIN arrecadacao.calculo as ac
                ON ac.cod_calculo = aic.cod_calculo

                INNER JOIN arrecadacao.lancamento_calculo AS lc
                ON lc.cod_calculo = ac.cod_calculo

                INNER JOIN arrecadacao.lancamento AS l
                ON l.cod_lancamento = lc.cod_lancamento
                and l.ativo = true


            '||stFiltroCredito||'
        
            group by
                aic.inscricao_municipal, ac.exercicio '|| stGroupByOrigem ||'

        ) as aic2
        ON aic2.inscricao = aic.inscricao_municipal
        AND aic2.cod_calculo = aic.cod_calculo

        INNER JOIN arrecadacao.lancamento_calculo as alc
        ON alc.cod_calculo = aic.cod_calculo

        INNER JOIN arrecadacao.lancamento as al
        ON al.cod_lancamento = alc.cod_lancamento
            AND al.ativo = true

        INNER JOIN (
            SELECT
                ccgm.numcgm, ccgm.cod_calculo, cgm.nom_cgm
            FROM
                arrecadacao.calculo_cgm as ccgm
                INNER JOIN sw_cgm as cgm
                ON cgm.numcgm = ccgm.numcgm
        ) as cgm
        ON cgm.cod_calculo = aic.cod_calculo

        INNER JOIN (
            SELECT
                IML.inscricao_municipal
                , IML.cod_lote
                , max(IML.timestamp) as timestamp
            FROM
                imobiliario.imovel_lote as IML
            GROUP BY
                IML.inscricao_municipal, IML.cod_lote
        ) as IML
        ON IML.inscricao_municipal = aic.inscricao_municipal

        INNER JOIN imobiliario.lote_localizacao as ILLO
        ON ILLO.cod_lote = IML.cod_lote

        INNER JOIN imobiliario.localizacao as ILOC
        ON ILOC.cod_localizacao = ILLO.cod_localizacao

        INNER JOIN (
            select ial.cod_lote, ial.area_real from
            imobiliario.area_lote as ial
            INNER JOIN (
                select cod_lote, max(timestamp) as timestamp
                from imobiliario.area_lote as ial2
                group by cod_lote
            ) as ial2 ON ial2.cod_lote = ial.cod_lote and ial2.timestamp = ial.timestamp
        ) as ial
        ON ial.cod_lote = IML.cod_lote

        INNER JOIN arrecadacao.imovel_v_venal as vvenal
        ON vvenal.inscricao_municipal = aic.inscricao_municipal
        AND vvenal.timestamp = aic.timestamp

        LEFT JOIN imobiliario.baixa_imovel as ibi
        ON ibi.inscricao_municipal = aic.inscricao_municipal

        LEFT JOIN ( SELECT  imovel_condominio.inscricao_municipal
                            , condominio.*
                        FROM imobiliario.imovel_condominio
                        INNER JOIN imobiliario.condominio
                            ON condominio.cod_condominio = imovel_condominio.cod_condominio
                 )as condominio
            ON condominio.inscricao_municipal = aic.inscricao_municipal

        LEFT JOIN (
            select
                iua.inscricao_municipal, cod_construcao, cod_tipo
            from
                imobiliario.unidade_autonoma as iua
                inner join (
                    select
                        coalesce (inscricao_municipal, null) as inscricao_municipal
                        , max(timestamp) as timestamp
                    from imobiliario.unidade_autonoma as iua2
                    group by inscricao_municipal
                ) as iua2
                ON iua2.inscricao_municipal = iua.inscricao_municipal
                and iua2.timestamp = iua.timestamp
        ) as iua
        ON iua.inscricao_municipal = aic.inscricao_municipal

	';

    stSql := stSql||' WHERE
						al.cod_lancamento is not null
                        and ibi.inscricao_municipal is null
                        and al.valor > 0.00
					'||stFiltro;


	/* ordenar */
	if ( stOrdem != '' ) then
		stSql := stSql||stOrdem;
    	stSql := stSql||' , al.cod_lancamento ';
	else
		stSql := stSql||' order by al.cod_lancamento  ';
	end if;

    stSql := stSql||'

    ) as tudo

    ';

    FOR reRegistro IN EXECUTE stSql LOOP
        return next reRegistro;
    END LOOP;

    return;

END;
$$ LANGUAGE 'plpgsql';
