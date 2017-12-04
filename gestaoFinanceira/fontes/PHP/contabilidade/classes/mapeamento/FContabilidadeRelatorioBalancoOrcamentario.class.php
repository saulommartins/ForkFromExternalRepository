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
    * Classe de mapeamento para relatorio do anexo 12
    * Data de Criação: 28/04/2005

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Author: eduardoschitz $
    $Date: 2008-03-26 17:57:40 -0300 (Qua, 26 Mar 2008) $

    * Casos de uso: uc-02.02.09

*/

/*
$Log$
Revision 1.6  2006/07/05 20:50:14  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FContabilidadeRelatorioBalancoOrcamentario extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FContabilidadeRelatorioBalancoOrcamentario()
{
    parent::Persistente();
    $this->setTabela('contabilidade.fn_relatorio_balanco_orcamentario');

    $this->setCampoCod('');
    $this->setComplementoChave('');

    $this->AddCampo( 'exercicio'    ,'char'   ,true,'04',false,false );
    $this->AddCampo( 'cod_entidade' ,'varchar',true,''  ,false,false );
    $this->AddCampo( 'dt_inicial'   ,'date'   ,true,''  ,false,false );
    $this->AddCampo( 'dt_final'     ,'date'   ,true,''  ,false,false );
    $this->AddCampo( 'situacao'     ,'varchar',true,''  ,false,false );
}

function montaRecuperaTodos()
{
    $stSql .= "SELECT *                                                              \n";
    $stSql .= "FROM ".$this->getTabela()."( '"  .$this->getDado('exercicio')     ."' \n";
    $stSql .= "                            ,'0,".$this->getDado('cod_entidade')  ."' \n";
    $stSql .= "                            ,'"  .$this->getDado('dt_inicial')    ."' \n";
    $stSql .= "                            ,'"  .$this->getDado('dt_final')      ."' \n";
    $stSql .= "                            ,'"  .$this->getDado('situacao')      ."' \n";
    $stSql .= ") as retorno( reduzido_receita      varchar                           \n";
    $stSql .= "             ,descricao_receita     varchar                           \n";
    $stSql .= "             ,vl_inicial_receita    numeric(14,2)                     \n";
    $stSql .= "             ,vl_atual_receita      numeric(14,2)                     \n";
    $stSql .= "             ,reduzido_despesa      varchar                           \n";
    $stSql .= "             ,descricao_despesa     varchar                           \n";
    $stSql .= "             ,vl_inicial_despesa    numeric(14,2)                     \n";
    $stSql .= "             ,vl_atual_despesa      numeric(14,2)                     \n";
    $stSql .= "             ,nivel                 integer                           \n";
    $stSql .= "             ,tipo                  varchar                           \n";
    $stSql .= ")                                                                     \n";

    return $stSql;
}

}
