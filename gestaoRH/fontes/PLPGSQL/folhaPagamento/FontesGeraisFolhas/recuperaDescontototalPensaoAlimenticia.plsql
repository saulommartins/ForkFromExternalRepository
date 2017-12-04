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

CREATE OR REPLACE FUNCTION recuperaDescontoTotalPensaoAlimenticia() RETURNS BOOLEAN AS $$
DECLARE

  boRetorno BOOLEAN := TRUE;
  inQuantDependentes INTEGER := 0;
  nuCargaHorariaSalario NUMERIC := 0.00;
  nuHorasMensais NUMERIC := 0.00;
  nuQtdeDias NUMERIC := 0.00;
  nuQuantidade NUMERIC := 0.00;
  nuValor NUMERIC := 0.00;
  salario NUMERIC := 0.00;
  stTipofolha VARCHAR := '';
  stTitc BOOLEAN := TRUE;
BEGIN
boRetorno := TRUE;
nuCargaHorariaSalario := pega1CampoSalarioHorasMensais(  );
nuValor := pega1ResultadoPensaoAlimenticia(  );
inQuantDependentes := pega1QtdDependentesPensaoAlimenticia(  );
nuHorasMensais := pega0MontaBaseQuantidadeFolhas(  '1' , ''  );
nuQuantidade := converteInteiroParaNumerico(  inQuantDependentes  );
IF   stTipofolha  =  'F' THEN
    nuQtdeDias := (nuHorasMensais *30)/nuCargaHorariaSalario ;
    nuValor := (nuValor /30)*nuQtdeDias ;
nuValor := criarBufferNumerico(  'descpensaoalimenticia' , nuValor  );
END IF;
boRetorno := gravarEvento(  nuValor , nuQuantidade  );
RETURN boRetorno;
END;
$$ LANGUAGE 'plpgsql';
