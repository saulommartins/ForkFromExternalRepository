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
* URBEM Solugues de Gestco Pzblica Ltda
* www.urbem.cnm.org.br

* $Revision: 23095 $
* $Name$
* $Autor: Marcia $
* Date: 2006/06/01 10:50:00 $
*
* Caso de uso: uc-04.05.15
* Caso de uso: uc-04.05.48
*
* Objetivo: 
* verifica a diferenca de salario familia para servidores com RGPS ( inss )
* mas que tem o regime de trabalho regido pelo estatuto da prefeitura.
* Necessario efetuar o pagamento de complemento de salario familia caso 
* o valor do salario familia como estatutario seja superior ao do inss ou se 
* existir diferenca em relacao ao nr. de dependentes, em especial nos casos
* diferenca de idade entre a avaliacao do estatuto e o da previdencia inss.
*/



create or replace FUNCTION  pega1ValorComplementoSalarioFamiliaEstatutarioRGPS() RETURNS numeric as ' 
DECLARE

   inCodConfiguracao                 INTEGER;
   inCodRegimePrevidenciario         INTEGER;
   inCodRegimeTrabalhista            INTEGER;
   stEventoProventoSF                VARCHAR;

   nuValorSalarioFamiliaRGPS         NUMERIC;
   nuValorTotalSalarioFamiliaRGPS    NUMERIC;
   nuQtdSalarioFamiliaRGPS           NUMERIC;

   nuDiferenca                       NUMERIC;

   inQtdSFEstatutario                INTEGER;
   nuBaseSFEstatutario               NUMERIC;
   nuValorSFEstatutario              NUMERIC;
   nuValorTotalSFEstatutario         NUMERIC;

stEntidade VARCHAR := recuperarBufferTexto(''stEntidade'');
 BEGIN


   inCodConfiguracao := recuperarBufferInteiro(  ''inCodConfiguracao''  );

   -- somente executar para regime estatutario e previdencia oficial RGPS (inss)
   inCodRegimePrevidenciario := pega1RegimePrevidenciarioPrevidenciaOficial();
--   inCodRegimePrevidenciario := 1;
   inCodRegimeTrabalhista := pega1RegimeTrabalhista();
--   inCodRegimeTrabalhista := 2;


   IF ( inCodRegimeTrabalhista = 2 ) AND (inCodRegimePrevidenciario = 1) THEN

       stEventoProventoSF := pega2EventoProventoSalarioFamilia();

       -- busca os valores ja pagos no salario familia como inss
       nuValorTotalSalarioFamiliaRGPS := pegaValorCalculado(stEventoProventoSF,inCodConfiguracao);
       nuQtdSalarioFamiliaRGPS := pegaQuantidadeCalculado(stEventoProventoSF,inCodConfiguracao);

       -- valor por dependente
       nuValorSalarioFamiliaRGPS  := arredondar( nuValorTotalSalarioFamiliaRGPS / nuQtdSalarioFamiliaRGPS, 2 );


       inQtdSFEstatutario :=  pega1QtdDependentesSalarioFamiliaEstatutario();

--********************
       -- recuperar o buffer da base de salario familia para estatutario
--teste
       nuBaseSFEstatutario := 1000.00;
--***************************

       -- valor por dependente
       nuValorSFEstatutario := pega1ValorSalarioFamiliaEstatutario( nuBaseSFEstatutario );
       inQtdSFEstatutario :=  pega1QtdDependentesSalarioFamiliaEstatutario();
       nuValorTotalSFEstatutario := arredondar( nuValorSFEstatutario * inQtdSFEstatutario,2);

       nuDiferenca := 0;


       -- nr. igual entre dependentes 
       --IF converteInteiroParaNumerico(inQtdSFEstatutario) = nuQtdSalarioFamiliaRGPS THEN
       --    IF nuValorTotalSalarioFamiliaRGPS < nuValorTotalSFEstatutario THEN
       --        nuDiferenca :=  nuValorTotalSFEstatutario - nuValorTotalSalarioFamiliaRGPS ;
       --    END IF;
       --END IF;


       -- nr.maior de dependentes como estaturario
       IF (    (inQtdSFEstatutario >= nuQtdSalarioFamiliaRGPS)  
            or (nuValorSFEstatutario > nuValorSalarioFamiliaRGPS )
          )
       THEN

           

           nuDiferenca := arredondar( (converteInteiroParaNumerico(inQtdSFEstatutario)
                                       - nuQtdSalarioFamiliaRGPS
                                      ) 
                                    * nuValorSFEstatutario 
                                    ,2);
           nuDiferenca := nuDiferenca 
             + (   arredondar( nuQtdSalarioFamiliaRGPS
                               * nuValorSFEstatutario 
                             ,2) 
                 - nuValorTotalSalarioFamiliaRGPS 
               );

       END IF;

       -- nr.menor de dependentes como estatutario
       IF nuDiferenca < 0 THEN
          nuDiferenca = 0;
       END IF;


    END IF;

    return nuDiferenca;


RETURN BORETORNO;

END;
' LANGUAGE 'plpgsql';
