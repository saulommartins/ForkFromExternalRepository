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
     * Classe de mapeamento para a tabela IMOBILIARIO.LOTE_LOTEAMENTO
     * Data de Criação: 07/09/2004

     * @author Analista: Ricardo Lopes de Alencar
     * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

     * @package URBEM
     * @subpackage Mapeamento

    * $Id: TCIMLoteLoteamento.class.php 59612 2014-09-02 12:00:51Z gelson $

     * Casos de uso: uc-05.01.15
*/

/*
$Log$
Revision 1.6  2006/09/18 09:12:53  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  IMOBILIARIO.LOTE_LOTEAMENTO
  * Data de Criação: 07/09/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Cassiano de Vasconcellos Ferrerira

  * @package URBEM
  * @subpackage Mapeamento
*/
class TCIMLoteLoteamento extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TCIMLoteLoteamento()
{
    parent::Persistente();
    $this->setTabela('imobiliario.lote_loteamento');

    $this->setCampoCod('cod_lote');
    $this->setComplementoChave('');

    $this->AddCampo('cod_lote','integer',true,'',true,true);
    $this->AddCampo('cod_loteamento','integer',true,'',false,true);
    $this->AddCampo('caucionado','boolean',true,'',false,false);

}

function recuperaLoteLoteamento(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaLoteLoteamento().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaLoteLoteamento()
{
    $stSQL .= "SELECT                                       \n";
    $stSQL .= "    l.cod_lote ,                             \n";
    $stSQL .= "    l.cod_loteamento ,                       \n";
    $stSQL .= "    l.caucionado,                            \n";
    $stSQL .= "    ll.valor,                                \n";
    $stSQL .= "    lo.codigo_composto                       \n";
    $stSQL .= "FROM                                         \n";
    $stSQL .= "    imobiliario.lote_loteamento as l             \n";
    $stSQL .= "LEFT JOIN                                    \n";
    $stSQL .= "    imobiliario.lote_localizacao as ll           \n";
    $stSQL .= "ON                                           \n";
    $stSQL .= "    ll.cod_lote = l.cod_lote                 \n";
    $stSQL .= "LEFT JOIN                                    \n";
    $stSQL .= "    imobiliario.localizacao as lo                \n";
    $stSQL .= "ON                                           \n";
    $stSQL .= "    ll.cod_localizacao = lo.cod_localizacao  \n";

    return $stSQL;
}

}
