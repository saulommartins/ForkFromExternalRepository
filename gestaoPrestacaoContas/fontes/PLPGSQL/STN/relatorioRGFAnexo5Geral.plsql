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
* Titulo do arquivo RGF Anexo 5 - Tabela de Dados Gerais
* Data de Criação : 09/06/2008 


* @author Analista Gelson
* @author Desenvolvedor eduardoschitz 

* @package URBEM
* @subpackage 

$Id:$

*/

CREATE OR REPLACE FUNCTION stn.fn_rgf_anexo5_geral (  varchar, varchar , varchar , varchar , varchar ) RETURNS RECORD AS $$ 
DECLARE
    stExercicio alias for $1;
    dtInicial   alias for $2;
    dtFim       alias for $3;
    stEntidades alias for $4;
    stRPPS      alias for $5;
    reRegistro RECORD;
    stSql   varchar := '';
BEGIN

stSql := '
SELECT 
      stn.pl_saldo_contas (   ''' || stExercicio || '''
                            , ''' || dtInicial || '''
                            , ''' || dtFim || '''
                            , '' plano_conta.cod_estrutural like ''''1.1.1.1.1% ''''''
                            , ''' || stEntidades || '''
                            , ''' || stRPPS || '''
    ) as caixa 

    , stn.pl_saldo_contas (   ''' || stExercicio || '''
                            , ''' || dtInicial || '''
                            , ''' || dtFim || '''
                            , '' plano_conta.cod_estrutural like ''''1.1.1.1.2%'''' AND recurso_direto.tipo = ''''L'''' ''
                            , ''' || stEntidades || '''
                            , ''' || stRPPS || '''
    ) as conta_movimento 

    , stn.pl_saldo_contas (   ''' || stExercicio || '''
                            , ''' || dtInicial || '''
                            , ''' || dtFim || '''
                            , '' plano_conta.cod_estrutural like ''''1.1.1.1.2%'''' AND recurso_direto.tipo = ''''V'''' ''
                            , ''' || stEntidades || '''
                            , ''' || stRPPS || '''
    ) as contas_vinculadas

    , stn.pl_saldo_contas (   ''' || stExercicio || '''
                            , ''' || dtInicial || '''
                            , ''' || dtFim || '''
                            , '' plano_conta.cod_estrutural like ''''1.1.1.1.3%'''' ''
                            , ''' || stEntidades || '''
                            , ''' || stRPPS || '''
    ) as aplicacoes_financeiras

    , stn.pl_saldo_contas (   ''' || stExercicio || '''
                            , ''' || dtInicial || '''
                            , ''' || dtFim || '''
                            , '' plano_conta.cod_estrutural like ''''1.9.3.2.9%'''' ''
                            , ''' || stEntidades || '''
                            , ''' || stRPPS || '''
    ) as outras_disponibilidades_financeiras

    , stn.pl_saldo_contas (   ''' || stExercicio || '''
                            , ''' || dtInicial || '''
                            , ''' || dtFim || '''
                            , '' plano_conta.cod_estrutural like ''''2.1.1%'''' ''
                            , ''' || stEntidades || '''
                            , ''' || stRPPS || '''
    ) as depositos

    , stn.pl_saldo_contas (   ''' || stExercicio || '''
                            , ''' || dtInicial || '''
                            , ''' || dtFim || '''
                            , '' ( plano_conta.cod_estrutural like ''''2.1.2.1.1.01%'''' 
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.1.03.01%''''
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.2.01%'''' 
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.2.03.01%''''
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.3.01.00.01%'''' 
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.3.02.00.01%'''' 
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.3.03.00.01%'''' 
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.3.04.00.01%'''' 
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.3.16.00.01%'''' 
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.3.99.01.01%'''' 
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.3.99.02.01%'''' 
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.3.99.03.01%'''' 
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.5.09.00.01%'''' 
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.7.05.01.01%'''' 
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.7.05.02.01%'''' 
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.7.05.03.01%'''' 
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.9.08.01%'''' 
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.9.08.03%'''' 
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.9.99.00.01%'''' 
                            )''
                            , ''' || stEntidades || '''
                            , ''' || stRPPS || '''
    ) as rpp_exercicio

    , stn.pl_saldo_contas (   ''' || stExercicio || '''
                            , ''' || dtInicial || '''
                            , ''' || dtFim || '''
                            , '' ( plano_conta.cod_estrutural like ''''2.1.2.1.1.02%'''' 
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.1.03.02%'''' 
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.2.02%'''' 
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.2.03.02%''''
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.3.01.00.02%'''' 
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.3.01.00.03%'''' 
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.3.01.00.04%'''' 
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.3.02.00.02%'''' 
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.3.03.00.02%'''' 
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.3.03.00.03%'''' 
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.3.03.00.04%'''' 
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.3.04.00.02%'''' 
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.3.04.00.03%'''' 
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.3.04.00.04%'''' 
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.3.16.00.02%'''' 
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.3.99.01.02%'''' 
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.3.99.02.02%'''' 
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.3.99.03.02%'''' 
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.5.09.00.02%''''
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.5.09.00.03%''''
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.5.09.00.04%''''
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.7.04%''''
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.7.05.01.02%''''
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.7.05.01.03%''''
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.7.05.01.04%''''
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.7.05.02.02%''''
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.7.05.02.03%''''
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.7.05.02.04%''''
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.7.05.03.02%''''
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.7.05.03.03%''''
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.7.05.03.04%''''
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.9.08.02%''''
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.9.08.04%''''
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.9.99.00.02%''''
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.9.99.00.03%''''
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.9.99.00.04%''''
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.9.99.00.20%''''
                            )''
                            , ''' || stEntidades || '''
                            , ''' || stRPPS || '''
    ) as rpp_exercicios_anteriores

    , stn.pl_saldo_contas (   ''' || stExercicio || '''
                            , ''' || dtInicial || '''
                            , ''' || dtFim || '''
                            , '' ( plano_conta.cod_estrutural like ''''2.1.2.9.0%'''' 
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.6.02.02%'''' 
                                OR plano_conta.cod_estrutural like ''''2.1.2.2.2%'''' 
                                OR plano_conta.cod_estrutural like ''''2.1.2.6.0%'''' 
                            )''
                            , ''' || stEntidades || '''
                            , ''' || stRPPS || '''
    ) as outras_obrigacoes_financeiras

    , stn.calcula_restos_nao_processados (    ''' || stExercicio || '''
                                            , ''' || dtInicial || '''
                                            , ''' || dtFim || '''
                                            , ''' || stEntidades || '''
                                            , ''' || stRPPS || '''
    ) as outras_obrigacoes_financeiras

'; 

    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN reRegistro;
    END LOOP;
    
end;
$$ LANGUAGE 'plpgsql';
