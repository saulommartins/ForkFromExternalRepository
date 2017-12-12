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
  * Página de
  * Data de criação : 11/07/2005

    * @author Analista:
    * @author Programador: Fernando Zank Correa Evangelista

    $Revision: 30668 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    Caso de uso: uc-02.02.24
**/

/*
$Log$
Revision 1.7  2006/07/05 20:50:14  cleisson
Adicionada tag Log aos arquivos

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FContabilidadeDiarioBanco extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FContabilidadeDiarioBanco()
{
    parent::Persistente();
    $this->setTabela('contabilidade.fn_rl_diario_banco');

    $this->AddCampo('cod_estrutural'        ,'varchar',false,''    ,false,false);
    $this->AddCampo('cod_plano'             ,'integer',false,''    ,false,false);
    $this->AddCampo('nivel'                 ,'integer',false,''    ,false,false);
    $this->AddCampo('nom_conta'             ,'varchar',false,''    ,false,false);
    $this->AddCampo('vl_saldo_anterior'     ,'numeric',false,'14.2',false,false);
    $this->AddCampo('vl_saldo_debitos'      ,'numeric',false,'14.2',false,false);
    $this->AddCampo('vl_saldo_creditos'     ,'numeric',false,'14.2',false,false);
    $this->AddCampo('vl_saldo_atual'        ,'numeric',false,'14.2',false,false);
}

function montaRecuperaTodos()
{
    $stSql  = " SELECT                                                                                      \n";
    $stSql .= "     *                                                                                       \n";
    $stSql .= " FROM                                                                                        \n";
    $stSql .= "
".$this->getTabela()."('".$this->getDado("exercicio")."','".$this->getDado("stFiltro")."','".$this->getDado("stDtInicial")."','".$this->getDado("stDtFinal")."',".(trim($this->getDado("inCodContaInicial"))?$this->getDado("inCodContaInicial"):0).",'".(trim($this->getDado("inCodContaFinal"))?$this->getDado("inCodContaFinal"):0)."' )\n";
    $stSql .= "     as retorno( cod_estrutural varchar                                                      \n";
    $stSql .= "                ,cod_plano integer                                                               \n";
    $stSql .= "                ,nivel integer                                                               \n";
    $stSql .= "                ,nom_conta varchar                                                           \n";
    $stSql .= "                ,vl_saldo_anterior numeric                                                   \n";
    $stSql .= "                ,vl_saldo_debitos  numeric                                                   \n";
    $stSql .= "                ,vl_saldo_creditos numeric                                                   \n";
    $stSql .= "                ,vl_saldo_atual    numeric                                                   \n";
    $stSql .= "                )                                                                            \n";

    return $stSql;
}

}
