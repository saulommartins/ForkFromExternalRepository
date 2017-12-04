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
* Script de função PLPGSQL
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* Lucas Stephanou 10/03/2007
*
* $Revision: 24263 $
* $Name$
* $Author: domluc $
* $Date: 2007-07-25 12:49:24 -0300 (Qua, 25 Jul 2007) $
*
* Casos de uso: uc-02.04.04
*/
/*
$Log$
Revision 1.1  2007/07/25 15:49:24  domluc
Arr Carne

*/

CREATE OR REPLACE FUNCTION tesouraria.fn_creditos_por_carne(varchar,int,int ) RETURNS SETOF RECORD AS $$
DECLARE
    stNumeracao         ALIAS FOR $1;
    inCodConvenio       ALIAS FOR $2;
    inExercicio         ALIAS FOR $3;

    stSql               VARCHAR   := '';
    reRegistro          RECORD;

BEGIN

    -- buscar valores normais do creditos agrupado por receita    
		-- extra
    stSql := ' 
            select carne.numeracao
                 , carne.cod_convenio
                 , plano_analitica.cod_plano as codigo
   			     , '|| '''extra''' ||'::varchar as tipo
                 , sum(pagamento_calculo.valor) as soma
              from arrecadacao.carne
                  inner join arrecadacao.pagamento
                           on pagamento.numeracao               = carne.numeracao
                          and pagamento.cod_convenio            = carne.cod_convenio
                  inner join arrecadacao.pagamento_calculo
                           on pagamento.numeracao               = pagamento_calculo.numeracao
                          and pagamento.ocorrencia_pagamento    = pagamento_calculo.ocorrencia_pagamento
                          and pagamento.cod_convenio            = pagamento_calculo.cod_convenio
                  inner join arrecadacao.calculo
                           on calculo.cod_calculo = pagamento_calculo.cod_calculo
                  inner join monetario.credito
                           on credito.cod_credito = calculo.cod_credito
                          and credito.cod_especie = calculo.cod_especie
                          and credito.cod_genero  = calculo.cod_genero
                          and credito.cod_natureza= calculo.cod_natureza
                  inner join contabilidade.plano_analitica_credito
                           on plano_analitica_credito.cod_credito = credito.cod_credito
                          and plano_analitica_credito.cod_especie = credito.cod_especie
                          and plano_analitica_credito.cod_genero  = credito.cod_genero
                          and plano_analitica_credito.cod_natureza= credito.cod_natureza
                  inner join contabilidade.plano_analitica
                           on plano_analitica.cod_plano = plano_analitica_credito.cod_plano
                          and plano_analitica.exercicio = plano_analitica_credito.exercicio
                  inner join contabilidade.plano_conta
                           on plano_conta.cod_conta = plano_analitica.cod_conta
                          and plano_conta.exercicio = plano_analitica.exercicio

                        where carne.numeracao = ' || stNumeracao || '
                          and carne.cod_convenio = ' || inCodConvenio || '
                          and plano_analitica_credito.exercicio = '|| inExercicio ||'

                     group by carne.numeracao
                            , carne.cod_convenio
                            , plano_analitica.cod_plano
    ';

    stSql := stSql || ' UNION ';

    stSql := stSql || ' 
            select carne.numeracao
                 , carne.cod_convenio
                 , receita.cod_receita as codigo
				 , '|| '''orc''' ||'::varchar as tipo
                 , sum(pagamento_calculo.valor) as soma
              from arrecadacao.carne
                  inner join arrecadacao.pagamento
                           on pagamento.numeracao               = carne.numeracao
                          and pagamento.cod_convenio            = carne.cod_convenio
                  inner join arrecadacao.pagamento_calculo
                           on pagamento.numeracao               = pagamento_calculo.numeracao
                          and pagamento.ocorrencia_pagamento    = pagamento_calculo.ocorrencia_pagamento
                          and pagamento.cod_convenio            = pagamento_calculo.cod_convenio
                  inner join arrecadacao.calculo
                           on calculo.cod_calculo = pagamento_calculo.cod_calculo
                  inner join monetario.credito
                           on credito.cod_credito = calculo.cod_credito
                          and credito.cod_especie = calculo.cod_especie
                          and credito.cod_genero  = calculo.cod_genero
                          and credito.cod_natureza= calculo.cod_natureza
                  inner join orcamento.receita_credito
                           on receita_credito.cod_credito = credito.cod_credito
                          and receita_credito.cod_especie = credito.cod_especie
                          and receita_credito.cod_genero  = credito.cod_genero
                          and receita_credito.cod_natureza= credito.cod_natureza
                  inner join orcamento.receita
                           on receita.cod_receita = receita_credito.cod_receita
		                  and receita.exercicio = receita_credito.exercicio
                  inner join orcamento.conta_receita
                           on receita.cod_conta = conta_receita.cod_conta
                          and receita.exercicio = conta_receita.exercicio

                        where carne.numeracao = ' || stNumeracao || '
                          and carne.cod_convenio = ' || inCodConvenio || '
                          and receita_credito.exercicio = '|| inExercicio ||'

                     group by carne.numeracao
                            , carne.cod_convenio
                            , receita.cod_receita

    ';

    stSql := stSql || ' UNION ';

    -- buscar valores dos acrescimos do creditos agrupado por receita
    stSql := stSql || ' 
            select carne.numeracao
                 , carne.cod_convenio
                 , plano_analitica.cod_plano as codigo
			     , '|| '''extra''' ||'::varchar as tipo	
                 , sum(pagamento_acrescimo.valor) as soma
              from arrecadacao.carne
                  inner join arrecadacao.pagamento
                           on pagamento.numeracao               = carne.numeracao
                          and pagamento.cod_convenio            = carne.cod_convenio
                  inner join arrecadacao.pagamento_acrescimo
                           on pagamento.numeracao               = pagamento_acrescimo.numeracao
                          and pagamento.ocorrencia_pagamento    = pagamento_acrescimo.ocorrencia_pagamento
                          and pagamento.cod_convenio            = pagamento_acrescimo.cod_convenio
                  inner join arrecadacao.calculo
                           on calculo.cod_calculo = pagamento_acrescimo.cod_calculo
                  inner join monetario.credito_acrescimo
                           on credito_acrescimo.cod_credito = calculo.cod_credito
                          and credito_acrescimo.cod_especie = calculo.cod_especie
                          and credito_acrescimo.cod_genero  = calculo.cod_genero
                          and credito_acrescimo.cod_natureza= calculo.cod_natureza
                          and credito_acrescimo.cod_acrescimo= pagamento_acrescimo.cod_acrescimo 
                          and credito_acrescimo.cod_tipo = pagamento_acrescimo.cod_tipo 
                  inner join contabilidade.plano_analitica_credito_acrescimo
                           on plano_analitica_credito_acrescimo.cod_credito = credito_acrescimo.cod_credito
                          and plano_analitica_credito_acrescimo.cod_especie = credito_acrescimo.cod_especie
                          and plano_analitica_credito_acrescimo.cod_genero  = credito_acrescimo.cod_genero
                          and plano_analitica_credito_acrescimo.cod_natureza= credito_acrescimo.cod_natureza
                          and plano_analitica_credito_acrescimo.cod_acrescimo = credito_acrescimo.cod_acrescimo 
                          and plano_analitica_credito_acrescimo.cod_tipo = credito_acrescimo.cod_tipo 
                  inner join contabilidade.plano_analitica
                           on plano_analitica.cod_plano = plano_analitica_credito_acrescimo.cod_plano
                          and plano_analitica.exercicio = plano_analitica_credito_acrescimo.exercicio
                  inner join contabilidade.plano_conta
                           on plano_conta.cod_conta = plano_analitica.cod_conta
                          and plano_conta.exercicio = plano_analitica.exercicio

                        where carne.numeracao = ' || stNumeracao || '
                          and carne.cod_convenio = ' || inCodConvenio || '
                          and plano_analitica_credito_acrescimo.exercicio = '|| inExercicio ||'

                     group by carne.numeracao
                            , carne.cod_convenio
                            , plano_analitica.cod_plano

    ';

    stSql := stSql || ' UNION ';

    stSql := stSql || ' 
            select carne.numeracao
                 , carne.cod_convenio
                 , receita.cod_receita as codigo
                 , '|| '''orc''' ||'::varchar as tipo
                 , sum(pagamento_acrescimo.valor) as soma
              from arrecadacao.carne
                  inner join arrecadacao.pagamento
                           on pagamento.numeracao               = carne.numeracao
                          and pagamento.cod_convenio            = carne.cod_convenio
                  inner join arrecadacao.pagamento_acrescimo
                           on pagamento.numeracao               = pagamento_acrescimo.numeracao
                          and pagamento.ocorrencia_pagamento    = pagamento_acrescimo.ocorrencia_pagamento
                          and pagamento.cod_convenio            = pagamento_acrescimo.cod_convenio
                  inner join arrecadacao.calculo
                           on calculo.cod_calculo = pagamento_acrescimo.cod_calculo
                  inner join monetario.credito_acrescimo
                           on credito_acrescimo.cod_credito = calculo.cod_credito
                          and credito_acrescimo.cod_especie = calculo.cod_especie
                          and credito_acrescimo.cod_genero  = calculo.cod_genero
                          and credito_acrescimo.cod_natureza= calculo.cod_natureza
                          and credito_acrescimo.cod_acrescimo= pagamento_acrescimo.cod_acrescimo 
                          and credito_acrescimo.cod_tipo = pagamento_acrescimo.cod_tipo 
                  inner join orcamento.receita_credito_acrescimo
                           on receita_credito_acrescimo.cod_credito = credito_acrescimo.cod_credito
                          and receita_credito_acrescimo.cod_especie = credito_acrescimo.cod_especie
                          and receita_credito_acrescimo.cod_genero  = credito_acrescimo.cod_genero
                          and receita_credito_acrescimo.cod_natureza= credito_acrescimo.cod_natureza
                          and receita_credito_acrescimo.cod_acrescimo= credito_acrescimo.cod_acrescimo
                          and receita_credito_acrescimo.cod_tipo= credito_acrescimo.cod_tipo
                   inner join orcamento.receita
                           on receita.cod_receita = receita_credito_acrescimo.cod_receita
                          and receita.exercicio = receita_credito_acrescimo.exercicio
                  inner join orcamento.conta_receita
                           on receita.cod_conta = conta_receita.cod_conta
                          and receita.exercicio = conta_receita.exercicio

                        where carne.numeracao = ' || stNumeracao || '
                          and carne.cod_convenio = ' || inCodConvenio || '
                          and receita_credito_acrescimo.exercicio = '|| inExercicio ||'

                     group by carne.numeracao
                            , carne.cod_convenio
                            , receita.cod_receita

    ';

    stSql := stSql || ' UNION ';


    -- buscar valores de diff de pagamento
    stSql := stSql || '
            select carne.numeracao
                 , carne.cod_convenio
                 , plano_analitica.cod_plano as codigo
 		         , '|| '''extra''' ||'::varchar as tipo
                 , sum(pagamento_diferenca.valor) as soma
              from arrecadacao.carne
                  inner join arrecadacao.pagamento
                           on pagamento.numeracao               = carne.numeracao
                          and pagamento.cod_convenio            = carne.cod_convenio
                  inner join arrecadacao.pagamento_diferenca
                           on pagamento.numeracao               = pagamento_diferenca.numeracao
                          and pagamento.ocorrencia_pagamento    = pagamento_diferenca.ocorrencia_pagamento
                          and pagamento.cod_convenio            = pagamento_diferenca.cod_convenio
                  inner join arrecadacao.calculo
                           on calculo.cod_calculo = pagamento_diferenca.cod_calculo
                  inner join monetario.credito
                           on credito.cod_credito = calculo.cod_credito
                          and credito.cod_especie = calculo.cod_especie
                          and credito.cod_genero  = calculo.cod_genero
                          and credito.cod_natureza= calculo.cod_natureza
                  inner join contabilidade.plano_analitica_credito
                           on plano_analitica_credito.cod_credito = credito.cod_credito
                          and plano_analitica_credito.cod_especie = credito.cod_especie
                          and plano_analitica_credito.cod_genero  = credito.cod_genero
                          and plano_analitica_credito.cod_natureza= credito.cod_natureza
                  inner join contabilidade.plano_analitica
                           on plano_analitica.cod_plano = plano_analitica_credito.cod_plano
                          and plano_analitica.exercicio = plano_analitica_credito.exercicio
                  inner join contabilidade.plano_conta
                           on plano_conta.cod_conta = plano_analitica.cod_conta
                          and plano_conta.exercicio = plano_analitica.exercicio

                        where carne.numeracao = ' || stNumeracao || '
                          and carne.cod_convenio = ' || inCodConvenio || '
                          and plano_analitica_credito.exercicio = '|| inExercicio ||'

                     group by carne.numeracao
                            , carne.cod_convenio
                            , plano_analitica.cod_plano

    ';

    stSql := stSql || ' UNION ';

    stSql := stSql || '
            select carne.numeracao
                 , carne.cod_convenio
                 , receita.cod_receita as codigo
				  , '|| '''orc''' ||'::varchar as tipo
                 , sum(pagamento_diferenca.valor) as soma
              from arrecadacao.carne
                  inner join arrecadacao.pagamento
                           on pagamento.numeracao               = carne.numeracao
                          and pagamento.cod_convenio            = carne.cod_convenio
                  inner join arrecadacao.pagamento_diferenca
                           on pagamento.numeracao               = pagamento_diferenca.numeracao
                          and pagamento.ocorrencia_pagamento    = pagamento_diferenca.ocorrencia_pagamento
                          and pagamento.cod_convenio            = pagamento_diferenca.cod_convenio
                  inner join arrecadacao.calculo
                           on calculo.cod_calculo = pagamento_diferenca.cod_calculo
                  inner join monetario.credito
                           on credito.cod_credito = calculo.cod_credito
                          and credito.cod_especie = calculo.cod_especie
                          and credito.cod_genero  = calculo.cod_genero
                          and credito.cod_natureza= calculo.cod_natureza
             	     inner join orcamento.receita_credito
                           on receita_credito.cod_credito = credito.cod_credito
                          and receita_credito.cod_especie = credito.cod_especie
                          and receita_credito.cod_genero  = credito.cod_genero
                          and receita_credito.cod_natureza= credito.cod_natureza
                   inner join orcamento.receita
                           on receita.cod_receita = receita_credito.cod_receita
					                and receita.exercicio = receita_credito.exercicio
                  inner join orcamento.conta_receita
                           on receita.cod_conta = conta_receita.cod_conta
                          and receita.exercicio = conta_receita.exercicio

                        where carne.numeracao = ' || stNumeracao || '
                          and carne.cod_convenio = ' || inCodConvenio || '
                          and receita_credito.exercicio = '|| inExercicio ||'

                     group by carne.numeracao
                            , carne.cod_convenio
                            , receita.cod_receita

    ';



    -- agrupar consultas
    stSql := ' select numeracao, cod_convenio, codigo , tipo, sum(soma) 
                from ( ' || stSql || ' ) as tabela
                group by numeracao, cod_convenio, codigo , tipo ';

    FOR reRegistro IN EXECUTE stSql LOOP
        return next reRegistro;
    END LOOP;

END;

$$ language 'plpgsql';
