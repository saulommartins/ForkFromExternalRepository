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
* $Id: buscaEstruturalAtividadeDaInscricaoEconomica.plsql 65546 2016-05-31 18:54:34Z fabio $
*
* Casos de uso: uc-05.03.05
*
*/

   CREATE OR REPLACE FUNCTION buscaEstruturalAtividadeDaInscricaoEconomica( intInscricaoEconomica INTEGER
                                                                          , intOrdem              INTEGER)
   RETURNS VARCHAR AS $$
   DECLARE
      stValor              VARCHAR := '';
      intUltimaOcorrencia  INTEGER;
   BEGIN

      -- Busca última ocorrencia válida.
      SELECT MAX(atividade_cadastro_economico.ocorrencia_atividade)
        INTO intUltimaOcorrencia
        FROM economico.atividade_cadastro_economico
       WHERE atividade_cadastro_economico.inscricao_economica =  intInscricaoEconomica
           ;

      IF FOUND THEN
         SELECT atividade.cod_estrutural
           INTO stValor
           FROM economico.atividade
           JOIN economico.atividade_cadastro_economico
             ON atividade_cadastro_economico.cod_atividade = atividade.cod_atividade
          WHERE atividade_cadastro_economico.inscricao_economica  =  intInscricaoEconomica
            AND atividade_cadastro_economico.ocorrencia_atividade =  intUltimaOcorrencia
         ORDER BY atividade_cadastro_economico.principal desc, atividade_cadastro_economico.OID
         LIMIT 1 OFFSET  (intOrdem-1)
         ;
      END IF;

      RETURN stValor;
   END;
   $$ LANGUAGE 'plpgsql';

