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
/**
    * Abertura de Periodo de Movimentação
    * Data de Criação: 12/11/2008
    
    
    * @author Analista: Dagiane
    * @author Desenvolvedor: Diego Lemos de Souza
    
    * @ignore
    
    $Revision: 27917 $
    $Name$
    $Author: souzadl $
    $Date: 2008-02-08 12:36:33 -0200 (Sex, 08 Fev 2008) $
    
    * Casos de uso: uc-04.05.40
*/
CREATE OR REPLACE FUNCTION pega0MaiorParcela(NUMERIC,INTEGER,INTEGER,INTEGER) RETURNS NUMERIC AS $$
DECLARE
    nuParcelaSalario            ALIAS FOR $1;
    inCodContrato               ALIAS FOR $2;
    inCodPeriodoMovimentacao    ALIAS FOR $3;
    inCodEvento                 ALIAS FOR $4;
    stSql                       VARCHAR:='';
    stEntidade                  VARCHAR:='';
    nuParcela                   NUMERIC;
BEGIN
    stEntidade := recuperarBufferTexto('stEntidade');
    stSql := 'SELECT registro_evento_ferias.quantidade
                FROM folhapagamento'||stEntidade||'.registro_evento_ferias
                JOIN folhapagamento'||stEntidade||'.ultimo_registro_evento_ferias
                  ON registro_evento_ferias.cod_registro = ultimo_registro_evento_ferias.cod_registro
                 AND registro_evento_ferias.cod_evento = ultimo_registro_evento_ferias.cod_evento
                 AND registro_evento_ferias.desdobramento = ultimo_registro_evento_ferias.desdobramento
                 AND registro_evento_ferias.timestamp = ultimo_registro_evento_ferias.timestamp
                JOIN folhapagamento'||stEntidade||'.registro_evento_ferias_parcela
                  ON registro_evento_ferias.cod_registro = registro_evento_ferias_parcela.cod_registro
                 AND registro_evento_ferias.cod_evento = registro_evento_ferias_parcela.cod_evento
                 AND registro_evento_ferias.desdobramento = registro_evento_ferias_parcela.desdobramento
                 AND registro_evento_ferias.timestamp = registro_evento_ferias_parcela.timestamp
               WHERE registro_evento_ferias.cod_contrato = '||inCodContrato||' 
                 AND registro_evento_ferias.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                 AND registro_evento_ferias.cod_evento = '||inCodEvento;
    nuParcela := selectIntoNumeric(stSql);
    IF nuParcela IS NULL OR nuParcela < nuParcelaSalario THEN
        nuParcela := nuParcelaSalario;
    END IF;
    RETURN nuParcela;
END;
$$ LANGUAGE 'plpgsql';
