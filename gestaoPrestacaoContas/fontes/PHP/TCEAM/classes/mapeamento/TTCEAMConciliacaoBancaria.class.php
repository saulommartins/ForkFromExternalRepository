<?php
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
?>
<?php
/**
    * Extensão da Classe de mapeamento

    * Data de Criação   : 28/03/2011

    * @author Desenvolvedor: Davi Ritter Aroldi

    * @ignore

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCEAMConciliacaoBancaria extends Persistente
{
function TTCEAMConciliacaoBancaria()
{
    parent::Persistente();
    $this->setDado('exercicio', Sessao::getExercicio() );
}

function montaRecuperaTodos()
{
    $stSql  = " SELECT                                                                            \n";
    $stSql .= "       '1' as forma_conciliacao                                                    \n";
    $stSql .= "      ,case when conciliacao.vl_extrato != 0 then                                   \n";
    $stSql .= "          'Conciliacao bancaria conforme extrato do dia'                           \n";
    $stSql .= "      else 'Sem saldo a conciliar' end as descricao                                    \n";
    $stSql .= "      , trim(upper(replace(plano_banco.conta_corrente,'-',''))) as conta_corrente  \n";
    $stSql .= "      , to_char(conciliacao.dt_extrato, 'dd/mm/yyyy') as data_fato                 \n";
    $stSql .= "      , to_char(conciliacao.dt_extrato, 'yymm') as sequencial                      \n";
    $stSql .= "      , '000000' as nro_cheque                                                     \n";
    $stSql .= "      , conciliacao.vl_extrato as valor_conciliado                                 \n";
    $stSql .= "      , '000000' as reservado_tce                                                  \n";
    $stSql .= "      , plano_banco.cod_plano                                                      \n";
    $stSql .= "      , plano_conta.cod_estrutural as codigo_conta                                                      \n";
    $stSql .= "      , plano_conta.exercicio as exercicio_conta                                                      \n";
    $stSql .= "   FROM                                                                            \n";
    $stSql .= "        tesouraria.conciliacao                                                     \n";
    $stSql .= "      , contabilidade.plano_banco                                                  \n";
    $stSql .= "      , monetario.banco                                                  \n";
    $stSql .= "      , contabilidade.plano_analitica                                                  \n";
    $stSql .= "      , contabilidade.plano_conta                                                 \n";
    $stSql .= "  WHERE                                                                            \n";
    $stSql .= "        conciliacao.mes           = '".$this->getDado('inMes')."'                  \n";
    $stSql .= "    AND conciliacao.exercicio     = '".$this->getDado('exercicio')."'              \n";
    $stSql .= "    AND plano_banco.cod_plano     = conciliacao.cod_plano                          \n";
    $stSql .= "    AND plano_banco.exercicio     = conciliacao.exercicio                          \n";
    $stSql .= "    AND plano_banco.cod_banco     = banco.cod_banco                          \n";
    $stSql .= "    AND plano_banco.cod_plano     = plano_analitica.cod_plano                          \n";
    $stSql .= "    AND plano_banco.exercicio     = plano_analitica.exercicio                          \n";
    $stSql .= "    AND plano_analitica.cod_conta     = plano_conta.cod_conta                          \n";
    $stSql .= "    AND plano_analitica.exercicio     = plano_conta.exercicio                          \n";
    $stSql .= "    AND plano_banco.cod_entidade IN (".$this->getDado('stEntidades').")            \n";

    return $stSql;
}
}
