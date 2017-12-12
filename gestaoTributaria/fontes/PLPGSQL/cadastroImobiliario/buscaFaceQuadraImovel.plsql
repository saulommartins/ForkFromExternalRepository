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
*/

CREATE OR REPLACE FUNCTION buscaFaceQuadraImovel( inImovel     INTEGER
                                                 ) RETURNS      INTEGER AS $$
DECLARE
    inRetorno   INTEGER;
BEGIN
    SELECT face_quadra_trecho.cod_face
      INTO inRetorno
      FROM imobiliario.face_quadra_trecho
      JOIN imobiliario.confrontacao_trecho
        ON confrontacao_trecho.cod_trecho     = face_quadra_trecho.cod_trecho
       AND confrontacao_trecho.cod_logradouro = face_quadra_trecho.cod_logradouro
       AND confrontacao_trecho.principal      = TRUE
      JOIN imobiliario.imovel_lote
        ON imovel_lote.cod_lote = confrontacao_trecho.cod_lote
      JOIN (   SELECT inscricao_municipal
                    , MAX(timestamp) AS timestamp
                 FROM imobiliario.imovel_lote
                WHERE imovel_lote.inscricao_municipal = inImovel
             GROUP BY inscricao_municipal
           ) AS max_lote
        ON max_lote.inscricao_municipal = imovel_lote.inscricao_municipal
       AND max_lote.timestamp           = imovel_lote.timestamp
     WHERE imovel_lote.inscricao_municipal = inImovel
         ;

    RETURN inRetorno;
END;
$$ LANGUAGE 'plpgsql';
