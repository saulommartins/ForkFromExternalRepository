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
* Objetivo: retorna o valor total da pensao alimenticia a descontar
* , levando em conta cada um de seus dependentes e o tipo de calculo de cada um.
*/



CREATE OR REPLACE FUNCTION pega1ResultadoPensaoAlimenticia() RETURNS numeric as $$

DECLARE
    stDataFinalCompetencia    VARCHAR;
    dtDataFinalCompetencia    VARCHAR;
    inCodContrato             INTEGER;
    inCodServidor             INTEGER;
    stSql                     VARCHAR := '';
    reRegistro                RECORD ;
    stFormula                 VARCHAR ;
    nuExecutaFormula          NUMERIC := 0.00;
    stExecutaFormula          VARCHAR := '';
    nuValorPensao             NUMERIC := 0.00;
    nuValor                   NUMERIC := 0.00;
    inBuffer                  INTEGER;
    stEntidade             VARCHAR;
    boRetorno                 boolean;
 BEGIN
    stEntidade := recuperarBufferTexto('stEntidade');
    inCodContrato := recuperarBufferInteiro('inCodContrato');
    stDataFinalCompetencia := recuperarBufferTexto('stDataFinalCompetencia');

    dtDataFinalCompetencia := substr(stDataFinalCompetencia,1,10) ; 

    inCodServidor := pega0ServidorDoContrato(inCodContrato);

    stSql := '
    SELECT
        sd.cod_servidor   as sdcod_servidor
      , sd.cod_dependente as sdcod_dependente
      , sd.dt_inicio      as sddt_inicio
      , p.cod_pensao      as pcod_pensao
      , p.timestamp       as ptimestamp
      , p.cod_dependente  as pcoddependente
      , p.cod_servidor    as pcod_servidor
      , p.tipo_pensao     as ptipo_pensao
      , p.dt_inclusao     as pdt_inclusao
      , p.dt_limite       as pdt_limite
      , p.percentual      as ppercentual
      , pf.cod_pensao     as pfcod_pensao
      , pf.timestamp      as pftimestamp
      , pf.cod_biblioteca as pfcod_biblioteca
      , pf.cod_modulo     as pfcod_modulo
      , pf.cod_funcao     as pfcod_funcao
      , pv.timestamp      as pvtimestamp
      , pv.cod_pensao     as pvcod_pensao
      , pv.valor          as pvvalor
      , de.cod_dependente as decod_dependente
      , de.cod_servidor   as decod_servidor
      , de.data_exclusao  as dedata_exclusao
      , pe.cod_pensao     as pecod_pensao
      , pe.timestamp      as petimestamp
      FROM pessoal'||stEntidade||'.servidor_dependente  as sd
      LEFT OUTER JOIN
      ( SELECT   distinct on(cod_pensao)cod_pensao
           , timestamp
           , cod_dependente
           , cod_servidor
           , tipo_pensao
           , dt_inclusao
           , dt_limite
           , percentual
      FROM pessoal'||stEntidade||'.pensao
      WHERE dt_inclusao <= '''||dtDataFinalCompetencia||'''
        AND ( dt_limite is null
              or dt_limite >= '''||dtDataFinalCompetencia||'''
            )
        AND timestamp = ( SELECT MAX_PENSAO.timestamp FROM pessoal'||stEntidade||'.pensao as MAX_PENSAO
                           WHERE MAX_PENSAO.cod_pensao = pensao.cod_pensao
                             AND MAX_PENSAO.cod_dependente = pensao.cod_dependente
                             AND MAX_PENSAO.cod_servidor = pensao.cod_servidor
                             ORDER BY MAX_PENSAO.timestamp DESC
                             LIMIT 1
                        )
      ORDER BY cod_pensao, timestamp desc
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
    ORDER BY p.cod_dependente
      ';


     FOR reRegistro IN EXECUTE stSql
     LOOP
          IF reRegistro.pfcod_pensao is not null THEN
              stFormula := trim(to_char(reRegistro.pfcod_modulo,'99999'))||'.'||trim(to_char(reRegistro.pfcod_biblioteca,'99999'))||'.'||trim(to_char(reRegistro.pfcod_funcao,'99999'));

              -- apagar buffer pois pode ja existir..

              inBuffer := criarBufferInteiro('incodpensao', reRegistro.pfcod_pensao );

              stExecutaFormula := executaGCNumerico( stFormula );
              nuExecutaFormula := to_number( stExecutaFormula ,'99999999999.99' );


              boRetorno := removerBufferInteiro('incodpensao');

              IF nuExecutaFormula > 0 THEN
                 nuValor := nuValor + nuExecutaFormula;

              END IF;
          ELSE
             IF reRegistro.pvcod_pensao is not null THEN
                 nuValorPensao := reRegistro.pvvalor;
                 IF nuValorPensao > 0 THEN
                    nuValor := nuValor + nuValorPensao;

                 END IF;
             END IF;
          END IF;
     END LOOP;


  RETURN nuValor;


END;
$$ LANGUAGE 'plpgsql';

