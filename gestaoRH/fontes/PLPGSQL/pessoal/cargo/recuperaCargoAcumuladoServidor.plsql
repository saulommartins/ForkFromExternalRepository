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
/* recuperarCargoAcumuladoServidor
 * Data de Criação : 07/10/2015
 * @author Analista : Dagiane Vieira
 * @author Desenvolvedor : Evandro Melos
 * $Id:$
*/

CREATE OR REPLACE FUNCTION recuperaCargoAcumuladoServidor(INTEGER, INTEGER, INTEGER, VARCHAR) RETURNS VARCHAR as $$
DECLARE
    inCodContrato                   ALIAS FOR $1;
    inCodServidor                   ALIAS FOR $2;
    inCodPeriodoMovimentacao        ALIAS FOR $3;
    stEntidade                      ALIAS FOR $4;
    stSQL                           VARCHAR:='';
    stRetorno                       VARCHAR:='';
    stFiltro                        VARCHAR:='';
    reRecord                        RECORD;
BEGIN
    
    stSQL := '
        SELECT cargo.cod_cargo||'' - ''||cargo.descricao AS nome_cargo_acumulado
          FROM ultimo_contrato_servidor_funcao('''||stEntidade||''','||inCodPeriodoMovimentacao||') as contrato_servidor_funcao 
   
   INNER JOIN pessoal'||stEntidade||'.servidor_contrato_servidor
           ON servidor_contrato_servidor.cod_contrato = contrato_servidor_funcao.cod_contrato
          AND servidor_contrato_servidor.cod_servidor = '||inCodServidor||'
   INNER JOIN pessoal'||stEntidade||'.cargo
           ON cargo.cod_cargo = contrato_servidor_funcao.cod_cargo
   INNER JOIN ( SELECT  contrato_servidor_situacao.*
                    FROM pessoal'||stEntidade||'.contrato_servidor_situacao 
                    INNER JOIN (SELECT  max.cod_contrato
                                        ,MAX(max.timestamp) as timestamp
                                    FROM pessoal'||stEntidade||'.contrato_servidor_situacao as max
                                    INNER JOIN pessoal'||stEntidade||'.servidor_contrato_servidor
                                            ON servidor_contrato_servidor.cod_servidor = '||inCodServidor||'
                                            AND servidor_contrato_servidor.cod_contrato = max.cod_contrato
                                    WHERE max.timestamp <= (ultimoTimestampPeriodoMovimentacao('||inCodPeriodoMovimentacao||','''||stEntidade||'''))::timestamp
                                    GROUP BY max.cod_contrato
                    ) as max_servidor_situacao
                         ON max_servidor_situacao.timestamp = contrato_servidor_situacao.timestamp
                        AND max_servidor_situacao.cod_contrato = contrato_servidor_situacao.cod_contrato
                        AND contrato_servidor_situacao.situacao = ''A''

         ) as contrato_servidor_situacao
           ON contrato_servidor_situacao.cod_contrato = contrato_servidor_funcao.cod_contrato
          AND contrato_servidor_situacao.cod_periodo_movimentacao <= '||inCodPeriodoMovimentacao||'

    WHERE contrato_servidor_situacao.situacao = ''A''
      AND servidor_contrato_servidor.cod_servidor = '||inCodServidor||'
      AND contrato_servidor_funcao.cod_contrato <> '||inCodContrato||';
    ';
    
    FOR reRecord IN EXECUTE stSQL
    LOOP
        stRetorno := reRecord.nome_cargo_acumulado;        
    END LOOP;

    RETURN stRetorno;

END;
$$ LANGUAGE 'plpgsql';