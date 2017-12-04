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
* Script de DDL e DML
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id: GT_1971.sql 59612 2014-09-02 12:00:51Z gelson $
*
* Versão 1.97.1
*/


----------------
-- Ticket #14465
----------------

ALTER TABLE fiscalizacao.nota          ALTER COLUMN nro_serie TYPE VARCHAR(10);
ALTER TABLE fiscalizacao.retencao_nota ALTER COLUMN num_serie TYPE VARCHAR(10);


----------------
-- Ticket #14457
----------------

CREATE OR REPLACE FUNCTION manutencao(  ) RETURNS INTEGER AS '
DECLARE
    inNumParcelamento   INTEGER;

    inContadorParcelas  INTEGER;
    inMaxNumParcela     INTEGER;
    inMinNumParcela     INTEGER;

    stSQL               VARCHAR;
    reRECORD            RECORD;
    reRECORD2           RECORD;

    inCount             INTEGER := 1;
    inRetorno           INTEGER := 0;

BEGIN

    -- DROP DAS PKs e FKs
    ALTER TABLE divida.parcela_calculo   DROP CONSTRAINT fk_parcela_calculo_1;
    ALTER TABLE divida.parcela_calculo   DROP CONSTRAINT fk_parcela_calculo_3;
    ALTER TABLE divida.parcela_calculo   DROP CONSTRAINT pk_parcela_calculo;

    ALTER TABLE divida.parcela_acrescimo DROP CONSTRAINT fk_divida_parcela_acrescimo_1;
    ALTER TABLE divida.parcela_acrescimo DROP CONSTRAINT fk_divida_parcela_acrescimo_2;
    ALTER TABLE divida.parcela_acrescimo DROP CONSTRAINT pk_divida_parcela_acrescimo;

    ALTER TABLE divida.parcela_reducao   DROP CONSTRAINT fk_parcela_reducao_1;
    ALTER TABLE divida.parcela_reducao   DROP CONSTRAINT pk_parcela_reducao;

    ALTER TABLE divida.documento_parcela DROP CONSTRAINT fk_documento_parcela_1;
    ALTER TABLE divida.documento_parcela DROP CONSTRAINT fk_documento_parcela_2;
    ALTER TABLE divida.documento_parcela DROP CONSTRAINT pk_documento_parcela;

    ALTER TABLE divida.parcela           DROP CONSTRAINT pk_parcela;



    stSQL := ''     SELECT DP.num_parcelamento
                      FROM divida.parcelamento      AS DP
                INNER JOIN divida.parcela           AS DPP
                        ON DPP.num_parcelamento = DP.num_parcelamento 
