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
* Versao 2.01.2
*
* Fabio Bertoldi - 20121120
*
*/

----------------
-- Ticket #19900
----------------

UPDATE administracao.modulo SET ativo = FALSE WHERE cod_modulo = 34;


-----------------------------
-- PL para NOVA CONTABILIDADE
-----------------------------

CREATE OR REPLACE FUNCTION  empenholiquidacaomodalidadeslicitacao(character varying, numeric, character varying, integer, character varying, integer, integer) RETURNS INTEGER AS $$

   DECLARE
   EXERCICIO ALIAS FOR $1;
   VALOR ALIAS FOR $2;
   COMPLEMENTO ALIAS FOR $3;
   CODLOTE ALIAS FOR $4;
   TIPOLOTE ALIAS FOR $5;
   CODENTIDADE ALIAS FOR $6;
   CODNOTA ALIAS FOR $7;

   MODALIDADE VARCHAR := '';
   SEQUENCIA INTEGER;
   BEGIN

   MODALIDADE := PEGAEMPENHOLIQUIDACAOMODALIDADE(  EXERCICIO , CODNOTA , CODENTIDADE  );
    MODALIDADE := sem_acentos(MODALIDADE);

    IF EXERCICIO::integer > 2012 THEN
        IF   MODALIDADE  =  'Concurso' THEN
           SEQUENCIA := FAZERLANCAMENTO(  '622920401' , '622920601' , 902 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
        END IF;
        IF   MODALIDADE  =  'Convite' THEN
           SEQUENCIA := FAZERLANCAMENTO(  '622920402' , '622920602' , 902 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
        END IF;
        IF   MODALIDADE  =  'Tomada' THEN
           SEQUENCIA := FAZERLANCAMENTO(  '622920403' , '622920603' , 902 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
        END IF;
        IF   MODALIDADE  =  'Concorrencia' THEN
           SEQUENCIA := FAZERLANCAMENTO(  '622920404' , '622920604' , 902 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
        END IF;
        IF   MODALIDADE  =  'Dispensa' THEN
           SEQUENCIA := FAZERLANCAMENTO(  '622920406' , '622920606' , 902 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
        END IF;
        IF   MODALIDADE  =  'Inexigivel' THEN
           SEQUENCIA := FAZERLANCAMENTO(  '622920407' , '622920607' , 902 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
        END IF;
        IF   MODALIDADE  =  'Nao Aplicavel' THEN
           SEQUENCIA := FAZERLANCAMENTO(  '622920408' , '622920608' , 902 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
        END IF;
        IF   MODALIDADE  =  'Suprimentos' THEN
           SEQUENCIA := FAZERLANCAMENTO(  '622920409' , '622920609' , 902 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
        END IF;
        IF   MODALIDADE  =  'Consulta' THEN
           SEQUENCIA := FAZERLANCAMENTO(  '622920411' , '622920611' , 902 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
        END IF;
        IF   MODALIDADE  =  'Pregao Presencial' THEN
           SEQUENCIA := FAZERLANCAMENTO(  '622920412' , '622920612' , 902 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
        END IF;
        IF   MODALIDADE  =  'Pregao Eletronico' THEN
           SEQUENCIA := FAZERLANCAMENTO(  '622920412' , '622920612' , 902 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
        END IF;
    ELSE
        IF   MODALIDADE  =  'Concurso' THEN
           SEQUENCIA := FAZERLANCAMENTO(  '292410201000000' , '292410301000000' , 902 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
        END IF;
        IF   MODALIDADE  =  'Convite' THEN
           SEQUENCIA := FAZERLANCAMENTO(  '292410202000000' , '292410302000000' , 902 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
        END IF;
        IF   MODALIDADE  =  'Tomada' THEN
           SEQUENCIA := FAZERLANCAMENTO(  '292410203000000' , '292410303000000' , 902 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
        END IF;
        IF   MODALIDADE  =  'Concorrencia' THEN
           SEQUENCIA := FAZERLANCAMENTO(  '292410204000000' , '292410304000000' , 902 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
        END IF;
        IF   MODALIDADE  =  'Dispensa' THEN
           SEQUENCIA := FAZERLANCAMENTO(  '292410206000000' , '292410306000000' , 902 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
        END IF;
        IF   MODALIDADE  =  'Inexigivel' THEN
           SEQUENCIA := FAZERLANCAMENTO(  '292410207000000' , '292410307000000' , 902 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
        END IF;
        IF   MODALIDADE  =  'Nao Aplicavel' THEN
           SEQUENCIA := FAZERLANCAMENTO(  '292410208000000' , '292410308000000' , 902 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
        END IF;
        IF   MODALIDADE  =  'Suprimentos' THEN
           SEQUENCIA := FAZERLANCAMENTO(  '292410209000000' , '292410309000000' , 902 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
        END IF;
        IF     MODALIDADE  =  'Integracao' THEN
           SEQUENCIA := FAZERLANCAMENTO(  '292410210000000' , '292410310000000' , 902 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
        END IF;
        IF   MODALIDADE  =  'Pregao' THEN
           SEQUENCIA := FAZERLANCAMENTO(  '292410212000000' , '292410312000000' , 902 , EXERCICIO , VALOR , COMPLEMENTO , CODLOTE , TIPOLOTE , CODENTIDADE  );
        END IF;
    END IF;

    RETURN SEQUENCIA;
END;
$$ LANGUAGE 'plpgsql';

