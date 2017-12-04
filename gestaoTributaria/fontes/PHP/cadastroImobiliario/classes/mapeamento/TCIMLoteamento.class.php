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
     * Classe de mapeamento para a tabela IMOBILIARIO.LOTEAMENTO
     * Data de Criação: 07/09/2004

     * @author Analista: Ricardo Lopes de Alencar
     * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

     * @package URBEM
     * @subpackage Mapeamento

    * $Id: TCIMLoteamento.class.php 59612 2014-09-02 12:00:51Z gelson $

     * Casos de uso: uc-05.01.15
*/

/*
$Log$
Revision 1.5  2006/09/18 09:12:53  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  IMOBILIARIO.LOTEAMENTO
  * Data de Criação: 07/09/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Cassiano de Vasconcellos Ferrerira

  * @package URBEM
  * @subpackage Mapeamento
*/
class TCIMLoteamento extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TCIMLoteamento()
{
    parent::Persistente();
    $this->setTabela('imobiliario.loteamento');

    $this->setCampoCod('cod_loteamento');
    $this->setComplementoChave('');

    $this->AddCampo('cod_loteamento','integer',true,'',true,false);
    $this->AddCampo('nom_loteamento','varchar',true,'100',false,false);
    $this->AddCampo('area_logradouro','numeric',true,'14,2',false,false);
    $this->AddCampo('area_comunitaria','numeric',true,'14,2',false,false);

}

function recuperaLote(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaLote().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaLote()
{
$stSql .= " select                                                          \n";
$stSql .= "     l.cod_loteamento,                                           \n";
$stSql .= "     l.nom_loteamento,                                           \n";
$stSql .= "     l.area_logradouro,                                          \n";
$stSql .= "     l.area_comunitaria,                                         \n";
$stSql .= "     pl.cod_processo,                                            \n";
$stSql .= "     pl.exercicio,                                               \n";
$stSql .= "     lt.cod_lote,                                                \n";
$stSql .= "     to_char(lt.dt_aprovacao, 'dd/mm/yyyy') as dt_aprovacao,     \n";
$stSql .= "     to_char(lt.dt_liberacao, 'dd/mm/yyyy') as dt_liberacao,     \n";
$stSql .= "     ll.cod_localizacao,                                         \n";
$stSql .= "     ll.valor,                                                   \n";
$stSql .= "     il.codigo_composto                                          \n";
$stSql .= " from                                                            \n";
$stSql .= "     imobiliario.loteamento as l                                     \n";
$stSql .= " left join                                                       \n";
$stSql .= "     imobiliario.processo_loteamento as pl                           \n";
$stSql .= " on                                                              \n";
$stSql .= "     l.cod_loteamento = pl.cod_loteamento                        \n";
$stSql .= " left join                                                       \n";
$stSql .= "     imobiliario.loteamento_lote_origem as lt                        \n";
$stSql .= " on                                                              \n";
$stSql .= "     l.cod_loteamento = lt.cod_loteamento                        \n";
$stSql .= " left join                                                       \n";
$stSql .= "     imobiliario.lote_localizacao as ll                              \n";
$stSql .= " on                                                              \n";
$stSql .= "     lt.cod_lote = ll.cod_lote                                   \n";
$stSql .= " left join                                                       \n";
$stSql .= "     imobiliario.localizacao as il                                   \n";
$stSql .= " on                                                              \n";
$stSql .= "     il.cod_localizacao = ll.cod_localizacao                     \n";

return $stSql;
}

}
