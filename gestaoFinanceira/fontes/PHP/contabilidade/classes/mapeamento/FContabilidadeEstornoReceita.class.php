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
    * Classe de mapeamento da tabela FN_CONTABILIDADE_ESTORNO_RECEITA
    * Data de Criação: 03/12/2004

    * @author Desenvolvedor: Eduardo Martins
    * @author Analista: Jorge Ribarr

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.02.05
*/

/*
$Log$
Revision 1.6  2006/07/05 20:50:14  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FContabilidadeEstornoReceita extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FContabilidadeEstornoReceita()
{
    parent::Persistente();
    $this->setTabela('EstornoRealizacaoReceita');

    $this->AddCampo('conta_recebimento'      ,'varchar',false,''    ,false,false);
    $this->AddCampo('clas_receita'           ,'varchar',false,''    ,false,false);
    $this->AddCampo('exercicio'              ,'varchar',false,''    ,false,false);
    $this->AddCampo('valor'                  ,'numeric',false,'14.2',false,false);
    $this->AddCampo('complemento'            ,'varchar',false,''    ,false,false);
    $this->AddCampo('cod_lote'               ,'integer',false,''    ,false,false);
    $this->AddCampo('tipo_lote'              ,'varchar',false,''    ,false,false);
    $this->AddCampo('cod_entidade'           ,'integer',false,''    ,false,false);
}

function montaRecuperaTodos()
{
    $stSql  = " SELECT  \n";
    $stSql .= " ".$this->getTabela()."('".$this->getDado("conta_recebimento")."', \n";
    $stSql .= "                        '".$this->getDado("clas_receita")     ."', \n";
    $stSql .= "                        '".$this->getDado("exercicio")        ."', \n";
    $stSql .= "                         ".$this->getDado("valor")            ." , \n";
    $stSql .= "                        '".$this->getDado("complemento")      ."', \n";
    if( $this->getDado('cod_lote') )
        $stSql .= "     ".$this->getDado('cod_lote').", ";
    else {
        $stSql .= "      contabilidade.fn_insere_lote( ";
        $stSql .= " '".$this->getDado('exercicio')."' ";
        $stSql .= " ,".$this->getDado('cod_entidade');
        $stSql .= " ,'".$this->getDado('tipo_lote')."' ";
        $stSql .= " ,'".$this->getDado('nom_lote')."' ";
        $stSql .= " ,'".$this->getDado('dt_lote')."' ";
        $stSql .= " ), \n";
    }
    $stSql .= "                        '".$this->getDado("tipo_lote")        ."', \n";
    $stSql .= "                         ".$this->getDado("cod_entidade")     ." ) \n";

    return $stSql;
}

}
