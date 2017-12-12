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
    * Classe de mapeamento da tabela ORCAMENTO.RECEITA
    * Data de Criação: 24/09/2004

    * @author Desenvolvedor: Eduardo Martins
    * @author Desenvolvedor: Gustavo Tourinho

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.01.15
*/

/*
$Log$
Revision 1.6  2006/07/05 20:42:02  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FOrcamentoSomatorioDotacaoFuncionalProgramaticaRecurso extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FOrcamentoSomatorioDotacaoFuncionalProgramaticaRecurso()
{
    parent::Persistente();
    $this->setTabela("orcamento.fn_somatorio_dotacao_funcional_programatica_recurso");

    $this->AddCampo('dotacao'               ,'varchar',false,''    ,false,false);
    $this->AddCampo('cod_despesa'           ,'integer',false,''    ,false,false);
    $this->AddCampo('descricao'             ,'varchar',false,''    ,false,false);
    $this->AddCampo('vl_ordinario'          ,'numeric',false,'14.2',false,false);
    $this->AddCampo('vl_vinculado'          ,'numeric',false,'14.2',false,false);
    $this->AddCampo('vl_total'              ,'numeric',false,'14.2',false,false);
    $this->AddCampo('nivel'                 ,'integer',false,''    ,false,false);
}

function montaRecuperaTodos()
{
    $stSql  = " SELECT                                                                                         \n";
    $stSql .= "     *                                                                                          \n";
    $stSql .= " FROM                                                                                           \n";
    $stSql .= "     ".$this->getTabela()."('".$this->getDado("exercicio")."','".$this->getDado("stFiltro")."') \n";
    $stSql .= "     AS RETORNO(                                                                                \n";
    $stSql .= "     dotacao varchar,                                                                           \n";
    $stSql .= "     cod_despesa integer,                                                                       \n";
    $stSql .= "     descricao varchar,                                                                         \n";
    $stSql .= "     vl_ordinario numeric,                                                                      \n";
    $stSql .= "     vl_vinculado numeric,                                                                      \n";
    $stSql .= "     vl_total numeric,                                                                          \n";
    $stSql .= "     nivel integer)                                                                             \n";

    return $stSql;
}

}
