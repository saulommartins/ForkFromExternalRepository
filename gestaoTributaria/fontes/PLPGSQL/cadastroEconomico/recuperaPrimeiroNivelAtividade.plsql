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
* $Id: recuperaPrimeiroNivelAtividade.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Casos de uso: uc-05.03.05
*
*/

/*
$Log $

*/

   CREATE OR REPLACE FUNCTION economico.recuperaPrimeiroNivelAtividade( intInscricaoEconomica INTEGER
                                                                      , intNivel              INTEGER
                                                                      , intOrdem              INTEGER )
   RETURNS VARCHAR AS $$
   DECLARE
      intValor             INTEGER := 0 ;
      intUltimaOcorrencia  INTEGER;
      stValorNivel         VARCHAR;
      stMascara            VARCHAR;
      stRetorno            VARCHAR;
   BEGIN

      -- Busca última ocorrencia válida.
      SELECT atividade_cadastro_economico.ocorrencia_atividade
        INTO intUltimaOcorrencia
        FROM economico.atividade_cadastro_economico
       WHERE atividade_cadastro_economico.inscricao_economica =  intInscricaoEconomica
       ORDER BY atividade_cadastro_economico.ocorrencia_atividade DESC
       LIMIT 1
      ;

      IF FOUND THEN
         SELECT atividade_cadastro_economico.cod_atividade
           INTO intValor
           FROM economico.atividade_cadastro_economico
          WHERE atividade_cadastro_economico.inscricao_economica  =  intInscricaoEconomica
            AND atividade_cadastro_economico.ocorrencia_atividade =  intUltimaOcorrencia
         ORDER BY atividade_cadastro_economico.principal desc, atividade_cadastro_economico.OID
         LIMIT 1 OFFSET (intOrdem-1)
         ;
      END IF;

      SELECT enav.valor
        INTO stValorNivel
        FROM economico.nivel_atividade_valor        AS enav
       WHERE cod_atividade = intValor
         AND cod_nivel     = intNivel
      ;

      SELECT mascara
        INTO stMascara
        FROM economico.nivel_atividade
       WHERE cod_nivel    = intNivel
         AND cod_vigencia = (
                                SELECT cod_vigencia
                                  FROM economico.vigencia_atividade
                                 WHERE dt_inicio < now()::date
                              ORDER BY dt_inicio DESC
                                 LIMIT 1
                            )
      ;

      stRetorno := lpad( stValorNivel ,length( stMascara ),'0' );

      RETURN stValorNivel;
   END;
   $$ LANGUAGE 'plpgsql';
