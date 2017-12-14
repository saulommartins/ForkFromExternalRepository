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
* $Id: recuperaFaceQuadraImovel.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.01.07
*/

/*
$Log$
*/

CREATE OR REPLACE FUNCTION recuperaFaceQuadraImovel(integer) returns integer AS '
DECLARE
    inInscricaoMunicipal ALIAS FOR $1;
    inRetorno integer;

BEGIN

    SELECT face_quadra_trecho.cod_face
      INTO inRetorno
      FROM imobiliario.imovel_confrontacao

INNER JOIN imobiliario.imovel_lote
        ON imovel_lote.inscricao_municipal         = imovel_confrontacao.inscricao_municipal

INNER JOIN imobiliario.lote_localizacao
        ON lote_localizacao.cod_lote               = imovel_lote.cod_lote
   
INNER JOIN imobiliario.confrontacao_trecho
        ON confrontacao_trecho.cod_confrontacao    = imovel_confrontacao.cod_confrontacao
       AND confrontacao_trecho.cod_lote            = imovel_confrontacao.cod_lote
    
INNER JOIN imobiliario.face_quadra_trecho
        ON face_quadra_trecho.cod_trecho           = confrontacao_trecho.cod_trecho
       AND face_quadra_trecho.cod_logradouro       = confrontacao_trecho.cod_logradouro
       AND face_quadra_trecho.cod_localizacao      = lote_localizacao.cod_localizacao
    
     WHERE imovel_confrontacao.inscricao_municipal = inInscricaoMunicipal;

    return inRetorno;
END;
' LANGUAGE 'plpgsql';
