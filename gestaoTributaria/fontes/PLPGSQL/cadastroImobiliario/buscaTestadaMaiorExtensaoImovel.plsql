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
* $Id: buscaTestadaMaiorExtensaoImovel.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.03.05
*/

/*
$Log$

*/

CREATE OR REPLACE FUNCTION imobiliario.busca_testada_maior_extensao_imovel( INTEGER )  RETURNS NUMERIC(8,2) AS '
DECLARE
    inImovel                ALIAS FOR $1;
    stSQL                   VARCHAR;
    reRecord                RECORD;
    nuExtensaoConf          NUMERIC(8,2) := 0.00;
    nuResultado             NUMERIC      := 0.00;

BEGIN

    stSQL := ''     SELECT ICT.cod_lote
                         , ICT.cod_confrontacao
                      FROM imobiliario.confrontacao_trecho      AS ICT
                INNER JOIN imobiliario.imovel_confrontacao      AS IIC
                        ON IIC.cod_lote            = ICT.cod_lote
                       AND IIC.inscricao_municipal = '' || inImovel || ''

             '';

    FOR reRecord IN EXECUTE stSQL LOOP

        SELECT COALESCE( ( CE.valor) , 0)
          INTO nuExtensaoConf
          FROM imobiliario.confrontacao_extensao   AS CE
             , (
                   SELECT MAX(timestamp)           AS timestamp
                        , cod_confrontacao
                     FROM imobiliario.confrontacao_extensao
                 GROUP BY 2
               )                                   AS TMP
         WHERE CE.cod_confrontacao = TMP.cod_confrontacao
           AND CE.timestamp        = TMP.timestamp
           AND CE.cod_confrontacao = reRecord.cod_confrontacao
           AND CE.cod_lote         = reRecord.cod_lote;

        IF nuExtensaoConf > nuResultado THEN

            nuResultado := nuExtensaoConf;

        END IF;

    END LOOP;

    nuResultado := cast(nuResultado as numeric(8,2));

RETURN nuResultado;

END;
' LANGUAGE 'plpgsql';
