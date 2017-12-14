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
* Date: 2006/05/31 10:50:00 $
*
* Caso de uso: uc-04.05.17
* Caso de uso: uc-04.05.48
*
* Objetivo: Recupera a quantidade de dependentes ativos para pensao alimenticia 
*/



CREATE OR REPLACE FUNCTION pega0QtdDependentesPensaoAlimenticia(integer,varchar) RETURNS integer as $$

DECLARE
   inCodContrato              ALIAS FOR $1;
    stDataFinalCompetencia    ALIAS FOR $2;

    dtDataFinalCompetencia    VARCHAR;
    inCodServidor             INTEGER;
    stSql                     VARCHAR := '';
    reRegistro                RECORD ;
    inQtdPensoes              INTEGER ;

stEntidade VARCHAR := recuperarBufferTexto('stEntidade');
BEGIN

    dtDataFinalCompetencia := substr(stDataFinalCompetencia,1,10) ; 

    inCodServidor := pega0ServidorDoContrato(inCodContrato);

    stSql:= '
    SELECT COALESCE( COUNT(sd.cod_servidor),0 ) as qtd_pensao
      FROM pessoal'||stEntidade||'.servidor_dependente  as sd
      JOIN 
      ( SELECT pensao.cod_pensao
             , pensao.timestamp
             , cod_dependente
             , cod_servidor
             , tipo_pensao
             , dt_inclusao
             , dt_limite
             , percentual
         FROM pessoal'||stEntidade||'.pensao
            , (SELECT cod_pensao
                    , max(timestamp) as timestamp
                 FROM pessoal'||stEntidade||'.pensao
               GROUP BY cod_pensao) as max_pensao
        WHERE dt_inclusao <= '''||dtDataFinalCompetencia||'''
          AND ( dt_limite is null
             or dt_limite >= '''||dtDataFinalCompetencia||''' )
          AND pensao.cod_pensao = max_pensao.cod_pensao
          AND pensao.timestamp = max_pensao.timestamp
          AND NOT EXISTS (SELECT *
                            FROM pessoal'||stEntidade||'.pensao_excluida
                           WHERE pensao.cod_pensao = pensao_excluida.cod_pensao
                             AND pensao.timestamp = pensao_excluida.timestamp)
      ) as p
       ON p.cod_dependente = sd.cod_dependente
      AND p.cod_servidor = sd.cod_servidor
     LEFT OUTER JOIN  pessoal'||stEntidade||'.pensao_funcao as pf
       ON p.cod_pensao = pf.cod_pensao
      AND p.timestamp = pf.timestamp
     LEFT OUTER JOIN  pessoal'||stEntidade||'.pensao_valor as pv
       ON p.cod_pensao = pv.cod_pensao
      AND p.timestamp = pv.timestamp
     LEFT OUTER JOIN  pessoal'||stEntidade||'.pensao_excluida as pe
       ON p.cod_pensao = pe.cod_pensao
      AND p.timestamp = pe.timestamp
     LEFT OUTER JOIN pessoal'||stEntidade||'.dependente_excluido as de
       ON sd.cod_dependente = de.cod_dependente
      AND sd.cod_servidor = de.cod_servidor
    WHERE sd.cod_servidor = '||inCodServidor||'
      AND de.cod_servidor is null
      AND pe.cod_pensao   is null 
      ';
    EXECUTE stSql;

    FOR reRegistro IN EXECUTE stSql
    LOOP
        inQtdPensoes :=  reRegistro.qtd_pensao;
    END LOOP;

  RETURN inQtdPensoes;

END;
$$ LANGUAGE 'plpgsql';

