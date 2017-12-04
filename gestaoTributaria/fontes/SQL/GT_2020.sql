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
* Script de DDL e DML
*
* Versao 2.02.0
*
* Fabio Bertoldi - 201320702
*
*/

----------------
-- Ticket #17075
----------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE

BEGIN
    PERFORM 1
       FROM administracao.configuracao
      WHERE exercicio  = '2013'
        AND cod_modulo = 2
        AND parametro  = 'cnpj'
        AND valor      = '94068418000184'
          ;
    IF FOUND THEN

        INSERT INTO administracao.arquivos_documento VALUES ((SELECT MAX(cod_arquivo)+1 FROM administracao.arquivos_documento), 'certidaoPositivaMariana.odt', '0f8a4e73dd0f6c29d785b03fa10d503e', FALSE);
        UPDATE administracao.modelo_arquivos_documento SET cod_arquivo = (SELECT MAX(cod_arquivo) FROM administracao.arquivos_documento), sistema = FALSE WHERE cod_documento = 6 AND cod_tipo_documento = 3;

        INSERT INTO administracao.arquivos_documento VALUES ((SELECT MAX(cod_arquivo)+1 FROM administracao.arquivos_documento), 'certidaoNegativaMariana.odt', 'ba903fae36590df7a44c74d235549ce6', FALSE);
        UPDATE administracao.modelo_arquivos_documento SET cod_arquivo = (SELECT MAX(cod_arquivo) FROM administracao.arquivos_documento), sistema = FALSE WHERE cod_documento = 7 AND cod_tipo_documento = 3;

        INSERT INTO administracao.arquivos_documento VALUES ((SELECT MAX(cod_arquivo)+1 FROM administracao.arquivos_documento), 'certidaoPositivaNegativaMariana.odt', '0f3e57e240708f308a6296edc23fcc71', FALSE);
        UPDATE administracao.modelo_arquivos_documento SET cod_arquivo = (SELECT MAX(cod_arquivo) FROM administracao.arquivos_documento), sistema = FALSE WHERE cod_documento = 8 AND cod_tipo_documento = 3;

    END IF;
END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();


----------------
-- Ticket #19282
----------------

UPDATE administracao.arquivos_documento SET nome_arquivo_swx = 'termoInscricaoDAUrbem.odt'    WHERE nome_arquivo_swx = 'termoInscricaoDASiamWeb.odt';
UPDATE administracao.arquivos_documento SET nome_arquivo_swx = 'certidaoDAUrbem.odt'          WHERE nome_arquivo_swx = 'certidaoDASiamWeb.odt';
UPDATE administracao.arquivos_documento SET nome_arquivo_swx = 'memorialCalculoDAUrbem.odt'   WHERE nome_arquivo_swx = 'memorialCalculoDASiamWeb.odt';
UPDATE administracao.arquivos_documento SET nome_arquivo_swx = 'termoParcelamentoDAUrbem.odt' WHERE nome_arquivo_swx = 'termoParcelamentoDASiamWeb.odt';
UPDATE administracao.arquivos_documento SET nome_arquivo_swx = 'termoConsolidacaoDAUrbem.odt' WHERE nome_arquivo_swx = 'termoConsolidacaoDASiamWeb.odt';
UPDATE administracao.arquivos_documento SET nome_arquivo_swx = 'notificacaoDAUrbem.odt'       WHERE nome_arquivo_swx = 'notificacaoDASiamWeb.odt';


-------------------------
-- Ticket #20907 E #20909
-------------------------

DELETE FROM administracao.configuracao where exercicio = '2014' and parametro = 'ordem_entrega';
INSERT INTO administracao.configuracao SELECT '2014' AS exercicio, cod_modulo, parametro, valor FROM administracao.configuracao where exercicio = '2013' and parametro = 'ordem_entrega';


UPDATE administracao.funcao_externa SET corpo_pl=
    'FUNCTION  regraAcrescimoGeralDivida(INTEGER) RETURNS BOOLEAN as ''
DECLARE
inRegistro ALIAS FOR $1;

  boRetorno BOOLEAN := TRUE;
BEGIN
RETURN boRetorno;
END;
 '' LANGUAGE ''plpgsql'';'
     WHERE cod_modulo = 33 AND cod_biblioteca = 2 AND cod_funcao = 1 ;


CREATE OR REPLACE FUNCTION manutencao_itau() RETURNS VOID AS $$
DECLARE
    stSQL   VARCHAR;
BEGIN
    stSQL := '
CREATE OR REPLACE FUNCTION recuperaCadastroImobiliarioImovelISENCAODOIMPOSTOIPTU(  INTEGER ) RETURNS VARCHAR AS ''
DECLARE
    stSql VARCHAR;
    crCursor  REFCURSOR;
    rsRetorno RECORD;
    inInscricaoMunicipal ALIAS FOR $1;
BEGIN
    stSql := ''''  SELECT
     AD.cod_cadastro,
     AD.cod_atributo,
     AD.ativo,
     AD.nao_nulo,
     AD.nom_atributo,
     CASE TA.cod_tipo
         WHEN 4 THEN  administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,'''''''''''''''')
     END AS valor_padrao,
     CASE TA.cod_tipo
       WHEN 3 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,''''''''''''''''))
       WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,VALOR.valor))
         ELSE         null
     END AS valor_padrao_desc,
     CASE TA.cod_tipo WHEN
         4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         null
     END AS valor_desc,
     AD.ajuda,
     AD.mascara,
     TA.cod_tipo,
     TA.nom_tipo,
     VALOR.valor,
     VALOR.timestamp
  FROM
     administracao.atributo_dinamico          AS AD,
     administracao.tipo_atributo              AS TA,
     administracao.atributo_dinamico AS ACA
     LEFT JOIN
     imobiliario.atributo_imovel_valor         AS VALOR
  ON ( ACA.cod_atributo = VALOR.cod_atributo
          AND ACA.cod_cadastro = VALOR.cod_cadastro
              AND VALOR.inscricao_municipal = ''''||inInscricaoMunicipal||''''
 AND ACA.cod_atributo = 5044
         AND CAST(VALOR.timestamp as varchar)||CAST(VALOR.cod_atributo as varchar) IN (
             SELECT
         (CAST(max(VALOR.timestamp) as varchar))::varchar||CAST(VALOR.cod_atributo as varchar)
             FROM
                administracao.atributo_dinamico AS ACA,
                imobiliario.atributo_imovel_valor         AS VALOR,
                administracao.atributo_dinamico          AS AD,
                administracao.tipo_atributo              AS TA
             WHERE
                ACA.cod_atributo = AD.cod_atributo
                AND ACA.cod_cadastro = AD.cod_cadastro
                AND ACA.cod_modulo   = AD.cod_modulo
             AND ACA.cod_atributo = VALOR.cod_atributo
             AND ACA.cod_cadastro = VALOR.cod_cadastro
             AND ACA.cod_modulo   = VALOR.cod_modulo
              AND VALOR.inscricao_municipal = ''''||inInscricaoMunicipal||''''
 AND ACA.cod_atributo = 5044
             AND AD.cod_tipo = TA.cod_tipo
             AND ACA.ativo = true
             AND AD.cod_modulo   =12
             AND AD.cod_cadastro=4

             GROUP BY VALOR.cod_cadastro, VALOR.cod_atributo
                                  )

     )
  WHERE
      AD.cod_tipo = TA.cod_tipo
  AND ACA.ativo = true
  AND     AD.ativo
  AND AD.cod_atributo =  ACA.cod_atributo
  AND AD.cod_modulo   = ACA.cod_modulo
  AND AD.cod_cadastro = ACA.cod_cadastro
  AND ACA.cod_cadastro=4
  AND ACA.cod_modulo  =12


 AND ACA.cod_atributo = 5044;'''';OPEN crCursor FOR EXECUTE stSql;
                                    FETCH crCursor INTO rsRetorno;
                                    CLOSE crCursor;
                                    RETURN rsRetorno.valor;
                                    END;
                                    '' LANGUAGE plpgsql;
                 ';
    EXECUTE stSQL;

    UPDATE administracao.corpo_funcao_externa SET linha = ' SE   #stIsento  =  "1"  OU  #stIsento  =  "3" ENTAO ' WHERE cod_modulo = 25 AND cod_biblioteca = 2 AND cod_funcao = 1 AND cod_linha = 2;
    UPDATE administracao.funcao_externa SET cod_modulo = 25, cod_biblioteca = 2, cod_funcao = 1, comentario = '', corpo_pl =
        'FUNCTION  regraConcessaoDesoneracaoIPTU2012(INTEGER) RETURNS BOOLEAN as ''
    DECLARE
    inRegistro ALIAS FOR $1;

      boRetorno BOOLEAN;
      stIsento VARCHAR := '''''''';
    BEGIN
    stIsento := recuperaCadastroImobiliarioImovelISENCAODOIMPOSTOIPTU(  inRegistro  );
         IF   stIsento  =  ''''1''''  OR  stIsento  =  ''''3'''' THEN
        boRetorno := TRUE;
    ELSE
        boRetorno := FALSE;
    END IF;
    RETURN boRetorno;
    END;
     '' LANGUAGE ''plpgsql'';
    ', corpo_ln = ''
    WHERE cod_modulo = 25 AND cod_biblioteca = 2 AND cod_funcao = 1 ;



UPDATE administracao.funcao_externa SET     cod_modulo= 33,    cod_biblioteca= 2,    cod_funcao= 1,    comentario=
    '',    corpo_pl=
    'FUNCTION  regraAcrescimoGeralDivida(INTEGER) RETURNS BOOLEAN as ''
DECLARE
inRegistro ALIAS FOR $1;

  boRetorno BOOLEAN := TRUE;
BEGIN
RETURN boRetorno;
END;
 '' LANGUAGE ''plpgsql'';
',    corpo_ln=
    '' WHERE cod_modulo = 33 AND cod_biblioteca = 2 AND cod_funcao = 1 ;

END;
$$ LANGUAGE 'plpgsql';



CREATE OR REPLACE FUNCTION manutencao_manaquiri() RETURNS VOID AS $$
DECLARE
    stSQL   VARCHAR;
BEGIN
        stSQL := '
CREATE OR REPLACE FUNCTION recuperaCadastroEconomicoInscricaoEconomicaAutonomoCZCoeficienteDasZonasSetor(  INTEGER ) RETURNS VARCHAR AS ''DECLARE stSql VARCHAR; crCursor  REFCURSOR; rsRetorno RECORD;inInscricaoEconomica ALIAS FOR $1;BEGIN stSql := '''' SELECT
     AD.cod_cadastro,
     AD.cod_atributo,
     AD.ativo,
     AD.nao_nulo,
     AD.nom_atributo,
     CASE TA.cod_tipo
         WHEN 4 THEN  administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,'''''''''''''''')
     END AS valor_padrao,
     CASE TA.cod_tipo
       WHEN 3 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,''''''''''''''''))
       WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,VALOR.valor))
         ELSE         null
     END AS valor_padrao_desc,
     CASE TA.cod_tipo WHEN
         4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         null
     END AS valor_desc,
     AD.ajuda,
     AD.mascara,
     TA.cod_tipo,
     TA.nom_tipo,
     VALOR.valor,
     VALOR.timestamp
  FROM
     administracao.atributo_dinamico          AS AD,
     administracao.tipo_atributo              AS TA,
     administracao.atributo_dinamico AS ACA
     LEFT JOIN
     economico.atributo_cad_econ_autonomo_valor         AS VALOR
  ON ( ACA.cod_atributo = VALOR.cod_atributo
          AND ACA.cod_cadastro = VALOR.cod_cadastro
              AND VALOR.inscricao_economica = ''''||inInscricaoEconomica||''''
 AND ACA.cod_atributo = 5060
         AND CAST(VALOR.timestamp as varchar)||CAST(VALOR.cod_atributo as varchar) IN (
             SELECT
         (CAST(max(VALOR.timestamp) as varchar))::varchar||CAST(VALOR.cod_atributo as varchar)
             FROM
                administracao.atributo_dinamico AS ACA,
                economico.atributo_cad_econ_autonomo_valor         AS VALOR,
                administracao.atributo_dinamico          AS AD,
                administracao.tipo_atributo              AS TA
             WHERE
                ACA.cod_atributo = AD.cod_atributo
                AND ACA.cod_cadastro = AD.cod_cadastro
                AND ACA.cod_modulo   = AD.cod_modulo
             AND ACA.cod_atributo = VALOR.cod_atributo
             AND ACA.cod_cadastro = VALOR.cod_cadastro
             AND ACA.cod_modulo   = VALOR.cod_modulo
              AND VALOR.inscricao_economica = ''''||inInscricaoEconomica||''''
 AND ACA.cod_atributo = 5060
             AND AD.cod_tipo = TA.cod_tipo
             AND ACA.ativo = true
             AND AD.cod_modulo   =14
             AND AD.cod_cadastro=3

             GROUP BY VALOR.cod_cadastro, VALOR.cod_atributo
                                  )

     )
  WHERE
      AD.cod_tipo = TA.cod_tipo
  AND ACA.ativo = true
  AND     AD.ativo
  AND AD.cod_atributo =  ACA.cod_atributo
  AND AD.cod_modulo   = ACA.cod_modulo
  AND AD.cod_cadastro = ACA.cod_cadastro
  AND ACA.cod_cadastro=3
  AND ACA.cod_modulo  =14


 AND ACA.cod_atributo = 5060;'''';OPEN crCursor FOR EXECUTE stSql;
                                    FETCH crCursor INTO rsRetorno;
                                    CLOSE crCursor;
                                    RETURN rsRetorno.valor;
                                    END;
                                    '' LANGUAGE plpgsql;
             ';
    EXECUTE stSQL;

    stSQL := '
CREATE OR REPLACE FUNCTION recuperaCadastroEconomicoInscricaoEconomicaAutonomoCECoeficienteDeEdificacao(  INTEGER ) RETURNS VARCHAR AS ''DECLARE stSql VARCHAR; crCursor  REFCURSOR; rsRetorno RECORD;inInscricaoEconomica ALIAS FOR $1;BEGIN stSql := '''' SELECT
     AD.cod_cadastro,
     AD.cod_atributo,
     AD.ativo,
     AD.nao_nulo,
     AD.nom_atributo,
     CASE TA.cod_tipo
         WHEN 4 THEN  administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,'''''''''''''''')
     END AS valor_padrao,
     CASE TA.cod_tipo
       WHEN 3 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,''''''''''''''''))
       WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,VALOR.valor))
         ELSE         null
     END AS valor_padrao_desc,
     CASE TA.cod_tipo WHEN
         4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         null
     END AS valor_desc,
     AD.ajuda,
     AD.mascara,
     TA.cod_tipo,
     TA.nom_tipo,
     VALOR.valor,
     VALOR.timestamp
  FROM
     administracao.atributo_dinamico          AS AD,
     administracao.tipo_atributo              AS TA,
     administracao.atributo_dinamico AS ACA
     LEFT JOIN
     economico.atributo_cad_econ_autonomo_valor         AS VALOR
  ON ( ACA.cod_atributo = VALOR.cod_atributo
          AND ACA.cod_cadastro = VALOR.cod_cadastro
              AND VALOR.inscricao_economica = ''''||inInscricaoEconomica||''''
 AND ACA.cod_atributo = 5061
         AND CAST(VALOR.timestamp as varchar)||CAST(VALOR.cod_atributo as varchar) IN (
             SELECT
         (CAST(max(VALOR.timestamp) as varchar))::varchar||CAST(VALOR.cod_atributo as varchar)
             FROM
                administracao.atributo_dinamico AS ACA,
                economico.atributo_cad_econ_autonomo_valor         AS VALOR,
                administracao.atributo_dinamico          AS AD,
                administracao.tipo_atributo              AS TA
             WHERE
                ACA.cod_atributo = AD.cod_atributo
                AND ACA.cod_cadastro = AD.cod_cadastro
                AND ACA.cod_modulo   = AD.cod_modulo
             AND ACA.cod_atributo = VALOR.cod_atributo
             AND ACA.cod_cadastro = VALOR.cod_cadastro
             AND ACA.cod_modulo   = VALOR.cod_modulo
              AND VALOR.inscricao_economica = ''''||inInscricaoEconomica||''''
 AND ACA.cod_atributo = 5061
             AND AD.cod_tipo = TA.cod_tipo
             AND ACA.ativo = true
             AND AD.cod_modulo   =14
             AND AD.cod_cadastro=3

             GROUP BY VALOR.cod_cadastro, VALOR.cod_atributo
                                  )

     )
  WHERE
      AD.cod_tipo = TA.cod_tipo
  AND ACA.ativo = true
  AND     AD.ativo
  AND AD.cod_atributo =  ACA.cod_atributo
  AND AD.cod_modulo   = ACA.cod_modulo
  AND AD.cod_cadastro = ACA.cod_cadastro
  AND ACA.cod_cadastro=3
  AND ACA.cod_modulo  =14


 AND ACA.cod_atributo = 5061;'''';OPEN crCursor FOR EXECUTE stSql;
                                    FETCH crCursor INTO rsRetorno;
                                    CLOSE crCursor;
                                    RETURN rsRetorno.valor;
                                    END;
                                    '' LANGUAGE plpgsql;
             ';
    EXECUTE stSQL;

    stSQL := '
CREATE OR REPLACE FUNCTION recuperaCadastroEconomicoInscricaoEconomicaAutonomoKNCoeficienteDoNumeroDeEmpregadosCCCoeficienteDaCategoria(  INTEGER ) RETURNS VARCHAR AS ''DECLARE stSql VARCHAR; crCursor  REFCURSOR; rsRetorno RECORD;inInscricaoEconomica ALIAS FOR $1;BEGIN stSql := '''' SELECT
     AD.cod_cadastro,
     AD.cod_atributo,
     AD.ativo,
     AD.nao_nulo,
     AD.nom_atributo,
     CASE TA.cod_tipo
         WHEN 4 THEN  administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,'''''''''''''''')
     END AS valor_padrao,
     CASE TA.cod_tipo
       WHEN 3 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,''''''''''''''''))
       WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,VALOR.valor))
         ELSE         null
     END AS valor_padrao_desc,
     CASE TA.cod_tipo WHEN
         4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         null
     END AS valor_desc,
     AD.ajuda,
     AD.mascara,
     TA.cod_tipo,
     TA.nom_tipo,
     VALOR.valor,
     VALOR.timestamp
  FROM
     administracao.atributo_dinamico          AS AD,
     administracao.tipo_atributo              AS TA,
     administracao.atributo_dinamico AS ACA
     LEFT JOIN
     economico.atributo_cad_econ_autonomo_valor         AS VALOR
  ON ( ACA.cod_atributo = VALOR.cod_atributo
          AND ACA.cod_cadastro = VALOR.cod_cadastro
              AND VALOR.inscricao_economica = ''''||inInscricaoEconomica||''''
 AND ACA.cod_atributo = 5062
         AND CAST(VALOR.timestamp as varchar)||CAST(VALOR.cod_atributo as varchar) IN (
             SELECT
         (CAST(max(VALOR.timestamp) as varchar))::varchar||CAST(VALOR.cod_atributo as varchar)
             FROM
                administracao.atributo_dinamico AS ACA,
                economico.atributo_cad_econ_autonomo_valor         AS VALOR,
                administracao.atributo_dinamico          AS AD,
                administracao.tipo_atributo              AS TA
             WHERE
                ACA.cod_atributo = AD.cod_atributo
                AND ACA.cod_cadastro = AD.cod_cadastro
                AND ACA.cod_modulo   = AD.cod_modulo
             AND ACA.cod_atributo = VALOR.cod_atributo
             AND ACA.cod_cadastro = VALOR.cod_cadastro
             AND ACA.cod_modulo   = VALOR.cod_modulo
              AND VALOR.inscricao_economica = ''''||inInscricaoEconomica||''''
 AND ACA.cod_atributo = 5062
             AND AD.cod_tipo = TA.cod_tipo
             AND ACA.ativo = true
             AND AD.cod_modulo   =14
             AND AD.cod_cadastro=3

             GROUP BY VALOR.cod_cadastro, VALOR.cod_atributo
                                  )

     )
  WHERE
      AD.cod_tipo = TA.cod_tipo
  AND ACA.ativo = true
  AND     AD.ativo
  AND AD.cod_atributo =  ACA.cod_atributo
  AND AD.cod_modulo   = ACA.cod_modulo
  AND AD.cod_cadastro = ACA.cod_cadastro
  AND ACA.cod_cadastro=3
  AND ACA.cod_modulo  =14


 AND ACA.cod_atributo = 5062;'''';OPEN crCursor FOR EXECUTE stSql;
                                    FETCH crCursor INTO rsRetorno;
                                    CLOSE crCursor;
                                    RETURN rsRetorno.valor;
                                    END;
                                    '' LANGUAGE plpgsql;
             ';
    EXECUTE stSQL;

    stSQL := '
CREATE OR REPLACE FUNCTION recuperaCadastroEconomicoInscricaoEconomicaAutonomoAlvaraIsento(  INTEGER ) RETURNS VARCHAR AS ''DECLARE stSql VARCHAR; crCursor  REFCURSOR; rsRetorno RECORD;inInscricaoEconomica ALIAS FOR $1;BEGIN stSql := '''' SELECT
     AD.cod_cadastro,
     AD.cod_atributo,
     AD.ativo,
     AD.nao_nulo,
     AD.nom_atributo,
     CASE TA.cod_tipo
         WHEN 4 THEN  administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,'''''''''''''''')
     END AS valor_padrao,
     CASE TA.cod_tipo
       WHEN 3 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,''''''''''''''''))
       WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,VALOR.valor))
         ELSE         null
     END AS valor_padrao_desc,
     CASE TA.cod_tipo WHEN
         4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         null
     END AS valor_desc,
     AD.ajuda,
     AD.mascara,
     TA.cod_tipo,
     TA.nom_tipo,
     VALOR.valor,
     VALOR.timestamp
  FROM
     administracao.atributo_dinamico          AS AD,
     administracao.tipo_atributo              AS TA,
     administracao.atributo_dinamico AS ACA
     LEFT JOIN
     economico.atributo_cad_econ_autonomo_valor         AS VALOR
  ON ( ACA.cod_atributo = VALOR.cod_atributo
          AND ACA.cod_cadastro = VALOR.cod_cadastro
              AND VALOR.inscricao_economica = ''''||inInscricaoEconomica||''''
 AND ACA.cod_atributo = 5063
         AND CAST(VALOR.timestamp as varchar)||CAST(VALOR.cod_atributo as varchar) IN (
             SELECT
         (CAST(max(VALOR.timestamp) as varchar))::varchar||CAST(VALOR.cod_atributo as varchar)
             FROM
                administracao.atributo_dinamico AS ACA,
                economico.atributo_cad_econ_autonomo_valor         AS VALOR,
                administracao.atributo_dinamico          AS AD,
                administracao.tipo_atributo              AS TA
             WHERE
                ACA.cod_atributo = AD.cod_atributo
                AND ACA.cod_cadastro = AD.cod_cadastro
                AND ACA.cod_modulo   = AD.cod_modulo
             AND ACA.cod_atributo = VALOR.cod_atributo
             AND ACA.cod_cadastro = VALOR.cod_cadastro
             AND ACA.cod_modulo   = VALOR.cod_modulo
              AND VALOR.inscricao_economica = ''''||inInscricaoEconomica||''''
 AND ACA.cod_atributo = 5063
             AND AD.cod_tipo = TA.cod_tipo
             AND ACA.ativo = true
             AND AD.cod_modulo   =14
             AND AD.cod_cadastro=3

             GROUP BY VALOR.cod_cadastro, VALOR.cod_atributo
                                  )

     )
  WHERE
      AD.cod_tipo = TA.cod_tipo
  AND ACA.ativo = true
  AND     AD.ativo
  AND AD.cod_atributo =  ACA.cod_atributo
  AND AD.cod_modulo   = ACA.cod_modulo
  AND AD.cod_cadastro = ACA.cod_cadastro
  AND ACA.cod_cadastro=3
  AND ACA.cod_modulo  =14


 AND ACA.cod_atributo = 5063;'''';OPEN crCursor FOR EXECUTE stSql;
                                    FETCH crCursor INTO rsRetorno;
                                    CLOSE crCursor;
                                    RETURN rsRetorno.valor;
                                    END;
                                    '' LANGUAGE plpgsql;
             ';
    EXECUTE stSQL;

    stSQL := '
CREATE OR REPLACE FUNCTION recuperaCadastroEconomicoInscricaoEconomicaAutonomoISSIsento(  INTEGER ) RETURNS VARCHAR AS ''DECLARE stSql VARCHAR; crCursor  REFCURSOR; rsRetorno RECORD;inInscricaoEconomica ALIAS FOR $1;BEGIN stSql := '''' SELECT
     AD.cod_cadastro,
     AD.cod_atributo,
     AD.ativo,
     AD.nao_nulo,
     AD.nom_atributo,
     CASE TA.cod_tipo
         WHEN 4 THEN  administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,'''''''''''''''')
     END AS valor_padrao,
     CASE TA.cod_tipo
       WHEN 3 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,''''''''''''''''))
       WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,VALOR.valor))
         ELSE         null
     END AS valor_padrao_desc,
     CASE TA.cod_tipo WHEN
         4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         null
     END AS valor_desc,
     AD.ajuda,
     AD.mascara,
     TA.cod_tipo,
     TA.nom_tipo,
     VALOR.valor,
     VALOR.timestamp
  FROM
     administracao.atributo_dinamico          AS AD,
     administracao.tipo_atributo              AS TA,
     administracao.atributo_dinamico AS ACA
     LEFT JOIN
     economico.atributo_cad_econ_autonomo_valor         AS VALOR
  ON ( ACA.cod_atributo = VALOR.cod_atributo
          AND ACA.cod_cadastro = VALOR.cod_cadastro
              AND VALOR.inscricao_economica = ''''||inInscricaoEconomica||''''
 AND ACA.cod_atributo = 5065
         AND CAST(VALOR.timestamp as varchar)||CAST(VALOR.cod_atributo as varchar) IN (
             SELECT
         (CAST(max(VALOR.timestamp) as varchar))::varchar||CAST(VALOR.cod_atributo as varchar)
             FROM
                administracao.atributo_dinamico AS ACA,
                economico.atributo_cad_econ_autonomo_valor         AS VALOR,
                administracao.atributo_dinamico          AS AD,
                administracao.tipo_atributo              AS TA
             WHERE
                ACA.cod_atributo = AD.cod_atributo
                AND ACA.cod_cadastro = AD.cod_cadastro
                AND ACA.cod_modulo   = AD.cod_modulo
             AND ACA.cod_atributo = VALOR.cod_atributo
             AND ACA.cod_cadastro = VALOR.cod_cadastro
             AND ACA.cod_modulo   = VALOR.cod_modulo
              AND VALOR.inscricao_economica = ''''||inInscricaoEconomica||''''
 AND ACA.cod_atributo = 5065
             AND AD.cod_tipo = TA.cod_tipo
             AND ACA.ativo = true
             AND AD.cod_modulo   =14
             AND AD.cod_cadastro=3

             GROUP BY VALOR.cod_cadastro, VALOR.cod_atributo
                                  )

     )
  WHERE
      AD.cod_tipo = TA.cod_tipo
  AND ACA.ativo = true
  AND     AD.ativo
  AND AD.cod_atributo =  ACA.cod_atributo
  AND AD.cod_modulo   = ACA.cod_modulo
  AND AD.cod_cadastro = ACA.cod_cadastro
  AND ACA.cod_cadastro=3
  AND ACA.cod_modulo  =14


 AND ACA.cod_atributo = 5065;'''';OPEN crCursor FOR EXECUTE stSql;
                                    FETCH crCursor INTO rsRetorno;
                                    CLOSE crCursor;
                                    RETURN rsRetorno.valor;
                                    END;
                                    '' LANGUAGE plpgsql;
             ';
    EXECUTE stSQL;

    stSQL := '
CREATE OR REPLACE FUNCTION recuperaCadastroEconomicoInscricaoEconomicaEmpresaDeDireitoOptanteSimplesNacional(  INTEGER ) RETURNS VARCHAR AS ''DECLARE stSql VARCHAR; crCursor  REFCURSOR; rsRetorno RECORD;inInscricaoEconomica ALIAS FOR $1;BEGIN stSql := '''' SELECT
     AD.cod_cadastro,
     AD.cod_atributo,
     AD.ativo,
     AD.nao_nulo,
     AD.nom_atributo,
     CASE TA.cod_tipo
         WHEN 4 THEN  administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,'''''''''''''''')
     END AS valor_padrao,
     CASE TA.cod_tipo
       WHEN 3 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,''''''''''''''''))
       WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,VALOR.valor))
         ELSE         null
     END AS valor_padrao_desc,
     CASE TA.cod_tipo WHEN
         4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         null
     END AS valor_desc,
     AD.ajuda,
     AD.mascara,
     TA.cod_tipo,
     TA.nom_tipo,
     VALOR.valor,
     VALOR.timestamp
  FROM
     administracao.atributo_dinamico          AS AD,
     administracao.tipo_atributo              AS TA,
     administracao.atributo_dinamico AS ACA
     LEFT JOIN
     economico.atributo_empresa_direito_valor         AS VALOR
  ON ( ACA.cod_atributo = VALOR.cod_atributo
          AND ACA.cod_cadastro = VALOR.cod_cadastro
              AND VALOR.inscricao_economica = ''''||inInscricaoEconomica||''''
 AND ACA.cod_atributo = 1
         AND CAST(VALOR.timestamp as varchar)||CAST(VALOR.cod_atributo as varchar) IN (
             SELECT
         (CAST(max(VALOR.timestamp) as varchar))::varchar||CAST(VALOR.cod_atributo as varchar)
             FROM
                administracao.atributo_dinamico AS ACA,
                economico.atributo_empresa_direito_valor         AS VALOR,
                administracao.atributo_dinamico          AS AD,
                administracao.tipo_atributo              AS TA
             WHERE
                ACA.cod_atributo = AD.cod_atributo
                AND ACA.cod_cadastro = AD.cod_cadastro
                AND ACA.cod_modulo   = AD.cod_modulo
             AND ACA.cod_atributo = VALOR.cod_atributo
             AND ACA.cod_cadastro = VALOR.cod_cadastro
             AND ACA.cod_modulo   = VALOR.cod_modulo
              AND VALOR.inscricao_economica = ''''||inInscricaoEconomica||''''
 AND ACA.cod_atributo = 1
             AND AD.cod_tipo = TA.cod_tipo
             AND ACA.ativo = true
             AND AD.cod_modulo   =14
             AND AD.cod_cadastro=2

             GROUP BY VALOR.cod_cadastro, VALOR.cod_atributo
                                  )

     )
  WHERE
      AD.cod_tipo = TA.cod_tipo
  AND ACA.ativo = true
  AND     AD.ativo
  AND AD.cod_atributo =  ACA.cod_atributo
  AND AD.cod_modulo   = ACA.cod_modulo
  AND AD.cod_cadastro = ACA.cod_cadastro
  AND ACA.cod_cadastro=2
  AND ACA.cod_modulo  =14


 AND ACA.cod_atributo = 1;'''';OPEN crCursor FOR EXECUTE stSql;
                                    FETCH crCursor INTO rsRetorno;
                                    CLOSE crCursor;
                                    RETURN rsRetorno.valor;
                                    END;
                                    '' LANGUAGE plpgsql;
             ';
    EXECUTE stSQL;

    stSQL := '
CREATE OR REPLACE FUNCTION recuperaCadastroEconomicoInscricaoEconomicaEmpresaDeDireitoAlvaraIsento(  INTEGER ) RETURNS VARCHAR AS ''DECLARE stSql VARCHAR; crCursor  REFCURSOR; rsRetorno RECORD;inInscricaoEconomica ALIAS FOR $1;BEGIN stSql := '''' SELECT
     AD.cod_cadastro,
     AD.cod_atributo,
     AD.ativo,
     AD.nao_nulo,
     AD.nom_atributo,
     CASE TA.cod_tipo
         WHEN 4 THEN  administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,'''''''''''''''')
     END AS valor_padrao,
     CASE TA.cod_tipo
       WHEN 3 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,''''''''''''''''))
       WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,VALOR.valor))
         ELSE         null
     END AS valor_padrao_desc,
     CASE TA.cod_tipo WHEN
         4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         null
     END AS valor_desc,
     AD.ajuda,
     AD.mascara,
     TA.cod_tipo,
     TA.nom_tipo,
     VALOR.valor,
     VALOR.timestamp
  FROM
     administracao.atributo_dinamico          AS AD,
     administracao.tipo_atributo              AS TA,
     administracao.atributo_dinamico AS ACA
     LEFT JOIN
     economico.atributo_empresa_direito_valor         AS VALOR
  ON ( ACA.cod_atributo = VALOR.cod_atributo
          AND ACA.cod_cadastro = VALOR.cod_cadastro
              AND VALOR.inscricao_economica = ''''||inInscricaoEconomica||''''
 AND ACA.cod_atributo = 5049
         AND CAST(VALOR.timestamp as varchar)||CAST(VALOR.cod_atributo as varchar) IN (
             SELECT
         (CAST(max(VALOR.timestamp) as varchar))::varchar||CAST(VALOR.cod_atributo as varchar)
             FROM
                administracao.atributo_dinamico AS ACA,
                economico.atributo_empresa_direito_valor         AS VALOR,
                administracao.atributo_dinamico          AS AD,
                administracao.tipo_atributo              AS TA
             WHERE
                ACA.cod_atributo = AD.cod_atributo
                AND ACA.cod_cadastro = AD.cod_cadastro
                AND ACA.cod_modulo   = AD.cod_modulo
             AND ACA.cod_atributo = VALOR.cod_atributo
             AND ACA.cod_cadastro = VALOR.cod_cadastro
             AND ACA.cod_modulo   = VALOR.cod_modulo
              AND VALOR.inscricao_economica = ''''||inInscricaoEconomica||''''
 AND ACA.cod_atributo = 5049
             AND AD.cod_tipo = TA.cod_tipo
             AND ACA.ativo = true
             AND AD.cod_modulo   =14
             AND AD.cod_cadastro=2

             GROUP BY VALOR.cod_cadastro, VALOR.cod_atributo
                                  )

     )
  WHERE
      AD.cod_tipo = TA.cod_tipo
  AND ACA.ativo = true
  AND     AD.ativo
  AND AD.cod_atributo =  ACA.cod_atributo
  AND AD.cod_modulo   = ACA.cod_modulo
  AND AD.cod_cadastro = ACA.cod_cadastro
  AND ACA.cod_cadastro=2
  AND ACA.cod_modulo  =14


 AND ACA.cod_atributo = 5049;'''';OPEN crCursor FOR EXECUTE stSql;
                                    FETCH crCursor INTO rsRetorno;
                                    CLOSE crCursor;
                                    RETURN rsRetorno.valor;
                                    END;
                                    '' LANGUAGE plpgsql;
             ';
    EXECUTE stSQL;

    stSQL := '
CREATE OR REPLACE FUNCTION recuperaCadastroEconomicoInscricaoEconomicaEmpresaDeDireitoInscricaoMunicipal(  INTEGER ) RETURNS VARCHAR AS ''DECLARE stSql VARCHAR; crCursor  REFCURSOR; rsRetorno RECORD;inInscricaoEconomica ALIAS FOR $1;BEGIN stSql := '''' SELECT
     AD.cod_cadastro,
     AD.cod_atributo,
     AD.ativo,
     AD.nao_nulo,
     AD.nom_atributo,
     CASE TA.cod_tipo
         WHEN 4 THEN  administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,'''''''''''''''')
     END AS valor_padrao,
     CASE TA.cod_tipo
       WHEN 3 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,''''''''''''''''))
       WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,VALOR.valor))
         ELSE         null
     END AS valor_padrao_desc,
     CASE TA.cod_tipo WHEN
         4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         null
     END AS valor_desc,
     AD.ajuda,
     AD.mascara,
     TA.cod_tipo,
     TA.nom_tipo,
     VALOR.valor,
     VALOR.timestamp
  FROM
     administracao.atributo_dinamico          AS AD,
     administracao.tipo_atributo              AS TA,
     administracao.atributo_dinamico AS ACA
     LEFT JOIN
     economico.atributo_empresa_direito_valor         AS VALOR
  ON ( ACA.cod_atributo = VALOR.cod_atributo
          AND ACA.cod_cadastro = VALOR.cod_cadastro
              AND VALOR.inscricao_economica = ''''||inInscricaoEconomica||''''
 AND ACA.cod_atributo = 5048
         AND CAST(VALOR.timestamp as varchar)||CAST(VALOR.cod_atributo as varchar) IN (
             SELECT
         (CAST(max(VALOR.timestamp) as varchar))::varchar||CAST(VALOR.cod_atributo as varchar)
             FROM
                administracao.atributo_dinamico AS ACA,
                economico.atributo_empresa_direito_valor         AS VALOR,
                administracao.atributo_dinamico          AS AD,
                administracao.tipo_atributo              AS TA
             WHERE
                ACA.cod_atributo = AD.cod_atributo
                AND ACA.cod_cadastro = AD.cod_cadastro
                AND ACA.cod_modulo   = AD.cod_modulo
             AND ACA.cod_atributo = VALOR.cod_atributo
             AND ACA.cod_cadastro = VALOR.cod_cadastro
             AND ACA.cod_modulo   = VALOR.cod_modulo
              AND VALOR.inscricao_economica = ''''||inInscricaoEconomica||''''
 AND ACA.cod_atributo = 5048
             AND AD.cod_tipo = TA.cod_tipo
             AND ACA.ativo = true
             AND AD.cod_modulo   =14
             AND AD.cod_cadastro=2

             GROUP BY VALOR.cod_cadastro, VALOR.cod_atributo
                                  )

     )
  WHERE
      AD.cod_tipo = TA.cod_tipo
  AND ACA.ativo = true
  AND     AD.ativo
  AND AD.cod_atributo =  ACA.cod_atributo
  AND AD.cod_modulo   = ACA.cod_modulo
  AND AD.cod_cadastro = ACA.cod_cadastro
  AND ACA.cod_cadastro=2
  AND ACA.cod_modulo  =14


 AND ACA.cod_atributo = 5048;'''';OPEN crCursor FOR EXECUTE stSql;
                                    FETCH crCursor INTO rsRetorno;
                                    CLOSE crCursor;
                                    RETURN rsRetorno.valor;
                                    END;
                                    '' LANGUAGE plpgsql;
             ';
    EXECUTE stSQL;

    stSQL := '
CREATE OR REPLACE FUNCTION recuperaCadastroEconomicoInscricaoEconomicaEmpresaDeDireitoISSIsento(  INTEGER ) RETURNS VARCHAR AS ''DECLARE stSql VARCHAR; crCursor  REFCURSOR; rsRetorno RECORD;inInscricaoEconomica ALIAS FOR $1;BEGIN stSql := '''' SELECT
     AD.cod_cadastro,
     AD.cod_atributo,
     AD.ativo,
     AD.nao_nulo,
     AD.nom_atributo,
     CASE TA.cod_tipo
         WHEN 4 THEN  administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,'''''''''''''''')
     END AS valor_padrao,
     CASE TA.cod_tipo
       WHEN 3 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,''''''''''''''''))
       WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,VALOR.valor))
         ELSE         null
     END AS valor_padrao_desc,
     CASE TA.cod_tipo WHEN
         4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         null
     END AS valor_desc,
     AD.ajuda,
     AD.mascara,
     TA.cod_tipo,
     TA.nom_tipo,
     VALOR.valor,
     VALOR.timestamp
  FROM
     administracao.atributo_dinamico          AS AD,
     administracao.tipo_atributo              AS TA,
     administracao.atributo_dinamico AS ACA
     LEFT JOIN
     economico.atributo_empresa_direito_valor         AS VALOR
  ON ( ACA.cod_atributo = VALOR.cod_atributo
          AND ACA.cod_cadastro = VALOR.cod_cadastro
              AND VALOR.inscricao_economica = ''''||inInscricaoEconomica||''''
 AND ACA.cod_atributo = 5050
         AND CAST(VALOR.timestamp as varchar)||CAST(VALOR.cod_atributo as varchar) IN (
             SELECT
         (CAST(max(VALOR.timestamp) as varchar))::varchar||CAST(VALOR.cod_atributo as varchar)
             FROM
                administracao.atributo_dinamico AS ACA,
                economico.atributo_empresa_direito_valor         AS VALOR,
                administracao.atributo_dinamico          AS AD,
                administracao.tipo_atributo              AS TA
             WHERE
                ACA.cod_atributo = AD.cod_atributo
                AND ACA.cod_cadastro = AD.cod_cadastro
                AND ACA.cod_modulo   = AD.cod_modulo
             AND ACA.cod_atributo = VALOR.cod_atributo
             AND ACA.cod_cadastro = VALOR.cod_cadastro
             AND ACA.cod_modulo   = VALOR.cod_modulo
              AND VALOR.inscricao_economica = ''''||inInscricaoEconomica||''''
 AND ACA.cod_atributo = 5050
             AND AD.cod_tipo = TA.cod_tipo
             AND ACA.ativo = true
             AND AD.cod_modulo   =14
             AND AD.cod_cadastro=2

             GROUP BY VALOR.cod_cadastro, VALOR.cod_atributo
                                  )

     )
  WHERE
      AD.cod_tipo = TA.cod_tipo
  AND ACA.ativo = true
  AND     AD.ativo
  AND AD.cod_atributo =  ACA.cod_atributo
  AND AD.cod_modulo   = ACA.cod_modulo
  AND AD.cod_cadastro = ACA.cod_cadastro
  AND ACA.cod_cadastro=2
  AND ACA.cod_modulo  =14


 AND ACA.cod_atributo = 5050;'''';OPEN crCursor FOR EXECUTE stSql;
                                    FETCH crCursor INTO rsRetorno;
                                    CLOSE crCursor;
                                    RETURN rsRetorno.valor;
                                    END;
                                    '' LANGUAGE plpgsql;
             ';
    EXECUTE stSQL;

    stSQL := '
CREATE OR REPLACE FUNCTION recuperaCadastroEconomicoInscricaoEconomicaEmpresaDeDireitoCZCoeficienteDasZonasSetor(  INTEGER ) RETURNS VARCHAR AS ''DECLARE stSql VARCHAR; crCursor  REFCURSOR; rsRetorno RECORD;inInscricaoEconomica ALIAS FOR $1;BEGIN stSql := '''' SELECT
     AD.cod_cadastro,
     AD.cod_atributo,
     AD.ativo,
     AD.nao_nulo,
     AD.nom_atributo,
     CASE TA.cod_tipo
         WHEN 4 THEN  administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,'''''''''''''''')
     END AS valor_padrao,
     CASE TA.cod_tipo
       WHEN 3 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,''''''''''''''''))
       WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,VALOR.valor))
         ELSE         null
     END AS valor_padrao_desc,
     CASE TA.cod_tipo WHEN
         4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         null
     END AS valor_desc,
     AD.ajuda,
     AD.mascara,
     TA.cod_tipo,
     TA.nom_tipo,
     VALOR.valor,
     VALOR.timestamp
  FROM
     administracao.atributo_dinamico          AS AD,
     administracao.tipo_atributo              AS TA,
     administracao.atributo_dinamico AS ACA
     LEFT JOIN
     economico.atributo_empresa_direito_valor         AS VALOR
  ON ( ACA.cod_atributo = VALOR.cod_atributo
          AND ACA.cod_cadastro = VALOR.cod_cadastro
              AND VALOR.inscricao_economica = ''''||inInscricaoEconomica||''''
 AND ACA.cod_atributo = 5057
         AND CAST(VALOR.timestamp as varchar)||CAST(VALOR.cod_atributo as varchar) IN (
             SELECT
         (CAST(max(VALOR.timestamp) as varchar))::varchar||CAST(VALOR.cod_atributo as varchar)
             FROM
                administracao.atributo_dinamico AS ACA,
                economico.atributo_empresa_direito_valor         AS VALOR,
                administracao.atributo_dinamico          AS AD,
                administracao.tipo_atributo              AS TA
             WHERE
                ACA.cod_atributo = AD.cod_atributo
                AND ACA.cod_cadastro = AD.cod_cadastro
                AND ACA.cod_modulo   = AD.cod_modulo
             AND ACA.cod_atributo = VALOR.cod_atributo
             AND ACA.cod_cadastro = VALOR.cod_cadastro
             AND ACA.cod_modulo   = VALOR.cod_modulo
              AND VALOR.inscricao_economica = ''''||inInscricaoEconomica||''''
 AND ACA.cod_atributo = 5057
             AND AD.cod_tipo = TA.cod_tipo
             AND ACA.ativo = true
             AND AD.cod_modulo   =14
             AND AD.cod_cadastro=2

             GROUP BY VALOR.cod_cadastro, VALOR.cod_atributo
                                  )

     )
  WHERE
      AD.cod_tipo = TA.cod_tipo
  AND ACA.ativo = true
  AND     AD.ativo
  AND AD.cod_atributo =  ACA.cod_atributo
  AND AD.cod_modulo   = ACA.cod_modulo
  AND AD.cod_cadastro = ACA.cod_cadastro
  AND ACA.cod_cadastro=2
  AND ACA.cod_modulo  =14


 AND ACA.cod_atributo = 5057;'''';OPEN crCursor FOR EXECUTE stSql;
                                    FETCH crCursor INTO rsRetorno;
                                    CLOSE crCursor;
                                    RETURN rsRetorno.valor;
                                    END;
                                    '' LANGUAGE plpgsql;
             ';
    EXECUTE stSQL;

    stSQL := '
CREATE OR REPLACE FUNCTION recuperaCadastroEconomicoInscricaoEconomicaEmpresaDeDireitoCECoeficienteDeEdificacao(  INTEGER ) RETURNS VARCHAR AS ''DECLARE stSql VARCHAR; crCursor  REFCURSOR; rsRetorno RECORD;inInscricaoEconomica ALIAS FOR $1;BEGIN stSql := '''' SELECT
     AD.cod_cadastro,
     AD.cod_atributo,
     AD.ativo,
     AD.nao_nulo,
     AD.nom_atributo,
     CASE TA.cod_tipo
         WHEN 4 THEN  administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,'''''''''''''''')
     END AS valor_padrao,
     CASE TA.cod_tipo
       WHEN 3 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,''''''''''''''''))
       WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,VALOR.valor))
         ELSE         null
     END AS valor_padrao_desc,
     CASE TA.cod_tipo WHEN
         4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         null
     END AS valor_desc,
     AD.ajuda,
     AD.mascara,
     TA.cod_tipo,
     TA.nom_tipo,
     VALOR.valor,
     VALOR.timestamp
  FROM
     administracao.atributo_dinamico          AS AD,
     administracao.tipo_atributo              AS TA,
     administracao.atributo_dinamico AS ACA
     LEFT JOIN
     economico.atributo_empresa_direito_valor         AS VALOR
  ON ( ACA.cod_atributo = VALOR.cod_atributo
          AND ACA.cod_cadastro = VALOR.cod_cadastro
              AND VALOR.inscricao_economica = ''''||inInscricaoEconomica||''''
 AND ACA.cod_atributo = 5058
         AND CAST(VALOR.timestamp as varchar)||CAST(VALOR.cod_atributo as varchar) IN (
             SELECT
         (CAST(max(VALOR.timestamp) as varchar))::varchar||CAST(VALOR.cod_atributo as varchar)
             FROM
                administracao.atributo_dinamico AS ACA,
                economico.atributo_empresa_direito_valor         AS VALOR,
                administracao.atributo_dinamico          AS AD,
                administracao.tipo_atributo              AS TA
             WHERE
                ACA.cod_atributo = AD.cod_atributo
                AND ACA.cod_cadastro = AD.cod_cadastro
                AND ACA.cod_modulo   = AD.cod_modulo
             AND ACA.cod_atributo = VALOR.cod_atributo
             AND ACA.cod_cadastro = VALOR.cod_cadastro
             AND ACA.cod_modulo   = VALOR.cod_modulo
              AND VALOR.inscricao_economica = ''''||inInscricaoEconomica||''''
 AND ACA.cod_atributo = 5058
             AND AD.cod_tipo = TA.cod_tipo
             AND ACA.ativo = true
             AND AD.cod_modulo   =14
             AND AD.cod_cadastro=2

             GROUP BY VALOR.cod_cadastro, VALOR.cod_atributo
                                  )

     )
  WHERE
      AD.cod_tipo = TA.cod_tipo
  AND ACA.ativo = true
  AND     AD.ativo
  AND AD.cod_atributo =  ACA.cod_atributo
  AND AD.cod_modulo   = ACA.cod_modulo
  AND AD.cod_cadastro = ACA.cod_cadastro
  AND ACA.cod_cadastro=2
  AND ACA.cod_modulo  =14


 AND ACA.cod_atributo = 5058;'''';OPEN crCursor FOR EXECUTE stSql;
                                    FETCH crCursor INTO rsRetorno;
                                    CLOSE crCursor;
                                    RETURN rsRetorno.valor;
                                    END;
                                    '' LANGUAGE plpgsql;
             ';
    EXECUTE stSQL;

    stSQL := '
CREATE OR REPLACE FUNCTION recuperaCadastroEconomicoInscricaoEconomicaEmpresaDeDireitoKNCoeficienteDoNumeroDeEmpregadosCCCoeficienteDaCategoria(  INTEGER ) RETURNS VARCHAR AS ''DECLARE stSql VARCHAR; crCursor  REFCURSOR; rsRetorno RECORD;inInscricaoEconomica ALIAS FOR $1;BEGIN stSql := '''' SELECT
     AD.cod_cadastro,
     AD.cod_atributo,
     AD.ativo,
     AD.nao_nulo,
     AD.nom_atributo,
     CASE TA.cod_tipo
         WHEN 4 THEN  administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,'''''''''''''''')
     END AS valor_padrao,
     CASE TA.cod_tipo
       WHEN 3 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,''''''''''''''''))
       WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,VALOR.valor))
         ELSE         null
     END AS valor_padrao_desc,
     CASE TA.cod_tipo WHEN
         4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         null
     END AS valor_desc,
     AD.ajuda,
     AD.mascara,
     TA.cod_tipo,
     TA.nom_tipo,
     VALOR.valor,
     VALOR.timestamp
  FROM
     administracao.atributo_dinamico          AS AD,
     administracao.tipo_atributo              AS TA,
     administracao.atributo_dinamico AS ACA
     LEFT JOIN
     economico.atributo_empresa_direito_valor         AS VALOR
  ON ( ACA.cod_atributo = VALOR.cod_atributo
          AND ACA.cod_cadastro = VALOR.cod_cadastro
              AND VALOR.inscricao_economica = ''''||inInscricaoEconomica||''''
 AND ACA.cod_atributo = 5059
         AND CAST(VALOR.timestamp as varchar)||CAST(VALOR.cod_atributo as varchar) IN (
             SELECT
         (CAST(max(VALOR.timestamp) as varchar))::varchar||CAST(VALOR.cod_atributo as varchar)
             FROM
                administracao.atributo_dinamico AS ACA,
                economico.atributo_empresa_direito_valor         AS VALOR,
                administracao.atributo_dinamico          AS AD,
                administracao.tipo_atributo              AS TA
             WHERE
                ACA.cod_atributo = AD.cod_atributo
                AND ACA.cod_cadastro = AD.cod_cadastro
                AND ACA.cod_modulo   = AD.cod_modulo
             AND ACA.cod_atributo = VALOR.cod_atributo
             AND ACA.cod_cadastro = VALOR.cod_cadastro
             AND ACA.cod_modulo   = VALOR.cod_modulo
              AND VALOR.inscricao_economica = ''''||inInscricaoEconomica||''''
 AND ACA.cod_atributo = 5059
             AND AD.cod_tipo = TA.cod_tipo
             AND ACA.ativo = true
             AND AD.cod_modulo   =14
             AND AD.cod_cadastro=2

             GROUP BY VALOR.cod_cadastro, VALOR.cod_atributo
                                  )

     )
  WHERE
      AD.cod_tipo = TA.cod_tipo
  AND ACA.ativo = true
  AND     AD.ativo
  AND AD.cod_atributo =  ACA.cod_atributo
  AND AD.cod_modulo   = ACA.cod_modulo
  AND AD.cod_cadastro = ACA.cod_cadastro
  AND ACA.cod_cadastro=2
  AND ACA.cod_modulo  =14


 AND ACA.cod_atributo = 5059;'''';OPEN crCursor FOR EXECUTE stSql;
                                    FETCH crCursor INTO rsRetorno;
                                    CLOSE crCursor;
                                    RETURN rsRetorno.valor;
                                    END;
                                    '' LANGUAGE plpgsql;
             ';
    EXECUTE stSQL;

    stSQL := '
CREATE OR REPLACE FUNCTION recuperaCadastroEconomicoInscricaoEconomicoEmpresaDeFatoCZCoeficienteDasZonasSetor(  INTEGER ) RETURNS VARCHAR AS ''DECLARE stSql VARCHAR; crCursor  REFCURSOR; rsRetorno RECORD;inInscricaoEconomica ALIAS FOR $1;BEGIN stSql := '''' SELECT
     AD.cod_cadastro,
     AD.cod_atributo,
     AD.ativo,
     AD.nao_nulo,
     AD.nom_atributo,
     CASE TA.cod_tipo
         WHEN 4 THEN  administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,'''''''''''''''')
     END AS valor_padrao,
     CASE TA.cod_tipo
       WHEN 3 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,''''''''''''''''))
       WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,VALOR.valor))
         ELSE         null
     END AS valor_padrao_desc,
     CASE TA.cod_tipo WHEN
         4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         null
     END AS valor_desc,
     AD.ajuda,
     AD.mascara,
     TA.cod_tipo,
     TA.nom_tipo,
     VALOR.valor,
     VALOR.timestamp
  FROM
     administracao.atributo_dinamico          AS AD,
     administracao.tipo_atributo              AS TA,
     administracao.atributo_dinamico AS ACA
     LEFT JOIN
     economico.atributo_empresa_fato_valor         AS VALOR
  ON ( ACA.cod_atributo = VALOR.cod_atributo
          AND ACA.cod_cadastro = VALOR.cod_cadastro
              AND VALOR.inscricao_economica = ''''||inInscricaoEconomica||''''
 AND ACA.cod_atributo = 5053
         AND CAST(VALOR.timestamp as varchar)||CAST(VALOR.cod_atributo as varchar) IN (
             SELECT
         (CAST(max(VALOR.timestamp) as varchar))::varchar||CAST(VALOR.cod_atributo as varchar)
             FROM
                administracao.atributo_dinamico AS ACA,
                economico.atributo_empresa_fato_valor         AS VALOR,
                administracao.atributo_dinamico          AS AD,
                administracao.tipo_atributo              AS TA
             WHERE
                ACA.cod_atributo = AD.cod_atributo
                AND ACA.cod_cadastro = AD.cod_cadastro
                AND ACA.cod_modulo   = AD.cod_modulo
             AND ACA.cod_atributo = VALOR.cod_atributo
             AND ACA.cod_cadastro = VALOR.cod_cadastro
             AND ACA.cod_modulo   = VALOR.cod_modulo
              AND VALOR.inscricao_economica = ''''||inInscricaoEconomica||''''
 AND ACA.cod_atributo = 5053
             AND AD.cod_tipo = TA.cod_tipo
             AND ACA.ativo = true
             AND AD.cod_modulo   =14
             AND AD.cod_cadastro=1

             GROUP BY VALOR.cod_cadastro, VALOR.cod_atributo
                                  )

     )
  WHERE
      AD.cod_tipo = TA.cod_tipo
  AND ACA.ativo = true
  AND     AD.ativo
  AND AD.cod_atributo =  ACA.cod_atributo
  AND AD.cod_modulo   = ACA.cod_modulo
  AND AD.cod_cadastro = ACA.cod_cadastro
  AND ACA.cod_cadastro=1
  AND ACA.cod_modulo  =14


 AND ACA.cod_atributo = 5053;'''';OPEN crCursor FOR EXECUTE stSql;
                                    FETCH crCursor INTO rsRetorno;
                                    CLOSE crCursor;
                                    RETURN rsRetorno.valor;
                                    END;
                                    '' LANGUAGE plpgsql;
             ';
    EXECUTE stSQL;

    stSQL := '
CREATE OR REPLACE FUNCTION recuperaCadastroEconomicoInscricaoEconomicoEmpresaDeFatoCECoeficienteDeEdificacao(  INTEGER ) RETURNS VARCHAR AS ''DECLARE stSql VARCHAR; crCursor  REFCURSOR; rsRetorno RECORD;inInscricaoEconomica ALIAS FOR $1;BEGIN stSql := '''' SELECT
     AD.cod_cadastro,
     AD.cod_atributo,
     AD.ativo,
     AD.nao_nulo,
     AD.nom_atributo,
     CASE TA.cod_tipo
         WHEN 4 THEN  administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,'''''''''''''''')
     END AS valor_padrao,
     CASE TA.cod_tipo
       WHEN 3 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,''''''''''''''''))
       WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,VALOR.valor))
         ELSE         null
     END AS valor_padrao_desc,
     CASE TA.cod_tipo WHEN
         4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         null
     END AS valor_desc,
     AD.ajuda,
     AD.mascara,
     TA.cod_tipo,
     TA.nom_tipo,
     VALOR.valor,
     VALOR.timestamp
  FROM
     administracao.atributo_dinamico          AS AD,
     administracao.tipo_atributo              AS TA,
     administracao.atributo_dinamico AS ACA
     LEFT JOIN
     economico.atributo_empresa_fato_valor         AS VALOR
  ON ( ACA.cod_atributo = VALOR.cod_atributo
          AND ACA.cod_cadastro = VALOR.cod_cadastro
              AND VALOR.inscricao_economica = ''''||inInscricaoEconomica||''''
 AND ACA.cod_atributo = 5054
         AND CAST(VALOR.timestamp as varchar)||CAST(VALOR.cod_atributo as varchar) IN (
             SELECT
         (CAST(max(VALOR.timestamp) as varchar))::varchar||CAST(VALOR.cod_atributo as varchar)
             FROM
                administracao.atributo_dinamico AS ACA,
                economico.atributo_empresa_fato_valor         AS VALOR,
                administracao.atributo_dinamico          AS AD,
                administracao.tipo_atributo              AS TA
             WHERE
                ACA.cod_atributo = AD.cod_atributo
                AND ACA.cod_cadastro = AD.cod_cadastro
                AND ACA.cod_modulo   = AD.cod_modulo
             AND ACA.cod_atributo = VALOR.cod_atributo
             AND ACA.cod_cadastro = VALOR.cod_cadastro
             AND ACA.cod_modulo   = VALOR.cod_modulo
              AND VALOR.inscricao_economica = ''''||inInscricaoEconomica||''''
 AND ACA.cod_atributo = 5054
             AND AD.cod_tipo = TA.cod_tipo
             AND ACA.ativo = true
             AND AD.cod_modulo   =14
             AND AD.cod_cadastro=1

             GROUP BY VALOR.cod_cadastro, VALOR.cod_atributo
                                  )

     )
  WHERE
      AD.cod_tipo = TA.cod_tipo
  AND ACA.ativo = true
  AND     AD.ativo
  AND AD.cod_atributo =  ACA.cod_atributo
  AND AD.cod_modulo   = ACA.cod_modulo
  AND AD.cod_cadastro = ACA.cod_cadastro
  AND ACA.cod_cadastro=1
  AND ACA.cod_modulo  =14


 AND ACA.cod_atributo = 5054;'''';OPEN crCursor FOR EXECUTE stSql;
                                    FETCH crCursor INTO rsRetorno;
                                    CLOSE crCursor;
                                    RETURN rsRetorno.valor;
                                    END;
                                    '' LANGUAGE plpgsql;
             ';
    EXECUTE stSQL;

    stSQL := '
CREATE OR REPLACE FUNCTION recuperaCadastroEconomicoInscricaoEconomicoEmpresaDeFatoKNCoeficienteDoNumeroDeEmpregadosCCCoeficienteDaCategoria(  INTEGER ) RETURNS VARCHAR AS ''DECLARE stSql VARCHAR; crCursor  REFCURSOR; rsRetorno RECORD;inInscricaoEconomica ALIAS FOR $1;BEGIN stSql := '''' SELECT
     AD.cod_cadastro,
     AD.cod_atributo,
     AD.ativo,
     AD.nao_nulo,
     AD.nom_atributo,
     CASE TA.cod_tipo
         WHEN 4 THEN  administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,'''''''''''''''')
     END AS valor_padrao,
     CASE TA.cod_tipo
       WHEN 3 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,''''''''''''''''))
       WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,VALOR.valor))
         ELSE         null
     END AS valor_padrao_desc,
     CASE TA.cod_tipo WHEN
         4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         null
     END AS valor_desc,
     AD.ajuda,
     AD.mascara,
     TA.cod_tipo,
     TA.nom_tipo,
     VALOR.valor,
     VALOR.timestamp
  FROM
     administracao.atributo_dinamico          AS AD,
     administracao.tipo_atributo              AS TA,
     administracao.atributo_dinamico AS ACA
     LEFT JOIN
     economico.atributo_empresa_fato_valor         AS VALOR
  ON ( ACA.cod_atributo = VALOR.cod_atributo
          AND ACA.cod_cadastro = VALOR.cod_cadastro
              AND VALOR.inscricao_economica = ''''||inInscricaoEconomica||''''
 AND ACA.cod_atributo = 5055
         AND CAST(VALOR.timestamp as varchar)||CAST(VALOR.cod_atributo as varchar) IN (
             SELECT
         (CAST(max(VALOR.timestamp) as varchar))::varchar||CAST(VALOR.cod_atributo as varchar)
             FROM
                administracao.atributo_dinamico AS ACA,
                economico.atributo_empresa_fato_valor         AS VALOR,
                administracao.atributo_dinamico          AS AD,
                administracao.tipo_atributo              AS TA
             WHERE
                ACA.cod_atributo = AD.cod_atributo
                AND ACA.cod_cadastro = AD.cod_cadastro
                AND ACA.cod_modulo   = AD.cod_modulo
             AND ACA.cod_atributo = VALOR.cod_atributo
             AND ACA.cod_cadastro = VALOR.cod_cadastro
             AND ACA.cod_modulo   = VALOR.cod_modulo
              AND VALOR.inscricao_economica = ''''||inInscricaoEconomica||''''
 AND ACA.cod_atributo = 5055
             AND AD.cod_tipo = TA.cod_tipo
             AND ACA.ativo = true
             AND AD.cod_modulo   =14
             AND AD.cod_cadastro=1

             GROUP BY VALOR.cod_cadastro, VALOR.cod_atributo
                                  )

     )
  WHERE
      AD.cod_tipo = TA.cod_tipo
  AND ACA.ativo = true
  AND     AD.ativo
  AND AD.cod_atributo =  ACA.cod_atributo
  AND AD.cod_modulo   = ACA.cod_modulo
  AND AD.cod_cadastro = ACA.cod_cadastro
  AND ACA.cod_cadastro=1
  AND ACA.cod_modulo  =14


 AND ACA.cod_atributo = 5055;'''';OPEN crCursor FOR EXECUTE stSql;
                                    FETCH crCursor INTO rsRetorno;
                                    CLOSE crCursor;
                                    RETURN rsRetorno.valor;
                                    END;
                                    '' LANGUAGE plpgsql;
             ';
    EXECUTE stSQL;

    stSQL := '
CREATE OR REPLACE FUNCTION recuperaCadastroEconomicoInscricaoEconomicoEmpresaDeFatoAlvaraIsento(  INTEGER ) RETURNS VARCHAR AS ''DECLARE stSql VARCHAR; crCursor  REFCURSOR; rsRetorno RECORD;inInscricaoEconomica ALIAS FOR $1;BEGIN stSql := '''' SELECT
     AD.cod_cadastro,
     AD.cod_atributo,
     AD.ativo,
     AD.nao_nulo,
     AD.nom_atributo,
     CASE TA.cod_tipo
         WHEN 4 THEN  administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,'''''''''''''''')
     END AS valor_padrao,
     CASE TA.cod_tipo
       WHEN 3 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,''''''''''''''''))
       WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,VALOR.valor))
         ELSE         null
     END AS valor_padrao_desc,
     CASE TA.cod_tipo WHEN
         4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         null
     END AS valor_desc,
     AD.ajuda,
     AD.mascara,
     TA.cod_tipo,
     TA.nom_tipo,
     VALOR.valor,
     VALOR.timestamp
  FROM
     administracao.atributo_dinamico          AS AD,
     administracao.tipo_atributo              AS TA,
     administracao.atributo_dinamico AS ACA
     LEFT JOIN
     economico.atributo_empresa_fato_valor         AS VALOR
  ON ( ACA.cod_atributo = VALOR.cod_atributo
          AND ACA.cod_cadastro = VALOR.cod_cadastro
              AND VALOR.inscricao_economica = ''''||inInscricaoEconomica||''''
 AND ACA.cod_atributo = 5056
         AND CAST(VALOR.timestamp as varchar)||CAST(VALOR.cod_atributo as varchar) IN (
             SELECT
         (CAST(max(VALOR.timestamp) as varchar))::varchar||CAST(VALOR.cod_atributo as varchar)
             FROM
                administracao.atributo_dinamico AS ACA,
                economico.atributo_empresa_fato_valor         AS VALOR,
                administracao.atributo_dinamico          AS AD,
                administracao.tipo_atributo              AS TA
             WHERE
                ACA.cod_atributo = AD.cod_atributo
                AND ACA.cod_cadastro = AD.cod_cadastro
                AND ACA.cod_modulo   = AD.cod_modulo
             AND ACA.cod_atributo = VALOR.cod_atributo
             AND ACA.cod_cadastro = VALOR.cod_cadastro
             AND ACA.cod_modulo   = VALOR.cod_modulo
              AND VALOR.inscricao_economica = ''''||inInscricaoEconomica||''''
 AND ACA.cod_atributo = 5056
             AND AD.cod_tipo = TA.cod_tipo
             AND ACA.ativo = true
             AND AD.cod_modulo   =14
             AND AD.cod_cadastro=1

             GROUP BY VALOR.cod_cadastro, VALOR.cod_atributo
                                  )

     )
  WHERE
      AD.cod_tipo = TA.cod_tipo
  AND ACA.ativo = true
  AND     AD.ativo
  AND AD.cod_atributo =  ACA.cod_atributo
  AND AD.cod_modulo   = ACA.cod_modulo
  AND AD.cod_cadastro = ACA.cod_cadastro
  AND ACA.cod_cadastro=1
  AND ACA.cod_modulo  =14


 AND ACA.cod_atributo = 5056;'''';OPEN crCursor FOR EXECUTE stSql;
                                    FETCH crCursor INTO rsRetorno;
                                    CLOSE crCursor;
                                    RETURN rsRetorno.valor;
                                    END;
                                    '' LANGUAGE plpgsql;
             ';
    EXECUTE stSQL;

    stSQL := '
CREATE OR REPLACE FUNCTION recuperaCadastroEconomicoInscricaoEconomicoEmpresaDeFatoISSIsento(  INTEGER ) RETURNS VARCHAR AS ''DECLARE stSql VARCHAR; crCursor  REFCURSOR; rsRetorno RECORD;inInscricaoEconomica ALIAS FOR $1;BEGIN stSql := '''' SELECT
     AD.cod_cadastro,
     AD.cod_atributo,
     AD.ativo,
     AD.nao_nulo,
     AD.nom_atributo,
     CASE TA.cod_tipo
         WHEN 4 THEN  administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,'''''''''''''''')
     END AS valor_padrao,
     CASE TA.cod_tipo
       WHEN 3 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,''''''''''''''''))
       WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,VALOR.valor))
         ELSE         null
     END AS valor_padrao_desc,
     CASE TA.cod_tipo WHEN
         4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         null
     END AS valor_desc,
     AD.ajuda,
     AD.mascara,
     TA.cod_tipo,
     TA.nom_tipo,
     VALOR.valor,
     VALOR.timestamp
  FROM
     administracao.atributo_dinamico          AS AD,
     administracao.tipo_atributo              AS TA,
     administracao.atributo_dinamico AS ACA
     LEFT JOIN
     economico.atributo_empresa_fato_valor         AS VALOR
  ON ( ACA.cod_atributo = VALOR.cod_atributo
          AND ACA.cod_cadastro = VALOR.cod_cadastro
              AND VALOR.inscricao_economica = ''''||inInscricaoEconomica||''''
 AND ACA.cod_atributo = 5064
         AND CAST(VALOR.timestamp as varchar)||CAST(VALOR.cod_atributo as varchar) IN (
             SELECT
         (CAST(max(VALOR.timestamp) as varchar))::varchar||CAST(VALOR.cod_atributo as varchar)
             FROM
                administracao.atributo_dinamico AS ACA,
                economico.atributo_empresa_fato_valor         AS VALOR,
                administracao.atributo_dinamico          AS AD,
                administracao.tipo_atributo              AS TA
             WHERE
                ACA.cod_atributo = AD.cod_atributo
                AND ACA.cod_cadastro = AD.cod_cadastro
                AND ACA.cod_modulo   = AD.cod_modulo
             AND ACA.cod_atributo = VALOR.cod_atributo
             AND ACA.cod_cadastro = VALOR.cod_cadastro
             AND ACA.cod_modulo   = VALOR.cod_modulo
              AND VALOR.inscricao_economica = ''''||inInscricaoEconomica||''''
 AND ACA.cod_atributo = 5064
             AND AD.cod_tipo = TA.cod_tipo
             AND ACA.ativo = true
             AND AD.cod_modulo   =14
             AND AD.cod_cadastro=1

             GROUP BY VALOR.cod_cadastro, VALOR.cod_atributo
                                  )

     )
  WHERE
      AD.cod_tipo = TA.cod_tipo
  AND ACA.ativo = true
  AND     AD.ativo
  AND AD.cod_atributo =  ACA.cod_atributo
  AND AD.cod_modulo   = ACA.cod_modulo
  AND AD.cod_cadastro = ACA.cod_cadastro
  AND ACA.cod_cadastro=1
  AND ACA.cod_modulo  =14


 AND ACA.cod_atributo = 5064;'''';OPEN crCursor FOR EXECUTE stSql;
                                    FETCH crCursor INTO rsRetorno;
                                    CLOSE crCursor;
                                    RETURN rsRetorno.valor;
                                    END;
                                    '' LANGUAGE plpgsql;
             ';
    EXECUTE stSQL;

    stSQL := '
CREATE OR REPLACE FUNCTION recuperaCadastroImobiliarioFaceDeQuadraPavimentacao(  INTEGER, INTEGER ) RETURNS VARCHAR AS ''DECLARE stSql VARCHAR; crCursor  REFCURSOR; rsRetorno RECORD;inCodFace ALIAS FOR $1;inCodLocalizacao ALIAS FOR $2;BEGIN stSql := '''' SELECT
     AD.cod_cadastro,
     AD.cod_atributo,
     AD.ativo,
     AD.nao_nulo,
     AD.nom_atributo,
     CASE TA.cod_tipo
         WHEN 4 THEN  administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,'''''''''''''''')
     END AS valor_padrao,
     CASE TA.cod_tipo
       WHEN 3 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,''''''''''''''''))
       WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,VALOR.valor))
         ELSE         null
     END AS valor_padrao_desc,
     CASE TA.cod_tipo WHEN
         4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         null
     END AS valor_desc,
     AD.ajuda,
     AD.mascara,
     TA.cod_tipo,
     TA.nom_tipo,
     VALOR.valor,
     VALOR.timestamp
  FROM
     administracao.atributo_dinamico          AS AD,
     administracao.tipo_atributo              AS TA,
     administracao.atributo_dinamico AS ACA
     LEFT JOIN
     imobiliario.atributo_face_quadra_valor         AS VALOR
  ON ( ACA.cod_atributo = VALOR.cod_atributo
          AND ACA.cod_cadastro = VALOR.cod_cadastro
              AND VALOR.cod_face = ''''||inCodFace||''''
 AND VALOR.cod_localizacao = ''''||inCodLocalizacao||''''
 AND ACA.cod_atributo = 5030
         AND CAST(VALOR.timestamp as varchar)||CAST(VALOR.cod_atributo as varchar) IN (
             SELECT
         (CAST(max(VALOR.timestamp) as varchar))::varchar||CAST(VALOR.cod_atributo as varchar)
             FROM
                administracao.atributo_dinamico AS ACA,
                imobiliario.atributo_face_quadra_valor         AS VALOR,
                administracao.atributo_dinamico          AS AD,
                administracao.tipo_atributo              AS TA
             WHERE
                ACA.cod_atributo = AD.cod_atributo
                AND ACA.cod_cadastro = AD.cod_cadastro
                AND ACA.cod_modulo   = AD.cod_modulo
             AND ACA.cod_atributo = VALOR.cod_atributo
             AND ACA.cod_cadastro = VALOR.cod_cadastro
             AND ACA.cod_modulo   = VALOR.cod_modulo
              AND VALOR.cod_face = ''''||inCodFace||''''
 AND VALOR.cod_localizacao = ''''||inCodLocalizacao||''''
 AND ACA.cod_atributo = 5030
             AND AD.cod_tipo = TA.cod_tipo
             AND ACA.ativo = true
             AND AD.cod_modulo   =12
             AND AD.cod_cadastro=8

             GROUP BY VALOR.cod_cadastro, VALOR.cod_atributo
                                  )

     )
  WHERE
      AD.cod_tipo = TA.cod_tipo
  AND ACA.ativo = true
  AND     AD.ativo
  AND AD.cod_atributo =  ACA.cod_atributo
  AND AD.cod_modulo   = ACA.cod_modulo
  AND AD.cod_cadastro = ACA.cod_cadastro
  AND ACA.cod_cadastro=8
  AND ACA.cod_modulo  =12


 AND ACA.cod_atributo = 5030;'''';OPEN crCursor FOR EXECUTE stSql;
                                    FETCH crCursor INTO rsRetorno;
                                    CLOSE crCursor;
                                    RETURN rsRetorno.valor;
                                    END;
                                    '' LANGUAGE plpgsql;
             ';
    EXECUTE stSQL;

    stSQL := '
CREATE OR REPLACE FUNCTION recuperaCadastroImobiliarioFaceDeQuadraColetaDeLixo(  INTEGER, INTEGER ) RETURNS VARCHAR AS ''DECLARE stSql VARCHAR; crCursor  REFCURSOR; rsRetorno RECORD;inCodFace ALIAS FOR $1;inCodLocalizacao ALIAS FOR $2;BEGIN stSql := '''' SELECT
     AD.cod_cadastro,
     AD.cod_atributo,
     AD.ativo,
     AD.nao_nulo,
     AD.nom_atributo,
     CASE TA.cod_tipo
         WHEN 4 THEN  administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,'''''''''''''''')
     END AS valor_padrao,
     CASE TA.cod_tipo
       WHEN 3 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,''''''''''''''''))
       WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,VALOR.valor))
         ELSE         null
     END AS valor_padrao_desc,
     CASE TA.cod_tipo WHEN
         4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         null
     END AS valor_desc,
     AD.ajuda,
     AD.mascara,
     TA.cod_tipo,
     TA.nom_tipo,
     VALOR.valor,
     VALOR.timestamp
  FROM
     administracao.atributo_dinamico          AS AD,
     administracao.tipo_atributo              AS TA,
     administracao.atributo_dinamico AS ACA
     LEFT JOIN
     imobiliario.atributo_face_quadra_valor         AS VALOR
  ON ( ACA.cod_atributo = VALOR.cod_atributo
          AND ACA.cod_cadastro = VALOR.cod_cadastro
              AND VALOR.cod_face = ''''||inCodFace||''''
 AND VALOR.cod_localizacao = ''''||inCodLocalizacao||''''
 AND ACA.cod_atributo = 5036
         AND CAST(VALOR.timestamp as varchar)||CAST(VALOR.cod_atributo as varchar) IN (
             SELECT
         (CAST(max(VALOR.timestamp) as varchar))::varchar||CAST(VALOR.cod_atributo as varchar)
             FROM
                administracao.atributo_dinamico AS ACA,
                imobiliario.atributo_face_quadra_valor         AS VALOR,
                administracao.atributo_dinamico          AS AD,
                administracao.tipo_atributo              AS TA
             WHERE
                ACA.cod_atributo = AD.cod_atributo
                AND ACA.cod_cadastro = AD.cod_cadastro
                AND ACA.cod_modulo   = AD.cod_modulo
             AND ACA.cod_atributo = VALOR.cod_atributo
             AND ACA.cod_cadastro = VALOR.cod_cadastro
             AND ACA.cod_modulo   = VALOR.cod_modulo
              AND VALOR.cod_face = ''''||inCodFace||''''
 AND VALOR.cod_localizacao = ''''||inCodLocalizacao||''''
 AND ACA.cod_atributo = 5036
             AND AD.cod_tipo = TA.cod_tipo
             AND ACA.ativo = true
             AND AD.cod_modulo   =12
             AND AD.cod_cadastro=8

             GROUP BY VALOR.cod_cadastro, VALOR.cod_atributo
                                  )

     )
  WHERE
      AD.cod_tipo = TA.cod_tipo
  AND ACA.ativo = true
  AND     AD.ativo
  AND AD.cod_atributo =  ACA.cod_atributo
  AND AD.cod_modulo   = ACA.cod_modulo
  AND AD.cod_cadastro = ACA.cod_cadastro
  AND ACA.cod_cadastro=8
  AND ACA.cod_modulo  =12


 AND ACA.cod_atributo = 5036;'''';OPEN crCursor FOR EXECUTE stSql;
                                    FETCH crCursor INTO rsRetorno;
                                    CLOSE crCursor;
                                    RETURN rsRetorno.valor;
                                    END;
                                    '' LANGUAGE plpgsql;
             ';
    EXECUTE stSQL;

    stSQL := '
CREATE OR REPLACE FUNCTION recuperaCadastroImobiliarioFaceDeQuadraLimpezaPublica(  INTEGER, INTEGER ) RETURNS VARCHAR AS ''DECLARE stSql VARCHAR; crCursor  REFCURSOR; rsRetorno RECORD;inCodFace ALIAS FOR $1;inCodLocalizacao ALIAS FOR $2;BEGIN stSql := '''' SELECT
     AD.cod_cadastro,
     AD.cod_atributo,
     AD.ativo,
     AD.nao_nulo,
     AD.nom_atributo,
     CASE TA.cod_tipo
         WHEN 4 THEN  administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,'''''''''''''''')
     END AS valor_padrao,
     CASE TA.cod_tipo
       WHEN 3 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,''''''''''''''''))
       WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,VALOR.valor))
         ELSE         null
     END AS valor_padrao_desc,
     CASE TA.cod_tipo WHEN
         4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         null
     END AS valor_desc,
     AD.ajuda,
     AD.mascara,
     TA.cod_tipo,
     TA.nom_tipo,
     VALOR.valor,
     VALOR.timestamp
  FROM
     administracao.atributo_dinamico          AS AD,
     administracao.tipo_atributo              AS TA,
     administracao.atributo_dinamico AS ACA
     LEFT JOIN
     imobiliario.atributo_face_quadra_valor         AS VALOR
  ON ( ACA.cod_atributo = VALOR.cod_atributo
          AND ACA.cod_cadastro = VALOR.cod_cadastro
              AND VALOR.cod_face = ''''||inCodFace||''''
 AND VALOR.cod_localizacao = ''''||inCodLocalizacao||''''
 AND ACA.cod_atributo = 5029
         AND CAST(VALOR.timestamp as varchar)||CAST(VALOR.cod_atributo as varchar) IN (
             SELECT
         (CAST(max(VALOR.timestamp) as varchar))::varchar||CAST(VALOR.cod_atributo as varchar)
             FROM
                administracao.atributo_dinamico AS ACA,
                imobiliario.atributo_face_quadra_valor         AS VALOR,
                administracao.atributo_dinamico          AS AD,
                administracao.tipo_atributo              AS TA
             WHERE
                ACA.cod_atributo = AD.cod_atributo
                AND ACA.cod_cadastro = AD.cod_cadastro
                AND ACA.cod_modulo   = AD.cod_modulo
             AND ACA.cod_atributo = VALOR.cod_atributo
             AND ACA.cod_cadastro = VALOR.cod_cadastro
             AND ACA.cod_modulo   = VALOR.cod_modulo
              AND VALOR.cod_face = ''''||inCodFace||''''
 AND VALOR.cod_localizacao = ''''||inCodLocalizacao||''''
 AND ACA.cod_atributo = 5029
             AND AD.cod_tipo = TA.cod_tipo
             AND ACA.ativo = true
             AND AD.cod_modulo   =12
             AND AD.cod_cadastro=8

             GROUP BY VALOR.cod_cadastro, VALOR.cod_atributo
                                  )

     )
  WHERE
      AD.cod_tipo = TA.cod_tipo
  AND ACA.ativo = true
  AND     AD.ativo
  AND AD.cod_atributo =  ACA.cod_atributo
  AND AD.cod_modulo   = ACA.cod_modulo
  AND AD.cod_cadastro = ACA.cod_cadastro
  AND ACA.cod_cadastro=8
  AND ACA.cod_modulo  =12


 AND ACA.cod_atributo = 5029;'''';OPEN crCursor FOR EXECUTE stSql;
                                    FETCH crCursor INTO rsRetorno;
                                    CLOSE crCursor;
                                    RETURN rsRetorno.valor;
                                    END;
                                    '' LANGUAGE plpgsql;
             ';
    EXECUTE stSQL;

    stSQL := '
CREATE OR REPLACE FUNCTION recuperaCadastroImobiliarioFaceDeQuadraRedeDeEsgoto(  INTEGER, INTEGER ) RETURNS VARCHAR AS ''DECLARE stSql VARCHAR; crCursor  REFCURSOR; rsRetorno RECORD;inCodFace ALIAS FOR $1;inCodLocalizacao ALIAS FOR $2;BEGIN stSql := '''' SELECT
     AD.cod_cadastro,
     AD.cod_atributo,
     AD.ativo,
     AD.nao_nulo,
     AD.nom_atributo,
     CASE TA.cod_tipo
         WHEN 4 THEN  administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,'''''''''''''''')
     END AS valor_padrao,
     CASE TA.cod_tipo
       WHEN 3 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,''''''''''''''''))
       WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,VALOR.valor))
         ELSE         null
     END AS valor_padrao_desc,
     CASE TA.cod_tipo WHEN
         4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         null
     END AS valor_desc,
     AD.ajuda,
     AD.mascara,
     TA.cod_tipo,
     TA.nom_tipo,
     VALOR.valor,
     VALOR.timestamp
  FROM
     administracao.atributo_dinamico          AS AD,
     administracao.tipo_atributo              AS TA,
     administracao.atributo_dinamico AS ACA
     LEFT JOIN
     imobiliario.atributo_face_quadra_valor         AS VALOR
  ON ( ACA.cod_atributo = VALOR.cod_atributo
          AND ACA.cod_cadastro = VALOR.cod_cadastro
              AND VALOR.cod_face = ''''||inCodFace||''''
 AND VALOR.cod_localizacao = ''''||inCodLocalizacao||''''
 AND ACA.cod_atributo = 5028
         AND CAST(VALOR.timestamp as varchar)||CAST(VALOR.cod_atributo as varchar) IN (
             SELECT
         (CAST(max(VALOR.timestamp) as varchar))::varchar||CAST(VALOR.cod_atributo as varchar)
             FROM
                administracao.atributo_dinamico AS ACA,
                imobiliario.atributo_face_quadra_valor         AS VALOR,
                administracao.atributo_dinamico          AS AD,
                administracao.tipo_atributo              AS TA
             WHERE
                ACA.cod_atributo = AD.cod_atributo
                AND ACA.cod_cadastro = AD.cod_cadastro
                AND ACA.cod_modulo   = AD.cod_modulo
             AND ACA.cod_atributo = VALOR.cod_atributo
             AND ACA.cod_cadastro = VALOR.cod_cadastro
             AND ACA.cod_modulo   = VALOR.cod_modulo
              AND VALOR.cod_face = ''''||inCodFace||''''
 AND VALOR.cod_localizacao = ''''||inCodLocalizacao||''''
 AND ACA.cod_atributo = 5028
             AND AD.cod_tipo = TA.cod_tipo
             AND ACA.ativo = true
             AND AD.cod_modulo   =12
             AND AD.cod_cadastro=8

             GROUP BY VALOR.cod_cadastro, VALOR.cod_atributo
                                  )

     )
  WHERE
      AD.cod_tipo = TA.cod_tipo
  AND ACA.ativo = true
  AND     AD.ativo
  AND AD.cod_atributo =  ACA.cod_atributo
  AND AD.cod_modulo   = ACA.cod_modulo
  AND AD.cod_cadastro = ACA.cod_cadastro
  AND ACA.cod_cadastro=8
  AND ACA.cod_modulo  =12


 AND ACA.cod_atributo = 5028;'''';OPEN crCursor FOR EXECUTE stSql;
                                    FETCH crCursor INTO rsRetorno;
                                    CLOSE crCursor;
                                    RETURN rsRetorno.valor;
                                    END;
                                    '' LANGUAGE plpgsql;
             ';
    EXECUTE stSQL;

    stSQL := '
CREATE OR REPLACE FUNCTION recuperaCadastroImobiliarioFaceDeQuadraRedeDeAgua(  INTEGER, INTEGER ) RETURNS VARCHAR AS ''DECLARE stSql VARCHAR; crCursor  REFCURSOR; rsRetorno RECORD;inCodFace ALIAS FOR $1;inCodLocalizacao ALIAS FOR $2;BEGIN stSql := '''' SELECT
     AD.cod_cadastro,
     AD.cod_atributo,
     AD.ativo,
     AD.nao_nulo,
     AD.nom_atributo,
     CASE TA.cod_tipo
         WHEN 4 THEN  administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,'''''''''''''''')
     END AS valor_padrao,
     CASE TA.cod_tipo
       WHEN 3 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,''''''''''''''''))
       WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,VALOR.valor))
         ELSE         null
     END AS valor_padrao_desc,
     CASE TA.cod_tipo WHEN
         4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         null
     END AS valor_desc,
     AD.ajuda,
     AD.mascara,
     TA.cod_tipo,
     TA.nom_tipo,
     VALOR.valor,
     VALOR.timestamp
  FROM
     administracao.atributo_dinamico          AS AD,
     administracao.tipo_atributo              AS TA,
     administracao.atributo_dinamico AS ACA
     LEFT JOIN
     imobiliario.atributo_face_quadra_valor         AS VALOR
  ON ( ACA.cod_atributo = VALOR.cod_atributo
          AND ACA.cod_cadastro = VALOR.cod_cadastro
              AND VALOR.cod_face = ''''||inCodFace||''''
 AND VALOR.cod_localizacao = ''''||inCodLocalizacao||''''
 AND ACA.cod_atributo = 5027
         AND CAST(VALOR.timestamp as varchar)||CAST(VALOR.cod_atributo as varchar) IN (
             SELECT
         (CAST(max(VALOR.timestamp) as varchar))::varchar||CAST(VALOR.cod_atributo as varchar)
             FROM
                administracao.atributo_dinamico AS ACA,
                imobiliario.atributo_face_quadra_valor         AS VALOR,
                administracao.atributo_dinamico          AS AD,
                administracao.tipo_atributo              AS TA
             WHERE
                ACA.cod_atributo = AD.cod_atributo
                AND ACA.cod_cadastro = AD.cod_cadastro
                AND ACA.cod_modulo   = AD.cod_modulo
             AND ACA.cod_atributo = VALOR.cod_atributo
             AND ACA.cod_cadastro = VALOR.cod_cadastro
             AND ACA.cod_modulo   = VALOR.cod_modulo
              AND VALOR.cod_face = ''''||inCodFace||''''
 AND VALOR.cod_localizacao = ''''||inCodLocalizacao||''''
 AND ACA.cod_atributo = 5027
             AND AD.cod_tipo = TA.cod_tipo
             AND ACA.ativo = true
             AND AD.cod_modulo   =12
             AND AD.cod_cadastro=8

             GROUP BY VALOR.cod_cadastro, VALOR.cod_atributo
                                  )

     )
  WHERE
      AD.cod_tipo = TA.cod_tipo
  AND ACA.ativo = true
  AND     AD.ativo
  AND AD.cod_atributo =  ACA.cod_atributo
  AND AD.cod_modulo   = ACA.cod_modulo
  AND AD.cod_cadastro = ACA.cod_cadastro
  AND ACA.cod_cadastro=8
  AND ACA.cod_modulo  =12


 AND ACA.cod_atributo = 5027;'''';OPEN crCursor FOR EXECUTE stSql;
                                    FETCH crCursor INTO rsRetorno;
                                    CLOSE crCursor;
                                    RETURN rsRetorno.valor;
                                    END;
                                    '' LANGUAGE plpgsql;
             ';
    EXECUTE stSQL;

    stSQL := '
CREATE OR REPLACE FUNCTION recuperaCadastroImobiliarioFaceDeQuadraGaleriasFluviais(  INTEGER, INTEGER ) RETURNS VARCHAR AS ''DECLARE stSql VARCHAR; crCursor  REFCURSOR; rsRetorno RECORD;inCodFace ALIAS FOR $1;inCodLocalizacao ALIAS FOR $2;BEGIN stSql := '''' SELECT
     AD.cod_cadastro,
     AD.cod_atributo,
     AD.ativo,
     AD.nao_nulo,
     AD.nom_atributo,
     CASE TA.cod_tipo
         WHEN 4 THEN  administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,'''''''''''''''')
     END AS valor_padrao,
     CASE TA.cod_tipo
       WHEN 3 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,''''''''''''''''))
       WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,VALOR.valor))
         ELSE         null
     END AS valor_padrao_desc,
     CASE TA.cod_tipo WHEN
         4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         null
     END AS valor_desc,
     AD.ajuda,
     AD.mascara,
     TA.cod_tipo,
     TA.nom_tipo,
     VALOR.valor,
     VALOR.timestamp
  FROM
     administracao.atributo_dinamico          AS AD,
     administracao.tipo_atributo              AS TA,
     administracao.atributo_dinamico AS ACA
     LEFT JOIN
     imobiliario.atributo_face_quadra_valor         AS VALOR
  ON ( ACA.cod_atributo = VALOR.cod_atributo
          AND ACA.cod_cadastro = VALOR.cod_cadastro
              AND VALOR.cod_face = ''''||inCodFace||''''
 AND VALOR.cod_localizacao = ''''||inCodLocalizacao||''''
 AND ACA.cod_atributo = 5031
         AND CAST(VALOR.timestamp as varchar)||CAST(VALOR.cod_atributo as varchar) IN (
             SELECT
         (CAST(max(VALOR.timestamp) as varchar))::varchar||CAST(VALOR.cod_atributo as varchar)
             FROM
                administracao.atributo_dinamico AS ACA,
                imobiliario.atributo_face_quadra_valor         AS VALOR,
                administracao.atributo_dinamico          AS AD,
                administracao.tipo_atributo              AS TA
             WHERE
                ACA.cod_atributo = AD.cod_atributo
                AND ACA.cod_cadastro = AD.cod_cadastro
                AND ACA.cod_modulo   = AD.cod_modulo
             AND ACA.cod_atributo = VALOR.cod_atributo
             AND ACA.cod_cadastro = VALOR.cod_cadastro
             AND ACA.cod_modulo   = VALOR.cod_modulo
              AND VALOR.cod_face = ''''||inCodFace||''''
 AND VALOR.cod_localizacao = ''''||inCodLocalizacao||''''
 AND ACA.cod_atributo = 5031
             AND AD.cod_tipo = TA.cod_tipo
             AND ACA.ativo = true
             AND AD.cod_modulo   =12
             AND AD.cod_cadastro=8

             GROUP BY VALOR.cod_cadastro, VALOR.cod_atributo
                                  )

     )
  WHERE
      AD.cod_tipo = TA.cod_tipo
  AND ACA.ativo = true
  AND     AD.ativo
  AND AD.cod_atributo =  ACA.cod_atributo
  AND AD.cod_modulo   = ACA.cod_modulo
  AND AD.cod_cadastro = ACA.cod_cadastro
  AND ACA.cod_cadastro=8
  AND ACA.cod_modulo  =12


 AND ACA.cod_atributo = 5031;'''';OPEN crCursor FOR EXECUTE stSql;
                                    FETCH crCursor INTO rsRetorno;
                                    CLOSE crCursor;
                                    RETURN rsRetorno.valor;
                                    END;
                                    '' LANGUAGE plpgsql;
             ';
    EXECUTE stSQL;

    stSQL := '
CREATE OR REPLACE FUNCTION recuperaCadastroImobiliarioFaceDeQuadraIluminacaoPublica(  INTEGER, INTEGER ) RETURNS VARCHAR AS ''DECLARE stSql VARCHAR; crCursor  REFCURSOR; rsRetorno RECORD;inCodFace ALIAS FOR $1;inCodLocalizacao ALIAS FOR $2;BEGIN stSql := '''' SELECT
     AD.cod_cadastro,
     AD.cod_atributo,
     AD.ativo,
     AD.nao_nulo,
     AD.nom_atributo,
     CASE TA.cod_tipo
         WHEN 4 THEN  administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,'''''''''''''''')
     END AS valor_padrao,
     CASE TA.cod_tipo
       WHEN 3 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,''''''''''''''''))
       WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,VALOR.valor))
         ELSE         null
     END AS valor_padrao_desc,
     CASE TA.cod_tipo WHEN
         4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         null
     END AS valor_desc,
     AD.ajuda,
     AD.mascara,
     TA.cod_tipo,
     TA.nom_tipo,
     VALOR.valor,
     VALOR.timestamp
  FROM
     administracao.atributo_dinamico          AS AD,
     administracao.tipo_atributo              AS TA,
     administracao.atributo_dinamico AS ACA
     LEFT JOIN
     imobiliario.atributo_face_quadra_valor         AS VALOR
  ON ( ACA.cod_atributo = VALOR.cod_atributo
          AND ACA.cod_cadastro = VALOR.cod_cadastro
              AND VALOR.cod_face = ''''||inCodFace||''''
 AND VALOR.cod_localizacao = ''''||inCodLocalizacao||''''
 AND ACA.cod_atributo = 5034
         AND CAST(VALOR.timestamp as varchar)||CAST(VALOR.cod_atributo as varchar) IN (
             SELECT
         (CAST(max(VALOR.timestamp) as varchar))::varchar||CAST(VALOR.cod_atributo as varchar)
             FROM
                administracao.atributo_dinamico AS ACA,
                imobiliario.atributo_face_quadra_valor         AS VALOR,
                administracao.atributo_dinamico          AS AD,
                administracao.tipo_atributo              AS TA
             WHERE
                ACA.cod_atributo = AD.cod_atributo
                AND ACA.cod_cadastro = AD.cod_cadastro
                AND ACA.cod_modulo   = AD.cod_modulo
             AND ACA.cod_atributo = VALOR.cod_atributo
             AND ACA.cod_cadastro = VALOR.cod_cadastro
             AND ACA.cod_modulo   = VALOR.cod_modulo
              AND VALOR.cod_face = ''''||inCodFace||''''
 AND VALOR.cod_localizacao = ''''||inCodLocalizacao||''''
 AND ACA.cod_atributo = 5034
             AND AD.cod_tipo = TA.cod_tipo
             AND ACA.ativo = true
             AND AD.cod_modulo   =12
             AND AD.cod_cadastro=8

             GROUP BY VALOR.cod_cadastro, VALOR.cod_atributo
                                  )

     )
  WHERE
      AD.cod_tipo = TA.cod_tipo
  AND ACA.ativo = true
  AND     AD.ativo
  AND AD.cod_atributo =  ACA.cod_atributo
  AND AD.cod_modulo   = ACA.cod_modulo
  AND AD.cod_cadastro = ACA.cod_cadastro
  AND ACA.cod_cadastro=8
  AND ACA.cod_modulo  =12


 AND ACA.cod_atributo = 5034;'''';OPEN crCursor FOR EXECUTE stSql;
                                    FETCH crCursor INTO rsRetorno;
                                    CLOSE crCursor;
                                    RETURN rsRetorno.valor;
                                    END;
                                    '' LANGUAGE plpgsql;
             ';
    EXECUTE stSQL;

    stSQL := '
CREATE OR REPLACE FUNCTION recuperaCadastroImobiliarioFaceDeQuadraRedeEletrica(  INTEGER, INTEGER ) RETURNS VARCHAR AS ''DECLARE stSql VARCHAR; crCursor  REFCURSOR; rsRetorno RECORD;inCodFace ALIAS FOR $1;inCodLocalizacao ALIAS FOR $2;BEGIN stSql := '''' SELECT
     AD.cod_cadastro,
     AD.cod_atributo,
     AD.ativo,
     AD.nao_nulo,
     AD.nom_atributo,
     CASE TA.cod_tipo
         WHEN 4 THEN  administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,'''''''''''''''')
     END AS valor_padrao,
     CASE TA.cod_tipo
       WHEN 3 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,''''''''''''''''))
       WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,VALOR.valor))
         ELSE         null
     END AS valor_padrao_desc,
     CASE TA.cod_tipo WHEN
         4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         null
     END AS valor_desc,
     AD.ajuda,
     AD.mascara,
     TA.cod_tipo,
     TA.nom_tipo,
     VALOR.valor,
     VALOR.timestamp
  FROM
     administracao.atributo_dinamico          AS AD,
     administracao.tipo_atributo              AS TA,
     administracao.atributo_dinamico AS ACA
     LEFT JOIN
     imobiliario.atributo_face_quadra_valor         AS VALOR
  ON ( ACA.cod_atributo = VALOR.cod_atributo
          AND ACA.cod_cadastro = VALOR.cod_cadastro
              AND VALOR.cod_face = ''''||inCodFace||''''
 AND VALOR.cod_localizacao = ''''||inCodLocalizacao||''''
 AND ACA.cod_atributo = 5033
         AND CAST(VALOR.timestamp as varchar)||CAST(VALOR.cod_atributo as varchar) IN (
             SELECT
         (CAST(max(VALOR.timestamp) as varchar))::varchar||CAST(VALOR.cod_atributo as varchar)
             FROM
                administracao.atributo_dinamico AS ACA,
                imobiliario.atributo_face_quadra_valor         AS VALOR,
                administracao.atributo_dinamico          AS AD,
                administracao.tipo_atributo              AS TA
             WHERE
                ACA.cod_atributo = AD.cod_atributo
                AND ACA.cod_cadastro = AD.cod_cadastro
                AND ACA.cod_modulo   = AD.cod_modulo
             AND ACA.cod_atributo = VALOR.cod_atributo
             AND ACA.cod_cadastro = VALOR.cod_cadastro
             AND ACA.cod_modulo   = VALOR.cod_modulo
              AND VALOR.cod_face = ''''||inCodFace||''''
 AND VALOR.cod_localizacao = ''''||inCodLocalizacao||''''
 AND ACA.cod_atributo = 5033
             AND AD.cod_tipo = TA.cod_tipo
             AND ACA.ativo = true
             AND AD.cod_modulo   =12
             AND AD.cod_cadastro=8

             GROUP BY VALOR.cod_cadastro, VALOR.cod_atributo
                                  )

     )
  WHERE
      AD.cod_tipo = TA.cod_tipo
  AND ACA.ativo = true
  AND     AD.ativo
  AND AD.cod_atributo =  ACA.cod_atributo
  AND AD.cod_modulo   = ACA.cod_modulo
  AND AD.cod_cadastro = ACA.cod_cadastro
  AND ACA.cod_cadastro=8
  AND ACA.cod_modulo  =12


 AND ACA.cod_atributo = 5033;'''';OPEN crCursor FOR EXECUTE stSql;
                                    FETCH crCursor INTO rsRetorno;
                                    CLOSE crCursor;
                                    RETURN rsRetorno.valor;
                                    END;
                                    '' LANGUAGE plpgsql;
             ';
    EXECUTE stSQL;

    stSQL := '
CREATE OR REPLACE FUNCTION recuperaCadastroImobiliarioFaceDeQuadraSargetaMeioFio(  INTEGER, INTEGER ) RETURNS VARCHAR AS ''DECLARE stSql VARCHAR; crCursor  REFCURSOR; rsRetorno RECORD;inCodFace ALIAS FOR $1;inCodLocalizacao ALIAS FOR $2;BEGIN stSql := '''' SELECT
     AD.cod_cadastro,
     AD.cod_atributo,
     AD.ativo,
     AD.nao_nulo,
     AD.nom_atributo,
     CASE TA.cod_tipo
         WHEN 4 THEN  administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,'''''''''''''''')
     END AS valor_padrao,
     CASE TA.cod_tipo
       WHEN 3 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,''''''''''''''''))
       WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,VALOR.valor))
         ELSE         null
     END AS valor_padrao_desc,
     CASE TA.cod_tipo WHEN
         4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         null
     END AS valor_desc,
     AD.ajuda,
     AD.mascara,
     TA.cod_tipo,
     TA.nom_tipo,
     VALOR.valor,
     VALOR.timestamp
  FROM
     administracao.atributo_dinamico          AS AD,
     administracao.tipo_atributo              AS TA,
     administracao.atributo_dinamico AS ACA
     LEFT JOIN
     imobiliario.atributo_face_quadra_valor         AS VALOR
  ON ( ACA.cod_atributo = VALOR.cod_atributo
          AND ACA.cod_cadastro = VALOR.cod_cadastro
              AND VALOR.cod_face = ''''||inCodFace||''''
 AND VALOR.cod_localizacao = ''''||inCodLocalizacao||''''
 AND ACA.cod_atributo = 5032
         AND CAST(VALOR.timestamp as varchar)||CAST(VALOR.cod_atributo as varchar) IN (
             SELECT
         (CAST(max(VALOR.timestamp) as varchar))::varchar||CAST(VALOR.cod_atributo as varchar)
             FROM
                administracao.atributo_dinamico AS ACA,
                imobiliario.atributo_face_quadra_valor         AS VALOR,
                administracao.atributo_dinamico          AS AD,
                administracao.tipo_atributo              AS TA
             WHERE
                ACA.cod_atributo = AD.cod_atributo
                AND ACA.cod_cadastro = AD.cod_cadastro
                AND ACA.cod_modulo   = AD.cod_modulo
             AND ACA.cod_atributo = VALOR.cod_atributo
             AND ACA.cod_cadastro = VALOR.cod_cadastro
             AND ACA.cod_modulo   = VALOR.cod_modulo
              AND VALOR.cod_face = ''''||inCodFace||''''
 AND VALOR.cod_localizacao = ''''||inCodLocalizacao||''''
 AND ACA.cod_atributo = 5032
             AND AD.cod_tipo = TA.cod_tipo
             AND ACA.ativo = true
             AND AD.cod_modulo   =12
             AND AD.cod_cadastro=8

             GROUP BY VALOR.cod_cadastro, VALOR.cod_atributo
                                  )

     )
  WHERE
      AD.cod_tipo = TA.cod_tipo
  AND ACA.ativo = true
  AND     AD.ativo
  AND AD.cod_atributo =  ACA.cod_atributo
  AND AD.cod_modulo   = ACA.cod_modulo
  AND AD.cod_cadastro = ACA.cod_cadastro
  AND ACA.cod_cadastro=8
  AND ACA.cod_modulo  =12


 AND ACA.cod_atributo = 5032;'''';OPEN crCursor FOR EXECUTE stSql;
                                    FETCH crCursor INTO rsRetorno;
                                    CLOSE crCursor;
                                    RETURN rsRetorno.valor;
                                    END;
                                    '' LANGUAGE plpgsql;
             ';
    EXECUTE stSQL;

    stSQL := '
CREATE OR REPLACE FUNCTION recuperaCadastroImobiliarioFaceDeQuadraRedeTelefonica(  INTEGER, INTEGER ) RETURNS VARCHAR AS ''DECLARE stSql VARCHAR; crCursor  REFCURSOR; rsRetorno RECORD;inCodFace ALIAS FOR $1;inCodLocalizacao ALIAS FOR $2;BEGIN stSql := '''' SELECT
     AD.cod_cadastro,
     AD.cod_atributo,
     AD.ativo,
     AD.nao_nulo,
     AD.nom_atributo,
     CASE TA.cod_tipo
         WHEN 4 THEN  administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,'''''''''''''''')
     END AS valor_padrao,
     CASE TA.cod_tipo
       WHEN 3 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,''''''''''''''''))
       WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,VALOR.valor))
         ELSE         null
     END AS valor_padrao_desc,
     CASE TA.cod_tipo WHEN
         4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         null
     END AS valor_desc,
     AD.ajuda,
     AD.mascara,
     TA.cod_tipo,
     TA.nom_tipo,
     VALOR.valor,
     VALOR.timestamp
  FROM
     administracao.atributo_dinamico          AS AD,
     administracao.tipo_atributo              AS TA,
     administracao.atributo_dinamico AS ACA
     LEFT JOIN
     imobiliario.atributo_face_quadra_valor         AS VALOR
  ON ( ACA.cod_atributo = VALOR.cod_atributo
          AND ACA.cod_cadastro = VALOR.cod_cadastro
              AND VALOR.cod_face = ''''||inCodFace||''''
 AND VALOR.cod_localizacao = ''''||inCodLocalizacao||''''
 AND ACA.cod_atributo = 5035
         AND CAST(VALOR.timestamp as varchar)||CAST(VALOR.cod_atributo as varchar) IN (
             SELECT
         (CAST(max(VALOR.timestamp) as varchar))::varchar||CAST(VALOR.cod_atributo as varchar)
             FROM
                administracao.atributo_dinamico AS ACA,
                imobiliario.atributo_face_quadra_valor         AS VALOR,
                administracao.atributo_dinamico          AS AD,
                administracao.tipo_atributo              AS TA
             WHERE
                ACA.cod_atributo = AD.cod_atributo
                AND ACA.cod_cadastro = AD.cod_cadastro
                AND ACA.cod_modulo   = AD.cod_modulo
             AND ACA.cod_atributo = VALOR.cod_atributo
             AND ACA.cod_cadastro = VALOR.cod_cadastro
             AND ACA.cod_modulo   = VALOR.cod_modulo
              AND VALOR.cod_face = ''''||inCodFace||''''
 AND VALOR.cod_localizacao = ''''||inCodLocalizacao||''''
 AND ACA.cod_atributo = 5035
             AND AD.cod_tipo = TA.cod_tipo
             AND ACA.ativo = true
             AND AD.cod_modulo   =12
             AND AD.cod_cadastro=8

             GROUP BY VALOR.cod_cadastro, VALOR.cod_atributo
                                  )

     )
  WHERE
      AD.cod_tipo = TA.cod_tipo
  AND ACA.ativo = true
  AND     AD.ativo
  AND AD.cod_atributo =  ACA.cod_atributo
  AND AD.cod_modulo   = ACA.cod_modulo
  AND AD.cod_cadastro = ACA.cod_cadastro
  AND ACA.cod_cadastro=8
  AND ACA.cod_modulo  =12


 AND ACA.cod_atributo = 5035;'''';OPEN crCursor FOR EXECUTE stSql;
                                    FETCH crCursor INTO rsRetorno;
                                    CLOSE crCursor;
                                    RETURN rsRetorno.valor;
                                    END;
                                    '' LANGUAGE plpgsql;
             ';
    EXECUTE stSQL;

    stSQL := '
CREATE OR REPLACE FUNCTION recuperaCadastroImobiliarioConstrucaoOutrosCECoeficienteDeEdificacao(  INTEGER ) RETURNS VARCHAR AS ''DECLARE stSql VARCHAR; crCursor  REFCURSOR; rsRetorno RECORD;inCodConstrucao ALIAS FOR $1;BEGIN stSql := '''' SELECT
     AD.cod_cadastro,
     AD.cod_atributo,
     AD.ativo,
     AD.nao_nulo,
     AD.nom_atributo,
     CASE TA.cod_tipo
         WHEN 4 THEN  administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,'''''''''''''''')
     END AS valor_padrao,
     CASE TA.cod_tipo
       WHEN 3 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,''''''''''''''''))
       WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,VALOR.valor))
         ELSE         null
     END AS valor_padrao_desc,
     CASE TA.cod_tipo WHEN
         4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         null
     END AS valor_desc,
     AD.ajuda,
     AD.mascara,
     TA.cod_tipo,
     TA.nom_tipo,
     VALOR.valor,
     VALOR.timestamp
  FROM
     administracao.atributo_dinamico          AS AD,
     administracao.tipo_atributo              AS TA,
     administracao.atributo_dinamico AS ACA
     LEFT JOIN
     imobiliario.atributo_construcao_outros_valor         AS VALOR
  ON ( ACA.cod_atributo = VALOR.cod_atributo
          AND ACA.cod_cadastro = VALOR.cod_cadastro
              AND VALOR.cod_construcao = ''''||inCodConstrucao||''''
 AND ACA.cod_atributo = 5051
         AND CAST(VALOR.timestamp as varchar)||CAST(VALOR.cod_atributo as varchar) IN (
             SELECT
         (CAST(max(VALOR.timestamp) as varchar))::varchar||CAST(VALOR.cod_atributo as varchar)
             FROM
                administracao.atributo_dinamico AS ACA,
                imobiliario.atributo_construcao_outros_valor         AS VALOR,
                administracao.atributo_dinamico          AS AD,
                administracao.tipo_atributo              AS TA
             WHERE
                ACA.cod_atributo = AD.cod_atributo
                AND ACA.cod_cadastro = AD.cod_cadastro
                AND ACA.cod_modulo   = AD.cod_modulo
             AND ACA.cod_atributo = VALOR.cod_atributo
             AND ACA.cod_cadastro = VALOR.cod_cadastro
             AND ACA.cod_modulo   = VALOR.cod_modulo
              AND VALOR.cod_construcao = ''''||inCodConstrucao||''''
 AND ACA.cod_atributo = 5051
             AND AD.cod_tipo = TA.cod_tipo
             AND ACA.ativo = true
             AND AD.cod_modulo   =12
             AND AD.cod_cadastro=9

             GROUP BY VALOR.cod_cadastro, VALOR.cod_atributo
                                  )

     )
  WHERE
      AD.cod_tipo = TA.cod_tipo
  AND ACA.ativo = true
  AND     AD.ativo
  AND AD.cod_atributo =  ACA.cod_atributo
  AND AD.cod_modulo   = ACA.cod_modulo
  AND AD.cod_cadastro = ACA.cod_cadastro
  AND ACA.cod_cadastro=9
  AND ACA.cod_modulo  =12


 AND ACA.cod_atributo = 5051;'''';OPEN crCursor FOR EXECUTE stSql;
                                    FETCH crCursor INTO rsRetorno;
                                    CLOSE crCursor;
                                    RETURN rsRetorno.valor;
                                    END;
                                    '' LANGUAGE plpgsql;
             ';
    EXECUTE stSQL;

    stSQL := '
CREATE OR REPLACE FUNCTION recuperaCadastroImobiliarioImovelIPTU(  INTEGER ) RETURNS VARCHAR AS ''DECLARE stSql VARCHAR; crCursor  REFCURSOR; rsRetorno RECORD;inInscricaoMunicipal ALIAS FOR $1;BEGIN stSql := '''' SELECT
     AD.cod_cadastro,
     AD.cod_atributo,
     AD.ativo,
     AD.nao_nulo,
     AD.nom_atributo,
     CASE TA.cod_tipo
         WHEN 4 THEN  administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,'''''''''''''''')
     END AS valor_padrao,
     CASE TA.cod_tipo
       WHEN 3 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,''''''''''''''''))
       WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,VALOR.valor))
         ELSE         null
     END AS valor_padrao_desc,
     CASE TA.cod_tipo WHEN
         4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         null
     END AS valor_desc,
     AD.ajuda,
     AD.mascara,
     TA.cod_tipo,
     TA.nom_tipo,
     VALOR.valor,
     VALOR.timestamp
  FROM
     administracao.atributo_dinamico          AS AD,
     administracao.tipo_atributo              AS TA,
     administracao.atributo_dinamico AS ACA
     LEFT JOIN
     imobiliario.atributo_imovel_valor         AS VALOR
  ON ( ACA.cod_atributo = VALOR.cod_atributo
          AND ACA.cod_cadastro = VALOR.cod_cadastro
              AND VALOR.inscricao_municipal = ''''||inInscricaoMunicipal||''''
 AND ACA.cod_atributo = 5015
         AND CAST(VALOR.timestamp as varchar)||CAST(VALOR.cod_atributo as varchar) IN (
             SELECT
         (CAST(max(VALOR.timestamp) as varchar))::varchar||CAST(VALOR.cod_atributo as varchar)
             FROM
                administracao.atributo_dinamico AS ACA,
                imobiliario.atributo_imovel_valor         AS VALOR,
                administracao.atributo_dinamico          AS AD,
                administracao.tipo_atributo              AS TA
             WHERE
                ACA.cod_atributo = AD.cod_atributo
                AND ACA.cod_cadastro = AD.cod_cadastro
                AND ACA.cod_modulo   = AD.cod_modulo
             AND ACA.cod_atributo = VALOR.cod_atributo
             AND ACA.cod_cadastro = VALOR.cod_cadastro
             AND ACA.cod_modulo   = VALOR.cod_modulo
              AND VALOR.inscricao_municipal = ''''||inInscricaoMunicipal||''''
 AND ACA.cod_atributo = 5015
             AND AD.cod_tipo = TA.cod_tipo
             AND ACA.ativo = true
             AND AD.cod_modulo   =12
             AND AD.cod_cadastro=4

             GROUP BY VALOR.cod_cadastro, VALOR.cod_atributo
                                  )

     )
  WHERE
      AD.cod_tipo = TA.cod_tipo
  AND ACA.ativo = true
  AND     AD.ativo
  AND AD.cod_atributo =  ACA.cod_atributo
  AND AD.cod_modulo   = ACA.cod_modulo
  AND AD.cod_cadastro = ACA.cod_cadastro
  AND ACA.cod_cadastro=4
  AND ACA.cod_modulo  =12


 AND ACA.cod_atributo = 5015;'''';OPEN crCursor FOR EXECUTE stSql;
                                    FETCH crCursor INTO rsRetorno;
                                    CLOSE crCursor;
                                    RETURN rsRetorno.valor;
                                    END;
                                    '' LANGUAGE plpgsql;
             ';
    EXECUTE stSQL;

    stSQL := '
CREATE OR REPLACE FUNCTION recuperaCadastroImobiliarioImovelUso(  INTEGER ) RETURNS VARCHAR AS ''DECLARE stSql VARCHAR; crCursor  REFCURSOR; rsRetorno RECORD;inInscricaoMunicipal ALIAS FOR $1;BEGIN stSql := '''' SELECT
     AD.cod_cadastro,
     AD.cod_atributo,
     AD.ativo,
     AD.nao_nulo,
     AD.nom_atributo,
     CASE TA.cod_tipo
         WHEN 4 THEN  administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,'''''''''''''''')
     END AS valor_padrao,
     CASE TA.cod_tipo
       WHEN 3 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,''''''''''''''''))
       WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,VALOR.valor))
         ELSE         null
     END AS valor_padrao_desc,
     CASE TA.cod_tipo WHEN
         4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         null
     END AS valor_desc,
     AD.ajuda,
     AD.mascara,
     TA.cod_tipo,
     TA.nom_tipo,
     VALOR.valor,
     VALOR.timestamp
  FROM
     administracao.atributo_dinamico          AS AD,
     administracao.tipo_atributo              AS TA,
     administracao.atributo_dinamico AS ACA
     LEFT JOIN
     imobiliario.atributo_imovel_valor         AS VALOR
  ON ( ACA.cod_atributo = VALOR.cod_atributo
          AND ACA.cod_cadastro = VALOR.cod_cadastro
              AND VALOR.inscricao_municipal = ''''||inInscricaoMunicipal||''''
 AND ACA.cod_atributo = 5014
         AND CAST(VALOR.timestamp as varchar)||CAST(VALOR.cod_atributo as varchar) IN (
             SELECT
         (CAST(max(VALOR.timestamp) as varchar))::varchar||CAST(VALOR.cod_atributo as varchar)
             FROM
                administracao.atributo_dinamico AS ACA,
                imobiliario.atributo_imovel_valor         AS VALOR,
                administracao.atributo_dinamico          AS AD,
                administracao.tipo_atributo              AS TA
             WHERE
                ACA.cod_atributo = AD.cod_atributo
                AND ACA.cod_cadastro = AD.cod_cadastro
                AND ACA.cod_modulo   = AD.cod_modulo
             AND ACA.cod_atributo = VALOR.cod_atributo
             AND ACA.cod_cadastro = VALOR.cod_cadastro
             AND ACA.cod_modulo   = VALOR.cod_modulo
              AND VALOR.inscricao_municipal = ''''||inInscricaoMunicipal||''''
 AND ACA.cod_atributo = 5014
             AND AD.cod_tipo = TA.cod_tipo
             AND ACA.ativo = true
             AND AD.cod_modulo   =12
             AND AD.cod_cadastro=4

             GROUP BY VALOR.cod_cadastro, VALOR.cod_atributo
                                  )

     )
  WHERE
      AD.cod_tipo = TA.cod_tipo
  AND ACA.ativo = true
  AND     AD.ativo
  AND AD.cod_atributo =  ACA.cod_atributo
  AND AD.cod_modulo   = ACA.cod_modulo
  AND AD.cod_cadastro = ACA.cod_cadastro
  AND ACA.cod_cadastro=4
  AND ACA.cod_modulo  =12


 AND ACA.cod_atributo = 5014;'''';OPEN crCursor FOR EXECUTE stSql;
                                    FETCH crCursor INTO rsRetorno;
                                    CLOSE crCursor;
                                    RETURN rsRetorno.valor;
                                    END;
                                    '' LANGUAGE plpgsql;
             ';
    EXECUTE stSQL;

    stSQL := '
CREATE OR REPLACE FUNCTION recuperaCadastroImobiliarioLoteUrbanoNivelDeTibutacao(  INTEGER ) RETURNS VARCHAR AS ''DECLARE stSql VARCHAR; crCursor  REFCURSOR; rsRetorno RECORD;inCodLote ALIAS FOR $1;BEGIN stSql := '''' SELECT
     AD.cod_cadastro,
     AD.cod_atributo,
     AD.ativo,
     AD.nao_nulo,
     AD.nom_atributo,
     CASE TA.cod_tipo
         WHEN 4 THEN  administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,'''''''''''''''')
     END AS valor_padrao,
     CASE TA.cod_tipo
       WHEN 3 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,''''''''''''''''))
       WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,VALOR.valor))
         ELSE         null
     END AS valor_padrao_desc,
     CASE TA.cod_tipo WHEN
         4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         null
     END AS valor_desc,
     AD.ajuda,
     AD.mascara,
     TA.cod_tipo,
     TA.nom_tipo,
     VALOR.valor,
     VALOR.timestamp
  FROM
     administracao.atributo_dinamico          AS AD,
     administracao.tipo_atributo              AS TA,
     administracao.atributo_dinamico AS ACA
     LEFT JOIN
     imobiliario.atributo_lote_urbano_valor         AS VALOR
  ON ( ACA.cod_atributo = VALOR.cod_atributo
          AND ACA.cod_cadastro = VALOR.cod_cadastro
              AND VALOR.cod_lote = ''''||inCodLote||''''
 AND ACA.cod_atributo = 5037
         AND CAST(VALOR.timestamp as varchar)||CAST(VALOR.cod_atributo as varchar) IN (
             SELECT
         (CAST(max(VALOR.timestamp) as varchar))::varchar||CAST(VALOR.cod_atributo as varchar)
             FROM
                administracao.atributo_dinamico AS ACA,
                imobiliario.atributo_lote_urbano_valor         AS VALOR,
                administracao.atributo_dinamico          AS AD,
                administracao.tipo_atributo              AS TA
             WHERE
                ACA.cod_atributo = AD.cod_atributo
                AND ACA.cod_cadastro = AD.cod_cadastro
                AND ACA.cod_modulo   = AD.cod_modulo
             AND ACA.cod_atributo = VALOR.cod_atributo
             AND ACA.cod_cadastro = VALOR.cod_cadastro
             AND ACA.cod_modulo   = VALOR.cod_modulo
              AND VALOR.cod_lote = ''''||inCodLote||''''
 AND ACA.cod_atributo = 5037
             AND AD.cod_tipo = TA.cod_tipo
             AND ACA.ativo = true
             AND AD.cod_modulo   =12
             AND AD.cod_cadastro=2

             GROUP BY VALOR.cod_cadastro, VALOR.cod_atributo
                                  )

     )
  WHERE
      AD.cod_tipo = TA.cod_tipo
  AND ACA.ativo = true
  AND     AD.ativo
  AND AD.cod_atributo =  ACA.cod_atributo
  AND AD.cod_modulo   = ACA.cod_modulo
  AND AD.cod_cadastro = ACA.cod_cadastro
  AND ACA.cod_cadastro=2
  AND ACA.cod_modulo  =12


 AND ACA.cod_atributo = 5037;'''';OPEN crCursor FOR EXECUTE stSql;
                                    FETCH crCursor INTO rsRetorno;
                                    CLOSE crCursor;
                                    RETURN rsRetorno.valor;
                                    END;
                                    '' LANGUAGE plpgsql;
             ';
    EXECUTE stSQL;

    stSQL := '
CREATE OR REPLACE FUNCTION recuperaCadastroImobiliarioLoteUrbanoQuadra(  INTEGER ) RETURNS VARCHAR AS ''DECLARE stSql VARCHAR; crCursor  REFCURSOR; rsRetorno RECORD;inCodLote ALIAS FOR $1;BEGIN stSql := '''' SELECT
     AD.cod_cadastro,
     AD.cod_atributo,
     AD.ativo,
     AD.nao_nulo,
     AD.nom_atributo,
     CASE TA.cod_tipo
         WHEN 4 THEN  administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,'''''''''''''''')
     END AS valor_padrao,
     CASE TA.cod_tipo
       WHEN 3 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,''''''''''''''''))
       WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,VALOR.valor))
         ELSE         null
     END AS valor_padrao_desc,
     CASE TA.cod_tipo WHEN
         4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         null
     END AS valor_desc,
     AD.ajuda,
     AD.mascara,
     TA.cod_tipo,
     TA.nom_tipo,
     VALOR.valor,
     VALOR.timestamp
  FROM
     administracao.atributo_dinamico          AS AD,
     administracao.tipo_atributo              AS TA,
     administracao.atributo_dinamico AS ACA
     LEFT JOIN
     imobiliario.atributo_lote_urbano_valor         AS VALOR
  ON ( ACA.cod_atributo = VALOR.cod_atributo
          AND ACA.cod_cadastro = VALOR.cod_cadastro
              AND VALOR.cod_lote = ''''||inCodLote||''''
 AND ACA.cod_atributo = 5038
         AND CAST(VALOR.timestamp as varchar)||CAST(VALOR.cod_atributo as varchar) IN (
             SELECT
         (CAST(max(VALOR.timestamp) as varchar))::varchar||CAST(VALOR.cod_atributo as varchar)
             FROM
                administracao.atributo_dinamico AS ACA,
                imobiliario.atributo_lote_urbano_valor         AS VALOR,
                administracao.atributo_dinamico          AS AD,
                administracao.tipo_atributo              AS TA
             WHERE
                ACA.cod_atributo = AD.cod_atributo
                AND ACA.cod_cadastro = AD.cod_cadastro
                AND ACA.cod_modulo   = AD.cod_modulo
             AND ACA.cod_atributo = VALOR.cod_atributo
             AND ACA.cod_cadastro = VALOR.cod_cadastro
             AND ACA.cod_modulo   = VALOR.cod_modulo
              AND VALOR.cod_lote = ''''||inCodLote||''''
 AND ACA.cod_atributo = 5038
             AND AD.cod_tipo = TA.cod_tipo
             AND ACA.ativo = true
             AND AD.cod_modulo   =12
             AND AD.cod_cadastro=2

             GROUP BY VALOR.cod_cadastro, VALOR.cod_atributo
                                  )

     )
  WHERE
      AD.cod_tipo = TA.cod_tipo
  AND ACA.ativo = true
  AND     AD.ativo
  AND AD.cod_atributo =  ACA.cod_atributo
  AND AD.cod_modulo   = ACA.cod_modulo
  AND AD.cod_cadastro = ACA.cod_cadastro
  AND ACA.cod_cadastro=2
  AND ACA.cod_modulo  =12


 AND ACA.cod_atributo = 5038;'''';OPEN crCursor FOR EXECUTE stSql;
                                    FETCH crCursor INTO rsRetorno;
                                    CLOSE crCursor;
                                    RETURN rsRetorno.valor;
                                    END;
                                    '' LANGUAGE plpgsql;
             ';
    EXECUTE stSQL;

    stSQL := '
CREATE OR REPLACE FUNCTION recuperaCadastroImobiliarioLoteUrbanoAPartirDeQueAnoTeveMelhoramentoNoPerimetroDoLote(  INTEGER ) RETURNS VARCHAR AS ''DECLARE stSql VARCHAR; crCursor  REFCURSOR; rsRetorno RECORD;inCodLote ALIAS FOR $1;BEGIN stSql := '''' SELECT
     AD.cod_cadastro,
     AD.cod_atributo,
     AD.ativo,
     AD.nao_nulo,
     AD.nom_atributo,
     CASE TA.cod_tipo
         WHEN 4 THEN  administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,'''''''''''''''')
     END AS valor_padrao,
     CASE TA.cod_tipo
       WHEN 3 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,''''''''''''''''))
       WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,VALOR.valor))
         ELSE         null
     END AS valor_padrao_desc,
     CASE TA.cod_tipo WHEN
         4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         null
     END AS valor_desc,
     AD.ajuda,
     AD.mascara,
     TA.cod_tipo,
     TA.nom_tipo,
     VALOR.valor,
     VALOR.timestamp
  FROM
     administracao.atributo_dinamico          AS AD,
     administracao.tipo_atributo              AS TA,
     administracao.atributo_dinamico AS ACA
     LEFT JOIN
     imobiliario.atributo_lote_urbano_valor         AS VALOR
  ON ( ACA.cod_atributo = VALOR.cod_atributo
          AND ACA.cod_cadastro = VALOR.cod_cadastro
              AND VALOR.cod_lote = ''''||inCodLote||''''
 AND ACA.cod_atributo = 5047
         AND CAST(VALOR.timestamp as varchar)||CAST(VALOR.cod_atributo as varchar) IN (
             SELECT
         (CAST(max(VALOR.timestamp) as varchar))::varchar||CAST(VALOR.cod_atributo as varchar)
             FROM
                administracao.atributo_dinamico AS ACA,
                imobiliario.atributo_lote_urbano_valor         AS VALOR,
                administracao.atributo_dinamico          AS AD,
                administracao.tipo_atributo              AS TA
             WHERE
                ACA.cod_atributo = AD.cod_atributo
                AND ACA.cod_cadastro = AD.cod_cadastro
                AND ACA.cod_modulo   = AD.cod_modulo
             AND ACA.cod_atributo = VALOR.cod_atributo
             AND ACA.cod_cadastro = VALOR.cod_cadastro
             AND ACA.cod_modulo   = VALOR.cod_modulo
              AND VALOR.cod_lote = ''''||inCodLote||''''
 AND ACA.cod_atributo = 5047
             AND AD.cod_tipo = TA.cod_tipo
             AND ACA.ativo = true
             AND AD.cod_modulo   =12
             AND AD.cod_cadastro=2

             GROUP BY VALOR.cod_cadastro, VALOR.cod_atributo
                                  )

     )
  WHERE
      AD.cod_tipo = TA.cod_tipo
  AND ACA.ativo = true
  AND     AD.ativo
  AND AD.cod_atributo =  ACA.cod_atributo
  AND AD.cod_modulo   = ACA.cod_modulo
  AND AD.cod_cadastro = ACA.cod_cadastro
  AND ACA.cod_cadastro=2
  AND ACA.cod_modulo  =12


 AND ACA.cod_atributo = 5047;'''';OPEN crCursor FOR EXECUTE stSql;
                                    FETCH crCursor INTO rsRetorno;
                                    CLOSE crCursor;
                                    RETURN rsRetorno.valor;
                                    END;
                                    '' LANGUAGE plpgsql;
             ';
    EXECUTE stSQL;

    stSQL := '
CREATE OR REPLACE FUNCTION recuperaCadastroImobiliarioLoteUrbanoSituacao(  INTEGER ) RETURNS VARCHAR AS ''DECLARE stSql VARCHAR; crCursor  REFCURSOR; rsRetorno RECORD;inCodLote ALIAS FOR $1;BEGIN stSql := '''' SELECT
     AD.cod_cadastro,
     AD.cod_atributo,
     AD.ativo,
     AD.nao_nulo,
     AD.nom_atributo,
     CASE TA.cod_tipo
         WHEN 4 THEN  administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,'''''''''''''''')
     END AS valor_padrao,
     CASE TA.cod_tipo
       WHEN 3 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,''''''''''''''''))
       WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,VALOR.valor))
         ELSE         null
     END AS valor_padrao_desc,
     CASE TA.cod_tipo WHEN
         4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         null
     END AS valor_desc,
     AD.ajuda,
     AD.mascara,
     TA.cod_tipo,
     TA.nom_tipo,
     VALOR.valor,
     VALOR.timestamp
  FROM
     administracao.atributo_dinamico          AS AD,
     administracao.tipo_atributo              AS TA,
     administracao.atributo_dinamico AS ACA
     LEFT JOIN
     imobiliario.atributo_lote_urbano_valor         AS VALOR
  ON ( ACA.cod_atributo = VALOR.cod_atributo
          AND ACA.cod_cadastro = VALOR.cod_cadastro
              AND VALOR.cod_lote = ''''||inCodLote||''''
 AND ACA.cod_atributo = 5004
         AND CAST(VALOR.timestamp as varchar)||CAST(VALOR.cod_atributo as varchar) IN (
             SELECT
         (CAST(max(VALOR.timestamp) as varchar))::varchar||CAST(VALOR.cod_atributo as varchar)
             FROM
                administracao.atributo_dinamico AS ACA,
                imobiliario.atributo_lote_urbano_valor         AS VALOR,
                administracao.atributo_dinamico          AS AD,
                administracao.tipo_atributo              AS TA
             WHERE
                ACA.cod_atributo = AD.cod_atributo
                AND ACA.cod_cadastro = AD.cod_cadastro
                AND ACA.cod_modulo   = AD.cod_modulo
             AND ACA.cod_atributo = VALOR.cod_atributo
             AND ACA.cod_cadastro = VALOR.cod_cadastro
             AND ACA.cod_modulo   = VALOR.cod_modulo
              AND VALOR.cod_lote = ''''||inCodLote||''''
 AND ACA.cod_atributo = 5004
             AND AD.cod_tipo = TA.cod_tipo
             AND ACA.ativo = true
             AND AD.cod_modulo   =12
             AND AD.cod_cadastro=2

             GROUP BY VALOR.cod_cadastro, VALOR.cod_atributo
                                  )

     )
  WHERE
      AD.cod_tipo = TA.cod_tipo
  AND ACA.ativo = true
  AND     AD.ativo
  AND AD.cod_atributo =  ACA.cod_atributo
  AND AD.cod_modulo   = ACA.cod_modulo
  AND AD.cod_cadastro = ACA.cod_cadastro
  AND ACA.cod_cadastro=2
  AND ACA.cod_modulo  =12


 AND ACA.cod_atributo = 5004;'''';OPEN crCursor FOR EXECUTE stSql;
                                    FETCH crCursor INTO rsRetorno;
                                    CLOSE crCursor;
                                    RETURN rsRetorno.valor;
                                    END;
                                    '' LANGUAGE plpgsql;
             ';
    EXECUTE stSQL;

    stSQL := '
CREATE OR REPLACE FUNCTION recuperaCadastroImobiliarioLoteUrbanoPedologia(  INTEGER ) RETURNS VARCHAR AS ''DECLARE stSql VARCHAR; crCursor  REFCURSOR; rsRetorno RECORD;inCodLote ALIAS FOR $1;BEGIN stSql := '''' SELECT
     AD.cod_cadastro,
     AD.cod_atributo,
     AD.ativo,
     AD.nao_nulo,
     AD.nom_atributo,
     CASE TA.cod_tipo
         WHEN 4 THEN  administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,'''''''''''''''')
     END AS valor_padrao,
     CASE TA.cod_tipo
       WHEN 3 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,''''''''''''''''))
       WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,VALOR.valor))
         ELSE         null
     END AS valor_padrao_desc,
     CASE TA.cod_tipo WHEN
         4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         null
     END AS valor_desc,
     AD.ajuda,
     AD.mascara,
     TA.cod_tipo,
     TA.nom_tipo,
     VALOR.valor,
     VALOR.timestamp
  FROM
     administracao.atributo_dinamico          AS AD,
     administracao.tipo_atributo              AS TA,
     administracao.atributo_dinamico AS ACA
     LEFT JOIN
     imobiliario.atributo_lote_urbano_valor         AS VALOR
  ON ( ACA.cod_atributo = VALOR.cod_atributo
          AND ACA.cod_cadastro = VALOR.cod_cadastro
              AND VALOR.cod_lote = ''''||inCodLote||''''
 AND ACA.cod_atributo = 5006
         AND CAST(VALOR.timestamp as varchar)||CAST(VALOR.cod_atributo as varchar) IN (
             SELECT
         (CAST(max(VALOR.timestamp) as varchar))::varchar||CAST(VALOR.cod_atributo as varchar)
             FROM
                administracao.atributo_dinamico AS ACA,
                imobiliario.atributo_lote_urbano_valor         AS VALOR,
                administracao.atributo_dinamico          AS AD,
                administracao.tipo_atributo              AS TA
             WHERE
                ACA.cod_atributo = AD.cod_atributo
                AND ACA.cod_cadastro = AD.cod_cadastro
                AND ACA.cod_modulo   = AD.cod_modulo
             AND ACA.cod_atributo = VALOR.cod_atributo
             AND ACA.cod_cadastro = VALOR.cod_cadastro
             AND ACA.cod_modulo   = VALOR.cod_modulo
              AND VALOR.cod_lote = ''''||inCodLote||''''
 AND ACA.cod_atributo = 5006
             AND AD.cod_tipo = TA.cod_tipo
             AND ACA.ativo = true
             AND AD.cod_modulo   =12
             AND AD.cod_cadastro=2

             GROUP BY VALOR.cod_cadastro, VALOR.cod_atributo
                                  )

     )
  WHERE
      AD.cod_tipo = TA.cod_tipo
  AND ACA.ativo = true
  AND     AD.ativo
  AND AD.cod_atributo =  ACA.cod_atributo
  AND AD.cod_modulo   = ACA.cod_modulo
  AND AD.cod_cadastro = ACA.cod_cadastro
  AND ACA.cod_cadastro=2
  AND ACA.cod_modulo  =12


 AND ACA.cod_atributo = 5006;'''';OPEN crCursor FOR EXECUTE stSql;
                                    FETCH crCursor INTO rsRetorno;
                                    CLOSE crCursor;
                                    RETURN rsRetorno.valor;
                                    END;
                                    '' LANGUAGE plpgsql;
             ';
    EXECUTE stSQL;

    stSQL := '
CREATE OR REPLACE FUNCTION recuperaCadastroImobiliarioLoteUrbanoLimitacao(  INTEGER ) RETURNS VARCHAR AS ''DECLARE stSql VARCHAR; crCursor  REFCURSOR; rsRetorno RECORD;inCodLote ALIAS FOR $1;BEGIN stSql := '''' SELECT
     AD.cod_cadastro,
     AD.cod_atributo,
     AD.ativo,
     AD.nao_nulo,
     AD.nom_atributo,
     CASE TA.cod_tipo
         WHEN 4 THEN  administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,'''''''''''''''')
     END AS valor_padrao,
     CASE TA.cod_tipo
       WHEN 3 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,''''''''''''''''))
       WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,VALOR.valor))
         ELSE         null
     END AS valor_padrao_desc,
     CASE TA.cod_tipo WHEN
         4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         null
     END AS valor_desc,
     AD.ajuda,
     AD.mascara,
     TA.cod_tipo,
     TA.nom_tipo,
     VALOR.valor,
     VALOR.timestamp
  FROM
     administracao.atributo_dinamico          AS AD,
     administracao.tipo_atributo              AS TA,
     administracao.atributo_dinamico AS ACA
     LEFT JOIN
     imobiliario.atributo_lote_urbano_valor         AS VALOR
  ON ( ACA.cod_atributo = VALOR.cod_atributo
          AND ACA.cod_cadastro = VALOR.cod_cadastro
              AND VALOR.cod_lote = ''''||inCodLote||''''
 AND ACA.cod_atributo = 5007
         AND CAST(VALOR.timestamp as varchar)||CAST(VALOR.cod_atributo as varchar) IN (
             SELECT
         (CAST(max(VALOR.timestamp) as varchar))::varchar||CAST(VALOR.cod_atributo as varchar)
             FROM
                administracao.atributo_dinamico AS ACA,
                imobiliario.atributo_lote_urbano_valor         AS VALOR,
                administracao.atributo_dinamico          AS AD,
                administracao.tipo_atributo              AS TA
             WHERE
                ACA.cod_atributo = AD.cod_atributo
                AND ACA.cod_cadastro = AD.cod_cadastro
                AND ACA.cod_modulo   = AD.cod_modulo
             AND ACA.cod_atributo = VALOR.cod_atributo
             AND ACA.cod_cadastro = VALOR.cod_cadastro
             AND ACA.cod_modulo   = VALOR.cod_modulo
              AND VALOR.cod_lote = ''''||inCodLote||''''
 AND ACA.cod_atributo = 5007
             AND AD.cod_tipo = TA.cod_tipo
             AND ACA.ativo = true
             AND AD.cod_modulo   =12
             AND AD.cod_cadastro=2

             GROUP BY VALOR.cod_cadastro, VALOR.cod_atributo
                                  )

     )
  WHERE
      AD.cod_tipo = TA.cod_tipo
  AND ACA.ativo = true
  AND     AD.ativo
  AND AD.cod_atributo =  ACA.cod_atributo
  AND AD.cod_modulo   = ACA.cod_modulo
  AND AD.cod_cadastro = ACA.cod_cadastro
  AND ACA.cod_cadastro=2
  AND ACA.cod_modulo  =12


 AND ACA.cod_atributo = 5007;'''';OPEN crCursor FOR EXECUTE stSql;
                                    FETCH crCursor INTO rsRetorno;
                                    CLOSE crCursor;
                                    RETURN rsRetorno.valor;
                                    END;
                                    '' LANGUAGE plpgsql;
             ';
    EXECUTE stSQL;

    stSQL := '
CREATE OR REPLACE FUNCTION recuperaCadastroImobiliarioLoteUrbanoCalcada(  INTEGER ) RETURNS VARCHAR AS ''DECLARE stSql VARCHAR; crCursor  REFCURSOR; rsRetorno RECORD;inCodLote ALIAS FOR $1;BEGIN stSql := '''' SELECT
     AD.cod_cadastro,
     AD.cod_atributo,
     AD.ativo,
     AD.nao_nulo,
     AD.nom_atributo,
     CASE TA.cod_tipo
         WHEN 4 THEN  administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,'''''''''''''''')
     END AS valor_padrao,
     CASE TA.cod_tipo
       WHEN 3 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,''''''''''''''''))
       WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,VALOR.valor))
         ELSE         null
     END AS valor_padrao_desc,
     CASE TA.cod_tipo WHEN
         4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         null
     END AS valor_desc,
     AD.ajuda,
     AD.mascara,
     TA.cod_tipo,
     TA.nom_tipo,
     VALOR.valor,
     VALOR.timestamp
  FROM
     administracao.atributo_dinamico          AS AD,
     administracao.tipo_atributo              AS TA,
     administracao.atributo_dinamico AS ACA
     LEFT JOIN
     imobiliario.atributo_lote_urbano_valor         AS VALOR
  ON ( ACA.cod_atributo = VALOR.cod_atributo
          AND ACA.cod_cadastro = VALOR.cod_cadastro
              AND VALOR.cod_lote = ''''||inCodLote||''''
 AND ACA.cod_atributo = 5008
         AND CAST(VALOR.timestamp as varchar)||CAST(VALOR.cod_atributo as varchar) IN (
             SELECT
         (CAST(max(VALOR.timestamp) as varchar))::varchar||CAST(VALOR.cod_atributo as varchar)
             FROM
                administracao.atributo_dinamico AS ACA,
                imobiliario.atributo_lote_urbano_valor         AS VALOR,
                administracao.atributo_dinamico          AS AD,
                administracao.tipo_atributo              AS TA
             WHERE
                ACA.cod_atributo = AD.cod_atributo
                AND ACA.cod_cadastro = AD.cod_cadastro
                AND ACA.cod_modulo   = AD.cod_modulo
             AND ACA.cod_atributo = VALOR.cod_atributo
             AND ACA.cod_cadastro = VALOR.cod_cadastro
             AND ACA.cod_modulo   = VALOR.cod_modulo
              AND VALOR.cod_lote = ''''||inCodLote||''''
 AND ACA.cod_atributo = 5008
             AND AD.cod_tipo = TA.cod_tipo
             AND ACA.ativo = true
             AND AD.cod_modulo   =12
             AND AD.cod_cadastro=2

             GROUP BY VALOR.cod_cadastro, VALOR.cod_atributo
                                  )

     )
  WHERE
      AD.cod_tipo = TA.cod_tipo
  AND ACA.ativo = true
  AND     AD.ativo
  AND AD.cod_atributo =  ACA.cod_atributo
  AND AD.cod_modulo   = ACA.cod_modulo
  AND AD.cod_cadastro = ACA.cod_cadastro
  AND ACA.cod_cadastro=2
  AND ACA.cod_modulo  =12


 AND ACA.cod_atributo = 5008;'''';OPEN crCursor FOR EXECUTE stSql;
                                    FETCH crCursor INTO rsRetorno;
                                    CLOSE crCursor;
                                    RETURN rsRetorno.valor;
                                    END;
                                    '' LANGUAGE plpgsql;
             ';
    EXECUTE stSQL;

    stSQL := '
CREATE OR REPLACE FUNCTION recuperaCadastroImobiliarioLoteUrbanoTopografia(  INTEGER ) RETURNS VARCHAR AS ''DECLARE stSql VARCHAR; crCursor  REFCURSOR; rsRetorno RECORD;inCodLote ALIAS FOR $1;BEGIN stSql := '''' SELECT
     AD.cod_cadastro,
     AD.cod_atributo,
     AD.ativo,
     AD.nao_nulo,
     AD.nom_atributo,
     CASE TA.cod_tipo
         WHEN 4 THEN  administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,'''''''''''''''')
     END AS valor_padrao,
     CASE TA.cod_tipo
       WHEN 3 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,''''''''''''''''))
       WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,VALOR.valor))
         ELSE         null
     END AS valor_padrao_desc,
     CASE TA.cod_tipo WHEN
         4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         null
     END AS valor_desc,
     AD.ajuda,
     AD.mascara,
     TA.cod_tipo,
     TA.nom_tipo,
     VALOR.valor,
     VALOR.timestamp
  FROM
     administracao.atributo_dinamico          AS AD,
     administracao.tipo_atributo              AS TA,
     administracao.atributo_dinamico AS ACA
     LEFT JOIN
     imobiliario.atributo_lote_urbano_valor         AS VALOR
  ON ( ACA.cod_atributo = VALOR.cod_atributo
          AND ACA.cod_cadastro = VALOR.cod_cadastro
              AND VALOR.cod_lote = ''''||inCodLote||''''
 AND ACA.cod_atributo = 5005
         AND CAST(VALOR.timestamp as varchar)||CAST(VALOR.cod_atributo as varchar) IN (
             SELECT
         (CAST(max(VALOR.timestamp) as varchar))::varchar||CAST(VALOR.cod_atributo as varchar)
             FROM
                administracao.atributo_dinamico AS ACA,
                imobiliario.atributo_lote_urbano_valor         AS VALOR,
                administracao.atributo_dinamico          AS AD,
                administracao.tipo_atributo              AS TA
             WHERE
                ACA.cod_atributo = AD.cod_atributo
                AND ACA.cod_cadastro = AD.cod_cadastro
                AND ACA.cod_modulo   = AD.cod_modulo
             AND ACA.cod_atributo = VALOR.cod_atributo
             AND ACA.cod_cadastro = VALOR.cod_cadastro
             AND ACA.cod_modulo   = VALOR.cod_modulo
              AND VALOR.cod_lote = ''''||inCodLote||''''
 AND ACA.cod_atributo = 5005
             AND AD.cod_tipo = TA.cod_tipo
             AND ACA.ativo = true
             AND AD.cod_modulo   =12
             AND AD.cod_cadastro=2

             GROUP BY VALOR.cod_cadastro, VALOR.cod_atributo
                                  )

     )
  WHERE
      AD.cod_tipo = TA.cod_tipo
  AND ACA.ativo = true
  AND     AD.ativo
  AND AD.cod_atributo =  ACA.cod_atributo
  AND AD.cod_modulo   = ACA.cod_modulo
  AND AD.cod_cadastro = ACA.cod_cadastro
  AND ACA.cod_cadastro=2
  AND ACA.cod_modulo  =12


 AND ACA.cod_atributo = 5005;'''';OPEN crCursor FOR EXECUTE stSql;
                                    FETCH crCursor INTO rsRetorno;
                                    CLOSE crCursor;
                                    RETURN rsRetorno.valor;
                                    END;
                                    '' LANGUAGE plpgsql;
             ';
    EXECUTE stSQL;

    stSQL := '
CREATE OR REPLACE FUNCTION recuperaCadastroImobiliarioTipoDeEdificacaoTipoDeConstrucao(  INTEGER, INTEGER ) RETURNS VARCHAR AS ''DECLARE stSql VARCHAR; crCursor  REFCURSOR; rsRetorno RECORD;inCodTipo ALIAS FOR $1;inCodConstrucao ALIAS FOR $2;BEGIN stSql := '''' SELECT
     AD.cod_cadastro,
     AD.cod_atributo,
     AD.ativo,
     AD.nao_nulo,
     AD.nom_atributo,
     CASE TA.cod_tipo
         WHEN 4 THEN  administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,'''''''''''''''')
     END AS valor_padrao,
     CASE TA.cod_tipo
       WHEN 3 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,''''''''''''''''))
       WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,VALOR.valor))
         ELSE         null
     END AS valor_padrao_desc,
     CASE TA.cod_tipo WHEN
         4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         null
     END AS valor_desc,
     AD.ajuda,
     AD.mascara,
     TA.cod_tipo,
     TA.nom_tipo,
     VALOR.valor,
     VALOR.timestamp
  FROM
     administracao.atributo_dinamico          AS AD,
     administracao.tipo_atributo              AS TA,
     imobiliario.atributo_tipo_edificacao AS ACA
     LEFT JOIN
     imobiliario.atributo_tipo_edificacao_valor         AS VALOR
  ON ( ACA.cod_atributo = VALOR.cod_atributo
          AND ACA.cod_cadastro = VALOR.cod_cadastro
              AND VALOR.cod_tipo = ''''||inCodTipo||''''
 AND VALOR.cod_construcao = ''''||inCodConstrucao||''''
 AND ACA.cod_atributo = 5017
         AND CAST(VALOR.timestamp as varchar)||CAST(VALOR.cod_atributo as varchar) IN (
             SELECT
         (CAST(max(VALOR.timestamp) as varchar))::varchar||CAST(VALOR.cod_atributo as varchar)
             FROM
                imobiliario.atributo_tipo_edificacao AS ACA,
                imobiliario.atributo_tipo_edificacao_valor         AS VALOR,
                administracao.atributo_dinamico          AS AD,
                administracao.tipo_atributo              AS TA
             WHERE
                ACA.cod_atributo = AD.cod_atributo
                AND ACA.cod_cadastro = AD.cod_cadastro
                AND ACA.cod_modulo   = AD.cod_modulo
             AND ACA.cod_atributo = VALOR.cod_atributo
             AND ACA.cod_cadastro = VALOR.cod_cadastro
             AND ACA.cod_modulo   = VALOR.cod_modulo
              AND VALOR.cod_tipo = ''''||inCodTipo||''''
 AND VALOR.cod_construcao = ''''||inCodConstrucao||''''
 AND ACA.cod_atributo = 5017
             AND AD.cod_tipo = TA.cod_tipo
             AND ACA.ativo = true
             AND AD.cod_modulo   =12
             AND AD.cod_cadastro=5

             GROUP BY VALOR.cod_cadastro, VALOR.cod_atributo
                                  )

     )
  WHERE
      AD.cod_tipo = TA.cod_tipo
  AND ACA.ativo = true
  AND     AD.ativo
  AND AD.cod_atributo =  ACA.cod_atributo
  AND AD.cod_modulo   = ACA.cod_modulo
  AND AD.cod_cadastro = ACA.cod_cadastro
  AND ACA.cod_cadastro=5
  AND ACA.cod_modulo  =12


 AND ACA.cod_atributo = 5017;'''';OPEN crCursor FOR EXECUTE stSql;
                                    FETCH crCursor INTO rsRetorno;
                                    CLOSE crCursor;
                                    RETURN rsRetorno.valor;
                                    END;
                                    '' LANGUAGE plpgsql;
             ';
    EXECUTE stSQL;

    stSQL := '
CREATE OR REPLACE FUNCTION recuperaCadastroImobiliarioTipoDeEdificacaoAlinhamento(  INTEGER, INTEGER ) RETURNS VARCHAR AS ''DECLARE stSql VARCHAR; crCursor  REFCURSOR; rsRetorno RECORD;inCodTipo ALIAS FOR $1;inCodConstrucao ALIAS FOR $2;BEGIN stSql := '''' SELECT
     AD.cod_cadastro,
     AD.cod_atributo,
     AD.ativo,
     AD.nao_nulo,
     AD.nom_atributo,
     CASE TA.cod_tipo
         WHEN 4 THEN  administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,'''''''''''''''')
     END AS valor_padrao,
     CASE TA.cod_tipo
       WHEN 3 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,''''''''''''''''))
       WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,VALOR.valor))
         ELSE         null
     END AS valor_padrao_desc,
     CASE TA.cod_tipo WHEN
         4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         null
     END AS valor_desc,
     AD.ajuda,
     AD.mascara,
     TA.cod_tipo,
     TA.nom_tipo,
     VALOR.valor,
     VALOR.timestamp
  FROM
     administracao.atributo_dinamico          AS AD,
     administracao.tipo_atributo              AS TA,
     imobiliario.atributo_tipo_edificacao AS ACA
     LEFT JOIN
     imobiliario.atributo_tipo_edificacao_valor         AS VALOR
  ON ( ACA.cod_atributo = VALOR.cod_atributo
          AND ACA.cod_cadastro = VALOR.cod_cadastro
              AND VALOR.cod_tipo = ''''||inCodTipo||''''
 AND VALOR.cod_construcao = ''''||inCodConstrucao||''''
 AND ACA.cod_atributo = 5018
         AND CAST(VALOR.timestamp as varchar)||CAST(VALOR.cod_atributo as varchar) IN (
             SELECT
         (CAST(max(VALOR.timestamp) as varchar))::varchar||CAST(VALOR.cod_atributo as varchar)
             FROM
                imobiliario.atributo_tipo_edificacao AS ACA,
                imobiliario.atributo_tipo_edificacao_valor         AS VALOR,
                administracao.atributo_dinamico          AS AD,
                administracao.tipo_atributo              AS TA
             WHERE
                ACA.cod_atributo = AD.cod_atributo
                AND ACA.cod_cadastro = AD.cod_cadastro
                AND ACA.cod_modulo   = AD.cod_modulo
             AND ACA.cod_atributo = VALOR.cod_atributo
             AND ACA.cod_cadastro = VALOR.cod_cadastro
             AND ACA.cod_modulo   = VALOR.cod_modulo
              AND VALOR.cod_tipo = ''''||inCodTipo||''''
 AND VALOR.cod_construcao = ''''||inCodConstrucao||''''
 AND ACA.cod_atributo = 5018
             AND AD.cod_tipo = TA.cod_tipo
             AND ACA.ativo = true
             AND AD.cod_modulo   =12
             AND AD.cod_cadastro=5

             GROUP BY VALOR.cod_cadastro, VALOR.cod_atributo
                                  )

     )
  WHERE
      AD.cod_tipo = TA.cod_tipo
  AND ACA.ativo = true
  AND     AD.ativo
  AND AD.cod_atributo =  ACA.cod_atributo
  AND AD.cod_modulo   = ACA.cod_modulo
  AND AD.cod_cadastro = ACA.cod_cadastro
  AND ACA.cod_cadastro=5
  AND ACA.cod_modulo  =12


 AND ACA.cod_atributo = 5018;'''';OPEN crCursor FOR EXECUTE stSql;
                                    FETCH crCursor INTO rsRetorno;
                                    CLOSE crCursor;
                                    RETURN rsRetorno.valor;
                                    END;
                                    '' LANGUAGE plpgsql;
             ';
    EXECUTE stSQL;

    stSQL := '
CREATE OR REPLACE FUNCTION recuperaCadastroImobiliarioTipoDeEdificacaoSituacaoLote(  INTEGER, INTEGER ) RETURNS VARCHAR AS ''DECLARE stSql VARCHAR; crCursor  REFCURSOR; rsRetorno RECORD;inCodTipo ALIAS FOR $1;inCodConstrucao ALIAS FOR $2;BEGIN stSql := '''' SELECT
     AD.cod_cadastro,
     AD.cod_atributo,
     AD.ativo,
     AD.nao_nulo,
     AD.nom_atributo,
     CASE TA.cod_tipo
         WHEN 4 THEN  administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,'''''''''''''''')
     END AS valor_padrao,
     CASE TA.cod_tipo
       WHEN 3 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,''''''''''''''''))
       WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,VALOR.valor))
         ELSE         null
     END AS valor_padrao_desc,
     CASE TA.cod_tipo WHEN
         4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         null
     END AS valor_desc,
     AD.ajuda,
     AD.mascara,
     TA.cod_tipo,
     TA.nom_tipo,
     VALOR.valor,
     VALOR.timestamp
  FROM
     administracao.atributo_dinamico          AS AD,
     administracao.tipo_atributo              AS TA,
     imobiliario.atributo_tipo_edificacao AS ACA
     LEFT JOIN
     imobiliario.atributo_tipo_edificacao_valor         AS VALOR
  ON ( ACA.cod_atributo = VALOR.cod_atributo
          AND ACA.cod_cadastro = VALOR.cod_cadastro
              AND VALOR.cod_tipo = ''''||inCodTipo||''''
 AND VALOR.cod_construcao = ''''||inCodConstrucao||''''
 AND ACA.cod_atributo = 5019
         AND CAST(VALOR.timestamp as varchar)||CAST(VALOR.cod_atributo as varchar) IN (
             SELECT
         (CAST(max(VALOR.timestamp) as varchar))::varchar||CAST(VALOR.cod_atributo as varchar)
             FROM
                imobiliario.atributo_tipo_edificacao AS ACA,
                imobiliario.atributo_tipo_edificacao_valor         AS VALOR,
                administracao.atributo_dinamico          AS AD,
                administracao.tipo_atributo              AS TA
             WHERE
                ACA.cod_atributo = AD.cod_atributo
                AND ACA.cod_cadastro = AD.cod_cadastro
                AND ACA.cod_modulo   = AD.cod_modulo
             AND ACA.cod_atributo = VALOR.cod_atributo
             AND ACA.cod_cadastro = VALOR.cod_cadastro
             AND ACA.cod_modulo   = VALOR.cod_modulo
              AND VALOR.cod_tipo = ''''||inCodTipo||''''
 AND VALOR.cod_construcao = ''''||inCodConstrucao||''''
 AND ACA.cod_atributo = 5019
             AND AD.cod_tipo = TA.cod_tipo
             AND ACA.ativo = true
             AND AD.cod_modulo   =12
             AND AD.cod_cadastro=5

             GROUP BY VALOR.cod_cadastro, VALOR.cod_atributo
                                  )

     )
  WHERE
      AD.cod_tipo = TA.cod_tipo
  AND ACA.ativo = true
  AND     AD.ativo
  AND AD.cod_atributo =  ACA.cod_atributo
  AND AD.cod_modulo   = ACA.cod_modulo
  AND AD.cod_cadastro = ACA.cod_cadastro
  AND ACA.cod_cadastro=5
  AND ACA.cod_modulo  =12


 AND ACA.cod_atributo = 5019;'''';OPEN crCursor FOR EXECUTE stSql;
                                    FETCH crCursor INTO rsRetorno;
                                    CLOSE crCursor;
                                    RETURN rsRetorno.valor;
                                    END;
                                    '' LANGUAGE plpgsql;
             ';
    EXECUTE stSQL;

    stSQL := '
CREATE OR REPLACE FUNCTION recuperaCadastroImobiliarioTipoDeEdificacaoSituacaoUnidade(  INTEGER, INTEGER ) RETURNS VARCHAR AS ''DECLARE stSql VARCHAR; crCursor  REFCURSOR; rsRetorno RECORD;inCodTipo ALIAS FOR $1;inCodConstrucao ALIAS FOR $2;BEGIN stSql := '''' SELECT
     AD.cod_cadastro,
     AD.cod_atributo,
     AD.ativo,
     AD.nao_nulo,
     AD.nom_atributo,
     CASE TA.cod_tipo
         WHEN 4 THEN  administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,'''''''''''''''')
     END AS valor_padrao,
     CASE TA.cod_tipo
       WHEN 3 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,''''''''''''''''))
       WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,VALOR.valor))
         ELSE         null
     END AS valor_padrao_desc,
     CASE TA.cod_tipo WHEN
         4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         null
     END AS valor_desc,
     AD.ajuda,
     AD.mascara,
     TA.cod_tipo,
     TA.nom_tipo,
     VALOR.valor,
     VALOR.timestamp
  FROM
     administracao.atributo_dinamico          AS AD,
     administracao.tipo_atributo              AS TA,
     imobiliario.atributo_tipo_edificacao AS ACA
     LEFT JOIN
     imobiliario.atributo_tipo_edificacao_valor         AS VALOR
  ON ( ACA.cod_atributo = VALOR.cod_atributo
          AND ACA.cod_cadastro = VALOR.cod_cadastro
              AND VALOR.cod_tipo = ''''||inCodTipo||''''
 AND VALOR.cod_construcao = ''''||inCodConstrucao||''''
 AND ACA.cod_atributo = 5020
         AND CAST(VALOR.timestamp as varchar)||CAST(VALOR.cod_atributo as varchar) IN (
             SELECT
         (CAST(max(VALOR.timestamp) as varchar))::varchar||CAST(VALOR.cod_atributo as varchar)
             FROM
                imobiliario.atributo_tipo_edificacao AS ACA,
                imobiliario.atributo_tipo_edificacao_valor         AS VALOR,
                administracao.atributo_dinamico          AS AD,
                administracao.tipo_atributo              AS TA
             WHERE
                ACA.cod_atributo = AD.cod_atributo
                AND ACA.cod_cadastro = AD.cod_cadastro
                AND ACA.cod_modulo   = AD.cod_modulo
             AND ACA.cod_atributo = VALOR.cod_atributo
             AND ACA.cod_cadastro = VALOR.cod_cadastro
             AND ACA.cod_modulo   = VALOR.cod_modulo
              AND VALOR.cod_tipo = ''''||inCodTipo||''''
 AND VALOR.cod_construcao = ''''||inCodConstrucao||''''
 AND ACA.cod_atributo = 5020
             AND AD.cod_tipo = TA.cod_tipo
             AND ACA.ativo = true
             AND AD.cod_modulo   =12
             AND AD.cod_cadastro=5

             GROUP BY VALOR.cod_cadastro, VALOR.cod_atributo
                                  )

     )
  WHERE
      AD.cod_tipo = TA.cod_tipo
  AND ACA.ativo = true
  AND     AD.ativo
  AND AD.cod_atributo =  ACA.cod_atributo
  AND AD.cod_modulo   = ACA.cod_modulo
  AND AD.cod_cadastro = ACA.cod_cadastro
  AND ACA.cod_cadastro=5
  AND ACA.cod_modulo  =12


 AND ACA.cod_atributo = 5020;'''';OPEN crCursor FOR EXECUTE stSql;
                                    FETCH crCursor INTO rsRetorno;
                                    CLOSE crCursor;
                                    RETURN rsRetorno.valor;
                                    END;
                                    '' LANGUAGE plpgsql;
             ';
    EXECUTE stSQL;

    stSQL := '
CREATE OR REPLACE FUNCTION recuperaCadastroImobiliarioTipoDeEdificacaoEstrutura(  INTEGER, INTEGER ) RETURNS VARCHAR AS ''DECLARE stSql VARCHAR; crCursor  REFCURSOR; rsRetorno RECORD;inCodTipo ALIAS FOR $1;inCodConstrucao ALIAS FOR $2;BEGIN stSql := '''' SELECT
     AD.cod_cadastro,
     AD.cod_atributo,
     AD.ativo,
     AD.nao_nulo,
     AD.nom_atributo,
     CASE TA.cod_tipo
         WHEN 4 THEN  administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,'''''''''''''''')
     END AS valor_padrao,
     CASE TA.cod_tipo
       WHEN 3 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,''''''''''''''''))
       WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,VALOR.valor))
         ELSE         null
     END AS valor_padrao_desc,
     CASE TA.cod_tipo WHEN
         4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         null
     END AS valor_desc,
     AD.ajuda,
     AD.mascara,
     TA.cod_tipo,
     TA.nom_tipo,
     VALOR.valor,
     VALOR.timestamp
  FROM
     administracao.atributo_dinamico          AS AD,
     administracao.tipo_atributo              AS TA,
     imobiliario.atributo_tipo_edificacao AS ACA
     LEFT JOIN
     imobiliario.atributo_tipo_edificacao_valor         AS VALOR
  ON ( ACA.cod_atributo = VALOR.cod_atributo
          AND ACA.cod_cadastro = VALOR.cod_cadastro
              AND VALOR.cod_tipo = ''''||inCodTipo||''''
 AND VALOR.cod_construcao = ''''||inCodConstrucao||''''
 AND ACA.cod_atributo = 5021
         AND CAST(VALOR.timestamp as varchar)||CAST(VALOR.cod_atributo as varchar) IN (
             SELECT
         (CAST(max(VALOR.timestamp) as varchar))::varchar||CAST(VALOR.cod_atributo as varchar)
             FROM
                imobiliario.atributo_tipo_edificacao AS ACA,
                imobiliario.atributo_tipo_edificacao_valor         AS VALOR,
                administracao.atributo_dinamico          AS AD,
                administracao.tipo_atributo              AS TA
             WHERE
                ACA.cod_atributo = AD.cod_atributo
                AND ACA.cod_cadastro = AD.cod_cadastro
                AND ACA.cod_modulo   = AD.cod_modulo
             AND ACA.cod_atributo = VALOR.cod_atributo
             AND ACA.cod_cadastro = VALOR.cod_cadastro
             AND ACA.cod_modulo   = VALOR.cod_modulo
              AND VALOR.cod_tipo = ''''||inCodTipo||''''
 AND VALOR.cod_construcao = ''''||inCodConstrucao||''''
 AND ACA.cod_atributo = 5021
             AND AD.cod_tipo = TA.cod_tipo
             AND ACA.ativo = true
             AND AD.cod_modulo   =12
             AND AD.cod_cadastro=5

             GROUP BY VALOR.cod_cadastro, VALOR.cod_atributo
                                  )

     )
  WHERE
      AD.cod_tipo = TA.cod_tipo
  AND ACA.ativo = true
  AND     AD.ativo
  AND AD.cod_atributo =  ACA.cod_atributo
  AND AD.cod_modulo   = ACA.cod_modulo
  AND AD.cod_cadastro = ACA.cod_cadastro
  AND ACA.cod_cadastro=5
  AND ACA.cod_modulo  =12


 AND ACA.cod_atributo = 5021;'''';OPEN crCursor FOR EXECUTE stSql;
                                    FETCH crCursor INTO rsRetorno;
                                    CLOSE crCursor;
                                    RETURN rsRetorno.valor;
                                    END;
                                    '' LANGUAGE plpgsql;
             ';
    EXECUTE stSQL;

    stSQL := '
CREATE OR REPLACE FUNCTION recuperaCadastroImobiliarioTipoDeEdificacaoPadraoDeConstrucao(  INTEGER, INTEGER ) RETURNS VARCHAR AS ''DECLARE stSql VARCHAR; crCursor  REFCURSOR; rsRetorno RECORD;inCodTipo ALIAS FOR $1;inCodConstrucao ALIAS FOR $2;BEGIN stSql := '''' SELECT
     AD.cod_cadastro,
     AD.cod_atributo,
     AD.ativo,
     AD.nao_nulo,
     AD.nom_atributo,
     CASE TA.cod_tipo
         WHEN 4 THEN  administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,'''''''''''''''')
     END AS valor_padrao,
     CASE TA.cod_tipo
       WHEN 3 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,''''''''''''''''))
       WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,VALOR.valor))
         ELSE         null
     END AS valor_padrao_desc,
     CASE TA.cod_tipo WHEN
         4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         null
     END AS valor_desc,
     AD.ajuda,
     AD.mascara,
     TA.cod_tipo,
     TA.nom_tipo,
     VALOR.valor,
     VALOR.timestamp
  FROM
     administracao.atributo_dinamico          AS AD,
     administracao.tipo_atributo              AS TA,
     imobiliario.atributo_tipo_edificacao AS ACA
     LEFT JOIN
     imobiliario.atributo_tipo_edificacao_valor         AS VALOR
  ON ( ACA.cod_atributo = VALOR.cod_atributo
          AND ACA.cod_cadastro = VALOR.cod_cadastro
              AND VALOR.cod_tipo = ''''||inCodTipo||''''
 AND VALOR.cod_construcao = ''''||inCodConstrucao||''''
 AND ACA.cod_atributo = 5022
         AND CAST(VALOR.timestamp as varchar)||CAST(VALOR.cod_atributo as varchar) IN (
             SELECT
         (CAST(max(VALOR.timestamp) as varchar))::varchar||CAST(VALOR.cod_atributo as varchar)
             FROM
                imobiliario.atributo_tipo_edificacao AS ACA,
                imobiliario.atributo_tipo_edificacao_valor         AS VALOR,
                administracao.atributo_dinamico          AS AD,
                administracao.tipo_atributo              AS TA
             WHERE
                ACA.cod_atributo = AD.cod_atributo
                AND ACA.cod_cadastro = AD.cod_cadastro
                AND ACA.cod_modulo   = AD.cod_modulo
             AND ACA.cod_atributo = VALOR.cod_atributo
             AND ACA.cod_cadastro = VALOR.cod_cadastro
             AND ACA.cod_modulo   = VALOR.cod_modulo
              AND VALOR.cod_tipo = ''''||inCodTipo||''''
 AND VALOR.cod_construcao = ''''||inCodConstrucao||''''
 AND ACA.cod_atributo = 5022
             AND AD.cod_tipo = TA.cod_tipo
             AND ACA.ativo = true
             AND AD.cod_modulo   =12
             AND AD.cod_cadastro=5

             GROUP BY VALOR.cod_cadastro, VALOR.cod_atributo
                                  )

     )
  WHERE
      AD.cod_tipo = TA.cod_tipo
  AND ACA.ativo = true
  AND     AD.ativo
  AND AD.cod_atributo =  ACA.cod_atributo
  AND AD.cod_modulo   = ACA.cod_modulo
  AND AD.cod_cadastro = ACA.cod_cadastro
  AND ACA.cod_cadastro=5
  AND ACA.cod_modulo  =12


 AND ACA.cod_atributo = 5022;'''';OPEN crCursor FOR EXECUTE stSql;
                                    FETCH crCursor INTO rsRetorno;
                                    CLOSE crCursor;
                                    RETURN rsRetorno.valor;
                                    END;
                                    '' LANGUAGE plpgsql;
             ';
    EXECUTE stSQL;

    stSQL := '
CREATE OR REPLACE FUNCTION recuperaCadastroImobiliarioTipoDeEdificacaoConservacao(  INTEGER, INTEGER ) RETURNS VARCHAR AS ''DECLARE stSql VARCHAR; crCursor  REFCURSOR; rsRetorno RECORD;inCodTipo ALIAS FOR $1;inCodConstrucao ALIAS FOR $2;BEGIN stSql := '''' SELECT
     AD.cod_cadastro,
     AD.cod_atributo,
     AD.ativo,
     AD.nao_nulo,
     AD.nom_atributo,
     CASE TA.cod_tipo
         WHEN 4 THEN  administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,'''''''''''''''')
     END AS valor_padrao,
     CASE TA.cod_tipo
       WHEN 3 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,''''''''''''''''))
       WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,VALOR.valor))
         ELSE         null
     END AS valor_padrao_desc,
     CASE TA.cod_tipo WHEN
         4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         null
     END AS valor_desc,
     AD.ajuda,
     AD.mascara,
     TA.cod_tipo,
     TA.nom_tipo,
     VALOR.valor,
     VALOR.timestamp
  FROM
     administracao.atributo_dinamico          AS AD,
     administracao.tipo_atributo              AS TA,
     imobiliario.atributo_tipo_edificacao AS ACA
     LEFT JOIN
     imobiliario.atributo_tipo_edificacao_valor         AS VALOR
  ON ( ACA.cod_atributo = VALOR.cod_atributo
          AND ACA.cod_cadastro = VALOR.cod_cadastro
              AND VALOR.cod_tipo = ''''||inCodTipo||''''
 AND VALOR.cod_construcao = ''''||inCodConstrucao||''''
 AND ACA.cod_atributo = 5023
         AND CAST(VALOR.timestamp as varchar)||CAST(VALOR.cod_atributo as varchar) IN (
             SELECT
         (CAST(max(VALOR.timestamp) as varchar))::varchar||CAST(VALOR.cod_atributo as varchar)
             FROM
                imobiliario.atributo_tipo_edificacao AS ACA,
                imobiliario.atributo_tipo_edificacao_valor         AS VALOR,
                administracao.atributo_dinamico          AS AD,
                administracao.tipo_atributo              AS TA
             WHERE
                ACA.cod_atributo = AD.cod_atributo
                AND ACA.cod_cadastro = AD.cod_cadastro
                AND ACA.cod_modulo   = AD.cod_modulo
             AND ACA.cod_atributo = VALOR.cod_atributo
             AND ACA.cod_cadastro = VALOR.cod_cadastro
             AND ACA.cod_modulo   = VALOR.cod_modulo
              AND VALOR.cod_tipo = ''''||inCodTipo||''''
 AND VALOR.cod_construcao = ''''||inCodConstrucao||''''
 AND ACA.cod_atributo = 5023
             AND AD.cod_tipo = TA.cod_tipo
             AND ACA.ativo = true
             AND AD.cod_modulo   =12
             AND AD.cod_cadastro=5

             GROUP BY VALOR.cod_cadastro, VALOR.cod_atributo
                                  )

     )
  WHERE
      AD.cod_tipo = TA.cod_tipo
  AND ACA.ativo = true
  AND     AD.ativo
  AND AD.cod_atributo =  ACA.cod_atributo
  AND AD.cod_modulo   = ACA.cod_modulo
  AND AD.cod_cadastro = ACA.cod_cadastro
  AND ACA.cod_cadastro=5
  AND ACA.cod_modulo  =12


 AND ACA.cod_atributo = 5023;'''';OPEN crCursor FOR EXECUTE stSql;
                                    FETCH crCursor INTO rsRetorno;
                                    CLOSE crCursor;
                                    RETURN rsRetorno.valor;
                                    END;
                                    '' LANGUAGE plpgsql;
             ';
    EXECUTE stSQL;

    stSQL := '
CREATE OR REPLACE FUNCTION recuperaCadastroImobiliarioTipoDeEdificacaoInstalacaoSanitaria(  INTEGER, INTEGER ) RETURNS VARCHAR AS ''DECLARE stSql VARCHAR; crCursor  REFCURSOR; rsRetorno RECORD;inCodTipo ALIAS FOR $1;inCodConstrucao ALIAS FOR $2;BEGIN stSql := '''' SELECT
     AD.cod_cadastro,
     AD.cod_atributo,
     AD.ativo,
     AD.nao_nulo,
     AD.nom_atributo,
     CASE TA.cod_tipo
         WHEN 4 THEN  administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,'''''''''''''''')
     END AS valor_padrao,
     CASE TA.cod_tipo
       WHEN 3 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,''''''''''''''''))
       WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,VALOR.valor))
         ELSE         null
     END AS valor_padrao_desc,
     CASE TA.cod_tipo WHEN
         4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         null
     END AS valor_desc,
     AD.ajuda,
     AD.mascara,
     TA.cod_tipo,
     TA.nom_tipo,
     VALOR.valor,
     VALOR.timestamp
  FROM
     administracao.atributo_dinamico          AS AD,
     administracao.tipo_atributo              AS TA,
     imobiliario.atributo_tipo_edificacao AS ACA
     LEFT JOIN
     imobiliario.atributo_tipo_edificacao_valor         AS VALOR
  ON ( ACA.cod_atributo = VALOR.cod_atributo
          AND ACA.cod_cadastro = VALOR.cod_cadastro
              AND VALOR.cod_tipo = ''''||inCodTipo||''''
 AND VALOR.cod_construcao = ''''||inCodConstrucao||''''
 AND ACA.cod_atributo = 5024
         AND CAST(VALOR.timestamp as varchar)||CAST(VALOR.cod_atributo as varchar) IN (
             SELECT
         (CAST(max(VALOR.timestamp) as varchar))::varchar||CAST(VALOR.cod_atributo as varchar)
             FROM
                imobiliario.atributo_tipo_edificacao AS ACA,
                imobiliario.atributo_tipo_edificacao_valor         AS VALOR,
                administracao.atributo_dinamico          AS AD,
                administracao.tipo_atributo              AS TA
             WHERE
                ACA.cod_atributo = AD.cod_atributo
                AND ACA.cod_cadastro = AD.cod_cadastro
                AND ACA.cod_modulo   = AD.cod_modulo
             AND ACA.cod_atributo = VALOR.cod_atributo
             AND ACA.cod_cadastro = VALOR.cod_cadastro
             AND ACA.cod_modulo   = VALOR.cod_modulo
              AND VALOR.cod_tipo = ''''||inCodTipo||''''
 AND VALOR.cod_construcao = ''''||inCodConstrucao||''''
 AND ACA.cod_atributo = 5024
             AND AD.cod_tipo = TA.cod_tipo
             AND ACA.ativo = true
             AND AD.cod_modulo   =12
             AND AD.cod_cadastro=5

             GROUP BY VALOR.cod_cadastro, VALOR.cod_atributo
                                  )

     )
  WHERE
      AD.cod_tipo = TA.cod_tipo
  AND ACA.ativo = true
  AND     AD.ativo
  AND AD.cod_atributo =  ACA.cod_atributo
  AND AD.cod_modulo   = ACA.cod_modulo
  AND AD.cod_cadastro = ACA.cod_cadastro
  AND ACA.cod_cadastro=5
  AND ACA.cod_modulo  =12


 AND ACA.cod_atributo = 5024;'''';OPEN crCursor FOR EXECUTE stSql;
                                    FETCH crCursor INTO rsRetorno;
                                    CLOSE crCursor;
                                    RETURN rsRetorno.valor;
                                    END;
                                    '' LANGUAGE plpgsql;
             ';
    EXECUTE stSQL;

    stSQL := '
CREATE OR REPLACE FUNCTION recuperaCadastroImobiliarioTipoDeEdificacaoInstalacaoEletrica(  INTEGER, INTEGER ) RETURNS VARCHAR AS ''DECLARE stSql VARCHAR; crCursor  REFCURSOR; rsRetorno RECORD;inCodTipo ALIAS FOR $1;inCodConstrucao ALIAS FOR $2;BEGIN stSql := '''' SELECT
     AD.cod_cadastro,
     AD.cod_atributo,
     AD.ativo,
     AD.nao_nulo,
     AD.nom_atributo,
     CASE TA.cod_tipo
         WHEN 4 THEN  administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,'''''''''''''''')
     END AS valor_padrao,
     CASE TA.cod_tipo
       WHEN 3 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,''''''''''''''''))
       WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,VALOR.valor))
         ELSE         null
     END AS valor_padrao_desc,
     CASE TA.cod_tipo WHEN
         4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         null
     END AS valor_desc,
     AD.ajuda,
     AD.mascara,
     TA.cod_tipo,
     TA.nom_tipo,
     VALOR.valor,
     VALOR.timestamp
  FROM
     administracao.atributo_dinamico          AS AD,
     administracao.tipo_atributo              AS TA,
     imobiliario.atributo_tipo_edificacao AS ACA
     LEFT JOIN
     imobiliario.atributo_tipo_edificacao_valor         AS VALOR
  ON ( ACA.cod_atributo = VALOR.cod_atributo
          AND ACA.cod_cadastro = VALOR.cod_cadastro
              AND VALOR.cod_tipo = ''''||inCodTipo||''''
 AND VALOR.cod_construcao = ''''||inCodConstrucao||''''
 AND ACA.cod_atributo = 5025
         AND CAST(VALOR.timestamp as varchar)||CAST(VALOR.cod_atributo as varchar) IN (
             SELECT
         (CAST(max(VALOR.timestamp) as varchar))::varchar||CAST(VALOR.cod_atributo as varchar)
             FROM
                imobiliario.atributo_tipo_edificacao AS ACA,
                imobiliario.atributo_tipo_edificacao_valor         AS VALOR,
                administracao.atributo_dinamico          AS AD,
                administracao.tipo_atributo              AS TA
             WHERE
                ACA.cod_atributo = AD.cod_atributo
                AND ACA.cod_cadastro = AD.cod_cadastro
                AND ACA.cod_modulo   = AD.cod_modulo
             AND ACA.cod_atributo = VALOR.cod_atributo
             AND ACA.cod_cadastro = VALOR.cod_cadastro
             AND ACA.cod_modulo   = VALOR.cod_modulo
              AND VALOR.cod_tipo = ''''||inCodTipo||''''
 AND VALOR.cod_construcao = ''''||inCodConstrucao||''''
 AND ACA.cod_atributo = 5025
             AND AD.cod_tipo = TA.cod_tipo
             AND ACA.ativo = true
             AND AD.cod_modulo   =12
             AND AD.cod_cadastro=5

             GROUP BY VALOR.cod_cadastro, VALOR.cod_atributo
                                  )

     )
  WHERE
      AD.cod_tipo = TA.cod_tipo
  AND ACA.ativo = true
  AND     AD.ativo
  AND AD.cod_atributo =  ACA.cod_atributo
  AND AD.cod_modulo   = ACA.cod_modulo
  AND AD.cod_cadastro = ACA.cod_cadastro
  AND ACA.cod_cadastro=5
  AND ACA.cod_modulo  =12


 AND ACA.cod_atributo = 5025;'''';OPEN crCursor FOR EXECUTE stSql;
                                    FETCH crCursor INTO rsRetorno;
                                    CLOSE crCursor;
                                    RETURN rsRetorno.valor;
                                    END;
                                    '' LANGUAGE plpgsql;
             ';
    EXECUTE stSQL;

    stSQL := '
CREATE OR REPLACE FUNCTION recuperaCadastroImobiliarioTipoDeEdificacaoCECoeficienteDeEdificacao(  INTEGER, INTEGER ) RETURNS VARCHAR AS ''DECLARE stSql VARCHAR; crCursor  REFCURSOR; rsRetorno RECORD;inCodTipo ALIAS FOR $1;inCodConstrucao ALIAS FOR $2;BEGIN stSql := '''' SELECT
     AD.cod_cadastro,
     AD.cod_atributo,
     AD.ativo,
     AD.nao_nulo,
     AD.nom_atributo,
     CASE TA.cod_tipo
         WHEN 4 THEN  administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,'''''''''''''''')
     END AS valor_padrao,
     CASE TA.cod_tipo
       WHEN 3 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,''''''''''''''''))
       WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,VALOR.valor))
         ELSE         null
     END AS valor_padrao_desc,
     CASE TA.cod_tipo WHEN
         4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         null
     END AS valor_desc,
     AD.ajuda,
     AD.mascara,
     TA.cod_tipo,
     TA.nom_tipo,
     VALOR.valor,
     VALOR.timestamp
  FROM
     administracao.atributo_dinamico          AS AD,
     administracao.tipo_atributo              AS TA,
     imobiliario.atributo_tipo_edificacao AS ACA
     LEFT JOIN
     imobiliario.atributo_tipo_edificacao_valor         AS VALOR
  ON ( ACA.cod_atributo = VALOR.cod_atributo
          AND ACA.cod_cadastro = VALOR.cod_cadastro
              AND VALOR.cod_tipo = ''''||inCodTipo||''''
 AND VALOR.cod_construcao = ''''||inCodConstrucao||''''
 AND ACA.cod_atributo = 5052
         AND CAST(VALOR.timestamp as varchar)||CAST(VALOR.cod_atributo as varchar) IN (
             SELECT
         (CAST(max(VALOR.timestamp) as varchar))::varchar||CAST(VALOR.cod_atributo as varchar)
             FROM
                imobiliario.atributo_tipo_edificacao AS ACA,
                imobiliario.atributo_tipo_edificacao_valor         AS VALOR,
                administracao.atributo_dinamico          AS AD,
                administracao.tipo_atributo              AS TA
             WHERE
                ACA.cod_atributo = AD.cod_atributo
                AND ACA.cod_cadastro = AD.cod_cadastro
                AND ACA.cod_modulo   = AD.cod_modulo
             AND ACA.cod_atributo = VALOR.cod_atributo
             AND ACA.cod_cadastro = VALOR.cod_cadastro
             AND ACA.cod_modulo   = VALOR.cod_modulo
              AND VALOR.cod_tipo = ''''||inCodTipo||''''
 AND VALOR.cod_construcao = ''''||inCodConstrucao||''''
 AND ACA.cod_atributo = 5052
             AND AD.cod_tipo = TA.cod_tipo
             AND ACA.ativo = true
             AND AD.cod_modulo   =12
             AND AD.cod_cadastro=5

             GROUP BY VALOR.cod_cadastro, VALOR.cod_atributo
                                  )

     )
  WHERE
      AD.cod_tipo = TA.cod_tipo
  AND ACA.ativo = true
  AND     AD.ativo
  AND AD.cod_atributo =  ACA.cod_atributo
  AND AD.cod_modulo   = ACA.cod_modulo
  AND AD.cod_cadastro = ACA.cod_cadastro
  AND ACA.cod_cadastro=5
  AND ACA.cod_modulo  =12


 AND ACA.cod_atributo = 5052;'''';OPEN crCursor FOR EXECUTE stSql;
                                    FETCH crCursor INTO rsRetorno;
                                    CLOSE crCursor;
                                    RETURN rsRetorno.valor;
                                    END;
                                    '' LANGUAGE plpgsql;
                 ';
    EXECUTE stSQL;


UPDATE administracao.corpo_funcao_externa SET linha = ' SE   #stIsento  =  "3" ENTAO    ' WHERE cod_modulo = 25 AND cod_biblioteca = 2 AND cod_funcao = 1 AND cod_linha =  2;
UPDATE administracao.funcao_externa SET     cod_modulo= 25,    cod_biblioteca= 2,    cod_funcao= 1,    comentario=
    'Regra de validação para concessão de desoneração no cálculo do IPTU 2011',    corpo_pl=
    'FUNCTION  regraDesoneracaoIPTU2011(INTEGER) RETURNS BOOLEAN as ''
DECLARE
inImovel ALIAS FOR $1;

  boRetorno BOOLEAN;
  stIsento VARCHAR := '''''''';
BEGIN
stIsento := recuperaCadastroImobiliarioImovelIPTU(  inImovel  );
     IF   stIsento  =  ''''3'''' THEN
    boRetorno := TRUE;
ELSE
    boRetorno := FALSE;
END IF;
RETURN boRetorno;
END;
 '' LANGUAGE ''plpgsql'';
',    corpo_ln=
    '' WHERE cod_modulo = 25 AND cod_biblioteca = 2 AND cod_funcao = 1;


UPDATE administracao.corpo_funcao_externa SET linha = ' SE   #stIsento  =  "3" ENTAO    ' WHERE cod_modulo = 25 AND cod_biblioteca = 2 AND cod_funcao = 2 AND cod_linha =  2;
UPDATE administracao.funcao_externa SET     cod_modulo= 25,    cod_biblioteca= 2,    cod_funcao= 2,    comentario=
    'Regra de validação para concessão de desoneração no cálculo do IPTU 2011',    corpo_pl=
    'FUNCTION  regraDesoneracaoIPTU2009(INTEGER) RETURNS BOOLEAN as ''
DECLARE
inImovel ALIAS FOR $1;

  boRetorno BOOLEAN;
  stIsento VARCHAR := '''''''';
BEGIN
stIsento := recuperaCadastroImobiliarioImovelIPTU(  inImovel  );
     IF   stIsento  =  ''''3'''' THEN
    boRetorno := TRUE;
ELSE
    boRetorno := FALSE;
END IF;
RETURN boRetorno;
END;
 '' LANGUAGE ''plpgsql'';
',    corpo_ln=
    '' WHERE cod_modulo = 25 AND cod_biblioteca = 2 AND cod_funcao = 2 ;

UPDATE administracao.corpo_funcao_externa SET linha = ' SE   #stIsento  =  "3" ENTAO    ' WHERE cod_modulo = 25 AND cod_biblioteca = 2 AND cod_funcao = 3 AND cod_linha =  2;
UPDATE administracao.funcao_externa SET     cod_modulo= 25,    cod_biblioteca= 2,    cod_funcao= 3,    comentario=
    'Regra de validação para concessão de desoneração no cálculo do IPTU 2011',    corpo_pl=
    'FUNCTION  regraDesoneracaoIPTU2010(INTEGER) RETURNS BOOLEAN as ''
DECLARE
inImovel ALIAS FOR $1;

  boRetorno BOOLEAN;
  stIsento VARCHAR := '''''''';
BEGIN
stIsento := recuperaCadastroImobiliarioImovelIPTU(  inImovel  );
     IF   stIsento  =  ''''3'''' THEN
    boRetorno := TRUE;
ELSE
    boRetorno := FALSE;
END IF;
RETURN boRetorno;
END;
 '' LANGUAGE ''plpgsql'';
',    corpo_ln=
    '' WHERE cod_modulo = 25 AND cod_biblioteca = 2 AND cod_funcao = 3 ;

UPDATE administracao.corpo_funcao_externa SET linha = ' SE     #stIsento  =   "1" ENTAO ' WHERE cod_modulo = 25 AND cod_biblioteca = 2 AND cod_funcao = 4 AND cod_linha = 11;
UPDATE administracao.funcao_externa SET     cod_modulo= 25,    cod_biblioteca= 2,    cod_funcao= 4,    comentario=
    'Regra de validação para concessão de desoneração no cálculo de concessão de alvarás 2012',    corpo_pl=
    'FUNCTION  regradesoneracaoAlvara2012(INTEGER) RETURNS BOOLEAN as ''
DECLARE
inEmpresa ALIAS FOR $1;

  boRetorno BOOLEAN;
  stAlvaraIsento VARCHAR := '''''''';
  stIsento VARCHAR := '''''''';
  stTipoInscricao VARCHAR := '''''''';
BEGIN
stTipoInscricao := buscaTipoDaInscricaoEconomica(  inEmpresa  );
IF   stTipoInscricao  =  ''''direito'''' THEN
    stIsento := recuperaCadastroEconomicoInscricaoEconomicaEmpresaDeDireitoAlvaraIsento(  inEmpresa  );
END IF;
IF   stTipoInscricao  =  ''''fato'''' THEN
    stIsento := recuperaCadastroEconomicoInscricaoEconomicoEmpresaDeFatoAlvaraIsento(  inEmpresa  );
END IF;
IF   stTipoInscricao  =  ''''autonomo'''' THEN
    stIsento := recuperaCadastroEconomicoInscricaoEconomicaAutonomoAlvaraIsento(  inEmpresa  );
END IF;
     IF     stIsento  =   ''''1'''' THEN
    boRetorno := TRUE;
ELSE
    boRetorno := FALSE;
END IF;
RETURN boRetorno;
END;
 '' LANGUAGE ''plpgsql'';
',    corpo_ln=
    '' WHERE cod_modulo = 25 AND cod_biblioteca = 2 AND cod_funcao = 4 ;

UPDATE administracao.corpo_funcao_externa SET linha = ' SE     #stIsento  =   "1" ENTAO ' WHERE cod_modulo = 25 AND cod_biblioteca = 2 AND cod_funcao = 5 AND cod_linha = 11;
UPDATE administracao.funcao_externa SET     cod_modulo= 25,    cod_biblioteca= 2,    cod_funcao= 5,    comentario=
    'Regra de validação para concessão de desoneração na emissão de Nota Avulsa 2012',    corpo_pl=
    'FUNCTION  regraDesoneracaoNotaAvulsa2012(INTEGER) RETURNS BOOLEAN as ''
DECLARE
inEmpresa ALIAS FOR $1;

  boRetorno BOOLEAN;
  stAlvaraIsento VARCHAR := '''''''';
  stIsento VARCHAR := '''''''';
  stTipoInscricao VARCHAR := '''''''';
BEGIN
stTipoInscricao := buscaTipoDaInscricaoEconomica(  inEmpresa  );
IF   stTipoInscricao  =  ''''direito'''' THEN
    stIsento := recuperaCadastroEconomicoInscricaoEconomicaEmpresaDeDireitoISSIsento(  inEmpresa  );
END IF;
IF   stTipoInscricao  =  ''''fato'''' THEN
    stIsento := recuperaCadastroEconomicoInscricaoEconomicoEmpresaDeFatoISSIsento(  inEmpresa  );
END IF;
IF   stTipoInscricao  =  ''''autonomo'''' THEN
    stIsento := recuperaCadastroEconomicoInscricaoEconomicaAutonomoISSIsento(  inEmpresa  );
END IF;
     IF     stIsento  =   ''''1'''' THEN
    boRetorno := TRUE;
ELSE
    boRetorno := FALSE;
END IF;
RETURN boRetorno;
END;
 '' LANGUAGE ''plpgsql'';
',    corpo_ln=
    '' WHERE cod_modulo = 25 AND cod_biblioteca = 2 AND cod_funcao = 5 ;


UPDATE administracao.corpo_funcao_externa SET linha = ' SE     #stIsento  =   "1" ENTAO ' WHERE cod_modulo = 25 AND cod_biblioteca = 2 AND cod_funcao = 6 AND cod_linha = 11;
UPDATE administracao.funcao_externa SET     cod_modulo= 25,    cod_biblioteca= 2,    cod_funcao= 6,    comentario=
    'Regra de validação para concessão de desoneração na emissão de Nota Avulsa 2012',    corpo_pl=
    'FUNCTION  regraDesoneracaoISS2012(INTEGER) RETURNS BOOLEAN as ''
DECLARE
inEmpresa ALIAS FOR $1;

  boRetorno BOOLEAN;
  stAlvaraIsento VARCHAR := '''''''';
  stIsento VARCHAR := '''''''';
  stTipoInscricao VARCHAR := '''''''';
BEGIN
stTipoInscricao := buscaTipoDaInscricaoEconomica(  inEmpresa  );
IF   stTipoInscricao  =  ''''direito'''' THEN
    stIsento := recuperaCadastroEconomicoInscricaoEconomicaEmpresaDeDireitoISSIsento(  inEmpresa  );
END IF;
IF   stTipoInscricao  =  ''''fato'''' THEN
    stIsento := recuperaCadastroEconomicoInscricaoEconomicoEmpresaDeFatoISSIsento(  inEmpresa  );
END IF;
IF   stTipoInscricao  =  ''''autonomo'''' THEN
    stIsento := recuperaCadastroEconomicoInscricaoEconomicaAutonomoISSIsento(  inEmpresa  );
END IF;
     IF     stIsento  =   ''''1'''' THEN
    boRetorno := TRUE;
ELSE
    boRetorno := FALSE;
END IF;
RETURN boRetorno;
END;
 '' LANGUAGE ''plpgsql'';
',    corpo_ln=
    '' WHERE cod_modulo = 25 AND cod_biblioteca = 2 AND cod_funcao = 6 ;

UPDATE administracao.corpo_funcao_externa SET linha = ' SE   #stIsento  =  "3" ENTAO    ' WHERE cod_modulo = 25 AND cod_biblioteca = 2 AND cod_funcao = 7 AND cod_linha =  2;
UPDATE administracao.funcao_externa SET     cod_modulo= 25,    cod_biblioteca= 2,    cod_funcao= 7,    comentario=
    'Regra de validação para concessão de desoneração no cálculo do IPTU 2012',    corpo_pl=
    'FUNCTION  regraDesoneracaoIPTU2012(INTEGER) RETURNS BOOLEAN as ''
DECLARE
inImovel ALIAS FOR $1;

  boRetorno BOOLEAN;
  stIsento VARCHAR := '''''''';
BEGIN
stIsento := recuperaCadastroImobiliarioImovelIPTU(  inImovel  );
     IF   stIsento  =  ''''3'''' THEN
    boRetorno := TRUE;
ELSE
    boRetorno := FALSE;
END IF;
RETURN boRetorno;
END;
 '' LANGUAGE ''plpgsql'';
',    corpo_ln=
    '' WHERE cod_modulo = 25 AND cod_biblioteca = 2 AND cod_funcao = 7 ;


UPDATE administracao.funcao_externa SET     cod_modulo= 33,    cod_biblioteca= 2,    cod_funcao= 1,    comentario=
    '',    corpo_pl=
    'FUNCTION  regraAcrescimoGeralDivida(INTEGER) RETURNS BOOLEAN as ''
DECLARE
inRegistro ALIAS FOR $1;

  boRetorno BOOLEAN := TRUE;
BEGIN
RETURN boRetorno;
END;
 '' LANGUAGE ''plpgsql'';
',    corpo_ln=
    '' WHERE cod_modulo = 33 AND cod_biblioteca = 2 AND cod_funcao = 1 ;

END;
$$ LANGUAGE 'plpgsql';



CREATE OR REPLACE FUNCTION manutencao_mariana() RETURNS VOID AS $$
DECLARE
    stSQL   VARCHAR;
BEGIN
        stSQL := '
CREATE OR REPLACE FUNCTION recuperaCadastroEconomicoInscricaoEconomicaAutonomoAtividade(  INTEGER ) RETURNS VARCHAR AS ''DECLARE stSql VARCHAR; crCursor  REFCURSOR; rsRetorno RECORD;inInscricaoEconomica ALIAS FOR $1;BEGIN stSql := ''''  SELECT
     AD.cod_cadastro,
     AD.cod_atributo,
     AD.ativo,
     AD.nao_nulo,
     AD.nom_atributo,
     CASE TA.cod_tipo
         WHEN 4 THEN  administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,'''''''''''''''')
     END AS valor_padrao,
     CASE TA.cod_tipo
       WHEN 3 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,''''''''''''''''))
       WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,VALOR.valor))
         ELSE         null
     END AS valor_padrao_desc,
     CASE TA.cod_tipo WHEN
         4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         null
     END AS valor_desc,
     AD.ajuda,
     AD.mascara,
     TA.cod_tipo,
     TA.nom_tipo,
     VALOR.valor,
     VALOR.timestamp
  FROM
     administracao.atributo_dinamico          AS AD,
     administracao.tipo_atributo              AS TA,
     administracao.atributo_dinamico AS ACA
     LEFT JOIN
     economico.atributo_cad_econ_autonomo_valor         AS VALOR
  ON ( ACA.cod_atributo = VALOR.cod_atributo
          AND ACA.cod_cadastro = VALOR.cod_cadastro
              AND VALOR.inscricao_economica = ''''||inInscricaoEconomica||''''
 AND ACA.cod_atributo = 5039
         AND CAST(VALOR.timestamp as varchar)||CAST(VALOR.cod_atributo as varchar) IN (
             SELECT
         (CAST(max(VALOR.timestamp) as varchar))::varchar||CAST(VALOR.cod_atributo as varchar)
             FROM
                administracao.atributo_dinamico AS ACA,
                economico.atributo_cad_econ_autonomo_valor         AS VALOR,
                administracao.atributo_dinamico          AS AD,
                administracao.tipo_atributo              AS TA
             WHERE
                ACA.cod_atributo = AD.cod_atributo
                AND ACA.cod_cadastro = AD.cod_cadastro
                AND ACA.cod_modulo   = AD.cod_modulo
             AND ACA.cod_atributo = VALOR.cod_atributo
             AND ACA.cod_cadastro = VALOR.cod_cadastro
             AND ACA.cod_modulo   = VALOR.cod_modulo
              AND VALOR.inscricao_economica = ''''||inInscricaoEconomica||''''
 AND ACA.cod_atributo = 5039
             AND AD.cod_tipo = TA.cod_tipo
             AND ACA.ativo = true
             AND AD.cod_modulo   =14
             AND AD.cod_cadastro=3

             GROUP BY VALOR.cod_cadastro, VALOR.cod_atributo
                                  )

     )
  WHERE
      AD.cod_tipo = TA.cod_tipo
  AND ACA.ativo = true
  AND     AD.ativo
  AND AD.cod_atributo =  ACA.cod_atributo
  AND AD.cod_modulo   = ACA.cod_modulo
  AND AD.cod_cadastro = ACA.cod_cadastro
  AND ACA.cod_cadastro=3
  AND ACA.cod_modulo  =14


 AND ACA.cod_atributo = 5039;'''';OPEN crCursor FOR EXECUTE stSql;
                                    FETCH crCursor INTO rsRetorno;
                                    CLOSE crCursor;
                                    RETURN rsRetorno.valor;
                                    END;
                                    '' LANGUAGE plpgsql;
             ';
    EXECUTE stSQL;

    stSQL := '
CREATE OR REPLACE FUNCTION recuperaCadastroEconomicoInscricaoEconomicaEmpresaDeDireitoValorDeclarado(  INTEGER ) RETURNS VARCHAR AS ''DECLARE stSql VARCHAR; crCursor  REFCURSOR; rsRetorno RECORD;inInscricaoEconomica ALIAS FOR $1;BEGIN stSql := ''''  SELECT
     AD.cod_cadastro,
     AD.cod_atributo,
     AD.ativo,
     AD.nao_nulo,
     AD.nom_atributo,
     CASE TA.cod_tipo
         WHEN 4 THEN  administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,'''''''''''''''')
     END AS valor_padrao,
     CASE TA.cod_tipo
       WHEN 3 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,''''''''''''''''))
       WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,VALOR.valor))
         ELSE         null
     END AS valor_padrao_desc,
     CASE TA.cod_tipo WHEN
         4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         null
     END AS valor_desc,
     AD.ajuda,
     AD.mascara,
     TA.cod_tipo,
     TA.nom_tipo,
     VALOR.valor,
     VALOR.timestamp
  FROM
     administracao.atributo_dinamico          AS AD,
     administracao.tipo_atributo              AS TA,
     administracao.atributo_dinamico AS ACA
     LEFT JOIN
     economico.atributo_empresa_direito_valor         AS VALOR
  ON ( ACA.cod_atributo = VALOR.cod_atributo
          AND ACA.cod_cadastro = VALOR.cod_cadastro
              AND VALOR.inscricao_economica = ''''||inInscricaoEconomica||''''
 AND ACA.cod_atributo = 5
         AND CAST(VALOR.timestamp as varchar)||CAST(VALOR.cod_atributo as varchar) IN (
             SELECT
         (CAST(max(VALOR.timestamp) as varchar))::varchar||CAST(VALOR.cod_atributo as varchar)
             FROM
                administracao.atributo_dinamico AS ACA,
                economico.atributo_empresa_direito_valor         AS VALOR,
                administracao.atributo_dinamico          AS AD,
                administracao.tipo_atributo              AS TA
             WHERE
                ACA.cod_atributo = AD.cod_atributo
                AND ACA.cod_cadastro = AD.cod_cadastro
                AND ACA.cod_modulo   = AD.cod_modulo
             AND ACA.cod_atributo = VALOR.cod_atributo
             AND ACA.cod_cadastro = VALOR.cod_cadastro
             AND ACA.cod_modulo   = VALOR.cod_modulo
              AND VALOR.inscricao_economica = ''''||inInscricaoEconomica||''''
 AND ACA.cod_atributo = 5
             AND AD.cod_tipo = TA.cod_tipo
             AND ACA.ativo = true
             AND AD.cod_modulo   =14
             AND AD.cod_cadastro=2

             GROUP BY VALOR.cod_cadastro, VALOR.cod_atributo
                                  )

     )
  WHERE
      AD.cod_tipo = TA.cod_tipo
  AND ACA.ativo = true
  AND     AD.ativo
  AND AD.cod_atributo =  ACA.cod_atributo
  AND AD.cod_modulo   = ACA.cod_modulo
  AND AD.cod_cadastro = ACA.cod_cadastro
  AND ACA.cod_cadastro=2
  AND ACA.cod_modulo  =14


 AND ACA.cod_atributo = 5;'''';OPEN crCursor FOR EXECUTE stSql;
                                    FETCH crCursor INTO rsRetorno;
                                    CLOSE crCursor;
                                    RETURN rsRetorno.valor;
                                    END;
                                    '' LANGUAGE plpgsql;
             ';
    EXECUTE stSQL;

    stSQL := '
CREATE OR REPLACE FUNCTION recuperaCadastroEconomicoInscricaoEconomicaEmpresaDeDireitoAtividade(  INTEGER ) RETURNS VARCHAR AS ''DECLARE stSql VARCHAR; crCursor  REFCURSOR; rsRetorno RECORD;inInscricaoEconomica ALIAS FOR $1;BEGIN stSql := ''''  SELECT
     AD.cod_cadastro,
     AD.cod_atributo,
     AD.ativo,
     AD.nao_nulo,
     AD.nom_atributo,
     CASE TA.cod_tipo
         WHEN 4 THEN  administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,'''''''''''''''')
     END AS valor_padrao,
     CASE TA.cod_tipo
       WHEN 3 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,''''''''''''''''))
       WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,VALOR.valor))
         ELSE         null
     END AS valor_padrao_desc,
     CASE TA.cod_tipo WHEN
         4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         null
     END AS valor_desc,
     AD.ajuda,
     AD.mascara,
     TA.cod_tipo,
     TA.nom_tipo,
     VALOR.valor,
     VALOR.timestamp
  FROM
     administracao.atributo_dinamico          AS AD,
     administracao.tipo_atributo              AS TA,
     administracao.atributo_dinamico AS ACA
     LEFT JOIN
     economico.atributo_empresa_direito_valor         AS VALOR
  ON ( ACA.cod_atributo = VALOR.cod_atributo
          AND ACA.cod_cadastro = VALOR.cod_cadastro
              AND VALOR.inscricao_economica = ''''||inInscricaoEconomica||''''
 AND ACA.cod_atributo = 5038
         AND CAST(VALOR.timestamp as varchar)||CAST(VALOR.cod_atributo as varchar) IN (
             SELECT
         (CAST(max(VALOR.timestamp) as varchar))::varchar||CAST(VALOR.cod_atributo as varchar)
             FROM
                administracao.atributo_dinamico AS ACA,
                economico.atributo_empresa_direito_valor         AS VALOR,
                administracao.atributo_dinamico          AS AD,
                administracao.tipo_atributo              AS TA
             WHERE
                ACA.cod_atributo = AD.cod_atributo
                AND ACA.cod_cadastro = AD.cod_cadastro
                AND ACA.cod_modulo   = AD.cod_modulo
             AND ACA.cod_atributo = VALOR.cod_atributo
             AND ACA.cod_cadastro = VALOR.cod_cadastro
             AND ACA.cod_modulo   = VALOR.cod_modulo
              AND VALOR.inscricao_economica = ''''||inInscricaoEconomica||''''
 AND ACA.cod_atributo = 5038
             AND AD.cod_tipo = TA.cod_tipo
             AND ACA.ativo = true
             AND AD.cod_modulo   =14
             AND AD.cod_cadastro=2

             GROUP BY VALOR.cod_cadastro, VALOR.cod_atributo
                                  )

     )
  WHERE
      AD.cod_tipo = TA.cod_tipo
  AND ACA.ativo = true
  AND     AD.ativo
  AND AD.cod_atributo =  ACA.cod_atributo
  AND AD.cod_modulo   = ACA.cod_modulo
  AND AD.cod_cadastro = ACA.cod_cadastro
  AND ACA.cod_cadastro=2
  AND ACA.cod_modulo  =14


 AND ACA.cod_atributo = 5038;'''';OPEN crCursor FOR EXECUTE stSql;
                                    FETCH crCursor INTO rsRetorno;
                                    CLOSE crCursor;
                                    RETURN rsRetorno.valor;
                                    END;
                                    '' LANGUAGE plpgsql;
             ';
    EXECUTE stSQL;

    stSQL := '
CREATE OR REPLACE FUNCTION recuperaCadastroEconomicoInscricaoEconomicoEmpresaDeFatoAtividade(  INTEGER ) RETURNS VARCHAR AS ''DECLARE stSql VARCHAR; crCursor  REFCURSOR; rsRetorno RECORD;inInscricaoEconomica ALIAS FOR $1;BEGIN stSql := ''''  SELECT
     AD.cod_cadastro,
     AD.cod_atributo,
     AD.ativo,
     AD.nao_nulo,
     AD.nom_atributo,
     CASE TA.cod_tipo
         WHEN 4 THEN  administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,'''''''''''''''')
     END AS valor_padrao,
     CASE TA.cod_tipo
       WHEN 3 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,''''''''''''''''))
       WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,VALOR.valor))
         ELSE         null
     END AS valor_padrao_desc,
     CASE TA.cod_tipo WHEN
         4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         null
     END AS valor_desc,
     AD.ajuda,
     AD.mascara,
     TA.cod_tipo,
     TA.nom_tipo,
     VALOR.valor,
     VALOR.timestamp
  FROM
     administracao.atributo_dinamico          AS AD,
     administracao.tipo_atributo              AS TA,
     administracao.atributo_dinamico AS ACA
     LEFT JOIN
     economico.atributo_empresa_fato_valor         AS VALOR
  ON ( ACA.cod_atributo = VALOR.cod_atributo
          AND ACA.cod_cadastro = VALOR.cod_cadastro
              AND VALOR.inscricao_economica = ''''||inInscricaoEconomica||''''
 AND ACA.cod_atributo = 5037
         AND CAST(VALOR.timestamp as varchar)||CAST(VALOR.cod_atributo as varchar) IN (
             SELECT
         (CAST(max(VALOR.timestamp) as varchar))::varchar||CAST(VALOR.cod_atributo as varchar)
             FROM
                administracao.atributo_dinamico AS ACA,
                economico.atributo_empresa_fato_valor         AS VALOR,
                administracao.atributo_dinamico          AS AD,
                administracao.tipo_atributo              AS TA
             WHERE
                ACA.cod_atributo = AD.cod_atributo
                AND ACA.cod_cadastro = AD.cod_cadastro
                AND ACA.cod_modulo   = AD.cod_modulo
             AND ACA.cod_atributo = VALOR.cod_atributo
             AND ACA.cod_cadastro = VALOR.cod_cadastro
             AND ACA.cod_modulo   = VALOR.cod_modulo
              AND VALOR.inscricao_economica = ''''||inInscricaoEconomica||''''
 AND ACA.cod_atributo = 5037
             AND AD.cod_tipo = TA.cod_tipo
             AND ACA.ativo = true
             AND AD.cod_modulo   =14
             AND AD.cod_cadastro=1

             GROUP BY VALOR.cod_cadastro, VALOR.cod_atributo
                                  )

     )
  WHERE
      AD.cod_tipo = TA.cod_tipo
  AND ACA.ativo = true
  AND     AD.ativo
  AND AD.cod_atributo =  ACA.cod_atributo
  AND AD.cod_modulo   = ACA.cod_modulo
  AND AD.cod_cadastro = ACA.cod_cadastro
  AND ACA.cod_cadastro=1
  AND ACA.cod_modulo  =14


 AND ACA.cod_atributo = 5037;'''';OPEN crCursor FOR EXECUTE stSql;
                                    FETCH crCursor INTO rsRetorno;
                                    CLOSE crCursor;
                                    RETURN rsRetorno.valor;
                                    END;
                                    '' LANGUAGE plpgsql;
             ';
    EXECUTE stSQL;

    stSQL := '
CREATE OR REPLACE FUNCTION recuperaCadastroImobiliarioImovelTIPODEIMOVEL(  INTEGER ) RETURNS VARCHAR AS ''DECLARE stSql VARCHAR; crCursor  REFCURSOR; rsRetorno RECORD;inInscricaoMunicipal ALIAS FOR $1;BEGIN stSql := ''''  SELECT
     AD.cod_cadastro,
     AD.cod_atributo,
     AD.ativo,
     AD.nao_nulo,
     AD.nom_atributo,
     CASE TA.cod_tipo
         WHEN 4 THEN  administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,'''''''''''''''')
     END AS valor_padrao,
     CASE TA.cod_tipo
       WHEN 3 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,''''''''''''''''))
       WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,VALOR.valor))
         ELSE         null
     END AS valor_padrao_desc,
     CASE TA.cod_tipo WHEN
         4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         null
     END AS valor_desc,
     AD.ajuda,
     AD.mascara,
     TA.cod_tipo,
     TA.nom_tipo,
     VALOR.valor,
     VALOR.timestamp
  FROM
     administracao.atributo_dinamico          AS AD,
     administracao.tipo_atributo              AS TA,
     administracao.atributo_dinamico AS ACA
     LEFT JOIN
     imobiliario.atributo_imovel_valor         AS VALOR
  ON ( ACA.cod_atributo = VALOR.cod_atributo
          AND ACA.cod_cadastro = VALOR.cod_cadastro
              AND VALOR.inscricao_municipal = ''''||inInscricaoMunicipal||''''
 AND ACA.cod_atributo = 112
         AND CAST(VALOR.timestamp as varchar)||CAST(VALOR.cod_atributo as varchar) IN (
             SELECT
         (CAST(max(VALOR.timestamp) as varchar))::varchar||CAST(VALOR.cod_atributo as varchar)
             FROM
                administracao.atributo_dinamico AS ACA,
                imobiliario.atributo_imovel_valor         AS VALOR,
                administracao.atributo_dinamico          AS AD,
                administracao.tipo_atributo              AS TA
             WHERE
                ACA.cod_atributo = AD.cod_atributo
                AND ACA.cod_cadastro = AD.cod_cadastro
                AND ACA.cod_modulo   = AD.cod_modulo
             AND ACA.cod_atributo = VALOR.cod_atributo
             AND ACA.cod_cadastro = VALOR.cod_cadastro
             AND ACA.cod_modulo   = VALOR.cod_modulo
              AND VALOR.inscricao_municipal = ''''||inInscricaoMunicipal||''''
 AND ACA.cod_atributo = 112
             AND AD.cod_tipo = TA.cod_tipo
             AND ACA.ativo = true
             AND AD.cod_modulo   =12
             AND AD.cod_cadastro=4

             GROUP BY VALOR.cod_cadastro, VALOR.cod_atributo
                                  )

     )
  WHERE
      AD.cod_tipo = TA.cod_tipo
  AND ACA.ativo = true
  AND     AD.ativo
  AND AD.cod_atributo =  ACA.cod_atributo
  AND AD.cod_modulo   = ACA.cod_modulo
  AND AD.cod_cadastro = ACA.cod_cadastro
  AND ACA.cod_cadastro=4
  AND ACA.cod_modulo  =12


 AND ACA.cod_atributo = 112;'''';OPEN crCursor FOR EXECUTE stSql;
                                    FETCH crCursor INTO rsRetorno;
                                    CLOSE crCursor;
                                    RETURN rsRetorno.valor;
                                    END;
                                    '' LANGUAGE plpgsql;
             ';
    EXECUTE stSQL;

    stSQL := '
CREATE OR REPLACE FUNCTION recuperaCadastroImobiliarioImovelUtilizacao(  INTEGER ) RETURNS VARCHAR AS ''DECLARE stSql VARCHAR; crCursor  REFCURSOR; rsRetorno RECORD;inInscricaoMunicipal ALIAS FOR $1;BEGIN stSql := ''''  SELECT
     AD.cod_cadastro,
     AD.cod_atributo,
     AD.ativo,
     AD.nao_nulo,
     AD.nom_atributo,
     CASE TA.cod_tipo
         WHEN 4 THEN  administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,'''''''''''''''')
     END AS valor_padrao,
     CASE TA.cod_tipo
       WHEN 3 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,''''''''''''''''))
       WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,VALOR.valor))
         ELSE         null
     END AS valor_padrao_desc,
     CASE TA.cod_tipo WHEN
         4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         null
     END AS valor_desc,
     AD.ajuda,
     AD.mascara,
     TA.cod_tipo,
     TA.nom_tipo,
     VALOR.valor,
     VALOR.timestamp
  FROM
     administracao.atributo_dinamico          AS AD,
     administracao.tipo_atributo              AS TA,
     administracao.atributo_dinamico AS ACA
     LEFT JOIN
     imobiliario.atributo_imovel_valor         AS VALOR
  ON ( ACA.cod_atributo = VALOR.cod_atributo
          AND ACA.cod_cadastro = VALOR.cod_cadastro
              AND VALOR.inscricao_municipal = ''''||inInscricaoMunicipal||''''
 AND ACA.cod_atributo = 124
         AND CAST(VALOR.timestamp as varchar)||CAST(VALOR.cod_atributo as varchar) IN (
             SELECT
         (CAST(max(VALOR.timestamp) as varchar))::varchar||CAST(VALOR.cod_atributo as varchar)
             FROM
                administracao.atributo_dinamico AS ACA,
                imobiliario.atributo_imovel_valor         AS VALOR,
                administracao.atributo_dinamico          AS AD,
                administracao.tipo_atributo              AS TA
             WHERE
                ACA.cod_atributo = AD.cod_atributo
                AND ACA.cod_cadastro = AD.cod_cadastro
                AND ACA.cod_modulo   = AD.cod_modulo
             AND ACA.cod_atributo = VALOR.cod_atributo
             AND ACA.cod_cadastro = VALOR.cod_cadastro
             AND ACA.cod_modulo   = VALOR.cod_modulo
              AND VALOR.inscricao_municipal = ''''||inInscricaoMunicipal||''''
 AND ACA.cod_atributo = 124
             AND AD.cod_tipo = TA.cod_tipo
             AND ACA.ativo = true
             AND AD.cod_modulo   =12
             AND AD.cod_cadastro=4

             GROUP BY VALOR.cod_cadastro, VALOR.cod_atributo
                                  )

     )
  WHERE
      AD.cod_tipo = TA.cod_tipo
  AND ACA.ativo = true
  AND     AD.ativo
  AND AD.cod_atributo =  ACA.cod_atributo
  AND AD.cod_modulo   = ACA.cod_modulo
  AND AD.cod_cadastro = ACA.cod_cadastro
  AND ACA.cod_cadastro=4
  AND ACA.cod_modulo  =12


 AND ACA.cod_atributo = 124;'''';OPEN crCursor FOR EXECUTE stSql;
                                    FETCH crCursor INTO rsRetorno;
                                    CLOSE crCursor;
                                    RETURN rsRetorno.valor;
                                    END;
                                    '' LANGUAGE plpgsql;
             ';
    EXECUTE stSQL;

    stSQL := '
CREATE OR REPLACE FUNCTION recuperaCadastroImobiliarioImovelLeiMun6372010remissao(  INTEGER ) RETURNS VARCHAR AS ''DECLARE stSql VARCHAR; crCursor  REFCURSOR; rsRetorno RECORD;inInscricaoMunicipal ALIAS FOR $1;BEGIN stSql := ''''  SELECT
     AD.cod_cadastro,
     AD.cod_atributo,
     AD.ativo,
     AD.nao_nulo,
     AD.nom_atributo,
     CASE TA.cod_tipo
         WHEN 4 THEN  administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,'''''''''''''''')
     END AS valor_padrao,
     CASE TA.cod_tipo
       WHEN 3 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,''''''''''''''''))
       WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,VALOR.valor))
         ELSE         null
     END AS valor_padrao_desc,
     CASE TA.cod_tipo WHEN
         4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         null
     END AS valor_desc,
     AD.ajuda,
     AD.mascara,
     TA.cod_tipo,
     TA.nom_tipo,
     VALOR.valor,
     VALOR.timestamp
  FROM
     administracao.atributo_dinamico          AS AD,
     administracao.tipo_atributo              AS TA,
     administracao.atributo_dinamico AS ACA
     LEFT JOIN
     imobiliario.atributo_imovel_valor         AS VALOR
  ON ( ACA.cod_atributo = VALOR.cod_atributo
          AND ACA.cod_cadastro = VALOR.cod_cadastro
              AND VALOR.inscricao_municipal = ''''||inInscricaoMunicipal||''''
 AND ACA.cod_atributo = 5034
         AND CAST(VALOR.timestamp as varchar)||CAST(VALOR.cod_atributo as varchar) IN (
             SELECT
         (CAST(max(VALOR.timestamp) as varchar))::varchar||CAST(VALOR.cod_atributo as varchar)
             FROM
                administracao.atributo_dinamico AS ACA,
                imobiliario.atributo_imovel_valor         AS VALOR,
                administracao.atributo_dinamico          AS AD,
                administracao.tipo_atributo              AS TA
             WHERE
                ACA.cod_atributo = AD.cod_atributo
                AND ACA.cod_cadastro = AD.cod_cadastro
                AND ACA.cod_modulo   = AD.cod_modulo
             AND ACA.cod_atributo = VALOR.cod_atributo
             AND ACA.cod_cadastro = VALOR.cod_cadastro
             AND ACA.cod_modulo   = VALOR.cod_modulo
              AND VALOR.inscricao_municipal = ''''||inInscricaoMunicipal||''''
 AND ACA.cod_atributo = 5034
             AND AD.cod_tipo = TA.cod_tipo
             AND ACA.ativo = true
             AND AD.cod_modulo   =12
             AND AD.cod_cadastro=4

             GROUP BY VALOR.cod_cadastro, VALOR.cod_atributo
                                  )

     )
  WHERE
      AD.cod_tipo = TA.cod_tipo
  AND ACA.ativo = true
  AND     AD.ativo
  AND AD.cod_atributo =  ACA.cod_atributo
  AND AD.cod_modulo   = ACA.cod_modulo
  AND AD.cod_cadastro = ACA.cod_cadastro
  AND ACA.cod_cadastro=4
  AND ACA.cod_modulo  =12


 AND ACA.cod_atributo = 5034;'''';OPEN crCursor FOR EXECUTE stSql;
                                    FETCH crCursor INTO rsRetorno;
                                    CLOSE crCursor;
                                    RETURN rsRetorno.valor;
                                    END;
                                    '' LANGUAGE plpgsql;
             ';
    EXECUTE stSQL;

    stSQL := '
CREATE OR REPLACE FUNCTION recuperaCadastroImobiliarioImovelISENTODOIMPOSTO(  INTEGER ) RETURNS VARCHAR AS ''DECLARE stSql VARCHAR; crCursor  REFCURSOR; rsRetorno RECORD;inInscricaoMunicipal ALIAS FOR $1;BEGIN stSql := ''''  SELECT
     AD.cod_cadastro,
     AD.cod_atributo,
     AD.ativo,
     AD.nao_nulo,
     AD.nom_atributo,
     CASE TA.cod_tipo
         WHEN 4 THEN  administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,'''''''''''''''')
     END AS valor_padrao,
     CASE TA.cod_tipo
       WHEN 3 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,''''''''''''''''))
       WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,VALOR.valor))
         ELSE         null
     END AS valor_padrao_desc,
     CASE TA.cod_tipo WHEN
         4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         null
     END AS valor_desc,
     AD.ajuda,
     AD.mascara,
     TA.cod_tipo,
     TA.nom_tipo,
     VALOR.valor,
     VALOR.timestamp
  FROM
     administracao.atributo_dinamico          AS AD,
     administracao.tipo_atributo              AS TA,
     administracao.atributo_dinamico AS ACA
     LEFT JOIN
     imobiliario.atributo_imovel_valor         AS VALOR
  ON ( ACA.cod_atributo = VALOR.cod_atributo
          AND ACA.cod_cadastro = VALOR.cod_cadastro
              AND VALOR.inscricao_municipal = ''''||inInscricaoMunicipal||''''
 AND ACA.cod_atributo = 111
         AND CAST(VALOR.timestamp as varchar)||CAST(VALOR.cod_atributo as varchar) IN (
             SELECT
         (CAST(max(VALOR.timestamp) as varchar))::varchar||CAST(VALOR.cod_atributo as varchar)
             FROM
                administracao.atributo_dinamico AS ACA,
                imobiliario.atributo_imovel_valor         AS VALOR,
                administracao.atributo_dinamico          AS AD,
                administracao.tipo_atributo              AS TA
             WHERE
                ACA.cod_atributo = AD.cod_atributo
                AND ACA.cod_cadastro = AD.cod_cadastro
                AND ACA.cod_modulo   = AD.cod_modulo
             AND ACA.cod_atributo = VALOR.cod_atributo
             AND ACA.cod_cadastro = VALOR.cod_cadastro
             AND ACA.cod_modulo   = VALOR.cod_modulo
              AND VALOR.inscricao_municipal = ''''||inInscricaoMunicipal||''''
 AND ACA.cod_atributo = 111
             AND AD.cod_tipo = TA.cod_tipo
             AND ACA.ativo = true
             AND AD.cod_modulo   =12
             AND AD.cod_cadastro=4

             GROUP BY VALOR.cod_cadastro, VALOR.cod_atributo
                                  )

     )
  WHERE
      AD.cod_tipo = TA.cod_tipo
  AND ACA.ativo = true
  AND     AD.ativo
  AND AD.cod_atributo =  ACA.cod_atributo
  AND AD.cod_modulo   = ACA.cod_modulo
  AND AD.cod_cadastro = ACA.cod_cadastro
  AND ACA.cod_cadastro=4
  AND ACA.cod_modulo  =12


 AND ACA.cod_atributo = 111;'''';OPEN crCursor FOR EXECUTE stSql;
                                    FETCH crCursor INTO rsRetorno;
                                    CLOSE crCursor;
                                    RETURN rsRetorno.valor;
                                    END;
                                    '' LANGUAGE plpgsql;
                 ';
    EXECUTE stSQL;

-- TaxaLixo
UPDATE administracao.corpo_funcao_externa SET linha = ' SE   #stTemp  =  "1" ENTAO               ' WHERE cod_modulo = 12 AND cod_biblioteca = 2 AND cod_funcao = 13 AND cod_linha = 23 ;
UPDATE administracao.corpo_funcao_externa SET linha = ' SE     #stUtilizacao  =   "1" ENTAO      ' WHERE cod_modulo = 12 AND cod_biblioteca = 2 AND cod_funcao = 13 AND cod_linha =  7 ;
UPDATE administracao.corpo_funcao_externa SET linha = ' SE   #stUtilizacao  =  "2" ENTAO         ' WHERE cod_modulo = 12 AND cod_biblioteca = 2 AND cod_funcao = 13 AND cod_linha = 11 ;
UPDATE administracao.corpo_funcao_externa SET linha = ' SE   #stUtilizacao  =  "3" ENTAO         ' WHERE cod_modulo = 12 AND cod_biblioteca = 2 AND cod_funcao = 13 AND cod_linha = 15 ;
UPDATE administracao.funcao_externa SET     cod_modulo= 12,    cod_biblioteca= 2,    cod_funcao= 13,    comentario=
    'Taxa de LIxo',    corpo_pl=
    'FUNCTION  TaxaLixo() RETURNS NUMERIC as ''
DECLARE

  boElse BOOLEAN;
  inExercicio INTEGER;
  inImovel INTEGER;
  inTipoImovel INTEGER;
  nuAliquota NUMERIC;
  nuTaxa NUMERIC;
  nuUrm NUMERIC := 85.51;
  nuUrm2007 NUMERIC := 88.79;
  nuURMAnual NUMERIC;
  stTemp VARCHAR := '''''''';
  stUtilizacao VARCHAR := '''''''';
BEGIN
inExercicio := RecuperarBufferInteiro(  ''''inExercicio''''  );
inImovel := RecuperarBufferInteiro(  ''''inRegistro''''  );
stUtilizacao := recuperaCadastroImobiliarioImovelUtilizacao(  inImovel  );
stTemp := recuperaCadastroImobiliarioImovelTIPODEIMOVEL(  inImovel  );
nuURMAnual := monetario.buscaValorAcrescimo(  1 , 1 , 1 , 2010  );
boElse := TRUE;
     IF     stUtilizacao  =   ''''1'''' THEN
    boElse := FALSE;
    nuAliquota := 0.6;
END IF;
     IF   stUtilizacao  =  ''''2'''' THEN
    boElse := FALSE;
    nuAliquota := 1.5;
END IF;
     IF   stUtilizacao  =  ''''3'''' THEN
    boElse := FALSE;
    nuAliquota := 3;
END IF;
IF   boElse  =  TRUE THEN
    nuAliquota := 2;
END IF;
nuTaxa := nuURMAnual*nuAliquota ;
     IF   stTemp  =  ''''1'''' THEN
    nuTaxa := 0.00;
END IF;
RETURN nuTaxa;
END;
 '' LANGUAGE ''plpgsql'';
',    corpo_ln=
    '' WHERE cod_modulo = 12 AND cod_biblioteca = 2 AND cod_funcao = 13 ;

-- TaxaLixo2011
UPDATE administracao.corpo_funcao_externa SET linha = ' SE   #stTemp  =  "1" ENTAO               ' WHERE cod_modulo = 12 AND cod_biblioteca = 2 AND cod_funcao = 55 AND cod_linha = 23 ;
UPDATE administracao.corpo_funcao_externa SET linha = ' SE     #stUtilizacao  =   "1" ENTAO      ' WHERE cod_modulo = 12 AND cod_biblioteca = 2 AND cod_funcao = 55 AND cod_linha =  7 ;
UPDATE administracao.corpo_funcao_externa SET linha = ' SE   #stUtilizacao  =  "2" ENTAO         ' WHERE cod_modulo = 12 AND cod_biblioteca = 2 AND cod_funcao = 55 AND cod_linha = 11 ;
UPDATE administracao.corpo_funcao_externa SET linha = ' SE   #stUtilizacao  =  "3" ENTAO         ' WHERE cod_modulo = 12 AND cod_biblioteca = 2 AND cod_funcao = 55 AND cod_linha = 15 ;
UPDATE administracao.funcao_externa SET     cod_modulo= 12,    cod_biblioteca= 2,    cod_funcao= 55,    comentario=
    'Taxa de LIxo',    corpo_pl=
    'FUNCTION  TaxaLixo2011() RETURNS NUMERIC as ''
DECLARE

  boElse BOOLEAN;
  inExercicio INTEGER;
  inImovel INTEGER;
  inTipoImovel INTEGER;
  nuAliquota NUMERIC;
  nuTaxa NUMERIC;
  nuUrm NUMERIC := 85.51;
  nuUrm2007 NUMERIC := 88.79;
  nuURMAnual NUMERIC;
  stTemp VARCHAR := '''''''';
  stUtilizacao VARCHAR := '''''''';
BEGIN
inExercicio := RecuperarBufferInteiro(  ''''inExercicio''''  );
inImovel := RecuperarBufferInteiro(  ''''inRegistro''''  );
stUtilizacao := recuperaCadastroImobiliarioImovelUtilizacao(  inImovel  );
stTemp := recuperaCadastroImobiliarioImovelTIPODEIMOVEL(  inImovel  );
nuURMAnual := monetario.buscaValorAcrescimo(  1 , 1 , 1 , 2011  );
boElse := TRUE;
     IF     stUtilizacao  =   ''''1'''' THEN
    boElse := FALSE;
    nuAliquota := 0.6;
END IF;
     IF   stUtilizacao  =  ''''2'''' THEN
    boElse := FALSE;
    nuAliquota := 1.5;
END IF;
     IF   stUtilizacao  =  ''''3'''' THEN
    boElse := FALSE;
    nuAliquota := 3;
END IF;
IF   boElse  =  TRUE THEN
    nuAliquota := 2;
END IF;
nuTaxa := nuURMAnual*nuAliquota ;
     IF   stTemp  =  ''''1'''' THEN
    nuTaxa := 0.00;
END IF;
RETURN nuTaxa;
END;
 '' LANGUAGE ''plpgsql'';
',    corpo_ln=
    '' WHERE cod_modulo = 12 AND cod_biblioteca = 2 AND cod_funcao = 55 ;

-- TaxaLixo2012
UPDATE administracao.corpo_funcao_externa SET linha = ' SE   #stTemp  =  "1" ENTAO               ' WHERE cod_modulo = 12 AND cod_biblioteca = 2 AND cod_funcao = 57 AND cod_linha = 23 ;
UPDATE administracao.corpo_funcao_externa SET linha = ' SE     #stUtilizacao  =   "1" ENTAO      ' WHERE cod_modulo = 12 AND cod_biblioteca = 2 AND cod_funcao = 57 AND cod_linha =  7 ;
UPDATE administracao.corpo_funcao_externa SET linha = ' SE   #stUtilizacao  =  "2" ENTAO         ' WHERE cod_modulo = 12 AND cod_biblioteca = 2 AND cod_funcao = 57 AND cod_linha = 11 ;
UPDATE administracao.corpo_funcao_externa SET linha = ' SE   #stUtilizacao  =  "3" ENTAO         ' WHERE cod_modulo = 12 AND cod_biblioteca = 2 AND cod_funcao = 57 AND cod_linha = 15 ;
UPDATE administracao.funcao_externa SET     cod_modulo= 12,    cod_biblioteca= 2,    cod_funcao= 57,    comentario=
    'Taxa de LIxo',    corpo_pl=
    'FUNCTION  TaxaLixo2012() RETURNS NUMERIC as ''
DECLARE

  boElse BOOLEAN;
  inExercicio INTEGER;
  inImovel INTEGER;
  inTipoImovel INTEGER;
  nuAliquota NUMERIC;
  nuTaxa NUMERIC;
  nuUrm NUMERIC := 85.51;
  nuUrm2007 NUMERIC := 88.79;
  nuURMAnual NUMERIC;
  stTemp VARCHAR := '''''''';
  stUtilizacao VARCHAR := '''''''';
BEGIN
inExercicio := RecuperarBufferInteiro(  ''''inExercicio''''  );
inImovel := RecuperarBufferInteiro(  ''''inRegistro''''  );
stUtilizacao := recuperaCadastroImobiliarioImovelUtilizacao(  inImovel  );
stTemp := recuperaCadastroImobiliarioImovelTIPODEIMOVEL(  inImovel  );
nuURMAnual := monetario.buscaValorAcrescimo(  1 , 1 , 1 , 2012  );
boElse := TRUE;
     IF     stUtilizacao  =   ''''1'''' THEN
    boElse := FALSE;
    nuAliquota := 0.6;
END IF;
     IF   stUtilizacao  =  ''''2'''' THEN
    boElse := FALSE;
    nuAliquota := 1.5;
END IF;
     IF   stUtilizacao  =  ''''3'''' THEN
    boElse := FALSE;
    nuAliquota := 3;
END IF;
IF   boElse  =  TRUE THEN
    nuAliquota := 2;
END IF;
nuTaxa := nuURMAnual*nuAliquota ;
     IF   stTemp  =  ''''1'''' THEN
    nuTaxa := 0.00;
END IF;
RETURN nuTaxa;
END;
 '' LANGUAGE ''plpgsql'';
',    corpo_ln=
    '' WHERE cod_modulo = 12 AND cod_biblioteca = 2 AND cod_funcao = 57 ;

-- TaxaLixo2013
UPDATE administracao.corpo_funcao_externa SET linha = ' SE   #stTemp  =  "1" ENTAO               ' WHERE cod_modulo = 12 AND cod_biblioteca = 2 AND cod_funcao = 59 AND cod_linha = 23 ;
UPDATE administracao.corpo_funcao_externa SET linha = ' SE     #stUtilizacao  =   "1" ENTAO      ' WHERE cod_modulo = 12 AND cod_biblioteca = 2 AND cod_funcao = 59 AND cod_linha =  7 ;
UPDATE administracao.corpo_funcao_externa SET linha = ' SE   #stUtilizacao  =  "2" ENTAO         ' WHERE cod_modulo = 12 AND cod_biblioteca = 2 AND cod_funcao = 59 AND cod_linha = 11 ;
UPDATE administracao.corpo_funcao_externa SET linha = ' SE   #stUtilizacao  =  "3" ENTAO         ' WHERE cod_modulo = 12 AND cod_biblioteca = 2 AND cod_funcao = 59 AND cod_linha = 15 ;
UPDATE administracao.funcao_externa SET     cod_modulo= 12,    cod_biblioteca= 2,    cod_funcao= 59,    comentario=
    'Taxa de LIxo',    corpo_pl=
    'FUNCTION  TaxaLixo2013() RETURNS NUMERIC as ''
DECLARE

  boElse BOOLEAN;
  inExercicio INTEGER;
  inImovel INTEGER;
  inTipoImovel INTEGER;
  nuAliquota NUMERIC;
  nuTaxa NUMERIC;
  nuUrm NUMERIC := 85.51;
  nuUrm2007 NUMERIC := 88.79;
  nuURMAnual NUMERIC;
  stTemp VARCHAR := '''''''';
  stUtilizacao VARCHAR := '''''''';
BEGIN
inExercicio := RecuperarBufferInteiro(  ''''inExercicio''''  );
inImovel := RecuperarBufferInteiro(  ''''inRegistro''''  );
stUtilizacao := recuperaCadastroImobiliarioImovelUtilizacao(  inImovel  );
stTemp := recuperaCadastroImobiliarioImovelTIPODEIMOVEL(  inImovel  );
nuURMAnual := monetario.buscaValorAcrescimo(  1 , 1 , 1 , 2013  );
boElse := TRUE;
     IF     stUtilizacao  =   ''''1'''' THEN
    boElse := FALSE;
    nuAliquota := 0.6;
END IF;
     IF   stUtilizacao  =  ''''2'''' THEN
    boElse := FALSE;
    nuAliquota := 1.5;
END IF;
     IF   stUtilizacao  =  ''''3'''' THEN
    boElse := FALSE;
    nuAliquota := 3;
END IF;
IF   boElse  =  TRUE THEN
    nuAliquota := 2;
END IF;
nuTaxa := nuURMAnual*nuAliquota ;
     IF   stTemp  =  ''''1'''' THEN
    nuTaxa := 0.00;
END IF;
RETURN nuTaxa;
END;
 '' LANGUAGE ''plpgsql'';
',    corpo_ln=
    '' WHERE cod_modulo = 12 AND cod_biblioteca = 2 AND cod_funcao = 59 ;

-- calculaiptu
UPDATE administracao.corpo_funcao_externa SET linha = ' SE       #stUtiliza  =   "1" ENTAO       ' WHERE cod_modulo = 12 AND cod_biblioteca = 2 AND cod_funcao =  6 AND cod_linha = 10 ;
UPDATE administracao.corpo_funcao_externa SET linha = ' SE   #stUtiliza  =  "2" ENTAO            ' WHERE cod_modulo = 12 AND cod_biblioteca = 2 AND cod_funcao =  6 AND cod_linha = 18 ;
UPDATE administracao.corpo_funcao_externa SET linha = ' SE   #stUtiliza  =  "3" ENTAO            ' WHERE cod_modulo = 12 AND cod_biblioteca = 2 AND cod_funcao =  6 AND cod_linha = 22 ;
UPDATE administracao.corpo_funcao_externa SET linha = ' SE   #stUtiliza  =  "4" ENTAO            ' WHERE cod_modulo = 12 AND cod_biblioteca = 2 AND cod_funcao =  6 AND cod_linha = 26 ;
UPDATE administracao.funcao_externa SET     cod_modulo= 12,    cod_biblioteca= 2,    cod_funcao= 6,    comentario=
    '',    corpo_pl=
    'FUNCTION  calculaiptu() RETURNS NUMERIC as ''
DECLARE

  boElse BOOLEAN;
  boVenal BOOLEAN;
  inExercicio INTEGER;
  inImovel INTEGER;
  nuAliquota NUMERIC;
  nuRetorno NUMERIC;
  nuUrm NUMERIC := 85.51;
  nuUrm2007 NUMERIC := 88.79;
  nuValorVenal NUMERIC;
  nuVV2005 NUMERIC;
  nuVV2006 NUMERIC;
  stAliquota VARCHAR := '''''''';
  stUtiliza VARCHAR := '''''''';
  stValorVenal VARCHAR := '''''''';
BEGIN
inExercicio := RecuperarBufferInteiro(  ''''inExercicio''''  );
inImovel := RecuperarBufferInteiro(  ''''inRegistro''''  );
boElse := TRUE;
nuVV2005 := arrecadacao.fn_ultimo_venal_por_im(  inImovel , ''''2006''''  );
nuVV2006 := nuVV2005*0.0121+(nuVV2005 );
nuValorVenal := nuVV2006*0.0383+(nuVV2006 );
boVenal := arrecadacao.fn_grava_venal(  inImovel , 0.00 , 0.00 , nuValorVenal , inExercicio  );
stUtiliza := recuperaCadastroImobiliarioImovelUtilizacao(  inImovel  );
nuUrm := nuUrm2007*100;
     IF       stUtiliza  =   ''''1'''' THEN
    boElse := FALSE;
    IF   nuValorVenal  <=  nuUrm THEN
        nuAliquota := 0.008;
    ELSE
        nuAliquota := 0.009;
    END IF;
END IF;
     IF   stUtiliza  =  ''''2'''' THEN
    boElse := FALSE;
    nuAliquota := 0.015;
END IF;
     IF   stUtiliza  =  ''''3'''' THEN
    boElse := FALSE;
    nuAliquota := 0.012;
END IF;
     IF   stUtiliza  =  ''''4'''' THEN
    boElse := FALSE;
    nuAliquota := 0.02;
END IF;
IF   boElse  =  TRUE THEN
    IF   nuValorVenal  <=  nuUrm THEN
        nuAliquota := 0.008;
    ELSE
        nuAliquota := 0.01;
    END IF;
END IF;
nuValorVenal := nuValorVenal*nuAliquota ;
RETURN nuValorVenal;
END;
 '' LANGUAGE ''plpgsql'';
',    corpo_ln=
    '' WHERE cod_modulo = 12 AND cod_biblioteca = 2 AND cod_funcao = 6 ;

-- bkp_calculaiptu
UPDATE administracao.corpo_funcao_externa SET linha = ' SE       #stUtiliza  =   "1" ENTAO       ' WHERE cod_modulo = 12 AND cod_biblioteca = 2 AND cod_funcao = 42 AND cod_linha =  8 ;
UPDATE administracao.corpo_funcao_externa SET linha = ' SE   #stUtiliza  =  "2" ENTAO            ' WHERE cod_modulo = 12 AND cod_biblioteca = 2 AND cod_funcao = 42 AND cod_linha = 16 ;
UPDATE administracao.corpo_funcao_externa SET linha = ' SE   #stUtiliza  =  "3" ENTAO            ' WHERE cod_modulo = 12 AND cod_biblioteca = 2 AND cod_funcao = 42 AND cod_linha = 20 ;
UPDATE administracao.corpo_funcao_externa SET linha = ' SE   #stUtiliza  =  "4" ENTAO            ' WHERE cod_modulo = 12 AND cod_biblioteca = 2 AND cod_funcao = 42 AND cod_linha = 24 ;
UPDATE administracao.funcao_externa SET     cod_modulo= 12,    cod_biblioteca= 2,    cod_funcao= 42,    comentario=
    '',    corpo_pl=
    'FUNCTION  bkp_calculaiptu() RETURNS NUMERIC as ''
DECLARE

  boElse BOOLEAN;
  boVenal BOOLEAN;
  inExercicio INTEGER;
  inImovel INTEGER;
  nuAliquota NUMERIC;
  nuRetorno NUMERIC;
  nuUrm NUMERIC := 85.51;
  nuValorVenal NUMERIC;
  stAliquota VARCHAR := '''''''';
  stUtiliza VARCHAR := '''''''';
  stValorVenal VARCHAR := '''''''';
BEGIN
inExercicio := RecuperarBufferInteiro(  ''''inExercicio''''  );
inImovel := RecuperarBufferInteiro(  ''''inRegistro''''  );
boElse := TRUE;
nuValorVenal := arrecadacao.fn_ultimo_venal_por_im(  inImovel , ''''2006''''  );
boVenal := arrecadacao.fn_grava_venal(  inImovel , 0.00 , 0.00 , nuValorVenal , inExercicio  );
stUtiliza := recuperaCadastroImobiliarioImovelUtilizacao(  inImovel  );
nuUrm := nuUrm*100;
     IF       stUtiliza  =   ''''1'''' THEN
    boElse := FALSE;
    IF   nuValorVenal  <=  nuUrm THEN
        nuAliquota := 0.008;
    ELSE
        nuAliquota := 0.01;
    END IF;
END IF;
     IF   stUtiliza  =  ''''2'''' THEN
    boElse := FALSE;
    nuAliquota := 0.015;
END IF;
     IF   stUtiliza  =  ''''3'''' THEN
    boElse := FALSE;
    nuAliquota := 0.012;
END IF;
     IF   stUtiliza  =  ''''4'''' THEN
    boElse := FALSE;
    nuAliquota := 0.02;
END IF;
IF   boElse  =  TRUE THEN
    IF   nuValorVenal  <=  nuUrm THEN
        nuAliquota := 0.008;
    ELSE
        nuAliquota := 0.01;
    END IF;
END IF;
nuValorVenal := nuValorVenal*nuAliquota ;
RETURN nuValorVenal;
END;
 '' LANGUAGE ''plpgsql'';
',    corpo_ln=
    '' WHERE cod_modulo = 12 AND cod_biblioteca = 2 AND cod_funcao = 42 ;

-- calculaIPTUmariana
UPDATE administracao.corpo_funcao_externa SET linha = ' SE   #stUtiliza  =  "1" ENTAO            ' WHERE cod_modulo = 12 AND cod_biblioteca = 2 AND cod_funcao = 52 AND cod_linha = 28 ;
UPDATE administracao.corpo_funcao_externa SET linha = ' SE     #stUtiliza  =  "2" ENTAO          ' WHERE cod_modulo = 12 AND cod_biblioteca = 2 AND cod_funcao = 52 AND cod_linha = 36 ;
UPDATE administracao.corpo_funcao_externa SET linha = ' SE   #stUtiliza  =  "3" ENTAO            ' WHERE cod_modulo = 12 AND cod_biblioteca = 2 AND cod_funcao = 52 AND cod_linha = 40 ;
UPDATE administracao.corpo_funcao_externa SET linha = ' SE   #stUtiliza  =  "4" ENTAO            ' WHERE cod_modulo = 12 AND cod_biblioteca = 2 AND cod_funcao = 52 AND cod_linha = 44 ;
UPDATE administracao.funcao_externa SET     cod_modulo= 12,    cod_biblioteca= 2,    cod_funcao= 52,    comentario=
    'Cálculo IPTU Mariana 2010',    corpo_pl=
    'FUNCTION  calculaIPTUmariana() RETURNS NUMERIC as ''
DECLARE

  boEdificacao BOOLEAN;
  boElse BOOLEAN;
  boVenal BOOLEAN;
  dtInscricao DATE;
  inCodAcrescimo INTEGER;
  inCodCredito INTEGER;
  inCodEspecie INTEGER;
  inCodGenero INTEGER;
  inCodNatureza INTEGER;
  inExercicio INTEGER;
  inImovel INTEGER;
  nuAliquota NUMERIC;
  nuAliquotaDeprec NUMERIC;
  nuImposto NUMERIC;
  nuRetorno NUMERIC;
  nuURM NUMERIC;
  nuURM100 NUMERIC;
  nuURM2008 NUMERIC;
  nuURMAnual NUMERIC;
  nuValorVenal NUMERIC;
  nuVenalOld NUMERIC;
  nuVlrVenalAtual NUMERIC;
  stUtiliza VARCHAR := '''''''';
BEGIN
inExercicio := RecuperarBufferInteiro(  ''''inExercicio''''  );
nuAliquotaDeprec := 0;
inImovel := RecuperarBufferInteiro(  ''''inRegistro''''  );
boElse := TRUE;
nuVenalOld := arrecadacao.fn_ultimo_venal_por_im(  inImovel , ''''2009''''  );
nuURM2008 := monetario.buscaValorAcrescimo(  1 , 1 , 1 , 2009  );
nuURMAnual := monetario.buscaValorAcrescimo(  1 , 1 , 1 , 2010  );
stUtiliza := recuperaCadastroImobiliarioImovelUtilizacao(  inImovel  );
dtInscricao := imobiliario.fn_buscaDataConstrucaoImovel(  inImovel  );
boEdificacao := arrecadacao.verificaEdificacaoImovel(  inImovel  );
nuURM := (nuURMAnual /nuURM2008 )-1;
nuURM100 := nuURMAnual*100;
nuVlrVenalAtual := (nuVenalOld *nuURM )+nuVenalOld ;
IF   boEdificacao  =  ''''true'''' THEN
    IF   dtInscricao  >=  ''''2005-01-01''''  AND  dtInscricao  <=  ''''2005-12-31'''' THEN
        nuAliquotaDeprec := 0.02;
    END IF;
    IF   dtInscricao  >=  ''''2006-01-01''''  AND  dtInscricao  <=  ''''2006-12-31'''' THEN
        nuAliquotaDeprec := 0.01;
    END IF;
    IF     dtInscricao  <=  ''''2004-12-31'''' THEN
        nuAliquotaDeprec := 0.03;
    END IF;
    nuValorVenal := nuVlrVenalAtual-(nuVlrVenalAtual *nuAliquotaDeprec );
ELSE
    nuValorVenal := nuVlrVenalAtual;
END IF;
     IF   stUtiliza  =  ''''1'''' THEN
    boElse := FALSE;
    IF   nuValorVenal  <=  nuURM100 THEN
        nuAliquota := 0.008;
    ELSE
        nuAliquota := 0.009;
    END IF;
END IF;
     IF     stUtiliza  =  ''''2'''' THEN
    boElse := FALSE;
    nuAliquota := 0.015;
END IF;
     IF   stUtiliza  =  ''''3'''' THEN
    boElse := FALSE;
    nuAliquota := 0.012;
END IF;
     IF   stUtiliza  =  ''''4'''' THEN
    boElse := FALSE;
    nuAliquota := 0.01;
END IF;
IF   boElse  =  TRUE THEN
    nuAliquota := 0.02;
END IF;
nuImposto := nuValorVenal*nuAliquota ;
boVenal := arrecadacao.fn_grava_venal(  inImovel , 0.00 , 0.00 , nuValorVenal , inExercicio  );
RETURN nuImposto;
END;
 '' LANGUAGE ''plpgsql'';
',    corpo_ln=
    '' WHERE cod_modulo = 12 AND cod_biblioteca = 2 AND cod_funcao = 52 ;

-- calculaIPTUmariana2011
UPDATE administracao.corpo_funcao_externa SET linha = ' SE   #stUtiliza  =  "1" ENTAO            ' WHERE cod_modulo = 12 AND cod_biblioteca = 2 AND cod_funcao = 54 AND cod_linha = 28 ;
UPDATE administracao.corpo_funcao_externa SET linha = ' SE     #stUtiliza  =  "2" ENTAO          ' WHERE cod_modulo = 12 AND cod_biblioteca = 2 AND cod_funcao = 54 AND cod_linha = 36 ;
UPDATE administracao.corpo_funcao_externa SET linha = ' SE   #stUtiliza  =  "3" ENTAO            ' WHERE cod_modulo = 12 AND cod_biblioteca = 2 AND cod_funcao = 54 AND cod_linha = 40 ;
UPDATE administracao.corpo_funcao_externa SET linha = ' SE   #stUtiliza  =  "4" ENTAO            ' WHERE cod_modulo = 12 AND cod_biblioteca = 2 AND cod_funcao = 54 AND cod_linha = 44 ;
UPDATE administracao.funcao_externa SET     cod_modulo= 12,    cod_biblioteca= 2,    cod_funcao= 54,    comentario=
    'Cálculo IPTU Mariana 2011',    corpo_pl=
    'FUNCTION  calculaIPTUmariana2011() RETURNS NUMERIC as ''
DECLARE

  boEdificacao BOOLEAN;
  boElse BOOLEAN;
  boVenal BOOLEAN;
  dtInscricao DATE;
  inCodAcrescimo INTEGER;
  inCodCredito INTEGER;
  inCodEspecie INTEGER;
  inCodGenero INTEGER;
  inCodNatureza INTEGER;
  inExercicio INTEGER;
  inExercicioAnterior INTEGER;
  inImovel INTEGER;
  nuAliquota NUMERIC;
  nuAliquotaDeprec NUMERIC;
  nuExercicioConstrucao NUMERIC;
  nuImposto NUMERIC;
  nuRetorno NUMERIC;
  nuURM NUMERIC;
  nuURM100 NUMERIC;
  nuURM2008 NUMERIC;
  nuURMAnual NUMERIC;
  nuValorVenal NUMERIC;
  nuVenalOld NUMERIC;
  nuVlrVenalAtual NUMERIC;
  stExercicioAnterior VARCHAR := '''''''';
  stUtiliza VARCHAR := '''''''';
BEGIN
nuAliquotaDeprec := 0;
boElse := TRUE;
inExercicio := RecuperarBufferInteiro(  ''''inExercicio''''  );
inImovel := RecuperarBufferInteiro(  ''''inRegistro''''  );
boEdificacao := arrecadacao.verificaEdificacaoImovel(  inImovel  );
stUtiliza := recuperaCadastroImobiliarioImovelUtilizacao(  inImovel  );
dtInscricao := imobiliario.fn_buscaDataConstrucaoImovel(  inImovel  );
nuExercicioConstrucao := recuperaExercicioConstrucaoImovel(  dtInscricao  );
inExercicioAnterior := inExercicio-1;
stExercicioAnterior := arrecadacao.fn_int2vc(  inExercicioAnterior  );
nuVenalOld := arrecadacao.fn_ultimo_venal_por_im(  inImovel , stExercicioAnterior  );
nuURM2008 := monetario.buscaValorAcrescimo(  1 , 1 , 1 , inExercicioAnterior  );
nuURMAnual := monetario.buscaValorAcrescimo(  1 , 1 , 1 , inExercicio  );
nuURM := (nuURMAnual /nuURM2008 )-1;
nuURM100 := nuURMAnual*100;
nuVlrVenalAtual := (nuVenalOld *nuURM )+nuVenalOld ;
boVenal := arrecadacao.fn_grava_venal(  inImovel , 0.00 , 0.00 , nuVlrVenalAtual , inExercicio  );
IF   boEdificacao  =  ''''true'''' THEN
    IF   nuExercicioConstrucao  >=  2005 THEN
        nuAliquotaDeprec := (inExercicio-(nuExercicioConstrucao+2))*0.01;
    ELSE
        nuAliquotaDeprec := (inExercicio -2006)*0.01;
    END IF;
    nuValorVenal := nuVlrVenalAtual-(nuVlrVenalAtual *nuAliquotaDeprec );
ELSE
    nuValorVenal := nuVlrVenalAtual;
END IF;
     IF   stUtiliza  =  ''''1'''' THEN
    boElse := FALSE;
    IF   nuValorVenal  <=  nuURM100 THEN
        nuAliquota := 0.008;
    ELSE
        nuAliquota := 0.009;
    END IF;
END IF;
     IF     stUtiliza  =  ''''2'''' THEN
    boElse := FALSE;
    nuAliquota := 0.015;
END IF;
     IF   stUtiliza  =  ''''3'''' THEN
    boElse := FALSE;
    nuAliquota := 0.012;
END IF;
     IF   stUtiliza  =  ''''4'''' THEN
    boElse := FALSE;
    nuAliquota := 0.01;
END IF;
IF   boElse  =  TRUE THEN
    nuAliquota := 0.02;
END IF;
nuImposto := nuValorVenal*nuAliquota ;
RETURN nuImposto;
END;
 '' LANGUAGE ''plpgsql'';
',    corpo_ln=
    '' WHERE cod_modulo = 12 AND cod_biblioteca = 2 AND cod_funcao = 54 ;

-- calculaIPTUmariana2012
UPDATE administracao.corpo_funcao_externa SET linha = ' SE   #stUtiliza  =  "1" ENTAO            ' WHERE cod_modulo = 12 AND cod_biblioteca = 2 AND cod_funcao = 56 AND cod_linha = 28 ;
UPDATE administracao.corpo_funcao_externa SET linha = ' SE     #stUtiliza  =  "2" ENTAO          ' WHERE cod_modulo = 12 AND cod_biblioteca = 2 AND cod_funcao = 56 AND cod_linha = 36 ;
UPDATE administracao.corpo_funcao_externa SET linha = ' SE   #stUtiliza  =  "3" ENTAO            ' WHERE cod_modulo = 12 AND cod_biblioteca = 2 AND cod_funcao = 56 AND cod_linha = 40 ;
UPDATE administracao.corpo_funcao_externa SET linha = ' SE   #stUtiliza  =  "4" ENTAO            ' WHERE cod_modulo = 12 AND cod_biblioteca = 2 AND cod_funcao = 56 AND cod_linha = 44 ;
UPDATE administracao.funcao_externa SET     cod_modulo= 12,    cod_biblioteca= 2,    cod_funcao= 56,    comentario=
    'Cálculo IPTU Mariana 2011',    corpo_pl=
    'FUNCTION  calculaIPTUmariana2012() RETURNS NUMERIC as ''
DECLARE

  boEdificacao BOOLEAN;
  boElse BOOLEAN;
  boVenal BOOLEAN;
  dtInscricao DATE;
  inCodAcrescimo INTEGER;
  inCodCredito INTEGER;
  inCodEspecie INTEGER;
  inCodGenero INTEGER;
  inCodNatureza INTEGER;
  inExercicio INTEGER;
  inExercicioAnterior INTEGER;
  inImovel INTEGER;
  nuAliquota NUMERIC;
  nuAliquotaDeprec NUMERIC;
  nuExercicioConstrucao NUMERIC;
  nuImposto NUMERIC;
  nuRetorno NUMERIC;
  nuURM NUMERIC;
  nuURM100 NUMERIC;
  nuURM2008 NUMERIC;
  nuURMAnual NUMERIC;
  nuValorVenal NUMERIC;
  nuVenalOld NUMERIC;
  nuVlrVenalAtual NUMERIC;
  stExercicioAnterior VARCHAR := '''''''';
  stUtiliza VARCHAR := '''''''';
BEGIN
nuAliquotaDeprec := 0;
boElse := TRUE;
inExercicio := RecuperarBufferInteiro(  ''''inExercicio''''  );
inImovel := RecuperarBufferInteiro(  ''''inRegistro''''  );
boEdificacao := arrecadacao.verificaEdificacaoImovel(  inImovel  );
stUtiliza := recuperaCadastroImobiliarioImovelUtilizacao(  inImovel  );
dtInscricao := imobiliario.fn_buscaDataConstrucaoImovel(  inImovel  );
nuExercicioConstrucao := recuperaExercicioConstrucaoImovel(  dtInscricao  );
inExercicioAnterior := inExercicio-1;
stExercicioAnterior := arrecadacao.fn_int2vc(  inExercicioAnterior  );
nuVenalOld := arrecadacao.fn_ultimo_venal_por_im(  inImovel , stExercicioAnterior  );
nuURM2008 := monetario.buscaValorAcrescimo(  1 , 1 , 1 , inExercicioAnterior  );
nuURMAnual := monetario.buscaValorAcrescimo(  1 , 1 , 1 , inExercicio  );
nuURM := (nuURMAnual /nuURM2008 )-1;
nuURM100 := nuURMAnual*100;
nuVlrVenalAtual := (nuVenalOld *nuURM )+nuVenalOld ;
boVenal := arrecadacao.fn_grava_venal(  inImovel , 0.00 , 0.00 , nuVlrVenalAtual , inExercicio  );
IF   boEdificacao  =  ''''true'''' THEN
    IF   nuExercicioConstrucao  >=  2005 THEN
        nuAliquotaDeprec := (inExercicio-(nuExercicioConstrucao+2))*0.01;
    ELSE
        nuAliquotaDeprec := (inExercicio -2006)*0.01;
    END IF;
    nuValorVenal := nuVlrVenalAtual-(nuVlrVenalAtual *nuAliquotaDeprec );
ELSE
    nuValorVenal := nuVlrVenalAtual;
END IF;
     IF   stUtiliza  =  ''''1'''' THEN
    boElse := FALSE;
    IF   nuValorVenal  <=  nuURM100 THEN
        nuAliquota := 0.008;
    ELSE
        nuAliquota := 0.009;
    END IF;
END IF;
     IF     stUtiliza  =  ''''2'''' THEN
    boElse := FALSE;
    nuAliquota := 0.015;
END IF;
     IF   stUtiliza  =  ''''3'''' THEN
    boElse := FALSE;
    nuAliquota := 0.012;
END IF;
     IF   stUtiliza  =  ''''4'''' THEN
    boElse := FALSE;
    nuAliquota := 0.01;
END IF;
IF   boElse  =  TRUE THEN
    nuAliquota := 0.02;
END IF;
nuImposto := nuValorVenal*nuAliquota ;
RETURN nuImposto;
END;
 '' LANGUAGE ''plpgsql'';
',    corpo_ln=
    '' WHERE cod_modulo = 12 AND cod_biblioteca = 2 AND cod_funcao = 56 ;

-- calculaIPTUmariana2013
UPDATE administracao.corpo_funcao_externa SET linha = ' SE   #stUtiliza  =  "1" ENTAO            ' WHERE cod_modulo = 12 AND cod_biblioteca = 2 AND cod_funcao = 58 AND cod_linha = 28 ;
UPDATE administracao.corpo_funcao_externa SET linha = ' SE     #stUtiliza  =  "2" ENTAO          ' WHERE cod_modulo = 12 AND cod_biblioteca = 2 AND cod_funcao = 58 AND cod_linha = 36 ;
UPDATE administracao.corpo_funcao_externa SET linha = ' SE   #stUtiliza  =  "3" ENTAO            ' WHERE cod_modulo = 12 AND cod_biblioteca = 2 AND cod_funcao = 58 AND cod_linha = 40 ;
UPDATE administracao.corpo_funcao_externa SET linha = ' SE   #stUtiliza  =  "4" ENTAO            ' WHERE cod_modulo = 12 AND cod_biblioteca = 2 AND cod_funcao = 58 AND cod_linha = 44 ;
UPDATE administracao.funcao_externa SET     cod_modulo= 12,    cod_biblioteca= 2,    cod_funcao= 58,    comentario=
    'Cálculo IPTU Mariana 2011',    corpo_pl=
    'FUNCTION  calculaIPTUmariana2013() RETURNS NUMERIC as ''
DECLARE

  boEdificacao BOOLEAN;
  boElse BOOLEAN;
  boVenal BOOLEAN;
  dtInscricao DATE;
  inCodAcrescimo INTEGER;
  inCodCredito INTEGER;
  inCodEspecie INTEGER;
  inCodGenero INTEGER;
  inCodNatureza INTEGER;
  inExercicio INTEGER;
  inExercicioAnterior INTEGER;
  inImovel INTEGER;
  nuAliquota NUMERIC;
  nuAliquotaDeprec NUMERIC;
  nuExercicioConstrucao NUMERIC;
  nuImposto NUMERIC;
  nuRetorno NUMERIC;
  nuURM NUMERIC;
  nuURM100 NUMERIC;
  nuURM2008 NUMERIC;
  nuURMAnual NUMERIC;
  nuValorVenal NUMERIC;
  nuVenalOld NUMERIC;
  nuVlrVenalAtual NUMERIC;
  stExercicioAnterior VARCHAR := '''''''';
  stUtiliza VARCHAR := '''''''';
BEGIN
nuAliquotaDeprec := 0;
boElse := TRUE;
inExercicio := RecuperarBufferInteiro(  ''''inExercicio''''  );
inImovel := RecuperarBufferInteiro(  ''''inRegistro''''  );
boEdificacao := arrecadacao.verificaEdificacaoImovel(  inImovel  );
stUtiliza := recuperaCadastroImobiliarioImovelUtilizacao(  inImovel  );
dtInscricao := imobiliario.fn_buscaDataConstrucaoImovel(  inImovel  );
nuExercicioConstrucao := recuperaExercicioConstrucaoImovel(  dtInscricao  );
inExercicioAnterior := inExercicio-1;
stExercicioAnterior := arrecadacao.fn_int2vc(  inExercicioAnterior  );
nuVenalOld := arrecadacao.fn_ultimo_venal_por_im(  inImovel , stExercicioAnterior  );
nuURM2008 := monetario.buscaValorAcrescimo(  1 , 1 , 1 , inExercicioAnterior  );
nuURMAnual := monetario.buscaValorAcrescimo(  1 , 1 , 1 , inExercicio  );
nuURM := (nuURMAnual /nuURM2008 )-1;
nuURM100 := nuURMAnual*100;
nuVlrVenalAtual := (nuVenalOld *nuURM )+nuVenalOld ;
boVenal := arrecadacao.fn_grava_venal(  inImovel , 0.00 , 0.00 , nuVlrVenalAtual , inExercicio  );
IF   boEdificacao  =  ''''true'''' THEN
    IF   nuExercicioConstrucao  >=  2005 THEN
        nuAliquotaDeprec := (inExercicio-(nuExercicioConstrucao+2))*0.01;
    ELSE
        nuAliquotaDeprec := (inExercicio -2006)*0.01;
    END IF;
    nuValorVenal := nuVlrVenalAtual-(nuVlrVenalAtual *nuAliquotaDeprec );
ELSE
    nuValorVenal := nuVlrVenalAtual;
END IF;
     IF   stUtiliza  =  ''''1'''' THEN
    boElse := FALSE;
    IF   nuValorVenal  <=  nuURM100 THEN
        nuAliquota := 0.008;
    ELSE
        nuAliquota := 0.009;
    END IF;
END IF;
     IF     stUtiliza  =  ''''2'''' THEN
    boElse := FALSE;
    nuAliquota := 0.015;
END IF;
     IF   stUtiliza  =  ''''3'''' THEN
    boElse := FALSE;
    nuAliquota := 0.012;
END IF;
     IF   stUtiliza  =  ''''4'''' THEN
    boElse := FALSE;
    nuAliquota := 0.01;
END IF;
IF   boElse  =  TRUE THEN
    nuAliquota := 0.02;
END IF;
nuImposto := nuValorVenal*nuAliquota ;
RETURN nuImposto;
END;
 '' LANGUAGE ''plpgsql'';
',    corpo_ln=
    '' WHERE cod_modulo = 12 AND cod_biblioteca = 2 AND cod_funcao = 58 ;

-- regraDesoneracaoIPTU2010
UPDATE administracao.corpo_funcao_externa SET linha = ' SE     #stIsentoDoImposto  !=  "1" ENTAO ' WHERE cod_modulo = 25 AND cod_biblioteca = 2 AND cod_funcao =  1 AND cod_linha =  2 ;
UPDATE administracao.funcao_externa SET     cod_modulo= 25,    cod_biblioteca= 2,    cod_funcao= 1,    comentario=
    '',    corpo_pl=
    'FUNCTION  regraDesoneracaoIPTU2010(INTEGER) RETURNS BOOLEAN as ''
DECLARE
inRegistro ALIAS FOR $1;

  boRetorno BOOLEAN := FALSE;
  stIsentoDoImposto VARCHAR := '''''''';
BEGIN
stIsentoDoImposto := recuperaCadastroImobiliarioImovelISENTODOIMPOSTO(  inRegistro  );
     IF     stIsentoDoImposto  !=  ''''1'''' THEN
    boRetorno := TRUE;
END IF;
RETURN boRetorno;
END;
 '' LANGUAGE ''plpgsql'';
',    corpo_ln=
    '' WHERE cod_modulo = 25 AND cod_biblioteca = 2 AND cod_funcao = 1 ;

-- regraDesoneracaoIPTU2011
UPDATE administracao.corpo_funcao_externa SET linha = ' SE     #stIsentoDoImposto  !=  "1" ENTAO ' WHERE cod_modulo = 25 AND cod_biblioteca = 2 AND cod_funcao =  2 AND cod_linha =  2 ;
UPDATE administracao.funcao_externa SET     cod_modulo= 25,    cod_biblioteca= 2,    cod_funcao= 2,    comentario=
    '',    corpo_pl=
    'FUNCTION  regraDesoneracaoIPTU2011(INTEGER) RETURNS BOOLEAN as ''
DECLARE
inRegistro ALIAS FOR $1;

  boRetorno BOOLEAN := FALSE;
  stIsentoDoImposto VARCHAR := '''''''';
BEGIN
stIsentoDoImposto := recuperaCadastroImobiliarioImovelISENTODOIMPOSTO(  inRegistro  );
     IF     stIsentoDoImposto  !=  ''''1'''' THEN
    boRetorno := TRUE;
END IF;
RETURN boRetorno;
END;
 '' LANGUAGE ''plpgsql'';
',    corpo_ln=
    '' WHERE cod_modulo = 25 AND cod_biblioteca = 2 AND cod_funcao = 2 ;

-- regraDesoneracaoIPTU2012
UPDATE administracao.corpo_funcao_externa SET linha = ' SE     #stIsentoDoImposto  !=  "1" ENTAO ' WHERE cod_modulo = 25 AND cod_biblioteca = 2 AND cod_funcao =  3 AND cod_linha =  2 ;
UPDATE administracao.funcao_externa SET     cod_modulo= 25,    cod_biblioteca= 2,    cod_funcao= 3,    comentario=
    '',    corpo_pl=
    'FUNCTION  regraDesoneracaoIPTU2012(INTEGER) RETURNS BOOLEAN as ''
DECLARE
inRegistro ALIAS FOR $1;

  boRetorno BOOLEAN := FALSE;
  stIsentoDoImposto VARCHAR := '''''''';
BEGIN
stIsentoDoImposto := recuperaCadastroImobiliarioImovelISENTODOIMPOSTO(  inRegistro  );
     IF     stIsentoDoImposto  !=  ''''1'''' THEN
    boRetorno := TRUE;
END IF;
RETURN boRetorno;
END;
 '' LANGUAGE ''plpgsql'';
',    corpo_ln=
    '' WHERE cod_modulo = 25 AND cod_biblioteca = 2 AND cod_funcao = 3 ;

-- regraDesoneracaoIPTU2013
UPDATE administracao.corpo_funcao_externa SET linha = ' SE     #stIsentoDoImposto  !=  "1" ENTAO ' WHERE cod_modulo = 25 AND cod_biblioteca = 2 AND cod_funcao =  4 AND cod_linha =  2 ;
UPDATE administracao.funcao_externa SET     cod_modulo= 25,    cod_biblioteca= 2,    cod_funcao= 4,    comentario=
    '',    corpo_pl=
    'FUNCTION  regraDesoneracaoIPTU2013(INTEGER) RETURNS BOOLEAN as ''
DECLARE
inRegistro ALIAS FOR $1;

  boRetorno BOOLEAN := FALSE;
  stIsentoDoImposto VARCHAR := '''''''';
BEGIN
stIsentoDoImposto := recuperaCadastroImobiliarioImovelISENTODOIMPOSTO(  inRegistro  );
     IF     stIsentoDoImposto  !=  ''''1'''' THEN
    boRetorno := TRUE;
END IF;
RETURN boRetorno;
END;
 '' LANGUAGE ''plpgsql'';
',    corpo_ln=
    '' WHERE cod_modulo = 25 AND cod_biblioteca = 2 AND cod_funcao = 4 ;

-- regraremissaolei6372010
UPDATE administracao.corpo_funcao_externa SET linha = ' SE   #stIsento  =  "1" ENTAO             ' WHERE cod_modulo = 33 AND cod_biblioteca = 5 AND cod_funcao =  1 AND cod_linha =  3 ;
UPDATE administracao.funcao_externa SET     cod_modulo= 33,    cod_biblioteca= 5,    cod_funcao= 1,    comentario=
    '',    corpo_pl=
    'FUNCTION  regraremissaolei6372010(INTEGER) RETURNS BOOLEAN as ''
DECLARE
inCodLancamento ALIAS FOR $1;

  boRetorno BOOLEAN := FALSE;
  inInscricao INTEGER;
  stIsento VARCHAR := '''''''';
BEGIN
inInscricao := arrecadacao.buscaInscricaoLancamento(  inCodLancamento  );
stIsento := recuperaCadastroImobiliarioImovelLeiMun6372010remissao(  inInscricao  );
     IF   stIsento  =  ''''1'''' THEN
    boRetorno := TRUE;
END IF;
RETURN boRetorno;
END;
 '' LANGUAGE ''plpgsql'';
',    corpo_ln=
    '' WHERE cod_modulo = 33 AND cod_biblioteca = 5 AND cod_funcao = 1 ;



UPDATE administracao.funcao_externa SET     cod_modulo= 33,    cod_biblioteca= 2,    cod_funcao= 1,    comentario=
    '',    corpo_pl=
    'FUNCTION  regraAcrescimoGeralDivida(INTEGER) RETURNS BOOLEAN as ''
DECLARE
inRegistro ALIAS FOR $1;

  boRetorno BOOLEAN := TRUE;
BEGIN
RETURN boRetorno;
END;
 '' LANGUAGE ''plpgsql'';
',    corpo_ln=
    '' WHERE cod_modulo = 33 AND cod_biblioteca = 2 AND cod_funcao = 1 ;

END;
$$ LANGUAGE 'plpgsql';


CREATE OR REPLACE FUNCTION manutencao_mata() RETURNS VOID AS $$
DECLARE
    stSQL   VARCHAR;
BEGIN
        stSQL := '
-- recuperaCadastroEconomicoInscricaoEconomicaAutonomoIsentoDeTaxas
CREATE OR REPLACE FUNCTION recuperaCadastroEconomicoInscricaoEconomicaAutonomoIsentoDeTaxas(  INTEGER ) RETURNS VARCHAR AS ''DECLARE stSql VARCHAR; crCursor  REFCURSOR; rsRetorno RECORD;inInscricaoEconomica ALIAS FOR $1;BEGIN stSql := ''''  SELECT
     AD.cod_cadastro,
     AD.cod_atributo,
     AD.ativo,
     AD.nao_nulo,
     AD.nom_atributo,
     CASE TA.cod_tipo
         WHEN 4 THEN  administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,'''''''''''''''')
     END AS valor_padrao,
     CASE TA.cod_tipo
       WHEN 3 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,''''''''''''''''))
       WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,VALOR.valor))
         ELSE         null
     END AS valor_padrao_desc,
     CASE TA.cod_tipo WHEN
         4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         null
     END AS valor_desc,
     AD.ajuda,
     AD.mascara,
     TA.cod_tipo,
     TA.nom_tipo,
     VALOR.valor,
     VALOR.timestamp
  FROM
     administracao.atributo_dinamico          AS AD,
     administracao.tipo_atributo              AS TA,
     administracao.atributo_dinamico AS ACA
     LEFT JOIN
     economico.atributo_cad_econ_autonomo_valor         AS VALOR
  ON ( ACA.cod_atributo = VALOR.cod_atributo
          AND ACA.cod_cadastro = VALOR.cod_cadastro
              AND VALOR.inscricao_economica = ''''||inInscricaoEconomica||''''
 AND ACA.cod_atributo = 6
         AND CAST(VALOR.timestamp as varchar)||CAST(VALOR.cod_atributo as varchar) IN (
             SELECT
         (CAST(max(VALOR.timestamp) as varchar))::varchar||CAST(VALOR.cod_atributo as varchar)
             FROM
                administracao.atributo_dinamico AS ACA,
                economico.atributo_cad_econ_autonomo_valor         AS VALOR,
                administracao.atributo_dinamico          AS AD,
                administracao.tipo_atributo              AS TA
             WHERE
                ACA.cod_atributo = AD.cod_atributo
                AND ACA.cod_cadastro = AD.cod_cadastro
                AND ACA.cod_modulo   = AD.cod_modulo
             AND ACA.cod_atributo = VALOR.cod_atributo
             AND ACA.cod_cadastro = VALOR.cod_cadastro
             AND ACA.cod_modulo   = VALOR.cod_modulo
              AND VALOR.inscricao_economica = ''''||inInscricaoEconomica||''''
 AND ACA.cod_atributo = 6
             AND AD.cod_tipo = TA.cod_tipo
             AND ACA.ativo = true
             AND AD.cod_modulo   =14
             AND AD.cod_cadastro=3

             GROUP BY VALOR.cod_cadastro, VALOR.cod_atributo
                                  )

     )
  WHERE
      AD.cod_tipo = TA.cod_tipo
  AND ACA.ativo = true
  AND     AD.ativo
  AND AD.cod_atributo =  ACA.cod_atributo
  AND AD.cod_modulo   = ACA.cod_modulo
  AND AD.cod_cadastro = ACA.cod_cadastro
  AND ACA.cod_cadastro=3
  AND ACA.cod_modulo  =14


 AND ACA.cod_atributo = 6;'''';OPEN crCursor FOR EXECUTE stSql;
                                    FETCH crCursor INTO rsRetorno;
                                    CLOSE crCursor;
                                    RETURN rsRetorno.valor;
                                    END;
                                    '' LANGUAGE plpgsql;
             ';
    EXECUTE stSQL;

    stSQL := '
CREATE OR REPLACE FUNCTION recuperaCadastroEconomicoInscricaoEconomicaEmpresaDeDireitoIsentoDeTaxas(  INTEGER ) RETURNS VARCHAR AS ''DECLARE stSql VARCHAR; crCursor  REFCURSOR; rsRetorno RECORD;inInscricaoEconomica ALIAS FOR $1;BEGIN stSql := ''''  SELECT
     AD.cod_cadastro,
     AD.cod_atributo,
     AD.ativo,
     AD.nao_nulo,
     AD.nom_atributo,
     CASE TA.cod_tipo
         WHEN 4 THEN  administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,'''''''''''''''')
     END AS valor_padrao,
     CASE TA.cod_tipo
       WHEN 3 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,''''''''''''''''))
       WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,VALOR.valor))
         ELSE         null
     END AS valor_padrao_desc,
     CASE TA.cod_tipo WHEN
         4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         null
     END AS valor_desc,
     AD.ajuda,
     AD.mascara,
     TA.cod_tipo,
     TA.nom_tipo,
     VALOR.valor,
     VALOR.timestamp
  FROM
     administracao.atributo_dinamico          AS AD,
     administracao.tipo_atributo              AS TA,
     administracao.atributo_dinamico AS ACA
     LEFT JOIN
     economico.atributo_empresa_direito_valor         AS VALOR
  ON ( ACA.cod_atributo = VALOR.cod_atributo
          AND ACA.cod_cadastro = VALOR.cod_cadastro
              AND VALOR.inscricao_economica = ''''||inInscricaoEconomica||''''
 AND ACA.cod_atributo = 5
         AND CAST(VALOR.timestamp as varchar)||CAST(VALOR.cod_atributo as varchar) IN (
             SELECT
         (CAST(max(VALOR.timestamp) as varchar))::varchar||CAST(VALOR.cod_atributo as varchar)
             FROM
                administracao.atributo_dinamico AS ACA,
                economico.atributo_empresa_direito_valor         AS VALOR,
                administracao.atributo_dinamico          AS AD,
                administracao.tipo_atributo              AS TA
             WHERE
                ACA.cod_atributo = AD.cod_atributo
                AND ACA.cod_cadastro = AD.cod_cadastro
                AND ACA.cod_modulo   = AD.cod_modulo
             AND ACA.cod_atributo = VALOR.cod_atributo
             AND ACA.cod_cadastro = VALOR.cod_cadastro
             AND ACA.cod_modulo   = VALOR.cod_modulo
              AND VALOR.inscricao_economica = ''''||inInscricaoEconomica||''''
 AND ACA.cod_atributo = 5
             AND AD.cod_tipo = TA.cod_tipo
             AND ACA.ativo = true
             AND AD.cod_modulo   =14
             AND AD.cod_cadastro=2

             GROUP BY VALOR.cod_cadastro, VALOR.cod_atributo
                                  )

     )
  WHERE
      AD.cod_tipo = TA.cod_tipo
  AND ACA.ativo = true
  AND     AD.ativo
  AND AD.cod_atributo =  ACA.cod_atributo
  AND AD.cod_modulo   = ACA.cod_modulo
  AND AD.cod_cadastro = ACA.cod_cadastro
  AND ACA.cod_cadastro=2
  AND ACA.cod_modulo  =14


 AND ACA.cod_atributo = 5;'''';OPEN crCursor FOR EXECUTE stSql;
                                    FETCH crCursor INTO rsRetorno;
                                    CLOSE crCursor;
                                    RETURN rsRetorno.valor;
                                    END;
                                    '' LANGUAGE plpgsql;
             ';
    EXECUTE stSQL;

    stSQL := '
CREATE OR REPLACE FUNCTION recuperaCadastroEconomicoInscricaoEconomicoEmpresaDeFatoIsentoDeTaxas(  INTEGER ) RETURNS VARCHAR AS ''DECLARE stSql VARCHAR; crCursor  REFCURSOR; rsRetorno RECORD;inInscricaoEconomica ALIAS FOR $1;BEGIN stSql := ''''  SELECT
     AD.cod_cadastro,
     AD.cod_atributo,
     AD.ativo,
     AD.nao_nulo,
     AD.nom_atributo,
     CASE TA.cod_tipo
         WHEN 4 THEN  administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,'''''''''''''''')
     END AS valor_padrao,
     CASE TA.cod_tipo
       WHEN 3 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,''''''''''''''''))
       WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,VALOR.valor))
         ELSE         null
     END AS valor_padrao_desc,
     CASE TA.cod_tipo WHEN
         4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         null
     END AS valor_desc,
     AD.ajuda,
     AD.mascara,
     TA.cod_tipo,
     TA.nom_tipo,
     VALOR.valor,
     VALOR.timestamp
  FROM
     administracao.atributo_dinamico          AS AD,
     administracao.tipo_atributo              AS TA,
     administracao.atributo_dinamico AS ACA
     LEFT JOIN
     economico.atributo_empresa_fato_valor         AS VALOR
  ON ( ACA.cod_atributo = VALOR.cod_atributo
          AND ACA.cod_cadastro = VALOR.cod_cadastro
              AND VALOR.inscricao_economica = ''''||inInscricaoEconomica||''''
 AND ACA.cod_atributo = 6
         AND CAST(VALOR.timestamp as varchar)||CAST(VALOR.cod_atributo as varchar) IN (
             SELECT
         (CAST(max(VALOR.timestamp) as varchar))::varchar||CAST(VALOR.cod_atributo as varchar)
             FROM
                administracao.atributo_dinamico AS ACA,
                economico.atributo_empresa_fato_valor         AS VALOR,
                administracao.atributo_dinamico          AS AD,
                administracao.tipo_atributo              AS TA
             WHERE
                ACA.cod_atributo = AD.cod_atributo
                AND ACA.cod_cadastro = AD.cod_cadastro
                AND ACA.cod_modulo   = AD.cod_modulo
             AND ACA.cod_atributo = VALOR.cod_atributo
             AND ACA.cod_cadastro = VALOR.cod_cadastro
             AND ACA.cod_modulo   = VALOR.cod_modulo
              AND VALOR.inscricao_economica = ''''||inInscricaoEconomica||''''
 AND ACA.cod_atributo = 6
             AND AD.cod_tipo = TA.cod_tipo
             AND ACA.ativo = true
             AND AD.cod_modulo   =14
             AND AD.cod_cadastro=1

             GROUP BY VALOR.cod_cadastro, VALOR.cod_atributo
                                  )

     )
  WHERE
      AD.cod_tipo = TA.cod_tipo
  AND ACA.ativo = true
  AND     AD.ativo
  AND AD.cod_atributo =  ACA.cod_atributo
  AND AD.cod_modulo   = ACA.cod_modulo
  AND AD.cod_cadastro = ACA.cod_cadastro
  AND ACA.cod_cadastro=1
  AND ACA.cod_modulo  =14


 AND ACA.cod_atributo = 6;'''';OPEN crCursor FOR EXECUTE stSql;
                                    FETCH crCursor INTO rsRetorno;
                                    CLOSE crCursor;
                                    RETURN rsRetorno.valor;
                                    END;
                                    '' LANGUAGE plpgsql;
             ';
    EXECUTE stSQL;

    stSQL := '
CREATE OR REPLACE FUNCTION recuperaCadastroImobiliarioImovelUsoDoSolo(  INTEGER ) RETURNS VARCHAR AS ''DECLARE stSql VARCHAR; crCursor  REFCURSOR; rsRetorno RECORD;inInscricaoMunicipal ALIAS FOR $1;BEGIN stSql := ''''  SELECT
     AD.cod_cadastro,
     AD.cod_atributo,
     AD.ativo,
     AD.nao_nulo,
     AD.nom_atributo,
     CASE TA.cod_tipo
         WHEN 4 THEN  administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,'''''''''''''''')
     END AS valor_padrao,
     CASE TA.cod_tipo
       WHEN 3 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,''''''''''''''''))
       WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,VALOR.valor))
         ELSE         null
     END AS valor_padrao_desc,
     CASE TA.cod_tipo WHEN
         4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         null
     END AS valor_desc,
     AD.ajuda,
     AD.mascara,
     TA.cod_tipo,
     TA.nom_tipo,
     VALOR.valor,
     VALOR.timestamp
  FROM
     administracao.atributo_dinamico          AS AD,
     administracao.tipo_atributo              AS TA,
     administracao.atributo_dinamico AS ACA
     LEFT JOIN
     imobiliario.atributo_imovel_valor         AS VALOR
  ON ( ACA.cod_atributo = VALOR.cod_atributo
          AND ACA.cod_cadastro = VALOR.cod_cadastro
              AND VALOR.inscricao_municipal = ''''||inInscricaoMunicipal||''''
 AND ACA.cod_atributo = 8
         AND CAST(VALOR.timestamp as varchar)||CAST(VALOR.cod_atributo as varchar) IN (
             SELECT
         (CAST(max(VALOR.timestamp) as varchar))::varchar||CAST(VALOR.cod_atributo as varchar)
             FROM
                administracao.atributo_dinamico AS ACA,
                imobiliario.atributo_imovel_valor         AS VALOR,
                administracao.atributo_dinamico          AS AD,
                administracao.tipo_atributo              AS TA
             WHERE
                ACA.cod_atributo = AD.cod_atributo
                AND ACA.cod_cadastro = AD.cod_cadastro
                AND ACA.cod_modulo   = AD.cod_modulo
             AND ACA.cod_atributo = VALOR.cod_atributo
             AND ACA.cod_cadastro = VALOR.cod_cadastro
             AND ACA.cod_modulo   = VALOR.cod_modulo
              AND VALOR.inscricao_municipal = ''''||inInscricaoMunicipal||''''
 AND ACA.cod_atributo = 8
             AND AD.cod_tipo = TA.cod_tipo
             AND ACA.ativo = true
             AND AD.cod_modulo   =12
             AND AD.cod_cadastro=4

             GROUP BY VALOR.cod_cadastro, VALOR.cod_atributo
                                  )

     )
  WHERE
      AD.cod_tipo = TA.cod_tipo
  AND ACA.ativo = true
  AND     AD.ativo
  AND AD.cod_atributo =  ACA.cod_atributo
  AND AD.cod_modulo   = ACA.cod_modulo
  AND AD.cod_cadastro = ACA.cod_cadastro
  AND ACA.cod_cadastro=4
  AND ACA.cod_modulo  =12


 AND ACA.cod_atributo = 8;'''';OPEN crCursor FOR EXECUTE stSql;
                                    FETCH crCursor INTO rsRetorno;
                                    CLOSE crCursor;
                                    RETURN rsRetorno.valor;
                                    END;
                                    '' LANGUAGE plpgsql;
             ';
    EXECUTE stSQL;

    stSQL := '
CREATE OR REPLACE FUNCTION recuperaCadastroImobiliarioImovelIsencaoIPTU(  INTEGER ) RETURNS VARCHAR AS ''DECLARE stSql VARCHAR; crCursor  REFCURSOR; rsRetorno RECORD;inInscricaoMunicipal ALIAS FOR $1;BEGIN stSql := ''''  SELECT
     AD.cod_cadastro,
     AD.cod_atributo,
     AD.ativo,
     AD.nao_nulo,
     AD.nom_atributo,
     CASE TA.cod_tipo
         WHEN 4 THEN  administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,'''''''''''''''')
     END AS valor_padrao,
     CASE TA.cod_tipo
       WHEN 3 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,''''''''''''''''))
       WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,VALOR.valor))
         ELSE         null
     END AS valor_padrao_desc,
     CASE TA.cod_tipo WHEN
         4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         null
     END AS valor_desc,
     AD.ajuda,
     AD.mascara,
     TA.cod_tipo,
     TA.nom_tipo,
     VALOR.valor,
     VALOR.timestamp
  FROM
     administracao.atributo_dinamico          AS AD,
     administracao.tipo_atributo              AS TA,
     administracao.atributo_dinamico AS ACA
     LEFT JOIN
     imobiliario.atributo_imovel_valor         AS VALOR
  ON ( ACA.cod_atributo = VALOR.cod_atributo
          AND ACA.cod_cadastro = VALOR.cod_cadastro
              AND VALOR.inscricao_municipal = ''''||inInscricaoMunicipal||''''
 AND ACA.cod_atributo = 2
         AND CAST(VALOR.timestamp as varchar)||CAST(VALOR.cod_atributo as varchar) IN (
             SELECT
         (CAST(max(VALOR.timestamp) as varchar))::varchar||CAST(VALOR.cod_atributo as varchar)
             FROM
                administracao.atributo_dinamico AS ACA,
                imobiliario.atributo_imovel_valor         AS VALOR,
                administracao.atributo_dinamico          AS AD,
                administracao.tipo_atributo              AS TA
             WHERE
                ACA.cod_atributo = AD.cod_atributo
                AND ACA.cod_cadastro = AD.cod_cadastro
                AND ACA.cod_modulo   = AD.cod_modulo
             AND ACA.cod_atributo = VALOR.cod_atributo
             AND ACA.cod_cadastro = VALOR.cod_cadastro
             AND ACA.cod_modulo   = VALOR.cod_modulo
              AND VALOR.inscricao_municipal = ''''||inInscricaoMunicipal||''''
 AND ACA.cod_atributo = 2
             AND AD.cod_tipo = TA.cod_tipo
             AND ACA.ativo = true
             AND AD.cod_modulo   =12
             AND AD.cod_cadastro=4

             GROUP BY VALOR.cod_cadastro, VALOR.cod_atributo
                                  )

     )
  WHERE
      AD.cod_tipo = TA.cod_tipo
  AND ACA.ativo = true
  AND     AD.ativo
  AND AD.cod_atributo =  ACA.cod_atributo
  AND AD.cod_modulo   = ACA.cod_modulo
  AND AD.cod_cadastro = ACA.cod_cadastro
  AND ACA.cod_cadastro=4
  AND ACA.cod_modulo  =12


 AND ACA.cod_atributo = 2;'''';OPEN crCursor FOR EXECUTE stSql;
                                    FETCH crCursor INTO rsRetorno;
                                    CLOSE crCursor;
                                    RETURN rsRetorno.valor;
                                    END;
                                    '' LANGUAGE plpgsql;
             ';
    EXECUTE stSQL;

    stSQL := '
CREATE OR REPLACE FUNCTION recuperaCadastroImobiliarioImovelIsencaoTSU(  INTEGER ) RETURNS VARCHAR AS ''DECLARE stSql VARCHAR; crCursor  REFCURSOR; rsRetorno RECORD;inInscricaoMunicipal ALIAS FOR $1;BEGIN stSql := ''''  SELECT
     AD.cod_cadastro,
     AD.cod_atributo,
     AD.ativo,
     AD.nao_nulo,
     AD.nom_atributo,
     CASE TA.cod_tipo
         WHEN 4 THEN  administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,'''''''''''''''')
     END AS valor_padrao,
     CASE TA.cod_tipo
       WHEN 3 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,''''''''''''''''))
       WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,VALOR.valor))
         ELSE         null
     END AS valor_padrao_desc,
     CASE TA.cod_tipo WHEN
         4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         null
     END AS valor_desc,
     AD.ajuda,
     AD.mascara,
     TA.cod_tipo,
     TA.nom_tipo,
     VALOR.valor,
     VALOR.timestamp
  FROM
     administracao.atributo_dinamico          AS AD,
     administracao.tipo_atributo              AS TA,
     administracao.atributo_dinamico AS ACA
     LEFT JOIN
     imobiliario.atributo_imovel_valor         AS VALOR
  ON ( ACA.cod_atributo = VALOR.cod_atributo
          AND ACA.cod_cadastro = VALOR.cod_cadastro
              AND VALOR.inscricao_municipal = ''''||inInscricaoMunicipal||''''
 AND ACA.cod_atributo = 3
         AND CAST(VALOR.timestamp as varchar)||CAST(VALOR.cod_atributo as varchar) IN (
             SELECT
         (CAST(max(VALOR.timestamp) as varchar))::varchar||CAST(VALOR.cod_atributo as varchar)
             FROM
                administracao.atributo_dinamico AS ACA,
                imobiliario.atributo_imovel_valor         AS VALOR,
                administracao.atributo_dinamico          AS AD,
                administracao.tipo_atributo              AS TA
             WHERE
                ACA.cod_atributo = AD.cod_atributo
                AND ACA.cod_cadastro = AD.cod_cadastro
                AND ACA.cod_modulo   = AD.cod_modulo
             AND ACA.cod_atributo = VALOR.cod_atributo
             AND ACA.cod_cadastro = VALOR.cod_cadastro
             AND ACA.cod_modulo   = VALOR.cod_modulo
              AND VALOR.inscricao_municipal = ''''||inInscricaoMunicipal||''''
 AND ACA.cod_atributo = 3
             AND AD.cod_tipo = TA.cod_tipo
             AND ACA.ativo = true
             AND AD.cod_modulo   =12
             AND AD.cod_cadastro=4

             GROUP BY VALOR.cod_cadastro, VALOR.cod_atributo
                                  )

     )
  WHERE
      AD.cod_tipo = TA.cod_tipo
  AND ACA.ativo = true
  AND     AD.ativo
  AND AD.cod_atributo =  ACA.cod_atributo
  AND AD.cod_modulo   = ACA.cod_modulo
  AND AD.cod_cadastro = ACA.cod_cadastro
  AND ACA.cod_cadastro=4
  AND ACA.cod_modulo  =12


 AND ACA.cod_atributo = 3;'''';OPEN crCursor FOR EXECUTE stSql;
                                    FETCH crCursor INTO rsRetorno;
                                    CLOSE crCursor;
                                    RETURN rsRetorno.valor;
                                    END;
                                    '' LANGUAGE plpgsql;
             ';
    EXECUTE stSQL;

    stSQL := '
CREATE OR REPLACE FUNCTION recuperaCadastroImobiliarioImovelLimitacao(  INTEGER ) RETURNS VARCHAR AS ''DECLARE stSql VARCHAR; crCursor  REFCURSOR; rsRetorno RECORD;inInscricaoMunicipal ALIAS FOR $1;BEGIN stSql := ''''  SELECT
     AD.cod_cadastro,
     AD.cod_atributo,
     AD.ativo,
     AD.nao_nulo,
     AD.nom_atributo,
     CASE TA.cod_tipo
         WHEN 4 THEN  administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,'''''''''''''''')
     END AS valor_padrao,
     CASE TA.cod_tipo
       WHEN 3 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,''''''''''''''''))
       WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,VALOR.valor))
         ELSE         null
     END AS valor_padrao_desc,
     CASE TA.cod_tipo WHEN
         4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         null
     END AS valor_desc,
     AD.ajuda,
     AD.mascara,
     TA.cod_tipo,
     TA.nom_tipo,
     VALOR.valor,
     VALOR.timestamp
  FROM
     administracao.atributo_dinamico          AS AD,
     administracao.tipo_atributo              AS TA,
     administracao.atributo_dinamico AS ACA
     LEFT JOIN
     imobiliario.atributo_imovel_valor         AS VALOR
  ON ( ACA.cod_atributo = VALOR.cod_atributo
          AND ACA.cod_cadastro = VALOR.cod_cadastro
              AND VALOR.inscricao_municipal = ''''||inInscricaoMunicipal||''''
 AND ACA.cod_atributo = 4
         AND CAST(VALOR.timestamp as varchar)||CAST(VALOR.cod_atributo as varchar) IN (
             SELECT
         (CAST(max(VALOR.timestamp) as varchar))::varchar||CAST(VALOR.cod_atributo as varchar)
             FROM
                administracao.atributo_dinamico AS ACA,
                imobiliario.atributo_imovel_valor         AS VALOR,
                administracao.atributo_dinamico          AS AD,
                administracao.tipo_atributo              AS TA
             WHERE
                ACA.cod_atributo = AD.cod_atributo
                AND ACA.cod_cadastro = AD.cod_cadastro
                AND ACA.cod_modulo   = AD.cod_modulo
             AND ACA.cod_atributo = VALOR.cod_atributo
             AND ACA.cod_cadastro = VALOR.cod_cadastro
             AND ACA.cod_modulo   = VALOR.cod_modulo
              AND VALOR.inscricao_municipal = ''''||inInscricaoMunicipal||''''
 AND ACA.cod_atributo = 4
             AND AD.cod_tipo = TA.cod_tipo
             AND ACA.ativo = true
             AND AD.cod_modulo   =12
             AND AD.cod_cadastro=4

             GROUP BY VALOR.cod_cadastro, VALOR.cod_atributo
                                  )

     )
  WHERE
      AD.cod_tipo = TA.cod_tipo
  AND ACA.ativo = true
  AND     AD.ativo
  AND AD.cod_atributo =  ACA.cod_atributo
  AND AD.cod_modulo   = ACA.cod_modulo
  AND AD.cod_cadastro = ACA.cod_cadastro
  AND ACA.cod_cadastro=4
  AND ACA.cod_modulo  =12


 AND ACA.cod_atributo = 4;'''';OPEN crCursor FOR EXECUTE stSql;
                                    FETCH crCursor INTO rsRetorno;
                                    CLOSE crCursor;
                                    RETURN rsRetorno.valor;
                                    END;
                                    '' LANGUAGE plpgsql;
             ';
    EXECUTE stSQL;

    stSQL := '
CREATE OR REPLACE FUNCTION recuperaCadastroImobiliarioImovelTipoDeIsencao(  INTEGER ) RETURNS VARCHAR AS ''DECLARE stSql VARCHAR; crCursor  REFCURSOR; rsRetorno RECORD;inInscricaoMunicipal ALIAS FOR $1;BEGIN stSql := ''''  SELECT
     AD.cod_cadastro,
     AD.cod_atributo,
     AD.ativo,
     AD.nao_nulo,
     AD.nom_atributo,
     CASE TA.cod_tipo
         WHEN 4 THEN  administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,'''''''''''''''')
     END AS valor_padrao,
     CASE TA.cod_tipo
       WHEN 3 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,''''''''''''''''))
       WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,VALOR.valor))
         ELSE         null
     END AS valor_padrao_desc,
     CASE TA.cod_tipo WHEN
         4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         null
     END AS valor_desc,
     AD.ajuda,
     AD.mascara,
     TA.cod_tipo,
     TA.nom_tipo,
     VALOR.valor,
     VALOR.timestamp
  FROM
     administracao.atributo_dinamico          AS AD,
     administracao.tipo_atributo              AS TA,
     administracao.atributo_dinamico AS ACA
     LEFT JOIN
     imobiliario.atributo_imovel_valor         AS VALOR
  ON ( ACA.cod_atributo = VALOR.cod_atributo
          AND ACA.cod_cadastro = VALOR.cod_cadastro
              AND VALOR.inscricao_municipal = ''''||inInscricaoMunicipal||''''
 AND ACA.cod_atributo = 7
         AND CAST(VALOR.timestamp as varchar)||CAST(VALOR.cod_atributo as varchar) IN (
             SELECT
         (CAST(max(VALOR.timestamp) as varchar))::varchar||CAST(VALOR.cod_atributo as varchar)
             FROM
                administracao.atributo_dinamico AS ACA,
                imobiliario.atributo_imovel_valor         AS VALOR,
                administracao.atributo_dinamico          AS AD,
                administracao.tipo_atributo              AS TA
             WHERE
                ACA.cod_atributo = AD.cod_atributo
                AND ACA.cod_cadastro = AD.cod_cadastro
                AND ACA.cod_modulo   = AD.cod_modulo
             AND ACA.cod_atributo = VALOR.cod_atributo
             AND ACA.cod_cadastro = VALOR.cod_cadastro
             AND ACA.cod_modulo   = VALOR.cod_modulo
              AND VALOR.inscricao_municipal = ''''||inInscricaoMunicipal||''''
 AND ACA.cod_atributo = 7
             AND AD.cod_tipo = TA.cod_tipo
             AND ACA.ativo = true
             AND AD.cod_modulo   =12
             AND AD.cod_cadastro=4

             GROUP BY VALOR.cod_cadastro, VALOR.cod_atributo
                                  )

     )
  WHERE
      AD.cod_tipo = TA.cod_tipo
  AND ACA.ativo = true
  AND     AD.ativo
  AND AD.cod_atributo =  ACA.cod_atributo
  AND AD.cod_modulo   = ACA.cod_modulo
  AND AD.cod_cadastro = ACA.cod_cadastro
  AND ACA.cod_cadastro=4
  AND ACA.cod_modulo  =12


 AND ACA.cod_atributo = 7;'''';OPEN crCursor FOR EXECUTE stSql;
                                    FETCH crCursor INTO rsRetorno;
                                    CLOSE crCursor;
                                    RETURN rsRetorno.valor;
                                    END;
                                    '' LANGUAGE plpgsql;
             ';
    EXECUTE stSQL;

    stSQL := '
CREATE OR REPLACE FUNCTION recuperaCadastroImobiliarioImovelUtilizacaoDoImovel(  INTEGER ) RETURNS VARCHAR AS ''DECLARE stSql VARCHAR; crCursor  REFCURSOR; rsRetorno RECORD;inInscricaoMunicipal ALIAS FOR $1;BEGIN stSql := ''''  SELECT
     AD.cod_cadastro,
     AD.cod_atributo,
     AD.ativo,
     AD.nao_nulo,
     AD.nom_atributo,
     CASE TA.cod_tipo
         WHEN 4 THEN  administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,'''''''''''''''')
     END AS valor_padrao,
     CASE TA.cod_tipo
       WHEN 3 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,''''''''''''''''))
       WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,VALOR.valor))
         ELSE         null
     END AS valor_padrao_desc,
     CASE TA.cod_tipo WHEN
         4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         null
     END AS valor_desc,
     AD.ajuda,
     AD.mascara,
     TA.cod_tipo,
     TA.nom_tipo,
     VALOR.valor,
     VALOR.timestamp
  FROM
     administracao.atributo_dinamico          AS AD,
     administracao.tipo_atributo              AS TA,
     administracao.atributo_dinamico AS ACA
     LEFT JOIN
     imobiliario.atributo_imovel_valor         AS VALOR
  ON ( ACA.cod_atributo = VALOR.cod_atributo
          AND ACA.cod_cadastro = VALOR.cod_cadastro
              AND VALOR.inscricao_municipal = ''''||inInscricaoMunicipal||''''
 AND ACA.cod_atributo = 9
         AND CAST(VALOR.timestamp as varchar)||CAST(VALOR.cod_atributo as varchar) IN (
             SELECT
         (CAST(max(VALOR.timestamp) as varchar))::varchar||CAST(VALOR.cod_atributo as varchar)
             FROM
                administracao.atributo_dinamico AS ACA,
                imobiliario.atributo_imovel_valor         AS VALOR,
                administracao.atributo_dinamico          AS AD,
                administracao.tipo_atributo              AS TA
             WHERE
                ACA.cod_atributo = AD.cod_atributo
                AND ACA.cod_cadastro = AD.cod_cadastro
                AND ACA.cod_modulo   = AD.cod_modulo
             AND ACA.cod_atributo = VALOR.cod_atributo
             AND ACA.cod_cadastro = VALOR.cod_cadastro
             AND ACA.cod_modulo   = VALOR.cod_modulo
              AND VALOR.inscricao_municipal = ''''||inInscricaoMunicipal||''''
 AND ACA.cod_atributo = 9
             AND AD.cod_tipo = TA.cod_tipo
             AND ACA.ativo = true
             AND AD.cod_modulo   =12
             AND AD.cod_cadastro=4

             GROUP BY VALOR.cod_cadastro, VALOR.cod_atributo
                                  )

     )
  WHERE
      AD.cod_tipo = TA.cod_tipo
  AND ACA.ativo = true
  AND     AD.ativo
  AND AD.cod_atributo =  ACA.cod_atributo
  AND AD.cod_modulo   = ACA.cod_modulo
  AND AD.cod_cadastro = ACA.cod_cadastro
  AND ACA.cod_cadastro=4
  AND ACA.cod_modulo  =12


 AND ACA.cod_atributo = 9;'''';OPEN crCursor FOR EXECUTE stSql;
                                    FETCH crCursor INTO rsRetorno;
                                    CLOSE crCursor;
                                    RETURN rsRetorno.valor;
                                    END;
                                    '' LANGUAGE plpgsql;
             ';
    EXECUTE stSQL;

    stSQL := '
CREATE OR REPLACE FUNCTION recuperaCadastroImobiliarioImovelZona(  INTEGER ) RETURNS VARCHAR AS ''DECLARE stSql VARCHAR; crCursor  REFCURSOR; rsRetorno RECORD;inInscricaoMunicipal ALIAS FOR $1;BEGIN stSql := ''''  SELECT
     AD.cod_cadastro,
     AD.cod_atributo,
     AD.ativo,
     AD.nao_nulo,
     AD.nom_atributo,
     CASE TA.cod_tipo
         WHEN 4 THEN  administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,'''''''''''''''')
     END AS valor_padrao,
     CASE TA.cod_tipo
       WHEN 3 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,''''''''''''''''))
       WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,VALOR.valor))
         ELSE         null
     END AS valor_padrao_desc,
     CASE TA.cod_tipo WHEN
         4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         null
     END AS valor_desc,
     AD.ajuda,
     AD.mascara,
     TA.cod_tipo,
     TA.nom_tipo,
     VALOR.valor,
     VALOR.timestamp
  FROM
     administracao.atributo_dinamico          AS AD,
     administracao.tipo_atributo              AS TA,
     administracao.atributo_dinamico AS ACA
     LEFT JOIN
     imobiliario.atributo_imovel_valor         AS VALOR
  ON ( ACA.cod_atributo = VALOR.cod_atributo
          AND ACA.cod_cadastro = VALOR.cod_cadastro
              AND VALOR.inscricao_municipal = ''''||inInscricaoMunicipal||''''
 AND ACA.cod_atributo = 106
         AND CAST(VALOR.timestamp as varchar)||CAST(VALOR.cod_atributo as varchar) IN (
             SELECT
         (CAST(max(VALOR.timestamp) as varchar))::varchar||CAST(VALOR.cod_atributo as varchar)
             FROM
                administracao.atributo_dinamico AS ACA,
                imobiliario.atributo_imovel_valor         AS VALOR,
                administracao.atributo_dinamico          AS AD,
                administracao.tipo_atributo              AS TA
             WHERE
                ACA.cod_atributo = AD.cod_atributo
                AND ACA.cod_cadastro = AD.cod_cadastro
                AND ACA.cod_modulo   = AD.cod_modulo
             AND ACA.cod_atributo = VALOR.cod_atributo
             AND ACA.cod_cadastro = VALOR.cod_cadastro
             AND ACA.cod_modulo   = VALOR.cod_modulo
              AND VALOR.inscricao_municipal = ''''||inInscricaoMunicipal||''''
 AND ACA.cod_atributo = 106
             AND AD.cod_tipo = TA.cod_tipo
             AND ACA.ativo = true
             AND AD.cod_modulo   =12
             AND AD.cod_cadastro=4

             GROUP BY VALOR.cod_cadastro, VALOR.cod_atributo
                                  )

     )
  WHERE
      AD.cod_tipo = TA.cod_tipo
  AND ACA.ativo = true
  AND     AD.ativo
  AND AD.cod_atributo =  ACA.cod_atributo
  AND AD.cod_modulo   = ACA.cod_modulo
  AND AD.cod_cadastro = ACA.cod_cadastro
  AND ACA.cod_cadastro=4
  AND ACA.cod_modulo  =12


 AND ACA.cod_atributo = 106;'''';OPEN crCursor FOR EXECUTE stSql;
                                    FETCH crCursor INTO rsRetorno;
                                    CLOSE crCursor;
                                    RETURN rsRetorno.valor;
                                    END;
                                    '' LANGUAGE plpgsql;
             ';
    EXECUTE stSQL;

    stSQL := '
CREATE OR REPLACE FUNCTION recuperaCadastroImobiliarioImovelOcupacaoDoTerreno(  INTEGER ) RETURNS VARCHAR AS ''DECLARE stSql VARCHAR; crCursor  REFCURSOR; rsRetorno RECORD;inInscricaoMunicipal ALIAS FOR $1;BEGIN stSql := ''''  SELECT
     AD.cod_cadastro,
     AD.cod_atributo,
     AD.ativo,
     AD.nao_nulo,
     AD.nom_atributo,
     CASE TA.cod_tipo
         WHEN 4 THEN  administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,'''''''''''''''')
     END AS valor_padrao,
     CASE TA.cod_tipo
       WHEN 3 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,''''''''''''''''))
       WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,VALOR.valor))
         ELSE         null
     END AS valor_padrao_desc,
     CASE TA.cod_tipo WHEN
         4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         null
     END AS valor_desc,
     AD.ajuda,
     AD.mascara,
     TA.cod_tipo,
     TA.nom_tipo,
     VALOR.valor,
     VALOR.timestamp
  FROM
     administracao.atributo_dinamico          AS AD,
     administracao.tipo_atributo              AS TA,
     administracao.atributo_dinamico AS ACA
     LEFT JOIN
     imobiliario.atributo_imovel_valor         AS VALOR
  ON ( ACA.cod_atributo = VALOR.cod_atributo
          AND ACA.cod_cadastro = VALOR.cod_cadastro
              AND VALOR.inscricao_municipal = ''''||inInscricaoMunicipal||''''
 AND ACA.cod_atributo = 5
         AND CAST(VALOR.timestamp as varchar)||CAST(VALOR.cod_atributo as varchar) IN (
             SELECT
         (CAST(max(VALOR.timestamp) as varchar))::varchar||CAST(VALOR.cod_atributo as varchar)
             FROM
                administracao.atributo_dinamico AS ACA,
                imobiliario.atributo_imovel_valor         AS VALOR,
                administracao.atributo_dinamico          AS AD,
                administracao.tipo_atributo              AS TA
             WHERE
                ACA.cod_atributo = AD.cod_atributo
                AND ACA.cod_cadastro = AD.cod_cadastro
                AND ACA.cod_modulo   = AD.cod_modulo
             AND ACA.cod_atributo = VALOR.cod_atributo
             AND ACA.cod_cadastro = VALOR.cod_cadastro
             AND ACA.cod_modulo   = VALOR.cod_modulo
              AND VALOR.inscricao_municipal = ''''||inInscricaoMunicipal||''''
 AND ACA.cod_atributo = 5
             AND AD.cod_tipo = TA.cod_tipo
             AND ACA.ativo = true
             AND AD.cod_modulo   =12
             AND AD.cod_cadastro=4

             GROUP BY VALOR.cod_cadastro, VALOR.cod_atributo
                                  )

     )
  WHERE
      AD.cod_tipo = TA.cod_tipo
  AND ACA.ativo = true
  AND     AD.ativo
  AND AD.cod_atributo =  ACA.cod_atributo
  AND AD.cod_modulo   = ACA.cod_modulo
  AND AD.cod_cadastro = ACA.cod_cadastro
  AND ACA.cod_cadastro=4
  AND ACA.cod_modulo  =12


 AND ACA.cod_atributo = 5;'''';OPEN crCursor FOR EXECUTE stSql;
                                    FETCH crCursor INTO rsRetorno;
                                    CLOSE crCursor;
                                    RETURN rsRetorno.valor;
                                    END;
                                    '' LANGUAGE plpgsql;
             ';
    EXECUTE stSQL;

    stSQL := '
CREATE OR REPLACE FUNCTION recuperaCadastroImobiliarioImovelEspecificacaoComercial(  INTEGER ) RETURNS VARCHAR AS ''DECLARE stSql VARCHAR; crCursor  REFCURSOR; rsRetorno RECORD;inInscricaoMunicipal ALIAS FOR $1;BEGIN stSql := ''''  SELECT
     AD.cod_cadastro,
     AD.cod_atributo,
     AD.ativo,
     AD.nao_nulo,
     AD.nom_atributo,
     CASE TA.cod_tipo
         WHEN 4 THEN  administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,'''''''''''''''')
     END AS valor_padrao,
     CASE TA.cod_tipo
       WHEN 3 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,''''''''''''''''))
       WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,VALOR.valor))
         ELSE         null
     END AS valor_padrao_desc,
     CASE TA.cod_tipo WHEN
         4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         null
     END AS valor_desc,
     AD.ajuda,
     AD.mascara,
     TA.cod_tipo,
     TA.nom_tipo,
     VALOR.valor,
     VALOR.timestamp
  FROM
     administracao.atributo_dinamico          AS AD,
     administracao.tipo_atributo              AS TA,
     administracao.atributo_dinamico AS ACA
     LEFT JOIN
     imobiliario.atributo_imovel_valor         AS VALOR
  ON ( ACA.cod_atributo = VALOR.cod_atributo
          AND ACA.cod_cadastro = VALOR.cod_cadastro
              AND VALOR.inscricao_municipal = ''''||inInscricaoMunicipal||''''
 AND ACA.cod_atributo = 1
         AND CAST(VALOR.timestamp as varchar)||CAST(VALOR.cod_atributo as varchar) IN (
             SELECT
         (CAST(max(VALOR.timestamp) as varchar))::varchar||CAST(VALOR.cod_atributo as varchar)
             FROM
                administracao.atributo_dinamico AS ACA,
                imobiliario.atributo_imovel_valor         AS VALOR,
                administracao.atributo_dinamico          AS AD,
                administracao.tipo_atributo              AS TA
             WHERE
                ACA.cod_atributo = AD.cod_atributo
                AND ACA.cod_cadastro = AD.cod_cadastro
                AND ACA.cod_modulo   = AD.cod_modulo
             AND ACA.cod_atributo = VALOR.cod_atributo
             AND ACA.cod_cadastro = VALOR.cod_cadastro
             AND ACA.cod_modulo   = VALOR.cod_modulo
              AND VALOR.inscricao_municipal = ''''||inInscricaoMunicipal||''''
 AND ACA.cod_atributo = 1
             AND AD.cod_tipo = TA.cod_tipo
             AND ACA.ativo = true
             AND AD.cod_modulo   =12
             AND AD.cod_cadastro=4

             GROUP BY VALOR.cod_cadastro, VALOR.cod_atributo
                                  )

     )
  WHERE
      AD.cod_tipo = TA.cod_tipo
  AND ACA.ativo = true
  AND     AD.ativo
  AND AD.cod_atributo =  ACA.cod_atributo
  AND AD.cod_modulo   = ACA.cod_modulo
  AND AD.cod_cadastro = ACA.cod_cadastro
  AND ACA.cod_cadastro=4
  AND ACA.cod_modulo  =12


 AND ACA.cod_atributo = 1;'''';OPEN crCursor FOR EXECUTE stSql;
                                    FETCH crCursor INTO rsRetorno;
                                    CLOSE crCursor;
                                    RETURN rsRetorno.valor;
                                    END;
                                    '' LANGUAGE plpgsql;
             ';
    EXECUTE stSQL;

    stSQL := '
CREATE OR REPLACE FUNCTION recuperaCadastroImobiliarioImovelAreaTotalDescoberta(  INTEGER ) RETURNS VARCHAR AS ''DECLARE stSql VARCHAR; crCursor  REFCURSOR; rsRetorno RECORD;inInscricaoMunicipal ALIAS FOR $1;BEGIN stSql := ''''  SELECT
     AD.cod_cadastro,
     AD.cod_atributo,
     AD.ativo,
     AD.nao_nulo,
     AD.nom_atributo,
     CASE TA.cod_tipo
         WHEN 4 THEN  administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,'''''''''''''''')
     END AS valor_padrao,
     CASE TA.cod_tipo
       WHEN 3 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,''''''''''''''''))
       WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,VALOR.valor))
         ELSE         null
     END AS valor_padrao_desc,
     CASE TA.cod_tipo WHEN
         4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         null
     END AS valor_desc,
     AD.ajuda,
     AD.mascara,
     TA.cod_tipo,
     TA.nom_tipo,
     VALOR.valor,
     VALOR.timestamp
  FROM
     administracao.atributo_dinamico          AS AD,
     administracao.tipo_atributo              AS TA,
     administracao.atributo_dinamico AS ACA
     LEFT JOIN
     imobiliario.atributo_imovel_valor         AS VALOR
  ON ( ACA.cod_atributo = VALOR.cod_atributo
          AND ACA.cod_cadastro = VALOR.cod_cadastro
              AND VALOR.inscricao_municipal = ''''||inInscricaoMunicipal||''''
 AND ACA.cod_atributo = 5002
         AND CAST(VALOR.timestamp as varchar)||CAST(VALOR.cod_atributo as varchar) IN (
             SELECT
         (CAST(max(VALOR.timestamp) as varchar))::varchar||CAST(VALOR.cod_atributo as varchar)
             FROM
                administracao.atributo_dinamico AS ACA,
                imobiliario.atributo_imovel_valor         AS VALOR,
                administracao.atributo_dinamico          AS AD,
                administracao.tipo_atributo              AS TA
             WHERE
                ACA.cod_atributo = AD.cod_atributo
                AND ACA.cod_cadastro = AD.cod_cadastro
                AND ACA.cod_modulo   = AD.cod_modulo
             AND ACA.cod_atributo = VALOR.cod_atributo
             AND ACA.cod_cadastro = VALOR.cod_cadastro
             AND ACA.cod_modulo   = VALOR.cod_modulo
              AND VALOR.inscricao_municipal = ''''||inInscricaoMunicipal||''''
 AND ACA.cod_atributo = 5002
             AND AD.cod_tipo = TA.cod_tipo
             AND ACA.ativo = true
             AND AD.cod_modulo   =12
             AND AD.cod_cadastro=4

             GROUP BY VALOR.cod_cadastro, VALOR.cod_atributo
                                  )

     )
  WHERE
      AD.cod_tipo = TA.cod_tipo
  AND ACA.ativo = true
  AND     AD.ativo
  AND AD.cod_atributo =  ACA.cod_atributo
  AND AD.cod_modulo   = ACA.cod_modulo
  AND AD.cod_cadastro = ACA.cod_cadastro
  AND ACA.cod_cadastro=4
  AND ACA.cod_modulo  =12


 AND ACA.cod_atributo = 5002;'''';OPEN crCursor FOR EXECUTE stSql;
                                    FETCH crCursor INTO rsRetorno;
                                    CLOSE crCursor;
                                    RETURN rsRetorno.valor;
                                    END;
                                    '' LANGUAGE plpgsql;
             ';
    EXECUTE stSQL;

    stSQL := '
CREATE OR REPLACE FUNCTION recuperaCadastroImobiliarioLoteUrbanoDescontoValorVenal(  INTEGER ) RETURNS VARCHAR AS ''DECLARE stSql VARCHAR; crCursor  REFCURSOR; rsRetorno RECORD;inCodLote ALIAS FOR $1;BEGIN stSql := ''''  SELECT
     AD.cod_cadastro,
     AD.cod_atributo,
     AD.ativo,
     AD.nao_nulo,
     AD.nom_atributo,
     CASE TA.cod_tipo
         WHEN 4 THEN  administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,'''''''''''''''')
     END AS valor_padrao,
     CASE TA.cod_tipo
       WHEN 3 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,''''''''''''''''))
       WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,VALOR.valor))
         ELSE         null
     END AS valor_padrao_desc,
     CASE TA.cod_tipo WHEN
         4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
         ELSE         null
     END AS valor_desc,
     AD.ajuda,
     AD.mascara,
     TA.cod_tipo,
     TA.nom_tipo,
     VALOR.valor,
     VALOR.timestamp
  FROM
     administracao.atributo_dinamico          AS AD,
     administracao.tipo_atributo              AS TA,
     administracao.atributo_dinamico AS ACA
     LEFT JOIN
     imobiliario.atributo_lote_urbano_valor         AS VALOR
  ON ( ACA.cod_atributo = VALOR.cod_atributo
          AND ACA.cod_cadastro = VALOR.cod_cadastro
              AND VALOR.cod_lote = ''''||inCodLote||''''
 AND ACA.cod_atributo = 8
         AND CAST(VALOR.timestamp as varchar)||CAST(VALOR.cod_atributo as varchar) IN (
             SELECT
         (CAST(max(VALOR.timestamp) as varchar))::varchar||CAST(VALOR.cod_atributo as varchar)
             FROM
                administracao.atributo_dinamico AS ACA,
                imobiliario.atributo_lote_urbano_valor         AS VALOR,
                administracao.atributo_dinamico          AS AD,
                administracao.tipo_atributo              AS TA
             WHERE
                ACA.cod_atributo = AD.cod_atributo
                AND ACA.cod_cadastro = AD.cod_cadastro
                AND ACA.cod_modulo   = AD.cod_modulo
             AND ACA.cod_atributo = VALOR.cod_atributo
             AND ACA.cod_cadastro = VALOR.cod_cadastro
             AND ACA.cod_modulo   = VALOR.cod_modulo
              AND VALOR.cod_lote = ''''||inCodLote||''''
 AND ACA.cod_atributo = 8
             AND AD.cod_tipo = TA.cod_tipo
             AND ACA.ativo = true
             AND AD.cod_modulo   =12
             AND AD.cod_cadastro=2

             GROUP BY VALOR.cod_cadastro, VALOR.cod_atributo
                                  )

     )
  WHERE
      AD.cod_tipo = TA.cod_tipo
  AND ACA.ativo = true
  AND     AD.ativo
  AND AD.cod_atributo =  ACA.cod_atributo
  AND AD.cod_modulo   = ACA.cod_modulo
  AND AD.cod_cadastro = ACA.cod_cadastro
  AND ACA.cod_cadastro=2
  AND ACA.cod_modulo  =12


 AND ACA.cod_atributo = 8;'''';OPEN crCursor FOR EXECUTE stSql;
                                    FETCH crCursor INTO rsRetorno;
                                    CLOSE crCursor;
                                    RETURN rsRetorno.valor;
                                    END;
                                    '' LANGUAGE plpgsql;

                 ';
    EXECUTE stSQL;

-- CalculaTFF2010
UPDATE administracao.corpo_funcao_externa SET linha = ' SE       (  #stIsento  =  "2"  )  OU  (  #inAtividade  =  704208  )  OU  (  #inAtividade  =  704209  )  OU  (  #inAtividade  =  704207  )  OU  (  #inAtividade  =  704202  )  OU  (  #inAtividade  =  704201  )  OU  (  #inAtividade  =  704203  )  OU  (  #inAtividade  =  704546  ) ENTAO ' WHERE cod_modulo = 14 AND cod_biblioteca =  2 AND cod_funcao = 48 AND cod_linha = 14;
UPDATE administracao.funcao_externa SET     cod_modulo= 14,    cod_biblioteca= 2,    cod_funcao= 48,    comentario=
    '',    corpo_pl=
    'FUNCTION  CalculaTFF2010() RETURNS NUMERIC as ''
DECLARE

  inAtividade INTEGER;
  inExercicio INTEGER;
  inInscricao INTEGER;
  nuAtrbiuto NUMERIC;
  nuValor NUMERIC;
  nuValorTLF NUMERIC;
  stAtributo VARCHAR := '''''''';
  stIsento VARCHAR := '''''''';
  stTipoInscricao VARCHAR := '''''''';
  stValor VARCHAR := '''''''';
BEGIN
inExercicio := RecuperarBufferInteiro(  ''''inExercicio''''  );
inInscricao := RecuperarBufferInteiro(  ''''inRegistro''''  );
inAtividade := buscaCodigoAtividadeDaInscricaoEconomica(  inInscricao , 1  );
stTipoInscricao := buscaTipoDaInscricaoEconomica(  inInscricao  );
IF   stTipoInscricao  =  ''''direito'''' THEN
    stIsento := recuperaCadastroEconomicoInscricaoEconomicaEmpresaDeDireitoIsentoDeTaxas(  inInscricao  );
END IF;
IF   stTipoInscricao  =  ''''fato'''' THEN
    stIsento := recuperaCadastroEconomicoInscricaoEconomicoEmpresaDeFatoIsentoDeTaxas(  inInscricao  );
END IF;
IF   stTipoInscricao  =  ''''autonomo'''' THEN
    stIsento := recuperaCadastroEconomicoInscricaoEconomicaAutonomoIsentoDeTaxas(  inInscricao  );
END IF;
     IF       (  stIsento  =  ''''2''''  )  OR  (  inAtividade  =  704208  )  OR  (  inAtividade  =  704209  )  OR  (  inAtividade  =  704207  )  OR  (  inAtividade  =  704202  )  OR  (  inAtividade  =  704201  )  OR  (  inAtividade  =  704203  )  OR  (  inAtividade  =  704546  ) THEN
    nuValorTLF := 0;
ELSE
nuValor := buscaAliquotaAtividade(  inAtividade  );
nuValorTLF := nuValor;
END IF;
RETURN nuValorTLF;
END;
 '' LANGUAGE ''plpgsql'';
',    corpo_ln=
    '' WHERE cod_modulo = 14 AND cod_biblioteca = 2 AND cod_funcao = 48 ;


-- CalculaTFF
UPDATE administracao.corpo_funcao_externa SET linha = ' SE     (  #stIsento  =  "2"  )  OU  (  #inAtividade  =  704208  )  OU  (  #inAtividade  =  704209  )  OU  (  #inAtividade  =  704207  ) ENTAO ' WHERE cod_modulo = 14 AND cod_biblioteca =  2 AND cod_funcao = 32 AND cod_linha = 14;
UPDATE administracao.funcao_externa SET     cod_modulo= 14,    cod_biblioteca= 2,    cod_funcao= 32,    comentario=
    '',    corpo_pl=
    'FUNCTION  CalculaTFF() RETURNS NUMERIC as ''
DECLARE

  inAtividade INTEGER;
  inExercicio INTEGER;
  inInscricao INTEGER;
  nuAtrbiuto NUMERIC;
  nuValor NUMERIC;
  nuValorTLF NUMERIC;
  stAtributo VARCHAR := '''''''';
  stIsento VARCHAR := '''''''';
  stTipoInscricao VARCHAR := '''''''';
  stValor VARCHAR := '''''''';
BEGIN
inExercicio := RecuperarBufferInteiro(  ''''inExercicio''''  );
inInscricao := RecuperarBufferInteiro(  ''''inRegistro''''  );
inAtividade := buscaCodigoAtividadeDaInscricaoEconomica(  inInscricao , 1  );
stTipoInscricao := buscaTipoDaInscricaoEconomica(  inInscricao  );
IF   stTipoInscricao  =  ''''direito'''' THEN
    stIsento := recuperaCadastroEconomicoInscricaoEconomicaEmpresaDeDireitoIsentoDeTaxas(  inInscricao  );
END IF;
IF   stTipoInscricao  =  ''''fato'''' THEN
    stIsento := recuperaCadastroEconomicoInscricaoEconomicoEmpresaDeFatoIsentoDeTaxas(  inInscricao  );
END IF;
IF   stTipoInscricao  =  ''''autonomo'''' THEN
    stIsento := recuperaCadastroEconomicoInscricaoEconomicaAutonomoIsentoDeTaxas(  inInscricao  );
END IF;
     IF     (  stIsento  =  ''''2''''  )  OR  (  inAtividade  =  704208  )  OR  (  inAtividade  =  704209  )  OR  (  inAtividade  =  704207  ) THEN
    nuValorTLF := 0;
ELSE
nuValor := buscaAliquotaAtividade(  inAtividade  );
nuValorTLF := nuValor;
END IF;
RETURN nuValorTLF;
END;
 '' LANGUAGE ''plpgsql'';
',    corpo_ln=
    '' WHERE cod_modulo = 14 AND cod_biblioteca = 2 AND cod_funcao = 32 ;


-- CalculaImpostoPredial
UPDATE administracao.corpo_funcao_externa SET linha = ' SE   #stDescontoVenal  =  "2" ENTAO ' WHERE cod_modulo = 12 AND cod_biblioteca =  2 AND cod_funcao = 20 AND cod_linha = 25;
UPDATE administracao.funcao_externa SET     cod_modulo= 12,    cod_biblioteca= 2,    cod_funcao= 20,    comentario=
    'Cálculo Imposto Predial',    corpo_pl=
    'FUNCTION  CalculaImpostoPredial() RETURNS NUMERIC as ''
DECLARE

  boEdificacao BOOLEAN;
  boErro BOOLEAN;
  inExercicio INTEGER;
  inImovel INTEGER;
  inLote INTEGER;
  nuAliquota NUMERIC;
  nuArea NUMERIC;
  nuAreaConstrucao NUMERIC;
  nuAreaConstruidaDescoberta NUMERIC;
  nuImpostoPredial NUMERIC;
  nuIsento NUMERIC;
  nuTmp NUMERIC;
  nuUsoSolo NUMERIC;
  nuVenalCalculo NUMERIC;
  nuVenalTerritorial NUMERIC;
  nuVenalTotal NUMERIC;
  nuVenapAreaDescoberta NUMERIC;
  nuVupConstrucao NUMERIC;
  stAreaConstruidaDescoberta VARCHAR := '''''''';
  stDescontoVenal VARCHAR := '''''''';
  stIsento VARCHAR := '''''''';
  stPadrao VARCHAR := '''''''';
  stUsoSolo VARCHAR := '''''''';
BEGIN
inImovel := RecuperarBufferInteiro(  ''''inRegistro''''  );
inExercicio := RecuperarBufferInteiro(  ''''inExercicio''''  );
boEdificacao := arrecadacao.verificaEdificacaoImovel(  inImovel  );
stIsento := recuperaCadastroImobiliarioImovelIsencaoIPTU(  inImovel  );
nuIsento := arrecadacao.fn_vc2num(  stIsento  );
inLote := imobiliario.fn_busca_lote_imovel(  inImovel  );
stDescontoVenal := recuperaCadastroImobiliarioLoteUrbanoDescontoValorVenal(  inLote  );
IF     nuIsento  =   1 THEN
nuImpostoPredial := 0.00;
ELSE
    nuVenalTerritorial := arrecadacao.fn_busca_valor_venal_territorial_calculado(  inImovel  );
IF   boEdificacao  =  TRUE THEN
    nuAreaConstrucao := imobiliario.fn_calcula_area_imovel(  inImovel  );
    stPadrao := atributoTipoEdificacaoValorClassificacaoEdificacao(  inImovel  );
    nuVupConstrucao := arrecadacao.fn_busca_tabela_conversao(  30 , inExercicio , stPadrao , '''''''' , '''''''' , ''''''''  );
    nuTmp := nuAreaConstrucao*nuVupConstrucao ;
    stAreaConstruidaDescoberta := recuperaCadastroImobiliarioImovelAreaTotalDescoberta(  inImovel  );
    nuAreaConstruidaDescoberta := arrecadacao.fn_vc2num(  stAreaConstruidaDescoberta  );
    nuVenapAreaDescoberta := nuAreaConstruidaDescoberta*(nuVupConstrucao /2);
    nuTmp := nuTmp+nuVenapAreaDescoberta ;
    nuVenalTotal := nuTmp+nuVenalTerritorial ;
    boErro := arrecadacao.fn_atualiza_venal(  inImovel , nuTmp , nuVenalTotal , inExercicio  );
    stUsoSolo := recuperaCadastroImobiliarioImovelUsoDoSolo(  inImovel  );
    nuUsoSolo := arrecadacao.fn_vc2num(  stUsoSolo  );
         IF   stDescontoVenal  =  ''''2'''' THEN
        nuVenalCalculo := nuTmp*0.5;
    ELSE
        nuVenalCalculo := nuTmp;
    END IF;
    IF   nuUsoSolo  =  2 THEN
        nuAliquota := arrecadacao.fn_busca_tabela_conversao(  15 , inExercicio , ''''Construção'''' , ''''true'''' , '''''''' , ''''''''  );
    ELSE
        nuAliquota := arrecadacao.fn_busca_tabela_conversao(  15 , inExercicio , ''''Construção'''' , '''''''' , ''''true'''' , ''''''''  );
    END IF;
        nuImpostoPredial := (nuVenalCalculo *nuAliquota )/100;
ELSE
    nuImpostoPredial := 0;
    nuTmp := 0;
    nuVenalTotal := nuTmp+nuVenalTerritorial ;
    boErro := arrecadacao.fn_atualiza_venal(  inImovel , nuTmp , nuVenalTotal , inExercicio  );
END IF;
END IF;
RETURN nuImpostoPredial;
END;
 '' LANGUAGE ''plpgsql'';
',    corpo_ln=
    '' WHERE cod_modulo = 12 AND cod_biblioteca = 2 AND cod_funcao = 20 ;


-- CalculaImpostoTerritorial
UPDATE administracao.corpo_funcao_externa SET linha = ' SE   #stDescontoVenal  =  "2" ENTAO ' WHERE cod_modulo = 12 AND cod_biblioteca =  2 AND cod_funcao = 19 AND cod_linha = 33;
UPDATE administracao.corpo_funcao_externa SET linha = ' SE         #stLimitacao  =  "1" ENTAO ' WHERE cod_modulo = 12 AND cod_biblioteca =  2 AND cod_funcao = 19 AND cod_linha = 46;
UPDATE administracao.funcao_externa SET     cod_modulo= 12,    cod_biblioteca= 2,    cod_funcao= 19,    comentario=
    'Função para Calcular Imposto Territorial',    corpo_pl=
    'FUNCTION  CalculaImpostoTerritorial() RETURNS NUMERIC as ''
DECLARE

  boEdificacao BOOLEAN;
  boErro BOOLEAN;
  inExercicio INTEGER;
  inImovel INTEGER;
  inLote INTEGER;
  inQuantidade INTEGER;
  nuAliquota NUMERIC;
  nuAlíquotaLimitacao NUMERIC;
  nuArea NUMERIC;
  nuAreaImoveisLote NUMERIC;
  nuAreaImovel NUMERIC;
  nuAreaLote NUMERIC;
  nuFracaoIdeal NUMERIC;
  nuImpostoTerritorial NUMERIC;
  nuIsento NUMERIC;
  nuLimitacão NUMERIC;
  nuRetorno NUMERIC;
  nuTmp NUMERIC;
  nuUsoSolo NUMERIC;
  nuVenalCalculo NUMERIC;
  nuVupTerreno NUMERIC;
  stDescontoVenal VARCHAR := '''''''';
  stErro VARCHAR := '''''''';
  stIsento VARCHAR := '''''''';
  stLimitacao VARCHAR := '''''''';
  stUsoSolo VARCHAR := '''''''';
  stVupTerreno VARCHAR := '''''''';
BEGIN
inExercicio := RecuperarBufferInteiro(  ''''inExercicio''''  );
inImovel := RecuperarBufferInteiro(  ''''inRegistro''''  );
nuArea := imobiliario.fn_area_real(  inImovel  );
nuAreaLote := imobiliario.fn_area_real(  inImovel  );
inQuantidade := recuperaQuantidadeImovelPorLote(  inImovel  );
stVupTerreno := recuperaTrechoValorMetroQuadradoTerritorialExercicio(  inImovel , inExercicio  );
nuVupTerreno := arrecadacao.fn_vc2num(  stVupTerreno  );
stUsoSolo := recuperaCadastroImobiliarioImovelUsoDoSolo(  inImovel  );
nuUsoSolo := arrecadacao.fn_vc2num(  stUsoSolo  );
stLimitacao := recuperaCadastroImobiliarioImovelLimitacao(  inImovel  );
nuLimitacão := arrecadacao.fn_vc2num(  stLimitacao  );
boEdificacao := arrecadacao.verificaEdificacaoImovel(  inImovel  );
stIsento := recuperaCadastroImobiliarioImovelIsencaoIPTU(  inImovel  );
nuIsento := arrecadacao.fn_vc2num(  stIsento  );
inLote := imobiliario.fn_busca_lote_imovel(  inImovel  );
stDescontoVenal := recuperaCadastroImobiliarioLoteUrbanoDescontoValorVenal(  inLote  );
IF     nuIsento  =  1 THEN
    nuImpostoTerritorial := 0.00;
    boErro := arrecadacao.fn_grava_venal(  inImovel , nuImpostoTerritorial , 0.00 , 0.00 , inExercicio  );
ELSE
    IF   inQuantidade  >  1 THEN
        nuAreaImoveisLote := imobiliario.fn_calcula_area_imovel_lote(  inImovel  );
        IF   nuAreaImoveisLote  >  0 THEN
            nuFracaoIdeal := nuAreaLote/inQuantidade ;
        ELSE
        nuAreaImovel := imobiliario.fn_calcula_area_imovel(  inImovel  );
        nuFracaoIdeal := (nuAreaImovel *nuAreaLote )/nuAreaImoveisLote ;
        END IF;
        nuTmp := nuFracaoIdeal*nuVupTerreno ;
    ELSE
        nuTmp := nuArea*nuVupTerreno ;
    END IF;
         IF   stDescontoVenal  =  ''''2'''' THEN
        nuVenalCalculo := nuTmp*0.5;
    ELSE
        nuVenalCalculo := nuTmp;
    END IF;
    boErro := arrecadacao.fn_grava_venal(  inImovel , nuTmp , 0.00 , 0.00 , inExercicio  );
    IF     boEdificacao  =   ''''true'''' THEN
        IF   nuUsoSolo  =  2 THEN
            nuLimitacão := arrecadacao.fn_busca_tabela_conversao(  15 , inExercicio , ''''Construção'''' , ''''true'''' , '''''''' , ''''''''  );
        ELSE
            nuLimitacão := arrecadacao.fn_busca_tabela_conversao(  15 , inExercicio , ''''Construção'''' , '''''''' , ''''true'''' , ''''''''  );
        END IF;
    ELSE
             IF         stLimitacao  =  ''''1'''' THEN
            nuLimitacão := arrecadacao.fn_busca_tabela_conversao(  14 , inExercicio , ''''Terreno'''' , ''''true'''' , '''''''' , ''''''''  );
        ELSE
            nuLimitacão := arrecadacao.fn_busca_tabela_conversao(  14 , inExercicio , ''''Terreno'''' , '''''''' , ''''true'''' , ''''''''  );
        END IF;
    END IF;
nuImpostoTerritorial := (nuVenalCalculo *nuLimitacão )/100;
END IF;
RETURN nuImpostoTerritorial;
END;
 '' LANGUAGE ''plpgsql'';
',    corpo_ln=
    '' WHERE cod_modulo = 12 AND cod_biblioteca = 2 AND cod_funcao = 19 ;



UPDATE administracao.funcao_externa SET     cod_modulo= 33,    cod_biblioteca= 2,    cod_funcao= 1,    comentario=
    '',    corpo_pl=
    'FUNCTION  regraAcrescimoGeralDivida(INTEGER) RETURNS BOOLEAN as ''
DECLARE
inRegistro ALIAS FOR $1;

  boRetorno BOOLEAN := TRUE;
BEGIN
RETURN boRetorno;
END;
 '' LANGUAGE ''plpgsql'';
',    corpo_ln=
    '' WHERE cod_modulo = 33 AND cod_biblioteca = 2 AND cod_funcao = 1 ;

END;
$$ LANGUAGE 'plpgsql';



CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE

BEGIN
    -- ITAU
    PERFORM 1
       FROM administracao.configuracao
      WHERE cod_modulo = 2
        AND exercicio  = '2013'
        AND parametro  = 'cnpj'
        AND valor      = '08148553000106'
          ;
    IF FOUND THEN
        PERFORM manutencao_itau();
    END IF;

    -- MANAQUIRI
    PERFORM 1
       FROM administracao.configuracao
      WHERE cod_modulo = 2
        AND exercicio  = '2013'
        AND parametro  = 'cnpj'
        AND valor      = '04641551000195'
          ;
    IF FOUND THEN
        PERFORM manutencao_manaquiri();
    END IF;

    -- MARIANA
    PERFORM 1
       FROM administracao.configuracao
      WHERE cod_modulo = 2
        AND exercicio  = '2013'
        AND parametro  = 'cnpj'
        AND valor      = '94068418000184'
          ;
    IF FOUND THEN
        PERFORM manutencao_mariana();
    END IF;

    -- MATA
    PERFORM 1
       FROM administracao.configuracao
      WHERE cod_modulo = 2
        AND exercicio  = '2013'
        AND parametro  = 'cnpj'
        AND valor      = '13805528000180'
          ;
    IF FOUND THEN
        PERFORM manutencao_mata();
    END IF;
END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();

DROP FUNCTION manutencao_itau();
DROP FUNCTION manutencao_manaquiri();
DROP FUNCTION manutencao_mariana();
DROP FUNCTION manutencao_mata();


CREATE OR REPLACE FUNCTION recriaPlGeradorCalculo() RETURNS void AS $$
DECLARE
    stSQL       VARCHAR;
    reRecord    RECORD;

    stPlSql VARCHAR;
BEGIN
    stSQL := '
                  SELECT ''CREATE OR REPLACE '' || REPLACE(funcao_externa.corpo_pl, E''\x5C''||E''\x27'', E''\x27'') AS corpo_pl
                   FROM administracao.funcao
                   JOIN administracao.funcao_externa
                     ON funcao_externa.cod_funcao     = funcao.cod_funcao
                    AND funcao_externa.cod_biblioteca = funcao.cod_biblioteca
                    AND funcao_externa.cod_modulo     = funcao.cod_modulo
                   JOIN administracao.corpo_funcao_externa
                     ON corpo_funcao_externa.cod_funcao     = funcao.cod_funcao
                    AND corpo_funcao_externa.cod_biblioteca = funcao.cod_biblioteca
                    AND corpo_funcao_externa.cod_modulo     = funcao.cod_modulo
                  WHERE funcao.cod_modulo IN (12, 14, 25, 28, 33)
               GROUP BY funcao_externa.corpo_pl
                      ;
             ';
    FOR reRecord IN EXECUTE stSQL LOOP
        stPlSql := reRecord.corpo_pl;
        EXECUTE stPlSql;
    END LOOP;
END;
$$ LANGUAGE plpgsql;

SELECT        recriaPlGeradorCalculo();
DROP FUNCTION recriaPlGeradorCalculo();


----------------
-- Ticket #16460
----------------

UPDATE administracao.funcao_externa
   SET corpo_pl = 'FUNCTION desoneracaoAbono(INTEGER,INTEGER,NUMERIC) RETURNS NUMERIC as '''''' \r
 DECLARE\r
 inCodDesoneracao ALIAS FOR $1;\r
 inNumCgm ALIAS FOR $2;\r
 nuValorLancamento ALIAS FOR $3;\r
 \r
   nuRetorno NUMERIC;\r
   nuValorAbono NUMERIC;\r
   nuValorLancado NUMERIC;\r
   stValorAbono VARCHAR := '''''''''''''''''''''''';\r
 BEGIN\r
 stValorAbono := recuperaArrecadacaoDesoneracaoAbono( inCodDesoneracao , inNumCgm ););\r
 nuValorAbono := arrecadacao.fn_vc2num(  stValorAbono  ); \r
 nuRetorno := nuValorLancamento-nuValorAbono ;\r
 RETURN nuRetorno;\r
 END;\r
  '''''' LANGUAGE ''''''plpgsql''''''; \r
 '
 WHERE cod_modulo     = 25
   AND cod_biblioteca = 3
   AND cod_funcao     = 40
     ;

UPDATE administracao.corpo_funcao_externa
   SET linha = '#nuValorAbono <- arrecadacao.fn_vc2num(  #stValorAbono  ); '
 WHERE cod_modulo = 25
   AND cod_biblioteca = 3
   AND cod_funcao = 40
   AND cod_linha = 2
     ;

CREATE OR REPLACE FUNCTION desoneracaoAbono(INTEGER,INTEGER,NUMERIC) RETURNS NUMERIC as '
DECLARE
    inCodDesoneracao ALIAS FOR $1;
    inNumCgm ALIAS FOR $2;
    nuValorLancamento ALIAS FOR $3;

    nuRetorno NUMERIC;
    nuValorAbono NUMERIC;
    nuValorLancado NUMERIC;
    stValorAbono VARCHAR := '''';
BEGIN
    stValorAbono := recuperaArrecadacaoDesoneracaoAbono( inCodDesoneracao , inNumCgm );
    nuValorAbono := arrecadacao.fn_vc2num(  stValorAbono  );
    nuRetorno := nuValorLancamento-nuValorAbono ;
    RETURN nuRetorno;
END;
' LANGUAGE 'plpgsql';

