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


CREATE OR REPLACE FUNCTION buscaCodigoConstrucaoAlvaraNovaEdificacao( inImovel  INTEGER
                                                                    ) RETURNS   INTEGER AS $$
DECLARE
    inCodConstrucao     INTEGER;
BEGIN
        SELECT COALESCE(licenca_imovel_nova_edificacao.cod_construcao, licenca_imovel_nova_construcao.cod_construcao)
          INTO inCodConstrucao
          FROM imobiliario.licenca_imovel
    INNER JOIN (
                   SELECT MAX(licenca.timestamp) AS max_timestamp
                        , licenca.cod_licenca
                        , licenca.exercicio
                        , licenca_imovel.inscricao_municipal
                     FROM imobiliario.licenca_imovel
                     JOIN imobiliario.licenca
                       ON licenca.cod_licenca = licenca_imovel.cod_licenca
                      AND licenca.exercicio   = licenca_imovel.exercicio
                    WHERE licenca.cod_tipo = 1
                 GROUP BY licenca.cod_licenca
                        , licenca.exercicio
                        , licenca_imovel.inscricao_municipal
               ) AS licenca
            ON licenca.cod_licenca         = licenca_imovel.cod_licenca
           AND licenca.exercicio           = licenca_imovel.exercicio
           AND licenca.inscricao_municipal = licenca_imovel.inscricao_municipal
     LEFT JOIN imobiliario.licenca_imovel_nova_edificacao
            ON licenca_imovel_nova_edificacao.cod_licenca         = licenca_imovel.cod_licenca
           AND licenca_imovel_nova_edificacao.exercicio           = licenca_imovel.exercicio
           AND licenca_imovel_nova_edificacao.inscricao_municipal = licenca_imovel.inscricao_municipal
     LEFT JOIN imobiliario.licenca_imovel_nova_construcao
            ON licenca_imovel_nova_construcao.cod_licenca         = licenca_imovel.cod_licenca
           AND licenca_imovel_nova_construcao.exercicio           = licenca_imovel.exercicio
           AND licenca_imovel_nova_construcao.inscricao_municipal = licenca_imovel.inscricao_municipal
         WHERE licenca_imovel.inscricao_municipal = inImovel
             ;

    RETURN inCodConstrucao;
END;
$$ LANGUAGE 'plpgsql';

