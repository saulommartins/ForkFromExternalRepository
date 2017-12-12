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
    * Classe de mapeamento da tabela FN_ORCAMENTO_SOMATORIO_PROGRAMA_TRABALHO_BALANCO
    * Data de Criação: 27/09/2004

    * @author Analista: Jorge Ribarr
    * @author Desenvolvedor: Diego Barbosa Victoria
    * @author Desenvolvedor: Eduardo Martins
    * @author Desenvolvedor: Gustavo Passos Tourinho
    * @author Desenvolvedor: Cleisson Barboza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.01.17
*/

/*
$Log$
Revision 1.3  2006/07/05 20:42:02  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FOrcamentoSomatorioProgramaTrabalhoBalanco Extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FOrcamentoSomatorioProgramaTrabalhoBalanco()
{
    parent::Persistente();
    $this->setTabela('orcamento.fn_somatorio_programa_trabalho_balanco');

    $this->AddCampo('dotacao'    ,'varchar',false,''    ,false,false);
    $this->AddCampo('cod_despesa','integer',false,''    ,false,false);
    $this->AddCampo('descricao'  ,'varchar',false,''    ,false,false);
    $this->AddCampo('vl_corrente','numeric',false,'14.2',false,false);
    $this->AddCampo('vl_capital' ,'numeric',false,'14.2',false,false);
    $this->AddCampo('vl_total'   ,'numeric',false,'14.2',false,false);
    $this->AddCampo('nivel'      ,'integer',false,''    ,false,false);
}

function montaRecuperaTodos()
{
    $stSql  = " SELECT                                                                                        \n";
    $stSql .= "     *                                                                                         \n";
    $stSql .= " FROM                                                                                          \n";
    $stSql .= "    ".$this->getTabela()."('".$this->getDado("exercicio")."','".$this->getDado("stFiltro")."',\n";
    $stSql .= "     '".$this->getDado('dataInicial')."','".$this->getDado('dataFinal')."','".$this->getDado('stEntidades')."','".$this->getDado('stSituacao')."')\n";     $stSql .= "     as retorno(dotacao varchar,cod_despesa integer, descricao varchar,                        \n";
    $stSql .= "                vl_corrente numeric, vl_capital numeric, vl_total numeric,                     \n";
    $stSql .= "                nivel integer)                                                                 \n";

    return $stSql;
}

}
