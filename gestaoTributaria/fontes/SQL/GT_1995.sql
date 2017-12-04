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
* $Id:$
*
* Versão 1.99.5
*/

-------------------------------------------------------------------
-- Correcao na regra de desoneracao de IPTU 2011 - Mata de Sao Joao
-------------------------------------------------------------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE
    inMaxModelo     INTEGER;
BEGIN

    PERFORM 1
       FROM administracao.configuracao
      WHERE exercicio  = '2011'
        AND cod_modulo = 2
        AND parametro  = 'cnpj'
        AND valor      = '13805528000180'
          ;

    IF FOUND THEN

        UPDATE administracao.corpo_funcao_externa SET linha = '#nuValorCred2 <- arrecadacao.buscaValorCalculoCredito(  #inRegistro , 261 , "2011" , 2 , 1 , 1 , 1  );' WHERE cod_modulo = 25 AND cod_biblioteca = 2 AND cod_funcao = 101 AND cod_linha = 3;
        UPDATE administracao.corpo_funcao_externa SET linha = '#nuValorCred3 <- arrecadacao.buscaValorCalculoCredito(  #inRegistro , 261 , "2011" , 3 , 1 , 1 , 1  );' WHERE cod_modulo = 25 AND cod_biblioteca = 2 AND cod_funcao = 101 AND cod_linha = 4;

        UPDATE administracao.funcao_externa SET corpo_pl = 'FUNCTION  regraConcessaoDesoneracaoIPTU2011(INTEGER) RETURNS BOOLEAN as \'
                                                            DECLARE
                                                            inRegistro ALIAS FOR $1;

                                                              boRetorno BOOLEAN;
                                                              nuLancamento NUMERIC;
                                                              nuUsoSolo NUMERIC;
                                                              nuValorCred2 NUMERIC;
                                                              nuValorCred3 NUMERIC;
                                                              stUsoSolo VARCHAR := \'\'\'\';
                                                            BEGIN
                                                            stUsoSolo := recuperaCadastroImobiliarioImovelUsoDoSolo(  inRegistro  );
                                                            nuUsoSolo := arrecadacao.fn_vc2num(  stUsoSolo  );
                                                            nuValorCred2 := arrecadacao.buscaValorCalculoCredito(  inRegistro , 261 , \'\'2011\'\' , 2 , 1 , 1 , 1  );
                                                            nuValorCred3 := arrecadacao.buscaValorCalculoCredito(  inRegistro , 261 , \'\'2011\'\' , 3 , 1 , 1 , 1  );
                                                            nuLancamento := nuValorCred2+nuValorCred3 ;
                                                            IF       (  nuUsoSolo  =  2  )  AND  (  nuLancamento  <    69  ) THEN
                                                                boRetorno := TRUE;
                                                            ELSE
                                                                IF       (  nuUsoSolo  =  1  )  AND  (  nuLancamento  <    27  ) THEN
                                                                    boRetorno := TRUE;
                                                                ELSE
                                                                    boRetorno := FALSE;
                                                                END IF;
                                                            END IF;
                                                            RETURN boRetorno;
                                                            END;
                                                             \' LANGUAGE \'plpgsql\';' 
        WHERE cod_modulo = 25 AND cod_biblioteca = 2 AND cod_funcao = 101;


    END IF;

END;
$$ LANGUAGE 'plpgsql';

SELECT manutencao();
DROP FUNCTION manutencao();

        CREATE OR REPLACE FUNCTION regraConcessaoDesoneracaoIPTU2011(INTEGER) RETURNS BOOLEAN AS $$
        DECLARE
        inRegistro ALIAS FOR $1;

          boRetorno BOOLEAN;
          nuLancamento NUMERIC;
          nuUsoSolo NUMERIC;
          nuValorCred2 NUMERIC;
          nuValorCred3 NUMERIC;
          stUsoSolo VARCHAR := '';
        BEGIN
        stUsoSolo := recuperaCadastroImobiliarioImovelUsoDoSolo(  inRegistro  );
        nuUsoSolo := arrecadacao.fn_vc2num(  stUsoSolo  );
        nuValorCred2 := arrecadacao.buscaValorCalculoCredito(  inRegistro , 261 , '2011' , 2 , 1 , 1 , 1  );
        nuValorCred3 := arrecadacao.buscaValorCalculoCredito(  inRegistro , 261 , '2011' , 3 , 1 , 1 , 1  );
        nuLancamento := nuValorCred2+nuValorCred3 ;
        IF       (  nuUsoSolo  =  2  )  AND  (  nuLancamento  <    69  ) THEN
            boRetorno := TRUE;
        ELSE
            IF       (  nuUsoSolo  =  1  )  AND  (  nuLancamento  <    27  ) THEN
                boRetorno := TRUE;
            ELSE
                boRetorno := FALSE;
            END IF;
        END IF;
        RETURN boRetorno;
        END;
        $$ LANGUAGE 'plpgsql';



CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE
    inMaxModelo     INTEGER;
BEGIN

    PERFORM 1
       FROM administracao.configuracao
      WHERE exercicio  = '2011'
        AND cod_modulo = 2
        AND parametro  = 'cnpj'
        AND valor      = '13805528000180'
          ;

    IF NOT FOUND THEN
        DROP FUNCTION regraConcessaoDesoneracaoIPTU2011(INTEGER);
    END IF;

END;
$$ LANGUAGE 'plpgsql';

SELECT manutencao();
DROP FUNCTION manutencao();

