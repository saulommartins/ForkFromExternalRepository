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
--    * Função PLSQL
--    * Data de Criação: 18/10/2006
--
--
--    * @author Analista: Vandré Miguel Ramos
--    * @author Desenvolvedor: Diego Lemos de Souza
--
--    * @package URBEM
--    * @subpackage
--
--    $Revision: 28997 $
--    $Name$
--    $Author: souzadl $
--    $Date: 2008-04-04 11:05:41 -0300 (Sex, 04 Abr 2008) $
--
--    * Casos de uso: uc-04.05.18
--*/

CREATE OR REPLACE FUNCTION inserirValorQuantidadeEventoRescisaoCalculado() RETURNS BOOLEAN AS $$
DECLARE
    inCodEvento                 INTEGER;
    inCodRegistro               INTEGER;
    stTimestamp                 VARCHAR;                   
    stDesdobramento             VARCHAR;                   
    nuValor                     NUMERIC;
    nuQuantidade                NUMERIC;
    nuRetorno                   BOOLEAN := TRUE;
    stInsert                    VARCHAR := '';
    dtDataFinalCompetencia      VARCHAR;
    reRegistro                  RECORD;        
    stSql                       VARCHAR := '';
    stEntidade VARCHAR;
BEGIN
    inCodEvento                 := recuperarBufferInteiro('inCodEvento');
    inCodRegistro               := recuperarBufferInteiro('inCodRegistro');
    stTimestamp                 := recuperarBufferTimestamp('stTimestamp');                   
    stDesdobramento             := recuperarBufferTexto('stDesdobramento');                   
    nuValor                     := recuperarBufferNumerico('nuValor');
    nuQuantidade                := recuperarBufferNumerico('nuQuantidade');
    dtDataFinalCompetencia      := substr(recuperarBufferTexto('stDataFinalCompetencia'),1,10);
    stEntidade := recuperarBufferTexto('stEntidade');
    stSql := 'INSERT INTO folhapagamento'||stEntidade||'.evento_rescisao_calculado 
                (cod_evento,
                 cod_registro,
                 timestamp_registro,
                 desdobramento,
                 valor,
                 quantidade) VALUES (
                '||inCodEvento||',
                '||inCodRegistro||',
                TO_TIMESTAMP('''||stTimestamp||''',''yyyy-mm-dd hh24:mi:ss.us''),
                '''||stDesdobramento||''',
                '||nuValor||',
                '||nuQuantidade||')';
    EXECUTE stSql;  
    
    IF inCodEvento = pega2CodEventoDescontoPensaoAlimenticia() THEN
        stSql := 'SELECT servidor_dependente.*
                     FROM pessoal'||stEntidade||'.servidor_dependente
                        , pessoal'||stEntidade||'.pensao
                        , (SELECT cod_pensao
                                , max(timestamp) as timestamp
                             FROM pessoal'||stEntidade||'.pensao
                           GROUP BY cod_pensao) as max_pensao
                    WHERE servidor_dependente.cod_dependente = pensao.cod_dependente
                      AND pensao.cod_pensao = max_pensao.cod_pensao
                      AND pensao.timestamp = max_pensao.timestamp
                      AND pensao.dt_inclusao <= '|| quote_literal(dtDataFinalCompetencia) ||'
                      AND (pensao.dt_limite IS NULL OR pensao.dt_limite >= '|| quote_literal(dtDataFinalCompetencia) ||')
                      AND NOT EXISTS (SELECT *
                                        FROM pessoal'||stEntidade||'.pensao_excluida
                                       WHERE pensao.cod_pensao = pensao_excluida.cod_pensao
                                         AND pensao.timestamp = pensao_excluida.timestamp)
                      AND NOT EXISTS (SELECT *
                                        FROM pessoal'||stEntidade||'.dependente_excluido
                                       WHERE servidor_dependente.cod_dependente = dependente_excluido.cod_dependente
                                         AND servidor_dependente.cod_servidor = dependente_excluido.cod_servidor)
                      AND servidor_dependente.cod_servidor = '||PEGA0SERVIDORDOCONTRATO(recuperarBufferInteiro('inCodContrato'));       
        FOR reRegistro IN  EXECUTE stSql
        LOOP
            nuValor := pega1ResultadoPensaoAlimenticiaIndividual(reRegistro.cod_dependente);
            nuQuantidade := 1;
            stInsert := 'INSERT INTO folhapagamento'||stEntidade||'.evento_rescisao_calculado_dependente
                        (cod_evento,
                         cod_registro,
                         cod_dependente,
                         timestamp_registro,
                         desdobramento,
                         valor,
                         quantidade) VALUES (
                        '||inCodEvento||',
                        '||inCodRegistro||',
                        '||reRegistro.cod_dependente||',
                        TO_TIMESTAMP('|| quote_literal(stTimestamp) ||',''yyyy-mm-dd hh24:mi:ss.us''),
                        '|| quote_literal(stDesdobramento) ||',
                        '||nuValor||',
                        '||nuQuantidade||')';
            EXECUTE stInsert;
        END LOOP; 
    END IF;    
    RETURN nuRetorno;
END;
$$ LANGUAGE 'plpgsql';
