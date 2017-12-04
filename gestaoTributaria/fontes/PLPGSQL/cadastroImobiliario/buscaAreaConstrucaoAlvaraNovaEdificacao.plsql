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
* Versao 2.00.9
*
* Fabio Bertoldi - 20120716
*
*/


CREATE OR REPLACE FUNCTION buscaAreaConstrucaoAlvaraNovaEdificacao ( inImovel           INTEGER
                                                                   , inCodConstrucao    INTEGER
                                                                   ) RETURNS            NUMERIC AS $$
DECLARE
    nuArea  NUMERIC;
BEGIN
        SELECT licenca_imovel_area.area
          INTO nuArea
          FROM imobiliario.licenca_imovel_area
          JOIN imobiliario.licenca_imovel
            ON licenca_imovel.inscricao_municipal = licenca_imovel_area.inscricao_municipal
           AND licenca_imovel.cod_licenca         = licenca_imovel_area.cod_licenca
           AND licenca_imovel.exercicio           = licenca_imovel_area.exercicio
     LEFT JOIN imobiliario.licenca_imovel_nova_edificacao
            ON licenca_imovel_nova_edificacao.inscricao_municipal = licenca_imovel.inscricao_municipal
           AND licenca_imovel_nova_edificacao.cod_licenca         = licenca_imovel.cod_licenca
           AND licenca_imovel_nova_edificacao.exercicio           = licenca_imovel.exercicio
     LEFT JOIN imobiliario.licenca_imovel_nova_construcao
            ON licenca_imovel_nova_construcao.inscricao_municipal = licenca_imovel.inscricao_municipal
           AND licenca_imovel_nova_construcao.cod_licenca         = licenca_imovel.cod_licenca
           AND licenca_imovel_nova_construcao.exercicio           = licenca_imovel.exercicio
         WHERE licenca_imovel.inscricao_municipal = inImovel
           AND (
                    licenca_imovel_nova_edificacao.cod_construcao = inCodConstrucao
                 OR licenca_imovel_nova_construcao.cod_construcao = inCodConstrucao
               )
             ;

    RETURN nuArea;
END;
$$ LANGUAGE 'plpgsql';

