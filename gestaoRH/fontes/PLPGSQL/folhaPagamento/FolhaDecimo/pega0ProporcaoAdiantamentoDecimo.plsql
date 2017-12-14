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
--/**
--    * Pl para retorno da informação de porporcionalização do evento na aba décimo
--    * Data de Criação: 20/11/2007
--
--
--    * @author Diego Lemos de Souza
--
--    * Casos de uso: uc-04.00.00
--
--    * $Id: pega0ProporcaoAdiantamentoDecimo.sql 31697 2008-08-04 19:33:31Z souzadl $
--*/
CREATE OR REPLACE FUNCTION pega0ProporcaoAdiantamentoDecimo() RETURNS BOOLEAN AS $$
DECLARE
    stSelect        VARCHAR:='';
    stEntidade   VARCHAR:='';
    inCodEvento     INTEGER;
    boRetorno       BOOLEAN;
BEGIN
    stEntidade := recuperarBufferTexto('stEntidade');
    inCodEvento   := recuperarBufferInteiro('inCodEvento');
stSelect := '    
SELECT configuracao_evento_caso.proporcao_adiantamento
  FROM folhapagamento'||stEntidade||'.configuracao_evento_caso
     , (  SELECT cod_evento
               , max(timestamp) as timestamp
            FROM folhapagamento'||stEntidade||'.configuracao_evento_caso
        GROUP BY cod_evento) as max_configuracao_evento_caso
 WHERE configuracao_evento_caso.cod_evento = max_configuracao_evento_caso.cod_evento
   AND configuracao_evento_caso.timestamp = max_configuracao_evento_caso.timestamp   
   AND configuracao_evento_caso.cod_configuracao = 3
   AND configuracao_evento_caso.cod_evento = '||inCodEvento;
   boRetorno := selectIntoBoolean(stSelect);
   RETURN boRetorno;
END;   
$$LANGUAGE 'plpgsql';

