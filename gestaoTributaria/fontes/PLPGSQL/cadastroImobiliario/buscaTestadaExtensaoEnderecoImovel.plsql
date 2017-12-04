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
* $Id: buscaTestadaExtensaoEnderecoImovel.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.03.05
*/

/*
$Log$

*/

CREATE OR REPLACE FUNCTION imobiliario.busca_testada_extensao_endereco_imovel( INTEGER )  RETURNS NUMERIC(8,2) AS '
DECLARE
    inImovel                ALIAS FOR $1;
    nuExtensaoConf          NUMERIC(8,2);
    nuResultado             NUMERIC := 0.00;

BEGIN

        SELECT Coalesce( ( ICE.valor ) , 0)
          INTO nuExtensaoConf
          FROM imobiliario.imovel_confrontacao              AS IIC
    INNER JOIN (
                 SELECT CE.valor
                      , CE.cod_confrontacao
                      , CE.cod_lote
                   FROM imobiliario.confrontacao_extensao   AS CE
                      , (
                            SELECT MAX(timestamp)           AS timestamp
                                 , cod_confrontacao
                                 , cod_lote
                              FROM imobiliario.confrontacao_extensao
                          GROUP BY 2,3
                        )                                   AS TMP
                  WHERE CE.cod_confrontacao = TMP.cod_confrontacao
                    AND CE.cod_lote         = TMP.cod_lote
                    AND CE.timestamp        = TMP.timestamp
               )                                            AS ICE
            ON IIC.cod_confrontacao         = ICE.cod_confrontacao
           AND IIC.cod_lote                 = ICE.cod_lote
           AND IIC.inscricao_municipal      = inImovel;

    nuResultado := cast(nuExtensaoConf as numeric(8,2));

RETURN nuResultado;

END;
' LANGUAGE 'plpgsql';
