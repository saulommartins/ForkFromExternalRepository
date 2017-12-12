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
    * Classe de mapeamento da tabela FN_ORCAMENTO_SOMATORIO_DESPESA
    * Data de Criação: 29/11/2004

    * @author Analista: Jorge Ribarr
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.02.22
*/

/*
$Log$
Revision 1.7  2006/07/05 20:50:14  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FContabilidadeBalanceteVerificacao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FContabilidadeBalanceteVerificacao()
{
    parent::Persistente();
    $this->setTabela('contabilidade.fn_rl_balancete_verificacao');

    $this->AddCampo('cod_estrutural'        ,'varchar',false,''    ,false,false);
    $this->AddCampo('nivel'                 ,'integer',false,''    ,false,false);
    $this->AddCampo('nom_conta'             ,'varchar',false,''    ,false,false);
    $this->AddCampo('vl_saldo_anterior'     ,'numeric',false,'14.2',false,false);
    $this->AddCampo('vl_saldo_debitos'      ,'numeric',false,'14.2',false,false);
    $this->AddCampo('vl_saldo_creditos'     ,'numeric',false,'14.2',false,false);
    $this->AddCampo('vl_saldo_atual'        ,'numeric',false,'14.2',false,false);
}

function montaRecuperaTodos()
{
    $stSql  = " SELECT cod_estrutural                                                                       \n";
    $stSql .= "      , nivel                                                                                \n";
    $stSql .= "      , nom_conta                                                                            \n";
    $stSql .= "      , cod_sistema                                                                          \n";
    $stSql .= "      , indicador_superavit                                                                  \n";
    if ($this->getDado('exportacao_blc')) {
        $stSql .= "      , ABS(vl_saldo_anterior) AS vl_saldo_anterior                                      \n";
        $stSql .= "      , ABS(vl_saldo_debitos) AS vl_saldo_debitos                                        \n";
        $stSql .= "      , ABS(vl_saldo_creditos) AS vl_saldo_creditos                                      \n";
        $stSql .= "      , ABS(vl_saldo_atual) AS vl_saldo_atual                                            \n";
    } else {
        $stSql .= "      , vl_saldo_anterior                                                                \n";
        $stSql .= "      , vl_saldo_debitos                                                                 \n";
        $stSql .= "      , vl_saldo_creditos                                                                \n";
        $stSql .= "      , vl_saldo_atual                                                                   \n";
    }
    $stSql .= " FROM                                                                                        \n";
    $stSql .= "   ".$this->getTabela()."('".$this->getDado("exercicio")."','".$this->getDado("stFiltro")."','".$this->getDado("stDtInicial")."','".$this->getDado("stDtFinal")."','".$this->getDado("chEstilo")."'::CHAR)\n";
    $stSql .= "     as retorno( cod_estrutural varchar                                                      \n";
    $stSql .= "                ,nivel integer                                                               \n";
    $stSql .= "                ,nom_conta varchar                                                           \n";
    $stSql .= "                ,cod_sistema integer                                                         \n";
    $stSql .= "                ,indicador_superavit char(12)                                                \n";
    $stSql .= "                ,vl_saldo_anterior numeric                                                   \n";
    $stSql .= "                ,vl_saldo_debitos  numeric                                                   \n";
    $stSql .= "                ,vl_saldo_creditos numeric                                                   \n";
    $stSql .= "                ,vl_saldo_atual    numeric                                                   \n";
    $stSql .= "                )                                                                            \n";

    return $stSql;
}

}
