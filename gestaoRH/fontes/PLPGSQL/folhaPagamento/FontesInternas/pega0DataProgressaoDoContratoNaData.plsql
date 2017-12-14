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
--
-- script de funcao PLSQL
-- 
-- URBEM Soluções de Gestão Pública Ltda
-- www.urbem.cnm.org.br
--
-- $Revision: 28448 $
-- $Name$
-- $Autor: Marcia $
-- Date: 2005/10/04 10:50:00 $
--
-- Caso de uso: uc-04.05.48
--
-- Objetivo: Recebe o codigo do contrato e data de referencia e 
-- retorna a data para inicio da contagem para progressao. 
-- Caso a data de progressao nao esteja informada devera ser consultada
-- a configuracao para que possa ser informada a data de posse ou a data]
-- de nomeacao para a contagem de tempo, como configurado.
--
--


CREATE OR REPLACE FUNCTION pega0DataProgressaoDoContratoNaData(integer) RETURNS varchar as $$

DECLARE
    inCodContrato           ALIAS FOR $1;
    stInicioProgressao      VARCHAR ;
    stEntidade VARCHAR := recuperarBufferTexto('stEntidade');
    stExercicio VARCHAR := recuperarBufferTexto('stExercicioSistema');
 BEGIN


    stInicioProgressao := selectIntoVarchar('
SELECT (CASE WHEN pessoal'|| stEntidade ||'.contrato_servidor_inicio_progressao.dt_inicio_progressao is not null 
             THEN pessoal'|| stEntidade ||'.contrato_servidor_inicio_progressao.dt_inicio_progressao
             ELSE ( CASE (SELECT valor FROM administracao.configuracao WHERE cod_modulo = 22 AND parametro = '|| quote_literal('dtContagemInicial'||stEntidade) ||' AND exercicio = '|| quote_literal(stExercicio) ||')
                    WHEN ''dtPosse''    THEN contrato_servidor_nomeacao_posse.dt_posse
                    WHEN ''dtAdmissao'' THEN contrato_servidor_nomeacao_posse.dt_admissao
                    WHEN ''dtNomeacao'' THEN contrato_servidor_nomeacao_posse.dt_nomeacao
                    END)
         END) as dt_inicio_progressao
  FROM pessoal'|| stEntidade ||'.contrato_servidor_inicio_progressao
     , (  SELECT cod_contrato
               , max(timestamp) as timestamp
            FROM pessoal'|| stEntidade ||'.contrato_servidor_inicio_progressao
        GROUP BY cod_contrato) as max_contrato_servidor_inicio_progressao
     , pessoal'|| stEntidade ||'.contrato_servidor_nomeacao_posse
     , (  SELECT cod_contrato
               , max(timestamp) as timestamp
            FROM pessoal'|| stEntidade ||'.contrato_servidor_nomeacao_posse
        GROUP BY cod_contrato) as max_contrato_servidor_nomeacao_posse
 WHERE contrato_servidor_inicio_progressao.cod_contrato = max_contrato_servidor_inicio_progressao.cod_contrato
   AND contrato_servidor_inicio_progressao.timestamp = max_contrato_servidor_inicio_progressao.timestamp
   AND contrato_servidor_inicio_progressao.cod_contrato = contrato_servidor_nomeacao_posse.cod_contrato
   AND contrato_servidor_nomeacao_posse.cod_contrato = max_contrato_servidor_nomeacao_posse.cod_contrato
   AND contrato_servidor_nomeacao_posse.timestamp = max_contrato_servidor_nomeacao_posse.timestamp
   AND contrato_servidor_inicio_progressao.cod_contrato = '|| inCodContrato);

    RETURN stInicioProgressao;
END;
$$ LANGUAGE 'plpgsql';

