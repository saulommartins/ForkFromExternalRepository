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
  * Classe de mapeamento da tabela PESSOAL.SEFIP
  * Data de Criação: 02/02/2005

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Lucas Leusin Oaigen

  * @package URBEM
  * @subpackage Mapeamento

  Caso de uso: uc-04.04.40
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  PESSOAL.SEFIP
  * Data de Criação: 02/02/2005

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Lucas Leusin Oaigen

  * @package URBEM
  * @subpackage Mapeamento
*/
class TPessoalSefip extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPessoalSefip()
{
    parent::Persistente();
    $this->setTabela('pessoal.sefip');

    $this->setCampoCod('cod_sefip');
    $this->setComplementoChave('');

     $this->AddCampo('cod_sefip','sequence',true,'',true,false);
     $this->AddCampo('descricao','varchar',true,'200',false,false);
     $this->AddCampo('num_sefip','char',true,'3',false,false);
     $this->AddCampo('repetir_mensal', 'boolean', true,1,false,false);

}

function recuperaSefip(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? $stOrdem : " ORDER BY descricao ";
    $stSql  = $this->montaRecuperaSefip().$stFiltro.$stOrdem;

    //$this->setDebug( $stSql );

    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaSefip()
{
    $stSQL .= " SELECT                                                          \n";
    $stSQL .= " cod_sefip                                                       \n";
    $stSQL .= " ,descricao                                                      \n";
    $stSQL .= " ,num_sefip                                                      \n";
    $stSQL .= " FROM                                                            \n";
    $stSQL .= "     pessoal.sefip                               \n";

   return $stSQL;

}

}
