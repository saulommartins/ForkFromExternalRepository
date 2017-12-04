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
* $Id: atributoTipoEdificacaoValorSituacaoUnidade_Alagoinhas.sql 29199 2008-04-15 13:42:29Z fabio $
*
* Casos de uso: uc-05.03.05
*
*/

/*
$Log$
Revision 1.1  2007/03/07 18:52:22  fabio
PLs para o calculo de Alagoinhas


*/

   CREATE OR REPLACE FUNCTION atributoTipoEdificacaoValorSituacaoUnidade_Alagoinhas( intInscricaoMunicipal INTEGER)
   RETURNS CHARACTER VARYING AS $$
   DECLARE
      varValor          VARCHAR := NULL;
      intCodModulo      INTEGER  := 12 ;
      intCodCadastro    INTEGER  := 5  ;
      intCodAtributo    INTEGER  := 76 ;
      booAtributoAtivo  BOOLEAN;
   BEGIN

      SELECT atributo_tipo_edificacao.ativo
        INTO booAtributoAtivo
        FROM imobiliario.atributo_tipo_edificacao
       WHERE atributo_tipo_edificacao.cod_modulo   = intCodModulo
         AND atributo_tipo_edificacao.cod_cadastro = intCodCadastro
         AND atributo_tipo_edificacao.cod_atributo = intCodAtributo
      ;

      IF FOUND AND booAtributoAtivo THEN
         SELECT atributo_tipo_edificacao_valor.valor
           INTO varValor
           FROM imobiliario.atributo_tipo_edificacao_valor
               , ( SELECT cod_modulo, cod_cadastro, cod_atributo, cod_tipo, cod_construcao, MAX(timestamp) AS timestamp
                     FROM imobiliario.atributo_tipo_edificacao_valor
                    WHERE atributo_tipo_edificacao_valor.cod_modulo   = intCodModulo
                      AND atributo_tipo_edificacao_valor.cod_cadastro = intCodCadastro
                      AND atributo_tipo_edificacao_valor.cod_atributo = intCodAtributo
                    GROUP BY cod_modulo, cod_atributo, cod_cadastro, cod_tipo, cod_construcao
                 ) AS max_atributo_tipo_edificacao_valor
              , imobiliario.unidade_autonoma
          WHERE atributo_tipo_edificacao_valor.cod_modulo      = max_atributo_tipo_edificacao_valor.cod_modulo
            AND atributo_tipo_edificacao_valor.cod_cadastro    = max_atributo_tipo_edificacao_valor.cod_cadastro
            AND atributo_tipo_edificacao_valor.cod_atributo    = max_atributo_tipo_edificacao_valor.cod_atributo
            AND atributo_tipo_edificacao_valor.cod_tipo        = max_atributo_tipo_edificacao_valor.cod_tipo
            AND atributo_tipo_edificacao_valor.cod_construcao  = max_atributo_tipo_edificacao_valor.cod_construcao
            AND atributo_tipo_edificacao_valor.timestamp       = max_atributo_tipo_edificacao_valor.timestamp
            AND atributo_tipo_edificacao_valor.cod_tipo       = unidade_autonoma.cod_tipo
            AND atributo_tipo_edificacao_valor.cod_construcao = unidade_autonoma.cod_construcao
            AND unidade_autonoma.inscricao_municipal = intInscricaoMunicipal
         ;

      END IF;

      RETURN varValor;
   END;
   $$ LANGUAGE 'plpgsql';

