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
--    * Data de Criação: 02/03/2009
--
--
--    * @author Analista: Dagiane Vieira
--    * @author Desenvolvedor: Diego Lemos de Souza
--
--    * @package URBEM
--    * @subpackage
--
--    $Revision: 25971 $
--    $Name$
--    $Author: souzadl $
--    $Date: 2007-10-10 13:08:17 -0300 (Qua, 10 Out 2007) $
--
--    * Casos de uso: uc-04.05.10
--*/
CREATE OR REPLACE FUNCTION deletarDeducaoDependente(INTEGER,INTEGER,VARCHAR,VARCHAR) RETURNS BOOLEAN as $$
DECLARE
    inCodRegistro               ALIAS FOR $1;
    inCodEvento                 ALIAS FOR $2;
    stDesdobramento             ALIAS FOR $3;
    stTimestamp                 ALIAS FOR $4;
    stSql                       VARCHAR;
    stEntidade                  VARCHAR;
    stTipoFolha                 VARCHAR;
    inCodContrato               INTEGER;
    inCodPeriodoMovimentacao    INTEGER;
    inCodTipo                   INTEGER;
    inCodEventoDep              INTEGER;
    inCodServidor               INTEGER;
    inNumCgm                    INTEGER;
    crCursor                    REFCURSOR;
BEGIN
    stEntidade      := recuperarBufferTexto('stEntidade');
    stTipoFolha     := recuperarBufferTexto('stTipoFolha');

    stSql := 'SELECT tabela_irrf_evento.cod_evento
                FROM folhapagamento'|| stEntidade ||'.tabela_irrf_evento
                   , (  SELECT cod_tabela
                             , max(timestamp) as timestamp
                          FROM folhapagamento'|| stEntidade ||'.tabela_irrf_evento
                      GROUP BY cod_tabela
                     ) as max_tabela_irrf_evento
               WHERE tabela_irrf_evento.cod_tabela = max_tabela_irrf_evento.cod_tabela
                 AND tabela_irrf_evento.timestamp  = max_tabela_irrf_evento.timestamp
                 AND tabela_irrf_evento.cod_tipo = 1';
    inCodEventoDep := selectIntoInteger(stSql);

    IF inCodEventoDep = inCodEvento THEN
        IF stTipoFolha = 'S' THEN
            stSql := '    SELECT registro_evento_periodo.cod_contrato
                               , registro_evento_periodo.cod_periodo_movimentacao
                            FROM folhapagamento'|| stEntidade ||'.registro_evento_periodo
                      INNER JOIN folhapagamento'|| stEntidade ||'.registro_evento
                              ON registro_evento_periodo.cod_registro = registro_evento.cod_registro
                           WHERE registro_evento_periodo.cod_registro = '|| inCodRegistro ||'
                             AND registro_evento.cod_evento = '|| inCodEventoDep ||'
                               ;
                     ';
            inCodTipo := 2;
        END IF;
        IF stTipoFolha = 'C' THEN
            stSql := 'SELECT registro_evento_complementar.cod_contrato
                           , registro_evento_complementar.cod_periodo_movimentacao
                        FROM folhapagamento'|| stEntidade ||'.registro_evento_complementar
                       WHERE registro_evento_complementar.cod_registro = '|| inCodRegistro ||'
                         AND registro_evento_complementar.cod_configuracao = '|| stDesdobramento::integer ||'
                         AND registro_evento_complementar.timestamp = '|| quote_literal(stTimestamp) ||'
                         AND registro_evento_complementar.cod_evento = '|| inCodEventoDep ||'
                               ;
                     ';
            inCodTipo := 3;
        END IF;
        
        
        IF stTipoFolha = 'F' THEN
            stSql := 'SELECT registro_evento_ferias.cod_contrato
                           , registro_evento_ferias.cod_periodo_movimentacao
                        FROM folhapagamento'|| stEntidade ||'.registro_evento_ferias
                       WHERE registro_evento_ferias.cod_registro = '|| inCodRegistro ||'
                         AND registro_evento_ferias.desdobramento = '|| quote_literal(stDesdobramento) ||'
                         AND registro_evento_ferias.timestamp = '|| quote_literal(stTimestamp) ||'
                         AND registro_evento_ferias.cod_evento = '|| inCodEventoDep ||'
                               ;
                     ';
            inCodTipo := 1;
        END IF;


        IF stTipoFolha = 'R' THEN
            stSql := 'SELECT registro_evento_rescisao.cod_contrato
                           , registro_evento_rescisao.cod_periodo_movimentacao
                        FROM folhapagamento'|| stEntidade ||'.registro_evento_rescisao
                       WHERE registro_evento_rescisao.cod_registro = '|| inCodRegistro ||'
                         AND registro_evento_rescisao.desdobramento = '|| quote_literal(stDesdobramento) ||'
                         AND registro_evento_rescisao.timestamp = '|| quote_literal(stTimestamp) ||'
                         AND registro_evento_rescisao.cod_evento = '|| inCodEventoDep ||'
                               ;
                     ';
            inCodTipo := 5;
        END IF;
        IF stTipoFolha = 'D' THEN
            stSql := 'SELECT registro_evento_decimo.cod_contrato
                           , registro_evento_decimo.cod_periodo_movimentacao
                        FROM folhapagamento'|| stEntidade ||'.registro_evento_decimo
                       WHERE registro_evento_decimo.cod_registro = '|| inCodRegistro ||'
                         AND registro_evento_decimo.desdobramento = '|| quote_literal(stDesdobramento) ||'
                         AND registro_evento_decimo.timestamp = '|| quote_literal(stTimestamp) ||'
                         AND registro_evento_decimo.cod_evento = '|| inCodEventoDep ||'
                               ;
                     ';
            inCodTipo := 4;
        END IF;

        OPEN crCursor FOR EXECUTE stSql;
            FETCH crCursor INTO inCodContrato,inCodPeriodoMovimentacao;
        CLOSE crCursor;  

        IF inCodContrato IS NOT NULL AND inCodPeriodoMovimentacao IS NOT NULL THEN
            inCodServidor := pega0ServidorDoContrato(inCodContrato);
            inNumCgm := pega0NumcgmServidor(inCodServidor);
            IF stTipoFolha = 'C' THEN
                stSql := 'DELETE FROM folhapagamento'|| stEntidade ||'.deducao_dependente_complementar
                           WHERE deducao_dependente_complementar.numcgm = '|| inNumCgm ||'
                             AND deducao_dependente_complementar.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                             AND deducao_dependente_complementar.cod_tipo = '|| inCodTipo ||'
                               ;
                         ';
                EXECUTE stSql;
            END IF;

            stSql := 'DELETE FROM folhapagamento'|| stEntidade ||'.deducao_dependente
                       WHERE deducao_dependente.numcgm = '|| inNumCgm ||'
                         AND deducao_dependente.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                         AND deducao_dependente.cod_tipo = '|| inCodTipo ||'
                           ;
                     ';
            EXECUTE stSql;
        END IF;
    END IF;

    RETURN TRUE;
END;
$$ LANGUAGE 'plpgsql';

-- SELECT deletarDeducaoDependente(261593,5,'','');