--                     AND DPP.num_parcelamento = 617
                  GROUP BY DP.num_parcelamento '' ;


    FOR reRECORD IN EXECUTE stSQL LOOP

                SELECT MAX(num_parcela)
                  INTO inMaxNumParcela
                  FROM divida.parcela
                 WHERE num_parcelamento = reRECORD.num_parcelamento;


                SELECT MIN(num_parcela)
                  INTO inMinNumParcela
                  FROM divida.parcela
                 WHERE num_parcelamento = reRECORD.num_parcelamento;

                
                SELECT COUNT(num_parcela)
                  INTO inContadorParcelas
                  FROM divida.parcela
                 WHERE num_parcelamento = reRECORD.num_parcelamento;




                IF inMaxNumParcela > inContadorParcelas THEN

                        inRetorno := inRetorno + 1;

                        RAISE NOTICE '' inMaxNumParcela    = % '',inMaxNumParcela;
                        RAISE NOTICE '' inContadorParcelas = % '',inContadorParcelas;

                                inCount := 1;

                                -- ATUALIZACAO DO NUM_PARCELA
                                stSQL  := '' SELECT num_parcela
                                               FROM divida.parcela
                                              WHERE num_parcelamento = '' || reRECORD.num_parcelamento || ''
                                           ORDER BY num_parcela '';
                            
                                FOR reRECORD2 IN EXECUTE stSQL LOOP -- LOOP DAS PARCELAS
                           
                                    RAISE NOTICE '' num_parcela      = % '',reRECORD2.num_parcela;
                                    RAISE NOTICE '' num_parcelamento = % '',reRECORD.num_parcelamento;
 
                                    UPDATE divida.parcela
                                       SET num_parcela      = inCount
                                     WHERE num_parcela      = reRECORD2.num_parcela
                                       AND num_parcelamento = reRECORD.num_parcelamento;
                            
                                    UPDATE divida.parcela_calculo
                                       SET num_parcela      = inCount
                                     WHERE num_parcela      = reRECORD2.num_parcela
                                       AND num_parcelamento = reRECORD.num_parcelamento;
                            
                                    UPDATE divida.parcela_reducao
                                       SET num_parcela      = inCount
                                     WHERE num_parcela      = reRECORD2.num_parcela
                                       AND num_parcelamento = reRECORD.num_parcelamento;
                            
                                    UPDATE divida.parcela_acrescimo
                                       SET num_parcela      = inCount
                                     WHERE num_parcela      = reRECORD2.num_parcela
                                       AND num_parcelamento = reRECORD.num_parcelamento;
                            
                                    UPDATE divida.documento_parcela
                                       SET num_parcela      = inCount
                                     WHERE num_parcela      = reRECORD2.num_parcela
                                       AND num_parcelamento = reRECORD.num_parcelamento;
                            
                                    inCount := inCount + 1;
                            
                                END LOOP;

                END IF;

    END LOOP; --LOOP DOS PARCELAMENTOS


    -- RECRIACAO DAS PKs e FKs
    ALTER TABLE divida.parcela           ADD  CONSTRAINT pk_parcela PRIMARY KEY (num_parcelamento, num_parcela);

    ALTER TABLE divida.parcela_calculo   ADD CONSTRAINT pk_parcela_calculo              PRIMARY KEY                (num_parcelamento, num_parcela, cod_calculo);
    ALTER TABLE divida.parcela_calculo   ADD CONSTRAINT fk_parcela_calculo_1            FOREIGN KEY                (num_parcelamento, num_parcela) 
                                                                                        REFERENCES divida.parcela  (num_parcelamento, num_parcela);
    ALTER TABLE divida.parcela_calculo   ADD CONSTRAINT fk_parcela_calculo_3            FOREIGN KEY                   (cod_calculo) 
                                                                                        REFERENCES arrecadacao.calculo(cod_calculo);

    ALTER TABLE divida.parcela_acrescimo ADD CONSTRAINT pk_divida_parcela_acrescimo     PRIMARY KEY                (num_parcelamento, num_parcela, cod_tipo, cod_acrescimo);
    ALTER TABLE divida.parcela_acrescimo ADD CONSTRAINT fk_divida_parcela_acrescimo_1   FOREIGN KEY                (num_parcelamento, num_parcela) 
                                                                                        REFERENCES divida.parcela  (num_parcelamento, num_parcela);
    ALTER TABLE divida.parcela_acrescimo ADD CONSTRAINT fk_divida_parcela_acrescimo_2   FOREIGN KEY                   (cod_acrescimo, cod_tipo) 
                                                                                        REFERENCES monetario.acrescimo(cod_acrescimo, cod_tipo);

    ALTER TABLE divida.parcela_reducao   ADD CONSTRAINT pk_parcela_reducao              PRIMARY KEY                (num_parcelamento, num_parcela);
    ALTER TABLE divida.parcela_reducao   ADD CONSTRAINT fk_parcela_reducao_1            FOREIGN KEY                (num_parcelamento, num_parcela) 
                                                                                        REFERENCES divida.parcela  (num_parcelamento, num_parcela);

    ALTER TABLE divida.documento_parcela ADD CONSTRAINT pk_documento_parcela            PRIMARY KEY                (num_parcelamento, cod_tipo_documento, cod_documento, num_parcela);
    ALTER TABLE divida.documento_parcela ADD CONSTRAINT fk_documento_parcela_1          FOREIGN KEY                (num_parcelamento, cod_tipo_documento, cod_documento) 
                                                                                        REFERENCES divida.documento(num_parcelamento, cod_tipo_documento, cod_documento);
    ALTER TABLE divida.documento_parcela ADD CONSTRAINT fk_documento_parcela_2          FOREIGN KEY                (num_parcelamento, num_parcela) 
                                                                                        REFERENCES divida.parcela (num_parcelamento, num_parcela);


    RETURN inRetorno;
END;

' LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();


UPDATE
    divida.parcela
