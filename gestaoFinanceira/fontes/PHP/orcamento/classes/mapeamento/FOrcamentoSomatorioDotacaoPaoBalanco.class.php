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
    * Classe de mapeamento para a função orcamento.fn_somatorio_dotacao_pao_balanco
    * Data de Criação: 27/09/2004

    * @author Desenvolvedor: Eduardo Martins
    * @author Desenvolvedor: Cleisson Barboza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.01.13
*/

/*
$Log$
Revision 1.3  2006/07/05 20:42:02  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FOrcamentoSomatorioDotacaoPaoBalanco extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FOrcamentoSomatorioDotacaoPaoBalanco()
{
    parent::Persistente();
    $this->setTabela('orcamento.fn_somatorio_dotacao_pao_balanco');

    $this->AddCampo('dotacao'     ,'varchar',false,''    ,false,false);
    $this->AddCampo('cod_despesa' ,'integer',false,''    ,false,false);
    $this->AddCampo('descricao'   ,'varchar',false,''    ,false,false);
    $this->AddCampo('vl_projeto'  ,'numeric',false,'14.2',false,false);
    $this->AddCampo('vl_atividade','numeric',false,'14.2',false,false);
    $this->AddCampo('vl_operacao' ,'numeric',false,'14.2',false,false);
    $this->AddCampo('vl_total'    ,'numeric',false,'14.2',false,false);
    $this->AddCampo('nivel'       ,'integer',false,''    ,false,false);
}

function montaRecuperaTodos()
{
    $stSql  = " SELECT                                                                              \n";
    $stSql .= "     *,                                                                              \n";
    $stSql .= "     CASE WHEN nivel > 3 THEN                                                        \n";
    $stSql .= "         3                                                                           \n";
    $stSql .= "     ELSE                                                                            \n";
    $stSql .= "         nivel                                                                       \n";
    $stSql .= "     END as alinhamento                                                              \n";
    $stSql .= " FROM orcamento.fn_somatorio_dotacao_pao_balanco ('".$this->getDado("exercicio")."','".$this->getDado("stFiltro")."',\n";
    $stSql .= "     '".$this->getDado('dataInicial')."','".$this->getDado('dataFinal')."','".$this->getDado('stEntidades')."','".$this->getDado('stSituacao')."')\n";
    $stSql .= "     as retorno(dotacao varchar, detalhamento text, cod_despesa integer, descricao varchar,             \n";
    $stSql .= "                vl_projeto numeric, vl_atividade numeric, vl_operacao numeric,       \n";
    $stSql .= "                vl_total numeric, nivel integer)                                     \n";

    return $stSql;

}

}
