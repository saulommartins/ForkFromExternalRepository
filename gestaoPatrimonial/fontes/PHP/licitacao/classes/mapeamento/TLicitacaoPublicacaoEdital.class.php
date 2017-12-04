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
    * Classe de mapeamento da tabela licitacao.publicacao_edital
    * Data de Criação: 15/09/2006

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Nome do Programador

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 19673 $
    $Name$
    $Author: hboaventura $
    $Date: 2007-01-29 09:20:19 -0200 (Seg, 29 Jan 2007) $

    * Casos de uso: uc-03.05.17
*/
/*
$Log$
Revision 1.6  2007/01/29 11:20:19  hboaventura
Bug #8077#, #7974#

Revision 1.5  2006/11/30 21:51:43  andre.almeida
Alterado de exercicio_edital para exercicio

Revision 1.4  2006/11/08 10:51:42  larocca
Inclusão dos Casos de Uso

Revision 1.3  2006/10/31 13:37:08  fmsilva
utilizada funcao to_char para retornar a data no formato correto (dd/mm/yyyy)

Revision 1.2  2006/10/31 11:27:50  fmsilva
ajustados nomes de campo em minusculo

Revision 1.1  2006/09/15 12:05:59  cleisson
inclusão

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  licitacao.publicacao_edital
  * Data de Criação: 15/09/2006

  * @author Analista: Gelson W. Gonçalves
  * @author Desenvolvedor: Nome do Programador

  * @package URBEM
  * @subpackage Mapeamento
*/
class TLicitacaoPublicacaoEdital extends Persistente
{
  /**
      * Método Construtor
      * @access Private
  */
  public function TLicitacaoPublicacaoEdital()
  {
    parent::Persistente();
    $this->setTabela("licitacao.publicacao_edital");

    $this->setCampoCod('');
    $this->setComplementoChave('numcgm,data_publicacao,num_edital,exercicio');

    $this->AddCampo('numcgm'          ,'integer',false ,''   ,true,'TLicitacaoVeiculosPublicidade');
    $this->AddCampo('data_publicacao' ,'date'   ,true  ,''   ,true,false);
    $this->AddCampo('num_edital'      ,'integer',false ,''   ,true,'TLicitacaoEdital');
    $this->AddCampo('exercicio'       ,'char'   ,false ,'4'  ,true,'TLicitacaoEdital');
    $this->AddCampo('observacao'      ,'varchar',false ,'80' ,false,false);
    $this->AddCampo('num_publicacao'  ,'integer',false ,''   ,false,false);
  }

  public function recuperaVeiculosPublicacao(&$rsRecordSet,  $stFiltro = "", $stOrdem = "", $boTransacao = "")
  {
      $obErro      = new Erro;
      $obConexao   = new Conexao;
      $rsRecordSet = new RecordSet;
      $stSql = $this->montaRecuperaVeiculosPublicacao($numEdital).$stFiltro.$stOrdem;
      $this->stDebug = $stSql;
      $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
  }

  public function montaRecuperaVeiculosPublicacao()
  {
    //minha tabela fisica
    $tab = $this->getTabela();

    $stSql .="SELECT                                              \n";
    $stSql .=" sw_cgm.numcgm as veiculoPublicacao,                \n";
    $stSql .=" sw_cgm.nom_cgm as nomeVeiculoPublicacao,           \n";
    $stSql .=  "to_char(".$tab.".data_publicacao,'dd/mm/yyyy') as dataPublicacao,          \n";
    $stSql .=  $tab.".observacao as observacao,                   \n";
    $stSql .=  $tab.".num_publicacao as num_publicacao            \n";
    $stSql .="FROM                                                \n";
    $stSql .=$tab.",sw_cgm                                        \n";
    $stSql .="WHERE                                               \n";
    $stSql .="    sw_cgm.numcgm=".$tab.".numcgm                   \n";
    if ( $this->getDado( 'num_edital' ) ) {
      $stSql .="    AND ".$tab.".num_edital=".$this->getDado( 'num_edital' )."  \n";
    }
    if ( $this->getDado( 'exercicio' ) ) {
      $stSql .="    AND ".$tab.".exercicio='".$this->getDado( 'exercicio' )."'  \n";
    }
    return $stSql;
  }

}