set paga=true
from  
    (
    select
        distinct
        div_parcela.num_parcelamento
        ,div_parcela.num_parcela
        ,pagamento.numeracao
        ,pagamento.cod_convenio
    from
        divida.parcelamento
    inner join
        divida.parcela as div_parcela
    on
        parcelamento.num_parcelamento = div_parcela.num_parcelamento
    inner join
        divida.parcela_origem
    on
        parcelamento.num_parcelamento = parcela_origem.num_parcelamento
    left join
        divida.parcelamento_cancelamento
    on
        parcelamento.num_parcelamento = parcelamento_cancelamento.num_parcelamento
    inner join
        divida.parcela_calculo
    on
        div_parcela.num_parcelamento = parcela_calculo.num_parcelamento
        and div_parcela.num_parcela =  parcela_calculo.num_parcela
    inner join
        arrecadacao.calculo
    on
        parcela_calculo.cod_calculo = calculo.cod_calculo
    inner join
        arrecadacao.lancamento_calculo
    on
        calculo.cod_calculo = lancamento_calculo.cod_calculo
    inner join
        arrecadacao.parcela as arr_parcela
    on
        lancamento_calculo.cod_lancamento = arr_parcela.cod_lancamento
        and parcela_calculo.num_parcela = arr_parcela.nr_parcela
    inner join
        arrecadacao.carne
    on
        arr_parcela.cod_parcela=carne.cod_parcela
    left join
        arrecadacao.carne_devolucao
    on
        carne.numeracao = carne_devolucao.numeracao
        and carne.cod_convenio =  carne_devolucao.cod_convenio
    inner join
        arrecadacao.pagamento
    on
        pagamento.numeracao = carne.numeracao
        and pagamento.cod_convenio = carne.cod_convenio
    left join
        arrecadacao.tipo_pagamento
    on
        pagamento.cod_tipo = tipo_pagamento.cod_tipo
    where
        div_parcela.paga = false
        and carne_devolucao.numeracao is null
        and tipo_pagamento.pagamento = true
        and parcelamento_cancelamento.num_parcelamento is null
    ) as pdsb
where
    pdsb.num_parcelamento = parcela.num_parcelamento
    and pdsb.num_parcela = parcela.num_parcela;



----------------
-- Ticket #14299
----------------

CREATE OR REPLACE FUNCTION correcaoNumDocLicenca() RETURNS INTEGER AS $$
DECLARE
    stSQL               VARCHAR;
    reRECORD            RECORD;
    inRetorno           INTEGER;

BEGIN
    inRetorno := 0;

    stSQL := '
        SELECT
            cod_licenca,
            exercicio

        FROM
            economico.licenca_documento
    ';

    FOR reRECORD IN EXECUTE stSQL LOOP
        UPDATE
            economico.licenca_documento
        SET
            num_alvara = reRECORD.cod_licenca
        WHERE
            cod_licenca = reRECORD.cod_licenca
            AND exercicio = reRECORD.exercicio;

        inRetorno := inRetorno + 1;
    END LOOP;

    RETURN inRetorno;
END;

$$ LANGUAGE 'plpgsql';

select correcaoNumDocLicenca();
DROP FUNCTION correcaoNumDocLicenca();


----------------
-- Ticket #14299
----------------

INSERT INTO administracao.configuracao
          ( cod_modulo
          , exercicio
          , parametro
          , valor
          )
     VALUES ( 14
          , '2009'
          , 'nro_alvara_licenca'
          , 'Exercicio'
          );


------------------------------------------------------
-- CORRECAO DE acao E documentos - MODULO FISCALIZACAO
------------------------------------------------------

UPDATE administracao.acao
   SET nom_acao = 'Emitir Notificação Fiscal'
 WHERE cod_acao = 2305;

DELETE
  FROM administracao.modelo_arquivos_documento
 WHERE cod_acao      = 2305
   AND cod_documento = ( SELECT cod_documento
                           FROM administracao.modelo_documento
                          WHERE nome_documento = 'Auto de Infração'
                       );

DELETE
  FROM administracao.modelo_arquivos_documento
 WHERE cod_acao      = 2304
   AND cod_documento = ( SELECT cod_documento
                           FROM administracao.modelo_documento
                          WHERE nome_documento = 'Notificação de Processo Fiscal'
                       );

INSERT
  INTO administracao.modelo_arquivos_documento
VALUES ( 2305
     , ( SELECT cod_documento
           FROM administracao.modelo_documento 
          WHERE nome_documento   = 'Notificação de Processo Fiscal' )
     , ( SELECT MAX (cod_arquivo)
           FROM administracao.arquivos_documento
          WHERE nome_arquivo_swx = 'notificacao_processo_fiscal.odt' )
     , true
     , true
     ,4
     );


INSERT
  INTO administracao.modelo_arquivos_documento
VALUES ( 2275
     , ( SELECT cod_documento
           FROM administracao.modelo_documento 
          WHERE nome_documento   = 'Auto de Infração' )
     , ( SELECT MAX (cod_arquivo)
           FROM administracao.arquivos_documento
          WHERE nome_arquivo_swx = 'auto_infracao.odt' )
     , true
     , true
     ,4
     );

DELETE
  FROM administracao.permissao
 WHERE cod_acao = 2312;

DELETE
  FROM administracao.auditoria
 WHERE cod_acao = 2312;

DELETE
  FROM administracao.acao
 WHERE cod_acao = 2312;
