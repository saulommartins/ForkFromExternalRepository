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
* $Id: recuperaLogradouroTrechoImovel.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Casos de uso: uc-05.03.05
*
*/

   --
   -- Criação das Funções no banco.
   --
   CREATE OR REPLACE FUNCTION recuperaLogradouroTrechoImovel(intInscricaoMunicipal INTEGER )
   RETURNS INTEGER AS $$
   DECLARE
      intCodTrecho      INTEGER;
      intCodLogradouro  INTEGER;
   BEGIN

      SELECT confrontacao_trecho.cod_trecho, confrontacao_trecho.cod_logradouro
        INTO intCodTrecho, intCodLogradouro
        FROM imobiliario.imovel
           , imobiliario.imovel_confrontacao
           , imobiliario.confrontacao_trecho
       WHERE imovel.inscricao_municipal            = imovel_confrontacao.inscricao_municipal
         AND imovel_confrontacao.cod_confrontacao  = confrontacao_trecho.cod_confrontacao
         AND imovel_confrontacao.cod_lote          = confrontacao_trecho.cod_lote
         AND imovel.inscricao_municipal            = intInscricaoMunicipal
      ;

      RETURN intCodLogradouro;
   END;
   $$ LANGUAGE 'plpgsql';
