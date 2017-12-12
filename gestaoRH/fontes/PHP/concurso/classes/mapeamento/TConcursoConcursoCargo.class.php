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
  * Classe de mapeamento da tabela CONCURSO.CONCURSO_CARGO
  * Data de Criação: 29/03/2005

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Rafael Almeida

  * @package URBEM
  * @subpackage Mapeamento
  $Revision: 30566 $
  $Name$
  $Author: souzadl $
  $Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

  * Casos de uso: uc-00.00.00

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  CONCURSO.CONCURSO_CARGO
  * Data de Criação: 29/03/2005

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Rafael Almeida

  * @package URBEM
  * @subpackage Mapeamento
*/
class TConcursoConcursoCargo extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TConcursoConcursoCargo()
{
    parent::Persistente();
    $this->setTabela('concurso.concurso_cargo');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_edital,cod_cargo');

    $this->AddCampo('cod_edital','integer',true,'',true,true);
    $this->AddCampo('cod_cargo','integer',true,'',true,true);

}
function listarCargos(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaRelacionamento().$stFiltro.$stOrdem;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}
function montaRecuperaRelacionamento()
{
    $stQuebra = "\n";
    $stSql  = " SELECT                                                        ".$stQuebra;
    $stSql .= "     C.*,                                                      ".$stQuebra;
    $stSql .= "     CA.descricao                                              ".$stQuebra;
    $stSql .= " FROM                                                          ".$stQuebra;
    $stSql .= "     concurso.concurso_cargo as C,                         ".$stQuebra;
    $stSql .= "     pessoal.cargo          as CA                          ".$stQuebra;
    $stSql .= " WHERE                                                         ".$stQuebra;
    $stSql .= "     C.cod_cargo = CA.cod_cargo                                ".$stQuebra;

    return $stSql;
}

}
