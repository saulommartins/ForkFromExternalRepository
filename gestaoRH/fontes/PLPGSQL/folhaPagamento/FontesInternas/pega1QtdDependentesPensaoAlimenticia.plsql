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
* script de funcao PLSQL
* 
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br

* $Revision: 23095 $
* $Name$
* $Autor: Marcia $
* Date: 2006/05/22 10:50:00 $
*
* Caso de uso: uc-04.05.17
* Caso de uso: uc-04.05.48
*
* Objetivo: Recupera a quantidade de dependentes ativos para pensao alimenticia 
*/

CREATE OR REPLACE FUNCTION pega1QtdDependentesPensaoAlimenticia() RETURNS integer as $$
DECLARE
    stDataFinalCompetencia    VARCHAR;
    dtDataFinalCompetencia    VARCHAR;
    inCodContrato             INTEGER;
    inCodServidor             INTEGER;
    stSql                     VARCHAR := '';
    reRegistro                RECORD;
    inQtdPensoes              INTEGER;
    stEntidade                VARCHAR;
 BEGIN
    stEntidade := recuperarBufferTexto('stEntidade');
    inCodContrato := recuperarBufferInteiro('inCodContrato');
    inCodContrato := recuperaContratoServidorPensionista(inCodContrato);
    stDataFinalCompetencia := recuperarBufferTexto('stDataFinalCompetencia');
    dtDataFinalCompetencia := substr(stDataFinalCompetencia,1,10) ; 
    inCodServidor := pega0ServidorDoContrato(inCodContrato);
    
    STSQL:= 'SELECT COALESCE( COUNT(servidor_dependente.cod_servidor),0 ) as qtd_pensao
               FROM pessoal'||stEntidade||'.servidor_dependente 
         INNER JOIN pessoal'||stEntidade||'.pensao
                 ON pensao.cod_dependente = servidor_dependente.cod_dependente
                AND pensao.cod_servidor = servidor_dependente.cod_servidor               
         INNER JOIN (  SELECT cod_pensao
                            , max(timestamp) as timestamp
                         FROM pessoal'||stEntidade||'.pensao
                     GROUP BY cod_pensao
                    ) as max_pensao
                 ON pensao.cod_pensao = max_pensao.cod_pensao
                AND pensao.timestamp = max_pensao.timestamp
          LEFT JOIN pessoal'||stEntidade||'.pensao_funcao
                 ON pensao.cod_pensao = pensao_funcao.cod_pensao
                AND pensao.timestamp = pensao_funcao.timestamp
          LEFT JOIN pessoal'||stEntidade||'.pensao_valor
                 ON pensao.cod_pensao = pensao_valor.cod_pensao
                AND pensao.timestamp = pensao_valor.timestamp
          LEFT JOIN pessoal'||stEntidade||'.pensao_excluida
                 ON pensao.cod_pensao = pensao_excluida.cod_pensao
                AND pensao.timestamp = pensao_excluida.timestamp
          LEFT JOIN pessoal'||stEntidade||'.dependente_excluido
                 ON servidor_dependente.cod_dependente = dependente_excluido.cod_dependente               
              WHERE pensao.dt_inclusao <= '''||dtDataFinalCompetencia||'''
                AND (    pensao.dt_limite IS NULL 
                      OR pensao.dt_limite >= '''||dtDataFinalCompetencia||'''
                    )
                AND servidor_dependente.cod_servidor = '||inCodServidor||'
                AND dependente_excluido.cod_servidor IS NULL
                AND pensao_excluida.cod_pensao IS NULL';
     EXECUTE stSql;

     FOR reRegistro IN EXECUTE stSql LOOP
        inQtdPensoes :=  reRegistro.qtd_pensao;
     END LOOP;

  RETURN inQtdPensoes;
END;
$$ LANGUAGE 'plpgsql';

