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

    * Data de Criação   : 24/04/2007

    * @author Analista: Gelson
    * @author Desenvolvedor: Alexandre Melo

    * @ignore

    * Casos de uso: uc-xx.xx.xx
*/
/*
$Log$
Revision 1.4  2007/05/11 20:23:14  hboaventura
Arquivos para geração do TCEPB

Revision 1.3  2007/05/10 21:39:47  hboaventura
Arquivos para geração do TCEPB

Revision 1.2  2007/04/24 18:46:02  melo
Construção

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTPBConciliacaoBancaria extends Persistente
{
function TTPBConciliacaoBancaria()
{
    parent::Persistente();
    $this->setDado('exercicio', Sessao::getExercicio() );
}

function montaRecuperaTodos()
{
    $stSql  = " SELECT                                                                                     \n";
    $stSql .= "       '1' as forma_conciliacao                                                             \n";
    $stSql .= "      ,case when conciliacao.vl_extrato != 0 then                                           \n";
    $stSql .= "          'Conciliacao bancaria conforme extrato do dia'                                    \n";
    $stSql .= "      else 'Sem saldo a conciliar' end as descricao                                         \n";
    $stSql .= "      , trim(upper(replace(plano_banco.conta_corrente,'-',''))) as conta_corrente           \n";
    $stSql .= "      , to_char(conciliacao.dt_extrato, 'dd/mm/yyyy') as data_fato                          \n";
    $stSql .= "      , to_char(conciliacao.dt_extrato, 'yymm') as sequencial                               \n";
    $stSql .= "      , '000000' as nro_cheque                                                              \n";
    $stSql .= "      , LPAD(REPLACE(conciliacao.vl_extrato::VARCHAR,'.',','), 16, '0') as valor_conciliado \n";
    $stSql .= "      , '000000' as reservado_tce                                                           \n";
    $stSql .= "      , plano_banco.cod_plano                                                               \n";
    $stSql .= "   FROM                                                                                     \n";
    $stSql .= "        tesouraria.conciliacao                                                              \n";
    $stSql .= "      , contabilidade.plano_banco                                                           \n";
    $stSql .= "      , monetario.banco                                                                     \n";
    $stSql .= "  WHERE                                                                                     \n";
    $stSql .= "        conciliacao.mes           = '".$this->getDado('inMes')."'                           \n";
    $stSql .= "    AND conciliacao.exercicio     = '".$this->getDado('exercicio')."'                       \n";
//    $stSql .= "    AND conciliacao.vl_extrato   != 0.00                                                  \n";
    $stSql .= "    AND plano_banco.cod_plano     = conciliacao.cod_plano                                   \n";
    $stSql .= "    AND plano_banco.exercicio     = conciliacao.exercicio                                   \n";
    $stSql .= "    AND plano_banco.cod_banco     = banco.cod_banco                                         \n";
    $stSql .= "    AND banco.num_banco     != '000'                                                        \n";
    $stSql .= "    AND banco.num_banco     != '999'                                                        \n";
    $stSql .= "    AND plano_banco.cod_entidade IN (".$this->getDado('stEntidades').")                     \n";

    return $stSql;
}
}
