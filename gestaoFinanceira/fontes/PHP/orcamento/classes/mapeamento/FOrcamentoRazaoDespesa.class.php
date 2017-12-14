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
    * Classe de mapeamento da tabela FN_CONTABILIDADE_RAZAO_DESPESA
    * Data de Criação: 22/03/2006

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2007-12-05 15:12:56 -0200 (Qua, 05 Dez 2007) $

    * Casos de uso: uc-02.01.32
*/

/*
$Log$
Revision 1.2  2006/07/05 20:42:02  cleisson
Adicionada tag Log aos arquivos

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FOrcamentoRazaoDespesa extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FOrcamentoRazaoDespesa()
{
    parent::Persistente();
    $this->setTabela('orcamento.fn_razao_despesa');

    $this->AddCampo('dt_lote'      ,'date'   ,false,''    ,false,false);
    $this->AddCampo('historico'    ,'varchar',false,''    ,false,false);
    $this->AddCampo('complemento'  ,'varchar',false,''    ,false,false);
    $this->AddCampo('codigo'       ,'integer',false,''    ,false,false);
    $this->AddCampo('descricao'    ,'varchar',false,''    ,false,false);
    $this->AddCampo('valor'        ,'numeric',false,'14.2',false,false);
    $this->AddCampo('cod_dotacao'  ,'integer',false,''    ,false,false);
    $this->AddCampo('cod_entidade' ,'integer',false,''    ,false,false);
}

function montaRecuperaTodos()
{
    $stSql  = "SELECT * FROM \n";
    $stSql .= $this->getTabela()."(\n";
    $stSql .= "'".$this->getDado('stExercicio')."',".$this->getDado('inCodEntidade').",'".$this->getDado('stDataInicio')."','".$this->getDado('stDataFim')."','".$this->getDado('inCodDotacao')."','".$this->getDado('boEmpenho')."','".$this->getDado('boLiquidacao')."','".$this->getDado('boPagamento')."','".$this->getDado('boSuplementacao')."','".$this->getDado('inCodConta')."','".$this->getDado('stDestinacaoRecurso')."','".$this->getDado('inCodDetalhamento')."') as retorno ( \n";
    $stSql .= " data date, \n";
    $stSql .= " boo_complem boolean, \n";
    $stSql .= " complemento varchar, \n";
    $stSql .= " estorno boolean, \n";
    $stSql .= " plano int, \n";
    $stSql .= " sequencia int, \n";
    $stSql .= " cod_estrutural varchar, \n";
    $stSql .= " tipo_valor char, \n";
    $stSql .= " historico varchar, \n";
    $stSql .= " valor numeric, \n";
    $stSql .= " exercicio char(4), \n";
    $stSql .= " tipo char, \n";
    $stSql .= " lote int, \n";
    $stSql .= " entidade int, \n";
    $stSql .= " nom_cgm varchar, \n";
    $stSql .= " numcgm int, \n";
    $stSql .= " conta int, \n";
    $stSql .= " dotacao int \n";
    $stSql .= ") \n";

    return $stSql;
}

}
